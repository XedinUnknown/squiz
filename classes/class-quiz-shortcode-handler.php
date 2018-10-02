<?php
/**
 * Quiz_Shortcode_Handler class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use DomainException;
use Exception;
use OutOfRangeException;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_Term_Query;


/**
 * Responsible for registering questions and answers related types and relationships between them.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
class Quiz_Shortcode_Handler extends Handler
{
    /* @since [*next-version*] */
    use Get_Quiz_Capable_Trait;

    /**
     * Quiz_Shortcode_Handler constructor.
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
        add_shortcode( $this->get_config('quiz_shortcode_name'), function ( $attributes, $content = '' ) {
            if (empty($attributes)) {
                $attributes = [];
            }

            return $this->get_shortcode_output( $attributes, $content );
        } );
    }

    /**
     * @param $attributes
     * @param string $content
     * @return string
     * @throws Exception
     */
    protected function get_shortcode_output( $attributes, $content = '' ) {
        try {
            $this->validate_attributes($attributes);
            $quiz = $this->get_quiz($attributes['id']);
            $questions = $this->get_quiz_questions($quiz->ID);
            $question_ids = wp_list_pluck($questions, 'ID');
            $question_groups = $this->get_question_groups_for_questions($question_ids);
            $grouped_questions = $this->get_grouped_questions($questions);
            $grouped_answers = $this->get_grouped_answers($question_ids);

            return $this->get_template('quiz')->render([
                'quiz' => $quiz,
                'question_groups' => $question_groups,
                'grouped_questions' => $grouped_questions,
                'grouped_answers' => $grouped_answers,
                'submission_answer_groups_var_name' => $this->get_config('submission_answer_groups_var_name'),
            ]);
        } catch (Exception $e) {
            $message = __('Could not render SQuiz shortcode', 'squiz');
            if (WP_DEBUG) {
                throw new Exception($message, 0, $e);
            }

            return vsprintf('%1$s: %2$s', [$message, $e->getMessage()]);
        }
    }

    /**
     * Validates a set of attributes for the shortcode.
     *
     * @since [*next-version*]
     *
     * @throws DomainException If the set is invalid.
     *
     * @param array $attributes The attributes to validate.
     */
    protected function validate_attributes($attributes) {
        if ( !isset($attributes['id']) ) {
            throw new DomainException(vsprintf('The ID parameter is required', []));
        }
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

    /**
     * Retrieves questions for a specific quiz.
     *
     * @since [*next-version*]
     *
     * @param int $quiz_id The ID of the quiz to get the questions for.
     *
     * @return WP_Post[] The list of question posts.
     */
    protected function get_quiz_questions($quiz_id) {
        $query = new WP_Query();
        $questions = $query->query([
            'post_type' => $this->get_config('question_post_type'),
            'relationship' => [
                'id'   => $this->get_config('quizes_to_questions_relationship_name'),
                'from' => $quiz_id,
            ],
        ]);

        return $questions;
    }

    /**
     * Retrieves a list of question groups for questions with the specified IDs.
     *
     * If the Simple Taxonomy Ordering plugin is installed, the groups will be ordered according to their position.
     *
     * @since [*next-version*]
     *
     * @param int[] $questions The list of question IDs to get the groups for.
     *
     * @return WP_Term[] The list of question group terms.
     */
    protected function get_question_groups_for_questions($questions) {
        $query = new WP_Term_Query();
        $args = [
            'object_ids' => $questions,
        ];

        if (class_exists('Yikes_Custom_Taxonomy_Order')) {
            $args = array_merge($args, [
                'meta_key' => 'tax_position',
                'orderby' => 'tax_position',
            ]);

        }

        return $query->query($args);
    }

    /**
     * Groups questions by question group.
     *
     * @since [*next-version*]
     *
     * @param WP_Post[] $questions The questions to group.
     *
     * @return array<int, array<WP_Post>> Lists of question posts, grouped by question group ID.
     * Questions that dont' have any group assigned will be grouped under `0`.
     */
    protected function get_grouped_questions($questions) {
        $grouped = [];
        $group_taxonomy_name = $this->get_config('question_groups_taxonomy');

        foreach ($questions as $question) {
            $groups = wp_get_post_terms($question->ID, $group_taxonomy_name);
            /* @var $groups WP_Term[] */

            if (!count($groups)) {
                $term_id = 0;
                if (!isset($grouped[$term_id])) {
                    $grouped[$term_id] = [];
                }

                $grouped[$term_id][] = $question;
                continue;
            }

            foreach ($groups as $group) {
                $term_id = (int) $group->term_id;

                if (!isset($grouped[$term_id])) {
                    $grouped[$term_id] = [];
                }

                $grouped[$term_id][] = $question;
            }
        }

        return $grouped;
    }

    /**
     * Retrieves answers for questions with specified IDs, grouped by question ID.
     *
     * @since [*next-version*]
     *
     * @param int[] $question_ids A list of IDs of the questions to get answers for.
     *
     * @return array<int, array<int, WP_Post>> Lists of answers, grouped by question ID.
     */
    protected function get_grouped_answers($question_ids) {
        $query = new WP_Query();
        $answers = $query->query([
            'post_type' => $this->get_config('answer_post_type'),
            'relationship' => [
                'id'   => $this->get_config('questions_to_answers_relationship_name'),
                'from' => $question_ids,
            ],
        ]);
        /* @var $answers WP_Post[] */

        $groups = [];
        foreach ($answers as $answer) {
            $questionQuery = new WP_Query();
            $questions = $questionQuery->query([
                'post_type' => $this->get_config('question_post_type'),
                'relationship' => [
                    'id'   => $this->get_config('questions_to_answers_relationship_name'),
                    'to' => $answer->ID,
                ],
            ]);
            /* @var $questions WP_Post[] */

            foreach ($questions as $question) {
                $question_id = $question->ID;
                if (!isset($groups[$question_id])) {
                    $groups[$question_id] = [];
                }

                $groups[$question_id][] = $answer;
            }
        }

        return $groups;
    }
}
