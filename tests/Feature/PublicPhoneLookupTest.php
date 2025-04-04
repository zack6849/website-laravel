<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\PhoneNumberLookup;
use App\Models\User;
use App\Services\TwilioService;
use Illuminate\Container\Attributes\Database;
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
    private int $authenticatedLimit = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $upperbound = 100;
        $this->limit = $this->faker->numberBetween(3, $upperbound / 2);
        $this->authenticatedLimit = $this->faker->numberBetween($this->limit, $upperbound);
        config([
            'twilio.public.rate_limit' => $this->limit,
            'twilio.authenticated.rate_limit' => $this->authenticatedLimit,
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
            ->assertSeeText("You have {$this->authenticatedLimit} lookups remaining")
            ->assertSet('dailyLimit', $this->authenticatedLimit);
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
    public function respectsRateLimitConfiguration($type, $user = null): void
    {
        $this->mockTwilioService();
        config([
            "twilio.$type.rate_limit" => 1,
            "twilio.$type.decay_rate" => 9999
        ]);
        $user = $user == null ? null : $user();
        $this->getLivewireInstance(PhoneNumberLookup::class, $user)
            ->assertSet('remainingLookups', 1)
            ->assertSet('rateLimited', false)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('remainingLookups', 0)
            ->assertSet('rateLimited', true);
        $this->travelTo(
            now()->addSeconds(config("twilio.$type.decay_rate")),
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
        config([
            "twilio.$type.rate_limit" => 1,
            "twilio.$type.decay_rate" => 9999
        ]);
        $user = $user == null ? null : $user();
        $this->getLivewireInstance(PhoneNumberLookup::class, $user)
            ->assertSet('remainingLookups', 1)
            ->assertSet('rateLimited', false)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('remainingLookups', 1)
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
                'type' => 'authenticated',
                'user' => fn() => User::factory()->create(),
            ],
            'unauthenticated user' => [
                'type' => 'public',
            ],
        ];
    }
}
