<?php
/**
 * Utilities to help handle arrays.
 * Based off Tribe\Utils\Arrays
 *
 * @since 3.0.0
 *
 * @package Pngx\Utils
 */

namespace Pngx\Utils;

/**
 * Class Array_Helpers
 *
 * @since 3.0.0
 *
 * @package Pngx\Utils
 */
class Array_Helpers {

	/**
	 * Set key/value within an array, can set a key nested inside of a multidimensional array.
	 *
	 * Example: set( $a, [ 0, 1, 2 ], 'hi' ) sets $a[0][1][2] = 'hi' and returns $a.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed        $array  The array containing the key this sets.
	 * @param string|array $key    To set a key nested multiple levels deep pass an array
	 *                             specifying each key in order as a value.
	 *                             Example: array( 'lvl1', 'lvl2', 'lvl3' );
	 * @param mixed         $value The value.
	 *
	 * @return array Full array with the key set to the specified value.
	 */
	public static function set( array $array, $key, $value ) {
		// Convert strings and such to array.
		$key = (array) $key;

		// Setup a pointer that we can point to the key specified.
		$key_pointer = &$array;

		// Iterate through every key, setting the pointer one level deeper each time.
		foreach ( $key as $i ) {

			// Ensure current array depth can have children set.
			if ( ! is_array( $key_pointer ) ) {
				// $key_pointer is set but is not an array. Converting it to an array
				// would likely lead to unexpected problems for whatever first set it.
				$error = sprintf(
					'Attempted to set $array[%1$s] but %2$s is already set and is not an array.',
					implode( $key, '][' ),
					$i
				);

				_doing_it_wrong( __FUNCTION__, esc_html( $error ), '4.3' );
				break;
			} elseif ( ! isset( $key_pointer[ $i ] ) ) {
				$key_pointer[ $i ] = array();
			}

			// Dive one level deeper into the nested array.
			$key_pointer = &$key_pointer[ $i ];
		}

		// Set the value for the specified key
		$key_pointer = $value;

		return $array;
	}

	/**
	 * Find a value inside of an array or object, including one nested a few levels deep.
	 *
	 * Example: get( $a, [ 0, 1, 2 ] ) returns the value of $a[0][1][2] or the default.
	 *
	 * @since 4.0.0
	 *
	 * @param  array        $variable Array or object to search within.
	 * @param  array|string $indexes  Specify each nested index in order.
	 *                                Example: array( 'lvl1', 'lvl2' );
	 * @param  mixed        $default  Default value if the search finds nothing.
	 *
	 * @return mixed The value of the specified index or the default if not found.
	 */
	public static function get( $variable, $indexes, $default = null ) {
		if ( is_object( $variable ) ) {
			$variable = (array) $variable;
		}

		if ( ! is_array( $variable ) ) {
			return $default;
		}

		foreach ( (array) $indexes as $index ) {
			if ( ! is_array( $variable ) || ! isset( $variable[ $index ] ) ) {
				$variable = $default;
				break;
			}

			$variable = $variable[ $index ];
		}

		return $variable;
	}

	/**
	 * Find a value inside a list of array or objects, including one nested a few levels deep.
	 *
	 * @since 4.0.0
	 *
	 * Example: get( [$a, $b, $c], [ 0, 1, 2 ] ) returns the value of $a[0][1][2] found in $a, $b or $c
	 * or the default.
	 *
	 * @param  array        $variables Array of arrays or objects to search within.
	 * @param  array|string $indexes   Specify each nested index in order.
	 *                                 Example: array( 'lvl1', 'lvl2' );
	 * @param  mixed        $default   Default value if the search finds nothing.
	 *
	 * @return mixed The value of the specified index or the default if not found.
	 */
	public static function get_in_any( array $variables, $indexes, $default = null ) {
		foreach ( $variables as $variable ) {
			$found = self::get( $variable, $indexes, '__not_found__' );
			if ( '__not_found__' !== $found ) {
				return $found;
			}
		}

		return $default;
	}

	/**
	 * Behaves exactly like the native strpos(), but accepts an array of needles.
	 *
	 * @since 4.0.0
	 *
	 * @see strpos()
	 *
	 * @param string       $haystack String to search in.
	 * @param array|string $needles  Strings to search for.
	 * @param int          $offset   Starting position of search.
	 *
	 * @return false|int Integer position of first needle occurrence.
	 */
	public static function strpos( $haystack, $needles, $offset = 0 ) {
		$needles = (array) $needles;

		foreach ( $needles as $i ) {
			$search = strpos( $haystack, $i, $offset );

			if ( false !== $search ) {
				return $search;
			}
		}

		return false;
	}

	/**
	 * Converts a list to an array filtering out empty string elements.
	 *
	 * @since 4.0.0
	 *
	 * @param     mixed   $value A string representing a list of values separated by the specified separator
	 *                           or an array. If the list is a string (e.g. a CSV list) then it will urldecoded
	 *                           before processing.
	 * @param string $sep The char(s) separating the list elements; will be ignored if the list is an array.
	 *
	 * @return array An array of list elements.
	 */
	public static function list_to_array( $value, $sep = ',' ) {
		// since we might receive URL encoded strings for CSV lists let's URL decode them first
		$value = is_array( $value ) ? $value : urldecode( $value );

		$sep = is_string( $sep ) ? $sep : ',';

		if ( $value === null || $value === '' ) {
			return array();
		}

		if ( ! is_array( $value ) ) {
			$value = preg_split( '/\\s*' . preg_quote( $sep ) . '\\s*/', $value );
		}

		$filtered = array();
		foreach ( $value as $v ) {
			if ( '' === $v ) {
				continue;
			}
			$filtered[] = is_numeric( $v ) ? $v + 0 : $v;
		}

		return $filtered;
	}

	/**
	 * Returns a list separated by the specified separator.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed  $list
	 * @param string $sep
	 *
	 * @return string The list separated by the specified separator or the original list if the list is empty.
	 */
	public static function to_list( $list, $sep = ',' ) {
		if ( empty( $list ) ) {
			return $list;
		}

		if ( is_array( $list ) ) {
			return implode( $sep, $list );
		}

		return $list;
	}

	/**
	 * Sanitize a multidimensional array.
	 *
	 * @since   4.0.0
	 *
	 * @param array $data The array to sanitize.
	 *
	 * @return array The sanitized array
	 *
	 * @link https://gist.github.com/esthezia/5804445
	 */
	public static function escape_multidimensional_array( $data = array() ) {

		if ( ! is_array( $data ) || ! count( $data ) ) {
			return array();
		}

		foreach ( $data as $key => $value ) {
			if ( ! is_array( $value ) && ! is_object( $value ) ) {
				$data[ $key ] = esc_attr( trim( $value ) );
			}
			if ( is_array( $value ) ) {
				$data[ $key ] = self::escape_multidimensional_array( $value );
			}
		}

		return $data;
	}

	/**
	 * Returns an array of values obtained by using the keys on the map; keys
	 * that do not have a match in map are discarded.
	 *
	 * To discriminate from not found results and legitimately `false`
	 * values from the map the `$found` parameter will be set by reference.
	 *
	 * @since 4.0.0
	 *
	 * @param      string|array $keys  One or more keys that should be used to get
	 *                                 the new values
	 * @param array             $map   An associative array relating the keys to the new
	 *                                 values.
	 * @param bool              $found When using a single key this argument will be
	 *                                 set to indicate whether the mapping was successful
	 *                                 or not.
	 *
	 * @return array|mixed|false An array of mapped values, a single mapped value when passing
	 *                           one key only or `false` if one key was passed but the key could
	 *                           not be mapped.
	 */
	public static function map_or_discard( $keys, array $map, &$found = true ) {
		$hash   = md5( time() );
		$mapped = array();

		foreach ( (array) $keys as $key ) {
			$meta_key = Pngx__Utils__Array::get( $map, $key, $hash );
			if ( $hash === $meta_key ) {
				continue;
			}
			$mapped[] = $meta_key;
		}

		$found = (bool) count( $mapped );

		if ( is_array( $keys ) ) {
			return $mapped;
		}

		return $found ? $mapped[0] : false;
	}

	/**
	 * Check if an array already has a value
	 *
	 * @param $items array a flat array of values
	 *
	 * @return bool
	 */
	public function array_has_value( $items ) {
		return count( $items ) > count( array_unique( $items ) );
	}
}
