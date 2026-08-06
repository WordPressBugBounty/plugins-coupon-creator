<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Coupon Type Template Loader
 *
 * Resolves `src/views/type/<type>.php` templates for free core, with theme
 * override support at `[your-theme]/cctor-coupons/type/<type>.php`.
 *
 * Free core historically rendered coupons through a hardcoded chain of actions in
 * Cctor__Coupon__Shortcode. That path is untouched and still serves the `default`
 * and `image` types. This loader serves every *other* type, so a new design is
 * written once as a view file instead of twice (once for free, once for Pro).
 *
 * Deliberately does NOT declare `cctor_get_template_part()` — Coupon Creator Pro
 * declares that function unguarded, and free's libraries load first
 * (`plugins_loaded` priority 0, before Pro at priority 1). Declaring it here would
 * fatal any site running a newer free alongside an older Pro.
 *
 * @since 3.6.0
 */
class Cctor__Coupon__Templates {

	/**
	 * Hook into the shared template path filter.
	 *
	 * Registered unconditionally so that Coupon Creator Pro's own loader can also
	 * resolve views that ship in free core. Pro's own path stays first in the
	 * array, so a template present in both plugins still resolves to Pro's copy.
	 */
	public function hook() {
		add_filter( 'cctor_coupon_template_paths', array( $this, 'add_template_path' ) );
	}

	/**
	 * Append free core's plugin path to the template search paths.
	 *
	 * @param array $paths Template base paths.
	 *
	 * @return array
	 */
	public function add_template_path( $paths ) {

		$paths = (array) $paths;

		$paths[] = pngx( 'cctor' )->plugin_path;

		return $paths;
	}

	/**
	 * Locate a template file, preferring a theme override.
	 *
	 * @param string $template Template name relative to `src/views/`, e.g. `type/ticket`.
	 *
	 * @return string|false Absolute path to the template, or false when not found.
	 */
	public static function get_template_hierarchy( $template ) {

		if ( '.php' !== substr( $template, - 4 ) ) {
			$template .= '.php';
		}

		$file = false;

		// Theme override: [your-theme]/cctor-coupons/type/<type>.php
		if ( locate_template( array( 'cctor-coupons/' ) ) ) {
			$file = locate_template( array( 'cctor-coupons/' . $template ), false, false );
		}

		if ( ! $file ) {

			/**
			 * Filter base paths searched for coupon templates.
			 *
			 * Shared with Coupon Creator Pro, which registers the same filter.
			 *
			 * @param array $paths An array of plugin base paths.
			 */
			$template_base_paths = apply_filters( 'cctor_coupon_template_paths', (array) pngx( 'cctor' )->plugin_path );

			foreach ( $template_base_paths as $template_base_path ) {

				$template_base_path = ! empty( $template_base_path ) ? trailingslashit( $template_base_path ) : $template_base_path;

				$candidate = $template_base_path . 'src/views/' . $template;

				/**
				 * Filter the resolved coupon template path.
				 *
				 * @param string $candidate The full path to the template.
				 * @param string $template  The template name.
				 */
				$candidate = apply_filters( 'cctor_coupon_template', $candidate, $template );

				// Return the first one found.
				if ( file_exists( $candidate ) ) {
					$file = $candidate;
					break;
				}
			}
		}

		/**
		 * Filter the resolved path for one specific template.
		 *
		 * @param string|false $file The full path to the template, or false.
		 */
		return apply_filters( 'cctor_coupon_template_' . $template, $file );
	}

	/**
	 * Whether a coupon type has a view file backing it.
	 *
	 * @param string $type Coupon type slug.
	 *
	 * @return bool
	 */
	public static function type_has_view( $type ) {

		if ( empty( $type ) ) {
			return false;
		}

		$file = self::get_template_hierarchy( 'type/' . $type );

		return ! empty( $file ) && file_exists( $file );
	}
}
