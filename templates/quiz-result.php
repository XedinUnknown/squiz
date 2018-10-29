<?php
/**
 * A template for a quiz submission result document
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */

/**
 * The Quiz post for which this is a submission
 *
 * @since [*next-version*]
 *
 * @var $quiz WP_Post
 */
$quiz = $c('quiz');

/**
 * A map of Course ID to the Course post.
 *
 * @since [*next-version*]
 *
 * @var $courses WP_Post[]
 */
$courses = $c('courses');

/**
 * A map of Course Group IDs to lists of Course IDs for that group.
 *
 * @since [*next-version*]
 *
 * @var $grouped_course_ids array<int, array<int, int>>
 */
$grouped_course_ids = $c('grouped_course_ids');

/**
 * The Submission post for which the result is being rendered.
 *
 * @since [*next-version*]
 *
 * @var $submission WP_Post
 */
$submission = $c('submission');

/**
 * A map of Course Group IDs to Course Group terms.
 *
 * @since [*next-version*]
 *
 * @var $course_groups WP_Term[]
 */
$course_groups = $c('course_groups');
?>
<?php echo $quiz->post_content ?>

<?php foreach ($grouped_course_ids as $course_group_id => $course_ids): ?>
    <?php $course_group = $course_groups[$course_group_id] ?>
    <h2><?php echo esc_html($course_group->name) ?></h2>
    <?php foreach ($course_ids as $course_id): ?>
        <?php $course = $courses[$course_id] ?>
        <h3><?php echo get_the_title($course) ?></h3>
    <?php endforeach; ?>
<?php endforeach ?>
