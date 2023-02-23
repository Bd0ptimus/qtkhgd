<table>
    <tr></tr>
    <tr>
        <td align="center" valign="center" colspan="9">Cộng hòa xã hội chủ nghĩa Việt Nam</td>
    </tr>
    <tr>
        <td align="center" valign="center" colspan="9">Độc lập - Tự do - Hạnh phúc</td>
    </tr>
    @for($i=0;$i<11;$i++)
        <tr></tr>
    @endfor
    <tr>
        <td align="center" valign="center" colspan="9">
            SỔ THEO DÕI TỔNG HỢP<br>
            {{ $title }}
        </td>
    </tr>
    @for($i=0;$i<18;$i++)
        <tr></tr>
    @endfor
    <tr>
        <td></td>
        <td valign="center" colspan="9">
            Trường: {{ $school->school_name }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td valign="center" colspan="9">
            Xã/phường/huyện/quận:
            {{ optional($school->ward)->name }}/{{ optional(optional($school->ward)->district)->name }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td valign="center" colspan="9">
            Tỉnh/thành phố: {{ optional(optional(optional($school->ward)->district)->province)->name }}
        </td>
    </tr>
</table>