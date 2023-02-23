<table>
    @include('exports.heading')

    {{-- Body--}}
    <tr>
        <td>STT</td>
        <td>Tên đơn vị quản lý</td>
        <td>Quyền hạn</td>
        <td>Tên tài khoản</td>
        <td>Tài khoản</td>
    </tr>
    @php
        $stt = 0;
    @endphp
    @foreach($district->users as $user)
        @php
            $stt++;
        @endphp
        <tr>
            <td>{{ $stt }}</td>
            <td>{{ $district->name }}</td>
            <td>{{ $user->roles->implode('name', ', ') }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->username }}</td>
        </tr>
    @endforeach
    @foreach($schools as $school)
        @foreach($school->users as $user)
            <tr>
                @if ($loop->first)
                    @php
                        $stt++;
                        $rowCount = count($school->users);
                    @endphp
                    <td rowspan="{{ $rowCount }}">{{ $stt }}</td>
                    <td rowspan="{{ $rowCount }}">{{ $school->school_name }}</td>
                @endif
                <td>{{ $user->roles->implode('name', ', ') }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->username }}</td>
            </tr>
        @endforeach
    @endforeach
</table>