@extends('admin.layouts.master')
@section('title', 'List all Recordings - Admin')
@section('maincontent')


    @component('components.breadcumb', ['fourthactive' => 'active'])
        @slot('heading')
            {{ __('List unlinked Recordings') }}
        @endslot
        @slot('menu1')
            {{ __('Live Streamings') }}
        @endslot
        @slot('menu2')
            {{ __('Big Blue') }}
        @endslot
        @slot('menu3')
            {{ __('List unlinked Recordings') }}
        @endslot
    @endcomponent


    <div class="contentbar">
        <!-- Start row -->
        <div class="row">

            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="box-title">{{ __('List unlinked Recordings') }}</h5>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="recordings-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>
                                            Meeting ID
                                        </th>
                                        <th>
                                            Meeting Name
                                        </th>
                                        <th>
                                            Link
                                        </th>
                                        <th>
                                            Get Recording
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;
                                    
                                    //	 dd(gettype($all_recordings));
                                    
                                    ?>

                                    @if (isset($unlinkedRecordings))
                                        @foreach ($unlinkedRecordings as $meeting)
                                            <?php $i++; ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><b>{{ $meeting->meetingID }}</b></td>
                                                <td><b>{{ $meeting->name }}</b></td>
                                                <td><a href="{{ route('link.meeting') }}" 
                                                   class="btn btn-primary"
                                                    >Link To Course</a>
                                                </td>
                                                <td>

                                                    <a href="{{ $meeting->playback->format->url }}" target="_blank"
                                                        class="btn btn-primary">Play Recording </a>
                                                </td>




                                            </tr>
                                        @endforeach
                                    @endif
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
@endsection

@section('script')
    <script>
        $(function() {
            $('#recordings-datatable').dataTable({
                language: {
                    searchPlaceholder: "Search recording here"
                },
                columnDefs: [{
                    "targets": [3],
                    orderable: false,
                    searchable: false
                }, ]

            });
        });
    </script>
@endsection
