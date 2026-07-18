<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    #[Test]
    public function homepageReturnsSuccessfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
