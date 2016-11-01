<?php
/**
 * Skrive UI Shortcodes
 *
 * @since NEXT
 * @package Skrive UI
 */


/**
 * Skrive UI Shortcodes.
 *
 * @since NEXT
 */
class Skrive_UI_Shortcodes {
  /**
   * Parent plugin class
   *
   * @var   class
   * @since NEXT
   */
  protected $plugin = null;


  private $internal_shortcode_classes = array(
    'Skrive_UI\Shortcodes\Image',
    'Skrive_UI\Shortcodes\YouTube',
    );
  private $registered_shortcode_classes = array();
  private $registered_shortcodes = array();
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
    add_action( 'init', array( $this, 'action_init_register_shortcodes' ) );
    add_action( 'shortcode_ui_after_do_shortcode', function( $shortcode ) {
      return self::get_shortcake_admin_dependencies();
    });
    add_filter( 'pre_kses', array( $this, 'filter_pre_kses' ) );
  }
  /**
   * Set up shortcode filters
   */
  private function setup_filters() {
  }

  /**
   * Register all of the shortcodes
   */
  public function action_init_register_shortcodes() {

    $this->registered_shortcode_classes = apply_filters( 'skrive_ui_shortcode_classes', $this->internal_shortcode_classes );
    foreach ( $this->registered_shortcode_classes as $class ) {
      $shortcode_tag = $class::get_shortcode_tag();
      $this->registered_shortcodes[ $shortcode_tag ] = $class;
      add_shortcode( $shortcode_tag, array( &$this, 'do_shortcode_callback' ) );
      $class::setup_actions();
      $ui_args = apply_filters( 'skrive_ui_shortcode_ui_args', $class::get_shortcode_ui_args(), $shortcode_tag );
      if ( ! empty( $ui_args ) && function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
        shortcode_ui_register_for_shortcode( $shortcode_tag, $ui_args );
      }
    }
  }

  /**
   * Modify post content before kses is applied
   * Used to trans
   */
  public function filter_pre_kses( $content ) {

    foreach ( $this->registered_shortcode_classes as $shortcode_class ) {
      $content = $shortcode_class::reversal( $content );
    }
    return $content;
  }

  /**
   * Do the shortcode callback
   */
  public function do_shortcode_callback( $attrs, $content = '', $shortcode_tag ) {
    if ( empty( $this->registered_shortcodes[ $shortcode_tag ] ) ) {
      return '';
    }

    // wp_enqueue_script( $this->plugin_name, SHORTCAKE_BAKERY_URL_ROOT . 'assets/js/shortcake-bakery.js', array( 'jquery' ), SHORTCAKE_BAKERY_VERSION );

    $class = $this->registered_shortcodes[ $shortcode_tag ];
    $output = $class::callback( $attrs, $content, $shortcode_tag );
    return apply_filters( 'skrive_ui_shortcode_callback', $output, $shortcode_tag, $attrs, $content );
  }

  /**
   * Admin dependencies.
   * Scripts required to make shortcake previews work correctly in the admin.
   *
   * @return string
   */
  public static function get_shortcake_admin_dependencies() {
    if ( ! is_admin() ) {
      return;
    }
    // $r = '<script src="' . esc_url( includes_url( 'js/jquery/jquery.js' ) ) . '"></script>';
    // $r .= '<script type="text/javascript" src="' . esc_url( SHORTCAKE_BAKERY_URL_ROOT . 'assets/js/shortcake-bakery.js' ) . '"></script>';
    // return $r;
  }
}
