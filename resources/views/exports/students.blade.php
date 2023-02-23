<table>
    <tr>
        <td align="center" valign="center"><b>STT</b></td>
        <td align="center" valign="center"><b>Mã học sinh</b></td>
        <td align="center" valign="center"><b>Tên học sinh</b></td>
        <td align="center" valign="center"><b>Lớp</b></td>
        <td align="center" valign="center"><b>Ngày sinh</b></td>
        <td align="center" valign="center"><b>Giới tính</b></td>
        <td align="center" valign="center"><b>Dân tộc</b></td>
        <td align="center" valign="center"><b>Tôn giáo</b></td>
        <td align="center" valign="center"><b>Quốc tịch</b></td>
        <td align="center" valign="center"><b>Địa chỉ</b></td>
    </tr>
    @php
        $stt = 0;
    @endphp
    @foreach($students as $student)
        @php
            $stt++;
        @endphp
        <tr>
            <td>{{ $stt }}</td>
            <td>{{ $student->student_code }}</td>
            <td>{{ $student->fullname }}</td>
            <td>{{ optional($student->class)->class_name }}</td>
            <td>{{ dateTimeToExcel($student->dob) }}</td>
            <td>{{ $student->gender }}</td>
            <td>{{ $student->ethnic }}</td>
            <td>{{ $student->religion }}</td>
            <td>{{ $student->nationality }}</td>
            <td>{{ $student->address }}</td>
        </tr>
    @endforeach
</table>