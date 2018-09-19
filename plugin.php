<?php
/**
 * Product Code for WooCommerce plugin.
 *
 * @package TaxonomyQuiz
 * @wordpress-plugin
 *
 * Plugin Name: Taxonomy Quiz
 * Description: A quiz plugin which relates answers to taxonomies
 * Version: [*next-version*]
 * Author: Anton Ukhanev
 * Author URI: https://twitter.com/XedinUnknown
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: taxonomy-quiz
 * Domain Path: /languages
 */

namespace XedinUnknown\TaxonomyQuiz;

/**
 * Retrieves the plugin singleton.
 *
 * @since 0.1
 *
 * @return null|Plugin
 */
function plugin() {
	static $instance = null;

	$autoload_file = __DIR__ . '/vendor/autoload.php';
	if ( file_exists( $autoload_file ) ) {
		require $autoload_file;
	}

	if ( is_null( $instance ) ) {
		$base_path        = __FILE__;
		$base_dir         = dirname( $base_path );
		$base_url         = plugins_url( '', $base_path );
		$services_factory = require_once "$base_dir/services.php";
		$services         = $services_factory( $base_path, $base_url );
		$container        = new DI_Container( $services );

		$instance = new Plugin( $container );
	}

	return $instance;
}

plugin()->run();
