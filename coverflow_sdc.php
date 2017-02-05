<?php
/**
 * Plugin Name: Coverflow SDC
 * Plugin URI: http://products.shawndewolfe.com/product/coverflow-wordpress-plugin/
 * Description: The plugin to orchestrate the display of items in a coverflow format.
 * Version: 1.0.1
 * Author: Shawn DeWolfe
 * Author URI: http://shawndewolfe.com/
 * Requires at least: 4.0.0
 * Tested up to: 4.0.0
 *
 * Text Domain: coverflowsdc
 * Domain Path: /languages/
 *
 * @package Coverflow_SDC
 * @category Core
 * @author dewolfe001
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Coverflow_SDC to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Coverflow_SDC
 */
function Coverflow_SDC() {
	return Coverflow_SDC::instance();
} // End Coverflow_SDC()

add_action( 'plugins_loaded', 'Coverflow_SDC' );

/**
 * Main Coverflow_SDC Class
 *
 * @class Coverflow_SDC
 * @version	1.0.0
 * @since 1.0.0
 * @package	Coverflow_SDC
 * @author Matty
 */
final class Coverflow_SDC {
	/**
	 * Coverflow_SDC The single instance of Coverflow_SDC.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;
	// Admin - End

	// Post Types - Start
	/**
	 * The post types we're registering.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $post_types = array();
	// Post Types - End
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		$this->token 			= 'coverflowsdc';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		// Admin - Start
		require_once( 'classes/class-cfsdc-settings.php' );
			$this->settings = Coverflow_SDC_Settings::instance();

		if ( is_admin() ) {
			require_once( 'classes/class-cfsdc-admin.php' );
			$this->admin = Coverflow_SDC_Admin::instance();
		}
		// Admin - End

		require_once( 'classes/class-cfsdc-shortcode.php' );
		add_action( 'plugins_loaded', array( 'Coverflow_SDC_ShortCode') );
			$this->shortcode = new Coverflow_SDC_ShortCode();


		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

	} // End __construct()

	/**
	 * Main Coverflow_SDC Instance
	 *
	 * Ensures only one instance of Coverflow_SDC is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Coverflow_SDC()
	 * @return Main Coverflow_SDC instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'coverflowsdc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	// Coverflow specifics

	public function enqueue_scripts() {
	/**
	 * The public-facing functionality of the plugin.
	 *
	 * @link       http://example.com
	*/
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'public/js/public-cfsdc.js', array( 'jquery' ), $this->version, false );

	}


} // End Class
