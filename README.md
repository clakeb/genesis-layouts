# What is Genesis Layouts?

I have been using Genesis for a long time and the more I progress in my programming abilities, I have found that it is difficult to maintain clean and orderly layouts within complex WordPress sites containing many design styles and custom post types. That is where Genesis Layouts comes in. With Genesis Layouts, you can use real SEO-friendly markup produced by the developers from StudioPress in any layout you need.

# Installation

## Via Composer
Run this inside your Genesis-based theme or plugin directory.

```
composer require tatemz/genesis-layouts dev-master
```

#How to Use Genesis Layouts

# Features
Genesis Layouts is divided into two parts, post (or rather post-type) layouts and page layouts.

* Post layouts can best be described as any post type that is output as markup **inside** a loop. Naturally, this can be a `page` post type, a `post` post type, or any other WordPress custom post type.
* Page layouts handle any markup **outside** of a loop. One caveat is that a few page layout settings can still effect the loop of a `page` post type within an `is_singular()` view.

* A "layout" has two primary features: an array of `settings` and a `condition` or rule.
* Each group of `settings` within a layout can be specified to only apply  when a certain `condition` or rule is met.
* Layouts are built and arranged in order of priority with the least prioritized occuring first in an array and the highest prioritized occuring last in an array.
* When a condition is met, settings from one layout may be overridden when a second condition is met in higher priority.

*Developer's Note: This package is meant to be a markup-first library. As such, it will only produce and rearrange markup. However, it will not customize or build out your stylesheets and user interfaces as they will inevitably vary from project to project.*

# Usage
To build a layout, simply filter a passed array via the two filter hooks: `custom_page_layouts` or `custom_post_layouts`.

```
add_filter( 'custom_page_layouts', 'build_custom_page_layouts' );
function build_custom_page_layouts( $page_layouts ) {
    $page_layouts = array(
        'settings' => $settings, // An array of settings
        'condition' => $condition, // A condition rule-set/array
    );

    return $page_layouts;
}

add_filter( 'custom_post_layouts', 'build_custom_post_layouts' );
function build_custom_post_layouts( $post_layouts ) {
    $post_layouts = array(
        'settings' => $settings, // An array of settings
        'condition' => $condition, // A condition rule-set/array
    );

    return $post_layouts;
}
```

# Conditions
Conditions are how Genesis Layouts applies settings. Multiple conditions can be applied to chain rules via `AND` / `OR` relationships. A **single** condition consist of 5 properties:

1. `var` | (*default*: `''`)
2. `var_type` | (*default*: `'function'`)
3. `params` | (*default*: `array()`)
4. `operator` | (*default*: `==`)
5. `value` | (*default*: `true`)


```
array(
    'var'      => 'some_var',
    'var_type' => 'function',
    'params'   => array(),
    'operator' => '==',
    'value'    => true,
);
```

Conditions are combined to chain rule-sets:

```
$condition = array(
    array( // The two arrays inside this array are AND conditions while this entire array is a single OR condition
        array(
            'var'      => 'some_first_ruleset_var',
            'var_type' => 'function',
            'params'   => array(),
            'operator' => '==',
            'value'    => true,
        ),
        // AND
        array(
            'var'      => 'some_other_first_ruleset_var',
            'var_type' => 'function',
            'params'   => array(),
            'operator' => '==',
            'value'    => true,
        ),
    ),

    // OR

    array( // The two arrays inside this array are AND conditions while this entire array is a single OR condition
        array(
            'var'      => 'some_second_ruleset_var',
            'var_type' => 'function',
            'params'   => array(),
            'operator' => '==',
            'value'    => true,
        ),
        // AND
        array(
            'var'      => 'some_other_second_ruleset_var',
            'var_type' => 'function',
            'params'   => array(),
            'operator' => '==',
            'value'    => true,
        ),
    ),
);
```

##### var
*default*: `''`

The `var` of a condition can be one of three things:

1. A function name or object-method array. (`string` | `array`)
2. A variable's value (any)
3. A `$post` property from the current WordPress `$post` global variable. (`string`)

##### var_type
*default*: `'function'`

The `var_type` of a condition can be one of three things:

1. `function` - Indicates that the function or method `var` should run and the return value be checked.
2. `variable` - Indicates that the real variable value of `var` should be checked.
3. `post_property` - Indicates that the `$post` property `var` should be checked

##### params
*default*: `array()`

The `params` array of a condition is only used when `var_type` is equal to `function`. Any unlimited array of `params` will be passed to the indicated `var` function or method when the condition is checked.

*Note: `call_user_func_array()` is the method by which `params` are passed to `var`.*

##### operator
*default*: `==`

The `operator` of a condition is simply the type of check to run. There are only two possible string values:

1. `'=='` (Equal to)
2. `'!='` (Not equal to)

##### value
*default*: `true`

The `value` of a condition is the real value to compare a `var` against. Which ever `operator` is used will determine whether the check against this `value` will pass as true or false.

# Settings
Settings are the meat of layouts. They configure the states of various pieces of markup in either a `page`, a `post`, or a custom post type.

## Post Layout Settings
These settings are only applied within loops.

#####loop_wrap
*default: `true`*

This setting will add the `.wrap` class to the `.loop` element that wraps a loop of posts or post types. `.wrap`s are helpful when adding negative margins in typical grid systems (similar to `.row` elements).

Possible values are: `true` | `false`

#####loop_wrap_class
*default: `false`*

This setting will add any custom classes to the `.loop` element that wraps a loop of posts or post types.

Possible values are: `true` | any **string** of classes seperated by spaces

#####entry_wrap
*default: `true`*

This setting wrap the *inner* contents of the `.entry` element if set to `true`.

Possible values are: `true` | `false`

#####entry_class
*default: `false`*

This setting will add any custom classes to the `.entry` element that contains a single post or post type.

Possible values are: `true` | any **string** of classes seperated by spaces

#####image_size
*default: `'large'`*

This setting will set the size of the featured image of a post.

Possible values are: `'thumbnail'` | `'medium'` | `'large'` | `'full'` | any *string* of custom WordPress image sizes

#####show_thumbnail
*default: `true`*

This setting will activate whether to show or hide the featured image (`.entry-thumbnail`).

Possible values are: `true` | `false`

#####link_thumbnail
*default: `true`*

This setting will activate whether or not to link the featured image to the singular view of the post or post type.

Possible values are: `true` | `false`

#####show_title
*default: `true`*

This setting will activate whether to show or hide the post title (`.entry-title`) element of a post.

Possible values are: `true` | `false`

#####link_title
*default: `true`*

This setting will activate whether or not to link the post title ('.entry-title') to the singular view of the post or post type.

Possible values are: `true` | `false`

#####show_info
*default: `true`*

This setting will activate whether to show or hide the post info (`.entry-header .entry-meta`) element of a post.

Possible values are: `true` | `false`

#####info
*default: `false`*

This setting can be set to customize the output of the post info (`.entry-header .entry-meta`) element of a post.

Possible values are: `true` | any **string** of output or WordPress shortcodes

#####show_author
*default: `true`*

This setting will activate whether to show or hide the post info author name (`.entry-meta .entry-author-name`) element of a post.

Possible values are: `true` | `false`

#####show_date
*default: `true`*

This setting will activate whether to show or hide the post info date (`.entry-meta .entry-time`) element of a post.

Possible values are: `true` | `false`

#####show_comment_info
*default: `true`*

This setting will activate whether to show or hide the post info comment count (`.entry-meta .entry-comments-link`) element of a post.

Possible values are: `true` | `false`

#####show_content
*default: `true`*

This setting will activate whether to show or hide the post content (`.entry-content`) element of a post.

Possible values are: `true` | `false`

#####excerpt_length
*default: `0`*

This setting will number of characters or words to limit the content by. (*Note: Setting to `0` will show the full amount of content.*)

Possible values are: any string of text

#####excerpt_count_by
*default: `'char'`*

#####read_more_text
*default: `'Read More ...'`*

This setting will override the text of the read more link (`.more-link`) of a post. *(Note: setting to false will deactivate the read more link.)*

Possible values are: any string of text

#####read_more_wrap
*default: `true`*

This setting will activate whether to wrap the read more link (`.more-link`) in a `<p></p>` tag to bump it to the next line.

Possible values are: `true` | `false`

#####show_meta
*default: `true`*

This setting will activate whether to show or hide the post meta (`.entry-footer .entry-meta`) element of a post.

Possible values are: `true` | `false`

#####meta
*default: `false`*

This setting can be set to customize the output of the post meta (`.entry-footer .entry-meta`) element of a post.

Possible values are: `true` | any **string** of output or WordPress shortcodes

#####show_categories
*default: `true`*

This setting will activate whether to show or hide the post meta category list (`.entry-meta .entry-categories`) element of a post.

Possible values are: `true` | `false`

#####show_tags
*default: `true`*

This setting will activate whether to show or hide the post meta tag list (`.entry-meta .entry-tags`) element of a post.

Possible values are: `true` | `false`

#####show_comments
*default: `false`*

This setting will activate whether to show or hide the comment list (`.entry-comments`) element of a post.

Possible values are: `true` | `false`

#####show_author_box
*default: `true`*

This setting will activate whether to show or hide the comment list (`.author-box`) element of a post.

Possible values are: `true` | `false`

#####elements
*default:*
```
array(
    'thumbnail'  => array( 'genesis_entry_header', 9 ),
    'title'      => array( 'genesis_entry_header', 10 ),
    'info'       => array( 'genesis_entry_header', 12 ),
    'content'    => array( 'genesis_entry_content', 10 ),
    'meta'       => array( 'genesis_entry_footer', 10 ),
    'author_box' => array( 'genesis_after_entry', 8 ),
    'comments'   => array( 'genesis_after_entry', 10 ),
)
```

This setting will call various genesis functions at customizable action hooks (with customizable priorities).

Possible values are: an array of element string id's and their `array()` values of hook-priority callbacks.

## Page Layout Settings

#####body_class
*default: `false`*

This setting will add any custom classes to the `body` element of a page.

Possible values are: `true` | any **string** of classes seperated by spaces

#####body_bg_image_src
*default: `false`*

This setting will add a `background-image` css styles to a page's `body`.

Possible values are: `false` | any valid url **string** to an image

#####site_logo_src
*default: `false`*

This setting will add a logo (`.site-logo`) within the site title (`.site-title`) element of a page.

Possible values are: `false` | any valid url **string** to an image

#####show_site_title
*default: `true`*

This setting will activate whether to show or hide the site title (`.site-title`) element of a page.

Possible values are: `true` | `false`

#####show_site_description
*default: `true`*

This setting will activate whether to show or hide the site description (`.site-description`) element of a page.

Possible values are: `true` | `false`

#####show_primary_nav
*default: `true`*

This setting will activate whether to show or hide the primary navigation (`.nav-primary`) element of a page.

Possible values are: `true` | `false`

#####show_secondary_nav
*default: `true`*

This setting will activate whether to show or hide the secondary navigation (`.nav-secondary`) element of a page.

Possible values are: `true` | `false`

#####show_header_widget_are
*default: `true`*

This setting will activate whether to show or hide the header right widget area (`.header-widget-area`) element of a page.

Possible values are: `true` | `false`

#####page_layout
*default: `false`*

This setting will override the page layout of a page.

Possible values are: `'full-width-content'` | `'content-sidebar'` | `'sidebar-content'` | `'content-sidebar-sidebar'` | `'sidebar-content-sidebar'` | `'sidebar-sidebar-content'`

#####inner_bg_image_src
*default: `false`*

This setting will add a `background-image` css styles to the `.site-inner` element of a page.

Possible values are: `false` | any valid url **string** to an image

#####page_sections
*default: `array()`*

This setting will build and add page sections (`.page-section`) to a page.

Possible values are: an array of custom page sections

#####show_page_title
*default: `true`*

This setting will activate whether to show or hide the page title (`.entry-title`) element of a page.

*Note: This will only affect the primary page title - not any inner loop of post's titles.*

Possible values are: `true` | `false`

#####show_content
*default: `true`*

This setting will activate whether to show or hide the page content (`.entry-conetnt`) element of a page.

*Note: This will only affect the primary page content - not any inner loop of post's contents.*

Possible values are: `true` | `false`

#####primary_sidebar
*default: `true`*

This setting will activate whether to show or hide the primary sidebar (`.sidebar-primary`) element of a page.

*Note: Any string with dashes or underscores will build a new sidebar widget area that is specific to this page. Passing true will set the default `.sidebar-primary` as the primary sidebar.*

Possible values are: `true` | `false` | any string to create a new custom sidebar widget area

#####secondary_sidebar
*default: `true`*

This setting will activate whether to show or hide the secondary sidebar (`.sidebar-secondary`) element of a page.

*Note: Any string with dashes or underscores will build a new sidebar widget area that is specific to this page. Passing true will set the default `.sidebar-secondary` as the secondary sidebar.*

Possible values are: `true` | `false` | any string to create a new custom sidebar widget area

#####footer_widgets
*default: `3`*

This setting will activate the number of footer widgets to show (in numerical order) on a page.

*Note: Set to 0 to hide footer widgets.*

Possible values are: any integer

#####show_footer
*default: `true`*

This setting will activate whether to show or hide the page footer (`.site-footer`) element of a page.

Possible values are: `true` | `false`

#####footer_creds
*default: `true`*

This settings will override the footer credits per a customizable string.

*Note: Set to `true` to keep the default Genesis page credits.*

Possible values are: `true` | `false` | any string to customize the credits text

#####elements
*default:*
```
array(
    'primary_nav' => array( 'genesis_after_header', 10 ),
    'secondary_nav' => array( 'genesis_after_header', 10 ),
)
```

This setting will call various genesis functions at customizable action hooks (with customizable priorities).

Possible values are: an array of element string id's and their `array()` values of hook-priority callbacks.

#####wraps
*default:*
```
array(
    'header',
    'nav',
    'subnav',
    'site-inner',
    'content-sidebar',
    'entry',
    'footer-widgets',
    'footer',
    'page-section',
)
```

This setting will set the items of `genesis_structural_wraps()` (`.wrap`) to activate.

Possible values are: `false` | any array of structural wrap ids

### Page Sections
Page Sections are how Genesis Layouts define large banners of content within a page. Page sections commonly have images or videos as background with content on top.

Page sections have these settings by default:

1. `font_size` | (*default*: `1`)
2. `font_background` | (*default*: `'#ffffff'`)
3. `content_align` | (*default*: `array( 'center', 'middle' )`)
4. `background` | (*default*: `array()`)
5. `atts` | (*default*: `array()`)

```
array(
    'font_size'        => 1,
    'font_background'  => '#ffffff',
    'content_align'    => array( 'center', 'middle' ),
    'background'       => array(
        'image'           => '',
        'video'           => '',
        'overlay'         => '#ffffff',
        'overlay_opacity' => '0',
    ),
    'atts'             => array(),
);
```