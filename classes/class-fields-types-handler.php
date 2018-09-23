<?php
/**
 * Fields_Types_Handler class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;


/**
 * Responsible for registering questions and answers related types and relationships between them.
 *
 * @since [*next-version*]
 *
 * @package SQuiz
 */
class Fields_Types_Handler extends Handler
{
    /* @since [*next-version*] */
    use Fields_Types_Register_Capable_Trait;

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
        add_action( 'mb_relationships_init', function () {
            $this->register_relationships($this->get_relationships());
        } );

        add_action(
            'init',
            function () {
                $this->register_post_types($this->get_post_types());
                $this->register_taxonomies($this->get_taxonomies());
            }
        );
    }

    /**
     * Returns the relationships to create.
     *
     * @since [*next-version*]
     *
     * @see https://docs.metabox.io/extensions/mb-relationships/
     *
     * @return array[] An array of MetaBox relationships, where key is the relationship ID, and value is other relationship configuration.
     */
    protected function get_relationships() {
        return (array) $this->get_config('field_relationships');
    }

    /**
     * Retrieves post type configurations.
     *
     * @since [*next-version*]
     *
     * @return array[] An array of post type configurations, where key is the post type name, and the value is post type arguments.
     */
    protected function get_post_types() {
        return (array) $this->get_config('post_types');
    }

    /**
     * Retrieves taxonomy configurations.
     *
     * @since [*next-version*]
     *
     * @return array[] An array of taxonomy configurations, where key is the taxonomy name, and the value is taxonomy arguments.
     */
    protected function get_taxonomies() {
        return (array) $this->get_config('taxonomies');
    }
}
