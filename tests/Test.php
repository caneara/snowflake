<?php declare(strict_types=1);

namespace Snowflake\Tests;

use Snowflake\ServiceProvider;
use Orchestra\Testbench\TestCase;

class Test extends TestCase
{
    /**
     * Setup the test environment.
     *
     */
    protected function setUp() : void
    {
        parent::setUp();

        (new ServiceProvider(app()))->register();
    }

    /** @test */
    public function it_can_resolve_the_snowflake_service_and_generate_an_identifier() : void
    {
        $this->assertTrue(is_string(resolve('snowflake')->id()));

        $this->assertEquals(16, strlen(resolve('snowflake')->id()));
    }

    /** @test */
    public function it_can_generate_a_snowflake_identifier_using_the_global_helper() : void
    {
        $this->assertTrue(is_string(snowflake()));

        $this->assertEquals(16, strlen(snowflake()));
    }
}
