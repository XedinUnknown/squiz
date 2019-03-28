<?php
/**
 * Contains service definitions used by the plugin.
 *
 * @package SQuiz
 */

use XedinUnknown\SQuiz\Callback_Block;
use XedinUnknown\SQuiz\DI_Container;
use XedinUnknown\SQuiz\Fields_Types_Handler;
use XedinUnknown\SQuiz\File_Path_Resolver;
use XedinUnknown\SQuiz\PHP_Template;
use XedinUnknown\SQuiz\Quiz_Shortcode_Handler;
use XedinUnknown\SQuiz\Quiz_Submission_Handler;
use XedinUnknown\SQuiz\Submission_Result_Output_Handler;
use XedinUnknown\SQuiz\Submission_Document_Creator;
use XedinUnknown\SQuiz\Template_Block;

/**
 * A factory of a service definition map.
 *
 * @since 0.1
 *
 * @param string $base_path Path to the plugin file.
 * @param string $base_url URL of the plugin folder.
 *
 * @return array A map of service names to service definitions.
 */
return function (
	$base_path,
	$base_url,
	$module_name,
	$parent_theme_path,
	$child_theme_path
) {
		return [
			'name'                                      => $module_name,
			'version'                                   => '0.1.0-alpha5',
			'base_path'                                 => $base_path,
			'base_dir'                                  => dirname( $base_path ),
			'base_url'                                  => $base_url,
			'js_path'                                   => '/assets/js',
			'templates_dir'                             => '/templates',
			'parent_theme_path'                         => $parent_theme_path,
			'child_theme_path'                          => $child_theme_path,
			'theme_template_dir'                        => '__modules',
			'translations_dir'                          => '/languages',
			'text_domain'                               => function ( DI_Container $c ) {
				return $c->get( 'name' );
			},

			'translation'                               => function ( DI_Container $c ) {
				$text_domain = $c->get( 'text_domain' );

				return function ( string $string, $placeholders = []) use ( $text_domain): string {
					return vsprintf( __( $string, $text_domain ), $placeholders );
				};
			},

			'template_path_factory'                     => function ( DI_Container $c ) {
				$baseDir      = rtrim( $c->get( 'base_dir' ), '\\/' );
				$templatesDir = trim( $c->get( 'templates_dir' ), '\\/' );

				return function ( $name ) use ( $baseDir, $templatesDir ) {
					$name = trim( $name, '\\/' );

					return "$baseDir/$templatesDir/$name";
				};
			},

			/*
			 * Makes templates.
			 *
			 * @since 0.1
			 */
			'template_factory'                          => function ( DI_Container $c ) {
				return function ( $path ) {
					return new PHP_Template( $path );
				};
			},

			'local_template_factory'                    => function ( DI_Container $c ) {
				$resolver = $c->get( 'template_path_resolver' );
				assert( $resolver instanceof File_Path_Resolver );

				$t = $c->get( 'translation' );
				assert( is_callable( $t ) );

				$f = $c->get( 'template_factory' );
				assert( is_callable( $f ) );

				return function ( $template) use ( $resolver, $f, $t) {
					$template = "{$template}.php";
					$path     = $resolver->resolve( $template );
					if ($path === null) {
						throw new UnexpectedValueException( $t( 'The path for template "%1$s" could not be resolved', [ $template ] ) );
					}

					return $f( $path );
				};
			},

			/*
			 * Makes blocs.
			 *
			 * @since 0.1
			 */
			'block_factory'                             => function ( DI_Container $c ) {
				return function ( PHP_Template $template, $context ) {
					return new Template_Block( $template, $context );
				};
			},

			/*
			 * Makes callback blocks.
			 *
			 * @since 0.1
			 */
			'callback_block_factory'                    => function ( DI_Container $c ) {
				return function ( callable $callback, $context = [] ) {
					return new Callback_Block( $callback, $context );
				};
			},

			/*
			 * List of handlers to run.
			 *
			 * @since 0.1
			 */
			'handlers'                                  => function ( DI_Container $c ) {
				return [
					$c->get( 'fields_types_handler' ),
					$c->get( 'quiz_shortcode_handler' ),
					$c->get( 'quiz_submission_handler' ),
				];
			},

			'question_groups_taxonomy'                  => function ( DI_Container $c ) {
				return 'question_groups';
			},

			'answer_post_type'                          => function ( DI_Container $c ) {
				return 'answer';
			},

			'question_post_type'                        => function ( DI_Container $c ) {
				return 'question';
			},

			'questions_to_answers_relationship_name'    => 'questions_to_answers',

			'course_post_type'                          => function ( DI_Container $c ) {
				return 'courses';
			},

			'course_groups_taxonomy'                    => function ( DI_Container $c ) {
				return 'course_groups';
			},

			'quiz_post_type'                            => 'quiz',
			'quiz_submission_post_type'                 => 'quiz_submission',

			'quizes_to_questions_relationship_name'     => 'quizes_to_questions',

			'answers_to_courses_relationship_name'      => 'answers_to_courses',

			'field_relationships'                       => function ( DI_Container $c ) {
				return [
					$c->get( 'questions_to_answers_relationship_name' ) => [
						'from' => [
							'object_type' => 'post',
							'post_type'   => $c->get( 'question_post_type' ),
							'meta_box'    => [
								'title' => __( 'Answers', 'squiz' ),
							],
						],
						'to'   => [
							'object_type' => 'post',
							'post_type'   => $c->get( 'answer_post_type' ),
							'meta_box'    => [
								'title' => __( 'Questions', 'squiz' ),
							],
						],
					],
					/*
					 * Courses
					 */
					$c->get( 'quizes_to_questions_relationship_name' ) => [
						'from' => [
							'object_type' => 'post',
							'post_type'   => $c->get( 'quiz_post_type' ),
							'meta_box'    => [
								'title'   => __( 'Questions', 'squiz' ),
								'context' => 'advanced',
							],
						],
						'to'   => [
							'object_type' => 'post',
							'post_type'   => $c->get( 'question_post_type' ),
							'meta_box'    => [
								'title' => __( 'Quizes', 'squiz' ),
							],
						],
					],
					$c->get( 'answers_to_courses_relationship_name' ) => [
						'from' => [
							'object_type' => 'post',
							'post_type'   => $c->get( 'answer_post_type' ),
							'meta_box'    => [
								'title'   => __( 'Courses', 'squiz' ),
								'context' => 'advanced',
							],
						],
						'to'   => [
							'object_type' => 'post',
							'post_type'   => $c->get( 'course_post_type' ),
							'meta_box'    => [
								'title' => __( 'Answers', 'squiz' ),
							],
						],
					],
				];
			},

			'fields_types_handler'                      => function ( DI_Container $c ) {
				return new Fields_Types_Handler( $c );
			},

			'quiz_shortcode_handler'                    => function ( DI_Container $c ) {
				return new Quiz_Shortcode_Handler(
					$c,
					$c->get( 'quiz_submission_document_creator_factory' ),
					$c->get( 'submission_request_var_name' ),
					$c->get( 'question_max_answers_field' )
				);
			},

			/*
			 * @see https://codex.wordpress.org/Function_Reference/register_post_type
			 */
			'post_types'                                => function ( DI_Container $c ) {
				return [
					$c->get( 'question_post_type' )        => [
						'labels'              => [
							'name'         => __( 'Questions', 'squiz' ),
							'add_new_item' => __( 'Add New Question', 'squiz' ),
						],
						'description'         => __( 'Questions for SQuiz plugin', 'squiz' ),
						'public'              => false,
						'show_ui'             => true,
						'show_in_menu'        => true,
						'exclude_from_search' => false,
						'menu_icon'           => 'dashicons-lightbulb',
						'capability_type'     => 'post',
						'supports'            => 'title',
						'has_archive'         => false,
						'rewrite'             => false,
					],
					$c->get( 'answer_post_type' )          => [
						'labels'              => [
							'name'         => __( 'Answers', 'squiz' ),
							'add_new_item' => __( 'Add New SQuiz', 'squiz' ),
						],
						'description'         => __( 'Answers for SQuiz plugin questions', 'squiz' ),
						'public'              => false,
						'show_ui'             => true,
						'exclude_from_search' => false,
						'show_in_menu'        => sprintf( 'edit.php?post_type=%1$s', $c->get( 'question_post_type' ) ),
						'capability_type'     => 'post',
						'supports'            => 'title',
						'has_archive'         => false,
						'rewrite'             => false,
					],
					/*
					 * Courses
					 */
					$c->get( 'course_post_type' )          => [
						'labels'              => [
							'name'         => __( 'Courses', 'squiz' ),
							'add_new_item' => __( 'Add New Course', 'squiz' ),
						],
						'description'         => __( 'Courses for SQuiz plugin', 'squiz' ),
						'public'              => false,
						'show_ui'             => true,
						'show_in_menu'        => true,
						'exclude_from_search' => false,
						'menu_icon'           => 'dashicons-welcome-learn-more',
						'capability_type'     => 'post',
						'supports'            => [ 'title', 'editor' ],
						'has_archive'         => false,
						'rewrite'             => false,
					],
					/*
					 * Quizes
					 */
					$c->get( 'quiz_post_type' )            => [
						'labels'          => [
							'name'         => __( 'Quizes', 'squiz' ),
							'add_new_item' => __( 'Add New Quiz', 'squiz' ),
						],
						'description'     => __( 'Quizes for SQuiz plugin', 'squiz' ),
						'public'          => false,
						'show_ui'         => true,
						'show_in_menu'    => true,
						'menu_icon'       => 'dashicons-format-aside',
						'capability_type' => 'post',
						'supports'        => [ 'title', 'editor' ],
						'has_archive'     => false,
						'rewrite'         => false,
					],
					$c->get( 'quiz_submission_post_type' ) => [
						'labels'              => [
							'name'         => __( 'Quiz Submissions', 'squiz' ),
							'add_new_item' => __( 'Add New Submissions', 'squiz' ),
						],
						'description'         => __( 'Quiz Submissions for SQuiz plugin', 'squiz' ),
						'public'              => false,
						'show_ui'             => true,
						'exclude_from_search' => false,
						'show_in_menu'        => sprintf( 'edit.php?post_type=%1$s', $c->get( 'quiz_post_type' ) ),
						'menu_icon'           => 'dashicons-format-aside',
						'capability_type'     => 'post',
						'capabilities'        => [
							'create_post' => 'do_not_allow',
						],
						'map_meta_cap'        => true,
						'supports'            => [
							'title',
							'custom-fields',
						],
						'has_archive'         => false,
						'rewrite'             => false,
					],
				];
			},

			'taxonomies'                                => function ( DI_Container $c ) {
				return [
					$c->get( 'question_groups_taxonomy' ) => [
						'object_type'  => [ $c->get( 'question_post_type' ) ],
						'labels'       => [
							'name' => __( 'Question Groups', 'squiz' ),
						],
						'description'  => __( 'Answer Groups for Taxonomy Quiz questions', 'squiz' ),
						'public'       => false,
						'show_ui'      => true,
						'show_in_menu' => sprintf( 'edit.php?post_type=%1$s', $c->get( 'answer_post_type' ) ),
						'rewrite'      => false,
						'hierarchical' => true,
					],
					/*
					 * Courses
					 */
					$c->get( 'course_groups_taxonomy' )   => [
						'object_type'  => [ $c->get( 'course_post_type' ) ],
						'labels'       => [
							'name' => __( 'Course Groups', 'squiz' ),
						],
						'description'  => __( 'Course Groups for Taxonomy Quiz questions', 'squiz' ),
						'public'       => false,
						'show_ui'      => true,
						'show_in_menu' => true,
						'rewrite'      => false,
						'hierarchical' => true,
					],
				];
			},
			'course_groups_max_courses_field'           => 'max_courses',
			'course_groups_description_field'           => 'long_description',
            'question_max_answers_field'                => 'max_answers',
            'question_type_field'                       => 'question_type',
			'taxonomy_metaboxes'                        => function ( DI_Container $c ) {
			    $t = $c->get('translation');
			    assert(is_callable($t));

				return [
					[
						'title'      => __( 'Quiz Submissions' ),
						'taxonomies' => $c->get( 'course_groups_taxonomy' ),
						'fields'     => [
							[
								'name'              => __( 'Max Courses' ),
								'id'                => $c->get( 'course_groups_max_courses_field' ),
								'label_description' => __( 'A non-negative integer. Set to 0 (zero) for unlimited.' ),
								'std'               => 0,
								'type'              => 'number',
							],
							[
								'name'              => __( 'Long Description' ),
								'id'                => $c->get( 'course_groups_description_field' ),
								'label_description' => __( 'The extended description of the course group.' ),
								'std'               => '',
								'type'              => 'wysiwyg',
							],
						],
					],
					[
						'title'      => __( 'Question Options' ),
						'post_types' => [ $c->get( 'question_post_type' ) ],
						'fields'     => [
                            [
                                'name'              => __( 'Max Answers' ),
                                'id'                => $c->get( 'question_max_answers_field' ),
                                'label_description' => __( 'A non-negative integer. Set to 0 (zero) for unlimited.' ),
                                'std'               => 0,
                                'type'              => 'number',
                            ],
                            [
                                'name'              => __( 'Question Type' ),
                                'id'                => $c->get( 'question_type_field' ),
                                'label_description' => __( 'What kind of answer are you expecting?' ),
                                'type'              => 'select',
                                'options'           => [
                                    'multiple_choice' => $t('Multiple Choice'),
                                    'text'            => $t('Text Field'),
                                ],
                                'std'               => 'text',
                                'plageholder'       => $t('Select type'),
                            ],
						],
					],
				];
			},
			'quiz_shortcode_name'                       => 'squiz',
			'submission_request_var_name'               => 'submission',
			'submission_answer_groups_var_name'         => 'squiz-answers',
			'submission_field_quiz_id'                  => 'squiz_quiz_id',
			'submission_field_grouped_answers'          => 'squiz_grouped_answers',
			'submission_document_default_template_name' => 'quiz-result',
			'quiz_default_template_name'                => 'quiz',

			'quiz_submission_handler'                   => function ( DI_Container $c ) {
				return new Quiz_Submission_Handler(
					$c,
					$c->get( 'submission_request_var_name' )
				);
			},

			'quiz_submission_document_creator_factory'  => function ( DI_Container $c) {
				return function ( PHP_Template $template) use ( $c) {
					return new Submission_Document_Creator(
						$c->get( 'submission_field_grouped_answers' ),
						$c->get( 'submission_field_quiz_id' ),
						$c->get( 'quiz_post_type' ),
						$c->get( 'quiz_submission_post_type' ),
						$c->get( 'course_groups_taxonomy' ),
						$c->get( 'answers_to_courses_relationship_name' ),
						$c->get( 'course_groups_max_courses_field' ),
						$c->get( 'course_post_type' ),
						$template
					);
				};
			},

			'template_path_resolver'                    => function ( DI_Container $c ) {
				return new File_Path_Resolver( $c->get( 'template_directories' ) );
			},

			'template_directories'                      => function ( DI_Container $c ) {
				$theme_template_dir    = trim( $c->get( 'theme_template_dir' ), '/' );
				$child_dir_name        = basename( $c->get( 'name' ) );
				$parent_theme_path     = rtrim( $c->get( 'parent_theme_path' ), '/' );
				$child_theme_path      = rtrim( $c->get( 'child_theme_path' ), '/' );
				$template_path_factory = $c->get( 'template_path_factory' );
				$local_template_path   = $template_path_factory( '' );
				$dirs                  = [ "{$child_theme_path}/{$theme_template_dir}/{$child_dir_name}" ];

				if ($parent_theme_path !== $child_theme_path) {
					$dirs[] = "{$parent_theme_path}/{$theme_template_dir}/{$child_dir_name}";
				}

				$dirs[] = $local_template_path;

				return $dirs;
			},
		];
};
