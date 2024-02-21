@extends('admin.layouts.master')
@section('title', 'Create a new live stream - Admin')

@section('maincontent')
    @component('components.breadcumb', ['fourthactive' => 'active'])
        @slot('heading')
            {{ __('List all Live Streamings') }}
        @endslot
        @slot('menu1')
            {{ __('Live Streamings') }}
        @endslot
        @slot('menu2')
            {{ __('Big Blue') }}
        @endslot
        @slot('menu3')
            {{ __(' List all Live Streamings') }}
        @endslot
        @slot('button')
            <div class="col-md-5 col-lg-5">
                <div class="widgetbar">
                    <a href="{{ url('bigblue/meetings') }}" class="btn btn-primary-rgba"><i
                            class="feather icon-arrow-left mr-2"></i>{{ __('Back') }}</a>

                </div>
            </div>
        @endslot
    @endcomponent

    <div class="contentbar">
        <div class="row">
            <div class="col-lg-12">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}<button type="button" class="close" data-dismiss="alert"
                                    aria-label="Close">
                                    <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                        @endforeach
                    </div>
                @endif
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Create new live streaming') }}</h5>
                    </div>
                    <div class="card-body">

                        <form autocomplete="off" action="{{ route('link.meeting.create') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                               
                            <input type="hidden" name="meeting_id" value="{{ $meeting_id }}" />
                                <div  class="form-group col-md-6 ">
                                    <label>{{ __('adminstaticword.Courses') }}:<span class="redstar">*</span></label>
                                    <select  name="course_id" id="course_id"  class="select2 form-control">
                                    <option disabled selected>Please select Course</option>
                                        @foreach ($course as $cor)
                                            <option value="{{ $cor->id }}" data-installments="{{ json_encode($cor->installments) }}">{{ $cor->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div  class="form-group col-md-6 " id="installment_container">
                                    <label id="installment_label">{{ __('adminstaticword.Installment') }}:<span class="redstar">*</span></label>
                                    <select name="unlock_installment" id="installment" class="select form-control">
                                        <!-- Options will be dynamically populated using JavaScript -->
                                    </select>
                                </div>
                                
                                <div class="form-group col-md-6">
                                <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}: <sup
                                                class="redstar">*</sup></label>
                                        <input type="number" step="1" min="0" required
                                             class="form-control"
                                            name="price" id="priceMain"
                                            placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Price') }}"
                                            value="{{ old('price') ?? 0 }}">

                                </div>

                                <div class="form-group col-md-6">
                                <label for="discount_type">{{ __('discount_type') }}</label>
                                        <select name="discount_type" id="discount_type" class="form-control js-example-basic-single ">
                                            <option value="none" selected disabled>
                                                {{ __('frontstaticword.SelectanOption') }}
                                            </option>
                                            <option value="percentage">{{ __('percentage') }}</option>
                                            <option value="fixed">{{ __('fixed') }}</option>
                                        </select>
                                </div>
                                <div class="form-group col-md-6">
                                <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}: <sup class="redstar">*</sup>
                                            <small class="text-muted"><i class="fa fa-question-circle"></i>
                                                {{ __('Discounted price Zero(0) consider as no discount') }}
                                            </small>
                                        </label>

                                        <div class="input-group">
                                            <input type="number" step="0.1" min="0" required class="form-control" name="discount_price" id="offerPrice"
                                                placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.DiscountPrice') }}"
                                                value="{{ old('discount_price') ?? 0 }}" />

                                            <div class="input-group-append">
                                                <span class="input-group-text" id="prefix">
                                                    @if(old('discount_type') == 'percentage')
                                                        %
                                                    @elseif(old('discount_type') == 'fixed')
                                                        KWD
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                </div>                          
                            </div>
                            <button type="reset" class="btn btn-danger-rgba"><i class="fa fa-ban"></i>
                                {{ __('Reset') }}</button>
                            <button type="submit" class="btn btn-primary-rgba"><i class="fa fa-check-circle"></i>
                                {{ __('Create') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        (function($) {
            "use strict";

            function updatePrefix() {

            var discountType = document.getElementById('discount_type').value;
            var prefixElement = document.getElementById('prefix');
            if (discountType === 'percentage') {
                prefixElement.textContent = '%';
            } else if (discountType === 'fixed') {
                prefixElement.textContent = 'KWD';
            } else {
                prefixElement.textContent = '';
            }
            };
            $("#course_id").change(updateInstallments);

    function updateInstallments() {
        // Get the selected course ID
        var selectedCourseId = $("#course_id").val();

        // Get the selected course's installments
        var installments = $("#course_id option:selected").data("installments");

        // Get the installment select element
        var installmentSelect = $("#installment");
        var installmentContainer = $('#installment_container')
        // Clear existing options
        installmentSelect.empty();

        // Check if installments array is not empty
        if (installments?.length > 0) {
            // Show the installment select
            installmentContainer.show()
            // Populate options based on the selected course's installments
            $.each(installments, function (index, installment) {
                var option = $("<option>").val(installment.sort).text(installment.sort);
                installmentSelect.append(option);
            });

            // Make the installmentSelect required
            installmentSelect.prop('required', true);
        } else {
            // Hide the installment select if the array is empty
            installmentContainer.hide()
            // Make the installmentSelect not required
            installmentSelect.prop('required', false);
        }
    }
// Add an event listener to the discount type select element
$('#discount_type').change(()=>{
updatePrefix()            })



// Initial call to set the prefix based on the default selected value
updatePrefix();
           
            
          
        })(jQuery);
    </script>
@endsection
