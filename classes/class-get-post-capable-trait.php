<?php
/**
 * Get_Post_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use OutOfRangeException;
use WP_Post;

/**
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Get_Post_Capable_Trait {

    /**
     * Retrieves a post by ID.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the post to get.
     *
     * @throws OutOfRangeException If post for the specified ID does not exist.
     *
     * @return WP_Post The quiz post.
     */
    protected function get_post(int $id): WP_Post {
        $result = get_post($id);

        if (is_wp_error($result) || is_null($result)) {
            throw new OutOfRangeException(vsprintf('Could not find post "%1$s"', [$id]));
        }

        return $result;
    }
}
