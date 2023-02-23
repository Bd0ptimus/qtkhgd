<table class="table zero-configuration timetable" id="shool-regular-group">
    <thead>
    <tr></tr>
    <tr>
        <th colspan="8">THỜI KHÓA BIỂU</th>
    </tr>
    <tr></tr>
    <tr>
        <th scope="col">Buổi</th>
        <th scope="col">Tiết</th>
        <th scope="col">Thứ 2</th>
        <th scope="col">Thứ 3</th>
        <th scope="col">Thứ 4</th>
        <th scope="col">Thứ 5</th>
        <th scope="col">Thứ 6</th>
        <th scope="col">Thứ 7</th>
    </tr>
    </thead>
    <tbody>
   
        @foreach([1,2,3,4,5,6,7,8,9] as $index)
        <tr>
            @if($index == 1)
             
                <td rowspan="5">Buổi sáng</td>
            @endif

            @if($index == 6)
                <td rowspan="4">Buổi chiều</td>
            @endif
            
            <td style="text-align: left">{{ $index }} </td>
            @foreach(['mon', 'tue', 'wed', 'thu', 'fri', 'sat'] as $date)
                @if($date == 'mon' && $index == 1)
                    <td>Chào cờ</td>
                @else
                    <td>
                       @php 
                            $classLesson = $timetable->classLessons->filter(function ($item) use ($date,$index) {
                                return $item->slot == $date.'_'.$index;
                            })->first();
                            if($classLesson) echo $classLesson->classSubject->subject->name." - ".$classLesson->classSubject->class->class_name;
                       @endphp
                    </td>
                @endif
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
