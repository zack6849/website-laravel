<x-base-page title="Delete File?">
    <h1>Are you sure?</h1>
    You're about to delete file <b>{{$file->original_filename}}</b>, this action cannot be undone.

    <form action="{{route('file.destroy', $file)}}" method="post">
        {{method_field('post')}}
        {{csrf_field()}}
        <button type="submit">Delete File</button>
        <button type="button">Never mind.</button>
    </form>
</x-base-page>
