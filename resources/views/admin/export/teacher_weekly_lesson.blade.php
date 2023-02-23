<table class="table">
    <thead>
    <tr></tr>
    <tr>
        <th colspan="9">Danh sách bài giảng{{ $monthYear ? ' - ' . $monthYear : '' }}</th>
    </tr>
    <tr></tr>
    <tr>
        <th scope="col">STT</th>
        <th scope="col">Tên bài giảng</th>
        <th scope="col">Chuyên mục</th>
        <th scope="col">Ngày bắt đầu theo kế hoạch</th>
        <th scope="col">Ngày kết thúc theo kế hoạch năm </th>
        <th scope="col">Ngày bắt đầu thực tế </th>
        <th scope="col">Ngày kết thúc thực tế </th>
        <th scope="col">Đánh gía tiến độ theo năm</th>
        <th scope="col">Đánh giá tiến độ theo tuần </th>
    </tr>
    </thead>
    <tbody>
        @foreach($teacherWeeklyLessons as $key => $teacherWeeklyLesson)
            <tr>
                <td scope="row" class="font-weight-bold">{{ $key + 1}}</td>
                <td>{{ $teacherWeeklyLesson->teacherLesson->ten_bai_hoc }}</td>
                <td>{{ $teacherWeeklyLesson->teacherLesson->bai_hoc }}</td>
                <td>{{ $teacherWeeklyLesson->teacherLesson->start_date }}</td>
                <td>{{ $teacherWeeklyLesson->teacherLesson->end_date }}</td>
                <td>{{ $teacherWeeklyLesson->start_date }}</td>
                <td>{{ $teacherWeeklyLesson->end_date }}</td>
                <td>{{ $teacherWeeklyLesson->getProgressByYear() }}</td>
                <td>{{ $teacherWeeklyLesson->getProgressByWeek() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
