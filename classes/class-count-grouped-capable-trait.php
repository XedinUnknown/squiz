<?php
/**
 * Fields_Types_Register_Capable_Trait class.
 *
 * @package SQuiz
 */

namespace XedinUnknown\SQuiz;

trait Count_Grouped_Capable_Trait {
	/**
	 * Counts the times an item appears in all given groups.
	 *
	 * Can only count scalar values.
	 *
	 * @since [*next-version*]
	 *
	 * @param array<int, array<int, int|string|float|bool>> $groups A map of group IDs to lists of item IDs.
	 *
	 * @return int[] A map of item to its count.
	 */
	protected function count_grouped( $groups ) {
		$counts = [];

		foreach ( $groups as $group ) {
		    if (!is_array($group)) {
		        continue;
            }

			foreach ( $group as $item ) {
				$counts[ $item ] = isset( $counts[ $item ] )
					? $counts[ $item ] + 1
					: 1;
			}
		}

		return $counts;
	}
}
