<table>
    @include('exports.heading')

    {{-- Body--}}
    <tr>
        <td>STT</td>
        <td>Quyền hạn</td>
        <td>Tên tài khoản</td>
        <td>Tài khoản</td>
    </tr>
    @php
        $stt = 0;
    @endphp
    @foreach($school->users as $user)
        @php
            $stt++;
        @endphp
        <tr>
            <td>{{ $stt }}</td>
            <td>{{ $user->roles->implode('name', ', ') }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->username }}</td>
        </tr>
    @endforeach

    @foreach($school->classes as $index => $class)
        @if(count($class->teachers) > 0)
            @foreach($class->teachers as $user)
                @php
                    $stt++;
                @endphp
                <tr>
                    <td>{{ $stt }}</td>
                    <td>Tài khoản giáo viên</td>
                    <td>{{ $user->name }} - Lớp {{ $class->class_name }} </td>
                    <td>{{ $user->username }}</td>
                </tr>
            @endforeach
        @endif
    @endforeach
</table>