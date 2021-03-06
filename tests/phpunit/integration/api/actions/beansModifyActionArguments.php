<?php
/**
 * Tests for beans_modify_action_arguments()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansModifyActionArguments
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   unit-integration
 * @group   api
 */
class Tests_BeansModifyActionArguments extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_arguments() should return false when the ID is not registered.
	 */
	public function test_should_return_false_when_id_not_registered() {
		$ids = array(
			'foo'   => null,
			'bar'   => 0,
			'baz'   => 1,
			'beans' => '3',
		);

		foreach ( $ids as $id => $number_of_args ) {
			$this->assertFalse( beans_modify_action_arguments( $id, $number_of_args ) );
		}
	}

	/**
	 * Test beans_modify_action_arguments() should return false when new args is a non-integer value.
	 */
	public function test_should_return_false_when_args_is_non_integer() {
		$ids = array(
			'foo'   => null,
			'bar'   => array( 10 ),
			'baz'   => false,
			'beans' => '',
		);

		foreach ( $ids as $id => $number_of_args ) {
			$action = $this->setup_original_action( $id, true );

			$this->assertFalse( beans_modify_action_arguments( $id, $number_of_args ) );
			$this->assertTrue( has_action( $action['hook'] ) );

			// Check that the parameters did not change.
			$this->check_parameters_registered_in_wp( $action );
		}
	}

	/**
	 * Test beans_modify_action_arguments() should modify the action's "args" when the new one is zero.
	 */
	public function test_should_modify_action_when_args_is_zero() {
		$ids = array(
			'foo'   => 0,
			'bar'   => 0.0,
			'baz'   => '0',
			'beans' => '0.0',
		);

		foreach ( $ids as $id => $number_of_args ) {
			$action = $this->setup_original_action( $id );
			$this->assertTrue( beans_modify_action_arguments( $id, $number_of_args ) );

			// Manually change the original to the new one.  Then test that it did change in WordPress.
			$action['args'] = (int) $number_of_args;
			$this->check_parameters_registered_in_wp( $action );
		}
	}

	/**
	 * Test beans_modify_action_arguments() should modify the registered action's args.
	 */
	public function test_should_modify_the_action_args() {
		$action          = $this->setup_original_action( 'beans' );
		$modified_action = array(
			'args' => 3,
		);
		$this->assertTrue( beans_modify_action_arguments( 'beans', $modified_action['args'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->check_parameters_registered_in_wp( array_merge( $action, $modified_action ) );
	}
}
