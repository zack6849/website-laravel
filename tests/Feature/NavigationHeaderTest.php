<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NavigationHeaderTest extends TestCase
{
    #[Test]
    public function publicHeaderFeaturesLogbookAndPhoneLookupAsTopLevelLinks(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(
            '<a href="'.route('radio').'" class="block lg:inline-block lg:mt-0 nav-link mr-4">',
            false,
        );
        $response->assertSee(
            '<a href="'.route('phone.lookup.index').'" class="block lg:inline-block lg:mt-0 nav-link mr-4">',
            false,
        );
        $response->assertSee('Logbook');
        $response->assertSee("Who's Calling Me?", false);
        $response->assertDontSee(route('admin.logbook.index'), false);
    }
}
