<?php

Use Pngx\Utilities\Arr;
/**
 * Class used to register and enqueue assets across our plugins
 *
 * Based off Modern Tribe's Tribe__Assets
 *
 * @since 3.0
 */
class Pngx__Assets {
	/**
	 * Stores all the Assets and it's configurations
	 *
	 * @var array
	 */
	protected $assets = [];

	/**
	 * Stores the localized scripts for reference
	 *
	 * @var array
	 */
	private $localized = [];

	/**
	 * Static Singleton Factory Method
	 *
	 * @since 3.0
	 *
	 * @return self
	 */
	public static function instance() {
		return pngx( 'pngx.assets' );
	}

	/**
	 * Register the Methods in the correct places
	 *
	 * @since 3.0
	 */
	public function __construct() {
		// Hook the actual registering of
		add_action( 'init', array( $this, 'register_in_wp' ), 1, 0 );
	}

	/**
	 * Register the Assets on the correct hooks
	 *
	 * @since 3.0
	 *
	 * @return void
	 */
	public function register_in_wp( $assets = null ) {
		if ( is_null( $assets ) ) {
			$assets = $this->assets;
		}

		if ( ! is_array( $assets ) ) {
			$assets = array( $assets );
		}

		uasort( $assets, array( $this, 'order_by_priority' ) );

		foreach ( $assets as $asset ) {
			if ( 'js' === $asset->type ) {
				wp_register_script( $asset->slug, $asset->url, $asset->deps, $asset->version, $asset->in_footer );
			} else {
				wp_register_style( $asset->slug, $asset->url, $asset->deps, $asset->version, $asset->media );
			}

			// Register that this asset is actually registered on the WP methods
			$asset->is_registered = true;

			// If we don't have an action we don't even register the action to enqueue
			if ( empty( $asset->action ) ) {
				continue;
			}

			// Now add an action to enqueue the registered assets
			foreach ( (array) $asset->action as $action ) {
				// Enqueue the registered assets at the appropriate time
				if ( did_action( $action ) > 0 ) {
					$this->enqueue();
				} else {
					add_action( $action, array( $this, 'enqueue' ), $asset->priority );
				}
			}
		}
	}

	/**
	 * Enqueues registered assets based on their groups.
	 *
	 * @since   2.6
	 *
	 * @uses    $this->enqueue
	 *
	 * @param   string|array $groups Which groups will be enqueued
	 *
	 * @return  void
	 */
	public function enqueue_group( $groups ) {
		$assets = $this->get();
		$enqueue = array();

		foreach ( $assets as $asset ) {
			if ( empty( $asset->groups ) ) {
				continue;
			}

			$instersect = array_intersect( (array) $groups, $asset->groups );

			if ( empty( $instersect ) ) {
				continue;
			}

			$enqueue[] = $asset->slug;
		}

		$this->enqueue( $enqueue );
	}

	/**
	 * Enqueues registered assets.
	 *
	 * This method is called on whichever action (if any) was declared during registration.
	 *
	 * It can also be called directly with a list of asset slugs to forcibly enqueue, which may be
	 * useful where an asset is required in a situation not anticipated when it was originally
	 * registered.
	 *
	 * @since 3.0
	 *
	 * @param string|array $forcibly_enqueue
	 */
	public function enqueue( $forcibly_enqueue = null ) {
		$forcibly_enqueue = array_filter( (array) $forcibly_enqueue );
		$assets = $this->get();

		foreach ( $assets as $asset ) {
			// Should this asset be enqueued regardless of the current filter/any conditional requirements?
			$must_enqueue = in_array( $asset->slug, $forcibly_enqueue );
			$in_filter    = in_array( current_filter(), (array) $asset->action );

			// Skip if we are not on the correct filter (unless we are forcibly enqueuing)
			if ( ! $in_filter && ! $must_enqueue ) {
				continue;
			}

			// If any single conditional returns true, then we need to enqueue the asset
			if ( empty( $asset->action ) && ! $must_enqueue ) {
				continue;
			}

			// If this asset was late called
			if ( ! $asset->is_registered ) {
				$this->register_in_wp( $asset );
			}

			// Default to enqueuing the asset if there are no conditionals,
			// and default to not enqueuing it if there *are* conditionals
			$enqueue = empty( $asset->conditionals );

			if ( ! $enqueue ) {
				// Reset Enqeue
				$enqueue = array();

				// Which is the operator?
				$conditional_operator = Arr::get( $asset->conditionals, 'operator', 'OR' );

				// If we have a set of conditionals we loop on then and get if they are true
				foreach ( $asset->conditionals as $key => $conditional ) {
					// Avoid doing anything to the operator
					if ( 'operator' === $key ) {
						continue;
					}

					$enqueue[] = call_user_func( $conditional );
				}

				// By default we use OR for backwards compatibility
				if ( 'OR' === $conditional_operator ) {
					$enqueue = in_array( true, $enqueue );
				} else {
					$enqueue = ! in_array( false, $enqueue );
				}
			}

			/**
			 * Allows developers to hook-in and prevent an asset from been loaded
			 *
			 * @since 3.0
			 *
			 * @param bool   $enqueue If we should enqueue or not a given asset
			 * @param object $asset   Which asset we are dealing with
			 */
			$enqueue = apply_filters( 'pngx_asset_enqueue', $enqueue, $asset );

			/**
			 * Allows developers to hook-in and prevent an asset from been loaded
			 *
			 * @since 3.0
			 *
			 * @param bool   $enqueue If we should enqueue or not a given asset
			 * @param object $asset   Which asset we are dealing with
			 */
			$enqueue = apply_filters( 'pngx_asset_enqueue_' . $asset->slug, $enqueue, $asset );

			if ( ! $enqueue && ! $must_enqueue ) {
				continue;
			}

			if ( 'js' === $asset->type ) {
				wp_enqueue_script( $asset->slug );

				// Only localize on JS and if we have data
				if ( ! empty( $asset->localize ) ) {
					// Makes sure we have an Array of Localize data
					if ( is_object( $asset->localize ) ) {
						$localization = array( $asset->localize );
					} else {
						$localization = (array) $asset->localize;
					}

					/**
					 * Check to ensure we haven't already localized it before
					 * @since 4.5.8
					 */
					foreach ( $localization as $localize ) {
						if ( in_array( $localize->name, $this->localized ) ) {
							continue;
						}

						// If we have a Callable as the Localize data we execute it
						if ( is_callable( $localize->data ) ) {
							$localize->data = call_user_func_array( $localize->data, array( $asset ) );
						}

						wp_localize_script( $asset->slug, $localize->name, $localize->data );
						$this->localized[] = $localize->name;
					}
				}
			} else {
				wp_enqueue_style( $asset->slug );
			}

			$asset->already_enqueued = true;
		}
	}

	/**
	 * Returns the path to a minified version of a js or css file, if it exists.
	 * If the file does not exist, returns false.
	 *
	 * @since 3.0
	 *
	 * @param string $url The absolute URL to the un-minified file.
	 *
	 * @return string|false The url to the minified version or false, if file not found.
	 */
	public static function maybe_get_min_file( $url ) {
		$urls = array();
		$wpmu_plugin_url = set_url_scheme( WPMU_PLUGIN_URL );
		$wp_plugin_url = set_url_scheme( WP_PLUGIN_URL );
		$wp_content_url = set_url_scheme( WP_CONTENT_URL );
		$plugins_url = plugins_url();

		if ( 0 === strpos( $url, $wpmu_plugin_url ) ) {
			// URL inside WPMU plugin dir.
			$base_dir = wp_normalize_path( WPMU_PLUGIN_DIR );
			$base_url = $wpmu_plugin_url;
		} elseif ( 0 === strpos( $url, $wp_plugin_url ) ) {
			// URL inside WP plugin dir.
			$base_dir = wp_normalize_path( WP_PLUGIN_DIR );
			$base_url = $wp_plugin_url;
		} elseif ( 0 === strpos( $url, $wp_content_url ) ) {
			// URL inside WP content dir.
			$base_dir = wp_normalize_path( WP_CONTENT_DIR );
			$base_url = $wp_content_url;
		} elseif ( 0 === strpos( $url, $plugins_url ) ) {
			$base_dir = wp_normalize_path( WP_PLUGIN_DIR );
			$base_url = $plugins_url;
		} else {
			// Resource needs to be inside wp-content or a plugins dir.
			return false;
		}

		// Strip the plugin URL and make this relative.
		$relative_location = str_replace( $base_url, '', $url );

		// If needed add the Min Files.
		if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG === false ) {
			if ( substr( $relative_location, - 3, 3 ) === '.js' ) {
				$urls[] = substr_replace( $relative_location, '.min', - 3, 0 );
			}

			if ( substr( $relative_location, - 4, 4 ) === '.css' ) {
				$urls[] = substr_replace( $relative_location, '.min', - 4, 0 );
			}
		}

		// Add the actual url after having the min file added.
		$urls[] = $relative_location;

		// Check for all Urls added to the array.
		foreach ( $urls as $partial_path ) {
			$file_path = wp_normalize_path( $base_dir . $partial_path );
			$file_url  = plugins_url( basename( $file_path ), $file_path );

			if ( file_exists( $file_path ) ) {
				return $file_url;
			}
		}

		// If we don't have any real file return false
		return false;
	}

	/**
	 * Register an Asset and attach a callback to the required action to display it correctly
	 *
	 * @since 3.0
	 *
	 * @param  object       $origin    The main Object for the plugin you are enqueueing the script/style for
	 * @param  string       $slug      Slug to save the asset
	 * @param  string       $file      Which file will be loaded, either CSS or JS
	 * @param  array        $deps      Dependencies
	 * @param  string|null  $action    (Optional) A WordPress Action, if set needs to happen after: `wp_enqueue_scripts`, `admin_enqueue_scripts`, or `login_enqueue_scripts`
	 * @param  string|array $arguments {
	 *     Optional. Array or string of parameters for this asset
	 *
	 *     @type string|null  $action         Which WordPress action this asset will be loaded on
	 *     @type int          $priority       Priority in which this asset will be loaded on the WordPress action
	 *     @type string       $file           The relative path to the File that will be enqueued, uses the $origin to get the full path
	 *     @type string       $type           Asset Type, `js` or `css`
	 *     @type array        $deps           An array of other asset as dependencies
	 *     @type string       $version        Version number, used for cache expiring
	 *     @type string       $media          Used only for CSS, when to load the file
	 *     @type bool         $in_footer      A boolean determining if the javascript should be loaded on the footer
	 *     @type array|object $localize       Variables needed on the JavaScript side {
	 *          @type string 		$name     Name of the JS variable
	 *          @type string|array  $data     Contents of the JS variable
	 *     }
	 *     @type callable[]   $conditionals   An callable method or an array of them, that will determine if the asset is loaded or not
	 * }
	 *
	 * @return string
	 */
	public function register( $origin, $slug, $file, $deps = array(), $action = null, $arguments = array() ) {
		// Prevent weird stuff here
		$slug = sanitize_title_with_dashes( $slug );

		if ( $this->exists( $slug ) ) {
			return $this->get( $slug );
		}

		if ( is_string( $origin ) ) {
			// Origin needs to be a class with a `instance` method and a Version constant
			if ( class_exists( $origin ) && method_exists( $origin, 'instance' ) && defined( $origin . '::VERSION' ) ) {
				$origin = call_user_func( array( $origin, 'instance' ) );
			}
		}

		if ( is_object( $origin ) ) {
			$origin_name = get_class( $origin );

			if ( ! defined( $origin_name . '::VERSION' ) ) {
				// If we have a Object and we don't have instance or version
				return false;
			}
		} else {
			return false;
		}

		// Fetches the version on the Origin Version constant
		$version = constant( $origin_name . '::VERSION' );

		// Default variables to prevent notices
		$defaults = array(
			'slug'          => null,
			'file'          => false,
			'url'           => false,
			'action'        => null,
			'priority'      => 10,
			'type'          => null,
			'deps'          => array(),
			'groups'        => array(),
			'version'       => $version,
			'media'         => 'all',
			'in_footer'     => true,
			'is_registered' => false,
			'origin_path'   => null,
			'origin_url'    => null,
			'origin_name'   => null,

			// Bigger Variables at the end
			'localize'      => array(),
			'conditionals'  => array(),
		);

		// Merge Arguments
		$asset = (object) wp_parse_args( $arguments, $defaults );

		// Enforce these one
		$asset->slug        = $slug;
		$asset->file        = $file;
		$asset->deps        = $deps;
		$asset->action      = $action;
		$asset->origin_path = trailingslashit( $origin->plugin_path );
		$asset->origin_name = $origin_name;

		// Origin URL might throw notices so we double check
		$asset->origin_url  = ! empty( $origin->plugin_url ) ? $origin->plugin_url : null;
		if ( ! empty( $asset->origin_url ) ) {
			$asset->origin_url = trailingslashit( $asset->origin_url );
		}

		// If we don't have a type on the arguments we grab from the File path
		if ( is_null( $asset->type ) ) {
			if ( substr( $asset->file, -3, 3 ) === '.js' ) {
				$asset->type = 'js';
			} elseif ( substr( $asset->file, -4, 4 ) === '.css' ) {
				$asset->type = 'css';
			}
		}

		// If asset type is wrong don't register
		if ( ! in_array( $asset->type, array( 'js', 'css' ) ) ) {
			return false;
		}

		/**
		 * Filter to change version number on assets
		 *
		 * @since  2.6
		 *
		 * @param string $version
		 * @param object $asset
		 */
		$asset->version = apply_filters( 'pngx_asset_version', $asset->version, $asset );

		// Clean these
		$asset->priority  = absint( $asset->priority );
		$asset->in_footer = (bool) $asset->in_footer;
		$asset->media     = esc_attr( $asset->media );

		// Ensures that we have a priority over 1
		if ( $asset->priority < 1 ) {
			$asset->priority = 1;
		}

		$is_vendor = strpos( $asset->file, 'vendor/' ) !== false ? true : false;

		// Setup the actual URL
		if ( filter_var( $asset->file, FILTER_VALIDATE_URL ) ) {
			$asset->url = $asset->file;
		} else {
			$asset->url = $this->maybe_get_min_file( pngx_resource_url( $asset->file, false, ( $is_vendor ? '' : null ), $origin ) );
		}

		// If you are passing localize, you need `name` and `data`
		if ( ! empty( $asset->localize ) && ( is_array( $asset->localize ) || is_object( $asset->localize ) ) ) {
			if ( is_array( $asset->localize ) && empty( $asset->localize['name'] )  ) {
				foreach ( $asset->localize as $index => $local ) {
					$asset->localize[ $index ] = (object) $local;
				}
			} else {
				$asset->localize = (object) $asset->localize;

				// if we don't have both reset localize
				if ( ! isset( $asset->localize->data, $asset->localize->name ) ) {
					$asset->localize = array();
				}
			}
		}

		// Looks for a single conditional callable and places it in an Array
		if ( ! empty( $asset->conditionals ) && is_callable( $asset->conditionals ) ) {
			$asset->conditionals = array( $asset->conditionals );
		}

		// Groups is always an array of unique strings
		if ( ! empty( $asset->groups ) ) {
			$asset->groups = (array) $asset->groups;
			$asset->groups = array_filter( $asset->groups, 'is_string' );
			$asset->groups = array_unique( $asset->groups );
		}

		/**
		 * Filter an Asset loading variables
		 *
		 * @since  2.6
		 *
		 * @param object $asset
		 */
		$asset = apply_filters( 'pngx_asset_pre_register', $asset );

		// Set the Asset on the array of notices
		$this->assets[ $slug ] = $asset;

		// Sorts by priority
		uasort( $this->assets, array( $this, 'order_by_priority' ) );

		// Return the Slug because it might be modified
		return $asset;
	}

	/**
	 * Removes an Asset from been registered and enqueue
	 *
	 * @since 3.0
	 *
	 * @param  string $slug Slug of the Asset
	 *
	 * @return bool
	 */
	public function remove( $slug ) {
		if ( ! $this->exists( $slug ) ) {
			return false;
		}

		unset( $this->assets[ $slug ] );
		return true;
	}

	/**
	 * Get the Asset Object configuration
	 *
	 * @since 3.0
	 *
	 * @param  string $slug Slug of the Asset
	 *
	 * @return bool
	 */
	public function get( $slug = null ) {
		uasort( $this->assets, array( $this, 'order_by_priority' ) );

		if ( is_null( $slug ) ) {
			return $this->assets;
		}

		// Prevent weird stuff here
		$slug = sanitize_title_with_dashes( $slug );

		if ( ! empty( $this->assets[ $slug ] ) ) {
			return $this->assets[ $slug ];
		}

		return null;
	}

	/**
	 * Add the Priority ordering, which was causing an issue of not respecting which order stuff was registered
	 *
	 * @since  2.6
	 *
	 * @param  object  $a  First Subject to compare
	 * @param  object  $b  Second subject to compare
	 *
	 * @return boolean
	 */
	public function order_by_priority( $a, $b ) {
		return (int) $a->priority === (int) $b->priority ? 0 : (int) $a->priority > (int) $b->priority;
	}

	/**
	 * Checks if an Asset exists
	 *
	 * @since  2.6
	 *
	 * @param  string $slug Slug of the Asset
	 *
	 * @return bool
	 */
	public function exists( $slug ) {
		return is_object( $this->get( $slug ) ) ? true : false;
	}
}
