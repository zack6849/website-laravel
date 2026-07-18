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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twilio\Exceptions\TwilioException;

class PhoneNumberLookup extends Component
{

    public string $phoneNumber = '';
    public ?array $formattedResult = null;
    public ?array $result = null;
    public ?string $resultSummary = null;
    public string $errorMessage = '';
    #[Locked]
    public int $dailyLimit = 0;
    #[Locked]
    public int $remainingLookups = 0;
    #[Locked]
    public bool $includeIdentityData = false;

    public function render(): Application|Factory|View|\Illuminate\View\View
    {
        $this->calculateRateLimitInformation();
        return view('livewire.phone-number-lookup');
    }

    public function calculateRateLimitInformation(): void
    {
        /** @var TwilioService $service */
        $service = resolve(TwilioService::class);
        [$key, $limit,] = $service->getRateLimitConfiguration(request()->user(), request()->ip());
        $available = $limit - RateLimiter::attempts($key);
        $this->dailyLimit = $limit;
        $this->remainingLookups = $available;
    }

    public function lookup(): void
    {
        /** @var TwilioService $service */
        $service = resolve(TwilioService::class);
        $this->errorMessage = '';
        $this->result = null;
        $this->formattedResult = null;
        $this->resultSummary = null;
        $this->phoneNumber = $service->normalizePhoneNumber($this->phoneNumber);
        $this->includeIdentityData = $service->isTrustedRequester(request()->user());
        try {
            $this->result = $service->performLookup($this->phoneNumber, request()->user(), request()->ip(), null, $this->includeIdentityData);
        } catch (HttpException $e) {
            $this->errorMessage = $e->getMessage();
            return;
        } catch (TwilioException) {
            $this->errorMessage = 'Something went wrong looking up that number. Please try again later.';
            return;
        }
        $this->formattedResult = $service->extractData($this->result, $this->includeIdentityData);
        $this->resultSummary = $service->toSms($this->formattedResult);
        session()->flash('message', 'Lookup complete');
    }

    public function hasResult(): bool
    {
        return $this->result !== null;
    }
}
