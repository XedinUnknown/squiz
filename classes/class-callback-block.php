<?php
/**
 * Callback_Block class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Exception;
use RuntimeException;

/**
 * Represents a block that wraps a callback.
 *
 * Useful for deferring arbitrary output generation.
 *
 * @since 0.1
 *
 * @package SQuiz
 */
class Callback_Block {

    /**
     * The callback that generates output.
     *
     * @since 0.1
     *
     * @var callable
     */
    protected $callback;

    /**
     * The context to render the template with.
     *
     * @since 0.1
     *
     * @var array
     */
    protected $context;

	/**
	 * Template_Block constructor.
	 *
	 * @since 0.1
	 *
	 * @param callable $callback The callback that generates output.
	 * @param array    $context  The list of arguments to pass to the callback.
	 */
	public function __construct( callable $callback, array $context ) {
		$this->callback = $callback;
		$this->context  = $context;
	}

	/**
	 * Runs the callback with pre-determined values, and retrieves the output.
	 *
	 * @since 0.1
	 *
	 * @return string The output.
	 */
	public function __toString() {
		try {
			ob_start();

            if ( ! is_callable( $this->callback ) ) {
                throw new RuntimeException( sprintf( 'Callback must be callable' ) );
            }

            call_user_func_array( $this->callback, $this->context );

			return ob_get_clean();
		} catch ( Exception $e ) {
			return $this->get_error_output( $e );
		}
	}

    /**
     * Retrieves the string representation of an error.
     *
     * @since [*next-version*]
     *
     * @param Exception $e The error to get the output for.
     *
     * @return string The string representation of the error.
     */
	protected function get_error_output( Exception $e ) {
        return (string) $e->getMessage() . PHP_EOL . $e->getTraceAsString();
    }
}
