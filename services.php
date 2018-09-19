<?php
/**
 * Contains service definitions used by the plugin.
 *
 * @package TaxonomyQuiz
 */

use XedinUnknown\TaxonomyQuiz\DI_Container;
use XedinUnknown\TaxonomyQuiz\PHP_Template;
use XedinUnknown\TaxonomyQuiz\Template_Block;

/**
 * A factory of a service definition map.
 *
 * @since 0.1
 *
 * @param string $base_path Path to the plugin file.
 * @param string $base_url URL of the plugin folder.
 *
 * @return array A map of service names to service definitions.
 */
return function ( $base_path, $base_url ) {
		return [
			'version'                         => '[*next-version*]',
			'base_path'                       => $base_path,
			'base_dir'                        => dirname( $base_path ),
			'base_url'                        => $base_url,
			'js_path'                         => '/assets/js',
			'templates_dir'                   => '/templates',
			'translations_dir'                => '/languages',
			'text_domain'                     => 'taxonomy-quiz',

			/*
			 * Makes templates.
			 *
			 * @since 0.1
			 */
			'template_factory'                => function ( DI_Container $c ) {
				return function ( $path ) {
					return new PHP_Template( $path );
				};
			},

			/*
			 * Makes blocs.
			 *
			 * @since 0.1
			 */
			'block_factory'                   => function ( DI_Container $c ) {
				return function ( PHP_Template $template, $context ) {
					return new Template_Block( $template, $context );
				};
			},

			/*
			 * List of handlers to run.
			 *
			 * @since 0.1
			 */
			'handlers'                        => function ( DI_Container $c ) {
				return [
				];
			},
		];
};
