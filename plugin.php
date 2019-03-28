<?php
/**
 * Product Code for WooCommerce plugin.
 *
 * @package SQuiz
 * @wordpress-plugin
 *
 * Plugin Name: SQuiz
 * Description: A quiz plugin which relates answers to taxonomies
 * Version: 0.1.0-alpha6
 * Author: Anton Ukhanev
 * Author URI: https://twitter.com/XedinUnknown
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: squiz
 * Domain Path: /languages
 */

namespace XedinUnknown\SQuiz;

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
		$base_path            = __FILE__;
		$base_dir             = dirname( $base_path );
		$base_url             = plugins_url( '', $base_path );
		$services_factory     = require_once "$base_dir/services.php";
		$parent_template_path = get_template_directory();
		$child_template_path  = get_stylesheet_directory();
		$module_name          = 'squiz'; // Code of the plugin
		$services             = $services_factory(
			$base_path,
			$base_url,
			$module_name,
			$parent_template_path,
			$child_template_path
		);
		$container            = new DI_Container( $services );

		$instance = new Plugin( $container );
	}

	return $instance;
}

plugin()->run();
