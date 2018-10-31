<?php
/**
 * Submission_Result_Output_Handler class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Throwable;

/**
 * Reacts to new quiz submissions.
 *
 * @package SQuiz
 */
class Submission_Result_Output_Handler extends Handler {

    /* @since [*next-version*] */
    use Config_Aware_Trait;

    /* @since [*next-version*] */
    use Get_Posts_Capable_Trait;

    /**
     * The object responsible for the creation of the submission document.
     *
     * @since [*next-version*]
     *
     * @var Submission_Document_Creator
     */
    protected $documentCreator;

    /**
     * The request variable name that contains the Submission slug for display.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $submission_request_var_name;

    public function __construct(
        DI_Container $config,
        Submission_Document_Creator $documentCreator,
        string $submission_request_var_name
    ) {
        parent::__construct($config);
        $this->documentCreator = $documentCreator;
        $this->submission_request_var_name = $submission_request_var_name;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function hook() {
        add_action('init', function () {
            $var_name = $this->submission_request_var_name;
            if (isset($_GET[$var_name])) {
                $sid = $_GET[$var_name];
                $submissions = $this->get_posts([
                    'post_type'     => $this->get_config('quiz_submission_post_type'),
                    'name'          => $sid,
                    'post_status'   => 'any',
                    'numberposts'   => 1,
                ]);
                $submission = reset($submissions);

                $document = $this->render_document_for_submission( intval( $submission->ID ) );
                echo $document;
            }
        });
    }

    /**
     * Retrieves the output of the submission document.
     *
     * @param int $submission_id The ID of the Submission to render the document for.
     *
     * @throws Throwable If problem rendering.
     *
     * @return string The output of the submission document.
     */
    protected function render_document_for_submission( int $submission_id ): string {
        return $this->documentCreator->get_document_output( $submission_id );
    }
}
