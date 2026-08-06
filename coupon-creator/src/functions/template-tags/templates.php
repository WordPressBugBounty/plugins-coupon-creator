<?php
/**
 * Coupon Creator Template Tags
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Cctor__Coupon__Main' ) ) {
	return;
}

if ( ! function_exists( 'cctor_core_get_template_part' ) ) {
	/**
	 * Include a coupon view file, injecting variables into its scope.
	 *
	 * Free core's equivalent of Pro's `cctor_get_template_part()`. Deliberately
	 * named differently: Pro declares its version unguarded, and free's libraries
	 * load first, so sharing the name would fatal a new-free / old-Pro pairing.
	 *
	 * @since 3.6.0
	 *
	 * @param string     $slug Template slug relative to `src/views/`, e.g. `type/ticket`.
	 * @param null|string $name Optional specialised template name.
	 * @param array|null $data Optional variables to make available to the template.
	 *
	 * @return bool Whether a template was found and included.
	 */
	function cctor_core_get_template_part( $slug, $name = null, ?array $data = null ) {

		/**
		 * Fires before a coupon template part is loaded.
		 *
		 * @param string      $slug
		 * @param null|string $name
		 * @param array|null  $data
		 */
		do_action( 'cctor_core_pre_get_template_part_' . $slug, $slug, $name, $data );

		$templates = array();
		if ( isset( $name ) ) {
			$templates[] = $slug . '-' . $name . '.php';
		}
		$templates[] = $slug . '.php';

		/**
		 * Filter the candidate template files for this part.
		 *
		 * @param array       $templates
		 * @param string      $slug
		 * @param null|string $name
		 */
		$templates = apply_filters( 'cctor_core_get_template_part_templates', $templates, $slug, $name );

		// Make provided variables available in the template's symbol table.
		if ( is_array( $data ) ) {
			extract( $data ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}

		foreach ( $templates as $template ) {

			$file = Cctor__Coupon__Templates::get_template_hierarchy( $template );

			/**
			 * Filter the resolved file path for a template part.
			 *
			 * @param string|false $file
			 * @param string       $template
			 * @param string       $slug
			 * @param null|string  $name
			 */
			$file = apply_filters( 'cctor_core_get_template_part_path', $file, $template, $slug, $name );

			if ( ! empty( $file ) && file_exists( $file ) ) {

				/**
				 * Fires before the template part is included.
				 *
				 * @param string $template
				 * @param string $file
				 */
				do_action( 'cctor_core_before_get_template_part', $template, $file, $slug, $name );

				include $file;

				/**
				 * Fires after the template part is included.
				 *
				 * @param string $template
				 * @param string $file
				 */
				do_action( 'cctor_core_after_get_template_part', $template, $file, $slug, $name );

				return true;
			}
		}

		return false;
	}
}
