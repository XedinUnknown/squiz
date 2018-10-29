<?php
/**
 * Index_Posts_By_Id_Capable_Trait class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use WP_Post;

/**
 * Functionality for indexing a list of posts by ID.
 *
 * @since [*next-version*]
 *
 * @package XedinUnknown\SQuiz
 */
trait Index_Posts_By_Id_Capable_Trait {

    /**
     * Indexes a list of posts by their ID.
     *
     * @since [*next-version*]
     *
     * @param iterable<WP_Post> $posts A list of entities to index.
     * @return array The map of post IDs to posts.
     */
    protected function index_posts_by_id($posts):array {
        return $this->index_list($posts, function ($entity) {
            return $entity;
        }, function (WP_Post $entity) {
            return $entity->ID;
        });
    }

    /**
     * Indexes a list of entities.
     *
     * @since [*next-version*]
     *
     * @param iterable $entities A list of entities to index.
     * @param callable $valueRetriever The callback that will retrieve the value for each index.
     * @param callable $indexRetriever The callback that will retrieve the index for each post.
     *
     * @return array The map of index to entity.
     */
    abstract protected function index_list($entities, callable $valueRetriever, callable $indexRetriever):array;
}
