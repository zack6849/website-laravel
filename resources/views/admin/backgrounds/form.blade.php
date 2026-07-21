<x-base-page :title="$backgroundId ? 'Edit background' : 'New background'" maxwidth="max-w-5xl">
    <livewire:admin.background-form :background-id="$backgroundId"/>
</x-base-page>
