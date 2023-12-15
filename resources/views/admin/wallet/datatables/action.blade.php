<div class="dropdown">
    <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu {{ strtolower($type) == 'credit' ? 'd-none' : '' }}" aria-labelledby="CustomdropdownMenuButton1">
        <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#transactionDetail{{ $id }}">
            <i class="feather icon-eye mr-2"></i>{{ __('Detail') }}</a>
        </a>
    </div>
</div>

@if(strtolower($type) == 'debit')
@php
    if (strtolower($detail) == 'installment paid') {
        $orders = \App\OrderInstallment::query()
            ->select('id', 'user_id', 'order_id', 'transaction_id', 'total_amount', 'coupon_id', 'coupon_discount')
            ->where('transaction_id', $id)
            ->with([
                'order' => function ($query) {
                    $query->with(['instructor', 'courses', 'chapter', 'bundle', 'meeting', 'offlinesession']);
                },
            ])
            ->get();
    } else {
        $orders = \App\Order::query()
            ->select('id', 'title', 'user_id', 'instructor_id', 'installments', 'course_id', 'chapter_id', 'bundle_id', 'chapter_id', 'meeting_id', 'offline_session_id', 'transaction_id', 'currency_icon', 'total_amount', 'paid_amount', 'coupon_id', 'coupon_discount', 'deleted_at')
            ->where('transaction_id', $id)
            ->with(['instructor', 'courses', 'chapter', 'bundle', 'meeting', 'offlinesession'])
            ->withTrashed()
            ->get();
    }
@endphp

<!-- Transaction Detail Modal start -->
<div class="modal" id="transactionDetail{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleSmallModalLabel">{{ __('Transaction Detail') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('#') }}</th>
                            <th>{{ __('adminstaticword.Instructor') }}</th>
                            <th>{{ __('adminstaticword.Items') }}</th>
                            <th>{{ __('adminstaticword.TotalAmount') }}</th>
                            <th>{{ __('adminstaticword.PaidAmount') }}</th>
                            <th>{{ __('adminstaticword.CouponDiscount') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if (strtolower($detail) == 'installment paid')
                            @foreach ($orders as $key => $installmentPaid)
                                <tr>
                                    <td>
                                        {{ $key + 1 }}
                                    </td>
                                    <td>
                                        {{ $installmentPaid?->order?->instructor?->fname }}
                                        {{ $installmentPaid?->order?->instructor?->lname }}
                                    </td>
                                    <td>
                                        @if ($installmentPaid?->order?->course_id)
                                            <b>{{ __('adminstaticword.Course') }}:</b>
                                            {{ $installmentPaid?->order?->courses?->title }}
                                        @elseif($installmentPaid?->order?->bundle_id)
                                            <b>{{ __('adminstaticword.BundleName') }}:</b>
                                            {{ $installmentPaid?->order?->bundle?->title }}
                                        @elseif($installmentPaid?->order?->meeting_id)
                                            <b>{{ __('adminstaticword.Meeting') }}:</b>
                                            {{ $installmentPaid?->order?->meeting?->meetingname }}
                                        @elseif($installmentPaid?->order?->offline_session_id)
                                            <b>{{ __('In-Person Session') }}:</b>
                                            {{ $installmentPaid?->order?->offlinesession?->title }}
                                        @elseif($installmentPaid?->order?->chapter_id)
                                            <b>{{ __('Chapter') }}:</b>
                                            {{ $installmentPaid?->order?->chapter?->chapter_name }}
                                        @endif
                                    </td>
                                    <td>{{ $installmentPaid?->total_amount + $installmentPaid?->coupon_discount }}
                                    </td>
                                    <td>{{ $installmentPaid?->total_amount }}</td>
                                    <td>{{ $installmentPaid?->coupon_discount != null ? $installmentPaid?->coupon_discount : __('0.000') }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @foreach ($orders as $key => $order)
                                <tr>
                                    <td class="{{ isset($order->deleted_at) ? 'text-danger' : '' }}">
                                        {{ $key + 1 }}
                                    </td>
                                    <td class="{{ isset($order->deleted_at) ? 'text-danger' : '' }}">
                                        {{ $order?->instructor?->fname }} {{ $order?->instructor?->lname }}
                                    </td>
                                    <td class="{{ isset($order->deleted_at) ? 'text-danger' : '' }}">
                                        @if ($order?->course_id)
                                            <b>{{ __('adminstaticword.Course') }}:</b>
                                            {{ $order?->courses?->title }}
                                        @elseif($order?->bundle_id)
                                            <b>{{ __('adminstaticword.BundleName') }}:</b>
                                            {{ $order?->bundle?->title }}
                                        @elseif($order?->meeting_id)
                                            <b>{{ __('adminstaticword.Meeting') }}:</b>
                                            {{ $order?->meeting?->meetingname }}
                                        @elseif($order?->offline_session_id)
                                            <b>{{ __('In-Person Session') }}:</b>
                                            {{ $order?->offlinesession?->title }}
                                        @elseif($order?->chapter_id)
                                            <b>{{ __('Chapter') }}:</b> {{ $order?->chapter?->chapter_name }}
                                        @endif
                                    </td>
                                    <td class="{{ isset($order->deleted_at) ? 'text-danger' : '' }}">{{ $order?->total_amount }}</td>
                                    @if ($order?->installments == 1)
                                        <td class="{{ isset($order->deleted_at) ? 'text-danger' : '' }}">{{ $order->payment_plan->sum('amount') }}
                                        </td>
                                        <!-- @foreach ($order->payment_plan as $plan)
                                            $paidInstallment = ($plan->order_installment_id && $plan->wallet_trans_id == $transaction->id) ? $plan->paidInstallment : null;
                                            $couponDiscount += ($paidInstallment && $paidInstallment->coupon_discount != null) ? $paidInstallment->coupon_discount : 0;
                                        @endforeach -->
                                        <td class="{{ isset($order->deleted_at) ? 'text-danger' : '' }}">{{ $couponDiscount ?? __('0.000') }}
                                        </td>
                                    @else
                                        <td class="{{ isset($order->deleted_at) ? 'text-danger' : '' }}">{{ $order?->paid_amount }}</td>
                                        <td class="{{ isset($order->deleted_at) ? 'text-danger' : '' }}">{{ $order?->coupon_discount != null ? $order?->coupon_discount : __('0.000') }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> <!-- Transaction Detail Model ended -->
@endif


<style>
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: transparent !important;
    }
</style>