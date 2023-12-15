<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-header">
                <h5 class="card-box">{{ __('adminstaticword.View') }} {{ __('adminstaticword.Course') }}</h5>
            </div>
            <div class="card-body ml-2">
                    <div class="row">
                        <div class="col-md-6">
                            <label>{{ __('adminstaticword.Category') }}<span class="redstar">*</span></label>
                            <select class="form-control" disabled>
                                <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                @php
                                $category = App\Categories::where('status', true)->get();
                                @endphp 
                                @foreach($category as $caat)
                                <option {{ $cor->category_id == $caat->id ? 'selected' : "" }} value="{{ $caat->id }}">{{ $caat->title }}</option>
                                @endforeach 
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>{{ __('adminstaticword.TypeCategory') }}:<span class="redstar">*</span></label>
                            <select class="form-control" disabled>
                                <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                @php
                                $typecategory = App\secondaryCategory::where('status', true)->where('category_id',$cor->category_id)->get();
                                @endphp
                                @foreach($typecategory as $caat)
                                <option {{ $cor->scnd_category_id == $caat->id ? 'selected' : "" }} value="{{ $caat->id }}">{{ $caat->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-6">
                            <label>{{ __('adminstaticword.SubCategory') }}:<span class="redstar">*</span></label>
                            <select class="form-control" disabled>
                                <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                @php
                                $subcategory = App\SubCategory::where('status', true)->where(['category_id'=>$cor->category_id,'scnd_category_id'=>$cor->scnd_category_id])->get();
                                @endphp
                                @foreach($subcategory as $caat)
                                <option {{ $cor->subcategory_id == $caat->id ? 'selected' : "" }} value="{{ $caat->id }}">{{ $caat->title }}</option>
                                @endforeach
                            </select>
                        </div>   
                        <div class="col-md-6">
                            <label>{{ __('adminstaticword.ChildCategory') }}:<span class="redstar">*</span></label>
                            <select class="form-control" disabled>
                                <option value="">{{ __('adminstaticword.SelectanOption') }}</option>
                                @php
                                $childcategory = App\ChildCategory::where('status', true)->where(['category_id'=>$cor->category_id,'scnd_category_id'=>$cor->scnd_category_id,'subcategory_id'=>$cor->subcategory_id])->get();
                                @endphp 
                                @foreach($childcategory as $caat)
                                <option {{ $cor->childcategory_id == $caat->id ? 'selected' : "" }} value="{{ $caat->id }}">{{ $caat->title }}</option>
                                @endforeach
                            </select>
                        </div>     
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="exampleInputTit1e">{{ __('adminstaticword.Instructor') }}:<sup class="redstar">*</sup></label>
                            <input type="text" class="form-control" value="{{$users->fname}} {{$users->lname}}" disabled>
                            <input type="hidden" class="form-control"   value="{{$users->id}}">
                            
                        </div>
                    </div>
                    <br>

                    <div class="row">

                        <div class="col-md-12"> 
                            <label for="exampleInputTit1e">{{ __('adminstaticword.CourseName') }}:<sup class="redstar">*</sup></label>
                            <input type="text" class="form-control" id="exampleInputTitle" value="{{ $cor->title }}" disabled>
                        </div>

                    </div>
                    <br>
                    <div class="row">

                        <div class="col-md-12"> 
                            <label for="exampleInputTit1e">{{ __('adminstaticword.CourseWhtsapGrupLink') }}:</label>
                            <input type="text" class="form-control" id="wtsap_link" value="{{ $cor->wtsap_link }}" disabled>
                        </div>

                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-12"> 
                            <label for="exampleInputSlug">{{ __('adminstaticword.CourseTags') }}:</label>
                            <select class="select2-multi-select form-control" multiple="multiple" size="5" disabled>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>
                            {{ __('Start Date') }}:<sup class="redstar">*</sup>
                            </label>
                            <div class="input-group">
                                @php
                                    $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $cor->start_date, 'UTC');
                                    $startDate->setTimezone(auth()->user()->timezone);
                                @endphp
                                <input type="text" disabled class="form-control"
                                    placeholder="yyyy-mm-dd" value="{{date('Y-m-d', strtotime($startDate))}}" aria-describedby="basic-addon2">

                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2"><i class="feather icon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>
                            {{ __('End Date') }}:<sup class="redstar">*</sup>
                            </label>

                            <div class="input-group">  
                                @php
                                    $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $cor->end_date, 'UTC');
                                    $endDate->setTimezone(auth()->user()->timezone);
                                @endphp                                
                                <input type="text" disabled class="form-control"
                                    placeholder="yyyy-mm-dd" value="{{date('Y-m-d', strtotime($endDate))}}" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2"><i class="feather icon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Detail') }}:<sup class="redstar">*</sup></label>
                            <textarea rows="5" class="form-control" disabled>{!! $cor->detail !!}</textarea>
                        </div>
                    </div>
                    <br>

                    <div class="row">

                        <div class="col-md-3">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Paid') }}:</label><br>  
                            <label class="switch">
                                <input class="slider" type="checkbox" id="customSwitch2" {{ $cor->type == '1' ? 'checked' : '' }} disabled/>
                                <span class="knob"></span>
                            </label>
                            <br>     

                            <div style="{{ $cor->type == 0 ? 'display:none' : '' }}">
                                <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}: <sup class="redstar">*</sup></label>
                                <input type="text"  disabled class="form-control" id="priceMain" value="{{ $cor->price?? 0 }}">

                                <br>
                                <label for="exampleInputSlug">{{ __('adminstaticword.DiscountPrice') }}: <sup class="redstar">*</sup></label><small class="text-muted">Discounted price Zero(0) consider as free</small>
                                <input type="text" disabled class="form-control" id="discount_price" value="{{ $cor->discount_price?? 0 }}">
                            </div>
                        </div>

                        <div class="col-md-3" style="{{ $cor->type == 0 ? 'display:none' : '' }}">
                            <label for="exampleInputDetails">{{ __('adminstaticword.Installment') }}:</label><br/>
                            <label class="switch">
                                <input class="slider" type="checkbox" value="1" id="installments" {{ $cor->installment == 1 ? 'checked' : '' }} disabled/>
                                <span class="knob"></span>
                            </label>
                            
                            <br>
                            <div style="{{ (count($installments) == 0 || $cor->installment == 0)? 'display:none' : '' }}">
                                <label for="exampleInputSlug">{{ __('adminstaticword.Price') }}:</label>
                                <input type="number" class="form-control" value="{{ $cor->installment_price }}" disabled>
                                <label for="total_installments">{{ __('adminstaticword.Installments') }}: </label>
                                <input type="number" class="form-control"value="{{ $cor->total_installments }}" disabled>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="exampleInputTit1e">{{ __('adminstaticword.Status') }}:</label><br>
                            <label class="switch">
                                <input  type="checkbox" {{ $cor->status==1 ? 'checked' : '' }} disabled/>
                                <span class="knob"></span>
                            </label>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <label>{{ __('adminstaticword.PreviewImage') }}:<sup class="redstar">*</sup> size: 270x200</label> 
                            <br> 
                            <!-- ====================== -->
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupFileAddon01">{{ __('Upload') }}</span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="inputGroupFile01" value="{{ $cor->preview_image }}" disabled>
                                    <label class="custom-file-label" for="inputGroupFile01">{{ $cor->preview_image?? __('Choose file') }}</label>
                                </div>
                            </div>
                            @if($cor['preview_image'] !== NULL && $cor['preview_image'] !== '')
                            <img src="{{ url('/images/course/'.$cor->preview_image) }}" height="70px;" width="70px;"/>
                            @else
                            <img src="{{ Avatar::create($cor->title)->toBase64() }}" alt="course" class="img-fluid">
                            @endif
                            <!-- ====================== -->
                            <br>
                        </div>
                    </div>
                    <br>
            </div>
        </div>
    </div>
</div>
<!-- edit media Modal start -->

<!-- edit media Model ended -->
@section('scripts')
<script>
    $(".midia-toggle").midia({
    base_url: '{{ url('') }}',
            title : 'Choose Course Image',
            dropzone : {
            acceptedFiles: '.jpg,.png,.jpeg,.webp,.bmp,.gif'
            },
            directory_name: 'course'
    });
</script>
@endsection