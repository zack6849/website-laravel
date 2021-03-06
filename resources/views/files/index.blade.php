<x-base-page title="Files">
    <x-slot name="buttons">
        <span class="inline-flex rounded-md shadow-sm">
            <a href="{{route('file.create')}}"
               class="whitespace-no-wrap inline-flex items-center justify-center px-4 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
                Upload
            </a>
          </span>
    </x-slot>
    <livewire:files.table></livewire:files.table>
</x-base-page>
