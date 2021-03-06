<?php

/**
 * Get_Posts_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use RuntimeException;
use WP_Post;
use WP_Query;

/**
 * Functionality for querying posts.
 *
 * @package SQuiz
 */
trait Get_Posts_Capable_Trait {

	/**
	 * Retrieves posts matching specified parameters.
	 *
	 * @param array $args The arguments for the query.
	 * {@see https://codex.wordpress.org/Class_Reference/WP_Query#Parameters}
	 *
	 * @throws RuntimeException If a problem occurred while querying.
	 *
	 * @return WP_Post[] A list of posts that match the conditions of the args.
	 */
	protected function get_posts( array $args ) {
		$query = new WP_Query();
		global $wpdb;

		$args = wp_parse_args(
			$args,
			[
				'posts_per_page' => -1,
			]
		);

		if (isset( $args['post_status'] ) && $args['post_status'] === 'any') {
			$args['post_status'] = get_post_stati( [ 'exclude_from_search' => false ] );
		}

		$results = $query->query( $args );
		if ( ! empty( $wpdb->last_error ) ) {
			throw new RuntimeException( $wpdb->last_error );
		}

		return $results;
	}
}
