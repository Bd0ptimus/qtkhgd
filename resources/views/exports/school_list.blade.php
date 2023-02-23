<table>
    @include('exports.heading')

    {{-- Body--}}
    <tr>
        <td>STT</td>
        <td>Mã trường</td>
        <td>Tên trường</td>
        <td>Cấp</td>
        <td>Địa chỉ</td>
        <td>Phòng Giáo Dục</td>
        <td>Sở Giáo Dục</td>
        <td>Email</td>
        <td>Số điện thoại</td>
        <td>Số điểm trường</td>
        <td>Số lớp học</td>
        <td>Số học sinh</td>
        <td>Học sinh nam</td>
        <td>Học sinh nữ</td>
        <td>Học sinh DTTS</td>
        <td>HS khuyết tật</td>
        <td>HS chính sách</td>
    </tr>
    @php
        $stt = 0;
    @endphp
    @foreach($schools as $school)
        <tr>
            <td>{{ ++$stt }}</td>
            <td>{{ $school->school_code ?? null  }}</td>
            <td>{{ $school->school_name ?? null  }}</td>
            <td>{{ $school->getSchoolType() ?? null }}</td>
            <td>{{ $school->school_address ?? null }}</td>
            <td>{{ $district_name }}</td>
            <td>{{ $province_name }}</td>
            <td>{{ $school->school_email ?? null  }}</td>
            <td>{{ $school->school_phone ?? null  }}</td>
            <td>{{ $school->total_branch }}</td>
            <td>{{ $school->total_class }}</td>
            <td>{{ $school->total_student }}</td>
            <td>{{ $school->total_student_boy }}</td>
            <td>{{ $school->total_student_girl  }}</td>
            <td>{{ $school->total_student_dtts  }}</td>
            <td>{{ $school->total_student_disabilities }}</td>
            <td>{{ $school->total_student_chinhsach }}</td>
        </tr>
    @endforeach
</table>