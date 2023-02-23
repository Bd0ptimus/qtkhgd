<?php

namespace App\Admin\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolBranch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SchoolBranchController extends Controller
{
    public function index($id)
    {
        $school = School::where('id', $id)->with('branches', 'ward')->first();

        return $this->renderView('admin.school.branch.index', [
            'school' => $school
        ]);
    }

    public function addBranch($id)
    {
        $school = School::find($id);
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        return $this->renderView('admin.school.branch.form_branch', [
            'title' => 'Thêm điểm trường cho trường',
            'routing' => route('admin.school.post_add_branch', ['id' => $school->id]),
            'school' => $school,
        ]);
    }

    public function postAddBranch($id)
    {
        $data = request()->only([
            'branch_name',
            'branch_email',
            'branch_address',
            'branch_phone',
            'is_main_branch',
        ]);

        $rules = [
            'branch_name' => "required",
            'branch_email' => "required|email",
            'branch_address' => "required",
            'branch_phone' => "required",
            'is_main_branch' => ["required", Rule::in(0, 1)],
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        /** @var $school School */
        $school = School::find($id);
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!')->withInput();
        }

        if (!!$data['is_main_branch']) {
            $school = $school->load('branches');
            $mainBranchExists = array_first($school->branches, function ($schoolBranch) {
                return !!$schoolBranch['is_main_branch'];
            });
            if (!is_null($mainBranchExists)) {
                return redirect()->back()->with('error', 'Đã có điểm trường là điểm trường chính. Vui lòng kiểm tra lại!')->withInput();
            }
        }

        $data['school_id'] = $school->id;

        SchoolBranch::create($data);

        return redirect()->route('admin.school.view_branch_list', [
            'id' => $school->id
        ])->with('success', 'Thêm điểm trường thành công!');
    }

    public function editBranch($id)
    {
        $branch = SchoolBranch::with('school')->find($id);
        if (is_null($branch)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        return $this->renderView('admin.school.branch.form_branch', [
            'title' => 'Chỉnh sửa nhân viên cho trường',
            'routing' => route('admin.school.post_edit_branch', ['id' => $branch->id]),
            'school' => $branch->school,
            'branch' => $branch,
        ]);
    }

    public function postEditBranch($id)
    {
        $data = request()->only([
            'branch_name',
            'branch_email',
            'branch_address',
            'branch_phone',
            'is_main_branch',
        ]);

        $rules = [
            'branch_name' => "required",
            'branch_email' => "required|email",
            'branch_address' => "required",
            'branch_phone' => "required",
            'is_main_branch' => ["required", Rule::in(0, 1)],
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        /** @var $branch SchoolBranch */
        $branch = SchoolBranch::with('school')->find($id);
        if (is_null($branch)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!')->withInput();
        }

        if (!!$data['is_main_branch']) {
            $school = $branch->school->load('branches');
            $mainBranchExists = array_first($school->branches, function ($schoolBranch) use ($branch) {
                return !!$schoolBranch['is_main_branch'] && $branch['id'] !== $schoolBranch['id'];
            });
            if (!is_null($mainBranchExists)) {
                return redirect()->back()->with('error', 'Đã có điểm trường là điểm trường chính. Vui lòng kiểm tra lại!')->withInput();
            }
        }

        $branch->update($data);

        return redirect()->route('admin.school.view_branch_list', [
            'id' => $branch->school->id
        ])->with('success', 'Chỉnh sửa điểm trường thành công!');
    }

    /**
     * Delete staff
     *
     * @return JsonResponse
     */
    public function deleteBranch()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $ids = request('ids');
            $schoolBranch = SchoolBranch::with(['school', 'school.branches'])->find($ids[0]);
            foreach($ids as $id) {
                $schoolBranch = SchoolBranch::with(['school', 'school.branches'])->find($id);
                $school = $schoolBranch->school;
                if(count($school->branches) <= 1) {
                    return response()->json(['error' => 1, 'msg' => 'Bạn không thể xoá điểm trường do đây là điểm trường duy nhất!']);
                } else {
                    SchoolBranch::destroy($id);
                }
            }
            
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}