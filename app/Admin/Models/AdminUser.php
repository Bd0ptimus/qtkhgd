<?php
#app/Admin/Models/AdminUser.php
namespace App\Admin\Models;

use App\Admin\Admin;
use App\Models\District;
use App\Models\Notification;
use App\Models\NotificationAdmin;
use App\Models\Province;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolStaff;
use App\Models\Student;
use App\Models\UserAgency;
use App\Models\UserDevice;
use App\Models\UserVerification;
use App\Models\Ward;
use App\Models\Task;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AdminUser extends Model implements AuthenticatableContract, JWTSubject
{
    use Authenticatable;

    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 0;
    const STATUS_DELETED = 4;
    protected static $allPermissions = null;
    public $table = 'admin_user';
    protected $guarded = [];
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if (in_array($model->id, SC_GUARD_ADMIN)) {
                return false;
            }
            $model->roles()->detach();
            $model->permissions()->detach();
        });

        static::deleted(function ($model) {
            $model->adminNotifications()->delete();
            $model->notifications()->delete();
            $model->userDevices()->delete();
            $model->agency()->delete();
        });
    }

    
    /**
     * Update info customer
     * @param  [array] $dataUpdate
     * @param  [int] $id
     */
    public static function updateInfo($dataUpdate, $id)
    {
        $dataUpdate = sc_clean($dataUpdate, 'password');
        $obj = self::find($id);
        return $obj->update($dataUpdate);
    }

    /**
     * Get all permissions of user.
     *
     * @return mixed
     */
    public static function allPermissions()
    {
        if (self::$allPermissions === null) {
            $user = Admin::user();
            self::$allPermissions = $user->roles()->with('permissions')->get()->pluck('permissions')->flatten()->merge($user->permissions);
        }
        return self::$allPermissions;
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_user', 'user_id', 'role_id');
    }

    public static function listUserStatus()
    {
        return [
            self::STATUS_ACTIVE => 'Hoạt động',
            self::STATUS_BANNED => 'Tạm khoá',
            self::STATUS_DELETED => 'Đã xoá'
        ];
    }

    public static function createAcount($data, $roleSlug, $agencyId)
    {
        $role = AdminRole::getRole($roleSlug);

        $user = AdminUser::where('username', $data['username'])->first();
        if (!$user) $user = AdminUser::createUser($data);

        $roles = [$role->id]; //Quản lý cấp Sở (so-gd)

        $permission = [];

        $user->roles()->detach();
        $user->permissions()->detach();
        //Insert roles
        if ($roles) {
            $user->roles()->attach($roles);
        }
        //Insert permission
        if ($permission) {
            $user->permissions()->attach($permission);
        }
        $agencyClass = "";
        switch ($roleSlug) {
            case ROLE_SO_GD:
            case 'tuyen-ttyt-province':
                $agencyClass = "App\Models\Province";
                break;
            case ROLE_PHONG_GD:
            case ROLE_CV_PHONG:
            case 'tuyen-ttyt-district':
                $agencyClass = "App\Models\District";
                break;
            case 'tuyen-ttyt-ward':
                $agencyClass = "App\Models\Ward";
                break;
            case ROLE_HIEU_TRUONG:
            case 'nv-yte':
            case ROLE_SCHOOL_MANAGER:
            case 'tuyen-ttyt-school':
                $agencyClass = "App\Models\School";
                break;
            case ROLE_GIAO_VIEN:
                $agencyClass = "App\Models\SchoolClass";
                break;
            case 'parents' :
                $agencyClass = "App\Models\Student";
                break;
        }

        UserAgency::create([
            "user_id" => $user->id,
            "agency_id" => $agencyId,
            "agency_type" => $agencyClass
        ]);
    }

    /**
     * Create new customer
     * @return [type] [description]
     */
    public static function createUser($dataInsert)
    {
        $dataUpdate = sc_clean($dataInsert, 'password');
        return self::create($dataUpdate);
    }

    

    /**
     * Has many admin notifications
     *
     * @return HasMany
     */
    public function adminNotifications()
    {
        return $this->hasMany(NotificationAdmin::class, 'user_id', 'id')
            ->orderBy('created_at', 'desc')
            ->where('read', 0);
    }

    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_user_permission', 'user_id', 'permission_id');
    }

    /**
     * Check if user has no permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function cannot(string $permission): bool
    {
        return !$this->can($permission);
    }

    /**
     * Check if user has permission.
     *
     * @param $ability
     * @param array $arguments
     *
     * @return bool
     */
    public function can($ability, $arguments = []): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if ($this->permissions->pluck('slug')->contains($ability)) {
            return true;
        }

        return $this->roles->pluck('permissions')->flatten()->pluck('slug')->contains($ability);
    }

    /**
     * Check if user is administrator.
     *
     * @return mixed
     */
    public function isAdministrator(): bool
    {
        return $this->isRole('administrator');
    }

    /**
     * Check if user is $role.
     *
     * @param string $role
     *
     * @return mixed
     */
    public function isRole(string $role): bool
    {
        return $this->roles->pluck('slug')->contains($role);
    }

    /**
     * Check user can visile menu.
     * Allow: is isAdministrator, is viewAll group,
     * or menu not yet require psermission, or require permission same user
     *
     *
     * @param AdminMenu $menu
     *
     * @return bool
     */
    public function visible(AdminMenu $menu): bool
    {
        $allPermissionsMenuAllow = $menu->permissions()
            ->pluck('slug')->flatten()->toArray();
        $allRolesMenuAllow = $menu->roles()
            ->pluck('slug')->flatten()->toArray();
        /*
            Allow if: user is administrator, is isViewAll
            or 
            menu not specify permission and role
         */
        if ((!count($allPermissionsMenuAllow)
                && !count($allRolesMenuAllow))
            || $this->isAdministrator()
            || $this->isViewAll()) {
            return true;
        }

        /*
            Allow if: user contains  role menu
        */
        if ($allRolesMenuAllow) {
            return $this->inRoles($allRolesMenuAllow);
        }
        /*
            Allow if: user contains  permission menu
        */
        if ($allPermissionsMenuAllow) {
            return $this->permissions
                ->pluck('slug')->intersect($allPermissionsMenuAllow)
                ->isNotEmpty();
        }

        return false;
    }

    /**
     * Check if user is view_all.
     *
     * @return mixed
     */
    public function isViewAll(): bool
    {
        return $this->isRole('view.all');
    }

    /**
     * Check if user in $roles.
     *
     * @param array $roles
     *
     * @return mixed
     */
    public function inRoles(array $roles = []): bool
    {
        return $this->roles->pluck('slug')->intersect($roles)->isNotEmpty();
    }

    public function userVerification()
    {
        return $this->hasOne(UserVerification::class, 'user_id', 'id');
    }

    public function canDeleteStudent()
    {
        //Todo: check permission
        return $this->inRoles(['administrator', 'school-manager']);
    }

    public function canDeleteSchool()
    {
        //Todo: check permission
        return $this->inRoles(['administrator']);
    }

    public function accessProvinces()
    {
        if ($this->inRoles(['administrator', 'customer-support'])) return Province::where('id', '>', 0)->with('districts');
        elseif ($this->isRole('so-gd')) {
            return Province::where('id', $this->agency->agency_id);
        } else return null;
    }

    public function accessDistricts()
    {
        if ($this->inRoles(['administrator', 'customer-support'])) return District::where('id', '>', 0)->with('province', 'users');
        elseif ($this->isRole('so-gd')) {
            return District::where('province_id', $this->agency->agency_id)->with('province', 'users');
        } elseif ($this->isRole('phong-gd')) {
            return District::where('id', $this->agency->agency_id)->with('province', 'users');
        } else return null;
    }

    public function accessWards()
    {
        if ($this->isRole('administrator')) return Ward::where('id', '>', 0)->with('users');
        elseif ($this->isRole('so-gd')) {
            $province = $this->provinces[0];
            return Ward::whereIn('district_id', District::where('province_id', $province->id)->pluck('id')->toArray())->with('users');
        } elseif ($this->isRole('phong-gd')) {
            return District::where('district_id', $this->agency->agency_id)->with('province', 'users');
        } else return null;
    }

    public function agency()
    {
        return $this->hasOne(UserAgency::class, 'user_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id');
    }

    public function wards()
    {
        return $this->morphedByMany(Ward::class, 'agency', 'user_agency', 'user_id', 'agency_id', 'id', 'id');
    }

    public function districts()
    {
        return $this->morphedByMany(District::class, 'agency', 'user_agency', 'user_id', 'agency_id', 'id', 'id');
    }

    public function provinces()
    {
        return $this->morphedByMany(Province::class, 'agency', 'user_agency', 'user_id', 'agency_id', 'id', 'id');
    }

    public function schools()
    {
        return $this->morphedByMany(School::class, 'agency', 'user_agency', 'user_id', 'agency_id', 'id', 'id');
    }

    public function classes()
    {
        return $this->morphedByMany(SchoolClass::class, 'agency', 'user_agency', 'user_id', 'agency_id', 'id', 'id');
    }

    public function students()
    {
        return $this->morphedByMany(Student::class, 'agency', 'user_agency', 'user_id', 'agency_id', 'id', 'id');
    }

    public function userDevices()
    {
        return $this->hasMany(UserDevice::class, 'user_id', 'id');
    }
    // Rest omitted for brevity


    /**
     * Get the thumbnail
     *
     * @param string $value
     * @return string
     */
    public function getAvatarAttribute($value)
    {
        if (empty($value)) {
            return 'https://dummyimage.com/300x300/3aa836/ffffff.jpg&text=Not+found';
        } else if (storageExists($value)) {
            return assetStorage($value);
        }

        return $value;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function checkCanEditSchoolAccount($school_id, $user_id){
        $login_user = Admin::user();
        $user = AdminUser::with('schools')->find($user_id);
        if(!$user){
            return [false, 'Tài khoản không tồn tại!'];
        }
        if(!$login_user->inRoles(['administrator', 'customer-support'])){
            $school = School::with('classes.teachers')->find($school_id);

            $is_user_belong_school = false;
            foreach($user->schools as $school){
                if($school->id == $school_id){
                    $is_user_belong_school = true;
                    break;
                }  
            }
            if(!$is_user_belong_school && count($school->classes) > 0){
                foreach($school->classes as $class){
                    if(count($class->teachers) > 0){
                        foreach($class->teachers as $user_teacher){
                            if($user_teacher->id == $user->id){
                                $is_user_belong_school = true;
                                break;
                            }
                        }
                    }
                }
            }
            if(!$is_user_belong_school){
                foreach($school->students as $student){
                    if(!($student->parent_accounts[0] ?? false)) continue;
                    if($student->parent_accounts[0]->id == $user->id){
                        $is_user_belong_school = true;
                        break;
                    }
                }
            }
            if(!$is_user_belong_school){
                return [false, 'Tài khoản chỉnh sửa không thuộc TK trường!'];
            }
            if($login_user->inRoles(['so-gd', 'phong-gd'])){
                $can_edit_school = false;
                $school = \App\Models\School::find($school_id);
                $school_district_id = $school->district_id ?? null;
                foreach($login_user->accessDistricts()->get() as $district){
                    if($district->id == $school_district_id){
                        $can_edit_school = true;
                        break;
                    }  
                }
                if(!$can_edit_school){
                    return [false, 'Bạn không có quyền chỉnh sửa tài khoản trường!'];
                }
            }
            elseif($login_user->inRoles(['school-manager', 'hieu-truong'])){
                $is_user_belong_school = false;
                foreach($login_user->schools as $school){
                    if($school->id == $school_id){
                        $is_user_belong_school = true;
                        break;
                    }  
                }
                if(!$is_user_belong_school){
                    return [false, 'Bạn không có quyền chỉnh sửa tài khoản trường!'];
                }
            }else{
                return [false, 'Bạn không có quyền chỉnh sửa tài khoản trường!'];
            }
        }
        return [true, null];
    }

    public static function checkCanEditStatusSchoolAccount($school_id, $user_id){
        $login_user = Admin::user();
        if(substr($login_user->username, -1) != "1" ){
            return [false, 'Bạn không có quyền chỉnh sửa trạng thái user!'];
        }
        return self::checkCanEditSchoolAccount($school_id, $user_id);
    }

    //taskAssigned
    public function assignee()
    {
        return $this->belongsToMany(Task::class, 'tasks_assignee', 'user_id', 'task_id');
    }

    //taskFollowing
    public function follower()
    {
        return $this->belongsToMany(Task::class, 'tasks_follower', 'user_id', 'task_id');
    }

    public function staffDetail() {
        return $this->hasOne(SchoolStaff::class, 'staff_code', 'username');
    }

    public function specialistSchool() {
        return $this->belongsToMany(School::class, 'district_specialist_school', 'specialist_id', 'school_id');
    }

    public function getUpdatedAtAttribute($value)
    {
        if (!empty($value)) return date(DATETIME_SHORT_FORMAT, strtotime($value)); 
    }
}
