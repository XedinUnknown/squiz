<?php
/**
 * Get_Term_Meta_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

/**
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Get_Term_Meta_Capable_Trait {

	/**
	 * Retrieves metadata for a term ID.
	 *
	 * @since [*next-version*]
	 *
	 * @param int    $term_id The ID of the term to get the data for.
	 * @param string $key The meta key of the data.
	 *
	 * @return mixed The metadata.
	 */
	protected function get_term_meta( int $term_id, string $key ) {
		$value = get_term_meta( $term_id, $key );

		return $value;
	}
}
