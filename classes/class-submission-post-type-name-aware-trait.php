<?php
/**
 * Quiz_Submission_Type_Name_Aware_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Exception;

/**
 * Awareness of the post type name for Submission objects.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Quiz_Submission_Type_Name_Aware_Trait {

	/**
	 * The name of the Submission post type.
	 *
	 * @since [*next-version*]
	 *
	 * @var string
	 */
	protected $submission_post_type_name;

	/**
	 * Retrieves the Submission post type name associated with this instance.
	 *
	 * @since [*next-version*]
	 *
	 * @throws Exception If problem retrieving.
	 *
	 * @return string The post type name.
	 */
	protected function get_submission_post_type_name(): string {
		return $this->submission_post_type_name;
	}

	/**
	 * Assigns the Submission post type name to this instance.
	 *
	 * @since [*next-version*]
	 *
	 * @param string $name The post type name.
	 *
	 * @throws Exception if could not assign.
	 */
	protected function set_submission_post_type_name( string $name ) {
		$this->submission_post_type_name = $name;
	}
}
