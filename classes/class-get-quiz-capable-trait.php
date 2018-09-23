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
    protected function get_quiz($id) {
        $result = get_post($id);

        if (is_wp_error($result) || is_null($result)) {
            throw new OutOfRangeException(vsprintf('Could not find quiz "%1$s"', [$id]));
        }

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
}