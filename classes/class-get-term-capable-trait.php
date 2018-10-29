<?php
/**
 * Get_Term_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use OutOfRangeException;
use RuntimeException;
use WP_Error;
use WP_Term;

/**
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Get_Term_Capable_Trait {

    /**
     * Retrieves a term for the specified ID.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the term to get.
     *
     * @throws OutOfRangeException If a term with the specified ID could not be found.
     * @throws RuntimeException If problem retrieving term.
     *
     * @return WP_Term The term for the specified ID.
     */
    protected  function get_term(int $id): WP_Term {
        $term = get_term($id);

        if (is_null($term)) {
            throw new OutOfRangeException(vsprintf('Term for ID "%1$s" not found', [$id]));
        }

        if (is_wp_error($term)) {
            /* @var $term WP_Error */
            throw new RuntimeException($term->get_error_message());
        }

        return $term;
    }
}
