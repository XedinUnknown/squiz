<?php
/**
 * Handler class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Throwable;

/**
 * A base class for all handlers.
 *
 * @package SQuiz
 */
abstract class Handler {

    /* @since [*next-version*] */
    use Config_Aware_Trait;

    /* @since [*next-version*] */
    use Get_Template_Capable_Trait;

	/**
	 * Handler constructor.
	 *
	 * @since 0.1
	 *
	 * @param DI_Container $config The configuration of this plugin.
	 */
	public function __construct( DI_Container $config ) {
		$this->_set_config_container($config);
	}

	/**
	 * Runs the plugin.
	 *
	 * @since 0.1
	 *
	 * @return mixed
	 */
	public function run() {
		$this->hook();

		return null;
	}

	/**
	 * Procedural way to run the handler.
	 *
	 * @since 0.1
	 *
	 * @return mixed The result of handling.
	 */
	public function __invoke() {
		return $this->run();
	}

	/**
	 * Retrieves a URL to the JS directory of the handler.
	 *
	 * @since 0.1
	 *
	 * @param string $path The path relative to the JS directory.
     *
     * @throws Throwable If problem retrieving.
	 *
	 * @return string The absolute URL to the JS directory.
	 */
	protected function get_js_url( $path = '' ) {
		$base_url = $this->get_config( 'base_url' );

		return "$base_url/assets/js/$path";
	}

	/**
	 * Retrieves a URL to the CSS directory of the handler.
	 *
	 * @since 0.1
	 *
	 * @param string $path The path relative to the CSS directory.
     *
     * @throws Throwable If problem retrieving.
	 *
	 * @return string The absolute URL to the CSS directory.
	 */
	protected function get_css_url( $path = '' ) {
		$base_url = $this->get_config( 'base_url' );

		return "$base_url/assets/css/$path";
	}

	/**
	 * Creates a new template block.
	 *
	 * @since 0.1
	 *
	 * @param PHP_Template|string $template The template or template key.
	 * @param array               $context The context for the template.
     *
     * @throws Throwable If problem retrieving.
	 *
	 * @return Template_Block The new block.
	 */
	protected function create_template_block( $template, $context ) {
		if ( ! ( $template instanceof PHP_Template ) ) {
			$template = $this->get_template( (string) $template );
		}

		$factory = $this->get_config( 'block_factory' );

		return $factory( $template, $context );
	}

	/**
	 * Adds handler hooks.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	abstract protected function hook();
}
