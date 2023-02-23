<?php

namespace App\Admin\Repositories;

use App\Admin\Admin;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use App\Models\UserAgency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Collection;

class AdminUserRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(AdminUser $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getAllUser()
    {
        $query = $this->model->newQuery();

        return $query->select('id', 'name', 'avatar')->get();
    }

    public function getUserByRoleSlug($slug)
    {
        $data = collect();
        foreach ($slug as $item) {
            $role = AdminRole::where('slug', $item)->select('id')->first();

            if($users = AdminRole::findOrFail($role->id)->users()->get()) {
                $data = $data->merge($users);
            }
        }

        return $data;
    }

    public function getAllUserAssign()
    {
        $userId = Admin::user()->id;
        $query = $this->model->newQuery();

        return $query->with(['schools.users' => function ($q) {
            $q->select('id', 'name', 'avatar');
        }])->where('id', $userId)->get();
    }

    public function getListUsers($ids)
    {
        $query = $this->model->newQuery();
        if(is_array($ids)) {
            return $query->whereIn('id', $ids)
                ->get();
        } else {
            return $query->where('id', $ids)
                ->get();
        }
    }

    public function getRolesUser()
    {
        return Admin::user()->roles->pluck('slug');
    }

    public function findByUsername($username) {
        return $this->model->where('username', $username)->get();
    }

    public function createStaffAccount($staff) {
        
        $dataInsert = [
            'username' => $staff->staff_code,
            'user_detail' => $staff->id,
            'password' => bcrypt(\Config::get('constants.password_reset')),
            'name' => $staff->fullname,
            'avatar' => null,
            'created_by' => Admin::user()->id,
            'phone_number' => $staff->phone_number,
            'force_change_pass' => 1
        ];

        DB::beginTransaction();
        try {
            $user = AdminUser::createUser($dataInsert);
            $role = $staff->getOriginal('position') == 1 ? 'hieu-truong' : 'giao-vien';
            $roles = AdminRole::where('slug',$role)->pluck('id')->toArray();
            $user->roles()->sync($roles);
            UserAgency::create([
                'user_id' => $user->id,
                'agency_id' => $staff->school_id,
                'agency_type' => 'App\Models\School'
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($e);
        }
    }
}