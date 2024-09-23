<?php
/**
 * HTML functions (template-tags) for use in WordPress templates.
 */
use Pngx\Utils\Element_Classes;
use Pngx\Utils\Element_Attributes;

/**
 * Parse input values into a valid array of classes to be used in the templates.
 *
 * @since 3.2.0
 *
 * @param  mixed $classes,... unlimited Any amount of params to be rendered as classes.
 *
 * @return array
 */
function pngx_get_classes() {
	$element_classes = new Element_Classes( func_get_args() );
	return $element_classes->get_classes();
}

/**
 * Parse input values into a valid array of attributes to be used in the templates.
 *
 * @since  3.2.0
 *
 * @param  mixed $attributes,... unlimited Any amount of params to be rendered as attributes.
 *
 * @return array<string> An array of the parsed string attributes.
 */
function pngx_get_attributes() {
	$element_attributes = new Element_Attributes( func_get_args() );
	return $element_attributes->get_attributes_array();
}

/**
 * Parse input values into a valid html attributes to be used in the templates.
 *
 * @since  3.2.0
 *
 * @param  mixed $attributes,... unlimited Any amount of params to be rendered as attributes.
 *
 * @return void
 */
function pngx_attributes() {
	$element_attributes = new Element_Attributes( func_get_args() );
	echo $element_attributes->get_attributes();
}

/**
 * Parses input values into a valid class html attribute to be used in the templates.
 *
 * @since 3.2.0
 *
 * @param  mixed $classes,... unlimited Any amount of params to be rendered as classes.
 *
 * @return string
 */
function pngx_classes() {
	$element_classes = new Element_Classes( func_get_args() );
	echo $element_classes->get_attribute();
}

/**
 * Get attributes for required fields.
 *
 * @since 3.2.0
 *
 * @param boolean $required If the field is required.
 * @param boolean $echo     Whether to echo the string or return it.
 *
 * @return string|void If echo is false, returns $required_string.
 */
function pngx_required( $required, $echo = true ) {
	if ( $required ) {
		$required_string = 'required aria-required="true"';

		if ( ! $echo ) {
			return $required_string;
		} else {
			echo $required_string;
		}
	}
}

/**
 * Get string for required field labels.
 *
 * @since 3.2.0
 *
 * @param boolean $required If the field is required.
 * @param boolean $echo     Whether to echo the string or return it.
 *
 * @return string|void If echo is false, returns $required_string.
 */
function pngx_required_label( $required, $echo = true ) {
	if ( $required ) {
		$required_string = '<span class="screen-reader-text">'
			. esc_html_x( '(required)', 'The associated field is required.', 'pngx-engine' )
			. '</span><span class="pngx-required" aria-hidden=”true” role=”presentation”>*</span>';

		if ( ! $echo ) {
			return $required_string;
		} else {
			echo $required_string;
		}
	}
}

/**
 * Get attributes for disabled fields.
 *
 * @since 3.2.0
 *
 * @param boolean $disabled If the field is disabled.
 * @param boolean $echo     Whether to echo the string or return it.
 *
 * @return string|void If echo is false, returns $disabled_string.
 */
function pngx_disabled( $disabled, $echo = true ) {
	if ( $disabled ) {
		$disabled_string = 'disabled aria-disabled="true"';

		if ( ! $echo ) {
			return $disabled_string;
		} else {
			echo $disabled_string;
		}
	}
}
