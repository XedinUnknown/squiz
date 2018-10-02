<?php
/**
 * Get_Terms_For_Post_Id_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use RuntimeException;
use WP_Term;

/**
 * Functionality for retrieving terms
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Get_Terms_For_Post_Id_Capable_Trait {

    /**
     * Retrieves terms for a post by post ID.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the post to get the terms for.
     * @param string[] $taxonomy The taxonomy or taxonomy list, which to retrieve terms of.
     *
     * @throws RuntimeException If terms could not be retrieved.
     *
     * @return WP_Term[] The list of terms for the specified post ID.
     */
    protected function get_terms_for_post_id(int $id, array $taxonomy): array {
        $terms = wp_get_post_terms($id, $taxonomy);

        if (is_wp_error($terms)) {
            throw new RuntimeException(vsprintf('Could not get terms for post ID "%1$s"', [$id]));
        }

        return $terms;
    }
}
