@extends('layouts.app')

@section('title', 'New File Upload')

@section('content')
    <div class="container-fluid" id="pageContent">
        <div class="container" style="padding-top: 4rem; padding-bottom: 4rem;">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <h1>Upload File&nbsp;<i class="fas fa-file"></i></h1>
            <form action="{{ route('file.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="form-group">
                    <label for="exampleFormControlFile1">Select a file for upload</label>
                    <input type="file" name="file" class="form-control-file" id="exampleFormControlFile1">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection
