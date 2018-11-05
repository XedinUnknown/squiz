<?php
/**
 * Course_Groups_Taxonomy_Name_Aware_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Exception;

/**
 * Awareness of the taxonomy name for Course Group objects.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Course_Groups_Taxonomy_Name_Aware_Trait {

	/**
	 * The name of the Course Groups taxonomy.
	 *
	 * @since [*next-version*]
	 *
	 * @var string
	 */
	protected $course_groups_taxonomy_name;

	/**
	 * Retrieves the Course Groups taxonomy name associated with this instance.
	 *
	 * @since [*next-version*]
	 *
	 * @throws Exception If problem retrieving.
	 *
	 * @return string The taxonomy name.
	 */
	protected function get_course_groups_taxonomy_name(): string {
		return $this->course_groups_taxonomy_name;
	}

	/**
	 * Assigns the Course Groups taxonomy name to this instance.
	 *
	 * @since [*next-version*]
	 *
	 * @param string $name The taxonomy name.
	 *
	 * @throws Exception if could not assign.
	 */
	protected function set_course_groups_taxonomy_name( string $name ) {
		$this->course_groups_taxonomy_name = $name;
	}
}
