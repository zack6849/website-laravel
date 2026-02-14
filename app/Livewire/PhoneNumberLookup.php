<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\TwilioService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PhoneNumberLookup extends Component
{

    public string $phoneNumber = '';
    public ?array $formattedResult = null;
    public ?array $result = null;
    public ?string $resultSummary = null;
    public string $rateLimitMessage = '';
    public string $errorMessage = '';
    #[Locked]
    public bool $rateLimited = false;
    #[Locked]
    public int $dailyLimit = 0;
    #[Locked]
    public int $remainingLookups = 0;

    public function render(): Application|Factory|View|\Illuminate\View\View
    {
        $this->calculateRateLimitInformation();
        return view('livewire.phone-number-lookup');
    }

    private function getRateLimitConfiguration(): array
    {
        $limit = config('twilio.public_rate_limit', 3);
        $decayRate = config('twilio.public_decay_rate', 86400);
        $key = request()->ip();
        $user = request()->user();
        if ($user !== null) {
            $key = $user->id;
            $limit = $user->lookup_limit ?? $limit;
            $decayRate = $user->lookup_decay_rate ?? $decayRate;
        }
        return [$key, $limit, $decayRate];
    }

    public function calculateRateLimitInformation(): void
    {
        [$key, $limit,] = $this->getRateLimitConfiguration();
        $available = $limit - RateLimiter::attempts($key);
        $availableIn = RateLimiter::availableIn($key);
        $availableAt = now()->addSeconds($availableIn)->longAbsoluteDiffForHumans();
        $this->rateLimited = $available <= 0;
        $this->dailyLimit = $limit;
        $this->remainingLookups = $available;
        if ($this->rateLimited) {
            $this->rateLimitMessage = "You have been rate limited. Please try again in $availableAt";
            $this->result = null;
            $this->formattedResult = null;
            $this->resultSummary = '';
        }
    }

    public function lookup(): void
    {
        /** @var TwilioService $service */
        $service = resolve(TwilioService::class);
        $this->phoneNumber = $service->normalizePhoneNumber($this->phoneNumber);
        //if we already cached the response, don't penalize the user
        if ($service->hasCachedResponseFor($this->phoneNumber)) {
            $this->result = $service->lookupNumber($this->phoneNumber);
            $this->formattedResult = $service->extractData($this->result);
            $this->resultSummary = $service->toSms($this->formattedResult);
            return;
        }
        [$key, , $decay] = $this->getRateLimitConfiguration();
        if ($this->rateLimited) {
            return;
        }
        RateLimiter::increment($key, $decay);
        $this->result = $service->lookupNumber($this->phoneNumber);
        $this->formattedResult = $service->extractData($this->result);
        $this->resultSummary = $service->toSms($this->formattedResult);
        session()->flash('message', 'Lookup complete');
    }

    public function hasResult(): bool
    {
        return $this->result !== null;
    }
}
