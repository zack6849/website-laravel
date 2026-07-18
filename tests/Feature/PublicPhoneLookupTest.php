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
            'twilio.public_rate_limit' => $this->limit,
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
                'twilio.public_rate_limit' => 1,
                'twilio.public_decay_rate' => 999,
            ]);
        }else {
            $user->update([
                'lookup_limit' => 1,
                'lookup_decay_rate' => 999,
            ]);
        }

        $this->getLivewireInstance(PhoneNumberLookup::class, $user)
            ->assertSet('remainingLookups', 1)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('remainingLookups', 0);
        $this->travelTo(
            now()->addSeconds(999),
            function () use ($user) {
                $this->getLivewireInstance(PhoneNumberLookup::class, $user)
                    ->assertSet('remainingLookups', 1)
                    ->assertSeeText('You have 1 lookups remaining');
            }
        );
    }

    #[Test]
    #[DataProvider('userTypeDataProvider')]
    public function clearsThePreviousResultWhenARateLimitedLookupFails($user): void
    {
        $this->mockTwilioService();

        $user = $user();
        if ($user == null) {
            config([
                'twilio.public_rate_limit' => 1,
                'twilio.public_decay_rate' => 999,
            ]);
        } else {
            $user->update([
                'lookup_limit' => 1,
                'lookup_decay_rate' => 999,
            ]);
        }

        $instance = $this->getLivewireInstance(PhoneNumberLookup::class, $user)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup');

        //authenticated (trusted) requests get the full identity-tier summary; anonymous
        //visitors only ever see the basic carrier/line-type/caller-name card
        if ($user !== null) {
            $instance->assertSet('resultSummary', 'This is a test')
                ->assertSeeText('This is a test');
        } else {
            $instance->assertSeeText('Carrier:')
                ->assertDontSeeText('This is a test');
        }

        // a second, different (uncached) number should hit the limit and must not leave the
        // first number's result on screen looking like it belongs to this new attempt
        $instance->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('resultSummary', null)
            ->assertSet('formattedResult', null)
            ->assertSet('result', null)
            ->assertDontSeeText('This is a test');
    }

    #[Test]
    #[DataProvider('userTypeDataProvider')]
    public function stillShowsTheResultOfTheLookupThatHitsTheLimit($user): void
    {
        $this->mockTwilioService();

        $user = $user();
        if ($user == null) {
            config([
                'twilio.public_rate_limit' => 1,
                'twilio.public_decay_rate' => 999,
            ]);
        } else {
            $user->update([
                'lookup_limit' => 1,
                'lookup_decay_rate' => 999,
            ]);
        }

        $instance = $this->getLivewireInstance(PhoneNumberLookup::class, $user)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('remainingLookups', 0);

        if ($user !== null) {
            $instance->assertSet('resultSummary', 'This is a test')
                ->assertSeeText('This is a test');
        } else {
            $instance->assertSeeText('Carrier:');
        }
    }

    #[Test]
    #[DataProvider('userTypeDataProvider')]
    public function cachedResponsesDontConsumeRateLimit($user = null): void
    {
        $this->mockTwilioService(true);
        $user = $user == null ? null : $user();
        $limit = $user?->lookup_limit ?? $this->limit;
        $this->getLivewireInstance(PhoneNumberLookup::class, $user)
            ->assertSet('remainingLookups', $limit)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('remainingLookups', $limit);
    }

    #[Test]
    public function anonymousVisitorsNeverSeeIdentityDataEvenWhenTheServiceReturnsIt(): void
    {
        $this->partialMock(TwilioService::class, function (MockInterface $mock) {
            $mock->shouldReceive('lookupNumber')->andReturn(['fake' => 'response']);
            $mock->shouldReceive('extractData')->andReturn([
                'possible_owners' => ['Some Caller Name'],
                'carrier' => 'Verizon',
                'type' => 'mobile',
                'country' => 'US',
                'associated_people' => [],
                'associated_addresses' => [],
            ]);
            $mock->shouldReceive('toSms')->andReturn("Likely Owner: \n - Jane Q Public\nLikely Addresses: \n - 123 Secret St Tampa FL US\n");
        });

        Livewire::test(PhoneNumberLookup::class)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('includeIdentityData', false)
            ->assertSeeText('Verizon')
            ->assertSeeText('Some Caller Name')
            ->assertDontSeeText('Jane Q Public')
            ->assertDontSeeText('123 Secret St');
    }

    #[Test]
    public function authenticatedVisitorsSeeTheFullIdentitySummary(): void
    {
        $user = User::factory()->create();
        $this->partialMock(TwilioService::class, function (MockInterface $mock) {
            $mock->shouldReceive('lookupNumber')->andReturn(['fake' => 'response']);
            $mock->shouldReceive('extractData')->andReturn([
                'possible_owners' => ['name' => 'Jane Q Public'],
                'carrier' => 'Verizon',
                'type' => 'mobile',
                'country' => 'US',
                'associated_people' => [],
                'associated_addresses' => [
                    ['street' => '123 Secret St', 'city' => 'Tampa', 'state' => 'FL', 'country' => 'US'],
                ],
            ]);
            $mock->shouldReceive('toSms')->andReturn("Likely Owner: \n - Jane Q Public\nLikely Addresses: \n - 123 Secret St Tampa FL US\n");
        });

        Livewire::actingAs($user)
            ->test(PhoneNumberLookup::class)
            ->set('phoneNumber', $this->faker->phoneNumber)
            ->call('lookup')
            ->assertSet('includeIdentityData', true)
            ->assertSeeText('Jane Q Public')
            ->assertSeeText('123 Secret St');
    }

    /**
     * @param string $class the name of the class, eg LiveWireComponent::class
     * @param User|null $user the user to act as
     * @param string|null $driver what driver to use when setting the authenticated user
     * @return Testable
     */
    private function getLivewireInstance(string $class, ?User $user = null, ?string $driver = null): Testable
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
