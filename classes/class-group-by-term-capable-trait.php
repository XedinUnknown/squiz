<?php
/**
 * Group_By_Term_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use RuntimeException;
use WP_Post;
use WP_Term;

/**
 * Functionality for grouping of entities by a taxonomy term.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Group_By_Term_Capable_Trait {

    /**
     * Groups posts by their terms.
     *
     * @since [*next-version*]
     *
     * @param WP_Post[] $entities The list of entities to group.
     * @param string[] $taxonomy The list of taxonomy name, which to retrieve terms of.
     *
     * @throws RuntimeException If could not group.
     *
     * @return array<int, <int, WP_Post>> A map of term ID to a list of of posts for that term.
     * Posts that that don't have any group assigned will be grouped under `0`.
     */
    protected function group_by_term(array $entities, array $taxonomy) {
        $grouped = [];

        foreach ($entities as $_idx => $entity) {
            try {
                $groups = $this->get_terms_for_post_id($entity->ID, $taxonomy);
            } catch (RuntimeException $e) {
                throw new RuntimeException(vsprintf('Could not get terms for entity at index "%1$s"', [$_idx]), 0, $e);
            }

            /* @var $groups WP_Term[] */

            // Posts without groups
            if (!count($groups)) {
                $term_id = 0;

                // Creating empty list
                if (!isset($grouped[$term_id])) {
                    $grouped[$term_id] = [];
                }

                $grouped[$term_id][] = $entity;
                continue;
            }

            // Posts with groups
            foreach ($groups as $group) {
                $term_id = (int) $group->term_id;

                // Creating empty list
                if (!isset($grouped[$term_id])) {
                    $grouped[$term_id] = [];
                }

                $grouped[$term_id][] = $entity;
            }
        }

        return $grouped;
    }

    /**
     * Retrieves terms for a post by post ID.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the post to get the terms for.
     * @param string[] $taxonomy The list of taxonomy name, which to retrieve terms of.
     *
     * @throws RuntimeException If terms could not be retrieved.
     *
     * @return WP_Term[] The list of terms for the specified post ID.
     */
    abstract protected function get_terms_for_post_id(int $id, array $taxonomy): array;
}
