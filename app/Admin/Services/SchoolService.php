<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Helpers\LogHelper;
use App\Admin\Models\Imports\ImportStaff;
use App\Admin\Repositories\AdminUserRepository;
use App\Admin\Repositories\RegularGroupRepository;
use App\Admin\Repositories\SchoolClassRepository;
use App\Admin\Repositories\SchoolRepository;
use App\Admin\Repositories\StaffRepository;
use App\Models\RegularGroupStaff;
use App\Models\School;
use App\Models\SchoolStaff;
use App\Models\Target;
use App\Models\TargetPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SchoolService
{
    protected $schoolRepo;
    protected $staffRepo;
    protected $rgRepo;
    protected $adminUserRepo;
    protected $staffService;
    public function __construct(
        SchoolRepository $repo,
        StaffRepository $staffRepo,
        RegularGroupRepository $rgRepo,
        AdminUserRepository $adminUserRepo,
        StaffService $staffService
    )
    {
        $this->schoolRepo = $repo;
        $this->staffRepo = $staffRepo;
        $this->rgRepo = $rgRepo;
        $this->adminUserRepo = $adminUserRepo;
        $this->staffService = $staffService;
    }

    public function allBySchool($schoolId) {
        return $this->schoolClassRepo->findBySchoolId($schoolId);
    }

    public function findById($schoolId) {
        return $this->schoolRepo->findById($schoolId, ['*'], [
            'users',
            'staffs','staffs.staffGrades', 'staffs.subjects', 'staffs.staffSubjects', 
            'classes', 'classes.classSubjects', 'classes.homeroomTeacher',
            'regularGroups', 'regularGroups.groupSubjects', 'regularGroups.groupGrades', 'regularGroups.subjects'
        ]);
    }

    public function chuanhoa($schoolId) {

        DB::beginTransaction();
        try{
            $school = $this->findById($schoolId);
            $staffs = $school->staffs;
            $regularGroups = $school->regularGroups;
            foreach($staffs as $staff) {
                $check = $this->adminUserRepo->findByUsername($staff->staff_code);
                if(count($check) == 0) {
                    $this->adminUserRepo->createStaffAccount($staff);
                } else {
                    $check[0]->update([
                        'name' => $staff->fullname,
                        'user_detail' => $staff->id
                    ]);
                }
                $staffGrades = $staff->staffGrades ? $staff->staffGrades->pluck('grade')->toArray() : [];
                $staffSubjects = $staff->staffSubjects ? $staff->staffSubjects->pluck('subject_id')->toArray() : [];

                //release all staff group
                RegularGroupStaff::where('staff_id', $staff->id)->delete();
                foreach($regularGroups as $group) {
                    $checkIfMatch = [];
                    if(in_array($school->school_type, [1,6])) {
                        $groupGrades = $group->groupGrades ? $group->groupGrades->pluck('grade')->toArray() : [];
                        $checkIfMatch = array_intersect($staffGrades, $groupGrades);
                    } else {
                        $groupSubjects = $group->groupSubjects ? $group->groupSubjects->pluck('subject_id')->toArray() : [];
                        $checkIfMatch = array_intersect($staffSubjects, $groupSubjects);
                    }
                    if(count($checkIfMatch) > 0) {
                        RegularGroupStaff::create([
                            'regular_group_id' => $group->id,
                            'staff_id' => $staff->id,
                            'member_role' => GROUP_MEMBER
                        ]);
                    }
                }
            }

            foreach($school->users as $user) {
                $pos = strpos($user->username, 'N');
                if(false !== $pos) {
                    $checkStaff = $this->staffRepo->findByCode($user->username);
                    if(!$checkStaff) $user->delete();
                }  
            }
            DB::commit();
        } catch ( Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
        }
        
    }

    public function createTarget($request) {
        DB::beginTransaction();
        try{
            $target = Target::create([
                "title"=>$request->title,
                "school_id"=>$request->school_id,
                'type' => $request->type,
                "final_target"=>$request->final_target,
                "target_index"=>$request->target_index,
                "description"=>$request->description,
                "solution"=>$request->solution,
            ]);
            if($request->has("count_points") && $request->count_points > 0) {
                for ($i=0; $i < $request->count_points; $i++) { 
                    $target->points()->create([
                        "content" => $request->content_points[$i],
                        "index_point"=>$request->index_points[$i],
                    ]);
                }
            }
            DB::commit();
        } catch ( Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
        }
    }

    public function updateTarget($request,$schoolId,$targetId) {
        DB::beginTransaction();
        try{
            $target = Target::find($targetId);
            $target->fill([
                'title' => $request->title,
                'type' => $request->type,
                'description' => $request->description,
                'solution' => $request->solution,
                'final_target' => $request->final_target,
                'target_index' => $request->target_index
            ])->save();
            if(($request->has("content_points") && count($request->content_points) > 0) &&
              ($request->has("index_points") && count($request->index_points) > 0)) 
             {
                $totalIndexMainPoint = collect($request->index_points)->sum();
                foreach ($request->content_points as $k=>$v) {
                    $point = TargetPoint::where("target_id", $targetId)->where("id", $k)->where("main_point",NULL)->first();
                    if($point) {
                        $point->subPoints()->update(["content"=>$v]);
                        $point->content = $v;
                        $point->index_point = $request->index_points[$k];
                        $point->final_point = $request->index_points[$k] * ($point->result ?? 0) / $totalIndexMainPoint;
                        $point->save();
                        $point->target->updateResultTarget($point);
                        continue;
                    }
                    
                        $target->points()->create([
                            "content" => $v,
                            "index_point"=>$request->index_points[$k],
                        ]);
                }
            }
            DB::commit();
        } catch ( Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
        }
    }
    public function getTotalIndexWithClass($arrayIndex, $arrayStaffIds) {
        try {
            $totalIndex = 0;
            foreach($arrayStaffIds as $staffId) {
                $totalRowExists = $this->staffService->findClassId($staffId)->count();
                $totalIndex += $totalRowExists * $arrayIndex[$staffId];
            }
            return $totalIndex;
        } catch (\Throwable $th) {
           dd($th);
        }

    }
    

    public function takeSchoolByDistrictWithCondition($districtId, $params=[]){
        return $this->schoolRepo->takeSchoolByDistrictWithCondition($districtId, $params);
    }
    
}