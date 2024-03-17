<div class="row">
    <div class="col-lg-12">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true" style="color:red;">&times;</span></button></p>
                @endforeach
            </div>
        @endif
        <div class="card m-b-30">
            <div class="card-header">
                <h5 class="card-box">{{ __('adminstaticword.Edit') }} {{ __('adminstaticword.Course') }}</h5>
            </div>
            <div class="card-body ml-2">
                <form autocomplete="off" action="{{ route('course.update', $cor->id) }}" method="post"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}

                    <div class="row">
                        <div class="col-md-6">
                            <label>{{ __('adminstaticword.Category') }}<span class="redstar">*</span></label>
                            <select name="category_id" id="category_id" class="form-control js-example-basic-single"
                                required>
                                <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                @php
                                    $category = App\Categories::where('status', true)->get();
                                @endphp
                                @foreach ($category as $caat)
                                    <option {{ $cor->category_id == $caat->id ? 'selected' : '' }}
                                        value="{{ $caat->id }}">{{ $caat->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('adminstaticword.TypeCategory') }}:<span class="redstar">*</span></label>
                            <select name="scnd_category_id" id="type_id" class="form-control js-example-basic-single"
                                required>
                                <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                @php
                                    $typecategory = App\secondaryCategory::where('status', true)
                                        ->where('category_id', $cor->category_id)
                                        ->get();
                                @endphp
                                @foreach ($typecategory as $caat)
                                    <option {{ $cor->scnd_category_id == $caat->id ? 'selected' : '' }}
                                        value="{{ $caat->id }}">{{ $caat->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <label>{{ __('adminstaticword.SubCategory') }}:<span class="redstar">*</span></label>
                            <select name="subcategory_id" id="upload_id" class="form-control js-example-basic-single"
                                required>
                                <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                @php
                                    $subcategory = App\SubCategory::where('status', true)
                                        ->where(['category_id' => $cor->category_id, 'scnd_category_id' => $cor->scnd_category_id])
                                        ->get();
                                @endphp
                                @foreach ($subcategory as $caat)
                                    <option {{ $cor->subcategory_id == $caat->id ? 'selected' : '' }}
                                        value="{{ $caat->id }}">{{ $caat->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('adminstaticword.ChildCategories') }}:<span class="redstar">*</span></label>
                            <select name="childcategory_id[]" id="grand" class="form-control select2"
                                multiple="mulitple" required>
                                <option value="">{{ __('adminstaticword.SelectanOption') }}</option>

                                @php
                                    $childcategory = App\ChildCategory::where('status', true)
                                        ->where(['category_id' => $cor->category_id, 'scnd_category_id' => $cor->scnd_category_id, 'subcategory_id' => $cor->subcategory_id])
                                        ->get();
                                @endphp

                                @foreach ($childcategory as $caat)
                                    @if (is_array($cor['childcategory_id']) || is_object($cor['childcategory_id']))
                                        <option value="{{ $caat->id }}"
                                            {{ in_array($caat->id, $cor['childcategory_id'] ?: []) ? 'selected' : '' }}>
                                            {{ $caat->title }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        @if (Auth::user()->role == 'admin')
                            <div class="col-md-6">
                                <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<sup
                                        class="redstar">*</sup></label>
                                <select name="user_id" class="form-control js-example-basic-single col-md-7 col-xs-12"
                                    required>
                                    <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                    @foreach ($users as $user)
                                        <option {{ $cor->user_id == $user->id ? 'selected' : '' }}
                                            value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        @endif
                        @if (Auth::user()->role == 'instructor')
                            <div class="col-md-6">
                                <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<sup
                                        class="redstar">*</sup></label>
                                <input type="text" class="form-control"
                                    value="{{ $users->fname }} {{ $users->lname }}" readonly>
                                <input type="hidden" class="form-control" name="user_id" value="{{ $users->id }}">

                            </div>
                        @endif
                    </div>
                    <br>

                    {{-- <div class="col-md-4"> 
                            @php
                                $ref_policy = App\RefundPolicy::all();
                            @endphp
                            <label for="exampleInputSlug">{{ __('adminstaticword.SelectRefundPolicy') }}</label>
                            <select name="refund_policy_id" class="form-control js-example-basic-single col-md-7 col-xs-12">
                                <option value="none" selected disabled hidden> 
                                    {{ __('frontstaticword.SelectanOption') }}
                                </option>
                                @foreach ($ref_policy as $ref)
                                <option {{ $cor->refund_policy_id == $ref->id ? 'selected' : "" }} value="{{ $ref->id }}">{{ $ref->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    {{-- @if (Auth::User()->role == 'admin')
                        <div class="col-md-4">
                            <label>{{ __('Institute') }}: <span class="redstar">*</span></label>
                            <select name="institude_id" class="form-control select2">
                                @php
                                $institute = App\Institute::all();
                                @endphp
                                <option value="0"  disabled hidden> 
                                    {{ __('adminstaticword.SelectanOption') }}
                                </option>  
                                @foreach ($institute as $inst)
                                <option value="{{ $inst->id }}" {{$inst->id  == $cor->institude_id ? 'selected' : ''}}>{{ $inst->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if (Auth::User()->role == 'instructor')
                        <div class="col-md-4">
                            <label>{{ __('Institute') }}: <span class="redstar">*</span></label>
                            <select name="institude_id" class="form-control select2">
                                @php
                                $institute = App\Institute::where('user_id',Auth::user()->id)->get();
                                @endphp
                                <option value="0" disabled hidden> 
                                    {{ __('adminstaticword.SelectanOption') }}
                                </option>  
                                @foreach ($institute as $inst)
                                <option  value="{{ $inst->id }}" {{$inst->id  == $cor->institude_id ? 'selected' : ''}}>{{ $inst->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif --}}

                    <div class="row">
                        <div class="col-md-12">
                            <label for="exampleInputTit1e">{{ __('adminstaticword.CourseName') }}:<sup
                                    class="redstar">*</sup></label>
                            <input type="text" class="form-control" name="title" id="exampleInputTitle"
                                value="{{ $cor->title }}" required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="exampleInputTit1e">{{ __('adminstaticword.CourseWhtsapGrupLink') }}:</label>
                            <input type="text" class="form-control" name="wtsap_link" id="wtsap_link"
                                value="{{ $cor->wtsap_link }}">
                        </div>
                    </div>
                    <br>

                    {{-- <div class="row">
                        <div class="col-md-6">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Requirements') }}:<sup class="redstar">*</sup></label>
                            <textarea name="requirement" rows="3" class="form-control" required >{!! $cor->requirement !!}</textarea>
                        </div>
                    </div>
                    <br> --}}

                    <div class="row">
                        {{-- <div class="col-md-6"> 
                            <label for="exampleInputSlug">{{ __('Level/Type Tags') }}</label>
                            <select class="form-control js-example-basic-single" name="level_tags">
                                <option value="0"  disabled hidden> 
                                    {{ __('adminstaticword.SelectanOption') }}
                                </option>
                                <option {{ $cor->level_tags == 'trending' ? 'selected' : ''}} value="trending">{{ __('Trending') }}</option>
                                <option {{ $cor->level_tags == 'onsale' ? 'selected' : ''}} value="onsale">{{ __('Onsale') }}</option>
                                <option {{ $cor->level_tags == 'bestseller' ? 'selected' : ''}} value="bestseller">{{ __('Bestseller') }}</option>
                                <option {{ $cor->level_tags == 'beginner' ? 'selected' : ''}} value="beginner">{{ __('Beginner') }}</option>
                                <option {{ $cor->level_tags == 'intermediate' ? 'selected' : ''}} value="intermediate">{{ __('Intermediate') }}</option>
                                <option {{ $cor->level_tags == 'expert' ? 'selected' : ''}} value="expert">{{ __('Expert') }}</option>
                            </select>
                        </div> --}}
                        <div class="col-md-12">
                            <label for="exampleInputSlug">{{ __('adminstaticword.CourseTags') }}:</label>
                            <select class="select2-multi-select form-control" name="course_tags[]" multiple="multiple"
                                size="5">
                                @if (is_array($cor['course_tags']) || is_object($cor['course_tags']))
                                    @foreach ($cor['course_tags'] as $cat)
                                        <option value="{{ $cat }}"
                                            {{ in_array($cat, $cor['course_tags'] ?: []) ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row @if (Auth::User()->role != 'admin') d-none @endif">
                        <div class="form-group col-md-6">
                            <label>
                                {{ __('Start Date') }}:<sup class="redstar">*</sup>
                            </label>
                            <div class="input-group">
                                <input type="text" required class="form-control datepicker" name="start_date"
                                    placeholder="yyyy-mm-dd" value="{{ $cor->start_date }}"
                                    aria-describedby="basic-addon2">

                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="feather icon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>
                                {{ __('End Date') }}:<sup class="redstar">*</sup>
                            </label>

                            <div class="input-group">
                                <input type="text" required class="form-control datepicker" name="end_date"
                                    placeholder="yyyy-mm-dd" value="{{ $cor->end_date }}"
                                    aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2"><i
                                            class="feather icon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Detail') }}:<sup
                                    class="redstar">*</sup></label>
                            <textarea id="detail" name="detail" rows="3" class="form-control">{!! $cor->detail !!}</textarea>
                        </div>
                    </div>
                    <br>

                    <!-- country start -->
                    {{-- <div class="row">
                        <div class="col-md-12">
                            <label>{{ __('Country') }}: <span></span></label>
                            <select class="select2-multi-select form-control" name="country[]" multiple="multiple">
                                @foreach ($countries as $country)
                                <option {{in_array($country->name, $cor->country ?: []) ? "selected": ""}}  value="{{ $country->name }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-info"><i class="fa fa-question-circle"></i> ({{ __('Select those countries where you want to block courses')}} )</small>
                        </div>
                    </div>
                    <br> --}}
                    <!-- country end -->

                    {{-- <div class="row">
                        <div class="col-md-3 display-none">
                        <label for="exampleInputDetails">{{ __('adminstaticword.MoneyBack') }}:</label><br>
                        <label class="switch">
                            <input class="slider" type="checkbox" id="customSwitch1" name="money" {{ $cor->day != '' ? 'checked' : '' }} />
                        <span class="knob"></span>
                        </label>
                        <br>     

                        <div style="{{ $cor->day == 1 ? '' : 'display:none' }}" id="jeet">
                            <label for="exampleInputSlug">{{ __('adminstaticword.Days') }}:<sup class="redstar">*</sup></label>
                            <input type="number" min="1"  class="form-control" name="day" id="exampleInputPassword1" placeholder="{{ __('adminstaticword.Enter') }} day" value="{{ $cor->day }}">
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-md-3">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Paid') }}:</label><br>
                            <label class="switch">
                                <input class="slider" type="checkbox" id="customSwitch2" name="type"
                                    {{ $cor->type == '1' ? 'checked' : '' }} />
                                <span class="knob"></span>
                            </label>
                            <br>

                            <div style="{{ $cor->type == 1 ? '' : 'display:none' }}" id="doabox">
                                <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}: <sup
                                        class="redstar">*</sup></label>
                                <input step="1" type="text" inputmode="numeric" required
                                    pattern="[-+]?[0-9]*[.,]?[0-9]+" class="form-control" name="price"
                                    id="priceMain"
                                    placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Price') }}"
                                    value="{{ $cor->price ?? 0 }}">
                                    <br>
                                        <label for="discount_type">{{ __('discount_type') }}</label>
                                        <select name="discount_type" id="discount_type" class="form-control js-example-basic-single col-md-7 col-xs-12 mb-2">
                                            <option value="none" disabled {{ ($cor->discount_type ?? null) == null ? 'selected' : '' }}>
                                                {{ __('frontstaticword.SelectanOption') }}
                                            </option>
                                            <option value="percentage" {{ ($cor->discount_type ?? null) == 'percentage' ? 'selected' : '' }}>
                                                {{ __('percentage') }}
                                            </option>
                                            <option value="fixed" {{ ($cor->discount_type ?? null) == 'fixed' ? 'selected' : '' }}>
                                                {{ __('fixed') }}
                                            </option>
                                        </select>


                                        <br>

                                        <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}: <sup class="redstar">*</sup>
                                            <small class="text-muted"><i class="fa fa-question-circle"></i>
                                                {{ __('Discounted price Zero(0) consider as no discount') }}
                                            </small>
                                        </label>

                                        <div class="input-group">
                                            <input type="number" step="0.1" min="0" required class="form-control" name="discount_price" id="offerPrice"
                                                placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.DiscountPrice') }}"
                                                value="{{ $cor->discount_price ?? 0 }}" />

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

                        <div class="col-md-9" style="{{ $cor->type == 1 ? '' : 'display:none' }}"
                            id="installment-box">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Installment') }}:</label><br>
                            <label class="switch">
                                <input class="slider" type="checkbox" value="1" id="installments"
                                    name="installment" {{ $cor->installment == 1 ? 'checked' : '' }} />
                                <span class="knob"></span>
                            </label>
                            <br>
                            <div style="{{ $cor->installment == 0 ? 'display:none' : '' }}"
                                id="installments-pricebox">
                                <label for="exampleInputSlug">{{ __('adminstaticword.InstallmentsTotalPrice') }}:
                                    <small class="text-muted"><i class="fa fa-question-circle"></i>
                                        {{ __('readonly') }}
                                    </small></label>
                                <input class="form-control" id="installments-price"
                                    value="{{ $installments->sum('amount') != 0 ? $installments->sum('amount') : 'Installments did not define yet' }}"
                                    readonly><br>

                                @if ($orderExists || $chapterExists)
                                    <label for="exampleInputDetails">{{ __('adminstaticword.TotalInstallments') }}:
                                        <sup class="redstar">*</sup><small class="text-muted"><i
                                                class="fa fa-question-circle"></i>
                                            {{ __("Can't modify installments due to defined chapters or existing orders.") }}
                                        </small></label>
                                    <input class="form-control" id="total_installments"
                                        value="{{ $cor->total_installments }}" readonly>
                                @else
                                    <label for="exampleInputDetails">{{ __('adminstaticword.TotalInstallments') }}:
                                        <sup class="redstar">*</sup></label>
                                    <select class="form-control select2" id="total_installments"
                                        name="total_installments">
                                        <option value="" selected disabled hidden>
                                            {{ __('Select an option') }}</option>
                                        <option value="2"
                                            {{ $cor->total_installments == '2' ? 'selected' : '' }}>2
                                        </option>
                                        <option value="3"
                                            {{ $cor->total_installments == '3' ? 'selected' : '' }}>3
                                        </option>
                                        <option value="4"
                                            {{ $cor->total_installments == '4' ? 'selected' : '' }}>4
                                        </option>
                                    </select>
                                @endif
                            </div>
                        </div>
                        {{-- <div class="col-md-3"> 
                            @if (Auth::User()->role == 'admin')
                            <label for="exampleInputTit1e">{{ __('adminstaticword.Featured') }}:</label><br>
                            <label class="switch">
                                <input class="slider" type="checkbox" id="customSwitch6" name="featured" {{ $cor->featured==1 ? 'checked' : '' }} />
                                <span class="knob"></span>
                            </label>
                            @endif
                        </div> --}}
                        <div class="col-md-6">
                            @if (Auth::User()->role == 'admin')
                                <label for="exampleInputTit1e">{{ __('adminstaticword.Status') }}:</label><br>
                                <label class="switch">
                                    <input class="slider" type="checkbox" id="customSwitch6" name="status"
                                        {{ $cor->status == 1 ? 'checked' : '' }} />
                                    <span class="knob"></span>
                                </label>
                            @endif
                        </div>
                    </div>

                    {{-- <div class="row">
                        <div class="col-md-4">
                            <label for="exampleInputDetails">{{ __('Instructor InvolvementRequest') }}:</label><br>
                            <label class="switch">
                                <input class="slider" type="checkbox" id="customSwitch6" name="involvement_request" {{ $cor->involvement_request==1 ? 'checked' : '' }} />
                                <span class="knob"></span>
                            </label>
                        </div>
                    </div> --}}
                    <br>

                    <div class="row" style="display:none;">
                        <div class="col-md-12">
                            <label for="exampleInputDetails">{{ __('adminstaticword.PreviewVideo') }}:</label><br>
                            <label class="switch">
                                <input class="slider" type="checkbox" id="customSwitch61" name="preview_type"
                                    {{ $cor->preview_type == 'video' ? 'checked' : '' }} />
                                <span class="knob"></span>
                            </label>

                            <div style="{{ $cor->preview_type == 'url' ? 'display:none' : '' }}" id="document1">
                                <label for="exampleInputSlug">{{ __('adminstaticword.UploadVideo') }}:</label>
                                <!-- -------------- -->
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                            id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="inputGroupFile01"
                                            name="video" value="{{ $cor->video }}"
                                            aria-describedby="inputGroupFileAddon01">
                                        <label class="custom-file-label"
                                            for="inputGroupFile01">{{ __('Choose file') }}</label>
                                    </div>
                                </div>
                                @if ($cor->video != '')
                                    <video src="{{ asset('video/preview/' . $cor->video) }}" width="200"
                                        height="150" controls>
                                    </video>
                                @endif
                                <!-- -------------- -->
                            </div>
                            <div @if ($cor->preview_type == 'video') class="display-none" @endif id="document2">
                                <label for="exampleInputSlug">{{ __('adminstaticword.URL') }}:</label>
                                <input type="url" class="form-control"
                                    placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.URL') }}"
                                    name="url" id="url" value="{{ $cor->url }}">
                            </div>
                        </div>
                    </div>

                    {{-- <div class="col-md-4">
                        <label for="">{{ __('adminstaticword.Duration') }}: </label><br>
                        <label class="switch">
                            <input class="slider" type="checkbox" name="duration_type"
                                {{ $cor->duration_type == 'm' ? 'checked' : '' }} />
                            <span class="knob"></span>
                        </label>
                        <br>
                        <small class="text-info"><i class="fa fa-question-circle"></i>
                            {{ __('If enabled duration can be in months') }}.</small><br>
                        <small class="text-info"> {{ __('when Disabled duration can be in days') }}.</small>

                        <br>
                        <label for="exampleInputSlug">{{ __('Course Expire Duration') }}</label>
                        <input min="1" class="form-control" name="duration" type="number" id="duration"
                            value="{{ $cor->duration }}"
                            placeholder="{{ __('adminstaticword.Enter') }} {{ __('adminstaticword.Duration') }}">
                    </div>
                    <br> --}}

                    <div class="row">
                        <div class="col-md-12">
                            <label>{{ __('adminstaticword.PreviewImage') }}:</label>
                            <br>
                            <!-- ====================== -->
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"
                                        id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="inputGroupFile01"
                                        name="preview_image" value="{{ $cor->preview_image }}">
                                    <label class="custom-file-label"
                                        for="inputGroupFile01">{{ $cor->preview_image ?? __('Choose file') }}</label>
                                </div>
                            </div>
                            @if ($cor['preview_image'] !== null && $cor['preview_image'] !== '')
                                <img src="{{ url('/images/course/' . $cor->preview_image) }}" height="70px;"
                                    width="70px;" />
                            @else
                                <img src="{{ Avatar::create($cor->title)->toBase64() }}" alt="course"
                                    class="img-fluid">
                            @endif
                            <!-- ====================== -->
                            <br>
                        </div>

                        {{-- <div class="col-md-6">
                            @if (Auth::User()->role == 'admin')
                            <label for="Revenue">{{ __('Instructor Revenue') }}:</label>

                            <div class="input-group">

                                <input min="1" class="form-control" name="instructor_revenue" type="number" value="{{ $cor['instructor_revenue'] }}" id="revenue"  placeholder="{{__('Enter revenue percentage')}}" class="{{ $errors->has('instructor_revenue') ? ' is-invalid' : '' }} form-control">
                                <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                            </div>
                            @endif
                        </div> --}}
                    </div>
                    <br>

                    {{-- <div class="row">
                        <div class="col-sm-3">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Assignment') }}:</label><br>
                            <label class="switch">
                                <input class="slider" type="checkbox" name="assignment_enable" {{ $cor['assignment_enable']=="1" ? 'checked' : '' }} />
                                <span class="knob"></span>
                            </label>
                            <br>
                            <small class="text-info"><i class="fa fa-question-circle"></i> {{ __('To enable assignment on portal') }}
                            </small>
                        </div>

                        <div class="col-sm-3">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Appointment') }}:</label><br>
                            <label class="switch">
                                <input class="slider" type="checkbox" name="appointment_enable" {{ $cor['appointment_enable']=="1" ? 'checked' : '' }} />
                                <span class="knob"></span>
                            </label>
                        </div>

                        <div class="col-sm-3">
                            <label for="exampleInputDetails">{{ __('adminstaticword.CertificateEnable') }}:</label><br>  
                            <label class="switch">
                                <input class="slider" type="checkbox" name="certificate_enable" id="customSwitch10" {{ $cor['certificate_enable'] == "1" ? 'checked' : '' }} />
                                <span class="knob"></span>
                            </label>
                        </div>

                        <div class="col-sm-3">
                            <label for="">{{ __('adminstaticword.DripContent') }}: </label><br>
                            <label class="switch">
                                <input class="slider" type="checkbox" name="drip_enable" {{ $cor['drip_enable'] == 1 ? 'checked' : '' }} />
                                <span class="knob"></span>
                            </label>
                            <br>
                            <small class="text-info"><i class="fa fa-question-circle"></i> {{ __('To release content on chapter & classes by a specific date or amount of days after enrollment') }}.
                            </small>
                        </div>
                    </div>
                    <br>
                    <br> --}}

                    <div class="box-footer">
                        <button type="submit"
                            class="btn btn-lg col-md-3 btn-primary-rgba">{{ __('adminstaticword.Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@section('scripts')
    <script>
        $(function() {
            $('.js-example-basic-single').select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });
        });
        $(function() {
            $('#cb1').change(function() {
                $('#f').val(+$(this).prop('checked'))
            })
        })

        $('#customSwitch2').change(function() {
            if ($('#customSwitch2').is(':checked')) {
                $('#doabox').show('fast');
                $('#installment-box').show('fast');
                $('#priceMain').attr('required', true);
            } else {
                $('#doabox').hide('fast');
                $('#installment-box').hide('fast');
                $('#priceMain').val(0);
                $('#discount_price').val(0);
                $('#priceMain').attr('required', false);
            }

        });
        $('#installments').on('change', function() {
            if ($('#installments').is(':checked')) {
                $('#installments-pricebox').show('fast');
                $('#total_installments').attr('required', true);
            } else {
                $('#installments-pricebox').hide('fast');
                $('#installments-price').val('');
                $('#installments-price').attr('required', false);
                $('#total_installments').attr('required', false);
            }
        });
        $('#customSwitch61').on('change', function() {
            if ($('#customSwitch61').is(':checked')) {
                $('#document1').show('fast');
                $('#document2').hide('fast');
            } else {
                $('#document2').show('fast');
                $('#document1').hide('fast');
            }
        });
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

        // Add an event listener to the discount type select element
        $('#discount_type').change(()=>{
        updatePrefix()            })

        // Initial call to set the prefix based on the default selected value
        updatePrefix();
        $(function() {
            var urlLike = '{{ url('type/categories') }}';
            $('#category_id').change(function() {
                var up = $('#type_id').empty();
                var cat_id = $(this).val();
                if (cat_id) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "GET",
                        url: urlLike,
                        data: {
                            catId: cat_id
                        },
                        success: function(data) {
                            console.log(data);
                            up.append("<option value=''>{{ __('Please Choose') }}</option>");
                            $.each(data, function(id, title) {
                                up.append($('<option>', {
                                    value: id,
                                    text: title
                                }));
                            });
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            console.log(XMLHttpRequest);
                        }
                    });
                }
            });
        });
        $(function() {
            var urlLike = '{{ url('admin/dropdown') }}';
            $('#type_id').change(function() {
                var up = $('#upload_id').empty();
                var cat_id = $('#category_id').val();
                var type_id = $(this).val();
                if (type_id) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "GET",
                        url: urlLike,
                        data: {
                            catId: cat_id,
                            typeId: type_id
                        },
                        success: function(data) {
                            console.log(data);
                            up.append("<option value=''>{{ __('Please Choose') }}</option>");
                            $.each(data, function(id, title) {
                                up.append($('<option>', {
                                    value: id,
                                    text: title
                                }));
                            });
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            console.log(XMLHttpRequest);
                        }
                    });
                }
            });
        });
        $(function() {
            var urlLike = '{{ url('admin/gcat') }}';
            $('#upload_id').change(function() {
                var up = $('#grand').empty();
                var cat_id = $('#category_id').val();
                var type_id = $('#type_id').val();
                var sub_id = $(this).val();
                if (sub_id) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "GET",
                        url: urlLike,
                        data: {
                            catId: cat_id,
                            typeId: type_id,
                            subId: sub_id
                        },
                        success: function(data) {
                            console.log(data);
                            up.select2({
                                placeholder: "{{ __('Please Choose') }}",
                                allowClear: true
                            });
                            $.each(data, function(id, title) {
                                up.append($('<option>', {
                                    value: id,
                                    text: title
                                }));
                            });
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            console.log(XMLHttpRequest);
                        }
                    });
                }
            });
        });
    </script>

    <script>
        $(".midia-toggle").midia({
            base_url: '{{ url('') }}',
            title: 'Choose Course Image',
            dropzone: {
                acceptedFiles: '.jpg,.png,.jpeg,.webp,.bmp,.gif'
            },
            directory_name: 'course'
        });
    </script>
@endsection
