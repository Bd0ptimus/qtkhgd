<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Models\RegularGroup;
use App\Admin\Repositories\RegularGroupRepository;
use App\Models\RegularGroupStaff;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RegularGroupService
{
    protected $rgRepo;

    public function __construct(RegularGroupRepository $repo)
    {
        $this->rgRepo = $repo;
    }

    public function index() {
        return [ 'regularGroups' => $this->rgRepo->all(['*'],['subjects']) ];
    }

    public function initBySchoolLevel($id) {
        $school = School::find($id);
        $defaultRegularGroups = $this->rgRepo->getDefaultRegularGroupBySchoolLevel($school->school_type);
        if(count($defaultRegularGroups) > 0) {
            foreach($defaultRegularGroups as $regularGroup) {
                $this->rgRepo->cloneRegularGroup($regularGroup, $school);
            }
        }
    }

    public function allBySchool($schoolId) {
        return [
            'school' => School::find($schoolId),
            'regularGroups' => $this->rgRepo->findBySchoolId($schoolId)
        ];
    }

    public function allByStaff($staffId) {
        return $this->rgRepo->findByStaffId($staffId);
    }

    public function findByGroupId($groupId) {
        return $this->rgRepo->findById($groupId, ['*'], ['groupPlans','groupSubjects', 'groupGrades', 'subjects', 'groupStaffs', 'groupStaffs.staff', 'leader', 'deputies']);
    }

    public function create($params, $schoolId = null) {
        DB::beginTransaction();
        try{
            $subjects = $grades = [];
            
            if(isset($params['subjects'])) {
                $subjects = $params['subjects'];
                unset($params['subjects']);
            }
           
            if(isset($params['grades'])) {
                $grades = $params['grades'];
                unset($params['grades']);
            }

            if($schoolId) $params['school_id'] = $schoolId;
            $regularGroup = $this->rgRepo->create($params);
            $this->rgRepo->setSubjects($regularGroup->id, $subjects);
            $this->rgRepo->setGrades($regularGroup->id, $grades);
            DB::commit();
            return ['success' => true, 'message' => 'Tạo Tổ chuyên môn thành công'];
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create subject]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return ['success' => false, 'message' =>  $ex->getMessage()];
        }
        
    }

    public function update($id, $params) {
        DB::beginTransaction();
        try{

            $subjects = $grades = [];

            if(isset($params['subjects'])) {
                $subjects = $params['subjects'];
                unset($params['subjects']);
            }
           
            if(isset($params['grades'])) {
                $grades = $params['grades'];
                unset($params['grades']);
            }
            
            $this->rgRepo->update($id, $params);
            $this->rgRepo->setSubjects($id, $subjects);
            $this->rgRepo->setGrades($id, $grades);
            DB::commit();
            return ['success' => true, 'message' => 'Sửa tổ chuyên môn thành công'];
        } catch (Exception $ex) {
            DB::rollBack();
            if(env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create subject]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return ['success' => false, 'message' =>  $ex->getMessage()];
        }
    }

    public function checkIfCanMange($rgId) {
        if(Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM, ROLE_GIAO_VIEN])) {
            if(Admin::user()->inRoles([ROLE_GIAO_VIEN])) {
                $userDetail = Admin::user()->staffDetail;
                if($userDetail) {
                    $grouptStaff = RegularGroupStaff::where([
                        'staff_id' => $userDetail->id,
                        'regular_group_id' => $rgId,
                        'member_role' => GROUP_LEADER
                    ])->first();
                    if($grouptStaff && $grouptStaff->member_role == GROUP_LEADER) return true;
                    else return false;
                }
            }
            return true;
        }
        return false;
    }
}