@extends('layouts.app')

@section('title', 'Files list')

@section('content')
    <div class="container mx-auto bg-gray-200 px-4 mb-4" id="pageContent">
        <div class="flex pt-5 mb-5 justify-between">
            <h3 class="text-2xl font-bold">My Files</h3>
            <a href="{{route('file.create')}}" class="bg-purple-600 rounded py-4 px-2 text-white leading-none shadow float-right">Upload New</a>
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
        <!-- hack to prevent purecss from deleting datatables classes
        <div class="dataTable no-footer dataTables_length dataTables_wrapper dataTables_length dataTables_filter dataTables_info dataTables_paginate paging_simple_numbers files_list_previous paginate_button next paginate_button sorting sorting_desc sorting_disabled  current  hidden"></div>
        -->
        <div>
            <table id="files_list" class="" data-src="{{route('file.ajax.index')}}">
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
@endsection

@push('scripts')
    <script src="{{mix('js/files.js')}}"></script>
@endpush
