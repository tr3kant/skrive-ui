<?php

namespace Skrive_UI\Shortcodes;

class Image extends Shortcode {

  public static function get_shortcode_ui_args() {
    return array(
      'label'          => esc_html__( 'Image', 'skrive-ui' ),
      'listItemImage' => 'dashicons-format-image',
      'attrs' => array(
        array(
          'label' => esc_html__( 'Choose Attachment', 'skrive-ui' ),
          'attr'  => 'attachment',
          'type'  => 'attachment',
          'libraryType' => array( 'image' ),
          'addButton'   => esc_attr__( 'Select Image', 'skrive-ui' ),
          'frameTitle'  => esc_attr__( 'Select Image', 'skrive-ui' ),
        ),
        array(
          'label'       => esc_html__( 'Alt', 'skrive-ui' ),
          'attr'        => 'alt',
          'type'        => 'text',
          'encode'      => true,
          'placeholder' => esc_attr__( 'Alt text for the image', 'skrive-ui' ),
        ),
        array(
          'label'       => esc_html__( 'Caption', 'skrive-ui' ),
          'attr'        => 'caption',
          'type'        => 'textarea',
          'encode'      => true,
          'placeholder' => esc_attr__( 'Caption for the image', 'skrive-ui' ),
        ),
        array(
          'label'       => esc_html__( 'Style', 'skrive-ui' ),
          'attr'        => 'style',
          'type'        => 'select',
          'value'       => 'default',
          'options' => array(
            'default'   => esc_attr__( 'Default',   'skrive-ui' ),
            'fullwidth' => esc_attr__( 'Fullwidth', 'skrive-ui' ),
            'parallax'  => esc_attr__( 'Parallax',  'skrive-ui' ),
            'small'     => esc_attr__( 'Small',     'skrive-ui' ),
          ),
        ),
        array(
          'label'       => esc_html__( 'Link to', 'skrive-ui' ),
          'attr'        => 'linkto',
          'type'        => 'select',
          'value'       => get_option( 'image_default_link_type' ),
          'options' => array(
            'none'       => esc_attr__( 'None (no link)',          'skrive-ui' ),
            'attachment' => esc_attr__( 'Link to attachment file', 'skrive-ui' ),
            'file'       => esc_attr__( 'Link to file',            'skrive-ui' ),
            'custom'     => esc_attr__( 'Custom link',             'skrive-ui' ),
          ),
        ),
        array(
          'label'       => esc_html__( 'Custom link', 'skrive-ui' ),
          'attr'        => 'url',
          'type'        => 'text',
          'placeholder' => esc_attr__( 'URL to link to (if above link is "custom")', 'skrive-ui' ),
        ),
      ),
    );
  }

  public static function reversal( $content ) {


    return $content;
  }

  /**
   * Allow subclasses to register their own action
   * Fires after the shortcode has been registered on init
   *
   * @return null
   */
  public static function setup_actions() {
    add_filter( 'media_send_to_editor', 'Skrive_UI\Shortcodes\Image::filter_media_send_to_editor', 15, 3 );
  }

  public static function callback( $attrs, $content = '' ) {
    $shortcode_args = static::get_shortcode_ui_args();
    $registered_atts = array_fill_keys( wp_list_pluck( $shortcode_args['attrs'], 'attr' ), null );
    $shortcode_tag = static::get_shortcode_tag();

    $args_with_defaults = array_merge( $registered_atts,
      array(
        'src'        => null,
        'style'      => 'default',
        'context'    => 'content'
      )
    );
    $attr = shortcode_atts( $args_with_defaults, $attrs, $shortcode_tag );
    $attr = apply_filters( 'image_shortcode_attrs', $attr );

    $img_src = wp_get_attachment_image_url( $attr['attachment'],$attr['style']);
    $img_srcset = wp_get_attachment_image_srcset( $attr['attachment'],$attr['style']);
    $image_html = '<img class="lazy-image" alt="'. $attr['alt'] .'" src="' . $img_src . '" srcset="' . $img_srcset .'" />';
    if ( ! empty( $attr['linkto'] ) &&
      ( in_array( $attr['linkto'], array( 'file', 'attachment' ), true ) ||
      ( 'custom' === $attr['linkto'] && ! empty( $attr['url'] ) ) ) ) {
        $image_html = self::linkify( $image_html, $attr );
    }

    $html = '<div id="media-'. $attr['attachment'].'" class="article-block article-block--image article-block--image-' . $attr['style'] . '">';
    $html .= '<figure class="field--slideshow image">';
    $html .= '<div class="image-wrapper">';
    $html .= $image_html;
    $html .= '</div>';
    if($attr['caption']){
      $html .= '<figcaption class="entry__image__caption">';
      $html .= '<p class="caption-text">' . $attr['caption'] . '</p>';
      $html .= '</figcaption>';
    }
    $html .= '</figure>';
    $html .= '</div>';

    return $html;

  }
  private static function linkify( $img_tag, $attributes ) {

    $_id = intval( $attributes['attachment'] );

    $link_attrs = array();

    if ( isset( $attributes['url'] ) ) {
      $link_attrs['href'] = esc_url( $attributes['url'] );
    } else if ( ! empty( $attributes['linkto'] ) && 'attachment' === $attributes['linkto'] ) {
      $link_attrs['href'] = get_permalink( $_id );
    } elseif ( ! empty( $attributes['linkto'] ) && 'file' === $attributes['linkto'] ) {
      $attachment_src = wp_get_attachment_image_src( $_id, 'full', false, $attributes );
      $link_attrs['href'] = $attachment_src[0];
    } else {
      // No link is defined, or its in a format that's not implemented yet.
      return $img_tag;
    }

    $html = '<a ';

    foreach ( $link_attrs as $attr_name => $attr_value ) {
      $html .= sanitize_key( $attr_name ) . '="' . esc_attr( $attr_value ) . '" ';
    }

    $html .= '>' . $img_tag .'</a>';

    return $html;
  }

  public static function filter_media_send_to_editor( $html, $attachment_id, $attachment ) {
    $media_post = get_post( $attachment_id );

    if ( ! $media_post || 'image' !== strtolower( substr( $media_post->post_mime_type, 0, 5 ) ) ) {
      return $html;
    }

    $shortcode_attrs = array(
      'attachment' => $media_post->ID,
    );

    if ( ! empty( $attachment['align'] ) ) {
      $shortcode_attrs['align'] = 'align' . $attachment['align'];
    }

    $allowed_attrs = array(
      'image-size' => 'size',
      'image_alt' => 'alt',
      'post_excerpt' => 'caption',
      'width' => 'width',
    );

    $shortcode_ui_def = self::get_shortcode_ui_args();
    $encoded_attributes = wp_list_pluck(
      array_filter( $shortcode_ui_def['attrs'], function( $attr ) {
        return ! empty( $attr['encode'] ) && $attr['encode'];
      } ),
      'attr'
    );

    foreach ( $allowed_attrs as $attachment_attr => $shortcode_attr ) {
      if ( ! empty( $attachment[ $attachment_attr ] ) ) {
        $shortcode_attrs[ $shortcode_attr ] = in_array( $shortcode_attr, $encoded_attributes, true ) ?
          rawurlencode( $attachment[ $attachment_attr ] ) : $attachment[ $attachment_attr ];
      }
    }
    $shortcode_attrs = apply_filters( 'image_shortcode_send_to_editor_attrs', $shortcode_attrs, $html, $attachment_id, $attachment );

    $shortcode = '[image ';

    foreach ( $shortcode_attrs as $attr_name => $attr_value ) {
      $shortcode .= sanitize_key( $attr_name ) . '="' . esc_attr( $attr_value ) . '" ';
    }

    $shortcode .= '/]';

    return $shortcode;


  }
}
