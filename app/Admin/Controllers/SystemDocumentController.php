<?php

namespace App\Admin\Controllers;

use App\Admin\Admin;
use App\Models\SystemDocument;

use App\Admin\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Models\Ward;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Response;

class SystemDocumentController extends Controller
{
    public function index()
    {
        if (!Admin::user()->inRoles(['administrator', 'view.all', 'so-gd'])) {
            return Permission::error();
        }
        $documents = SystemDocument::get();
        

        $title = "Tài liệu";
        $breadcrumbs = [
            ['name' => $title, 'link' => route('system_document.index')]
        ];
        return view('admin.document.index', compact('documents','breadcrumbs','title'));
    }

    public function create(){
        if (!Admin::user()) {
            return Permission::error();
        }
        $title = "Thêm tài liệu";
        $breadcrumbs = [
            ['name' => 'Tài liệu', 'link' => route('system_document.index')],
            ['name' => $title],
        ];
        
        return view('admin.document.create', [ 'title' => $title, 'breadcrumbs' => $breadcrumbs]);

    }

    public function store(Request $request){
        if (!Admin::user()) {
            return Permission::error();
        }
        $validator = $request->validate([
            'document_name' => 'required|string',
            'document_type' => 'required|numeric',
            'file_upload' => 'required|mimes:csv,txt,xlx,xls,xlsx,pdf,doc,docx|max:100000',
        ], [
            'document_name.required' => trans('validation.required'),
            'document_name.string' => trans('validation.string'),
            'document_type.numeric' => trans('validation.numeric'),
            'document_type.required' => trans('validation.required'),
            'file_upload.required' => trans('validation.required'),
            'file_upload.mimes' => trans('validation.mimes'),
        ]);

        $document_file = new SystemDocument;
        if($request->file()) {
            $fileName = time().'_'.$request->file('file_upload')->getClientOriginalName();
            $file_extension = $request->file('file_upload')->getClientOriginalExtension();
            if(in_array($file_extension, ['csv', 'xlx','xlsx', 'xls' ])){
                $document_file->file_type = 'Excel';
            }
            if(in_array($file_extension, ['pdf'])){
                $document_file->file_type = 'PDF';
            }
            if(in_array($file_extension, ['doc', 'docx'])){
                $document_file->file_type = 'Word';
            }
            if(in_array($file_extension, ['txt'])){
                $document_file->file_type = 'Text';
            }
            $document_file->creator_id = Admin::user()->id;
            $filePath = Storage::put('documents', $request->file('file_upload'));
            $file_type = $request->file('file_upload')->getClientMimeType();
            $document_file->document_name = $request->document_name;
            $document_file->document_type = $request->document_type;
            $document_file->file_path = $filePath;
            $document_file->file_size = $request->file('file_upload')->getSize();
            $document_file->save();   
        }
        return redirect()->back()->with('success','Tải file thành công');
    }

    public function edit($id){
        
        $document = SystemDocument::find($id);
        if (!(Admin::user()->isAdministrator() || Admin::user()->id == $document->creator_id)) {
            return Permission::error();
        }
        if(!empty($document)){
            $title = "Sửa tài liệu";
        $breadcrumbs = [
            ['name' => 'Tài liệu', 'link' => route('system_document.index')],
            ['name' => $title],
        ];
        
        return view('admin.document.edit', [ 'title' => $title, 'breadcrumbs' => $breadcrumbs, 'document' => $document]);
        }
        

    }

    public function update(Request $request, $id){
        
        $validator = $request->validate([
            'document_name' => 'required|string',
            'document_type' => 'required|numeric',
            'file_upload' => 'mimes:csv,txt,xlx,xls,xlsx,pdf,doc,docx|max:100000',
        ], [
            'document_name.required' => trans('validation.required'),
            'document_name.string' => trans('validation.string'),
            'document_type.numeric' => trans('validation.numeric'),
            'document_type.required' => trans('validation.required'),
            'file_upload.required' => trans('validation.required'),
            'file_upload.mimes' => trans('validation.mimes'),
        ]);

        $document_file = SystemDocument::find($id);
        if (!(Admin::user()->isAdministrator() || Admin::user()->id == $document_file->creator_id)) {
            return Permission::error();
        }
        if(empty($document_file)){
            return redirect()->back()->with('error','Tải file không thành công');
        }
        if($request->file()) {
            $fileName = time().'_'.$request->file('file_upload')->getClientOriginalName();
            $file_extension = $request->file('file_upload')->getClientOriginalExtension();
            if(in_array($file_extension, ['csv', 'xlx','xlsx', 'xls' ])){
                $document_file->file_type = 'Excel';
            }
            if(in_array($file_extension, ['pdf'])){
                $document_file->file_type = 'PDF';
            }
            if(in_array($file_extension, ['doc', 'docx'])){
                $document_file->file_type = 'Word';
            }
            if(in_array($file_extension, ['txt'])){
                $document_file->file_type = 'Text';
            }
            $document_file->creator_id = Admin::user()->id;
            $filePath = Storage::put('', $request->file('file_upload'));
            
            Storage::delete($document_file->file_path);
            
            $document_file->file_path = $filePath;
            $document_file->file_size = $request->file('file_upload')->getSize();
     
        }
        $document_file->document_name = $request->document_name;
        $document_file->document_type = $request->document_type;
        $document_file->save();

        return redirect()->route('system_document.index')->with('success','Chỉnh sửa tài liệu thành công');
    }

    public function delete(){
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {

            $document_file = SystemDocument::find(request()->id);
            if (!(Admin::user()->isAdministrator() || Admin::user()->id == $document_file->creator_id)) {
                return response()->json(['error' => 1, 'msg' => 'Bạn không có quyền xóa!']);
            }
            if(empty($document_file)){
                return redirect()->back()->with('error','File không tồn tại');
            }
            Storage::delete($document_file->file_path);
            $document_file->delete();
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function licenceFileShow($id)
    {
        $document_file = SystemDocument::find($id);
        //Todo: add permission user can access file or not
        if (!(Admin::user())) {
            return redirect()->back();
        }      
        //This method will look for the file and get it from drive
        try {
            $path = storage_path('/app/uploads/documents/' . $document_file->file_path);
            $file = pathinfo($path);
            $ext = $file['extension'];
            return response()->download($path, $document_file->document_name.'.'.$ext);
      
        } catch (FileNotFoundException $exception) {
            abort(404);
            return redirect()->back();
        }
    }


}