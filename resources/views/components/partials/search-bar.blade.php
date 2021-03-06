<div class="input-group mb-3">
    @if(!empty($buttons))
        <div class="input-group-prepend">
            {{ $buttons }}
        </div>
    @endif
    <input wire:model="search" type="text" class="form-control" name="search"
           placeholder="{{$placeholder}}" aria-label="{{$placeholder}}"
           aria-describedby="basic-addon2" value="{{ request()->search ?? ""}}">
    <div class="input-group-append">
        <button class="btn btn-outline-primary" type="submit">Search</button>
    </div>
    @if(!empty($search))
        <div class="ml-2">
            <a wire:click="clear" class="btn btn-danger">Reset</a>
        </div>
    @endif
</div>
