<x-base-page title="Files">
    @prepend('styles')
        <!-- this is a temporary workaround until the table library supports tailwind 4 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    @endprepend()
    <x-slot name="buttons">
        <span class="inline-flex rounded-md shadow-sm">
            <a href="{{route('file.create')}}"
               class="whitespace-no-wrap inline-flex items-center justify-center px-4 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
                Upload
            </a>
          </span>
    </x-slot>
    <livewire:tables.files :user="auth()->user()"></livewire:tables.files>
</x-base-page>
