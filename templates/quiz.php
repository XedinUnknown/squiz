<?php
/**
 * A template for a single quiz.
 *
 * Outputs questions
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */

$quiz = $c('quiz');
/* @var $quiz WP_Post */
$question_groups = $c('question_groups');
/* @var $question_groups WP_Term[] */
$grouped_questions = $c('grouped_questions');
/* @var $grouped_questions array<int, array<int, WP_Post>> */
$grouped_answers = $c('grouped_answers');
/* @var $grouped_answers array<int, array<int, WP_Post>> */
?>
<?php $quiz_id = $quiz->ID ?>
<div class="quiz" id="quiz-<?php echo esc_attr($quiz_id) ?>">
    <h2 class="quiz-title"><?php echo get_the_title($quiz) ?></h2>
    <form action="" method="post">
        <input type="hidden" name="quiz_id" value="<?php echo esc_attr($quiz_id) ?>" />
    <?php if ( count($question_groups) || isset($grouped_questions[0]) ): ?>
        <ul class="question-groups">
        <?php foreach ($question_groups as $group): ?>
            <?php $group_id = $group->term_id ?>
            <?php if (isset($grouped_questions[$group_id]) && count($grouped_questions[$group_id])): ?>
            <?php $questions = $grouped_questions[$group_id] ?>
            <li>
                <div class="question-group" id="question-group-<?php echo esc_attr($group_id) ?>">
                    <h3 class="question-group-title"><?php echo esc_html($group->name) ?></h3>
                    <ul class="questions">
                <?php foreach ($questions as $question): ?>
                    <?php /* @var $question WP_Post */ ?>
                    <?php $question_id = $question->ID ?>
                            <li>
                                <div class="question question-<?php echo esc_attr($question->ID) ?>" id="question-<?php echo esc_attr($group_id) ?>-<?php echo esc_attr($question_id) ?>">
                                    <dl>
                                    <dt><h4 class="question-title"><?php echo get_the_title($question) ?></h4></dt>
                                    <dd class="question-answers">
                    <?php if (isset($grouped_answers[$question_id]) && count($grouped_answers[$question_id])): ?>
                                        <ol>
                        <?php foreach ($grouped_answers[$question_id] as $answer): ?>
                            <?php /* @var $answer WP_Post */ ?>
                            <?php $answer_id =$answer->ID ?>
                            <?php $html_answer_id = "answer_{$question_id}_{$answer_id}" ?>
                                            <li>
                                                <div class="answer answer-container-<?php echo esc_attr($answer_id) ?>">
                                                    <input type="checkbox" id="<?php echo esc_attr($html_answer_id) ?>" name="answers[<?php echo esc_attr($question_id) ?>][]" value="<?php echo esc_attr($answer_id) ?>" />
                                                    <label for="<?php echo esc_attr($html_answer_id) ?>"><?php echo get_the_title($answer) ?></label>
                                                </div>
                                            </li>
                        <?php endforeach ?>
                                        </ol>
                    <?php endif ?>
                                    </dd>
                                </dl>
                            </div>
                        </li>
                <?php endforeach ?>
                    </ul>
                </div>
            </li>
            <?php endif ?>
        <?php endforeach ?>
        </ul>
        <div class="form-actions">
            <input type="submit" value="Submit" />
        </div>
    <?php endif ?>
    </form>
</div>
