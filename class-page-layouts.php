<?php

namespace Genesis;

class Page_Layouts {

    public static $instance = null;

    public $current_settings = false;
    public $max_footer_widgets = 3;
    public $additional_sidebars = array();
    public $page_section_queue = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            return self::$instance = new self();
        } else {
            return self::$instance;
        }
    }

    private function __construct() {
        if ( function_exists( 'add_action' ) )
            $this->init();
    }

    public function init() {
        // Init Settings
        $this->init_settings();
        add_action( 'after_setup_theme', array( $this, 'init_settings' ) );
        add_action( 'genesis_doctype', array( $this, 'init_settings' ), -1 );

        // Format & Reset Page
        add_action( 'genesis_doctype', array( $this, 'format_page' ), 0 );

        // Add Custom Post Layout For This Page
        add_filter( 'custom_post_layouts', array( $this, 'custom_post_layouts' ), 15 );

        // Content Sidebar Structural Wraps
        add_action( 'genesis_before_content',  array( $this, 'open_content_sidebar_wrap' ), -1 );
        add_action( 'genesis_after_content',  array( $this, 'close_content_sidebar_wrap' ), 9999 );

        // Footer Credits
        add_filter( 'genesis_footer_creds_text', array( $this, 'footer_creds_text' ) );
    }

    public function get_page_layouts() {
        $layouts = apply_filters( 'custom_page_layouts', array(
            array(
                'settings' => array(),
                'condition' => array(
                    array(
                        array(
                            'var'      => 'is_main_query',
                            'var_type' => 'function',
                            'params'   => array(),
                            'operator' => '==',
                            'value'    => true,
                        ),
                        array(
                            'var'      => 'is_front_page',
                            'var_type' => 'function',
                            'params'   => array(),
                            'operator' => '==',
                            'value'    => true,
                        ),
                        array(
                            'var'      => 'is_home',
                            'var_type' => 'function',
                            'params'   => array(),
                            'operator' => '!=',
                            'value'    => true,
                        ),
                    ),
                    array(
                        array(
                            'var'      => 'is_main_query',
                            'var_type' => 'function',
                            'params'   => array(),
                            'operator' => '==',
                            'value'    => true,
                        ),
                        array(
                            'var'      => 'is_page',
                            'var_type' => 'function',
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
            $var = ( $post ) ? $post->$var : false;
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

        foreach ( $this->get_page_layouts() as $args ) {
            if ( array_key_exists( 'footer_widgets', $args['settings'] ) ) {
                if ( is_int( $args['settings']['footer_widgets'] ) && $this->max_footer_widgets < $args['settings']['footer_widgets'] ) {
                    $this->max_footer_widgets = $args['settings']['footer_widgets'];
                }
            }

            if ( array_key_exists( 'primary_sidebar', $args['settings'] ) ) {
                if ( ! is_bool( $args['settings']['primary_sidebar'] ) ) {
                    if ( $args['settings']['primary_sidebar'] ) {
                        genesis_register_sidebar( array(
                            'id' => $args['settings']['primary_sidebar'],
                            'name' => ucwords( preg_replace( '/[-_]+/', ' ', $args['settings']['primary_sidebar'] ) ),
                            'description' => 'This is a custom primary sidebar created by the Genesis custom page formatter',
                        ) );
                    }
                }
            }

            if ( array_key_exists( 'secondary_sidebar', $args['settings'] ) ) {
                if ( ! is_bool( $args['settings']['secondary_sidebar'] ) ) {
                    if ( ! is_active_sidebar( $args['settings']['secondary_sidebar'] ) ) {
                        genesis_register_sidebar( array(
                            'id' => $args['settings']['secondary_sidebar'],
                            'name' => ucwords( preg_replace( '/[-_]+/', ' ', $args['settings']['secondary_sidebar'] ) ),
                            'description' => 'This is a custom secondary sidebar created by the Genesis custom page formatter',
                        ) );
                    }
                }
            }

            if ( $this->check_conditions( $args['condition'] ) ) {
                $settings[] = $args['settings'];
            }
        }

        $this->current_settings = ( $settings ) ? call_user_func_array( 'array_merge', $settings ) : false;

        $default_settings = array(
            'body_class'              => false,
            'body_bg_image_src'       => false,
            'site_logo_src'           => false,
            'show_site_title'         => true,
            'show_site_description'   => true,
            'show_primary_nav'        => true,
            'show_secondary_nav'      => true,
            'show_header_widget_area' => true,
            'page_layout'             => false,
            'inner_bg_image_src'      => false,
            'page_sections'           => array(),
            'show_page_title'         => true,
            'show_content'            => true,
            'primary_sidebar'         => true,
            'secondary_sidebar'       => true,
            'footer_widgets'          => 3,
            'show_footer'             => true,
            'footer_creds'            => true,
            'elements'                => array(),
            'wraps'                   => array(
                'header',
                'nav',
                'subnav',
                'site-inner',
                'content-sidebar',
                'entry',
                'footer-widgets',
                'footer',
                'page-section',
            ),
        );

        $default_elements = array(
            'primary_nav' => array( 'genesis_after_header', 10 ),
            'secondary_nav' => array( 'genesis_after_header', 10 ),
        );

        $this->current_settings = wp_parse_args( $this->current_settings, $default_settings );

        $this->current_settings['elements'] = wp_parse_args( $this->current_settings['elements'], $default_elements );

        // Theme Support
        add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

        if ( $this->current_settings['wraps'] && is_array( $this->current_settings['wraps'] ) ) {
            add_theme_support( 'genesis-structural-wraps', $this->current_settings['wraps'] );
        }

        add_theme_support( 'genesis-footer-widgets', $this->max_footer_widgets );

        return $this->current_settings;
    }

    public function format_page() {
        if ( ! $this->current_settings ) {
            return;
        }

        extract( $this->current_settings );

        if ( $body_class ) {
            add_filter( 'body_class', array( $this, 'body_class' ) );
        }

        if ( $body_bg_image_src ) {
            add_filter( 'genesis_attr_body', array( $this, 'body_attr' ) );
        }

        if ( $site_logo_src ) {
            add_filter( 'genesis_seo_title', array( $this, 'seo_title' ), 10, 3 );
        }

        if ( $show_site_title ) {
            add_action( 'genesis_site_title', 'genesis_seo_site_title' );
        } else {
            remove_action( 'genesis_site_title', 'genesis_seo_site_title' );
        }

        if ( $show_site_description ) {
            add_action( 'genesis_site_description', 'genesis_seo_site_description' );
        } else {
            remove_action( 'genesis_site_description', 'genesis_seo_site_description' );
        }

        remove_action( 'genesis_after_header', 'genesis_do_nav' );
        if ( $show_primary_nav ) {
            add_action( $elements['primary_nav'][0], 'genesis_do_nav', $elements['primary_nav'][1] );
        }

        remove_action( 'genesis_after_header', 'genesis_do_subnav' );
        if ( $show_primary_nav ) {
            add_action( $elements['secondary_nav'][0], 'genesis_do_subnav', $elements['secondary_nav'][1] );
        }

        if ( ! $show_header_widget_area ) {
            unregister_sidebar( 'header-right' );
        }

        if ( $page_layout ) {
            add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_' . str_replace( '-', '_', $page_layout ) );
        }

        if ( $inner_bg_image_src ) {
            add_filter( 'genesis_attr_site-inner', array( $this, 'site_inner_attr' ) );
        }

        remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
        if ( $primary_sidebar ) {
            add_action( 'genesis_sidebar', array( $this, 'do_sidebar' ) );
        }

        remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );
        if ( $primary_sidebar ) {
            add_action( 'genesis_sidebar_alt', array( $this, 'do_sidebar_alt' ) );
        }

        if ( $footer_widgets ) {
            add_theme_support( 'genesis-footer-widgets', ( is_bool( $footer_widgets ) ) ? 3 : $footer_widgets );
        }

        if ( ! $show_footer ) {
            remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
            remove_action( 'genesis_footer', 'genesis_do_footer' );
            remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );
        }

        if ( $page_sections ) {
            $counter = 0;
            foreach ( $page_sections as $i => $page_section ) {
                if ( ! isset( $page_section['action'] ) ) {
                    continue;
                }

                $hook = (array) $page_section['action'];

                $this->page_section_queue[ $hook[0] ][ $counter ] = $page_section;

                add_action( $hook[0], array( $this, 'register_page_section' ), isset( $hook[1] ) ? $hook[1] : 10 );
                $counter++;
            }
        }
    }

    public function body_class( $classes ) {
        if ( ! $this->current_settings ) {
            return;
        }

        extract( $this->current_settings );

        if ( $footer_widgets && isset( $footer_widgets ) && is_numeric( $footer_widgets ) ) {
            $classes[] = 'footer-widgets-' . $footer_widgets;
        }

        if ( $body_class ) {
            $classes[] = $body_class;
        }

        return $classes;
    }

    public function custom_post_layouts( $layouts ) {

        if ( ! $this->current_settings ) {
            return $layouts;
        }

        extract( $this->current_settings );

        $layouts[] = array(
            'settings' => array(
                'show_title' => $show_page_title,
                'show_content' => $show_content,
            ),
            'condition' => array(
                array(
                    array(
                        'var'      => 'is_main_query',
                        'var_type' => 'function',
                        'params'   => array(),
                        'operator' => '==',
                        'value'    => true,
                    ),
                ),
            ),
        );

        return $layouts;
    }

    public function body_attr( $atts ) {
        if ( ! $this->current_settings ) {
            return $output;
        }

        extract( $this->current_settings );

        $img_src = ( $body_bg_image_src ) ? $body_bg_image_src : false;

        if ( $img_src ) {
            if ( ! array_key_exists( 'style', $atts ) ) {
                $atts['style'] = '';
            }

            $atts['style'] .= sprintf( 'background-image:url(%s);', $img_src[0] );
        }

        return $atts;
    }

    public function site_inner_attr( $atts ) {
        if ( ! $this->current_settings ) {
            return $output;
        }

        extract( $this->current_settings );

        $img_src = ( $inner_bg_image_src ) ? $inner_bg_image_src : false;

        if ( $img_src ) {
            if ( ! array_key_exists( 'style', $atts ) ) {
                $atts['style'] = '';
            }

            $atts['style'] .= sprintf( 'background-image:url(%s);', $img_src[0] );
        }

        return $atts;
    }

    public function seo_title( $title, $insde, $wrap ) {
        if ( ! $this->current_settings ) {
            return $output;
        }

        extract( $this->current_settings );

        $logo = ( $site_logo_src ) ? sprintf( '<img class="site-logo" src="%s">', $site_logo_src ) : false;

        if ( $logo ) {
            $inside = sprintf( '<a href="%s" title="%s">%s</a>', trailingslashit( home_url() ), esc_attr( get_bloginfo( 'name' ) ), $logo );

            $title  = genesis_html5() ? sprintf( "<{$wrap} %s>", genesis_attr( 'site-title' ) ) : sprintf( '<%s id="title">%s</%s>', $wrap, $inside, $wrap );
            $title .= genesis_html5() ? "{$inside}</{$wrap}>" : '';
        }

        return $title;
    }

    public function footer_creds_text( $output ) {
        if ( ! $this->current_settings ) {
            return $output;
        }

        extract( $this->current_settings );

        if ( ! $footer_creds ) {
            return '';
        } else {
            if ( is_string( $footer_creds ) ) {
                return $footer_creds;
            }
        }

        return $output;
    }

    public function do_sidebar() {
        if ( ! $this->current_settings ) {
            return;
        }

        extract( $this->current_settings );

        $sidebar_id = ( is_string( $primary_sidebar ) ) ? $primary_sidebar : 'sidebar';

        $sidebar_name = ucwords( preg_replace( '/[-_]+/', ' ', $sidebar_id ) );

        if ( ! dynamic_sidebar( $sidebar_id ) && current_user_can( 'edit_theme_options' )  ) {
            genesis_default_widget_area_content( __( $sidebar_name . ' Widget Area', 'genesis' ) );
        }
    }

    public function do_sidebar_alt() {
        if ( ! $this->current_settings ) {
            return;
        }

        extract( $this->current_settings );

        $sidebar_id = ( is_string( $secondary_sidebar ) ) ? $secondary_sidebar : 'sidebar-alt';

        $sidebar_name = ucwords( preg_replace( '/[-_]+/', ' ', $sidebar_id ) );

        if ( ! dynamic_sidebar( $sidebar_id ) && current_user_can( 'edit_theme_options' )  ) {
            genesis_default_widget_area_content( __( $sidebar_name . ' Widget Area', 'genesis' ) );
        }
    }

    public function open_content_sidebar_wrap() {
        genesis_structural_wrap( 'content-sidebar', 'open' );
    }

    public function close_content_sidebar_wrap() {
        genesis_structural_wrap( 'content-sidebar', 'close' );
    }

    public function get_background( $args ) {
        $defaults = array(
            'image'           => '',
            'video'           => '',
            'overlay'         => '#ffffff',
            'overlay_opacity' => '0',
        );

        $args = wp_parse_args( $args, $defaults );;

        extract( $args );

        $output = '';

        if ( $video || $image ) {
            if ( $image && ! $video ) {

                $output .= sprintf( '<img src="%s">', $image );
            } else if ( $video ) {
                $basename   = basename( $video );
                $path       = preg_replace( '/\\.[^.\\s]{3,4}$/', '', $video );
                $file_types = array( 'mp4', 'webm', 'ogg' );

                foreach ( $file_types as $type ) {
                    $output .= sprintf( '<source src="%s.%s" type="video/%s">', $path, $type, $type );
                }

                $output .= 'Sorry, your browser does not support the video tag.';

                $output = sprintf( '<video loop muted autoplay>%s</video>', $output );
            }
        }

        $overlay = ( $overlay ) ? sprintf( '<span %s></span>', genesis_attr( 'section-overlay', array(
            'style' => sprintf( 'background-color:%s;opacity:%s;', $overlay, $overlay_opacity ),
        ) ) ) : '';

        return sprintf( '<div %s>%s%s</div>', genesis_attr( 'section-background' ), $output, $overlay  );
    }

    public function get_page_section( $args = array(), $content = '' ) {
        $passed = $args;
        $output = '';

        $defaults = array(
            'font_size'        => 1,
            'font_background'  => '#00ff00',
            'content_align'    => array( 'center', 'middle' ),
            'background'       => array(),
            'atts'             => array(),
        );

        $args = wp_parse_args( $args, $defaults );

        $atts = array_diff_key( $passed, $args );

        foreach ( $atts as $key => $att ) {
            $atts[ $key ] = $args[ $att ];
        }

        $args['background'] = $this->get_background( $args['background'] );

        if ( ! empty( $atts ) ) {
            $args['atts'] = $atts;
        }

        extract( $args );

        if ( ! array_key_exists( 'class', $atts ) ) {
            $atts['class'] = '';
        }

        $atts['class'] .= ' page-section';

        if ( $font_size ) {
            $atts['class'] .= sprintf( ' font-%sx', str_replace( '.', '-', $font_size ) );
        }

        if ( $background || $content ) {
            $content_atts = array(
                'class' => 'section-content',
            );

            if ( $content_align ) {
                $content_atts['class'] .= ' align-' . implode( '-', $content_align );
            }

            $pattern      = '<div %s><div class="table-wrap"><div class="wrap">%s</div></div></div>';

            $content_atts = genesis_attr( 'section-content', $content_atts );

            $content      = ( $content ) ? sprintf( $pattern, $content_atts, $content ) : '';

            $atts         = genesis_attr( 'page-section', $atts );

            $output       = sprintf( '<div %s>%s%s</div>', $atts, $background, $content );
        }

        return $output;
    }

    public function do_page_section( $args = array(), $content = '' ) {
        echo $this->get_page_section( $args, $content );
    }

    public function register_page_section() {
        static $call_count = 0;

        $hook = current_filter();

        if ( ! $hook ) {
            return;
        }

        $page_section = $this->page_section_queue[ $hook ][ $call_count ];
        unset( $page_section['action'] );

        $content = isset( $page_section['content'] ) ? $page_section['content'] : '';
        unset( $page_section['content'] );

        $this->do_page_section( $page_section, $content );
        $call_count++;
    }
}
