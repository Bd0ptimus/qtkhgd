<?php
#app/Http/Admin/Controllers/AdminLogController.php
namespace App\Admin\Controllers;

use App\Admin\Models\AdminLog;
use App\Admin\Models\AdminUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\UserActivity;

class AdminLogController extends Controller
{

    public function index_old()
    {   

        $data = [
            'title' => trans('log.admin.list'),
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
            'id' => trans('log.id'),
            'user' => trans('log.user'),
            'method' => trans('log.method'),
            'path' => trans('log.path'),
            'ip' => trans('log.ip'),
            'user_agent' => trans('log.user_agent'),
            'input' => trans('log.input'),
            'created_at' => trans('log.created_at')
        ];

        $sort_order = request('sort_order') ?? 'id_desc';
        $keyword = request('keyword') ?? '';
        $arrSort = [
            'id__desc' => trans('log.admin.sort_order.id_desc'),
            'id__asc' => trans('log.admin.sort_order.id_asc'),
            'user_id__desc' => trans('log.admin.sort_order.user_id_desc'),
            'user_id__asc' => trans('log.admin.sort_order.user_id_asc'),
            'path__desc' => trans('log.admin.sort_order.path_desc'),
            'path__asc' => trans('log.admin.sort_order.path_asc'),
            'user_agent__desc' => trans('log.admin.sort_order.user_agent_desc'),
            'user_agent__asc' => trans('log.admin.sort_order.user_agent_asc'),
            'method__desc' => trans('log.admin.sort_order.method_desc'),
            'method__asc' => trans('log.admin.sort_order.method_asc'),
            'ip__desc' => trans('log.admin.sort_order.ip_desc'),
            'ip__asc' => trans('log.admin.sort_order.ip_asc'),

        ];
        $obj = new AdminLog;

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $obj = $obj->orderBy($field, $sort_field);

        } else {
            $obj = $obj->orderBy('id', 'desc');
        }
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[] = [
                'check_row' => '<input type="checkbox" class="grid-row-checkbox" data-id="' . $row['id'] . '">',
                'id' => $row['id'],
                'user_id' => ($user = AdminUser::find($row['user_id'])) ? $user->name : 'N/A',
                'method' => '<span class="badge bg-' . (AdminLog::$methodColors[$row['method']] ?? '') . '">' . $row['method'] . '</span>',
                'path' => '<code>' . $row['path'] . '</code>',
                'ip' => $row['ip'],
                'user_agent' => $row['user_agent'],
                'input' => $row['input'],
                'created_at' => $row['created_at'],
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links('admin.component.pagination');
        $data['result_items'] = trans('log.admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'item_total' => $dataTmp->total()]);

//menu_left
        $data['menu_left'] = '<div class="pull-left">

                    <a class="btn   btn-flat btn-danger grid-trash" title="Delete"><i class="fa fa-trash-o"></i><span class="hidden-xs"> ' . trans('admin.delete') . '</span></a> &nbsp;

                      <a class="btn   btn-flat btn-primary grid-refresh" title="Refresh"><i class="fa fa-refresh"></i><span class="hidden-xs"> ' . trans('log.admin.refresh') . '</span></a> &nbsp;
                      </div>';
//=menu_left

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
      var url = '" . route('admin_log.index') . "?sort_order='+$('#order_sort option:selected').val();
      $.pjax({url: url, container: '#pjax-container'})
    });";

//=menu_sort

        $data['url_delete_item'] = route('admin_log.delete');

        return view('admin.screen.list')
            ->with($data);
    }

/*
Delete list item
Need mothod destroy to boot deleting in model
 */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            AdminLog::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function index(Request $request)
    {
        $title = 'Thống kê lịch sử truy cập';
        

        $filter_start_date = now()->subDays(6);
        $filter_end_date = now();

        if ($request->isMethod('post')) {
            if($request->filter_start_date <= $request->filter_end_date){
                $filter_start_date = $request->filter_start_date. ' 00:00:00';
                $filter_end_date = $request->filter_end_date. ' 23:59:59';
            } else {
                return redirect()->back()->with('error', 'Dữ liệu lọc không hợp lệ!!!');
            }
        }

        $activities = UserActivity::whereBetween('created_at', [$filter_start_date, $filter_end_date])->orderBy('id', 'desc')
                ->with(['school', 'school.district','school.district.province'])
                ->select('school_id', 'school_branch_id', 'route_name', \DB::raw('count(*) as total'))            
                ->groupBY('school_id', 'route_name')->get();

        $summary = [];
        $currentSchool = 0;
        foreach($activities as $activity) {
            if($currentSchool != $activity->school_id && $activity->school_id != null) {
                $currentSchool = $activity->school_id;
                $summary[$currentSchool] = [
                    'school_name' => $activity->school->school_name ?? $activity->school_id,
                    'district' => $activity->school->district->name ?? $activity->school_id,
                    'province' => $activity->school->district->province->name ?? $activity->school_id,
                    'import_student' => 0,
                    'import_staff' => 0,
                    'period_check' => 0,
                    'student_health_absnormal' => 0,
                    'student_specilist_check' => 0,
                    'staff_health_absnormal' => 0, 
                    'staff_specilist_check' => 0,
                    'insurance' => 0,
                    'medicines' => 0,
                    'equipment' => 0,
                    'sanitation' => 0,
                    'food_inspection' => 0,
                    'csvc' => 0,
                    'basic_report' => 0,
                    'advandce_report' => 0,
                    'dynamic_report' => 0,
                    'covid_report' => 0,
                ];
            }

            switch($activity->route_name) {
                case 'admin.school.import_student':
                    $summary[$currentSchool]['import_student'] = $activity->total;
                    break;
                case 'admin.school.import_staff': 
                    $summary[$currentSchool]['import_staff'] = $activity->total;
                    break;
                case 'student_health_periodical_check':
                    $summary[$currentSchool]['period_check'] = $activity->total;
                    break;
                case 'student_health_abnormals':
                    $summary[$currentSchool]['student_health_absnormal'] = $activity->total;
                    break;
                case 'student_health_edit_specialist_test':
                    $summary[$currentSchool]['student_specialist_check'] = $activity->total;
                    break;
                case 'staff_health_abnormals':
                    $summary[$currentSchool]['staff_health_abnormal'] = $activity->total;
                    break;
                case 'staff_health_specialist_test':
                    $summary[$currentSchool]['staff_specilist_check'] = $activity->total;
                    break;
                case 'admin.school_medical.export_excel':
                case 'admin.school_medical.import_insurance':
                case 'admin.school_medical.download_demo_insurance':
                case 'admin.school_medical.post_import_insurance':
                case 'admin.school_medical.mass_edit_insurance':
                case 'admin.school_medical.post_mass_edit_insurance':
                    $summary[$currentSchool]['insurance'] += $activity->total;
                    break;

                case 'school_medical_local_medicines':
                case 'school_medical_config_medicines':
                case 'school_medical_manage':
                case 'school_medical_manage.export.history_import':
                case 'school_medical_manage.export.history_export':
                case 'school_medical_import':
                case 'school_medical_export':
                    $summary[$currentSchool]['medicines'] += $activity->total;
                    break;
                
                case 'school_config_medical_equipments':
                case 'school_local_medical_equipments':
                case 'school_medical_equipments_manage':
                case 'school_medical_equipments_manage.export.equipments_import':
                case 'school_medical_equipments_manage.export.equipments_export':
                case 'school_medical_equipments_import':
                case 'school_medical_equipments_export':
                    $summary[$currentSchool]['equipment'] += $activity->total;
                    break;

                case 'school_sanitation_config_list':
                case 'school_sanitation_config_create':
                case 'school_sanitation_config_delete':
                case 'school_sanitation_config_edit':
                case 'school_sanitation_config_export':
                case 'school_sanitation_check':
                case 'school_sanitation_check.export':
                    $summary[$currentSchool]['sanitation'] += $activity->total;
                    break;

                case 'csvc.check_room.index':
                case 'csvc.check_room.create':
                case 'csvc.check_room.edit':
                case 'csvc.check_room.delete':
                case 'csvc.room.analytics':
                case 'csvc.room.analytics.export':
                case 'csvc.check_furniture.index':
                case 'csvc.check_furniture.create':
                case 'csvc.check_furniture.edit':
                case 'csvc.check_furniture.delete':
                case 'csvc.check_furniture.export':
                case 'csvc.check_furniture.export_per_check_furniture':
                    $summary[$currentSchool]['csvc'] += $activity->total;
                    break;

                case 'admin.school.report.pl02':
                case 'admin.school.report.pl02.create':
                case 'admin.school.report.pl02.export':
                case 'admin.school.report.pl02.edit':
                case 'admin.school.report.pl02.edit':
                case 'admin.school.report.pl02.send':
                case 'admin.school.report.pl02.delete':
                case 'admin.school.report.pl03':
                case 'admin.school.report.pl03.create':
                case 'admin.school.report.pl03.export':
                case 'admin.school.report.pl03.edit':
                case 'admin.school.report.pl03.edit':
                case 'admin.school.report.pl03.send':
                case 'admin.school.report.pl03.delete':
                case 'admin.school.report.pl04':
                case 'admin.school.report.pl04.create':
                case 'admin.school.report.pl04.export':
                case 'admin.school.report.pl04.edit':
                case 'admin.school.report.pl04.edit':
                case 'admin.school.report.pl04.send':
                case 'admin.school.report.pl04.delete':
                    $summary[$currentSchool]['basic_report'] += $activity->total;
                    break;
                case 'report_tonghopkiemtrasuckhoetheothang':
                case 'report_tonghopkiemtrasuckhoetheothang_export':
                case 'report_sotheodoitonghopskhocsinh':
                case 'report_tonghoptheodoisuckhoebatthuong':
                case 'report_tonghoptheodoisuckhoebatthuong_export':
                case 'report_quanlydichbenh':
                case 'report_quanlydichbenh_export':
                    $summary[$currentSchool]['advandce_report'] += $activity->total;
                    break;
                case 'report_baocaodong':
                    $summary[$currentSchool]['dynamic_report'] = $activity->total;
                    break;
                case 'school.covid.index':
                case 'school.covid.create':
                case 'school.covid.edit':
                case 'school.covid.export':
                case 'school.covid.delete':
                    $summary[$currentSchool]['covid_report'] += $activity->total;
                    break;
            }
        }

        $breadcrumbs = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $title],
        ];

        return view('admin.screen.system_summary_activities', [
            'breadcrumbs' => $breadcrumbs,
            'title' => $title,
            'filter_start_date' => $filter_start_date,
            'filter_end_date' => $filter_end_date,
            'activities' => $activities,
            'summary' => $summary
        ]);
    }


/*
User Activity list item
with route school_medical
 */
    public function userActivity($school_id, Request $request){
        $filter_start_date = now()->subDays(6);
        $filter_end_date = now();
        
        if ($request->isMethod('post')) {
            if($request->filter_start_date <= $request->filter_end_date && $request->filter_end_date <= (now())){
                $filter_start_date = $request->filter_start_date;
                $filter_end_date = $request->filter_end_date;
            }
        }
        $title = 'Kiểm tra hoạt động của trường';
        $school = School::find($school_id);
        $breadcrumbs = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school_id])],
            ['name' => $title],
        ];

        $user_activities = UserActivity::with('user', 'school', 'school_branch')->whereBetween('created_at', [$filter_start_date, $filter_end_date])->where('school_id', $school_id)->orderBy('school_id', 'desc')->get();

        return view('admin.screen.user_activity', [
            'school' => $school,
            'breadcrumbs' => $breadcrumbs,
            'title' => $title,
            'user_activities' => $user_activities,
            'filter_start_date' => $filter_start_date,
            'filter_end_date' => $filter_end_date,
        ]);
    }

    public function summaryActivities($school_id, Request $request) 
    {
        $title = 'Thống kê lịch sử truy cập';
        $school = School::find($school_id);
        $breadcrumbs = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school_id])],
            ['name' => $title],
        ];

        $filter_start_date = now()->subDays(6);
        $filter_end_date = now();
        
        if ($request->isMethod('post')) {
            if($request->filter_start_date <= $request->filter_end_date && $request->filter_end_date <= (now())){
                $filter_start_date = $request->filter_start_date;
                $filter_end_date = $request->filter_end_date;
            }
        }

        $activities = UserActivity::whereBetween('created_at', [$filter_start_date, $filter_end_date])->where('school_id', $school_id)->orderBy('id', 'desc')
                ->with('school')
                ->select('school_id', 'school_branch_id', 'route_name', \DB::raw('count(*) as total'))            
                ->groupBY('route_name')->get();
        return view('admin.screen.summary_activities', [
            'school' => $school,
            'breadcrumbs' => $breadcrumbs,
            'title' => $title,
            'filter_start_date' => $filter_start_date,
            'filter_end_date' => $filter_end_date,
            'activities' => $activities
        ]);
    }
}
