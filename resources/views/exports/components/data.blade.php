<table>
    @foreach($dataRecords as $dataRecord)
        <tr>
            <td><b>{{ $dataRecord['field'] }}</b></td>
            @foreach($dataRecord['data'] as $data)
                <td>{{ $data }}</td>
            @endforeach
        </tr>
    @endforeach
</table>