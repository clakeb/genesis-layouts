<?php

namespace GoNear;

class Genesis_Post_Layouts {

    public static $instance = null;
    public $current_settings = false;
    public $image_sizes = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            return self::$instance = new self();
        } else {
            return self::$instance;
        }
    }

    private function __construct() {
        add_action( 'genesis_before', array( $this, 'init' ) );
    }

    public function init() {
        // Init the settings
        add_action( 'genesis_before_while', array( $this, 'init_settings' ), -1 );
        add_action( 'genesis_before_entry', array( $this, 'init_settings' ), -1 );

        // Format & Reset the post
        add_action( 'genesis_before_entry', array( $this, 'format_post' ), 0 );
        add_action( 'genesis_after_entry', array( $this, 'reset_post' ), 9999 );

        // Loop Wrap
        add_action( 'genesis_before_while', array( $this, 'open_loop_wrap' ) );
        add_action( 'genesis_after_endwhile', array( $this, 'close_loop_wrap' ) );

        // Loop Wrap Atts
        add_filter( 'genesis_attr_loop', array( $this, 'attr_loop' ) );

        // Entry Wrap
        add_action( 'genesis_entry_header',  array( $this, 'open_entry_wrap' ), -1 );
        add_action( 'genesis_entry_footer',  array( $this, 'close_entry_wrap' ), 9999 );

        // Post Meta
        add_filter( 'genesis_post_meta', array( $this, 'post_meta' ) );

        // Entry Image Atts
        add_filter( 'genesis_attr_entry-thumbnail', array( $this, 'attr_entry_thumbnail' ) );
        add_filter( 'genesis_attr_entry-image', array( $this, 'attr_entry_image' ) );
        add_filter( 'the_content', array( $this, 'wrap_content_images' ) );


        // Read More Link
        add_filter( 'excerpt_more', array( $this, 'wrap_content_more_link' ) );
        add_filter( 'the_content_more_link', array( $this, 'wrap_content_more_link' ) );
        add_filter( 'get_the_content_more_link', array( $this, 'wrap_content_more_link' ) );

        // Excerpt Length
        add_filter( 'genesis_pre_get_option_content_archive_limit', array( $this, 'content_archive_limit' ) );

        // Footer Markup Override
        remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
        remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
        add_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_open' ), 5 );
        add_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_close' ), 15 );
    }

    public function get_post_layouts() {
        $layouts = apply_filters( 'custom_post_layouts', array(
            array(
                'settings' => array(),
                'condition' => array(
                    array(
                        array(
                            'var'      => true,
                            'var_type' => 'variable',
                            'params'   => array(),
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                ),
            ),
        ) );

        $defaults = array(
            'condition' => array(),
            'settings' => array(),
        );

        $defaults = array(
            'condition' => array(),
            'settings' => array(),
        );

        foreach ( $layouts as $key => $args ) {
            $layout = wp_parse_args( $args, $defaults );
            $layouts[ $key ] = $layout;
        }

        return $layouts;
    }

    public function check_condition( $condition = array() ) {
        global $post;

        $default_condition = array(
            'var'      => '',
            'var_type' => 'function',
            'params'   => array(),
            'operator' => '==',
            'value'    => true,
        );

        $condition = wp_parse_args( $condition, $default_condition );

        extract( $condition );

        if ( $var_type == 'function' ) {
            $var = call_user_func_array( $var, $params );
        } else if ( $var_type == 'variable' ) {
            $var = $var;
        } else if ( $var_type == 'post_property' ) {
            $var = $post->$var;
        }

        if ( $operator == "==" ) {
            $match = ( $var === $value );
        } else if ( $operator == "!=" ) {
            $match = ( $var !== $value );
        }

        return $match;
    }

    public function check_conditions( $conditions, $calc_ands = true ) {
        $or = array();

        foreach ( $conditions as $key => $ors ) {
            $and = array();

            foreach ( $ors as $key2 => $ands ) {
                if ( $this->check_condition( $ands ) ) {
                    $and[ $key2 ] = 1;
                } else if ( $calc_ands ) {
                    $and[ $key2 ] = 0;
                }
            }

            $or[ $key ] = ( ! empty( $and ) && array_product( $and ) == 1 );
        }

        return ( array_search( true, $or ) !== false );
    }

    public function init_settings() {
        $settings = array();

        foreach ( $this->get_post_layouts() as $args ) {
            if ( $this->check_conditions( $args['condition'] ) ) {
                $settings[] = $args['settings'];
            }
        }

        $this->current_settings = ( $settings ) ? call_user_func_array( 'array_merge', $settings ) : false;

        $default_settings = array(
            'loop_wrap'         => true,
            'loop_wrap_class'   => false,

            'entry_wrap'        => true,
            'entry_class'       => false,

            'image_size'        => 'large',
            'show_thumbnail'    => true,
            'link_thumbnail'    => true,
            'show_title'        => true,
            'link_title'        => true,

            'show_info'         => true,
            'info'              => false,
            'show_author'       => true,
            'show_date'         => true,
            'show_comment_info' => true,

            'show_content'      => true,
            'excerpt_length'    => 0,
            'excerpt_count_by'  => 'char',
            'read_more_text'    => 'Read More ...',
            'read_more_wrap'    => true,

            'show_meta'         => true,
            'meta'              => false,
            'show_categories'   => true,
            'show_tags'         => true,

            'show_comments'     => false,
            'show_author_box'   => true,

            'elements'          => array(),
        );

        $default_elements = array(
            'thumbnail'  => array( 'genesis_entry_header', 9 ),
            'title'      => array( 'genesis_entry_header', 10 ),
            'info'       => array( 'genesis_entry_header', 12 ),
            'content'    => array( 'genesis_entry_content', 10 ),
            'meta'       => array( 'genesis_entry_footer', 10 ),
            'author_box' => array( 'genesis_after_entry', 8 ),
            'comments'   => array( 'genesis_after_entry', 10 ),
        );

        $this->current_settings = wp_parse_args( $this->current_settings, $default_settings );

        $this->current_settings['elements'] = wp_parse_args( $this->current_settings['elements'], $default_elements );

        return $this->current_settings;
    }

    public function format_post() {
        if ( ! $this->current_settings ) {
            return;
        }

        extract( $this->current_settings );

        // Entry Wrap
        if ( ! $entry_wrap ) {
            remove_action( 'genesis_entry_header',  array( $this, 'open_entry_wrap' ), -1 );
        }

        // Thumbnail
        remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8);

        if ( $show_thumbnail) {
            add_action( $elements['thumbnail'][0], array( $this, 'genesis_do_post_image' ), $elements['thumbnail'][1] );
        }

        // Title
        remove_action( 'genesis_entry_header', 'genesis_do_post_title');
        if ( $show_title) {
            add_action( $elements['title'][0], 'genesis_do_post_title', $elements['title'][1] );
        }

        if ( ! $link_title ) {
            add_filter( 'genesis_link_post_title', '__return_false' );
        }

        // Info
        remove_action( 'genesis_entry_header', 'genesis_post_info', 12);
        if ( $show_info) {
            add_action( $elements['info'][0], 'genesis_post_info', $elements['info'][1] );
        }

        // Content
        remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
        if ( $show_content ) {
            add_action( $elements['content'][0], 'genesis_do_post_content', $elements['content'][1] );
        }

        // Meta
        remove_action( 'genesis_entry_footer', 'genesis_post_meta');
        if ( $show_meta) {
            add_action( $elements['meta'][0], 'genesis_post_meta', $elements['meta'][1] );
            add_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_open' ), 5 );
            add_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_close' ), 15 );
        } else {
            remove_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_open' ), 5 );
            remove_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_close' ), 15 );
        }

        // Author box
        remove_action( 'genesis_after_entry', 'genesis_do_author_box_single', 8);
        if ( $show_author_box) {
            add_action( $elements['author_box'][0], 'genesis_do_author_box_single', $elements['author_box'][1] );
        }

        // Comments
        remove_action( 'genesis_after_entry', 'genesis_get_comments_template');
        if ( $show_comments) {
            add_action( $elements['comments'][0], 'genesis_get_comments_template', $elements['comments'][1] );
        }

        add_filter( 'genesis_attr_entry', array( $this, 'add_entry_classes') );
        add_filter( 'genesis_post_info', array( $this, 'post_info') );
    }

    public function reset_post() {
        if ( ! $this->current_settings ) {
            return;
        }

        extract( $this->current_settings );

        // Entry Wrap
        if ( ! $entry_wrap ) {
            add_action( 'genesis_entry_footer',  array( $this, 'close_entry_wrap' ), 9999 );
        }

        // Thumbnail
        add_action( 'genesis_entry_content', 'genesis_do_post_image', 8);
        if ( $show_thumbnail ) {
            remove_action( $elements['thumbnail'][0], array( $this, 'genesis_do_post_image'), $elements['thumbnail'][1] );
        }

        // Title
        add_action( 'genesis_entry_header', 'genesis_do_post_title');
        if ( $show_title ) {
            remove_action( $elements['title'][0], 'genesis_do_post_title', $elements['title'][1] );
        }

        if ( ! $link_title ) {
            remove_filter( 'genesis_link_post_title', '__return_false' );
        }

        // Info
        add_action( 'genesis_entry_header', 'genesis_post_info', 12);
        if ( $show_info ) {
            remove_action( $elements['info'][0], 'genesis_post_info', $elements['info'][1] );
        }

        // Content
        if ( $show_content ) {
            remove_action( $elements['content'][0], 'genesis_do_post_content', $elements['content'][1] );
        }

        // Meta
        add_action( 'genesis_entry_footer', 'genesis_post_meta');
        if ( $show_meta ) {
            remove_action( $elements['meta'][0], 'genesis_post_meta', $elements['meta'][1] );
            remove_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_open' ), 5 );
            remove_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_close' ), 15 );
        } else {
            add_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_open' ), 5 );
            add_action( 'genesis_entry_footer', array( $this, 'entry_footer_markup_close' ), 15 );
        }

        // Author box
        add_action( 'genesis_after_entry', 'genesis_do_author_box_single', 8 );
        if ( $show_author_box ) {
            remove_action( $elements['author_box'][0], 'genesis_do_author_box_single', $elements['author_box'][1] );
        }

        // Comments
        add_action( 'genesis_after_entry', 'genesis_get_comments_template');
        if ( $show_comments ) {
            remove_action( $elements['comments'][0], 'genesis_get_comments_template', $elements['comments'][1] );
        }

        remove_filter( 'genesis_attr_entry', array( $this, 'add_entry_classes') );
        remove_filter( 'genesis_post_info', array( $this, 'post_info') );

        return $this->current_settings = false;
    }

    public function add_entry_classes( $atts) {
        if ( ! $this->current_settings ) {
            return $atts;
        }

        extract( $this->current_settings );

        $atts['class'] .= ' ' . $entry_class;

        return $atts;
    }

    public function post_info( $output) {
        if ( ! $this->current_settings ) {
            return $output;
        }

        extract( $this->current_settings );

        if ( $info) {
            $output = $info;
        } else {
            $output = array();

            if ( $show_date ) {
                $output[] = '[post_date]';
            }

            if ( $show_author ) {
                $output[] = __( 'by', 'genesis') . ' [post_author_posts_link]';
            }

            if ( $show_comment_info ) {
                $output[] = '[post_comments]';
            }

            $output[] = '[post_edit]';

            $output = implode( ' ', $output );
        }

        return $output;
    }

    public function post_meta( $output ) {
        if ( ! $this->current_settings ) {
            return $output;
        }

        extract( $this->current_settings );

        if ( $meta ) {
            $output = $meta;
        } else {
            $output = array();

            if ( $show_categories ) {
                $output[] = '[post_categories]';
            }

            if ( $show_tags ) {
                $output[] = '[post_tags]';
            }

            $output = implode( ' ', $output );
        }

        return $output;
    }

    public function open_loop_wrap() {
        printf( '<div %s>', genesis_attr( 'loop' ) );
    }

    public function close_loop_wrap() {
        echo '</div>';
    }

    public function attr_loop( $atts ) {
        if ( ! $this->current_settings ) {
            return $atts;
        }

        extract( $this->current_settings );

        if ( $loop_wrap ) {
            $atts['class'] .= ' wrap';
        }

        if ( $loop_wrap_class ) {
            $atts['class'] .= ' ' . $loop_wrap_class;
        }

        return $atts;
    }

    public function get_image_sizes() {
        global $_wp_additional_image_sizes;

        $sizes = get_intermediate_image_sizes();

        foreach( $sizes as $size ) {
            if ( in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
                $width  = get_option( $size . '_size_w' );
                $height = get_option( $size . '_size_h' );
                $crop   = (bool) get_option( $size . '_crop' );
            } elseif ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
                $width  = $_wp_additional_image_sizes[ $size ]['width'];
                $height = $_wp_additional_image_sizes[ $size ]['height'];
                $crop   = $_wp_additional_image_sizes[ $size ]['crop'];
            }

            $ratio = $this->ratio( intval( $width ), intval( $height ) );

            $sizes[ $size ] = array(
                'width'  => $width,
                'height' => $height,
                'ratio'  => $ratio,
                'crop'   => $crop,
            );
        }

        return $this->image_sizes = $sizes;
    }

    public function gcd( $a, $b ) {
        $a = bcmul( ( $a < 0 ) ? "-1" : "1", $a );
        $b = bcmul( ( $b < 0 ) ? "-1" : "1", $b );
        if ( bccomp( $a, $b ) > 0 ) {
            $w = $a;
            $a = $b;
            $b = $w;
        }

        $gcd = $a;
        while ( $b != 0 ) {
            $c = $b;
            $b = bcmod( $gcd, $b );
            $gcd = $c;
        }

        return $gcd;
    }

    public function ratio( $a, $b ) {
        $var = $this->gcd( $a, $b );
        return ( $a / $var ) . ':' . ( $b / $var );
    }

    public function attr_entry_thumbnail( $atts ) {
        if ( ! $this->current_settings ) {
            return $atts;
        }

        extract( $this->current_settings );

        $sizes = $this->get_image_sizes();
        $ratio = explode( ':', $sizes[ $image_size ]['ratio'] );

        if ( $sizes[ $image_size ]['crop'] ) {
            $atts['class'] .= sprintf( ' ratio-%s-%s', trim( $ratio[0] ), trim( $ratio[1] ) );
        }

        return $atts;
    }

    public function attr_entry_image( $atts ) {
        $atts['class'] = trim( str_replace( genesis_get_option( 'image_alignment' ), '', $atts['class'] ) );
        return $atts;
    }

    public function genesis_do_post_image() {
        global $_wp_additional_image_sizes;

        if ( ! $this->current_settings ) {
            return;
        }

        extract( $this->current_settings );

        $size = genesis_get_option( 'image_size' );

        if ( $image_size ) {
            $size = $image_size;
        }

        $size = ( is_array( $size ) ) ? 'custom' : $size;

        $img_args = array(
            'format'   => 'html',
            'size'     => $size,
            'context'  => is_singular() ? 'single' : 'archive',
            'attr'     => genesis_parse_attr( 'entry-image' ),
            'fallback' => false,
        );

        $img = genesis_get_image( $img_args );

        if ( array_key_exists( $size, $_wp_additional_image_sizes ) ) {
            $width = 'width:' . $_wp_additional_image_sizes[ $size ]['width'] . 'px;';
        } else {
            $width = '';
        }

        $background_img = sprintf( ' background-image:url(%s);', wp_get_attachment_image_src( genesis_get_image_id(), 'full' )[0] );
        $style = sprintf( ' style="%s"', $width );

        $pattern = '/(<img[^>]*class=\"([^>]*?)\"[^>]*>)/i';
        $replacement = '<div class="content-image $2 size-' . $size . '"' . $style . '>$1<div class="blur-backdrop" style="' . $background_img . '""></div></div>';
        $img = preg_replace( $pattern, $replacement, $img );

        if ( ! empty( $img ) ) {
            do_action( 'genesis_custom_before_entry_thumbnail_wrap' );

            printf( '<div %s>', genesis_attr( 'entry-thumbnail-wrap' ) );

            do_action( 'genesis_custom_before_entry_thumbnail' );

            if ( is_singular() || ! $link_thumbnail ) {
                echo $img;
            } else {
                printf( '<a %s>%s</a>', genesis_attr( 'entry-thumbnail-link', array(
                    'href' => get_permalink(),
                    'title' => the_title_attribute( 'echo=0' ),
                    'class' => "entry-thumbnail-link",
                ) ), $img );
            }

            do_action( 'genesis_custom_after_entry_thumbnail' );

            echo '</div>';

            do_action( 'genesis_custom_after_entry_thumbnail_wrap' );
        }
    }

    public function wrap_content_more_link( $output ) {

        if ( ! $this->current_settings ) {
            return $output;
        }

        extract( $this->current_settings );

        $text = 'Read More ...';
        $wrap = true;

        if ( $read_more_text ) {
            $text = $read_more_text;
            $wrap = $read_more_wrap;
        } else {
            return '';
        }

        if ( ! $text ) {
            return '';
        }

        $output = sprintf( '<a class="more-link" href="%s">%s</a>', get_permalink(), $text );

        if ( $wrap ) {
            $output = sprintf( '<p>%s</p>', $output );
        }

        return $output;
    }

    public function content_archive_limit( $limit ) {

        if ( ! $this->current_settings ) {
            return $limit;
        }

        extract( $this->current_settings );

        if ( $excerpt_length ) {
            return $excerpt_length;
        }

        return $limit;
    }

    public function open_entry_wrap() {
        genesis_structural_wrap( 'entry', 'open' );
    }

    public function close_entry_wrap() {
        genesis_structural_wrap( 'entry', 'close' );
    }

    public function entry_footer_markup_open() {
        printf( '<footer %s>', genesis_attr( 'entry-footer' ) );
    }

    public function entry_footer_markup_close() {
        echo '</footer>';
    }

    public function wrap_content_images( $content ) {
        $pattern = '/(<img[^>]*class=\"([^>]*?)\"[^>]*width=\"([^>]*?)\"[^>]*>)/i';
        $replacement = '<div class="content-image $2" style="width:$3px;">$1</div>';
        $content = preg_replace( $pattern, $replacement, $content );
        return $content;
    }
}
