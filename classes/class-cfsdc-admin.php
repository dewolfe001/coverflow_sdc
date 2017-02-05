<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Coverflow_SDC_Admin Class
 *
 * @class Coverflow_SDC_Admin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Coverflow_SDC
 * @author dewolfe001
 */
final class Coverflow_SDC_Admin {
	/**
	 * Coverflow_SDCAdmin The single instance of Coverflow_SDCAdmin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The string containing the dynamically generated hook token.
	 * @var     string
	 * @access  private
	 * @since   1.0.0
	 */
	private $_hook;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		// Register the settings with WordPress.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Register the settings screen within WordPress.
		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );
	} // End __construct()

	/**
	 * Main Coverflow_SDCAdmin Instance
	 *
	 * Ensures only one instance of Coverflow_SDCAdmin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Main Coverflow_SDCAdmin instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Register the admin screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings_screen () {
		$this->_hook = add_submenu_page( 'options-general.php', __( 'Coverflow Settings', 'cfsdc' ), __( 'Cloverflow Settings', 'cfsdc' ), 'manage_options', 'cfsdc', array( $this, 'settings_screen' ) );
	} // End register_settings_screen()

	/**
	 * Output the markup for the settings screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function settings_screen () {
		global $title;
		$sections = Coverflow_SDC()->settings->get_settings_sections();
		$tab = $this->_get_current_tab( $sections );
		?>
		<div class="wrap cfsdc-wrap">
			<?php
				echo $this->get_admin_header_html( $sections, $title );
			?>
			<form action="options.php" method="post">
				<?php
					settings_fields( 'cfsdc-settings-' . $tab );
					do_settings_sections( 'cfsdc-' . $tab );
					// submit_button( __( 'Save Changes', 'cfsdc' ) );
				?>
			</form>
		</div><!--/.wrap-->
		<div class="wrap cfsdc-wrap">
		<h3>Shortcode variables</h3>		
		<pre>[coverflowdec_show ...]</pre>
		<dl>
		<dt>posts: #id number,</dt>
		<dd>A comma separated list of post id numbers.</dd>

		<dt>parent: #id number,</dt>
		<dd>The ID# of a parent. It calls for all of the children of a parent page</dd>

		<dt>start: 'center',</dt>
		<dd>['center'|number]<br/>Zero based index of the starting item, or use 'center' to start in the middle</dd>

		<dt>fadeIn: 400,</dt>
		<dd>[milliseconds]<br/>Speed of the fade in animation after items have been setup</dd>

		<dt>loop: true,</dt>
		<dd>[true|false]<br/>Loop around when the start or end is reached</dd>

		<dt>autoplay: false,</dt>
		<dd>[false|milliseconds]<br/>If a positive number, Flipster will automatically advance to next item after that number of milliseconds</dd>

		<dt>pauseOnHover: true,</dt>
		<dd>[true|false]<br/>If true, autoplay advancement will pause when Flipster is hovered</dd>

		<dt>style: 'coverflow',</dt>
		<dd>>[coverflow|carousel|flat|...]</dd>
		</dl>
		<br/>Donate link: <a href="https://www.paypal.me/dewolfe001/20">Paypal</a>

		</div>
		<?php
	} // End settings_screen()

	/**
	 * Register the settings within the Settings API.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings () {
		$sections = Coverflow_SDC()->settings->get_settings_sections();
		if ( 0 < count( $sections ) ) {
			foreach ( $sections as $k => $v ) {
				register_setting( 'cfsdc-settings-' . sanitize_title_with_dashes( $k ), 'cfsdc-' . $k, array( $this, 'validate_settings' ) );
				add_settings_section( sanitize_title_with_dashes( $k ), $v, array( $this, 'render_settings' ), 'cfsdc-' . $k, $k, $k );
			}
		}
	} // End register_settings()

	/**
	 * Render the settings.
	 * @access  public
	 * @param  array $args arguments.
	 * @since   1.0.0
	 * @return  void
	 */
	public function render_settings ( $args ) {
		$token = $args['id'];
		$fields = Coverflow_SDC()->settings->get_settings_fields( $token );

		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				$args 		= $v;
				$args['id'] = $k;

				add_settings_field( $k, $v['name'], array( Coverflow_SDC()->settings, 'render_field' ), 'cfsdc-' . $token , $v['section'], $args );
			}
		}
	} // End render_settings()

	/**
	 * Validate the settings.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $input Inputted data.
	 * @return  array        Validated data.
	 */
	public function validate_settings ( $input ) {
		$sections = Coverflow_SDC()->settings->get_settings_sections();
		$tab = $this->_get_current_tab( $sections );
		return Coverflow_SDC()->settings->validate_settings( $input, $tab );
	} // End validate_settings()

	/**
	 * Return marked up HTML for the header tag on the settings screen.
	 * @access  public
	 * @since   1.0.0
	 * @param   array  $sections Sections to scan through.
	 * @param   string $title    Title to use, if only one section is present.
	 * @return  string 			 The current tab key.
	 */
	public function get_admin_header_html ( $sections, $title ) {
		$defaults = array(
							'tag' => 'h2',
							'atts' => array( 'class' => 'cfsdc-wrapper' ),
							'content' => $title
						);

		$args = $this->_get_admin_header_data( $sections, $title );

		$args = wp_parse_args( $args, $defaults );

		$atts = '';
		if ( 0 < count ( $args['atts'] ) ) {
			foreach ( $args['atts'] as $k => $v ) {
				$atts .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
			}
		}

		$response = '<' . esc_attr( $args['tag'] ) . $atts . '>' . $args['content'] . '</' . esc_attr( $args['tag'] ) . '>' . "\n";

		return $response;
	} // End get_admin_header_html()

	/**
	 * Return the current tab key.
	 * @access  private
	 * @since   1.0.0
	 * @param   array  $sections Sections to scan through for a section key.
	 * @return  string 			 The current tab key.
	 */
	private function _get_current_tab ( $sections = array() ) {
		if ( isset ( $_GET['tab'] ) ) {
			$response = sanitize_title_with_dashes( $_GET['tab'] );
		} else {
			if ( is_array( $sections ) && ! empty( $sections ) ) {
				list( $first_section ) = array_keys( $sections );
				$response = $first_section;
			} else {
				$response = '';
			}
		}

		return $response;
	} // End _get_current_tab()

	/**
	 * Return an array of data, used to construct the header tag.
	 * @access  private
	 * @since   1.0.0
	 * @param   array  $sections Sections to scan through.
	 * @param   string $title    Title to use, if only one section is present.
	 * @return  array 			 An array of data with which to mark up the header HTML.
	 */
	private function _get_admin_header_data ( $sections, $title ) {
		$response = array( 'tag' => 'h2', 'atts' => array( 'class' => 'cfsdc-wrapper' ), 'content' => $title );

		if ( is_array( $sections ) && 1 < count( $sections ) ) {
			$response['content'] = '';
			$response['atts']['class'] = 'nav-tab-wrapper';

			$tab = $this->_get_current_tab( $sections );

			foreach ( $sections as $key => $value ) {
				$class = 'nav-tab';
				if ( $tab == $key ) {
					$class .= ' nav-tab-active';
				}

				$response['content'] .= '<a href="' . admin_url( 'options-general.php?page=cfsdc&tab=' . sanitize_title_with_dashes( $key ) ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $value ) . '</a>';
			}
		}

		return (array)apply_filters( 'cfsdc-get-admin-header-data', $response );
	} // End _get_admin_header_data()
} // End Class
