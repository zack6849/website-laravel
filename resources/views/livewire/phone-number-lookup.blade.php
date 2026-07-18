<div>
    <div class="mb-5">
        <p>
            Look up the carrier and line type for a phone number &mdash; built because people-finder sites
            charge $30 for data Twilio sells for pennies.
        </p>
        <p>
            This page is rate limited to {{$this->dailyLimit}} requests per day, and the account is not set to be topped
            up automatically.
        </p>
        <em class="font-light block py-3">Feel free to use it, but please don't abuse it :)</em>
    </div>
    <p>
        You have <b>{{$this->remainingLookups}}</b> lookups remaining.
    </p>
    <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:items-center">
        <input
            class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline sm:max-w-md"
            type="tel"
            inputmode="tel"
            autocomplete="tel"
            label="Phone Number"
            placeholder="Phone Number"
            wire:model="phoneNumber"
            wire:keydown.enter="lookup"
        >

        <button
            class="btn-primary w-full font-bold focus:outline-none focus:shadow-outline sm:w-auto"
            wire:click.debounce.500ms="lookup"
            wire:loading.attr="disabled"
            wire:loading.class="bg-gray-700"
            wire:loading.class.remove="bg-brand-600"
        >
            Lookup
        </button>
    </div>
    <div wire:loading>
        Looking up phone number...
    </div>
    @if($errorMessage)
        <div class="text-red-600">
            {{$errorMessage}}
        </div>
    @endif
    @if($this->hasResult())
        <div wire:loading.remove>
            @if($includeIdentityData)
                <div class="p-5 bg-gray-200 my-4">
                    <pre class="overflow-x-auto whitespace-pre-wrap">{{$this->resultSummary}}</pre>
                </div>
                <div class="result-data grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="p-5">
                        <h2 class="text-2xl my-2">Formatted Data</h2>
                        <pre
                            class="max-h-64 overflow-auto rounded bg-gray-50 p-3 text-xs sm:text-sm">{{json_encode($this->formattedResult, JSON_PRETTY_PRINT)}}</pre>
                    </div>
                    <div class="p-5">
                        <h2 class="text-2xl my-2">Raw Data</h2>
                        <pre class="max-h-64 overflow-auto rounded bg-gray-50 p-3 text-xs sm:text-sm">{{json_encode($this->result, JSON_PRETTY_PRINT)}}</pre>
                    </div>
                </div>
            @else
                <div class="p-5 bg-gray-200 my-4 space-y-1">
                    <p><b>Carrier:</b> {{$this->formattedResult['carrier'] ?? 'Unknown'}}</p>
                    <p><b>Line Type:</b> {{$this->formattedResult['type'] ?? 'Unknown'}}</p>
                    <p><b>Caller Name:</b> {{$this->formattedResult['possible_owners'][0] ?? 'Unknown'}}</p>
                </div>
                <p class="text-sm text-gray-600">
                    <a href="{{route('login')}}" class="underline">Log in</a> for full historical lookup details.
                </p>
            @endif
        </div>
    @endif
</div>
