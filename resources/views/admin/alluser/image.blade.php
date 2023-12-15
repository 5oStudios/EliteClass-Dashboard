@if ($image = @file_get_contents('../public/images/user_img/' . $user_img))
    <img @error('photo') is-invalid @enderror src="{{ url('images/user_img/' . $user_img) }}" alt="profilephoto"
        class="img-responsive img-circle" data-toggle="modal" data-target="#exampleStandardModal{{ $id }}">
@else
    <img @error('photo') is-invalid @enderror src="{{ Avatar::create($fname)->toBase64() }}" alt="profilephoto"
        class="img-responsive img-circle" data-toggle="modal" data-target="#exampleStandardModal{{ $id }}">
@endif
<div class="modal fade" id="exampleStandardModal{{ $id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleStandardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleStandardModalLabel">
                    {{ $fname }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <div class="card m-b-30">
                        <div class="card-body py-5">
                            <div class="row">
                                <div class="user-modal">
                                    @if ($image = @file_get_contents('../public/images/user_img/' . $user_img))
                                        <img @error('photo') is-invalid @enderror
                                            src="{{ url('images/user_img/' . $user_img) }}" alt="profilephoto"
                                            class="img-responsive img-circle">
                                    @else
                                        <img @error('photo') is-invalid @enderror
                                            src="{{ Avatar::create($fname)->toBase64() }}" alt="profilephoto"
                                            class="img-responsive img-circle">
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    <h4 class="text-center">
                                        {{ $fname }} {{ $lname }}
                                    </h4>
                                    <div class="mt-4 mb-3">
                                        <i class="feather icon-mail mr-2"></i>{{ $email }}
                                        @isset($mobile)
                                            <i class="feather icon-phone mr-2 ml-2"></i>{{ $mobile }}
                                        @endisset
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0 user-table">
                                            <tbody>
                                                @isset($dob)
                                                    <tr>
                                                        <th scope="row" class="p-1">
                                                            Date Of Birth :</th>
                                                        <td class="p-1">
                                                            {{ $dob }}</td>
                                                    </tr>
                                                @endisset

                                                @isset($gender)
                                                    <tr>
                                                        <th scope="row" class="p-1">
                                                            Gender :</th>
                                                        <td class="p-1">
                                                            {{ $gender }}</td>
                                                    </tr>
                                                @endisset

                                                <tr>
                                                    <th scope="row" class="p-1">
                                                        Role :</th>
                                                    <td class="p-1">
                                                        {{ $role }}</td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
