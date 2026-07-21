<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NavigationHeaderTest extends TestCase
{
    #[Test]
    public function publicHeaderIncludesLogbookInTheToolsDropdown(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(
            '<a href="'.route('radio').'" class="block py-1 lg:py-2 lg:px-4 nav-link lg:text-gray-700 lg:hover:bg-gray-100 lg:hover:text-gray-900">',
            false,
        );
        $response->assertSee('Logbook');
        $response->assertDontSee(route('admin.logbook.index'), false);
    }
}
