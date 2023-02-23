<table>
    @include('exports.heading')

    {{-- Body--}}
    <tr>
        <td>STT</td>
        <td>Điểm trường</td>
        <td>Mã Nhân Viên</td>
        <td>Tên</td>
        <td>Ngày sinh</td>
        <td>Giới tính</td>
        <td>Dân tộc</td>
        <td>Tôn giáo</td>
        <td>Quốc tịch</td>
        <td>{{ trans('admin.address') }}</td>
        <td>CMND</td>
        <td>{{ trans('admin.phone') }}</td>
        <td>{{ trans('admin.email') }}</td>
        <td>Trình độ chuyên môn</td>
        <td>Chức vụ</td>
        <td>Chuyên trách</td>
        <td>Kiêm nghiệm</td>
        <td>Chứng chỉ hành nghề</td>
        <td>Trạng thái làm việc</td>
    </tr>
    @php
        $stt = 0;
    @endphp
    @foreach($school->staffs as $staff)
        @php
            $stt++;
        @endphp
        <tr>
            <td>{{ $stt }}</td>
            <td>{{ $staff->schoolBranch ? $staff->schoolBranch->branch_name : '' }}</td>
            <td>{{ $staff->staff_code }}</td>
            <td>{{ $staff->fullname }}</td>
            <td>{{ $staff->dob }}</td>
            <td>{{ $staff->gender }}</td>
            <td>{{ $staff->ethnic }}</td>
            <td>{{ $staff->religion }}</td>
            <td>{{ $staff->nationality }}</td>
            <td>{{ $staff->address }}</td>
            <td>{{ $staff->identity_card }}</td>
            <td>{{ $staff->phone_number }}</td>
            <td>{{ $staff->email }}</td>
            <td>{{ $staff->qualification }}</td>
            <td>{{ $staff->position }}</td>
            <td>{{ $staff->responsible }}</td>
            <td>{{ $staff->concurrently }}</td>
            <td>{{ $staff->professional_certificate }}</td>
            <td>{{ $staff->status }}</td>
        </tr>
    @endforeach
</table>