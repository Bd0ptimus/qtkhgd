<?php
#app/Http/Admin/Controllers/Auth/RoleController.php
namespace App\Admin\Controllers\Auth;

use App\Admin\Admin;
use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class RoutingController extends Controller
{
    public function route($route_name) {
        if(Admin::user()->inRoles(['hieu-truong', 'school-manager', 'nv-yte', 'giao-vien'])){
            if(count(Admin::user()->schools) > 0) {
                $school = Admin::user()->schools[0];
                return redirect()->route($route_name, ['school_id' => $school->id, 'id' => $school->id]);
            } else if(count(Admin::user()->classes) > 0) {
                $class = Admin::user()->classes[0];
                $school = $class->school;

                $params = ['school_id' => $school->id, 'id' => $school->id, 'class' => $class->id, 'school_branch' => $class->schoolBranch->id];
                if(in_array($route_name, ['school.daily_health_check.create', 'school.medical_declaration.create']) && \App\Admin\Admin::user()->inRoles(['giao-vien'])) {
                    $params['type'] = 'student';
                }
                return redirect()->route($route_name, $params);
            } else {
                return redirect()->back()->with('error', 'Bạn không có quyền truy cập vào bất kỳ đơn vị nào');
            }
        } else {
            return redirect()->back()->with('error', 'Bạn đang không có quyền quản lý trường học. Vui lòng kiểm tra lại thông tin');
        }
        
    }
}