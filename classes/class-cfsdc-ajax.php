<?php

/**
 * Coverflow_SDC_Ajax Class
 *
 * @class Coverflow_SDC_Ajax
 * @version	1.0.0
 * @since 1.0.0
 * @package	Coverflow_SDC
 * @author dewolfe001
 */

 // Unused but kept around... just in case...

class Coverflow_SDC_Ajax {
	public static $instance = null;
	public $nonce = '';
	public $name = 'cfsdcajax';
	public static function getInstance()
	{
		null === self::$instance AND self::$instance = new self;
		return self::$instance;
	}

	public function __construct()
	{
		add_action( 'wp_loaded', array( $this, 'scripts_register' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_enqueue' ) );
		add_action( "wp_ajax_nopriv_{$this->name}_action", array( $this, 'ajax_callback' ) );
	}

	public function scripts_register( $page )
	{
		$file = 'iquery.flipster.js';
		wp_register_script(
			$this->name,
			plugin_dir_url( __FILE__ )."../public/js/{$file}", 
			array('jquery'),
			true
		);
	}

	public function scripts_enqueue( $page )
	{
		$file = 'iquery.flipster.js';
		wp_enqueue_script( 
			$this->name, 
            plugin_dir_url( __FILE__ )."../public/js/{$file}", 
            array('jquery'), 
            TRUE 
        );
	}

	public function render_form()
	{
		wp_nonce_field( "{$this->name}_action", $this->name );
		# @TODO Build form
		# @TODO Hook somewhere
	}

	public function ajax_callback( $data )
	{
		$data = array_map( 'esc_attr', $_GET );
		! check_ajax_referer( $data['action'], "_ajax_nonce", false )
			AND wp_send_json_error();
		# @TODO Handle processing of data in here
		# @TODO Validate data with absint(), esc_*(), etc.
		# @example #2)
		if ( ! $data['foo'] )
			wp_send_json_error();
		wp_send_json_success( array(
			#'foo' => 'bar',
		) );
	}
}