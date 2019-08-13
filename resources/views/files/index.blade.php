@extends('layouts.app')

@section('title', 'Files list')

@section('content')
    <div class="container" id="pageContent">
        <div class="row pt-5 mb-5">
            <div class="col">
                <h3>My Files</h3>
            </div>
            <div class="col">
                <a href="{{route('file.create')}}" class="btn btn-success float-right">Upload New</a>
            </div>
        </div>
        <div class="row">
            <div class="col">
                @if(Session::has('status'))
                    <div class="alert alert-info" role="alert">
                        {{ Session::get('status') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table id="files_list" class="table table-sm table-striped table-bordered table-hover" data-src="{{route('file.ajax.index')}}">
                    <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>File</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{mix('js/files.js')}}"></script>
@endpush
