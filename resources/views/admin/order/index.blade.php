

    <div class="row">
        
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="box-title">{{ __('All Order') }}</h5>
                </div>
                <div class="card-body">
                
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('adminstaticword.User') }}</th>

                                    <th>{{ __('Payment') }} {{ __('adminstaticword.Detail') }}</th>

                                    <!-- <th>{{ __('adminstaticword.Status') }}</th> -->

                                    <th>{{ __('adminstaticword.Unenroll') }}</th>
                                    <th>{{ __('adminstaticword.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($orders as $order)
                                <?php $i++; ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td>

                                        <p><b>{{ __('adminstaticword.User') }}</b>:
                                            @if(Auth::user()->role == 'admin')
                                            @if(isset($order->user))
                                            {{ $order->user['fname'] }} {{ $order->user['lname'] }}
                                            @endif
                                            @else
                                            @if ($gsetting->hide_identity == 0)
                                            @if(isset($order->user))
                                            {{ $order->user['fname'] }} {{ $order->user['lname'] }}
                                            @endif
                                            @else
                                            {{ __('Hidden') }}
                                            @endif
                                            @endif
                                        </p>
                                        <p>
                                            @if ($order->course_id != null)
                                            <b>{{ __('adminstaticword.Course') }}</b>:
                                            {{ optional($order->courses)['title'] }}
                                            @elseif ($order->meeting_id != null)
                                            <b>{{ __('adminstaticword.Meeting') }}</b>:
                                            {{ optional($order->meeting)['meetingname'] }}
                                            @else
                                            <b>{{ __('adminstaticword.Bundle') }}</b>:
                                            {{ optional($order->bundle)['title'] }}
                                            @endif
                                        </p>

                                    </td>

                                    <td>
                                        <p><b>{{ __('adminstaticword.TransactionId') }}</b>:
                                            {{ $order->transaction_id }}</p>
                                        <p><b>{{ __('adminstaticword.PaymentMethod') }}</b>:
                                            {{ $order->payment_method }}</p>

                                        @php
                                            $contains = Illuminate\Support\Str::contains($order->currency_icon, 'fa');
                                        @endphp

                                            <b>{{ __('adminstaticword.TotalAmount') }}</b>:

                                            @if ($order->coupon_discount == !null)

                                                @if($contains)

                                                    <i class="{{ $order->currency_icon }}"></i>{{ $order->total_amount - $order->coupon_discount }}

                                                @else

                                                    {{ $order->currency_icon }} {{ $order->total_amount - $order->coupon_discount }}

                                                @endif

                                            @else

                                                @if($contains)

                                                    <i class="fa {{ $order->currency_icon }}"></i>{{ $order->total_amount }}
                                                
                                                @else
                                                    {{ $order->currency_icon }}

                                                    {{ $order->total_amount }}

                                                @endif

                                            @endif
                                        </p>

                                    </td>

                                    <!-- <td>
                                        <label class="switch">
                                        <input class="orders" type="checkbox"  data-id="{{$order->id}}" name="status" {{ $order->status == '1' ? 'checked' : '' }}>
                                        <span class="knob"></span>
                                      </label>

                                    </td> -->

                                    <td>
                                        @if ($order->subscription_status === 'active')
                                        <form method="post"
                                            action="{{ route('stripe.cancelsubscription', ['order_id' => $order->id, 'redirect_to' => '/order']) }}"
                                            data-parsley-validate class="form-horizontal form-label-left">
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-danger btn-xs">
                                                <i class="fa fa-fw fa-close"></i>
                                                {{ __('adminstaticword.Unenroll') }}
                                            </button>
                                        </form>
                                        @else
                                        -
                                        @endif

                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-round btn-outline-primary" type="button"
                                                id="CustomdropdownMenuButton1" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"><i
                                                    class="feather icon-more-vertical-"></i></button>
                                            <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
                                                <a class="dropdown-item" href="{{ route('view.order', $order->id) }}"><i
                                                        class="feather icon-eye mr-2"></i>{{ __('View') }}</a>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@section('script')
<script>
  $(function() {
    $(document).on("change",".orders",function() {
        
        $.ajax({
            type: "GET",
            dataType: "json",
            url: 'order-status',
            data: {'status': $(this).is(':checked') ? 1 : 0, 'id': $(this).data('id')},
            success: function(data){
                var warning = new PNotify( {
                title: 'success', text:'Status Update Successfully', type: 'success', desktop: {
                desktop: true, icon: 'feather icon-thumbs-down'
                }
            });
                warning.get().click(function() {
                    warning.remove();
                });
            }
        });
    })
   });
</script>
@endsection
