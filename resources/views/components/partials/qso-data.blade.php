<div>
    <b>{{$entry->mode}} Contact with {{$entry->callee->name}} at grid {{$entry->to_grid}}</b><br>
    <b>Date: {{$entry->created_at}}</b><br>
    @if($entry->rst_received !== null)
        <b>RST Received: {{$entry->rst_received}}</b><br>
    @endif
    @if(trim($entry->comments) !== "")
        <b>Comments:</b> {{$entry->comments}}<br>
    @endif
</div>
