@extends('layouts.app')

@section('title', 'New File Upload')

@section('content')
    <div class="container mx-auto my-40 flex-col justify-center">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
                @endforeach
            @endif
            <h1 class="text-2xl font-bold">Upload File&nbsp;<i class="fas fa-file"></i></h1>
            <form action="{{ route('file.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="my-4">
                    <label for="exampleFormControlFile1">Select a file for upload</label>
                    <input type="file" name="file" class="form-control-file" id="exampleFormControlFile1">
                </div>
                <button type="submit" class="bg-purple-600 rounded py-4 px-2 text-white leading-none shadow">Upload</button>
            </form>
        </div>
@endsection
