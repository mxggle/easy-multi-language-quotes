<?php
/**
 * Plugin Name: Easy Multi-Language Quotes
 * Plugin URI:  https://example.com/easy-multi-language-quotes
 * Description: Display random multi-language quotes (ZH, EN, JA) via shortcode. Supports JSON/CSV upload and customizable frequency.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * License:     GPL-2.0+
 * Text Domain: easy-multi-language-quotes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'EMLQ_VERSION', '1.0.0' );
define( 'EMLQ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EMLQ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class.
 */
class Easy_Multi_Language_Quotes {

	/**
	 * Loader that's responsible for maintaining and registering all hooks.
	 */
	protected $loader;

	/**
	 * Plugin version.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		$this->version = EMLQ_VERSION;
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		require_once EMLQ_PLUGIN_DIR . 'admin/class-emlq-admin.php';
		require_once EMLQ_PLUGIN_DIR . 'public/class-emlq-public.php';
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new EMLQ_Admin( $this->get_plugin_name(), $this->get_version() );
		add_action( 'admin_menu', array( $plugin_admin, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $plugin_admin, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 */
	private function define_public_hooks() {
		$plugin_public = new EMLQ_Public( $this->get_plugin_name(), $this->get_version() );
		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_styles' ) );
		add_shortcode( 'easy_multi_language_quote', array( $plugin_public, 'render_shortcode' ) );
        add_action( 'rest_api_init', array( $plugin_public, 'register_routes' ) );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return 'easy-multi-language-quotes';
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}

/**
 * Begins execution of the plugin.
 */
function run_easy_multi_language_quotes() {
	$plugin = new Easy_Multi_Language_Quotes();
}
run_easy_multi_language_quotes();
