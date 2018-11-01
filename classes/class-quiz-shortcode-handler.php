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
use Throwable;
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

    /* @since [*next-version*] */
    use Get_Post_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Posts_Capable_Trait;

    /* @since [*next-version*] */
    use Group_By_Term_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Terms_For_Post_Id_Capable_Trait;

    /* @since [*next-version*] */
    use Index_List_Capable_Trait;

    /**
     * The object responsible for the creation of the submission document.
     *
     * @since [*next-version*]
     *
     * @var Submission_Document_Creator
     */
    protected $documentCreator;

    /**
     * The field name of a Question that contains the max answers value for that question.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $question_max_answers_field_name;

    /**
     * The request field name that will contain the Submission ID for display.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $submission_request_var_name;

    /**
     * Quiz_Shortcode_Handler constructor.
     *
     * @since [*next-version*]
     *
     * @param DI_Container $config
     */
    public function __construct(
        DI_Container $config,
        Submission_Document_Creator $documentCreator,
        string $submission_request_var_name,
        string $question_max_answers_field_name
    ) {
        parent::__construct($config);

        $this->documentCreator = $documentCreator;
        $this->question_max_answers_field_name = $question_max_answers_field_name;
        $this->submission_request_var_name = $submission_request_var_name;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws Throwable If problem hooking.
     */
    protected function hook() {
        add_shortcode( $this->get_config('quiz_shortcode_name'), function ( $attributes, $content = '' ) {
            if (empty($attributes)) {
                $attributes = [];
            }

            $output = $this->get_shortcode_output( $attributes, $content );
            wp_enqueue_script( 'squiz-checkbox-group', null, null, null, true );

            return $output;
        } );

        add_action( 'init', function () {
            $this->register_assets();
        } );

        add_action('init', function () {
        });
    }

    protected function register_assets() {
        wp_register_script( 'squiz-checkbox-group', $this->get_js_url( 'checkbox-group.js' ), ['jquery'], $this->get_config('version') );
    }

    /**
     * Retrieves either the quiz or the result HTML.
     *
     *
     * @param $attributes
     * @param string $content
     * @throws Throwable
     *
     * @return string If the submission field is present in the URL query, retrieves the result HTML for that submission.
     * Otherwise, retrieves the quiz HTML.
     */
    protected function get_shortcode_output( $attributes, $content = '' ) {
        try {
            // Displaying submission result
            $var_name = $this->submission_request_var_name;
            if (isset($_GET[$var_name])) {
                return $this->render_document_for_submission( $_GET[$var_name] );
            }

            /// Displaying quiz
            $this->validate_attributes($attributes);

            return $this->get_quiz_output(intval($attributes['id']));
        } catch (Throwable $e) {
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
     * Retrieves the HTML output for a quiz.
     *
     * @param int $id The ID of the Quiz post to get the output for.
     *
     * @return string The HTML of the quiz.
     *
     * @throws Throwable If problem retrieving.
     */
    protected function get_quiz_output(int $id) {
        $quiz = $this->get_quiz($id);
        $questions = $this->get_quiz_questions($quiz->ID);
        $question_ids = array_keys( $questions );
        $question_groups = $this->get_question_groups_for_questions($question_ids);
        $grouped_questions = $this->get_grouped_questions($questions);
        $grouped_answers = $this->get_grouped_answers($question_ids);

        return $this->get_template('quiz')->render([
            'quiz' => $quiz,
            'question_groups' => $question_groups,
            'grouped_questions' => $grouped_questions,
            'grouped_answers' => $grouped_answers,
            'submission_answer_groups_var_name' => $this->get_config('submission_answer_groups_var_name'),
            'submit_url' => home_urL(add_query_arg([])),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws Throwable If problem retrieving.
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
     * @throws Throwable If problem retrieving.
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

        return $this->index_list(
            $questions,
            function (WP_Post $question) {
                $question->max_answers = intval( rwmb_meta( $this->question_max_answers_field_name, [], $question->ID ) );

                return $question;
            },
            function (WP_Post $question) {
                return $question->ID;
            }
        );
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
     * @throws Throwable If problem retrieving.
     *
     * @return array<int, array<WP_Post>> Lists of question posts, grouped by question group ID.
     * Questions that don't have any group assigned will be grouped under `0`.
     */
    protected function get_grouped_questions($questions) {
        return $this->group_by_term($questions, [$this->get_config('question_groups_taxonomy')]);
    }

    /**
     * Retrieves answers for questions with specified IDs, grouped by question ID.
     *
     * @since [*next-version*]
     *
     * @param int[] $question_ids A list of IDs of the questions to get answers for.
     *
     * @throws Throwable If problem retrieving.
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

    /**
     * Retrieves the output of the submission document.
     *
     * @param string $submission_code The code (slug) of the Submission to render the document for.
     *
     * @throws Throwable If problem rendering.
     *
     * @return string The output of the submission document.
     */
    protected function render_document_for_submission( string $submission_code ): string {
        $submissions = $this->get_posts([
            'post_type'     => $this->get_config('quiz_submission_post_type'),
            'name'          => $submission_code,
            'post_status'   => 'any',
            'numberposts'   => 1,
        ]);
        $submission = reset($submissions);

        if (!$submission) {
            throw new OutOfRangeException(sprintf(__('Submission with code "%1$s" not found'), $submission_code));
        }

        return $this->documentCreator->get_document_output( intval( $submission->ID ) );
    }
}
