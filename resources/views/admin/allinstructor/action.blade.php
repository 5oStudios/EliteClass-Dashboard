<div class="dropdown">
    <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
        @can('Allinstructor.view')
            <button type="button" class="dropdown-item" data-toggle="modal"
                data-target="#exampleStandardModal{{ $id }}">
                <i class="feather icon-eye mr-2"></i>{{ __('View') }}
            </button>
        @endcan
        @can('Allinstructor.edit')
            <a class="dropdown-item" href="{{ route('allinstructor.edit', $id) }}"><i
                    class="feather icon-edit mr-2"></i>{{ __('Edit') }}</a>
        @endcan
        @can('Allinstructor.delete')
            <a class="dropdown-item btn btn-link" data-toggle="modal" data-target="#delete{{ $id }}">
                <i class="feather icon-delete mr-2"></i>{{ __('Delete') }}</a>
            </a>
        @endcan
    </div>
</div>

<!-- delete Modal start -->
<div class="modal fade bd-example-modal-sm" id="delete{{ $id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleSmallModalLabel">{{ __('Delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>{{ __('Are You Sure ?') }}</h4>
                <p>{{ __('This process') }} <b>{{ __('delete') }}</b>
                    {{ __('the instructor, Do you really want to delete') }}
                    <b>{{ $fname }}</b>?
                    {{ __('This process cannot be undo.') }}
                </p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('user.delete', $id) }}" class="pull-right">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="reset" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- delete Model ended -->


<!-- View Model -->
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
<!-- View Model ended -->
