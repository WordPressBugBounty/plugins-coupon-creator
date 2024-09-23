<?php
/**
 * Provides methods to format items for a dropdown.
 *
 * @since   4.0.0
 *
 * @package Pngx\Traits;
 */

namespace Pngx\Traits;

use Pngx__Utils__Array as Arr;

/**
 * Trait Dropdowns
 *
 * @since   4.0.0
 *
 * @package Pngx\Traits;
 */
trait Dropdowns {

	/**
	 * Get passed items formatted for a dropdown.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string|mixed> $items    An array of items to format for a dropdown.
	 * @param string              $selected The selected item id.
	 *
	 * @return array<string> The array of items formatted for a dropdown.
	 */
	public function get_items_formatted_for_dropdown( $items, $selected ) {
		if ( empty( $items ) ) {
			return [];
		}

		$formatted_items = [];
		foreach ( $items as $key => $item ) {
			$name = Arr::get( $item, 'name', '' );
			$id   = Arr::get( $item, 'id', '' );

			if ( empty( $name ) || empty( $key ) ) {
				continue;
			}

			$formatted_items[] = [
				'text'     => (string) $name,
				'id'       => (string) $id,
				'sort'     => (string) trim( $name ),
				'selected' => $id === $selected ? true : false,
			];
		}

		// Sort the users array by name.
		$sort_arr = array_column( $formatted_items, 'sort' );
		array_multisort( $sort_arr, SORT_ASC, $formatted_items );

		return $formatted_items;
	}
}
