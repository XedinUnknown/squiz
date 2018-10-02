<?php
/**
 * Quiz_Submission_Handler class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use RuntimeException;


/**
 * Responsible for registering questions and answers related types and relationships between them.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
class Quiz_Submission_Handler extends Handler
{
    /* @since [*next-version*] */
    use Get_Quiz_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Post_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Submission_Uid_Capable_Trait;

    /**
     * Qanda_Fields_Types_Handler constructor.
     *
     * @since [*next-version*]
     *
     * @param DI_Container $config
     */
    public function __construct(DI_Container $config)
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function hook() {
        add_action( 'init', function () {
            $answer_groups_var = $this->get_config('submission_answer_groups_var_name');
            if (!is_admin() && isset($_POST['quiz_id']) && isset($_POST[$answer_groups_var])) {
                do_action('squiz_submitted', intval($_POST['quiz_id']), (array)$_POST[$answer_groups_var]);
            }
        } );

        add_action( 'squiz_submitted', function ($quiz_id, $answer_groups) {
            $this->create_quiz_submission($quiz_id, $answer_groups);
        }, 10, 2 );
    }

    /**
     * Creates a new quiz submission.
     *
     * Handles post creation, custom fields, and slug.
     *
     * @since [*next-version*]
     *
     * @param int $quiz_id A quiz ID.
     * @param array<int, array<int, int>> $answer_groups A map of question IDs to lists of answer IDs.
     *
     * @throws RuntimeException If submission could not be created.
     *
     * @return int The ID of the new submission post.
     */
    public function create_quiz_submission($quiz_id, $answer_groups) {
        $quiz = $this->get_quiz($quiz_id);

        $submission_id = wp_insert_post([
            'post_name' => $this->get_submission_uid($quiz_id, $answer_groups),
            'post_title' => vsprintf(__('Submission For Quiz "%1$s"', 'squiz'), [get_the_title($quiz)]),
            'post_type' => $this->get_config('quiz_submission_post_type'),
        ]);

        if (is_wp_error($submission_id)) {
            throw new RuntimeException(vsprintf('Could not create submission for quiz "%1$s"', [$quiz_id]));
        }

        update_post_meta($submission_id, $this->get_config('submission_field_quiz_id'), $quiz_id);
        update_post_meta($submission_id, $this->get_config('submission_field_grouped_answers'), json_encode($answer_groups));

        do_action('quiz_submission_created', $submission_id);

        return $submission_id;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function get_quiz_post_type_name()
    {
        return $this->get_config('quiz_post_type');
    }
}
