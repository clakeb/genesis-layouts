<?php

if ( function_exists( 'register_field_group' ) ) :

    $page_layout_settings = array(
        array(
            'key'               => 'field_3JCM48XKp6i1HUR',
            'label'             => 'Configure Page Layout Settings?',
            'name'              => 'enable_page_layout_settings',
            'prefix'            => '',
            'type'              => 'true_false',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => 0,
            'wrapper'           => array(
                'width' => '',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 0,
        ),
        array(
            'key'               => 'field_k340Ih6zTp9Abxv',
            'label'             => 'Show Site Title',
            'name'              => 'show_site_title',
            'prefix'            => '',
            'type'              => 'true_false',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '25',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 1,
        ),
        array(
            'key'               => 'field_XMu1RTAN62zqlYm',
            'label'             => 'Show Site Description',
            'name'              => 'show_site_description',
            'prefix'            => '',
            'type'              => 'true_false',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '25',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 1,
        ),
        array(
            'key'               => 'field_dZ9eJMtIuCpkWz1',
            'label'             => 'Show Header Widget Area',
            'name'              => 'show_header_widget_area',
            'prefix'            => '',
            'type'              => 'true_false',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '25',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 1,
        ),
        array(
            'key'               => 'field_3A0cGPC6OImusaV',
            'label'             => 'Show Primary Nav',
            'name'              => 'show_primary_nav',
            'prefix'            => '',
            'type'              => 'true_false',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '25',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 1,
        ),
        array(
            'key'               => 'field_heQsB4awEPiJRq0',
            'label'             => 'Show Secondary Nav',
            'name'              => 'show_secondary_nav',
            'prefix'            => '',
            'type'              => 'true_false',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '25',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 1,
        ),
        array(
            'key'               => 'field_X1sd4Pu8q6ztm9H',
            'label'             => 'Show Page Title',
            'name'              => 'show_page_title',
            'prefix'            => '',
            'type'              => 'true_false',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '25',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 1,
        ),
        array(
            'key'               => 'field_JYRdonIfF4i1aHW',
            'label'             => 'Show Page Content',
            'name'              => 'show_content',
            'prefix'            => '',
            'type'              => 'true_false',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '25',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 1,
        ),
        array(
            'key'               => 'field_8reRKU9Bl0ISJ5w',
            'label'             => 'Show Footer',
            'name'              => 'show_footer',
            'prefix'            => '',
            'type'              => 'true_false',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '25',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 1,
        ),
        array(
            'key'               => 'field_EbByYhfszP1AxLe',
            'label'             => 'Body Background Image',
            'name'              => 'body_bg_image_src',
            'prefix'            => '',
            'type'              => 'image',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '33.3333',
                'class' => '',
                'id'    => '',
            ),
            'return_format' => 'url',
            'preview_size'  => 'thumbnail',
            'library'       => 'all',
        ),
        array(
            'key'               => 'field_Dfe09HuRwdB2Xlc',
            'label'             => 'Inner Background Image',
            'name'              => 'inner_bg_image_src',
            'prefix'            => '',
            'type'              => 'image',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field' => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value' => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '33.3334',
                'class' => '',
                'id' => '',
            ),
            'return_format' => 'url',
            'preview_size'  => 'thumbnail',
            'library'       => 'all',
        ),
        array(
            'key'               => 'field_yMT2aDQpfLerit7',
            'label'             => 'Footer Widgets',
            'name'              => 'footer_widgets',
            'prefix'            => '',
            'type'              => 'number',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '33.3333',
                'class' => '',
                'id'    => '',
            ),
            'default_value' => get_theme_support( 'genesis-footer-widgets' )[0],
            'placeholder'   => '',
            'prepend'       => '',
            'append'        => '',
            'min'           => 0,
            'max'           => 12,
            'step'          => 1,
            'readonly'      => 0,
            'disabled'      => 0,
        ),
        array(
            'key'               => 'field_x9oplDkPW81KcQe',
            'label'             => 'Footer Credits',
            'name'              => 'footer_creds',
            'prefix'            => '',
            'type'              => 'wysiwyg',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'    => 'field_3JCM48XKp6i1HUR',
                        'operator' => '==',
                        'value'    => '1',
                    ),
                ),
            ),
            'wrapper' => array(
                'width' => '100',
                'class' => '',
                'id'    => '',
            ),
            'default_value' => '',
            'tabs'          => 'all',
            'toolbar'       => 'full',
            'media_upload'  => 1,
        ),
    );

    register_field_group( array(
        'key'      => 'group_lhmox254eREBFYK',
        'title'    => 'Page Layout',
        'fields'   => $page_layout_settings,
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'page',
                ),
            ),
        ),
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => '',
    ) );

endif;