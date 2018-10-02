<?php
/**
 * Get_Quiz_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use OutOfRangeException;
use RangeException;
use WP_Post;

/**
 * Functionality for retrieving Quiz entities.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Get_Quiz_Capable_Trait {

    /**
     * Retrieves a quiz by ID.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the quiz to get.
     *
     * @throws OutOfRangeException If quiz for the specified ID does not exist.
     *
     * @return WP_Post The quiz post.
     */
    protected function get_quiz(int $id): WP_Post {
        $result = $this->get_post($id);

        if ($result->post_type !== $this->get_quiz_post_type_name()) {
            throw new RangeException(vsprintf('Post with ID "%1$s" is not a quiz', [$id]));
        }

        return $result;
    }

    /**
     * Retrieves the name of the Quiz post type.
     *
     * @since [*next-version*]
     *
     * @return string The name of the Quiz post type.
     */
    abstract protected function get_quiz_post_type_name();

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
    abstract protected function get_post(int $id): WP_Post;
}
