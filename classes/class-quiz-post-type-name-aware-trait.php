<?php
/**
 * Quiz_Post_Type_Name_Aware_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Exception;

/**
 * Awareness of the post type name for Quiz objects.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Quiz_Post_Type_Name_Aware_Trait {

    /**
     * The name of the Quiz post type.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $quiz_post_type_name;

    /**
     * Retrieves the Quiz post type name associated with this instance.
     *
     * @since [*next-version*]
     *
     * @throws Exception If problem retrieving.
     *
     * @return string The post type name.
     */
    protected function get_quiz_post_type_name(): string {
        return $this->quiz_post_type_name;
    }

    /**
     * Assigns the Quiz post type name to this instance.
     *
     * @since [*next-version*]
     *
     * @param string $name The post type name.
     *
     * @throws Exception if could not assign.
     */
    protected function set_quiz_post_type_name( string $name ) {
        $this->quiz_post_type_name = $name;
    }
}
