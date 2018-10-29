<?php
/**
 * Get_Template_Capable_Trait trait.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

use Exception;

/**
 * Functionality for retrieving templates
 *
 * @package SQuiz
 */
trait Get_Template_Capable_Trait {


    /**
     * Gets the template for the specified key.
     *
     * @since [*next-version*]
     *
     * @param string $template The template key.
     *
     * @throws Exception If template could not be retrieved.
     *
     * @return PHP_Template The template for the key.
     */
    protected function get_template( $template ) {
        $path = $this->get_config('template_path_factory')("$template.php");

        return $this->get_config('template_factory')($path);
    }

    /**
     * Retrieves a config value.
     *
     * @since [*next-version*]
     *
     * @param string $key The key of the config value to retrieve.
     *
     * @throws Exception If problem retrieving.
     *
     * @return mixed The config value.
     */
    abstract public function get_config( $key );
}
