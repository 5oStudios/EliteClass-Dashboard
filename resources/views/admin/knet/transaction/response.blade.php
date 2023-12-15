<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="pragma" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link href="{{ url('admin_assets/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <title>Knet Payment Response</title>

</head>

<body>
    <div>
        <table class="text-center" width="100%" cellspacing="1" cellpadding="1">
            <tr>
                <td class="heading text-center"><strong>Elite-Class Payment
                        Status</strong>
                </td>
            </tr>
            
            @if ($errorNo != null || $errorText != null)
            <tr>
                <td width=26% class="text-right">Error :</td>
                <td width=74% class="tdwhite">{{ "$data->Error - $data->ErrorText" }}</td>
            </tr>
            @endif

            <tr>
                <td class="text-center" class="msg">
                    @if ($data->result != 'CAPTURED')
                        <em class="text-danger">Payment Failed</em>
                    @else
                        Purchased Successfully<br>
                        Thank You For Your Order
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    <table class="w-50 mx-auto border-0" cellpadding="0" cellspacing="1"style="background-color: #CCCCCC"
                        col="2">
                        <tr>
                            <td colspan="2" class="text-center" class="msg"><strong class="text">Transaction
                                    Details</strong></td>
                        </tr>
                        <tr>
                            <td class="text-right w-50 pr-2">Customer Name:</td>
                            <td class="w-50 text-left pl-2">{{ $data->udf1 ?? '' }} </td>
                        </tr>
                        <tr>
                            <td class="text-right w-50 pr-2">Post Date:</td>
                            <td class="w-50 text-left pl-2">{{ $data->postdate ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-right w-50 pr-2">Result Code:</td>
                            @if ($data->result != 'CAPTURED')
                                <td class="text-danger w-50">{{ $data->result ?? '' }}</td>
                            @else
                                <td class="w-50 text-left pl-2">{{ $data->result ?? '' }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td width=26% class="text-right w-50 pr-2">Payment ID:</td>
                            <td width=74% class="w-50 text-left pl-2">{{ $data->paymentid ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-right w-50 pr-2">Track ID:</td>
                            <td class="w-50 text-left pl-2">{{ $data->trackid ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-right w-50 pr-2">Amount:</td>
                            <td class="w-50 text-left pl-2">{{ $data->amt ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-right w-50 pr-2">&nbsp; </td>
                            <td class="w-50 text-left pl-2">

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    {{-- @if ($data->result == 'NOT CAPTURED' || $data->result == 'CAPTURED') --}}
                    @if ($data->result == 'CAPTURED')
                        <a class="btn text-white" style="background-color: #82B1CF"
                            href="{{ config('app.front-end-url') . '/user/success?success=1&message=Purchased successfully' }}">Continue</a>
                    @else
                        <a class="btn text-white" style="background-color: #82B1CF"
                            href="{{ config('app.front-end-url') . '/user/cart?success=0&message=Payment failed' }}">Continue</a>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
