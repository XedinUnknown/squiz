<?php
/**
 * Index_List_Capable_Trait class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

trait Index_List_Capable_Trait {

    /**
     * Indexes a list of entities.
     *
     * @since [*next-version*]
     *
     * @param iterable $entities A list of entities to index.
     * @param callable $valueRetriever The callback that will retrieve the value for each index.
     * @param callable $indexRetriever The callback that will retrieve the index for each post.
     *
     * @return array The map of index to entity.
     */
    protected function index_list($entities, callable $valueRetriever, callable $indexRetriever):array {

        $map = [];

        foreach ($entities as $_idx => $entity) {
            $index = call_user_func_array($indexRetriever, [$entity]);
            $value = call_user_func_array($valueRetriever, [$entity]);
            $map[$index] = $value;
        }

        return $map;
    }
}
