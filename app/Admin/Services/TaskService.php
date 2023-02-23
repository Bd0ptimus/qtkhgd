<?php

namespace App\Admin\Services;

use App\Admin\Models\AdminUser;
use App\Models\Attachment;
use App\Models\District;
use App\Admin\Repositories\AdminUserRepository;
use App\Admin\Repositories\AttachmentRepository;
use App\Admin\Repositories\CheckListRepository;
use App\Admin\Repositories\NotificationAdminRepository;
use App\Admin\Repositories\TaskAssigneeRepository;
use App\Admin\Repositories\TaskCheckListRepository;
use App\Admin\Repositories\TaskCommentRepository;
use App\Admin\Repositories\TaskFollowerRepository;
use App\Admin\Repositories\TaskRepository;
use App\Admin\Repositories\TaskStatusRepository;
use App\Models\NotificationAdmin;
use App\Models\Province;
use App\Models\School;
use App\Models\SchoolStaff;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Admin\Admin;
use App\Http\Resources\TaskCollection;

class TaskService
{
    protected $taskRepo;
    protected $taskStatusRepo;
    protected $checkListRepo;
    protected $attachmentRepo;
    protected $taskFollowerRepo;
    protected $taskAssigneeRepo;
    protected $taskCommentRepo;
    protected $adminUserRepo;
    protected $taskCheckListRepo;
    protected $notificationAdminRepo;

    public function __construct(
        TaskRepository          $taskRepo,
        TaskCommentRepository   $taskCommentRepo,
        AttachmentRepository    $attachmentRepo,
        TaskAssigneeRepository  $taskAssigneeRepo,
        TaskFollowerRepository  $taskFollowerRepo,
        CheckListRepository     $checkListRepo,
        AdminUserRepository     $adminUserRepo,
        TaskCheckListRepository $taskCheckListRepo,
        TaskStatusRepository    $taskStatusRepo,
        NotificationAdminRepository    $notificationAdminRepo
    )
    {
        $this->taskRepo = $taskRepo;
        $this->taskCommentRepo = $taskCommentRepo;
        $this->attachmentRepo = $attachmentRepo;
        $this->taskAssigneeRepo = $taskAssigneeRepo;
        $this->taskFollowerRepo = $taskFollowerRepo;
        $this->checkListRepo = $checkListRepo;
        $this->taskStatusRepo = $taskStatusRepo;
        $this->adminUserRepo = $adminUserRepo;
        $this->taskCheckListRepo = $taskCheckListRepo;
        $this->notificationAdminRepo = $notificationAdminRepo;
    }

    public function index($request)
    {
        $userId = Admin::user()->id;
        $taskStatuses = $this->taskStatusRepo->getList();
        $tasks = $this->taskRepo->search($request);
        $users = $this->getAllUserAssign();

        $filtered = collect($users)->filter(function ($value, $key) use($userId){
            return $value->id == $userId;
        });

        if(!count($filtered)) {
            $users  = collect(array_merge([Admin::user()->toArray()], collect($users)->toArray()));
        }

        return [
            'status' => $taskStatuses,
            'users' => $users,
            'tasks' => $tasks
        ];
    }

    public function show($id)
    {
        $userId = Admin::user()->id;
        $taskStatuses = $this->taskStatusRepo->getList();
        $users = $this->getAllUserAssign();

        $filtered = collect($users)->filter(function ($value, $key) use($userId){
            return $value->id == $userId;
        });

        if(!count($filtered)) {
            $users  = collect(array_merge([Admin::user()->toArray()], collect($users)->toArray()));
        }

        //array relations
        $relations = [
            'assigned',
            'followers',
            'checklists',
            'attachments',
            'currentStatus',
            'creator',
        ];
        //array with count relations
        $withCount = [
            'attachments',
            'checklists',
            'followers',
        ];

        $task = $this->taskRepo->findById($id, ['*'], $relations, $withCount);

        return [
            'user_id' => $userId,
            'status' => $taskStatuses,
            'users' => $users,
            'canEdit' => $this->checkCreatorTask($userId, $id),
            'task' => $task
        ];
    }


    public function checkCreatorTask($userId, $taskId)
    {
        if ($this->taskRepo->checkCreatorTask($userId, $taskId)) return true;
        return false;
    }

    public function createComment($params): bool
    {
        DB::beginTransaction();
        try {
            $userId = Admin::user()->id;
            $params['creator_id'] = $userId;
            $comment = $this->taskCommentRepo->create($params);
            //send email
            $this->sendMailTaskComment($params['task_id'], $comment);
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error('[create task comment]'.$ex->getMessage());
            return false;
        }
    }

    public function handleNotiComment($taskId, $creatorId, $creatorName)
    {
        // get task by id
        $task = $this->taskRepo->findById($taskId, ['*'], ['assigned', 'followers'], []);
        //dd($task->toArray());
        $assigned = $task->assigned->pluck('id')->toArray();
        $followers = $task->followers->pluck('id')->toArray();
        //dd($assigned, $followers);
        $creator = $task->creator_id;

        if($creatorId === $creator) {
            $userIds = array_unique(array_merge($assigned, $followers));
        } else {
            $userIds = array_unique(array_merge($assigned, $followers, [$creator]));
        }

        $this->createNotiTask($task, $userIds, NotificationAdmin::NOTI_TASK_COMMENT, $creatorName);
    }

    public function convertDateFormat($date, $formatStart, $formatEnd)
    {
        return \Carbon\Carbon::createFromFormat($formatStart, $date)
            ->format($formatEnd);
    }

    public function create($params): bool
    {
        DB::beginTransaction();
        try {
            $currentUserId = Admin::user()->id;
            $params['creator_id'] = $currentUserId;
            $task = $this->taskRepo->create($params);
            if ($task) {
                // Task Follower mapping
                if (isset($params['follower_ids']) && count($params['follower_ids']) > 0) {
                    $this->updateTaskFollower($task->id, $params['follower_ids']);
                    // create notification admin
                    $this->createNotiTask($task, $params['follower_ids'], NotificationAdmin::NOTI_TASK_FOLLOWER);
                }

                // Task Assignee mapping
                if (isset($params['assignee_ids']) && count($params['assignee_ids']) > 0) {
                    $this->updateTaskAssignee($task->id, $params['assignee_ids']);
                } else {
                    $this->updateTaskAssignee($task->id, [$currentUserId]);
                }

                // Task Check list mapping
                if (isset($params['check_list_ids']) && count($params['check_list_ids']) > 0) {
                    $this->updateTaskCheckList($task->id, $params['check_list_ids']);
                }

                // Task Attachment mapping
                if (isset($params['attachments']) && count($params['attachments']) > 0) {
                    $this->updateAttachment($task, $params['attachments']);
                }

                if (isset($params['assignee_ids'])) {
                    // create notification admin
                    $this->createNotiTask($task, $params['assignee_ids'], NotificationAdmin::NOTI_TASK_ASSIGNED);

                    //send email
                    $this->sendMailTaskNewAssignee($task->id, []);
                }
                
            }
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error('[update create task]'.$ex->getMessage());
            return false;
        }
    }

    public function getStatusTaskById($taskId)
    {
        return $this->taskRepo->getStatusTaskById($taskId);
    }

    public function updateOnlyFiled($id, $params)
    {
        DB::beginTransaction();
        try {
            $this->taskRepo->update($id, $params);
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error('[update only filed]'.$ex->getMessage());
            return false;
        }
    }

    public function updateTaskFollower($taskId, $followerIds)
    {
        if ($taskId) {
            DB::table('tasks_follower')
                ->where('task_id', $taskId)
                ->delete();
        }
        DB::beginTransaction();
        try {
            foreach ($followerIds as $item) {
                DB::table('tasks_follower')->insert([
                    'task_id' => $taskId,
                    'user_id' => intVal($item),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error('[update follower task]'.$ex->getMessage());
        }
    }

    public function updateTaskAssignee($taskId, $assigneeIds)
    {
        if ($taskId) {
            DB::table('tasks_assignee')
                ->where('task_id', $taskId)
                ->delete();
        }
        DB::beginTransaction();
        try {
            foreach ($assigneeIds as $item) {
                DB::table('tasks_assignee')->insert([
                    'task_id' => $taskId,
                    'user_id' => intVal($item),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error('[update assignee task]'.$ex->getMessage());
        }
    }

    public function createNotiTask($task, $userIds, $type, $userName = null)
    {
        $title = "Nhiệm vụ";
        $content = "";
        $start_date = $this->convertDateFormat($task->getOriginal('start_date'), 'Y-m-d', 'd-m-Y');
        $contentAssigned = "<small class='text-".PRIORITY_BG[$task->priority]."'>".PRIORITY_VALUE[$task->priority]."</small> - [ Bắt đầu " . $start_date . "]" . ' - ' . $task->title;
        $contentFollow = "[Giám sát] - <small class='text-".PRIORITY_BG[$task->priority]."'>".PRIORITY_VALUE[$task->priority]."</small> - [ Bắt đầu " . $start_date . "]" . ' - ' . $task->title;
        $contentComment = "<b>" . $userName . "</b>" . " bình luận nhiệm vụ " . "<b>" . $task->title . "</b>";
        switch ($type) {
            case NotificationAdmin::NOTI_TASK_ASSIGNED:
                $content = $contentAssigned;
                break;
            case NotificationAdmin::NOTI_TASK_FOLLOWER:
                $content = $contentFollow;
                break;
            case NotificationAdmin::NOTI_TASK_COMMENT:
                $content = $contentComment;
                break;
            default:
                $content = '';
                break;
        }

        $users = $this->adminUserRepo->getListUsers($userIds);
        foreach ($users as $user) {
            //check user config web_notification
            if ($user && $user->web_notification) {
                // Notification for admin
                DB::beginTransaction();
                try {
                    $this->notificationAdminRepo->create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'content' => $content,
                        'type' => NotificationAdmin::TYPE["nhiem_vu"],
                        'data' => json_encode([
                            'task_id' => $task->id,
                            'task_title' => $task->title,
                            'creator' => $task->creator->name,
                            'creator_id' => $task->creator_id,
                            'status' => $task->status,
                            'priority' => $task->priority,
                            'start_date' => $task->getOriginal('start_date'),
                            'due_date' => $task->getOriginal('due_date')
                        ]),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    DB::commit();
                } catch (Exception $ex) {
                    DB::rollBack();
                    if(env('APP_ENV') !== 'production') dd($ex);
                    Log::error('[create noti admin]'.$ex->getMessage());
                }

            }
        }
    }

    public function updateTaskCheckList($taskId, $checkListIds)
    {
        if ($taskId) {
            DB::table('tasks_checklist')
                ->where('task_id', $taskId)
                ->delete();
        }
        DB::beginTransaction();
        try {
            foreach ($checkListIds as $item) {
                DB::table('tasks_checklist')->insert([
                    'task_id' => $taskId,
                    'check_list_id' => intVal($item),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error('[update check list task]'.$ex->getMessage());
        }
    }

    public function updateAttachment($task, $payload)
    {
        DB::beginTransaction();
        try {
            foreach ($payload as $item) {
                $file = pathinfo($item);
                $attachment = new Attachment();
                $attachment->path = $item;
                $attachment->name = $file['basename'];
                $check = $task->attachments()->save($attachment);
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error('[update attachments task]'.$ex->getMessage());
        }
    }

    public function deleteAttachmentById($attachmentId)
    {
        return $this->attachmentRepo->deleteById($attachmentId);
    }

    public function sendMailTaskNewAssignee($taskId, $data)
    {
        $task = $this->taskRepo->findById($taskId, ['*']);
        $userIds = $this->getUserIdByTaskId($taskId);
        /** ----------------------------------------------
         * send email [assigned]
         * ----------------------------------------------*/
        //send to users
        if ($users = $this->adminUserRepo->getListUsers($userIds)) {
            foreach ($users as $user) {
                if($user->email && $user->email_notification) {
                    $mail = new \App\Mail\TaskAssignment($user, $data, $task);
                    $mail->build();
                }
            }
        }
    }

    public function sendMailTaskUpdateAssignee($taskId, $userIds)
    {
        $task = $this->taskRepo->findById($taskId, ['*']);
        /** ----------------------------------------------
         * send email [update assigned]
         * ----------------------------------------------*/
        //send to users
        if ($users = $this->adminUserRepo->getListUsers($userIds)) {
            foreach ($users as $user) {
                if($user->email && $user->email_notification) {
                    $mail = new \App\Mail\TaskAssignment($user, [], $task);
                    $mail->build();
                }
            }
        }
    }

    public function sendMailTaskUploadFile($taskId, $data)
    {
        $task = $this->taskRepo->findById($taskId, ['*']);
        $users = $this->getUserIdByTaskId($taskId);

        /** ----------------------------------------------
         * send email [upload file]
         * ----------------------------------------------*/
        //send to users
        if ($users = $this->adminUserRepo->getListUsers($users)) {
            foreach ($users as $user) {
                if($user->email && $user->email_notification) {
                    $mail = new \App\Mail\TaskFileUploaded($user, $data, $task);
                    $mail->build();
                }
            }
        }
    }

    public function sendMailTaskUpdateStatus($taskId, $data)
    {
        $task = $this->taskRepo->findById($taskId, ['*']);
        $users = $this->getUserIdByTaskId($taskId);

        /** ----------------------------------------------
         * send email [update status]
         * ----------------------------------------------*/
        //send to users
        if ($users = $this->adminUserRepo->getListUsers($users)) {
            foreach ($users as $user) {
                if($user->email && $user->email_notification) {
                    $mail = new \App\Mail\TaskStatusChanged($user, $data, $task);
                    $mail->build();
                }
            }
        }
    }

    public function sendMailTaskComment($taskId, $data)
    {
        $task = $this->taskRepo->findById($taskId, ['*']);
        $users = $this->getUserIdByTaskId($taskId);

        /** ----------------------------------------------
         * send email [comment]
         * ----------------------------------------------*/
        //send to users
        if ($datas = $this->adminUserRepo->getListUsers($users)) {
            foreach ($datas as $user) {
                if($user->email && $user->email_notification) {
                    $mail = new \App\Mail\TaskComment($user, $data, $task);
                    $mail->build();
                }
            }
        }
    }

    public function getUserIdByTaskId($taskId)
    {
        $task = $this->taskRepo->findById($taskId, ['*']);
        $users = collect($task->assigned)->implode('id', ',');
        return explode(",", $users);
    }

    public function deleteById($id): bool
    {
        DB::beginTransaction();
        try {
            $currentUserId = Admin::user()->id;
            //check is creator task
            if ($this->checkCreatorTask($currentUserId, $id)) {
                $this->taskRepo->deleteById($id);
                $this->taskCommentRepo->deleteByTaskId($id);
                $this->taskFollowerRepo->deleteByTaskId($id);
                $this->taskAssigneeRepo->deleteByTaskId($id);
                $this->taskCheckListRepo->deleteByTaskId($id);
                $this->attachmentRepo->deleteByTaskId($id, 'App\Models\Task');
            }
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error('[delete task]'.$ex->getMessage());
            return false;
        }
    }

    public function getAttachmentById($id)
    {
        //check if file exists in the database
        return $this->attachmentRepo->findById($id);
    }


    public function getAllUserAssign()
    {
        $roles = $this->adminUserRepo->getRolesUser()->toArray();
        //dd(Admin::user()->id, $roles);
        if (in_array(ROLE_ADMIN, $roles)) {
            return $this->adminUserRepo->getAllUser();
        } else if (in_array(ROLE_CM, $roles)) {
            return $this->adminUserRepo->getAllUser();
        } else if (in_array(ROLE_SO_GD, $roles)) {
            return $this->accessUserProvince();
        } else if (in_array(ROLE_PHONG_GD, $roles)) {
            return $this->accessUserDistrict();
        } else if (in_array(ROLE_CV_PHONG, $roles)) {
            return $this->accessUserSpecialist();
        } else if (in_array(ROLE_HIEU_TRUONG, $roles)) {
            return $this->accessUserSchool();
        } else if (in_array(ROLE_SCHOOL_MANAGER, $roles)) {
            return $this->accessUserSchool();
        } else if (in_array(ROLE_TO_TRUONG, $roles)) {
            return $this->accessUserSubject();
        } else if (in_array(ROLE_GIAO_VIEN, $roles)) {
            return $this->accessUserTeach();
        }

        return $this->adminUserRepo->getAllUser();
    }

    public function accessUserProvince()
    {
        $users = collect();
        $province = Admin::user()->provinces->first();
        $userOfProvince = Province::where('id', $province->id)->with(['users.roles' => function ($query) {
            $query->whereIn('slug', [ROLE_PHONG_GD]);
        }])->first();
        if($userOfProvince) {
            $users = $userOfProvince->users->filter(function ($value, $key) {
                return count($value->roles) > 0;
            });
        }

        return $users->all();
    }

    public function accessUserDistrict()
    {
        $users = collect();
        $district = Admin::user()->districts->first();
        $userOfDistrict = District::where('id', $district->id)->with(['users.roles' => function ($query) {
            $query->whereIn('slug', [ROLE_CV_PHONG, ROLE_HIEU_TRUONG]);
        }])->first();
        if($userOfDistrict) {
            $users = $userOfDistrict->users->filter(function ($value, $key) {
                return count($value->roles) > 0;
            });
        }

        return $users;
    }

    public function accessUserTeach()
    {
        $users = collect();
        /*$regularStaff = RegularGroupStaff::with(['users.roles' => function($query){
            $query->where('slug', ROLE_TO_TRUONG);
        }])->where('staff_id', Admin::user()->id)->first();

        if($regularStaff) {
            $users = $regularStaff->users->filter(function ($value, $key) {
                return count($value->roles) > 0;
            });
        }*/

        return $users->all();
    }

    public function accessUserSubject()
    {
        $users = collect();
        /*$regularStaff = RegularGroupStaff::with('users')
            ->where('staff_id', Admin::user()->id)
            ->where('member_role', GROUP_LEADER)
            ->first();*/

        return $users->all();
    }

    public function accessUserSchool()
    {
        $users = collect();
        $district = Admin::user()->districts->first();
        
        if($district) {
            $userRolePGD = District::where('id', $district->id)->with(['users.roles' => function ($query) {
                $query->where('slug', ROLE_PHONG_GD);
            }])->first();
            $users = $userRolePGD->users->filter(function ($value, $key) {
                return count($value->roles) > 0;
            });
        }
        
        $school = Admin::user()->schools->first();
        $staffCodes = SchoolStaff::where('school_id', $school->id)->get()->pluck('staff_code')->toArray();
        if (count($staffCodes)) {
            $staffs = AdminUser::whereIn('username', $staffCodes)->with(['roles' => function ($query) {
                $query->whereIn('slug', [ROLE_TO_TRUONG, ROLE_GIAO_VIEN]);
            } ])->get();

            $staffs = $staffs->filter(function ($value, $key) {
                return count($value->roles) > 0;
            });

            if($staffs) $users = $users->merge($staffs);
        }

        return $users->all();
    }

    public function accessUserSpecialist()
    {
        $district = Admin::user()->districts->first();
        $userRolePGD = District::where('id', $district->id)->with(['users.roles' => function ($query) {
            $query->where('slug', ROLE_PHONG_GD);
        }])->first();

        // get list school
        $specialist = AdminUser::where('id', Admin::user()->id)->with('specialistSchool')->first();

        $schoolIds = $specialist->specialistSchool->pluck('id')->unique()->toArray();

        $schools = School::whereIn('id', $schoolIds)->with(['users.roles' => function ($query) {
            $query->where('slug', ROLE_HIEU_TRUONG);
        }])->get();
        $userSchools = $schools->pluck('users')->flatten(1);

        return $userRolePGD->users->merge($userSchools)->unique()->filter(function ($value, $key) {
            return count($value->roles) > 0;
        })->values();
    }

    public function getTasks(array $params = [])
    {
        $tasks = $this->taskRepo->getTasks($params);

        return new TaskCollection($tasks);
    }
}
