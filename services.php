<?php
/**
 * Contains service definitions used by the plugin.
 *
 * @package SQuiz
 */

use XedinUnknown\SQuiz\DI_Container;
use XedinUnknown\SQuiz\Fields_Types_Handler;
use XedinUnknown\SQuiz\PHP_Template;
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
return function ( $base_path, $base_url ) {
		return [
			'version'                         => '[*next-version*]',
			'base_path'                       => $base_path,
			'base_dir'                        => dirname( $base_path ),
			'base_url'                        => $base_url,
			'js_path'                         => '/assets/js',
			'templates_dir'                   => '/templates',
			'translations_dir'                => '/languages',
			'text_domain'                     => 'squiz',

			/*
			 * Makes templates.
			 *
			 * @since 0.1
			 */
			'template_factory'                => function ( DI_Container $c ) {
				return function ( $path ) {
					return new PHP_Template( $path );
				};
			},

			/*
			 * Makes blocs.
			 *
			 * @since 0.1
			 */
			'block_factory'                   => function ( DI_Container $c ) {
				return function ( PHP_Template $template, $context ) {
					return new Template_Block( $template, $context );
				};
			},

			/*
			 * List of handlers to run.
			 *
			 * @since 0.1
			 */
			'handlers'                        => function ( DI_Container $c ) {
				return [
				    $c->get('fields_types_handler')
				];
			},

            'question_groups_taxonomy'         => function ( DI_Container $c ) {
                return 'question_groups';
            },

            'answer_post_type'                => function ( DI_Container $c ) {
                return 'answer';
            },

            'question_post_type'                => function ( DI_Container $c ) {
                return 'question';
            },

            'questions_to_answers_relationship_name' => 'questions_to_answers',

            'course_post_type'             => function ( DI_Container $c ) {
                return 'courses';
            },

            'course_groups_taxonomy'             => function ( DI_Container $c ) {
                return 'course_groups';
            },

            'quiz_post_type'                => 'quiz',

            'quizes_to_questions_relationship_name' => 'quizes_to_questions',

            'field_relationships'             => function (DI_Container $c) {
			    return [
                    $c->get('questions_to_answers_relationship_name') => [
                        'from' => [
                            'object_type' => 'post',
                            'post_type' => $c->get('question_post_type'),
                            'meta_box' => [
                                'title' => __('Answers', 'squiz'),
                            ],
                        ],
                        'to' => [
                            'object_type' => 'post',
                            'post_type' => $c->get('answer_post_type'),
                            'meta_box' => [
                                'title' => __('Questions', 'squiz'),
                            ],
                        ],
                    ],
                    /*
                     * Courses
                     */
                    $c->get('quizes_to_questions_relationship_name') => [
                        'from' => [
                            'object_type' => 'post',
                            'post_type' => $c->get('quiz_post_type'),
                            'meta_box' => [
                                'title' => __('Questions', 'squiz'),
                                'context' => 'advanced',
                            ],
                        ],
                        'to' => [
                            'object_type' => 'post',
                            'post_type' => $c->get('question_post_type'),
                            'meta_box' => [
                                'title' => __('Quizes', 'squiz'),
                            ],
                        ],
                    ],
                ];
            },

            'fields_types_handler'            => function ( DI_Container $c ) {
			    return new Fields_Types_Handler( $c );
            },

            /*
             * @see https://codex.wordpress.org/Function_Reference/register_post_type
             */
            'post_types'                           => function ( DI_Container $c ) {
                return [
                    $c->get('question_post_type') => [
                        'labels' => [
                            'name'          => __('Questions', 'squiz'),
                            'add_new_item' => __('Add New Question', 'squiz'),
                        ],
                        'description'   => __('Questions for SQuiz plugin', 'squiz'),
                        'public'        => false,
                        'show_ui'       => true,
                        'show_in_menu'  => true,
                        "menu_icon"     => 'dashicons-lightbulb',
                        'capability_type' => 'post',
                        'supports'      => 'title',
                        'has_archive'   => false,
                        'rewrite'       => false,
                    ],
                    $c->get('answer_post_type') => [
                        'labels' => [
                            'name'          => __('Answers', 'squiz'),
                            'add_new_item' => __('Add New SQuiz', 'squiz'),
                        ],
                        'description'   => __('Answers for SQuiz plugin questions', 'squiz'),
                        'public'        => false,
                        'show_ui'       => true,
                        'show_in_menu'  => sprintf('edit.php?post_type=%1$s', $c->get('question_post_type')),
                        'capability_type' => 'post',
                        'supports'      => 'title',
                        'has_archive'   => false,
                        'rewrite'       => false,
                    ],
                    /*
                     * Courses
                     */
                    $c->get('course_post_type') => [
                        'labels' => [
                            'name'          => __('Courses', 'squiz'),
                            'add_new_item' => __('Add New Course', 'squiz'),
                        ],
                        'description'   => __('Courses for SQuiz plugin', 'squiz'),
                        'public'        => false,
                        'show_ui'       => true,
                        'show_in_menu'  => true,
                        "menu_icon"     => 'dashicons-welcome-learn-more',
                        'capability_type' => 'post',
                        'supports'      => 'title',
                        'has_archive'   => false,
                        'rewrite'       => false,
                    ],
                    /*
                     * Quizes
                     */
                    $c->get('quiz_post_type') => [
                        'labels' => [
                            'name'          => __('Quizes', 'squiz'),
                            'add_new_item' => __('Add New Quiz', 'squiz'),
                        ],
                        'description'   => __('Quizes for SQuiz plugin', 'squiz'),
                        'public'        => false,
                        'show_ui'       => true,
                        'show_in_menu'  => true,
                        "menu_icon"     => 'dashicons-format-aside',
                        'capability_type' => 'post',
                        'supports'      => 'title',
                        'has_archive'   => false,
                        'rewrite'       => false,
                    ],
                ];
            },

            'taxonomies'                          => function ( DI_Container $c ) {
                return [
                    $c->get('question_groups_taxonomy') => [
                        'object_type' => [$c->get('question_post_type')],
                        'labels' => [
                            'name'=> __('Question Groups', 'squiz'),
                        ],
                        'description' => __('Answer Groups for Taxonomy Quiz questions', 'squiz'),
                        'public' => false,
                        'show_ui' => true,
                        'show_in_menu'  => sprintf('edit.php?post_type=%1$s', $c->get('answer_post_type')),
                        'rewrite' => false,
                        'hierarchical' => true,
                    ],
                    /*
                     * Courses
                     */
                    $c->get('course_groups_taxonomy') => [
                        'object_type' => [$c->get('course_post_type')],
                        'labels' => [
                            'name'=> __('Course Groups', 'squiz'),
                        ],
                        'description' => __('Course Groups for Taxonomy Quiz questions', 'squiz'),
                        'public' => false,
                        'show_ui' => true,
                        'show_in_menu'  => true,
                        'rewrite' => false,
                        'hierarchical' => true,
                    ],
                ];
            },
		];
};
