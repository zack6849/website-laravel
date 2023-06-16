<div>
    <div>
        <b>{{$entry->mode}} QSO w/ {{$entry->callee->name}}</b>
    </div>
    <div>
        <b>Date: {{$entry->created_at->format('Y-m-m H:i:s')}}</b>
    </div>
    <div>
        <b>Frequency: {{$entry->frequency}}Mhz</b>
    </div>
    @if($entry->rst_received !== null)
        <b>RST Received: {{$entry->rst_received}}</b>
    </div>
    @endif
    @if($entry->to_grid !== null)
        <div>
            <b>Grid: {{$entry->to_grid}}</b>
        </div>
    @endif
    @if(trim($entry->comments) !== "")
        <div>
            <b>Comments:</b> {{$entry->comments}}
        </div>
    @endif
</div>
