<?php
/**
 * Get_Related_Posts_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use RuntimeException;
use WP_Post;

/**
 * Functionality for retrieving related posts.
 *
 * Uses MB Relationships.
 *
 * @see https://github.com/wpmetabox/mb-relationships
 *
 * @package SQuiz
 */
trait Get_Related_Posts_Capable_Trait {
    /**
     * Retrieves posts related to those identified by the specified IDs.
     *
     * @since [*next-version*]
     *
     * @param array $related_to_ids A list of IDs to get the related posts for.
     * @param string $relationship_name The name of the relationship.
     * @param bool $isFrom Whether the relationship was specified as 'from' or 'to'.
     * @param string $post_type The type of the posts to retrieve. Default: all related posts of the relationship.
     * If a post type has `exclude_from_search` set to `true`, this MUST be set to the desired post type to yield results.
     * @param string $related_member What the property holding the list of related IDs should be called.
     *
     * @return WP_Post[] A map of related post IDs to their instances.
     * Each post will have an additional property corresponding to the `$related_member` argument, which will
     * contain a list of IDs of entities they are related to. Those should be some of the IDs from `$related_to_ids`.
     */
    protected function get_related_posts(
        array $related_to_ids,
        string $relationship_name,
        bool $isFrom,
        string $post_type = 'any',
        string $related_member = 'related_ids'
    ):array {
        $relationship_config = [
            'id'        => $relationship_name,
        ];
        $relationship_config[$isFrom ? 'from' : 'to'] = $related_to_ids;
        $entities = $this->get_posts([
            'post_type'     => $post_type,
            'relationship'  => $relationship_config,
        ]);

        $entity_map = [];

        foreach ($entities as $entity) {
            $entity_id = $entity->ID;

            // Add the entity to map once
            if (!isset($entity_map[$entity_id])) {
                $entity_map[$entity_id] = $entity;
            }

            $subject = $entity_map[$entity_id];
            $related_id = $entity->mb_origin;

            // Make sure related IDs set is an array
            if (!(isset($subject->{$related_member}) && is_array($subject->{$related_member}))) {
                $subject->{$related_member} = [];
            }

            // Add related ID to set once
            if (!in_array($related_id, $subject->{$related_member})) {
                $subject->{$related_member}[] = $related_id;
            }
        }

        return $entity_map;
    }

    /**
     * Retrieves posts matching specified parameters.
     *
     * @param array $args The arguments for the query.
     * {@see https://codex.wordpress.org/Class_Reference/WP_Query#Parameters}
     *
     * @throws RuntimeException If a problem occurred while querying.
     *
     * @return WP_Post[]|iterable A list of posts that match the conditions of the args.
     */
    abstract protected function get_posts(array $args);
}
