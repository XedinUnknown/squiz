<?php
/**
 * Get_Terms_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use WP_Term;
use WP_Term_Query;

/**
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Get_Terms_Capable_Trait {

    /**
     * Retrieves terms that match the specified arguments.
     *
     * @since [*next-version*]
     *
     * @param array $args A map of query argument names to values.
     *
     * @return WP_Term[] A list of terms that match the args.
     */
    protected function get_terms(array $args): array {
        $query = new WP_Term_Query();
        $result = $query->query($args);

        return $result;
    }
}
