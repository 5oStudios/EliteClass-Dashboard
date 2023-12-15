@extends('admin.layouts.master')
@section('title', __('Wallet Transactions - Admin'))
@section('maincontent')

@component('components.breadcumb', ['secondactive' => 'active'])
    @slot('heading')
        {{ __('Wallet Transactions') }}
    @endslot
    @slot('menu1')
        {{ __('Wallet Transactions') }}
    @endslot
@endcomponent

<!-- Content bar start -->
<div class="contentbar">
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="card-title">{{ __('Wallet Transactions') }}</h5>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <div class="row">
                      <div class="col-md-4 form-group d-flex align-items-baseline">
                        <label for="wallet_topup" class="mr-2">{{ __('Filter by') }}:</label>
                        <select class="form-control w-75"  id="wallet_topup"><option value="">{{ __('Choose option...') }}</option></select>
                      </div>

                      <div class="col-md-4 form-group d-flex align-items-baseline">
                        <label for="min" class="mr-2">{{ __('Date From') }}:</label>
                        <input type="text" class="form-control w-75 default-date" id="min" name="min" placeholder="YYYY-MM-DD">
                      </div>

                      <div class="col-md-4 form-group d-flex align-items-baseline">
                        <label for="max" class="mr-2">{{ __('To') }}:</label>
                        <input type="text" class="form-control w-75 default-date" id="max" name="max" placeholder="YYYY-MM-DD">
                      </div>
                    </div>
                        <table id="wallet-datatable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                  <th>{{ __('#') }}</th>
                                  <th class="d-none">{{ __('User ID') }}</th>
                                  <th>{{ __('adminstaticword.FirstName') }}</th>
                                  <th class="d-none">{{ __('adminstaticword.LastName') }}</th>
                                  <th class="d-none">{{ __('Email') }}</th>
                                  <th>{{ __('Mobile') }}</th>
                                  <th>{{ __('adminstaticword.Type') }}</th>
                                  <th>{{ __('adminstaticword.Amount') }}</th>
                                  <th>{{ __('adminstaticword.PaymentMethod') }}</th>
                                  <th>{{ __('adminstaticword.Detail') }}</th>
                                  <th class="d-none">{{ __('Reason') }}</th>
                                  <th>{{ __('adminstaticword.Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($wallet_transactions as $key => $wallet)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td class="d-none">
                                            @if (isset($wallet->user))
                                                {{ strip_tags($wallet->user->id) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($wallet->user))
                                                {{ strip_tags($wallet->user->fname) }}
                                            @endif
                                        </td>
                                        <td class="d-none">
                                            @if (isset($wallet->user))
                                                {{ strip_tags($wallet->user->lname) }}
                                            @endif
                                        </td>
                                        <td class="d-none">
                                            @if (isset($wallet->user))
                                                {{ strip_tags($wallet->user->email) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($wallet->user))
                                                {{ strip_tags($wallet->user->mobile) }}
                                            @endif
                                        </td>

                                        <td>{{ strip_tags($wallet->type) }}</td>

                                        <td>

                                            @if ($gsetting['currency_swipe'] == 1)
                                                <i
                                                    class="{{ $wallet->currency_icon }}"></i>{{ strip_tags($wallet->total_amount) }}
                                            @else
                                                {{ strip_tags($wallet->total_amount) }}

                                                <i class="{{ $wallet->currency_icon }}"></i>
                                            @endif

                                        </td>

                                        <td>{{ strip_tags($wallet->payment_method) }}</td>

                                        <td>{{ strip_tags($wallet->detail) }}</td>

                                        <td class="d-none">{{ strip_tags($wallet->reason) }}</td>

                                        <td>{{ getUserTimeZoneDateTime($wallet->created_at) }}</td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End col -->
    </div>
    <!-- End row -->
</div>
<!-- Content bar end -->
@endsection

@section('script')
<script>
  var minDate, maxDate;
  
  $(function() {

    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var min = minDate.val();
            var max = maxDate.val();
            var date = new Date(data[11]);
    
            if (
                ( min === null && max === null ) ||
                ( min === null && date <= max ) ||
                ( min <= date   && max === null ) ||
                ( min <= date   && date <= max )
            ) {
                return true;
            }
            return false;
        }
    );

    // Create date inputs
    minDate = new DateTime($('#min'), {
        format: 'YYYY-MM-DD'
    });
    maxDate = new DateTime($('#max'), {
        format: 'YYYY-MM-DD'
    });

    // Setup - add a text input to each footer cell
    // $('#wallet-datatable thead tr')
    //     .clone(true)
    //     .addClass('filters')
    //     .appendTo('#wallet-datatable thead');

    var table = $('#wallet-datatable').DataTable({

      language: {
        searchPlaceholder: "Search transaction here"
      },

      // scrollX: true,
      // orderCellsTop: true,
      // fixedHeader: true,

      // initComplete: function() {
      //   var api = this.api();

      //   // For each column
      //   api.columns().eq(0).each(function(colIdx) {
      //     // Set the header cell to contain the input element
      //     var cell = $('.filters th').eq(
      //         $(api.column(colIdx).header()).index()
      //     );
      //     var title = $(cell).text();
      //     $(cell).html('<input class="form-control" type="text" placeholder="' +
      //         title + '" />');

      //     // On every keypress in this input
      //     $('input', $('.filters th').eq($(api.column(colIdx).header()).index()))
      //       .off('keyup change')
      //       .on('keyup change', function(e) {
      //         e.stopPropagation();

      //         // Get the search value
      //         $(this).attr('title', $(this).val());
      //         var regexr =
      //         '({search})'; //$(this).parents('th').find('select').val();

      //         var cursorPosition = this.selectionStart;
      //         // Search the column for that value
      //         api.column(colIdx).search(
      //                 this.value != '' ?
      //                 regexr.replace('{search}', '(((' + this.value +
      //                     ')))') :
      //                 '',
      //                 this.value != '',
      //                 this.value == ''
      //             )
      //             .draw();

      //         $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
      //       });
      //   });

      // },

      initComplete: function (settings, json) {
        // Add select filter
        wallet_topup = [{'TopUp to wallet by Admin': 'Wallet Credit'}, {'Removed amount from wallet by Admin': 'Wallet Debit'}];
        for (var key in wallet_topup) {
            var obj = wallet_topup[key];
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop)) {
                    $('#wallet_topup').append('<option value="' + prop + '">' + obj[prop] + '</option>');
                }
            }
        }

        // Filter results on select change
        $('#wallet_topup').on('change', function () {
          table.column(9).search($(this).val()).draw();
        });
      },

      columnDefs: [
        {
          target: 1,
          visible: false,
          searchable: false,
        },
        {
          target: 2,
          visible: false,
          searchable: false,
        },
        {
          target: 4,
          visible: false,
          searchable: false,
        },
        {
          target: 10,
          visible: false,
          searchable: false,
        },
      ],

      dom: 'lBfrtip',
      buttons: [
          // 'copy',
          // {
          //   extend: 'print',
          //   exportOptions: {
          //         columns: [1,2,4,7,8]
          //     }
          // },

          // {
          //   extend:    'print',
          //   text:      '<i class="fa fa-print"></i> Print',
          //   titleAttr: 'Print',
          //   className: 'btn btn-default btn-sm',
          //   exportOptions: {
          //     columns: [1,2,4,7,8]
          //   }
          // },
          {
            extend:    'excel',
            text:      '<i class="fa fa-files-o"></i> Excel',
            titleAttr: 'Excel',
            className: 'btn btn-default btn-sm',
            exportOptions: {
              columns: [1,2,3,4,5,6,7,10,11]
            }
          },
          // 'csv',
          // 'pdf',
      ],

    });

    $("#min")
      .datepicker({
        onSelect: function(dateText) {
          this.value = dateText;
          minDate.val(dateText);

          table.draw();
        }
    });

    $("#max")
      .datepicker({
        onSelect: function(dateText) {
          this.value = dateText;
          maxDate.val(dateText);

          table.draw();
        }
    });

    // Refilter the table
    // $('#min, #max').on('change', function () { 
    //   table.draw();
    // });
  });
</script>
@endsection
