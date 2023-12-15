@extends('admin.layouts.master')
@section('title', '')
@section('maincontent')

    <div class="contentbar">
        <br>
        <br>
        <br>
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('file-import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" id="file"
                                aria-describedby="inputGroupFileAddon01" required>
                            <label class="custom-file-label" for="inputGroupFile01">{{ __('Choose file') }}</label>
                        </div>
                    </div>
                    <br>
                    <br>
                    <button type="submit" class="btn btn-primary">Import data</button>
                </form>
            </div>
        </div>
    </div>

@endsection
