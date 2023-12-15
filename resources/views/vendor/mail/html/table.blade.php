<table class="text-center border-0" style="background-color: #CCCCCC" col="2">
    <tr>
        <td colspan="2" class="text-center" class="msg"><strong class="text">Transaction
                Details</strong></td>
    </tr>
    <tr>
        <td>Result Code :</td>
        @if ($data['Result'] != 'CAPTURED')
            <td class="text-danger">{{ $data->Result ?? '' }}</td>
        @else
            <td>{{ $data['Result'] ?? '' }}</td>
        @endif
    </tr>
    <tr>
        <td>Post Date :</td>
        <td>{{ $data['PostDate'] ?? '' }}</td>
    </tr>
    <tr>
        <td width=26%>Payment ID :</td>
        <td width=74%>{{ $data['PaymentID'] ?? '' }}</td>
    </tr>
    <tr>
        <td>Track ID :</td>
        <td>{{ $data['TrackID'] ?? '' }}</td>
    </tr>
    <tr>
        <td>Amount :</td>
        <td>{{ json_decode($data['trnUdf'])->amt ?? '' }}</td>
    </tr>
</table>
