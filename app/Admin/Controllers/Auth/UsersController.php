<?php
#app/Http/Admin/Controllers/Auth/UsersController.php
namespace App\Admin\Controllers\Auth;

use App\Admin\Admin;
use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use App\Admin\Models\AgencyDiscount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\BalanceHistory;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use App\Models\UserAgency;
use App\Admin\Permission;
use DB;


class UsersController extends Controller
{
    public function index()
    {
        $data = [
            'title' => trans('user.admin.list'),
            'sub_title' => '',
            'icon' => 'fa fa-indent',
            'menu_left' => '',
            'menu_right' => '',
            'menu_sort' => '',
            'script_sort' => '',
            'menu_search' => '',
            'script_search' => '',
            'listTh' => '',
            'dataTr' => '',
            'pagination' => '',
            'result_items' => '',
            'url_delete_item' => '',
        ];

        $listTh = [
            'check_row' => '',
            'id' => trans('user.id'),
            'user_name' => trans('user.user_name'),
            'name' => trans('user.name'),
            'roles' => trans('user.roles'),
            'phone_number' => 'Số ĐT',
            'is_demo_account' => 'TK Demo',
            'status' => 'Trạng thái',
            'created_at' => trans('user.created_at'),
            'action' => trans('user.admin.action'),
        ];
        $sort_order = request('sort_order') ?? 'id_desc';
        $keyword = request('keyword') ?? '';
        $arrSort = [
            'id__desc' => trans('user.admin.sort_order.id_desc'),
            'id__asc' => trans('user.admin.sort_order.id_asc'),
            'username__desc' => trans('user.admin.sort_order.username_desc'),
            'username__asc' => trans('user.admin.sort_order.username_asc'),
            'name__desc' => trans('user.admin.sort_order.name_desc'),
            'name__asc' => trans('user.admin.sort_order.name_asc'),
        ];
        $obj = new AdminUser;
        if(Admin::user()->isAdministrator()) $obj = $obj->with(['roles', 'permissions'])->where('id','!=', Admin::user()->id);
        if ($keyword) {
            $obj = $obj->whereRaw('(id = ' . (int) $keyword . '  OR name like "%' . $keyword . '%" OR username like "%' . $keyword . '%"  )');
        }  
        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $obj = $obj->orderBy($field, $sort_field);

        } else {
            $obj = $obj->orderBy('id', 'desc');
        }
        if(Admin::user()->isRole('administrator')) {
            $data['menu_filter'] = '';
            //dd($data['menu_filter']);
            $roles_filter = $_REQUEST['role'] ?? '';
            if($roles_filter == "3"){
                $obj = $obj->whereNull('parent');
            } elseif($roles_filter == "4"){
                $obj = $obj->whereNotNull('parent');
            };
        }
        
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $showRoles = '';
            if ($row['roles']->count()) {
                foreach ($row['roles'] as $key => $rols) {
                    $showRoles .= '<span class="label label-success">' . $rols->name . '</span> ';
                }
            }
            $showPermission = '';
            if ($row['permissions']->count()) {
                foreach ($row['permissions'] as $key => $p) {
                    $showPermission .= '<span class="label label-success">' . $p->name . '</span> ';
                }
            }

            $action = '<span title="Cập nhật trạng thái" class="update-status" data-userid="'.$row->id.'" data-name="'.$row->name.'" data-status="'.$row->status.'"><i class="fa fa-user"></i></span> ';
            if(Admin::user()->isAdministrator())
            {
                $action .= '<a class="main-action" href="' . route('admin_user.edit', ['id' => $row['id']]) . '"><span title="' . trans('user.admin.edit') . '"><i class="fa fa-edit"></i></span></a> ';
                
            }
            $action .= '<a href="' . route('admin_user.view_user', ['id' => $row['id']]) . '"><span title="Xem"><i class="fa fa-eye"></i></span></a> ';
            $action .= '<a class="main-action" href="' . route('admin_user.reset_password', ['id' => $row['id']]) . '"><span title="Đặt lại mật khẩu"><i class="fa fa-key"></i></span></a> ';
            $dataTr[] = [
                'check_row' => '<input type="checkbox" class="grid-row-checkbox" data-id="' . $row['id'] . '">',
                'id' => $row['id'],
                'username' => $row['username'],
                'name' => $row->name,
                'roles' => $showRoles,
                'phone_number' => $row['phone_number'],
                'is_demo_account' => $row['is_demo_account'] ? 'X' : '',
                'status' => $row->status == 1 ? 'Hoạt động' : 'Tạm khoá',
                'created_at' => $row['created_at'],
                'action' => $action,
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links('admin.component.pagination');
        $data['result_items'] = trans('user.admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'item_total' => $dataTmp->total()]);
//menu_left
        $data['menu_left'] = '<div class="pull-left">
                    <button type="button" class="btn btn-default grid-select-all"><i class="fa fa-square-o"></i></button> &nbsp;

                    <a  class="main-action btn   btn-flat btn-danger grid-trash" title="Delete"><i class="fa fa-trash-o"></i><span class="hidden-xs"> ' . trans('admin.delete') . '</span></a> &nbsp;

                    <a class=" btn   btn-flat btn-primary grid-refresh" title="Refresh"><i class="fa fa-refresh"></i><span class="hidden-xs"> ' . trans('admin.refresh') . '</span></a> &nbsp;</div>
                    ';
//=menu_left

//menu_right
        $data['menu_right'] = '<a href="' . route('admin_user.create') . '" class="main-action btn  btn-success  btn-flat" title="New" id="button_create_new">
                           <i class="fa fa-plus"></i><span class="hidden-xs">' . trans('admin.add_new') . '</span>
                           </a>';
//=menu_right

//menu_sort

        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }

        $data['menu_sort'] = '
                       <div class="btn-group pull-left">
                        <div class="form-group">
                           <select class="form-control" id="order_sort">
                            ' . $optionSort . '
                           </select>
                         </div>
                       </div>

                       <div class="btn-group pull-left">
                           <a class="btn btn-flat btn-primary" title="Sort" id="button_sort">
                              <i class="fa fa-sort-amount-asc"></i><span class="hidden-xs"> ' . trans('admin.sort') . '</span>
                           </a>
                       </div>';

        $data['script_sort'] = "$('#button_sort').click(function(event) {
      var url = '" . route('admin_user.index') . "?sort_order='+$('#order_sort option:selected').val();
      $.pjax({url: url, container: '#pjax-container'})
    });";

//=menu_sort

//menu_search

        $data['menu_search'] = '
                <form action="' . route('admin_user.index') . '" id="button_search">
                   <div onclick="$(this).submit();" class="btn-group pull-right">
                           <a class="btn btn-flat btn-primary" title="Refresh">
                              <i class="fa  fa-search"></i><span class="hidden-xs"> ' . trans('admin.search') . '</span>
                           </a>
                   </div>
                   <div class="btn-group pull-right">
                         <div class="form-group">
                           <input type="text" name="keyword" class="form-control" placeholder="' . trans('user.admin.search_place') . '" value="' . $keyword . '">
                         </div>
                   </div>
                </form>';
//=menu_search

        $data['url_delete_item'] = route('admin_user.delete');
        return view('admin.screen.list')
            ->with($data);
    }

/**
 * Form create new order in admin
 * @return [type] [description]
 */
    public function create()
    {

        $data = [
            'title' => trans('user.admin.add_new_title'),
            'sub_title' => '',
            'title_description' => trans('user.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'user' => [],
            'roles' => Admin::user()->isRole('administrator') ? (new AdminRole)->pluck('name', 'id')->all() : (new AdminRole)->where('id', 4)->pluck('name', 'id')->all(),
            'permission' => (new AdminPermission)->pluck('name', 'id')->all(),
            'url_action' => route('admin_user.create'),
        ];

        return view('admin.auth.create')
            ->with($data);
    }

/**
 * Post create new order in admin
 * @return [type] [description]
 */
    public function postCreate()
    {   
        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => 'required|string|max:100',
            'username' => 'required|regex:/(^([0-9A-Za-z@\._]+)$)/|unique:admin_user,username|string|max:100|min:3',
            'avatar' => 'nullable|string|max:255',
            'password' => 'required|string|max:60|min:6|confirmed',
            'phone_number' => 'required'
        ], [
            'username.regex' => trans('user.username_validate'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dataInsert = [
            'name' => $data['name'],
            'username' => strtolower($data['username']),
            'avatar' => $data['avatar'],
            'phone_number' => $data['phone_number'],
            'password' => bcrypt($data['password']),
            'created_by' => Admin::user()->id,
            'is_demo_account' => $data['is_demo_account'],
        ];

        $user = AdminUser::createUser($dataInsert);

        $roles = $data['roles'] ?? [];
        $permission = $data['permission'] ?? [];

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

        return redirect()->route('admin_user.index')->with('success', trans('user.admin.create_success'));

    }

/**
 * Form edit
 */
    public function edit($id, Request $request)
    {
        $user = AdminUser::findOrFail($id);
        $school_id = $request->school_id ?? null;
        list($result, $message) = AdminUser::checkCanEditSchoolAccount($school_id, $id);
        if(!$result){
            return redirect()->back()->with('error', $message);
        }
        $classes = [];
        if($user->roles[0]->slug == 'giao-vien'){
            //$classes = $user->classes[0]->schoolBranch->classes;
        }
        $data = [
            'title' => trans('user.admin.edit'),
            'sub_title' => '',
            'title_description' => '',
            'icon' => 'fa fa-pencil-square-o',
            'user' => $user,
            'roles' => Admin::user()->isRole('administrator') ? (new AdminRole)->whereIn('id',[1,3])->pluck('name', 'id')->all() : (new AdminRole)->where('id', 4)->pluck('name', 'id')->all(),
            'permission' => (new AdminPermission)->pluck('name', 'id')->all(),
            'url_action' => route('admin_user.edit', ['id' => $user['id'], 'school_id' => $school_id]),
        ];
        $previous_url = url()->previous();
        return view('admin.auth.edit', compact('previous_url', 'classes'))
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id, Request $request)
    {
        $user = AdminUser::findOrFail($id);
        $school_id = $request->school_id ?? null;
        list($result, $message) = AdminUser::checkCanEditSchoolAccount($school_id, $id);
        if (!$result) {
            return redirect()->to($request->previous_url)->with('error', $message);
        }

        $rules = [
            'name' => 'required|string|max:100|regex:/^([^0-9]*)$/',
            'phone_number' => 'nullable|numeric',
            'username' => 'required|regex:/(^([0-9A-Za-z@\._]+)$)/|unique:admin_user,username,' . $user->id . '|string|max:100|min:3',
            'avatar' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:60|min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'nullable|string|max:60|min:6|required_with:password|same:password',
        ];

        $messages = [
            'username.regex' => trans('user.username_validate'),
            'name.regex' => trans('user.name_validate'),
            'phone_number.numeric' => trans('user.phone_validate'),
            'password.required_with' => trans('user.password_required_with'),
            'password.same' => trans('user.password_same'),
            'password.min' => trans('user.password_min'),
            'password.max' => trans('user.password_max'),
            'password_confirmation.required_with' => trans('user.password_confirm_required_with'),
            'password_confirmation.same' => trans('user.password_confirm_same'),
            'password_confirmation.min' => trans('user.password_confirm_min'),
            'password_confirmation.max' => trans('user.password_confirm_max'),
        ];

        $request->validate($rules, $messages);

        $data = request()->all();

        //Edit
        $dataUpdate = [
            'name' => $data['name'],
            'username' => strtoupper($data['username']),
            'avatar' => $data['avatar'],
            'phone_number' => $data['phone_number'],
            'is_demo_account' => $data['is_demo_account']
        ];
        if ($data['password']) {
            $dataUpdate['password'] = bcrypt($data['password']);
        }

        /* if ($user->roles[0]->slug == 'giao-vien' && strval($user->classes[0]->id) != strval(request()->class)) {
            $class_ids = $user->classes[0]->schoolBranch->classes->pluck('id')->toArray();
            if (in_array(intval(request()->class), $class_ids)) {
                $userAgency = UserAgency::where([
                    'user_id' => $user->id,
                    'agency_type' => 'App\Models\SchoolClass'
                ])->first();
                if ($userAgency) {
                    $userAgency->update(['agency_id' => request()->class]);
                }
            }
        } */

        AdminUser::updateInfo($dataUpdate, $id);

        $previous_url = $request->previous_url ?? 'admin_user.index';
        return redirect()->to($previous_url)->with('success', trans('user.admin.edit_success'));
    }

    /*
    Delete list Item
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if(!Admin::user()->isAdministrator()) {
            return redirect()->route('admin_user.index');
        }

        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $arrID = array_diff($arrID, SC_GUARD_ADMIN);
            $accounts = AdminUser::whereIn('id', $arrID)->pluck('id')->toArray();
            AdminUser::destroy($accounts);
            return response()->json(['error' => 0, 'msg' => 'Đã xoá các tài khoản']);
        }
    }

    public function updateStatus($user_id) {
        switch(Admin::user()->roles[0]->slug ?? null){
            case 'administrator':
            case 'customer-support':
                break;
            case 'school-manager':
            case 'hieu-truong':
                $school_id = request()->query('school_id', null);
                list($result, $message) = AdminUser::checkCanEditStatusSchoolAccount($school_id, $user_id);
                if(!$result){
                    return redirect()->back()->with('error', $message);
                }
                break;
            default:
                return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa user này');
        }

        AdminUser::where('id', $user_id)->update(array('status' => request()->input('status')));
        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công');//route('admin_user.index');
    }

    public function viewUser($id){
        $user = AdminUser::find($id);
        if ($user === null) {
            return redirect()->route('admin_user.index');
        }
        

        foreach ($user->roles as $key => $row){
            $role= $row['name'];
        }       
        $data = [
            'title' => trans('Thông tin user'),
            'icon' => 'fa fa-user',
            'user' => $user,
            'username'=> $user->username,
            'phone_number' => $user->phone_number,
            'role' => $role,
            'status' => $user->status == 1 ? 'Hoạt động' : 'Tạm khoá',
            'roles' => Admin::user()->isRole('administrator') ? (new AdminRole)->whereIn('id',[1,3])->pluck('name', 'id')->all() : (new AdminRole)->where('id', 4)->pluck('name', 'id')->all(),
            'permission' => (new AdminPermission)->pluck('name', 'id')->all(),
            'url_action' => route('admin_user.view_user', ['id' => $user['id']]),
        ];
        return view('admin.screen.view_user', $data);
        //return view('admin_user.index'.'view?id='.$id)->with($dataUpdate);
    }

    public function createSgdAccount($gso_id, $type = "QL"){       
        try{
            $province = Province::where('gso_id',$gso_id)->with('users')->first();  
            if(empty($province)){
                return redirect()->route('admin.agency.provices')->with('error', trans('admin.agency.not_exists_province'));
            }
            if(!Admin::user()->inRoles(['administrator', 'so-gd']) || (Admin::user()->isRole('so-gd') && Admin::user()->agency_id != $province->id)){
                    return Permission::error();
            }

            $account_code = strtoupper($gso_id.$type);
            $no = collect($province->users)->filter(function ($user) use ($account_code) {
                return preg_match("/^{$account_code}/i", $user->username);
            })->count() + 1;
            $new_account_name = $gso_id.$type.$no;

            $dataInsert = [
                'username' => strtoupper($new_account_name),
                'password' => bcrypt(\Config::get('constants.password_reset')),
                'name' => 'Sở Giáo Dục '.$province->name,
                'avatar' => null,
                'created_by' => Admin::user()->id,
                'phone_number' => null,
                'force_change_pass' => 1
            ];
            
            AdminUser::createAcount($dataInsert,'so-gd',$province->id);

            return redirect()->route('admin.agency.provinces')->with('success', trans('admin.agency.create_account_success'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return redirect()->route('admin.agency.provinces')->with('error', $error);
        }
    }

    public function createTtytSgdAccount($gso_id, $type = "Y"){       
        try{
            $province = Province::where('gso_id',$gso_id)->with('users')->first();  
            if(empty($province)){
                return redirect()->route('admin.agency.provices')->with('error', trans('admin.agency.not_exists_province'));
            }
            if(!Admin::user()->inRoles(['administrator', 'tuyen-ttyt-province']) || (Admin::user()->isRole('tuyen-ttyt-province') && Admin::user()->agency_id != $province->id)){
                    return Permission::error();
            }
            $account_code = strtoupper($gso_id.$type);
            
            $no = collect($province->users)->filter(function ($user) use ($account_code) {
                return preg_match("/^{$account_code}/i", $user->username);
            })->count() + 1;
            $new_account_name = $gso_id.$type.$no;

            $dataInsert = [
                'username' => strtoupper($new_account_name),
                'password' => bcrypt(\Config::get('constants.password_reset')),
                'name' => 'Tuyến TTYT Học Đường - '.$province->name,
                'avatar' => null,
                'created_by' => Admin::user()->id,
                'phone_number' => null,
                'force_change_pass' => 1
            ];
            
            AdminUser::createAcount($dataInsert,'tuyen-ttyt-province',$province->id);

            return redirect()->route('admin.agency.provinces')->with('success', trans('admin.agency.create_account_success'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return redirect()->route('admin.agency.provinces')->with('error', $error);
        }
    }

    public function createPgdAccount($gso_id, $type = 'QL'){
        try{
            $district = District::where('gso_id', $gso_id)->with('users', 'province')->first();
            if(empty($district)){
                return redirect()->route('admin.agency.districts')->with('error', trans('admin.agency.not_exists_district'));
            }
            if(!Admin::user()->inRoles(['administrator'])){
                return Permission::error();
            }   
            $account_code = strtoupper($district->province->gso_id.$gso_id.$type);   
            $no = collect($district->users)->filter(function ($user) use ($account_code) {
                return preg_match("/^{$account_code}/i", $user->username);
            })->count() + 1;
            $new_account_name = $account_code.$no;
            
            $dataInsert = [
                'username' => strtoupper($new_account_name),
                'password' => bcrypt(\Config::get('constants.password_reset')),
                'name' => 'Phòng Giáo Dục '.$district->name,
                'avatar' => null,
                'created_by' => Admin::user()->id,
                'phone_number' => null,
                'force_change_pass' => 1
            ];
            try{
                DB::beginTransaction();
                AdminUser::createAcount($dataInsert,'phong-gd',$district->id);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                dd($e);
            }
           

            return redirect()->route('admin.agency.districts', ['province' => $district->province->id])->with('success', trans('admin.agency.create_account_success'));

        } catch (\Exception $e) {
            $error = $e->getMessage();
            return redirect()->route('admin.agency.districts')->with('error', $error);
        }
        
    }

    public function createTtytPgdAccount($gso_id, $type = 'Y'){
        try{
            $district = District::where('gso_id', $gso_id)->with('users', 'province')->first();
            if(empty($district)){
                return redirect()->route('admin.agency.districts')->with('error', trans('admin.agency.not_exists_district'));
            }
            if(!Admin::user()->inRoles(['administrator'])){
                return Permission::error();
            }   
            $account_code = strtoupper($district->province->gso_id.$gso_id.$type);   
            $no = collect($district->users)->filter(function ($user) use ($account_code) {
                return preg_match("/^{$account_code}/i", $user->username);
            })->count() + 1;
            $new_account_name = $account_code.$no;
            
            $dataInsert = [
                'username' => strtoupper($new_account_name),
                'password' => bcrypt(\Config::get('constants.password_reset')),
                'name' => 'Tuyến TTYT Học Đường - '.$district->name,
                'avatar' => null,
                'created_by' => Admin::user()->id,
                'phone_number' => null,
                'force_change_pass' => 1
            ];
            try{
                DB::beginTransaction();
                AdminUser::createAcount($dataInsert,'tuyen-ttyt-district',$district->id);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                dd($e);
            }
           

            return redirect()->route('admin.agency.districts', ['province' => $district->province->id])->with('success', trans('admin.agency.create_account_success'));

        } catch (\Exception $e) {
            $error = $e->getMessage();
            return redirect()->route('admin.agency.districts')->with('error', $error);
        }
        
    }

    public function createTtytWardAccount($gso_id, $type = 'Y'){
        try{
            $ward = Ward::where('gso_id', $gso_id)->with('users', 'district.province')->first();
            if(empty($ward)){
                return redirect()->route('admin.agency.wards')->with('error', trans('admin.agency.not_exists_district'));
            }
            if(!Admin::user()->inRoles(['administrator'])){
                return Permission::error();
            }   
            $account_code = strtoupper($ward->district->province->gso_id.$ward->district->gso_id.$ward->gso_id.$type);
            $no = collect($ward->users)->filter(function ($user) use ($account_code) {
                return preg_match("/^{$account_code}/i", $user->username);
            })->count() + 1;
            $new_account_name = $account_code.$no;
            $dataInsert = [
                'username' => strtoupper($new_account_name),
                'password' => bcrypt(\Config::get('constants.password_reset')),
                'name' => 'Tuyến TTYT Học Đường - '.$ward->name,
                'avatar' => null,
                'created_by' => Admin::user()->id,
                'phone_number' => null,
                'force_change_pass' => 1
            ];
            try{
                DB::beginTransaction();
                AdminUser::createAcount($dataInsert,'tuyen-ttyt-ward',$ward->id);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                dd($e);
            }
            return redirect()->route('admin.agency.wards', ['province' => $ward->district->province->id, 'district' => $ward->district->id])->with('success', trans('admin.agency.create_account_success'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return redirect()->route('admin.agency.wards')->with('error', $error);
        }
    }

    public function resetPassword($id, Request $request){
        $user = AdminUser::find($id);
        $school_id = $request->school_id ?? null;
        list($result, $message) = AdminUser::checkCanEditSchoolAccount($school_id, $id);
        if(!$result){
            return redirect()->back()->with('error', $message);
        }
        $user->update([
            'password' => Hash::make(\Config::get('constants.password_reset')),
            'force_change_pass' => 1
        ]);

        return redirect()->back()->with('success', 'Đặt lại mật khẩu thành công!');

    }
}
