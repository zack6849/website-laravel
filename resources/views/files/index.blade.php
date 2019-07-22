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
                <span>Showing {{$files->firstItem()}} - {{$files->lastItem()}} of {{auth()->user()->files()->count()}}</span>
            </div>
            <div class="col">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search Files..." aria-label="Search Files..." aria-describedby="button-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-outline-info" type="button" id="button-addon2">Search</button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="float-right">
                    {{ $files->links() }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-sm table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>File</th>
                        <th>Mime Type</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($files as $file)
                        <tr>
                            <td>{{$file->id}}</td>
                            <td><a href="{{route('file.show', ['file_id' => $file->filename])}}">{{$file->original_filename}}</a></td>
                            <td>{{$file->mime}}</td>
                            <td>
                                <a hre="" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
              <div class="row">
            <div class="col">
                <span>Showing {{$files->firstItem()}} - {{$files->lastItem()}} of {{auth()->user()->files()->count()}}</span>
            </div>
            <div class="col">
                <div class="float-right">
                    {{ $files->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
