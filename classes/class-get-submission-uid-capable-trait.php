<?php
/**
 * Quiz_Submission_Handler class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

/**
 * Functionality for creation of quiz submission UIDs.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
trait Get_Submission_Uid_Capable_Trait {

	/**
	 * Creates a new quiz submission UID.
	 *
	 * @since [*next-version*]
	 *
	 * @param int                         $quiz_id The ID of the quiz, for which to create the submission UID.
	 * @param array<int, array<int, int>> $answer_groups A map of question IDs to lists of answer IDs.
	 *
	 * @return string The UID.
	 */
	protected function get_submission_uid( $quiz_id, $answer_groups ) {
		return 'squiz-submission-' . md5(
			implode(
				'|',
				[
					$quiz_id,
					json_encode( $answer_groups ),
					uniqid( '', true ),
				]
			)
		);
	}
}
