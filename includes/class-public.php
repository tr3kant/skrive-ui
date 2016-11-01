<?php
/**
 * Skrive UI Public
 *
 * @since NEXT
 * @package Skrive UI
 */

/**
 * Skrive UI Public.
 *
 * @since NEXT
 */
class Skrive_UI_Public {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since NEXT
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  NEXT
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function hooks() {
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
    wp_enqueue_script( 'skrive-ui-public', plugin_dir_url( dirname( __FILE__ ) )  . 'assets/js/skrive-ui-public.min.js', array( 'jquery' ), null, false );

	}
}
