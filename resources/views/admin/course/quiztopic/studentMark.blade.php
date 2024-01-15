@extends('admin.layouts.master')
@section('title', 'Report')
@section('maincontent')
    @component('components.breadcumb', ['secondaryactive' => 'active'])
        @slot('heading')
            {{ __('Report') }}
        @endslot

        @slot('menu1')
            {{ __('Report') }}
        @endslot
    @endcomponent



    <div class="contentbar">
        {{ $questions }}
    </div>
    <!-- End col -->
    </div>
    <!-- End row -->
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#quizreport-datatable').dataTable({
                language: {
                    searchPlaceholder: "Search quiz report here"
                },
            });
        });
    </script>
@endsection
