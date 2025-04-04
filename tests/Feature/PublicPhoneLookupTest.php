<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\PhoneNumberLookup;
use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicPhoneLookupTest extends TestCase
{

    use WithFaker;

    private int $limit = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->limit = $this->faker->numberBetween(3, 5);
        config([
            'twilio.public.rate_limit' => $this->limit,
        ]);
    }

    private function mockTwilioService(bool $fakeCachePrimed = false): void
    {
        $this->partialMock(TwilioService::class, function (MockInterface $mock) use ($fakeCachePrimed) {
            $mock->shouldReceive('lookupNumber')->andReturn([
                'fake' => 'response',
            ]);
            $mock->shouldReceive('extractData')->andReturn([
                'test' => 'data',
            ]);
            $mock->shouldReceive('toSms')->andReturn('This is a test');
            if ($fakeCachePrimed) {
                $mock->shouldReceive('hasCachedResponseFor')->andReturn(true);
            }
        });
    }

    #[Test]
    public function showsLimit(): void
    {
        Livewire::test(PhoneNumberLookup::class)
            ->assertSeeText("You have {$this->limit} lookups remaining")
            ->assertSet('dailyLimit', $this->limit);
        $user = User::factory()->create();
        Livewire::actingAs($user)
            ->test(PhoneNumberLookup::class)
            ->assertSeeText("You have {$user->lookup_limit} lookups remaining")
            ->assertSet('dailyLimit', $user->lookup_limit);
    }

    #[Test]
    public function showsAvailable(): void
    {
        Livewire::test(PhoneNumberLookup::class)
            ->assertSet('remainingLookups', $this->limit)
            ->assertSeeText("You have {$this->limit} lookups remaining");

        $this->mockTwilioService();

        $remaining = $this->limit - 1;
        Livewire::test(PhoneNumberLookup::class)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('remainingLookups', $remaining)
            ->assertSeeText("You have {$remaining} lookups remaining");
    }

    #[Test]
    #[DataProvider('userTypeDataProvider')]
    public function respectsRateLimitConfiguration($user): void
    {
        $this->mockTwilioService();

        $user = $user();
        if($user == null){
            config([
                'twilio.public.rate_limit' => 1,
                'twilio.public.decay_rate' => 999,
            ]);
        }else {
            $user->update([
                'lookup_limit' => 1,
                'lookup_decay_rate' => 999,
            ]);
        }

        $this->getLivewireInstance(PhoneNumberLookup::class, $user)
            ->assertSet('remainingLookups', 1)
            ->assertSet('rateLimited', false)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('remainingLookups', 0)
            ->assertSet('rateLimited', true);
        $this->travelTo(
            now()->addSeconds(999),
            function () use ($user) {
                $this->getLivewireInstance(PhoneNumberLookup::class, $user)
                    ->assertSet('remainingLookups', 1)
                    ->assertSet('rateLimited', false)
                    ->assertSeeText('You have 1 lookups remaining');
            }
        );
    }

    #[Test]
    #[DataProvider('userTypeDataProvider')]
    public function cachedResponsesDontConsumeRateLimit($type, $user = null): void
    {
        $this->mockTwilioService(true);
        $user = $user == null ? null : $user();
        $limit = $user?->lookup_limit ?? $this->limit;
        $this->getLivewireInstance(PhoneNumberLookup::class, $user)
            ->assertSet('remainingLookups', $limit)
            ->assertSet('rateLimited', false)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('remainingLookups', $limit)
            ->assertSet('rateLimited', false);
    }

    /**
     * @param string $class the name of the class, eg LiveWireComponent::class
     * @param User|null $user the user to act as
     * @param string|null $driver what driver to use when setting the authenticated user
     * @return Testable
     */
    private function getLivewireInstance(string $class, User $user = null, string $driver = null): Testable
    {
        if ($user !== null) {
            return Livewire::actingAs($user, $driver)
                ->test($class);
        }
        return Livewire::test($class);
    }

    public static function userTypeDataProvider(): array
    {
        return [

            "authenticated user" => [
                'user' => fn() => User::factory()->create(),
            ],
            'unauthenticated user' => [
                'user' => fn() => null,
            ],
        ];
    }
}
