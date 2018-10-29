<?php
/**
 * Answers_To_Courses_Relationship_Name_Aware_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Exception;

/**
 * Awareness of the relationship name between Answer and Course objects.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Answers_To_Courses_Relationship_Name_Aware_Trait {

    /**
     * The name of the relationship name between Answers and Courses.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $answers_to_courses_relationship_name;

    /**
     * Retrieves the Answers to Courses relationship name associated with this instance.
     *
     * @since [*next-version*]
     *
     * @throws Exception If problem retrieving.
     *
     * @return string The relationship name.
     */
    protected function get_answers_to_courses_relationship_name(): string {
        return $this->answers_to_courses_relationship_name;
    }

    /**
     * Assigns the Answers to Courses relationship name to this instance.
     *
     * @since [*next-version*]
     *
     * @param string $name The relationship name.
     *
     * @throws Exception If could not assign.
     */
    protected function set_answers_to_courses_relationship_name( string $name ) {
        $this->answers_to_courses_relationship_name = $name;
    }
}
