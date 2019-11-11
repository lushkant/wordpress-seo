<?php

namespace Yoast\WP\Free\Tests\Presenters;

use Mockery;
use Brain\Monkey;
use Yoast\WP\Free\Presentations\Indexable_Presentation;
use Yoast\WP\Free\Presenters\Robots_Presenter;
use Yoast\WP\Free\Tests\TestCase;

/**
 * Class Robots_Presenter_Test
 *
 * @coversDefaultClass \Yoast\WP\Free\Presenters\Robots_Presenter
 *
 * @group presenters
 *
 * @package Yoast\WP\Free\Tests\Presenters
 */
class Robots_Presenter_Test extends TestCase {

	/**
	 * @var Robots_Presenter
	 */
	private $instance;

	/**
	 * Sets up the test class.
	 */
	public function setUp() {
		parent::setUp();

		$this->instance = Mockery::mock( Robots_Presenter::class )
			->makePartial()
			->shouldAllowMockingProtectedMethods();
	}

	/**
	 * Tests whether the presenter returns the correct meta tag.
	 *
	 * @covers ::present
	 */
	public function test_present() {
		$indexable_presentation = new Indexable_Presentation();
		$indexable_presentation->robots = [
			'index'  => 'index',
			'follow' => 'nofollow',
		];

		$actual   = $this->instance->present( $indexable_presentation );
		$expected = '<meta name="robots" content="index,nofollow"/>';

		$this->assertEquals( $actual, $expected );
	}

	/**
	 * Tests whether the presenter returns the correct meta tag, when the `wpseo_robots` filter is applied.
	 *
	 * @covers ::present
	 * @covers ::filter
	 */
	public function test_present_filter() {
		$indexable_presentation = new Indexable_Presentation();
		$indexable_presentation->robots = [
			'index'  => 'index',
			'follow' => 'nofollow',
		];

		Monkey\Filters\expectApplied( 'wpseo_robots' )
			->once()
			->with( 'index,nofollow', $indexable_presentation )
			->andReturn( 'noindex' );

		$actual   = $this->instance->present( $indexable_presentation );
		$expected = '<meta name="robots" content="noindex"/>';

		$this->assertEquals( $actual, $expected );
	}

	/**
	 * Tests the situation where the presentation is empty.
	 *
	 * @covers ::present
	 */
	public function test_present_empty() {
		$indexable_presentation = new Indexable_Presentation();
		$indexable_presentation->robots = [];

		$this->assertEmpty( $this->instance->present( $indexable_presentation ) );
	}

	/**
	 * Tests if the default and null values are removed from the robots options array.
	 *
	 * @covers ::present
	 * @covers ::remove_defaults
	 */
	public function test_present_with_filtering_default_and_null_values() {
		$indexable_presentation = new Indexable_Presentation();
		$indexable_presentation->robots = [
			'index'        => 'index',
			'follow'       => 'follow',
			'noimageindex' => 'noimageindex',
			'nosnippet'    => null,
			'noarchive'    => null,
		];

		$actual   = $this->instance->present( $indexable_presentation );
		$expected = '<meta name="robots" content="noimageindex"/>';

		$this->assertEquals( $actual, $expected );
	}
}
