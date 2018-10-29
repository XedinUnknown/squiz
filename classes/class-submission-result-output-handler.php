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

    public function __construct(DI_Container $config, Submission_Document_Creator $documentCreator)
    {
        parent::__construct($config);
        $this->documentCreator = $documentCreator;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function hook() {
        add_action('init', function () {
            if (isset($_GET['submission'])) {
                $sid = $_GET['submission'];
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
