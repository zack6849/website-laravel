<div>
    <b>{{$entry->mode}} QSO w/ {{$entry->callee->name}}</b><br>
    <b>Date: {{$entry->created_at->format('Y-m-m')}}</b><br>
    @if($entry->rst_received !== null)
        <b>RST Received: {{$entry->rst_received}}</b><br>
    @endif
    @if($entry->to_grid !== null)
        <b>Grid: {{$entry->to_grid}}</b><br>
    @endif
    @if(trim($entry->comments) !== "")
        <b>Comments:</b> {{$entry->comments}}<br>
    @endif
</div>
