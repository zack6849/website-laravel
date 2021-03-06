<div>
    <div class="row">
        <div class="col-2">
            <x-partials.limit-selector></x-partials.limit-selector>
        </div>
        <div class="col-6 offset-4">
            <div>
                <x-partials.search-bar placeholder="Filename, or mime type"></x-partials.search-bar>
            </div>
        </div>
    </div>

    <div class="flex flex-col">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="table table-auto table-striped table-light">
                    <thead class="table-dark">
                    <tr>
                        @include('components.partials.table-th', ['field' => 'original_filename', 'label' => 'Name'])
                        @include('components.partials.table-th', ['field' => 'mime', 'label' => 'File Type'])
                        @include('components.partials.table-th', ['field' => 'created_at', 'label' => 'Uploaded'])
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($files as $file)
                        <tr>
                            <td>{{ $file->original_filename }}</td>
                            <td>{{ $file->mime }}</td>
                            <td>{{ $file->created_at }}</td>
                            <td><a href="{{route('file.show', ['filename' => $file->filename])}}">view</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <x-paginator :paginator="$files"></x-paginator>
    </div>
</div>
