<div>
    <p>
        This page is rate limited to {{$this->dailyLimit}} requests per day, and the account is not set to be topped up
        automatically,
        please do not abuse it.

        <p>
            You have <b>{{$this->remainingLookups}}</b> lookups remaining.
        </p>
        <div class="mt-5">
            <input class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   type="text"
                   label="Phone Number"
                   placeholder="Phone Number"
                   wire:model="phoneNumber"
                   wire:keydown.enter="lookup"
            >

            <button class="bg-blue-500 hover:bg-blue-700 text-gray-100 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    wire:click.debounce.500ms="lookup"
                    wire:loading.attr="disabled"
                    wire:loading.class="bg-gray-700"
                    wire:loading.class.remove="bg-blue-700"
            >
                Lookup
            </button>
        </div>
        <div wire:loading>
            Looking up phone number...
        </div>
        @if($this->rateLimited)
            <div>
                {{$this->rateLimitMessage}}
            </div>
        @endif
        @if($this->hasResult())
            <div wireL:loading.remove>
                <div class="p-5 bg-gray-200 my-4">
                    <pre>{{$this->resultSummary}}</pre>
                </div>
                <div class="result-data grid grid-cols-2">
                    <div class="p-5 mr-2">
                        <h2 class="text-3xl my-2">Formatted Data</h2>
                        <pre class="max-h-64 overflow-scroll">{{json_encode($this->formattedResult, JSON_PRETTY_PRINT)}}</pre>
                    </div>
                    <div class="p-5">
                        <h2 class="text-3xl my-2">Raw Data</h2>
                        <pre class="max-h-64 overflow-scroll">{{json_encode($this->result, JSON_PRETTY_PRINT)}}</pre>
                    </div>
                </div>
            </div>
        @endif
</div>