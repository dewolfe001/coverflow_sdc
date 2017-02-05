<?php

/**
 * Coverflow_SDC_ShortCode Class
 *
 * @class Coverflow_SDC_ShortCode
 * @version	1.0.0
 * @since 1.0.0
 * @package	Coverflow_SDC
 * @author dewolfe001
 */

class Coverflow_SDC_ShortCode {
	public function __construct()
	{
		add_shortcode('coverflowdec_show', array($this, 'shortcode_slideshow'));
	}

	public function shortcode_slideshow( $atts, $content = "" )
	{
		$atts = shortcode_atts(
			array(
				'posts' => '-1',
				'parent' => '-1',
				'fadeIn' => '400',
				'loop' => 'true',
				'autoplay' => '4000',
				'pauseOnHover' => 'true',
				'style' => 'coverflow',
			), $atts, 'shortcode_slideshow' );

			/*

			posts - comma separated list of post ids

			From Flipster documentation

			 start: 'center',
			// ['center'|number]
			// Zero based index of the starting item, or use 'center' to start in the middle

			fadeIn: 400,
			// [milliseconds]
			// Speed of the fade in animation after items have been setup

			loop: false,
			// [true|false]
			// Loop around when the start or end is reached

			autoplay: false,
			// [false|milliseconds]
			// If a positive number, Flipster will automatically advance to next item after that number of milliseconds

			pauseOnHover: true,
			// [true|false]
			// If true, autoplay advancement will pause when Flipster is hovered

			style: 'coverflow',
			// [coverflow|carousel|flat|...]
			*/

		// build output
		// add in the CSS
		$file = 'jquery.flipster.css';
		wp_enqueue_style( 
			"flipster", 
			plugin_dir_url( __FILE__ )."../public/css/{$file}"
		);
		$file = 'public-cfsdc.css';
		wp_enqueue_style( 
			"flipster", 
			plugin_dir_url( __FILE__ )."../public/css/{$file}"
		);

		// local file
		$file = 'jquery.flipster.js';
		wp_enqueue_script( 
			"flipster", 
            plugin_dir_url( __FILE__ )."../public/js/{$file}", 
            array('jquery'), 
            TRUE 
        );

		// build the content

		$key = "coverflow_sdc";		
		$content .= '<!-- start: ' . esc_attr( $key ) . " -->\n";
		$content .= "<div id=\"".esc_attr( $key )."\">\n";
		$content .= '<ul class="flip-items">';

		$posts_in = array('post_type' => 'any');
		if ($atts['posts'] != "-1") {
			$posts_in['post__in'] = explode(',',$atts['posts']);
		}
		if ($atts['parent'] != "-1") {
			$posts_in['post_parent'] = intval($atts['parent']);
		}
		

		// $posts = new WP_Meta_Query($posts_in);
		$posts = new WP_Query( $posts_in );

		// echo $posts->request;

		// The Loop
		if( $posts->have_posts()):
			while ( $posts->have_posts() ) : $posts->the_post(); 
				$content .= '<li data-flip-title="'.get_the_title().'">';
				$content .= '<a href="' .get_the_permalink() . '">';
				$content .= get_the_post_thumbnail(get_the_ID(), 'medium');
				$content .= '</a>';
				$content .= '</li>';
			endwhile; 
		endif;
		wp_reset_postdata();

		$content .= '</ul>';
		$content .= "\n<!-- end: " . esc_attr( $key ) . " -->\n";

		$content .= "<script>\n";
		$content .= "\tjQuery( document ).ready(function($) {\n";
		$content .= "\tvar carousel = $(\"#".$key."\").flipster({\n";
		$content .= "\t\tstyle: '".$atts['style']."',\n";
		$content .= "\t\tspacing: -0.5,\n";
		$content .= "\t\tnav: false,\n";
		$content .= "\t\t buttons:false,\n";
		$content .= "\t\tautoplay: ".$atts['autoplay'].",\n";	
		$content .= "\t\tloop: ".$atts['loop'].",\n";

		$content .= "\t\tpauseOnHover: ".$atts['pauseOnHover'].",\n";
		$content .= "\t\tfadeIn: ".$atts['fadeIn'].",\n";

		$content .= "\t});\n";
		$content .= "\t});\n";
		$content .= "</script>\n";

		// send output
		return $content;
	}

	// form generation functions

	protected function render_field_text ( $key, $args ) {
		$html = '<input id="cfsdc_' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" size="40" type="text" value="' . esc_attr( $this->get_value( $args['id'], $args['default'], $args['section'] ) ) . '" />' . "\n";
		return $html;
	} // End render_field_text()

	protected function render_field_hidden ( $key, $args ) {
		$html = '<input id="cfsdc_' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="hidden" value="' . esc_attr( $this->get_value( $args['id'], $args['default'], $args['section'] ) ) . '" />' . "\n";
		return $html;
	} // End render_field_hidden()

	protected function render_field_button ( $key, $args ) {
		$html = '<input id="cfsdc_' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="button" value="' . $args['value']  . '" />' . "\n";
		return $html;
	} // End render_field_gmap()


	/**
	 * Return a value, using a desired retrieval method.
	 * @access  public
	 * @param  string $key option key.
	 * @param  string $default default value.
	 * @param  string $section field section.
	 * @since   1.0.0
	 * @return  mixed Returned value.

	 * Reused from the settings class
	 *
	 */
	public function get_value ( $key, $default, $section ) {
		if ($default === false) {
			$default = "";
		}
		if ($section === false) {
			return $default;
		}
	
		$values = get_option( 'cfsdc-' . $section, array() );

		if ( is_array( $values ) && isset( $values[$key] ) ) {
			$response = $values[$key];
		} else {
			$response = $default;
		}

		return $response;
	} // End get_value()


}