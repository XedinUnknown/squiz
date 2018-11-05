<?php
/**
 * Fields_Types_Register_Capable_Trait class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Exception;
use MB_Relationships_API;
use MB_Relationships_Relationship;

/**
 * Functionality for registering types and relationships between them.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Fields_Types_Register_Capable_Trait {

	/**
	 * Registers relationships between types.
	 *
	 * @since [*next-version*]
	 *
	 * @param array[] $relationships An array of MetaBox relationships, where key is the relationship ID, and value is other relationship configuration.
	 *
	 * @throws Exception If relationship could not be registered.
	 */
	protected function register_relationships( $relationships ) {
		foreach ( $relationships as $id => $config ) {
			$result = MB_Relationships_API::register( array_merge( [ 'id' => $id ], $config ) );
			if ( ! ( $result instanceof MB_Relationships_Relationship ) ) {
				throw new Exception( vsprintf( 'Could not register relationship "%1$s"', [ $id ] ) );
			}
		}
	}

	/**
	 * Registers post types.
	 *
	 * @since [*next-version*]
	 *
	 * @param array[] $types A map of post type configurations, where key is post type name, and value is post type configuration.
	 *
	 * @throws Exception If a post type could not be registered.
	 */
	protected function register_post_types( $types ) {
		foreach ( $types as $type => $args ) {
			$result = register_post_type( $type, $args );
			if ( is_wp_error( $result ) ) {
				throw new Exception( vsprintf( 'Could not register post type "%1$s": %2%s', [ $type, $result->get_error_message() ] ) );
			}
		}
	}

	/**
	 * Registers taxonomies.
	 *
	 * @since [*next-version*]
	 *
	 * @param array[] $types A map of taxonomy configurations, where key is taxonomy name, and value is the configuration.
	 *
	 * @throws Exception If a taxonomy could not be registered.
	 */
	protected function register_taxonomies( $taxonomies ) {
		foreach ( $taxonomies as $name => $config ) {
			$result = register_taxonomy( $name, $config['object_type'], $config );
			if ( is_wp_error( $result ) ) {
				throw new Exception( vsprintf( 'Could not register taxonomy "%1$s": %2$s', [ $name, $result->get_error_message() ] ) );
			}
		}
	}

	/**
	 * Adds metabox entries to current list.
	 *
	 * Mostly intended to handle the `rwmb_meta_boxes` filter.
	 *
	 * @since [*next-version*]
	 * @see https://docs.metabox.io/extensions/mb-term-meta/#example
	 *
	 * @param int[] $metaboxes The current list of metabox entries.
	 *
	 * @return int[] The new list of metabox entries.
	 */
	protected function add_metaboxes( $metaboxes ) {
		return array_merge( $metaboxes, $this->get_metaboxes() );
	}

	/**
	 * Returns the metaboxes to create.
	 *
	 * @since [*next-version*]
	 *
	 * @see https://docs.metabox.io/extensions/mb-term-meta/
	 *
	 * @return array[] An array of MetaBox entries, each describing a metabox.
	 */
	protected function get_metaboxes():array {
		return (array) $this->get_config( 'taxonomy_metaboxes' );
	}

	/**
	 * Returns the relationships to create.
	 *
	 * @since [*next-version*]
	 *
	 * @see https://docs.metabox.io/extensions/mb-relationships/
	 *
	 * @return array[] An array of MetaBox relationships, where key is the relationship ID, and value is other relationship configuration.
	 */
	protected function get_relationships() {
		return (array) $this->get_config( 'field_relationships' );
	}

	/**
	 * Retrieves post type configurations.
	 *
	 * @since [*next-version*]
	 *
	 * @return array[] An array of post type configurations, where key is the post type name, and the value is post type arguments.
	 */
	protected function get_post_types() {
		return (array) $this->get_config( 'post_types' );
	}

	/**
	 * Retrieves taxonomy configurations.
	 *
	 * @since [*next-version*]
	 *
	 * @return array[] An array of taxonomy configurations, where key is the taxonomy name, and the value is taxonomy arguments.
	 */
	protected function get_taxonomies() {
		return (array) $this->get_config( 'taxonomies' );
	}
}
