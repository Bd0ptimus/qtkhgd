<?php

namespace App\Admin\Controllers\SystemConfig;

use App\Admin\Models\Imports\ImportHealthChannel;
use App\Admin\Models\Imports\ImportLocation;
use App\Admin\Models\Imports\ImportMedicalEquipment;
use App\Admin\Models\Imports\ImportMedicine;
use App\Admin\Models\Imports\ImportFood;
use App\Admin\Models\Imports\ImportDish;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\HealthChannel;
use App\Models\MedicalEquipment;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Province;
use App\Models\Food;
use App\Models\Dish;
use App\Models\DishFood;
use App\Admin\Admin;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\HeadingRowImport;
use Validator;

class SystemConfigController extends Controller
{
    public function medicines()
    {
        $mainCategories = MedicineCategory::whereNull('parent_id')->with(['medicines', 'subs.medicines'])->get();
        return view('admin.sysconf.medicines', [
            'title' => 'Danh sách thuốc của hệ thống',
            'mainCategories' => $mainCategories
        ]);
    }

    public function importMedicines(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make(request()->all(), [
                'file_upload' => 'required|file',
            ], [
                'file_upload.required' => trans('validation.file_required'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            /* Validate Heading */
            $heading = (new HeadingRowImport)->toArray(request()->file('file_upload'))[0][0];

            if (!ImportMedicine::validateFileHeader($heading)) {
                return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
            }

            $importData = (new ImportMedicine)->toArray(request()->file('file_upload'))[0];
            $importData = ImportMedicine::mappingKey($importData);
            $importData = ImportMedicine::filterData($importData);
            $validator = ImportMedicine::validator($importData);
            if ($validator->fails()) {
                $message = ImportMedicine::getErrorMessage($validator->errors());
                $validator->getMessageBag()->add('file_upload', $message);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $imports = [];

            foreach ($importData as $row) {
                $categoryName = mb_strtoupper($row['category']);
                $category = MedicineCategory::where('name', $categoryName)->first();
                if (!$category) $category = MedicineCategory::create(['name' => $categoryName]);
                $useCategory = $category;
                if (!empty($row['sub_category'])) {
                    $subCategoryName = mb_convert_case($row['sub_category'], MB_CASE_TITLE, "UTF-8");
                    $subCategory = MedicineCategory::where('name', $subCategoryName)->first();
                    if (!$subCategory) $subCategory = MedicineCategory::create(['name' => $subCategoryName, 'parent_id' => $category->id]);
                    else $subCategory->update(['parent_id' => $category->id]);
                    $useCategory = $subCategory;
                }

                $newRow = [
                    'category_id' => $useCategory->id,
                    'name' => $row['name'],
                    'medicine_info' => $row['medicine_info'],
                    'is_basic' => $row['is_basic'],
                    'note' => $row['note'],
                    'required_doctor' => $row['required_doctor']
                ];
                array_push($imports, $newRow);
            }


            Medicine::upsert(
                $imports,
                ['name', 'category_id'],
                ['medicine_info', 'is_basic', 'note', 'required_doctor']
            );
            return redirect()->route('admin.sysconf.medicines');
        }
        $title = "Import Danh mục thuốc";
        return view('admin.sysconf.imports.medicines', [
            'title' => $title
        ]);
    }

    public function medicalEquipments()
    {
        $equipments = MedicalEquipment::all();
        return view('admin.sysconf.medical_equipments', [
            'title' => 'Danh sách thiết bị y tế của hệ thống',
            'equipments' => $equipments
        ]);
    }

    public function importMedicalEquipments(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make(request()->all(), [
                'file_upload' => 'required|file',
            ], [
                'file_upload.required' => trans('validation.file_required'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            /* Validate Heading */
            $heading = (new HeadingRowImport)->toArray(request()->file('file_upload'))[0][0];
            if (!ImportMedicalEquipment::validateFileHeader($heading)) {
                return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
            }

            $importData = (new ImportMedicalEquipment)->toArray(request()->file('file_upload'))[0];
            $importData = ImportMedicalEquipment::mappingKey($importData);
            $importData = ImportMedicalEquipment::filterData($importData);
            $validator = ImportMedicalEquipment::validator($importData);

            if ($validator->fails()) {
                $message = ImportMedicalEquipment::getErrorMessage($validator->errors());
                $validator->getMessageBag()->add('file_upload', $message);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            MedicalEquipment::upsert($importData,
                ['name', 'type'],
                [
                    'unit', 'type', 'specialist', 'recommended_quantity'
                ]
            );
            return redirect()->route('admin.sysconf.medical_equipments');
        }

        $title = "Import Danh mục Thiết bị y tế";
        return view('admin.sysconf.imports.medical_equipments', [
            'title' => $title
        ]);
    }

    public function healthChannels($type = null)
    {
        if ($type) {
            switch ($type) {
                case "chieu-cao":
                    $queryTypes = [1];
                    break;
                case "can-nang":
                    $queryTypes = [2];
                    break;
                case "can-nang-theo-chieu-cao":
                    $queryTypes = [3];
                    break;
                case "bmi-61-78-thang":
                    $queryTypes = [4];
                    break;
                case "bmi-79-thang-tro-len":
                    $queryTypes = [5];
                    break;
                case "bmi":
                    $queryTypes = [4, 5];
                    break;
            }
            $healthChannels = HealthChannel::whereIn('type', $queryTypes)->get();
        } else $healthChannels = HealthChannel::all();
        return view('admin.sysconf.health_channels', [
            'title' => 'Bảng phân kênh sức khoẻ',
            'healthChannels' => $healthChannels
        ]);
    }

    public function importHealthChannels(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make(request()->all(), [
                'file_upload' => 'required|file',
            ], [
                'file_upload.required' => trans('validation.file_required'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            /* Validate Heading */
            $heading = (new HeadingRowImport)->toArray(request()->file('file_upload'))[0][0];
            if (!ImportHealthChannel::validateFileHeader($heading)) {
                return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
            }

            $importData = (new ImportHealthChannel)->toArray(request()->file('file_upload'))[0];
            $importData = ImportHealthChannel::mappingKey($importData);
            $importData = ImportHealthChannel::filterData($importData);
            $validator = ImportHealthChannel::validator($importData);
            if ($validator->fails()) {
                $message = ImportHealthChannel::getErrorMessage($validator->errors());
                $validator->getMessageBag()->add('file_upload', $message);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            HealthChannel::upsert($importData,
                ['gender', 'type', 'month', 'height'],
                [
                    'sd3neg', 'sd2neg', 'sd1neg', 'normal', 'sd1', 'sd2', 'sd3'
                ]
            );
            return redirect()->route('admin.sysconf.index_health_channels');
        }

        $title = "Import Dữ liệu phân kênh sức khoẻ";
        return view('admin.sysconf.imports.health_channels', [
            'title' => $title
        ]);
    }

    public function importLocations(Request $request)
    {

        if ($request->isMethod('post')) {
            $validator = Validator::make(request()->all(), [
                'file_upload' => 'required|file',
            ], [
                'file_upload.required' => trans('validation.file_required'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            /* Validate Heading */
            $heading = (new HeadingRowImport)->toArray(request()->file('file_upload'))[0][0];

            if (!ImportLocation::validateFileHeader($heading)) {
                return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
            }

            $importData = (new ImportLocation)->toArray(request()->file('file_upload'))[0];
            $validator = ImportLocation::validator($importData);
            if ($validator->fails()) {
                $message = ImportLocation::getErrorMessage($validator->errors());
                $validator->getMessageBag()->add('file_upload', $message);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $provines = [];
            $districts = [];
            $wards = [];
            foreach ($importData as $location) {
                $ma_tp = intval($location['ma_tp']);
                $ma_qh = intval($location['ma_qh']);
                $ma_px = intval($location['ma_px']);

                if (!isset($provines[$ma_tp])) $provines[$ma_tp] = [
                    'id' => $ma_tp,
                    'name' => $location['tinh_thanh_pho'],
                    'gso_id' => $location['ma_tp'],
                ];

                if (!isset($districts[$ma_qh])) $districts[$ma_qh] = [
                    'id' => $ma_qh,
                    'name' => $location['quan_huyen'],
                    'gso_id' => $location['ma_qh'],
                    'province_id' => intval($location['ma_tp'])
                ];

                if (!isset($wards[$ma_px])) $wards[$ma_px] = [
                    'id' => $ma_px,
                    'name' => $location['phuong_xa'],
                    'gso_id' => $location['ma_px'],
                    'district_id' => intval($location['ma_qh'])
                ];
            }


            DB::beginTransaction();
            try {
                DB::table('wards')->where('id', '>', 0)->delete();
                DB::table('districts')->where('id', '>', 0)->delete();
                DB::table('provinces')->where('id', '>', 0)->delete();
                Province::insert($provines);
                District::insert($districts);

                $chunk_data = array_chunk($wards, 1000);
                if (isset($chunk_data) && !empty($chunk_data)) {
                    foreach ($chunk_data as $chunk_data_val) {
                        DB::table('wards')->insert($chunk_data_val);
                    }
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                if(env('APP_ENV') !== 'production') dd($e);
            }

            return redirect()->route('admin.agency.index');
        }

        $title = "Import Dữ liệu Địa Chính";
        return view('admin.sysconf.imports.locations', [
            'title' => $title
        ]);
    }

    public function foods()
    {
        $foods = Food::where('school_id', null)->get();
        return view('admin.sysconf.foods.index', [
            'title' => 'Danh mục thực phẩm',
            'foods' => $foods
        ]);
    }

    public function addFood(Request $request)
    {
        if ($request->isMethod('post')) {
            Food::create($request->all());
            return redirect()->route('admin.sysconf.index_foods')->with('success', 'Thêm thành công');
        }
        return view('admin.sysconf.foods.add', [
            'title' => 'Thêm mới thực phẩm',
        ]);
    }

    public function editFood($id, Request $request)
    {
        $food = Food::find($id);
        if(empty($food)){
            return redirect()->back()->with('error', 'Không tồn tại!');
        }
        if ($request->isMethod('post')) {
            $food->update($request->all());
            
            return redirect()->route('admin.sysconf.index_foods')->with('success', 'Chỉnh sửa thành công');
        }
        $title = 'Chỉnh sửa';
        $breadcrumbs = [
            ['name' => 'Danh mục thực phẩm', 'link' => route('admin.sysconf.index_foods')],
            ['name' => $title],
        ];
        return view('admin.sysconf.foods.edit', [
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
            'food' => $food
        ]);
    }

    public function deleteFood(){
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            if (empty(Admin::user())) {
                return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền xóa!']);
            }
            $food = Food::find(request()->id);
            if(empty($food)){
                return response()->json(['error' => 1, 'msg' => 'Không tồn tại']);
            }
            DB::beginTransaction();
            try {
                $food->delete();
                DB::commit();
                
            } catch (Exception $e) {
                DB::rollback();
                if(env('APP_ENV') !== 'production') dd($e);
                return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function importFoods(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make(request()->all(), [
                'file_upload' => 'required|file',
            ], [
                'file_upload.required' => trans('validation.file_required'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            /* Validate Heading */
            $heading = (new HeadingRowImport)->toArray(request()->file('file_upload'))[0][0];

            if (!ImportFood::validateFileHeader($heading)) {
                return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
            }

            $importData = (new ImportFood)->toArray(request()->file('file_upload'))[0];
            //return $importData;
            $importData = ImportFood::filterData($importData);
            $validator = ImportFood::validator($importData);
            if ($validator->fails()) {
                $message = ImportFood::getErrorMessage($validator->errors());
                $validator->getMessageBag()->add('file_upload', $message);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $imports = [];

            foreach ($importData as $row) {
                $newRow = [
                    'category' => $row['category'],
                    'name' => $row['name'],
                    'ty_le_thai' => $row['ty_le_thai'],
                    'ty_le_calo' => $row['ty_le_calo'],
                    'khong_an' => $row['khong_an'],
                    'dong_vat' => $row['dong_vat'],
                    'kho_hang' => $row['kho_hang'],
                    'tphh_nuoc' => $row['tphh_nuoc'],
                    'tphh_protit' => $row['tphh_protit'],
                    'tphh_lipit' => $row['tphh_lipit'],
                    'tphh_gluxit' => $row['tphh_gluxit'],
                    'tphh_cellulose' => $row['tphh_cellulose'],
                    'tphh_tro' => $row['tphh_tro'],
                    'vitamin_caroten' => $row['vitamin_caroten'],
                    'vitamin_a' => $row['vitamin_a'],
                    'vitamin_b1' => $row['vitamin_b1'],
                    'vitamin_b2' => $row['vitamin_b2'],
                    'vitamin_c' => $row['vitamin_c'],
                    'vitamin_pp' => $row['vitamin_pp'],
                    'khoang_calci' => $row['khoang_calci'],
                    'khoang_photpho' => $row['khoang_photpho'],
                    'khoang_sat' => $row['khoang_sat'],
                    'usual' => $row['usual'],
                    'goi_y_nha_tre' => $row['goi_y_nha_tre'],
                    'goi_y_mau_giao' => $row['goi_y_mau_giao'],
                    'type' => $row['type'],
                    'source' => $row['source']
                ];
                array_push($imports, $newRow);
            }

            Food::upsert(
                $imports,
                ['name', 'category'],
                [
                    'ty_le_thai', 'ty_le_calo', 'khong_an', 'dong_vat', 'kho_hang', 
                    'tphh_nuoc', 'tphh_protit', 'tphh_lipit', 'tphh_gluxit', 'tphh_cellulose', 'tphh_tro',
                    'vitamin_caroten', 'vitamin_a', 'vitamin_b1', 'vitamin_b2', 'vitamin_c', 'vitamin_pp',
                    'khoang_calci', 'khoang_photpho', 'khoang_sat', 'usual', 'goi_y_nha_tre', 'goi_y_mau_giao', 'type', 'source'
                ]
            );
            return redirect()->route('admin.sysconf.index_foods');
        }
        $title = "Import Thực phẩm";
        return view('admin.sysconf.imports.foods', [
            'title' => $title
        ]);
    }

    public function dishes()
    {
        $dishes = Dish::with('foods')->where('school_id', null)->get();
        return view('admin.sysconf.dishes.index', [
            'title' => 'Danh sách món ăn',
            'dishes' => $dishes
        ]);
    }

    public function addDish(Request $request)
    {
        if ($request->isMethod('post')) {
            DB::beginTransaction();
            try {
                $dish = Dish::create([
                    'name' => $request->name,
                    'category' => $request->category,
                    'region' => $request->region,
                    'processing' => $request->processing,
                ]);
                
                if($request->has('thucpham')){
                    $dish_foods = $request->thucpham;
                    if(count($dish_foods) > 0){
                        foreach ($dish_foods as $dish_food){
                            DishFood::create([
                                'dish_id' => $dish->id,
                                'food_id' => $dish_food['id'],
                                'quantity' => $dish_food['quantity'],
                            ]);
                        }
                    }
                }
                DB::commit();
                
            } catch (Exception $e) {
                DB::rollback();
                if(env('APP_ENV') !== 'production') dd($e);
            }
            return redirect()->back()->with('success', 'Thêm món ăn thành công!');
        }
        $foods = Food::where('school_id', null)->get();
        $title = 'Thêm mới món ăn';
        $breadcrumbs = [
            ['name' => 'Danh sách món ăn', 'link' => route('admin.sysconf.index_dishes')],
            ['name' => $title],
        ];
        return view('admin.sysconf.dishes.add', [
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
            'foods' => $foods
        ]);
    }

    public function editDish(Request $request, $id)
    {
        $dish = Dish::find($id);
        if(empty($dish)){
            return redirect()->back()->with('error', 'Không tồn tại món ăn!');
        }
        if ($request->isMethod('post')) {
            DB::beginTransaction();
            try {
                $dish->update([
                    'name' => $request->name,
                    'category' => $request->category,
                    'region' => $request->region,
                    'processing' => $request->processing,
                ]);
                $dish_foods = $dish->dishFoods;
                if(!empty($dish_foods)){
                    foreach ($dish_foods as $dish_food){
                        $dish_food->delete();
                    }
                }
                if($request->has('thucpham')){
                    $dish_foods = $request->thucpham;
                    if(!empty($dish_foods)){
                        
                        foreach ($dish_foods as $dish_food){
                            DishFood::create([
                                'dish_id' => $dish->id,
                                'food_id' => $dish_food['id'],
                                'quantity' => $dish_food['quantity'],
                            ]);
                        }
                    }
                }
                DB::commit();
                
            } catch (Exception $e) {
                DB::rollback();
                if(env('APP_ENV') !== 'production') dd($e);
            }
            return redirect()->back()->with('success', 'Đã chỉnh sửa thành công!');
        }
        
        $dish_foods = $dish->foods;
        $foods = Food::where('school_id', null)->get();
        $title = 'Chỉnh sửa món ăn';
        $breadcrumbs = [
            ['name' => 'Danh sách món ăn', 'link' => route('admin.sysconf.index_dishes')],
            ['name' => $title],
        ];
        return view('admin.sysconf.dishes.edit', [
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
            'foods' => $foods,
            'dish' => $dish,
            'dish_foods' => $dish_foods
        ]);
    }

    public function deleteDish(){
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            if (empty(Admin::user())) {
                return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền xóa!']);
            }
            $dish = Dish::find(request()->id);
            if(empty($dish)){
                return response()->json(['error' => 1, 'msg' => 'Không tồn tại']);
            }
            DB::beginTransaction();
            try {
                $dish_foods = $dish->dishFoods;
                if(count($dish_foods) > 0){
                    foreach ($dish_foods as $dish_food){
                        $dish_food->delete();
                    }
                }
                $dish->delete();
                DB::commit();
                
            } catch (Exception $e) {
                DB::rollback();
                if(env('APP_ENV') !== 'production') dd($e);
                return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
            }
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function importDishes(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make(request()->all(), [
                'file_upload' => 'required|file',
            ], [
                'file_upload.required' => trans('validation.file_required'),
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            /* Validate Heading */
            $heading = (new HeadingRowImport)->toArray(request()->file('file_upload'))[0][0];
            if (!ImportDish::validateFileHeader($heading)) {
                return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
            }

            $importData = (new ImportDish)->toArray(request()->file('file_upload'))[0];
            $importData = ImportDish::mappingKey($importData);
            $importData = ImportDish::filterData($importData);
            $validator = ImportDish::validator($importData);
            if ($validator->fails()) {
                $message = ImportDish::getErrorMessage($validator->errors());
                $validator->getMessageBag()->add('file_upload', $message);
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $imports = [];

            foreach ($importData as $row) {
                $newRow = [
                    'category' => $row['category'],
                    'name' => $row['name'],
                    'region' => $row['region'],
                    'processing' => $row['processing']
                ];
                array_push($imports, $newRow);
            }
            Dish::upsert(
                $imports,
                ['name', 'category', 'region'],
                ['processing']
            );
            return redirect()->route('admin.sysconf.index_dishes');
        }
        $title = "Import Món ăn";
        return view('admin.sysconf.imports.dishes', [
            'title' => $title
        ]);
    }
    
}