<?php
/**
 * Main Plugin Engine class.
 */
class Pngx__Main {

	/**
	 * Stores the version for the plugin engine.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const VERSION = '4.0.0';

	/**
	 * Stores the slug for the plugin engine.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	const SLUG = 'plugin-engine';

	/**
	 * Name of the plugin engine options.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const OPTIONS_ID = 'plugin_engine_options';

	/**
	 * PNGX DB Version Key.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	public static $db_version_key = 'pngx_db_version';

	/**
	 * PNGX Schema Version Key.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	public static $schema_version_key = 'pngx_schema_version';

	/**
	 * PNGX Schema version.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	public static $db_version = '400';

	/**
	 * Plugin context.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_context;

	/**
	 * Plugin context class.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $plugin_context_class;

	/**
	 * Whether AJAX is currently being processed.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $doing_ajax = false;

	/**
	 * Plugin Directory path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_dir;

	/**
	 * Plugin Path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Plugin Ur.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_url;

	/**
	 * Plugin Resource Path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $resource_path;

	/**
	 * Plugin Resource Url.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $resource_url;


	/**
	 * Plugin Vendor Path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $vendor_path;

	/**
	 * Plugin Vendor Path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $vendor_url;

	/**
	 * The slug that will be used to identify HTTP requests pngx should handle.
	 *
	 * @since 3.3.0
	 *
	 * @var string
	 */
	public static $request_slug = 'pngx_request';

	/**
	 * Static Singleton Holder
	 *
	 * @since 1.0.0
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct( $context = null ) {
		if ( self::$instance ) {
			return;
		}

		require_once realpath( dirname( dirname( dirname( __FILE__ ) ) ) . '/vendor/autoload.php' );

		// the DI container class
		require_once dirname( __FILE__ ) . '/Container.php';

		if ( is_object( $context ) ) {
			$this->plugin_context       = $context;
			$this->plugin_context_class = get_class( $context );
		}

		$this->plugin_path   = trailingslashit( dirname( dirname( dirname( __FILE__ ) ) ) );
		$this->plugin_dir    = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url    = plugins_url( $this->plugin_dir );
		$parent_plugin_dir   = trailingslashit( plugin_basename( $this->plugin_path ) );
		$this->plugin_url    = plugins_url( $parent_plugin_dir === $this->plugin_dir ? $this->plugin_dir : $parent_plugin_dir );
		$this->resource_path = $this->plugin_path . 'src/resources/';
		$this->resource_url  = $this->plugin_url . 'src/resources/';
		$this->vendor_path   = $this->plugin_path . 'vendor/';
		$this->vendor_url    = $this->plugin_url . 'vendor/';

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 1 );

		if ( did_action( 'plugins_loaded' ) && ! doing_action( 'plugins_loaded' ) ) {
			/*
			 * This might happen in the context of a plugin activation.
			 * Complete the loading now and set the singleton instance to avoid infinite loops.
			 */
			self::$instance = $this;
			$this->plugins_loaded();
		}
	}

	/**
	 * Load the plugin engine.
	 */
	public function plugins_loaded() {
		$this->load_text_domain( 'plugin-engine', basename( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/plugin-engine/languages/' );

		$this->init_autoloading();

		$this->bind_implementations();

		$this->loadLibraries();

		$this->add_hooks();

		/**
		 * Runs once all pngx libs are loaded and initial hooks are in place.
		 *
		 * @since 3.0
		 */
		do_action( 'pngx_engine_loaded' );

		/**
		 * Runs to register loaded plugins
		 */
		do_action( 'pngx_plugins_loaded' );

		/**
		 * Use pngx_plugins_loaded instead
		 *
		 * @deprecated 2.6
		 */
		do_action( 'plugin_engine_loaded' );
	}

	/**
	 * Get's the instantiated context of this class. I.e. the object that instantiated this one.
	 */
	public function context() {
		return $this->plugin_context;
	}

	/**
	 * Setup the autoloader for pngx files
	 */
	protected function init_autoloading() {

		if ( ! class_exists( 'Pngx__Autoloader' ) ) {
			require_once dirname( __FILE__ ) . '/Autoloader.php';
		}

		$autoloader = Pngx__Autoloader::instance();

		$prefixes = array( 'Pngx__' => dirname( __FILE__ ) );
		$autoloader->register_prefixes( $prefixes );

		foreach ( glob( $this->plugin_path . 'src/deprecated/*.php' ) as $file ) {
			$class_name = str_replace( '.php', '', basename( $file ) );
			$autoloader->register_class( $class_name, $file );
		}

		$autoloader->register_autoloader();
	}

	/**
	 * Get's the class name of the instantiated plugin context of this class. I.e. the class name of the object that
	 * instantiated this one.
	 */
	public function context_class() {
		return $this->plugin_context_class;
	}

	/**
	 * Registers the slug bound to the implementations in the container.
	 */
	public function bind_implementations() {
		pngx_singleton( 'pngx.assets', 'Pngx__Assets' );
		pngx_singleton( 'pngx.context', 'Pngx__Context' );
		pngx_singleton( 'pngx.admin.assets', 'Pngx__Admin__Assets' );
		pngx_singleton( 'pngx.logger', 'Pngx__Log' );
		pngx_singleton( 'pngx.db', 'Pngx__Db' );
		pngx_singleton( 'pngx.allowed_tags', 'Pngx__Allowed_Tags' );
		pngx_register( 'pngx.register.cpt', new Pngx__Register_Post_Type() );
		pngx_register( 'pngx.register.tax', new Pngx__Register_Taxonomy() );
		pngx_singleton( 'cache', 'Pngx__Cache', [ 'hook' ] );
		pngx_singleton( \Pngx\Ajax\Dropdown::class, \Pngx\Ajax\Dropdown::class, [ 'hook' ] );

		pngx_register_provider( Pngx\Service_Providers\Dialog::class );
		pngx_register_provider( Pngx\Service_Providers\Carousel::class );
		pngx_register_provider( Pngx\Duplicate\Post_Types::class );
	}

	/**
	 * Load all the required library files.
	 */
	protected function loadLibraries() {
		//Core Functions
		require_once $this->plugin_path . 'src/functions/template-tags/general.php';
		require_once $this->plugin_path . 'src/functions/template-tags/strings.php';
		require_once $this->plugin_path . 'src/functions/template-tags/html.php';
		require_once $this->plugin_path . 'src/functions/utils.php';

	}

	/**
	 * Adds core hooks
	 */
	public function add_hooks() {
		//Load Admin Class if in Admin Section
		if ( is_admin() ) {
			new Pngx__Admin__Main();
		}

		add_action( 'plugins_loaded', array( 'Pngx__Admin__Notices', 'instance' ), 1 );
		add_action( 'plugins_loaded', array( $this, 'pngx_plugins_loaded' ), PHP_INT_MAX );
	}

	/**
	 * Runs pngx_after_plugins_loaded action, should be hooked to the end of plugins_loaded
	 */
	public function pngx_plugins_loaded() {
		pngx( 'cache' );
		pngx( \Pngx\Ajax\Dropdown::class );
		pngx_singleton( 'pngx.feature-detection', 'Pngx__Feature_Detection' );
		pngx_register_provider( 'Pngx__Service_Providers__Processes' );

		/**
		 * Runs after all plugins including Pngx ones have loaded
		 *
		 * @since 3.2.0
		 */
		do_action( 'pngx_after_plugins_loaded' );
	}

	/**
	 * A Helper method to load text domain
	 * First it tries to load the wp-content/languages translation then if falls to the
	 * try to load $dir language files
	 *
	 * @param string $domain The text domain that will be loaded
	 * @param string $dir    What directory should be used to try to load if the default doenst work
	 *
	 * @return bool  If it was able to load the text domain
	 */
	public function load_text_domain( $domain, $dir = false ) {

		// Added safety just in case this runs twice...
		if ( is_textdomain_loaded( $domain ) && ! is_a( $GLOBALS['l10n'][ $domain ], 'NOOP_Translations' ) ) {
			return true;
		}

		$locale = get_locale();
		$mofile = WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo';

		/**
		 * Allows users to filter which file will be loaded for a given text domain
		 * Be careful when using this filter, it will apply across the whole plugin suite.
		 *
		 * @param string      $mofile The path for the .mo File
		 * @param string      $domain Which plugin domain we are trying to load
		 * @param string      $locale Which Language we will load
		 * @param string|bool $dir    If there was a custom directory passed on the method call
		 */
		$mofile = apply_filters( 'pngx_load_text_domain', $mofile, $domain, $locale, $dir );

		$loaded = load_plugin_textdomain( $domain, false, $mofile );

		if ( false !== $dir && ! $loaded ) {
			return load_plugin_textdomain( $domain, false, $dir );
		}

		return $loaded;
	}

	/**
	 * Returns the post types registered with plugin engine
	 */
	public function get_post_types() {
		// we default the post type array to empty in plugin engine. Plugins like PNGX add to it
		return apply_filters( 'pngx_post_types', array() );
	}

	/**
	 * Merge the Defaults with new values
	 *
	 * @param $defaults
	 * @param $updates
	 *
	 * @return array
	 */
	public static function merge_defaults( $defaults, $updates ) {

		$updates = (array) $updates;
		$out     = array();
		foreach ( $defaults as $name => $default ) {
			if ( array_key_exists( $name, $updates ) ) {
				$out[ $name ] = $updates[ $name ];
			} else {
				$out[ $name ] = $default;
			}
		}

		return $out;
	}

	/**
	 * Static Singleton Factory Method
	 *
	 * @return Pngx__Main
	 */
	public static function instance() {
		static $instance;

		if ( ! $instance ) {
			$instance = new self;
		}

		return $instance;
	}
}