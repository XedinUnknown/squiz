<?php
/**
 * Submission_Creation_Handler class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Exception;
use RangeException;
use Throwable;
use WP_Post;
use WP_Term;

/**
 * Reacts to new quiz submissions.
 *
 * @package SQuiz
 */
class Submission_Document_Creator {

    /* @since [*next-version*] */
    use Get_Quiz_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Post_Capable_Trait;

    /* @since [*next-version*] */
    use Group_By_Term_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Terms_For_Post_Id_Capable_Trait;

    /* @since [*next-version*] */
    use Index_Posts_By_Id_Capable_Trait;

    /* @since [*next-version*] */
    use Index_List_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Related_Posts_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Posts_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Term_Meta_Capable_Trait;

    /* @since [*next-version*] */
    use Get_Terms_Capable_Trait;

    /* @since [*next-version*] */
    use Quiz_Post_Type_Name_Aware_Trait;

    /* @since [*next-version*] */
    use Quiz_Submission_Type_Name_Aware_Trait;

    /* @since [*next-version*] */
    use Count_Grouped_Capable_Trait;

    /* @since [*next-version*] */
    use Course_Groups_Taxonomy_Name_Aware_Trait;

    /* @since [*next-version*] */
    use Answers_To_Courses_Relationship_Name_Aware_Trait;

    /* @since [*next-version*] */
    const COURSE_MEMBER_ANSWER_IDS = 'answer_ids';

    /**
     * Name of the submission field containing grouped answers.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $sf_grouped_answers;

    /**
     * Name of the submission field containing the quiz ID.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $sf_quiz_id;

    /**
     * Name of the Course Group field containing max amount of courses for that group.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $course_groups_max_courses_field_name;

    /**
     * The template for the submission result document.
     *
     * @since [*next-version*]
     *
     * @var PHP_Template
     */
    protected $document_template;



    /**
     * Submission_Document_Creator constructor.
     *
     * @since [*next-version*]
     *
     * @param string $sf_grouped_answers The
     *
     * @throws Throwable If problem creating instance.
     */
    public function __construct(
        string $sf_grouped_answers,
        string $sf_quiz_id,
        string $quiz_post_type_name,
        string $submission_post_type_name,
        string $course_groups_taxonomy_name,
        string $answers_to_courses_relationship_name,
        string $course_groups_max_courses_field_name,
        PHP_Template $document_template
    ) {
        $this->sf_grouped_answers = $sf_grouped_answers;
        $this->sf_quiz_id = $sf_quiz_id;
        $this->set_quiz_post_type_name($quiz_post_type_name);
        $this->set_submission_post_type_name($submission_post_type_name);
        $this->set_course_groups_taxonomy_name($course_groups_taxonomy_name);
        $this->set_answers_to_courses_relationship_name($answers_to_courses_relationship_name);
        $this->course_groups_taxonomy_name = $course_groups_taxonomy_name;
        $this->course_groups_max_courses_field_name = $course_groups_max_courses_field_name;
        $this->document_template = $document_template;
    }

    /**
     * Retrieves output of the document of the specified submission.
     *
     * @param int $submission_id The ID of the submission to get the document for.
     *
     * @return string The string or stringable object containing the output.
     *
     * @throws Throwable If output could not be generated.
     */
    public function get_document_output(int $submission_id): string {
        $submission = $this->get_submission($submission_id);
        $answer_groups = json_decode($submission->{$this->sf_grouped_answers});
        $quiz_id = $submission->{$this->sf_quiz_id};
        $quiz = $this->get_quiz($quiz_id);
        $quiz->post_content = apply_filters( 'the_content', $quiz->post_content );

        $answer_counts = $this->count_grouped($answer_groups);
        $answer_ids = array_keys($answer_counts);

        $courses = $this->get_courses_for_answers($answer_ids);
        $course_ids = array_keys($courses);
        $course_ratings = $this->get_course_ratings($courses);
        $grouped_courses = $this->get_grouped_courses($courses);
        $rated_course_groups = $this->get_allowed_grouped_course_ids($grouped_courses, $course_ratings);
        $course_groups = $this->get_course_groups($course_ids);

        return $this->document_template->render([
            'grouped_course_ids'                    => $rated_course_groups, // A map of Course Group IDs to lists of Course IDs - only allowed ones
            'courses'                               => $courses, // All courses associated with answers for the submission
            'course_groups'                         => $course_groups, // A map of Course Group IDs to Course Group terms
            'quiz'                                  => $quiz, // The quiz post
            'submission'                            => $submission, // The submission post
        ]);
    }

    /**
     * Retrieves a submission by ID.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the submission.
     *
     * @throws Exception If submission could not be retrieved.
     *
     * @return WP_Post The submission post.
     */
    protected function get_submission(int $id): WP_Post {
        $result = $this->get_post($id);

        if ($result->post_type !== $this->get_submission_post_type_name()) {
            throw new RangeException(vsprintf('Post with ID "%1$s" is not a quiz submission', [$id]));
        }

        return $result;
    }

    /**
     * Retrieves Course posts for answer IDs.
     *
     * @since [*next-version*]
     *
     * @param int[] $answer_ids IDs of answers to get courses for.
     *
     * @throws Throwable If problem retrieving.
     *
     * @return WP_Post[] The a map of course IDs to Course posts which are related to answers with specified IDs.
     */
    protected function get_courses_for_answers($answer_ids) {
        return $this->get_related_posts(
            $answer_ids,
            $this->get_answers_to_courses_relationship_name(),
            true,
            $this->get_course_groups_taxonomy_name(),
            static::COURSE_MEMBER_ANSWER_IDS);
    }

    /**
     * Rates courses according to the questions related to them.
     *
     * @since [*next-version*]
     *
     * @param WP_Post[] $courses A list of course post instances.
     * Each post must contain its associated answer IDs in the property defined by `$answers_member`.
     * @param string $answers_member The name of the property of each course post which contains the list of related answer IDs.
     *
     * @return array<int, int> A map of course IDs to their ratings.
     */
    protected function get_course_ratings(array $courses): array {
        $ratings = [];
        $answers_member = static::COURSE_MEMBER_ANSWER_IDS;

        foreach ($courses as $course) {
            $ratings[$course->ID] = isset($course->{$answers_member}) && is_array($course->{$answers_member})
                ? count($course->{$answers_member})
                : 0;
        }

        return $ratings;
    }

    /**
     * Groups Courses by their Course Groups.
     *
     * @since [*next-version*]
     *
     * @param WP_Post[] $courses A list of Course posts.
     *
     * @throws Exception If problem retrieving.
     *
     * @return array<int, array<int, WP_Post>> A map of Course Group IDs to lists of Course posts.
     */
    protected function get_grouped_courses($courses) {
        return $this->group_by_term($courses, [$this->get_course_groups_taxonomy_name()]);
    }

    /**
     * Retrieves grouped allowed course IDs.
     *
     * @param array<int, array<int, WP_Post>> $grouped_courses A map of term IDs to lists of Course posts.
     * @param array<int, int|float> $course_ratings A map of course IDs to their ratings.
     *
     * @throws Throwable If problem retrieving.
     *
     * @return array<int, array<int, int>> A map of Course Group IDs to lists of Course IDs.
     */
    protected function get_allowed_grouped_course_ids(array $grouped_courses, array $course_ratings): array {
        $allowed_grouped_courses = [];

        foreach ($grouped_courses as $term_id => $courses) {
            $max_courses = (int) $this->get_max_courses_for_group($term_id);

            $allowed_grouped_courses[$term_id] = [];
            $cur_course_idx = 0;

            $sorted_courses = $this->sort_courses_by_rating($courses, $course_ratings);

            foreach ($sorted_courses as $course) {
                /* @var $course WP_Post */
                $allowed_grouped_courses[$term_id][] = $course->ID;
                $cur_course_idx++;

                if ($cur_course_idx === $max_courses) {
                    break;
                }
            }
        }

        return $allowed_grouped_courses;
    }

    /**
     * Retrieves course groups for the specified course IDs.
     *
     * @param array $course_ids
     *
     * @throws Exception
     *
     * @return array
     */
    protected function get_course_groups(array $course_ids = []) {
        $terms = $this->get_terms([
            'taxonomy'              => $this->get_course_groups_taxonomy_name(),
            'object_ids'            => $course_ids,
        ]);

        return $this->index_list($terms, function ($value) {
            /* @var $value WP_Term */
            $value->long_description = rwmb_meta( 'long_description', ['object_type' => 'term'], $value->term_id );
            return $value;
        }, function (WP_Term $value) {
            return $value->term_id;
        });
    }

    /**
     * Retrieves the maximal amount of Courses that can be included for a Course Group.
     *
     * @since [*next-version*]
     *
     * @param int $id The ID of the Course Group term to get the max courses for.
     *
     * @throws Throwable If problem retrieving.
     *
     * @return int The max number of courses.
     */
    protected function get_max_courses_for_group(int $id): int {
        $value = $this->get_term_meta($id, $this->course_groups_max_courses_field_name);
        if (is_array($value)) {
            $value = array_shift($value);
        }

        return $value;
    }

    /**
     * Sorts courses by their rating.
     *
     * @since [*next-version*]
     *
     * @param WP_Post $courses WP_Post[] A list or map of course posts.
     * @param array<int, int|float> $course_ratings A map of course ID to rating.
     *
     * @return WP_Post[] A list or map of course posts.
     */
    protected function sort_courses_by_rating(array $courses, array $course_ratings): array {
        uasort($courses, function (WP_Post $a, WP_Post $b) use ($course_ratings) {
            // If one of the courses is unrated, order is irrelevant
            if (!isset($course_ratings[$a->ID]) || !isset($course_ratings[$b->ID])) {
                return 0;
            }

            $aRating = (float) $course_ratings[$a->ID];
            $bRating = (float) $course_ratings[$b->ID];

            if ($aRating === $bRating) {
                return 0;
            }

            return $aRating > $bRating ? 1 : -1;
        });

        return $courses;
    }


}
