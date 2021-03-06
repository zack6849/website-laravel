
<th scope="col">
    @if(!isset($sortable) || $sortable == true)
    <a wire:click.prevent="sortBy('{{$field}}')" role="button">
        {{$label}}
    </a>
    @include('components.partials.sort-icon', ['field' => $field])
    @else
        {{$label}}
    @endif
</th>
