<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style>
        * {
            font-family: "Times New Roman";
        }
        table tbody tr {
            text-align: center;
        }
        table tbody tr td {
            font-size: 12px;
            border: 1px solid #000000;
        }
    </style>
</head>
<body>
<div class="container">
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td colspan="9">
                <strong>Tuần {{$datas['week']}}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="2">Thời gian</td>
            <td>Ngày/tháng</td>
            <td>Ngày/tháng</td>
            <td>Ngày/tháng</td>
            <td>Ngày/tháng</td>
            <td>Ngày/tháng</td>
            <td>Ngày/tháng</td>
            <td colspan="2" rowspan="2">Điều chỉnh kế hoạch tuần</td>
        </tr>
        <tr>
            <td>Buổi</td>
            <td>Tiết học</td>
            <td>Thứ 2</td>
            <td>Thứ 3</td>
            <td>Thứ 4</td>
            <td>Thứ 5</td>
            <td>Thứ 6</td>
            <td>Thứ 7</td>
        </tr>
        @if(isset($datas['sang']))
            @foreach($datas['sang'] as $key => $dt)
                <tr>
                    @if($loop->first)
                        <td rowspan="{{count($datas['sang'])}}">Sáng</td>
                    @endif
                    <td>{{$key + 1}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @if($loop->first)
                        <td rowspan="{{$datas['row_num']}}"></td>
                    @endif
                </tr>
            @endforeach
        @endif
        @if(isset($datas['chieu']))
            @foreach($datas['chieu'] as $key => $dt)
                <tr>
                    @if($loop->first)
                        <td rowspan="{{count($datas['chieu'])}}">Chiều</td>
                    @endif
                    <td>{{$key + 1}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        @endif
        <tr>
            <td colspan="2">Tổng số tiết/tuần</td>
            <td colspan="6"></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="9">TỔNG HỢP</td>
        </tr>
        <tr>
            <td>STT</td>
            <td colspan="3">Nội dung</td>
            <td colspan="2">Số tiết</td>
            <td colspan="3">Ghi chú</td>
        </tr>
        @if(isset($datas['total']))
            @foreach($datas['total'] as $key => $dt)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td colspan="3">{{$dt['name']}}</td>
                    <td colspan="2">{{$dt['lesson']}}</td>
                    <td colspan="3">{{$dt['note']}}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>
</body>
</html>