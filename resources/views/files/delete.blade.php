<x-base-page title="Delete File?">
    <p>You're about to delete file <b>{{$file->original_filename}}</b>, this action cannot be undone.</p>
    <h1 class="pb-5">Are you sure?</h1>
    <form action="{{route('file.destroy', $file)}}" method="post">
        {{method_field('post')}}
        {{csrf_field()}}
            <button type="submit" class="bg-red-600 rounded py-4 px-2 text-white leading-none shadow mr-4">Delete File</button>
            <button type="button" class="bg-purple-600 rounded py-4 px-2 text-white leading-none shadow">Never mind.</button>
    </form>
</x-base-page>
