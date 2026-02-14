<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_small_business_landing_page_loads(): void
    {
        $response = $this->get(route('landing.small-business'));

        $response->assertStatus(200);
        $response->assertSee('Is Your Website Putting');
        $response->assertSee('Scan My Website Free');
    }

    public function test_agencies_landing_page_loads(): void
    {
        $response = $this->get(route('landing.agencies'));

        $response->assertStatus(200);
        $response->assertSee('Recurring Revenue');
        $response->assertSee('Get Lifetime Access');
    }
}
