<?php

// Ahoy! All engines ready, let's fire up!
if ( ! function_exists( 'cb_start' ) ) {
    function cb_start() {
        // setting theme support
        cb_theme_support();
        // user rating loaded in footer
        add_action('wp_footer', 'cb_user_rating');
        // adding sidebars to Wordpress
        add_action( 'widgets_init', 'cb_register_sidebars' );
    }
}
add_action('after_setup_theme','cb_start', 16);

if ( ! function_exists( 'cb_admin_fonts' ) ) {
    function cb_admin_fonts(){

        $cb_admin_font = '//fonts.googleapis.com/css?family=Oswald:400,700,400italic';
        wp_register_style( 'cb-font-body-stylesheet',  $cb_admin_font, array(), '1.0', 'all' );
        wp_enqueue_style('cb-font-body-stylesheet');

    }
}
add_action('admin_enqueue_scripts', 'cb_admin_fonts');

// Load Mobile Detection Class
require_once get_template_directory().'/library/mobile-detect-class.php';

/*********************
THEME SUPPORT
*********************/

// Adding Functions & Theme Support
if ( ! function_exists( 'cb_theme_support' ) ) {
    function cb_theme_support() {

        // Title Tag
        add_theme_support( 'title-tag' );
        // wp thumbnails
        add_theme_support('post-thumbnails');
        // default thumb size
        set_post_thumbnail_size(125, 125, true);
        // RSS
        add_theme_support('automatic-feed-links');
        // adding post format support
        add_theme_support( 'post-formats',
            array(
                'video',
                'audio',
                'gallery',
            )
        );
        // wp menus
        add_theme_support( 'menus' );
        // registering menus
        register_nav_menus(
            array(
                    'top' => 'Secondary Navigation Menu',
                    'main' => 'Main Navigation Menu',
                    'footer' => 'Footer Navigation Menu',
                    'small' => 'Small-Screen Navigation Menu',
            )
        );
    }
}

/*********************
PRE 4.1 TITLE
*********************/
if ( ! function_exists( '_wp_render_title_tag' ) ) {
    function cb_pre_4_1_title() {
        ?>
        <title><?php wp_title( '|', true, 'right' ); ?></title>
        <?php
    }
   
    add_action( 'wp_head', 'cb_pre_4_1_title' );
}

/*********************
MENUS & NAVIGATION
*********************/

// Top Nav
if ( ! function_exists( 'cb_top_nav' ) ) {
    function cb_top_nav(){   wp_nav_menu(
            array(
                'theme_location'  => 'top',
                'container' => FALSE,
                'menu_class' => 'menu',
                'items_wrap' => '<ul class="cb-top-nav">%3$s</ul>',
                'walker'          => ''
            )
        );
    }
}

// Small Nav
if ( ! function_exists( 'cb_small_screen_nav' ) ) {
    function cb_small_screen_nav(){

        wp_nav_menu(
            array(
                'theme_location'  => 'small',
                'container' => FALSE,
                'menu_class' => 'menu',
                'items_wrap' => '<ul class="cb-small-nav">%3$s</ul>',
                'walker'          => ''
            )
        );
    }
}

// Footer Nav
if ( ! function_exists( 'footer_nav' ) ) {
    function footer_nav(){
        wp_nav_menu(
            array(
                'container_class' => 'cb-footer-links clearfix',
                'menu' => 'Footer Links',
                'menu_class' => 'nav cb-footer-nav clearfix',
                'theme_location' => 'footer',
                'depth' => 0,
                'fallback_cb' => 'none'
            )
        );
    }
}

/*********************
ADD SEARCH/LOGIN TO MAIN MENU
*********************/
if ( ! function_exists( 'cb_add_extras_main_menu' ) ) {
    function cb_add_extras_main_menu($content, $args) {

        $cb_menu_icons = ot_get_option('cb_main_nav_icons', 'both');
        $cb_nav_style = ot_get_option('cb_menu_style', 'cb_dark');
        $cb_logo_in_nav = ot_get_option('cb_logo_in_nav', 'off');
        $cb_menu_output = NULL;

        if ( $cb_nav_style == 'cb_light' ) {
            $cb_menu_color = 'cb-light-menu';
        } else {
             $cb_menu_color = 'cb-dark-menu';
        }

        if ( $cb_menu_icons != 'off' ) {

            $cb_login_space = '<i class="fa fa-user"></i>';
            $cb_login_class = '<li class="cb-icon-login">';

            if ( is_user_logged_in() == true ) {

                global $current_user;
                get_currentuserinfo();
                $cb_author_id = $current_user->ID;
                $cb_login_space = get_avatar( $cb_author_id, $size = '150' );
                $cb_login_class = '<li class="cb-icon-logged-in">';
                $cb_login_title = $current_user->display_name;

                if ( class_exists('buddypress') ) {

                    global $bp;
                    $cb_buddypress_current_user_id = $bp->loggedin_user->id;
                    $cb_login_space = bp_core_fetch_avatar( array( 'item_id' => $cb_buddypress_current_user_id, 'type' => 'thumb', 'width' => 25, 'height' => 25 ) );

                }

            } else {
                $cb_login_title = __('Login / Join', 'cubell');
            }

            $cb_menu_output = '<li class="cb-icons"><ul>';
            if ( function_exists('login_with_ajax') ) {
                         if ( ( $cb_menu_icons == 'both' ) || ($cb_menu_icons == 'login' ) ) {
                              $cb_menu_output .= $cb_login_class . '<a href="#" title="' . esc_attr( $cb_login_title ) . '" class="cb-tip-bot" data-reveal-id="cb-login-modal">' . $cb_login_space . '</a></li>';
                         }
            }
            if ( ( $cb_menu_icons == 'both') || ( $cb_menu_icons == 'search' ) ) {
                 $cb_menu_output .=  '<li class="cb-icon-search"><a href="#" title="' . __('Search', 'cubell') . '" class="cb-tip-bot" data-reveal-id="cb-search-modal"><i class="fa fa-search"></i></a></li>';
            }

            $cb_menu_output .= '</ul></li>';

        }

        if ( $cb_logo_in_nav != 'off' ) {

            $cb_logo_in_nav_when = ot_get_option( 'cb_logo_in_nav_when', 'always' );
            $cb_logo_nav_url = ot_get_option( 'cb_logo_nav_url', NULL );
            $cb_logo_nav_url_retina = ot_get_option( 'cb_logo_nav_url_retina', NULL );
            $cb_logo_nav_padding = ot_get_option( 'cb_logo_nav_padding', '10' );

            if ( $cb_logo_nav_url != NULL ) {
                $cb_menu_output .= '<li id="cb-nav-logo" class="cb-nav-logo-' . $cb_logo_in_nav_when . '" style="padding-top: ' . $cb_logo_nav_padding . 'px"><a href="' . get_home_url() . '"><img src="' . esc_url( $cb_logo_nav_url ) . '" alt="site logo" data-at2x="' . esc_url( $cb_logo_nav_url_retina ) . '" ></a></li>';
            }
        }

        if ( ( $cb_logo_in_nav != 'off' ) || ( $cb_menu_icons != 'off' ) ) {
            if( $args->theme_location == 'main' ) {
                ob_start();
                echo $cb_menu_output;
                $content .=  ob_get_contents();
                ob_end_clean();
            }
        }

        return $content;
    }
}
add_filter('wp_nav_menu_items','cb_add_extras_main_menu', 10, 2);

if ( ! function_exists( 'cb_add_modals_main_menu' ) ) {
    function cb_add_modals_main_menu() {

        $cb_menu_icons = ot_get_option('cb_main_nav_icons', 'both');
        $cb_nav_style = ot_get_option('cb_menu_style', 'cb_dark');
        if ($cb_nav_style == 'cb_light') {
            $cb_menu_color = 'cb-light-menu';
        } else {
             $cb_menu_color = 'cb-dark-menu';
        }

        if ( function_exists('login_with_ajax') && ( ( $cb_menu_icons == 'both' ) || ($cb_menu_icons == 'login' ) ) ) {  login_with_ajax();  }

        if ( ( $cb_menu_icons == 'both') || ( $cb_menu_icons == 'search' ) ) {

            echo '<div id="cb-search-modal" class="' . $cb_menu_color . '">
                        <div class="cb-search-box">
                            <div class="cb-header">
                                <div class="cb-title">' . __("Search", "cubell").'</div>
                                <div class="cb-close">
                                    <span class="cb-close-modal"><i class="fa fa-times"></i></span>
                                </div>
                            </div>';
                            get_search_form();
            echo '</div></div>';
        }
    }
}

/*********************
BREAKING NEWS
*********************/
if ( ! function_exists( 'cb_breaking_news' ) ) {
    function cb_breaking_news(){

        $cb_breaking_filter = ot_get_option('cb_breaking_news_filter', NULL);
        $cb_breaking_news_title = ot_get_option('cb_breaking_news_title', NULL);

        if ( $cb_breaking_news_title == NULL ) {
            $cb_breaking_news_title = __("Breaking", "cubell");
        }

        $cb_cpt_output = cb_get_custom_post_types();

        if ($cb_breaking_filter == NULL) { $cb_breaking_cat = implode(",", get_terms( 'category', array('fields' => 'ids') ));  } else { $cb_breaking_cat = implode(",", $cb_breaking_filter); }
        $cb_breaking_args = array( 'post_type' => $cb_cpt_output, 'numberposts' => '6', 'category' => $cb_breaking_cat, 'post_status' => 'publish', 'suppress_filters' => false);
        $cb_news_posts = wp_get_recent_posts( $cb_breaking_args);
        $cb_news = NULL;
        $cb_news .= '<div class="cb-breaking-news"><span>' . $cb_breaking_news_title . ' <i class="fa fa-long-arrow-right"></i></span><ul>';

        foreach( $cb_news_posts as $news ) {
            $cb_news .= '<li><a href="' . get_permalink($news["ID"]) . '" title="Look '.esc_attr($news["post_title"]) . '" >' .   $news["post_title"].'</a> </li> ';
        }

        $cb_news .= '</ul></div>';

        return $cb_news;
    }
}

/*********************
CUSTOM WALKER
*********************/
if ( ! function_exists( 'cb_menu_children' ) ) {
    function cb_menu_children ($object){

        $cb_with_children = array();

        foreach ( $object as $menu ) {

            $cb_current_obj = $menu->menu_item_parent;

            if ( $cb_current_obj != '0' ) {
                $cb_with_children[] .= $menu->menu_item_parent;
            }
        }

        foreach ( $object as $menu ) {

            $cb_current_obj = $menu->ID;

            if ( in_array( $cb_current_obj, $cb_with_children ) ) {
                $menu->classes[] = "cb-has-children";
            }
        }
        return $object;
    }
}
add_filter( 'wp_nav_menu_objects', 'cb_menu_children' );

if ( ! class_exists( 'CB_Walker' ) ) {
    class CB_Walker extends Walker_Nav_Menu {
        protected $cb_menu_css = array();

        function start_el( &$output, $object, $depth = 0, $args = array(), $id = 0 ) {

            $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
            $classes = empty( $object->classes ) ? array() : (array) $object->classes;
            $classes[] = 'menu-item-' . $object->ID;
            /**
             * Filter the CSS class(es) applied to a menu item's <li>.
             *
             * @since 3.0.0
             *
             * @see wp_nav_menu()
             *
             * @param array  $classes The CSS classes that are applied to the menu item's <li>.
             * @param object $item    The current menu item.
             * @param array  $args    An array of wp_nav_menu() arguments.
             */
            $class_names = join( ' ', (array) apply_filters( 'nav_menu_css_class', array_filter( $classes ), $object, $args ) );
            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
            /**
             * Filter the ID applied to a menu item's <li>.
             *
             * @since 3.0.1
             *
             * @see wp_nav_menu()
             *
             * @param string $menu_id The ID that is applied to the menu item's <li>.
             * @param object $object    The current menu item.
             * @param array  $args    An array of wp_nav_menu() arguments.
             */
            $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $object->ID, $object, $args );
            $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
            $output .= $indent . '<li' . $id . $class_names .'>';
            $atts = array();
            $atts['title']  = ! empty( $object->attr_title ) ? $object->attr_title : '';
            $atts['target'] = ! empty( $object->target )     ? $object->target     : '';
            $atts['rel']    = ! empty( $object->xfn )        ? $object->xfn        : '';
            $atts['href']   = ! empty( $object->url )        ? $object->url        : '';
            /**
             * Filter the HTML attributes applied to a menu item's <a>.
             *
             * @since 3.6.0
             *
             * @see wp_nav_menu()
             *
             * @param array $atts {
             *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
             *
             *     @type string $title  Title attribute.
             *     @type string $target Target attribute.
             *     @type string $rel    The rel attribute.
             *     @type string $href   The href attribute.
             * }
             * @param object $item The current menu item.
             * @param array  $args An array of wp_nav_menu() arguments.
             */
            $atts = apply_filters( 'nav_menu_link_attributes', $atts, $object, $args );
            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( ! empty( $value ) ) {
                    $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }       

            $cb_cat_menu = $object->cbmegamenu;
            if ( $depth > 0 ) {
                $cb_ajax_onoff = ot_get_option( 'cb_ajax_mm', 'on' );
                if ( $cb_ajax_onoff == 'on' ) {
                    $attributes .= ' data-cb-c="' . $object->object_id . '" class="cb-c-l"';
                }
            }

            if ( $cb_cat_menu == NULL ) {
                 $cb_cat_menu = '2';
            }

            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before . apply_filters( 'the_title', $object->title, $object->ID ) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;
            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $object, $depth, $args );

            $cb_base_color = ot_get_option('cb_base_color', '#eb9812');

            if ( function_exists( 'get_tax_meta' ) ) {
                $cb_color = get_tax_meta( $object->object_id,'cb_color_field_id' );
            } else {
                $cb_color = $cb_base_color;
            }

            $cb_output = $cb_posts = $cb_menu_featured = $cb_has_children = $cb_slider_output = NULL;
            $cb_current_type = $object->object;
            $cb_current_classes = $object->classes;
            if ( in_array('cb-has-children', $cb_current_classes) ) { $cb_has_children = ' cb-with-sub cb-pre-load'; }

            if ( ( ( $cb_cat_menu == 1 ) || ( $cb_cat_menu == 4 ) ) && ( $object->menu_item_parent == '0' ) ) { $output .= '<div class="cb-big-menu">'; }
            if ( ( $cb_cat_menu == 2 ) && ( $depth == 0 ) && ( $object->menu_item_parent == '0' ) && ( in_array('cb-has-children', $cb_current_classes) ) ) { $output .= '<div class="cb-links-menu">'; }
            if ( $cb_cat_menu == 3 && ( $object->menu_item_parent == '0' ) ) { $output .= '<div class="cb-mega-menu">'; }

            if ( ( $cb_cat_menu == 1 ) && ( $object->menu_item_parent == '0' ) ) {

                $cb_cat_id = $object->object_id;
                if ( function_exists( 'get_tax_meta' ) ) {
                    $cb_category_color = get_tax_meta( $cb_cat_id, 'cb_color_field_id' );
                } else {
                    $cb_category_color = NULL;
                }

                if ( ( $cb_category_color == NULL ) || ( $cb_category_color == '#' ) ) {
                    $cb_category_color = $cb_base_color;
                }

                $cb_cpt_output = cb_get_custom_post_types();

                $cb_args_featured = array( 'cat' => $cb_cat_id,  'post_type' => $cb_cpt_output,  'post_status' => 'publish',  'posts_per_page' => 1, 'ignore_sticky_posts'=> 1, 'meta_key' => 'cb_featured_post_menu', 'meta_value' => 'featured',  'meta_compare' => '==' );

                $cb_qry_featured = $cb_img = NULL;
                $cb_qry_featured = new WP_Query($cb_args_featured);

                $cb_featured_random_title = __('Featured', 'cubell');

                if ( $cb_qry_featured->post_count == 0 ) {
                    $cb_qry_featured = NULL;
                    $cb_args_featured = array( 'cat' => $cb_cat_id,  'post_type' => $cb_cpt_output,  'post_status' => 'publish',  'posts_per_page' => 1, 'ignore_sticky_posts'=> 1, 'orderby' => 'rand');
                    $cb_qry_featured = new WP_Query($cb_args_featured);
                    $cb_featured_random_title = __('Random', 'cubell');
                }

                foreach ( $cb_qry_featured->posts as $cb_post ) {
                    setup_postdata($cb_post);

                        $cb_post_id = $cb_post->ID;

                        $cb_img = cb_get_thumbnail('480', '240', $cb_post_id);
                        $cb_permalink = get_permalink($cb_post_id);

                        $cb_menu_featured .= ' <li class="cb-article clearfix">
                        <div class="cb-mask" style="background-color:' . $cb_category_color.';">' . $cb_img . cb_review_ext_box( $cb_post_id, $cb_category_color ) . '</div>
                        <div class="cb-meta">
                            <h2 class="h4"><a href="' . esc_url( $cb_permalink ) . '">' . $cb_post->post_title . '</a></h2>
                            '.cb_byline(true, $cb_post_id) . '
                        </div></li>';
                }

                wp_reset_postdata();
                if ( $cb_has_children == NULL ) {
                    $cb_qry_amount = 6;
                    $cb_big_recent = ' cb-recent-fw';
                    $cb_closer = '</div>';
                } else {
                    $cb_qry_amount = 3;
                    $cb_big_recent = $cb_closer = NULL;
                }

                $cb_cpt_output = cb_get_custom_post_types();

                $cb_args = array( 'cat' => $cb_cat_id,  'post_type' => $cb_cpt_output,  'post_status' => 'publish',  'posts_per_page' => $cb_qry_amount,  'ignore_sticky_posts'=> 1, 'post__not_in' => array( $cb_post_id ) );
                $cb_qry_latest = $cb_img = NULL;
                $cb_qry_latest = new WP_Query($cb_args);
                $i = 1;

                while ( $cb_qry_latest->have_posts() ) {

                        $cb_qry_latest->the_post();

                        $cb_post_id = get_the_ID();
                        $cb_post_title = get_the_title();

                        $cb_img = cb_get_thumbnail('80', '60', $cb_post_id);
                        $cb_permalink = get_permalink($cb_post_id);

                        $cb_posts .= ' <li class="cb-article-' . $i.' clearfix">
                        <div class="cb-mask" style="background-color:' . $cb_category_color . ';">' . $cb_img . cb_review_ext_box( $cb_post_id, $cb_category_color, true ) . '</div>
                        <div class="cb-meta">
                            <h2 class="h4"><a href="' . esc_url( $cb_permalink ) . '">' . $cb_post_title . '</a></h2>
                            ' . cb_byline( false, $cb_post_id, true ) . '
                        </div></li>';
                    $i++;
                }

                wp_reset_postdata();
            }

            if ( ( $cb_cat_menu == 4 ) && ( $object->menu_item_parent == '0' ) ) {

                $cb_cpt_output = cb_get_custom_post_types();
                $cb_cat_id = $object->object_id;
                if ( function_exists( 'get_tax_meta' ) ) {
                    $cb_category_color = get_tax_meta($cb_cat_id, 'cb_color_field_id');
                } else {
                    $cb_category_color = NULL;
                }

                if ( $cb_has_children != NULL ) {
                    $cb_slider_type = 'flexslider-1-menu';
                     $cb_args_featured = array( 'cat' => $cb_cat_id,  'post_type' => $cb_cpt_output,  'post_status' => 'publish',  'posts_per_page' => 9, 'ignore_sticky_posts'=> 1 );
                } else {
                    $cb_args_featured = array( 'cat' => $cb_cat_id,  'post_type' => $cb_cpt_output,  'post_status' => 'publish',  'posts_per_page' => 12, 'ignore_sticky_posts'=> 1 );
                    $cb_slider_type = 'flexslider-1-fw-menu';
                }

                $cb_qry_featured = $cb_img = NULL;
                $cb_qry_featured = new WP_Query($cb_args_featured);
                $cb_featured_random_title = __('Recent', 'cubell');

                foreach ( $cb_qry_featured->posts as $cb_post ) {
                    setup_postdata( $cb_post );

                        $cb_post_id = $cb_post->ID;
                        $cb_category_color = cb_get_cat_color($cb_post_id);

                        if  ( has_post_thumbnail( $cb_post_id ) ) { $cb_img = get_the_post_thumbnail( $cb_post_id, 'cb-750-400' ); }
                        $cb_permalink = get_permalink( $cb_post_id );

                        $cb_slider_output .= ' <li>
                        <div class="cb-mask" style="border-top-color:' . $cb_category_color . ';"><a href="' . esc_url( $cb_permalink ) . '">' . $cb_img . '</a>' . cb_review_ext_box( $cb_post_id, $cb_category_color ) . '</div>
                        <div class="cb-meta">
                            <h2><a href="' . esc_url( $cb_permalink ) . '">' . $cb_post->post_title . '</a></h2>
                            ' . cb_byline( true, $cb_post_id ) . '
                        </div>' . cb_review_ext_box( $cb_post_id, $cb_category_color ) . '</li>';
               }

                wp_reset_postdata();
            }

            if ( $cb_current_type == 'category' ) {
                    if ( ( $cb_color == '#' ) || ( $cb_color == NULL ) ) { $cb_color = $cb_base_color; }
                    $this->cb_menu_css[] .= '#cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ':hover,
                                             #cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ':focus,
                                             #cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ' .cb-sub-menu li .cb-grandchild-menu,
                                             #cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ' .cb-sub-menu { background:' . $cb_color.'!important; }
                                             #cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ' .cb-mega-menu .cb-sub-menu li a { border-bottom-color:' . $cb_color.'!important; }';

                                             $cb_border_color = $cb_color;
            } else {
                $cb_page_color = get_post_meta($object->object_id,'cb_overall_color_post');
                if ( ( $cb_page_color != NULL ) && ( $cb_page_color[0] != '#' ) ) { $cb_base_color = $cb_page_color[0]; }
                $this->cb_menu_css[] .= '#cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ':hover,
                                         #cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ':focus,
                                         #cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ' .cb-sub-menu li .cb-grandchild-menu,
                                         #cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ' .cb-sub-menu { background:' . $cb_base_color.'!important; }
                                         #cb-nav-bar #cb-main-menu .main-nav .menu-item-' . $object->ID . ' .cb-mega-menu .cb-sub-menu li a { border-bottom-color:' . $cb_base_color.'!important; }';
                                         $cb_border_color = $cb_base_color;
            }

            if ( $cb_posts != NULL ) {
                     $output .= '<div class="cb-articles' . $cb_has_children . '">
                                    <div class="cb-featured">
                                        <div class="cb-mega-title h2"><span style="border-bottom-color:' . $cb_border_color . ';">' . $cb_featured_random_title.'</span></div>
                                        <ul>' . $cb_menu_featured. '</ul>
                                     </div>
                                     <div class="cb-recent' . $cb_big_recent . '">
                                        <div class="cb-mega-title h2"><span style="border-bottom-color:' . $cb_border_color . ';">' . __('Recent', 'cubell') . '</span></div>
                                        <ul>' . $cb_posts . '</ul>
                                     </div>
                                 </div>';
                          $output .= $cb_closer;
            }

            if ( $cb_slider_output != NULL ) {
                    $output .= '<div class="cb-articles' . $cb_has_children . '">
                                    <h2 class="cb-mega-title cb-slider-title"><span style="border-bottom-color:' . $cb_border_color . ';">' . $cb_featured_random_title . '</span></h2>
                                    <div class="cb-slider-a">
                                        <div class="' . $cb_slider_type . ' clearfix">
                                            <ul class="slides">' . $cb_slider_output . '</ul>
                                            </div>
                                        </div>
                                    </div>';
            }

            add_action( 'wp_head', array( $this, 'cb_menu_css' ) );
        }

        public function cb_menu_css() {
            echo '<style>' . join( "\n", $this->cb_menu_css ) . '</style>';
        }

        function start_lvl( &$output, $depth=0, $args = array() ) {

            if ( $depth > 3 ) { return; }
            if ( $depth == 2 )  { $output .= '<ul class="cb-grandchild-menu cb-great-grandchild-menu">'; }
            if ( $depth == 1 )  { $output .= '<ul class="cb-grandchild-menu">'; }
            if ( $depth == 0 )  { $output .= '<ul class="cb-sub-menu">'; }
        }

        function end_lvl( &$output, $depth=0, $args = array() ) {

                        if ( $depth > 3 ) { return; }
                        if ( $depth == 0 ) { $output .= '</ul></div>'; }
                        if ( $depth == 1 ) { $output .= '</ul>'; }
                        if ( $depth == 2 ) { $output .= '</ul>'; }

        }
    }
}



/*********************
MMA
*********************/
if ( ! function_exists( 'cb_mm_a' ) ) {
    function cb_mm_a() {
       
        $cb_cat_id = isset( $_GET['cid'] ) && $_GET['cid'] ? intval( $_GET['cid'] ) : 0;
        $cb_a = isset( $_GET['acall'] ) ? 1 : 0;
        $cb_args = array( 'cat' => $cb_cat_id,  'post_status' => 'publish',  'posts_per_page' => 3,  'ignore_sticky_posts'=> 1 );
        $cb_qry_latest = new WP_Query($cb_args);
        $i = 1;
        $cb_post_output = NULL;

        while ( $cb_qry_latest->have_posts() ) {

            $cb_qry_latest->the_post();
            $cb_post_id = get_the_ID();
            $cb_post_title = get_the_title();

            $cb_img = cb_get_thumbnail('80', '60', $cb_post_id);
            $cb_permalink = get_permalink($cb_post_id);

            $cb_post_output .= ' <li class="cb-article-' . $i.' clearfix">
            <div class="cb-mask" style="background-color:' . cb_get_cat_color( $cb_post_id ) . ';">' . $cb_img . cb_review_ext_box( $cb_post_id, cb_get_cat_color( $cb_post_id ), true ) . '</div>
            <div class="cb-meta">
                <h2 class="h4"><a href="' . esc_url( $cb_permalink ) . '">' . $cb_post_title . '</a></h2>
                ' . cb_byline( false, $cb_post_id, true ) . '
            </div></li>';
            $i++;
        }

        wp_reset_postdata();
        if ( $cb_a == 1 ) {
            echo $cb_post_output;
        } else {
            return $cb_post_output;
        }

        die();
    }
}

add_action( 'wp_ajax_cb_mm_a', 'cb_mm_a' );
add_action( 'wp_ajax_nopriv_cb_mm_a', 'cb_mm_a' );

/*********************
GET CATEGORY COLOR
*********************/
if ( ! function_exists( 'cb_get_cat_color' ) ) {
    function cb_get_cat_color( $cb_post_id ) {

        $cb_cat_id_current = get_the_category( $cb_post_id );
        $cb_cat_parent = $cb_cat_id_current[0]->category_parent;
        $cb_category_color = NULL;

        if ( function_exists( 'get_tax_meta' ) ) {
            $cb_cat_id_current =$cb_cat_id_current[0]->term_id;
            $cb_category_color = get_tax_meta( $cb_cat_id_current, 'cb_color_field_id' );
        }

        if ( ( $cb_category_color == NULL ) || ( $cb_category_color == '#' ) ) {
            if ( $cb_cat_parent != '0' ) {
                
                if ( function_exists( 'get_tax_meta' ) ) {
                    $cb_category_color = get_tax_meta( $cb_cat_parent, 'cb_color_field_id' );

                }
            }
        }

        if ( ( $cb_category_color == NULL ) ||  ( $cb_category_color == '#' ) ) {
            $cb_category_color =  ot_get_option( 'cb_base_color', '#222' );
        }

        return $cb_category_color;
    }
}

/*********************
LIMITED TAGCLOUD WIDGET
*********************/
if ( ! function_exists( 'cb_tag_cloud_widget' ) ) {
    function cb_tag_cloud_widget($args) {
        $args['number'] = 20;
        return $args;
    }
}
add_filter( 'widget_tag_cloud_args', 'cb_tag_cloud_widget' );

/*********************
POSTS IN FRONTEND SEARCHES
*********************/
if ( ! function_exists( 'cb_clean_search' ) ) {
    function cb_clean_search($cb_query) {

        if ( ! is_admin() && ( $cb_query->is_search == true ) ) {

            if ( class_exists( 'bbPress') && ( is_bbpress() == true ) ) {
            } else {
                $cb_cpt_output = cb_get_custom_post_types();
                $cb_query->set( 'post_type', $cb_cpt_output );
            }

        }
        return $cb_query;
    }
}
add_filter('pre_get_posts','cb_clean_search');

/*********************
GET BLOG STYLE
*********************/
if ( ! function_exists( 'cb_get_blog_style' ) ) {
    function cb_get_blog_style() {
        
        $cb_output = NULL;

        if ( is_search() ) {
            $cb_output = ot_get_option('cb_misc_search_pl', 'blog-style-a');
        }

        if ( is_date() ) {
            $cb_output = ot_get_option('cb_misc_archives_pl', 'blog-style-a');
        }
        

        if ( is_author() ) {
            $cb_output = ot_get_option('cb_misc_author_pl', 'blog-style-a');
        }

        if ( is_tag() ) {
            $cb_tag_id = get_query_var('tag_id');

            if ( function_exists('get_tax_meta') ) {

                $cb_output = get_tax_meta( $cb_tag_id, 'cb_cat_style_field_id' ); 
              
            }
        }

        if ( $cb_output == NULL ) {
            $cb_output = 'style-a';
        }


        return $cb_output;
    }
}

/*********************
CLEAN EXCERPT
*********************/
if ( ! function_exists( 'cb_clean_excerpt' ) ) {
    function cb_clean_excerpt ($cb_characters, $cb_read_more = false) {
        global $post;
        $cb_excerpt_output = $post->post_excerpt;

        if ( $cb_excerpt_output == NULL ) {

            $cb_excerpt_output = get_the_content();
            $cb_excerpt_output = preg_replace( ' (\[.*?\])', '', $cb_excerpt_output );
            $cb_excerpt_output = strip_shortcodes( $cb_excerpt_output );
            $cb_excerpt_output = strip_tags( $cb_excerpt_output );
            $cb_characters = intval($cb_characters);
            $cb_excerpt_output = substr( $cb_excerpt_output, 0, $cb_characters );
            $cb_excerpt_output = substr( $cb_excerpt_output, 0, strripos( $cb_excerpt_output, ' ' ) );
            $cb_excerpt_output = trim( preg_replace( '/\s+/', ' ', $cb_excerpt_output) );

            if ( $cb_read_more != false ) {
                $cb_excerpt_output = $cb_excerpt_output.'... <a href="' . get_permalink() . '"><span class="cb-read-more"> '.__( "Read more", "cubell").'</span></a>';
            } else {
                $cb_excerpt_output = $cb_excerpt_output . '...';
            }
        }

        return $cb_excerpt_output;
    }
}

/*********************
BREADCRUMBS
*********************/
if ( ! function_exists( 'cb_breadcrumbs' ) ) {

    function cb_breadcrumbs( $cb_padding = 'padding-on') {
        $cb_breadcrumb = NULL;
        $cb_post_type = get_post_type();
        $cb_cpt = cb_get_custom_post_types();

        if ( $cb_padding == 'padding-off' ) {
            $cb_padding_type = ' cb-padding-off';
        } else {
            $cb_padding_type = NULL;
        }

        if ( is_page() == true ) {

            global $post;
            if ( $post->post_parent == 0 ) {
                return;
            }
        }
        
        
        $cb_breadcrumb = '<div class="cb-breadcrumbs wrap' . $cb_padding_type . '">';
        $cb_icon = '<i class="fa fa-angle-right"></i>';
        $cb_breadcrumb .= '<a href="' . home_url() . '">' . __("Home", "cubell").'</a>' . $cb_icon;

        if ( is_tag() ) {

            $cb_tag_id = get_query_var('tag_id');
            $cb_breadcrumb .=  '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . get_tag_link($cb_tag_id) . '" itemprop="url"><span itemprop="title">' . single_tag_title("", false) . '</span></a></div>';

            

        } elseif ( is_category() ) {

            $cb_cat_id = get_query_var('cat');
            $cb_current_category = get_category( $cb_cat_id );

            if ( $cb_current_category->category_parent == '0' ) {

                 $cb_breadcrumb .=  '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . get_category_link( $cb_current_category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), $cb_current_category->name ) ) . '" itemprop="url"><span itemprop="title">' . $cb_current_category->name . '</span></a></div>';

            } else {

                $cb_breadcrumb .=  '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . get_category_link( $cb_current_category->category_parent ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), get_the_category_by_ID( $cb_current_category->category_parent ) ) ) . '"><span itemprop="title">' . get_the_category_by_ID( $cb_current_category->category_parent ) . '</span></a></div>' . $cb_icon;
                $cb_breadcrumb .= '<div itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . get_category_link( $cb_current_category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), $cb_current_category->name ) ) . '" itemprop="url"><span itemprop="title">' . $cb_current_category->name . '</span></a></div>';

            }

        } elseif ( function_exists('buddypress') && ( is_buddypress() == true ) )  {
            global $bp;
            $cb_bp_output = NULL;
            $cb_bp_current_component = bp_current_component();
            $cb_bp_current_action = bp_current_action();

            if ( ( $cb_bp_current_action != 'my-groups' ) && ( $cb_bp_current_component == 'groups' ) ) {

                $cb_bp_group = $bp->groups->current_group;

                if ( ! is_numeric( $cb_bp_group ) ) {
                    $cb_bp_group_id = $cb_bp_group->id;
                    $cb_bp_group_name = $cb_bp_group->name;
                    $cb_bp_group_link = bp_get_group_permalink($cb_bp_group);
                    $cb_bp_output = '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ) . '" itemprop="url"><span itemprop="title">' . __('Groups', 'cubell') . '</span></a></div>' . $cb_icon . $cb_bp_group_name;
                } else {
                    $cb_bp_output =  __('Groups', 'cubell');
                }

                $cb_breadcrumb .=  $cb_bp_output;
            }

            if ( ( $cb_bp_current_component == 'activity' ) || ( $cb_bp_current_action == 'my-groups' ) || ( $cb_bp_current_action == 'public' ) || ( $cb_bp_current_component == 'settings' ) || ( $cb_bp_current_component == 'forums' ) || ( $cb_bp_current_component == 'friends' ) ) {

                if ( isset( $bp->activity->current_id ) ) {
                    $cb_bp_activity = $bp->activity->current_id;
                } else {
                    $cb_bp_activity = NULL;
                }

                $cb_activity_title = get_the_title();
                $cb_bp_activity_link = bp_get_members_directory_permalink();
                $cb_bp_output .=  '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . esc_url( $cb_bp_activity_link ) . '" itemprop="url"><span itemprop="title">' . __('Members', 'cubell') . '</span></a></div>' . $cb_icon . $cb_activity_title;

                if ( $cb_bp_activity != NULL ) {

                    $cb_bp_output .=  __('Members', 'cubell');
                }

                $cb_breadcrumb .=  $cb_bp_output;
            }

            if ( $cb_bp_current_component == 'messages' ) {

                $cb_breadcrumb .=  __('Messages', 'cubell');
            }

            if ( $cb_bp_current_component == 'register' ) {

                $cb_breadcrumb .=  __('Register', 'cubell');
            }

            if ( bp_is_directory() == true ) {
                $cb_breadcrumb = '<div>';
            }

        } elseif ( ( in_array( $cb_post_type, $cb_cpt ) == true ) || ( $cb_post_type == 'post' ) ) {

            $cb_categories =  get_the_category();

           if ( $cb_categories[0]->category_parent == '0' ) {

               $cb_breadcrumb .=  '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . get_category_link($cb_categories[0]->term_id) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), $cb_categories[0]->name ) ) . '" itemprop="url"><span itemprop="title">' . $cb_categories[0]->name.'</span></a></div>';

           } else {

                $cb_breadcrumb_output = '<a href="' . get_category_link($cb_categories[0]->category_parent) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), get_the_category_by_ID($cb_categories[0]->category_parent) ) ) . '" itemprop="url"><span itemprop="title">' . get_the_category_by_ID($cb_categories[0]->category_parent) . '</span></a>' . $cb_icon;

                $cb_breadcrumb_output .= '<a href="' . get_category_link($cb_categories[0]->term_id) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), $cb_categories[0]->name ) ) . '" itemprop="url"><span itemprop="title">' . $cb_categories[0]->name . '</span></a>';

                $cb_current_cat = get_category($cb_categories[0]->category_parent);

                if ( $cb_current_cat->category_parent != '0' ) {

                    $cb_breadcrumb_output = '<a href="' . get_category_link($cb_current_cat->category_parent) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), get_the_category_by_ID($cb_current_cat->category_parent) ) ) . '" itemprop="url"><span itemprop="title">' . get_the_category_by_ID($cb_current_cat->category_parent) . '</span></a>' . $cb_icon . $cb_breadcrumb_output;

                    $cb_current_cat = get_category( $cb_current_cat->category_parent );

                    if ( $cb_current_cat->category_parent != '0' ) {

                        $cb_breadcrumb_output = '<a href="' . get_category_link($cb_current_cat->category_parent) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), get_the_category_by_ID($cb_current_cat->category_parent) ) ) . '" itemprop="url"><span itemprop="title">' . get_the_category_by_ID($cb_current_cat->category_parent) . '</span></a>' . $cb_icon . $cb_breadcrumb_output;

                        $cb_current_cat = get_category( $cb_current_cat->category_parent );

                        if ( $cb_current_cat->category_parent != '0' ) {

                            $cb_breadcrumb_output = '<a href="' . get_category_link($cb_current_cat->category_parent) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), get_the_category_by_ID($cb_current_cat->category_parent) ) ) . '" itemprop="url"><span itemprop="title">' . get_the_category_by_ID($cb_current_cat->category_parent) . '</span></a>' . $cb_icon . $cb_breadcrumb_output;
                        }
                    }

                }       

                $cb_breadcrumb .=  '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb">' . $cb_breadcrumb_output . '</div>';

            }

        } elseif  ( ( class_exists('Woocommerce') )  &&  ( is_woocommerce() == true ) )  {
            $cb_breadcrumb = NULL;

            woocommerce_breadcrumb( array(
                'delimiter'   =>  $cb_icon,
                'wrap_before' => '<div class="cb-breadcrumbs wrap" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
                'wrap_after'  => ' ',
                'before'      => '',
                'after'       => '',
                'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
             ));
        } elseif ( is_page() == true ) {
            $cb_parent_page = get_post( $post->post_parent );

            $cb_parent_page_title = $cb_parent_page->post_title;
            $cb_breadcrumb .=  '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . get_permalink( $cb_parent_page->ID ) . '"><span itemprop="title">' . $cb_parent_page_title . '</span></a></div>';
        }

        $cb_breadcrumb .= apply_filters( 'cb_breadcrumbs_output', '' );

        $cb_breadcrumb .= '</div>';
        

        return $cb_breadcrumb ;
    }
}

/*********************
WooCommerce
*********************/
if ( class_exists('Woocommerce') ) {

    if ( ! function_exists( 'cb_add_cart_loop' ) ) {
        function cb_add_cart_loop(){
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
        }
    }
    add_action('init','cb_add_cart_loop');

    if ( ! function_exists( 'cb_row_number' ) ) {
        function cb_row_number() {

            $cb_woocommerce_sidebar = ot_get_option('cb_woocommerce_sidebar', 'sidebar');
            $cb_woocommerce_sidebar_override = ot_get_option('cb_woocommerce_sidebar_override', 'sidebar');

            if ( ( ( $cb_woocommerce_sidebar_override == 'cb_no_shop' ) && ( is_shop() == true ) ) || ( $cb_woocommerce_sidebar == 'nosidebar' ) ) {
                 $cb_woo_per_row = 4;
            } else {
                $cb_woo_per_row = 3;
            }

            return $cb_woo_per_row;
        }
    }

    add_filter('loop_shop_columns', 'cb_row_number');

    if ( ! function_exists( 'cb_woocommerce_return_false' ) ) {
        function cb_woocommerce_return_false() {
            return false;
        }
    }

    add_filter( 'woocommerce_show_page_title', 'cb_woocommerce_return_false' );

    if ( ! function_exists( 'woocommerce_output_related_products' ) ) {
        function woocommerce_output_related_products() {
            woocommerce_related_products(3,3);
        }
    }
}

/*********************
bbPress Breadcrumbs
*********************/
if ( ! function_exists( 'cb_bbpress_breadcrumbs' ) ) {
    function cb_bbpress_breadcrumbs() {

        $args['sep'] = ' <i class="fa fa-angle-right"></i> ';
        $args['before'] = '<div class="cb-breadcrumbs wrap">';
        $args['after'] = '</div>';
        $args['include_home'] = false;
        return $args;
    }
}
add_filter('bbp_before_get_breadcrumb_parse_args', 'cb_bbpress_breadcrumbs' );

if ( ! function_exists( 'cb_bbpress_tags' ) ) {
    function cb_bbpress_tags() {

        $args['sep'] = '';
        $args['before'] = '<div class="bbp-topic-tags"><p>';
        return $args;
    }
}
add_filter('bbp_before_get_topic_tag_list_parse_args', 'cb_bbpress_tags' );


if ( ! function_exists( 'cb_bbpress_empty' ) ) {
    function cb_bbpress_empty() {
        return '';
    }
}
add_filter( 'bbp_get_single_forum_description', 'cb_bbpress_empty' );
add_filter( 'bbp_get_single_topic_description', 'cb_bbpress_empty' );


/*********************
SOCIAL SHARING
*********************/
if ( ! function_exists( 'cb_social_sharing' ) ) {
    function cb_social_sharing($post, $cb_style = NULL ) {

            $cb_output = $cb_google_flag = $cb_social_box = NULL;
            $cb_o_twitter = 'horizontal';
            $cb_o_google = 'medium';
            $cb_o_stumble = '1';
            $cb_o_pinterest = 'beside';
            $cb_o_facebook = 'button_count';
            $cb_title = '<div class="cb-title-subtle">' . __('Share On', 'cubell') . ':</div>';
            $cb_twitter_url = 'https://twitter.com/share';
            $cb_social_fb = ot_get_option( 'cb_social_fb', 'on' );
            $cb_social_fb_sh = ot_get_option( 'cb_social_fb_share', 'on' );
            $cb_social_tw = ot_get_option( 'cb_social_tw', 'on' );
            $cb_social_go = ot_get_option( 'cb_social_go', 'on' );
            $cb_social_pi = ot_get_option( 'cb_social_pi', 'on' );
            $cb_social_st = ot_get_option( 'cb_social_st', 'on' );

            if ( $cb_style == 'cb-social-big' ) {
                $cb_o_twitter = 'vertical';
                $cb_o_google = 'tall';
                $cb_o_pinterest = 'above';
                $cb_o_facebook = 'box_count';
                $cb_o_stumble = '5';
                $cb_google_flag = 'cb-tall';
                $cb_social_box = ' cb-social-box';
            }

            $cb_featured_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
            $cb_encoded_img = urlencode( $cb_featured_image_url[0] );
            $cb_encoded_url = urlencode( get_permalink($post->ID) );
            $cb_encoded_desc = urlencode( get_the_title($post->ID) );
            $cb_site_locale = get_locale();

            $cb_output .= '<div class="cb-social-sharing cb-post-footer-block cb-beside' . $cb_social_box . ' clearfix">';

            $cb_output .= $cb_title;
            if ( ( $cb_social_fb != 'off' ) || ( $cb_social_fb_sh != 'off' ) ) {

                $cb_output .=  '<div id="fb-root"></div> <script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/' . $cb_site_locale . '/sdk.js#xfbml=1&version=v2.0"; fjs.parentNode.insertBefore(js, fjs); }(document, "script", "facebook-jssdk"));</script>';

                if ( $cb_social_fb_sh != 'off' ) {
                    $cb_output .= '<div class="cb-facebook"><div class="fb-share-button" data-href="' . get_permalink($post->ID) . '"  data-layout="' . $cb_o_facebook . '"></div></div>';
                }

                if ( $cb_social_fb != 'off' ) {
                    $cb_output .= '<div class="cb-facebook"><div class="fb-like" data-href="' . get_permalink($post->ID) . '" data-layout="' . $cb_o_facebook . '" data-action="like" data-show-faces="false" data-share="false"></div></div>';
                }
                
            }

            if ( $cb_social_pi != 'off' ) {
                $cb_output .= '<div class="cb-pinterest"><script type="text/javascript" src="//assets.pinterest.com/js/pinit.js" async></script>
            <a href="//pinterest.com/pin/create/button/?url=' . $cb_encoded_url . '&media=' . $cb_encoded_img . '&description=' . $cb_encoded_desc . '" data-pin-do="buttonPin" data-pin-config="' . $cb_o_pinterest . '" target="_blank"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a></div>';
            }
            
            if ( $cb_social_go != 'off' ) {
                $cb_output .= '<div class="cb-google ' . $cb_google_flag . '">
                            <div class="g-plusone" data-size="' . $cb_o_google . '"></div>

                            <script type="text/javascript">
                              (function() {
                                var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
                                po.src = "https://apis.google.com/js/plusone.js";
                                var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
                              })();
                            </script></div>';
            }
            
            if ( $cb_social_tw != 'off' ) {
                $cb_output .= '<div class="cb-twitter"><a href="' . esc_url( $cb_twitter_url ) . '" class="twitter-share-button" data-dnt="true"  data-count="' . $cb_o_twitter . '">Tweet</a>';
                $cb_output .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>";
            }
            
            if ( $cb_social_st != 'off' ) {
                $cb_output .= '<su:badge layout="' . $cb_o_stumble . '"></su:badge>
                            <script type="text/javascript">
                              (function() {
                                var li = document.createElement("script"); li.type = "text/javascript"; li.async = true;
                                li.src = ("https:" == document.location.protocol ? "https:" : "http:") + "//platform.stumbleupon.com/1/widgets.js";
                                var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(li, s);
                              })();
                            </script>';
            }
            

            $cb_output .= '</div>';

            return $cb_output;
    }
}

if ( ! function_exists( 'cb_post_format_check' ) ) {
    function cb_post_format_check( $cb_post_id ){

        $cb_post_format = get_post_format($cb_post_id);
        $cb_review_checkbox = get_post_meta( $cb_post_id, 'cb_review_checkbox', true );

        if ( $cb_post_format == 'video' ) {

            $cb_post_format_icon = '<div class="cb-media-icon"><a href="' . get_permalink($cb_post_id) . '"><i class="fa fa-play"></i></a></div>';

        } elseif ( $cb_post_format == 'audio' ) {

            $cb_post_format_icon = '<div class="cb-media-icon"><a href="' . get_permalink($cb_post_id) . '"><i class="fa fa-headphones"></i></a></div>';

        } else  {

            $cb_post_format_icon = NULL;

        }

        if ( $cb_review_checkbox == '1' ) {
             $cb_post_format_icon = NULL;
        }

        return $cb_post_format_icon;
    }
}
/*********************
CLEAN BYLINE
*********************/
if ( ! function_exists( 'cb_byline' ) ) {
    function cb_byline( $cb_cat = true, $cb_post_id = NULL, $cb_short_comment_line = false, $cb_posts_on = false, $cb_post_views_off = false ) {

        if ( $cb_post_id == NULL ) {
            global $post;
            $cb_post_id = $post->ID;
        }

        $cb_meta_onoff = ot_get_option( 'cb_meta_onoff', 'on' );
        $cb_byline_author = ot_get_option( 'cb_byline_author', 'on' );
        $cb_byline_date = ot_get_option( 'cb_byline_date', 'on' );
        $cb_byline_category = ot_get_option( 'cb_byline_category', 'on' );
        $cb_byline_comments = ot_get_option( 'cb_byline_comments', 'on' );
        $cb_byline_postviews = ot_get_option( 'cb_byline_postviews', 'off' );
        $cb_disqus_code = ot_get_option( 'cb_disqus_shortname', NULL );
        $cb_byline = $cb_cat_output = $cb_comments = $cb_post_views = NULL;
        $cb_cats = get_the_category($cb_post_id);

        if ( isset( $cb_cats ) && ( $cb_cat == true ) ) {
            $cb_cat_output = ' <div class="cb-category cb-byline-element"><i class="fa fa-folder"></i> ';
            $i = 1;
            foreach($cb_cats as $category) {
                if ( $i != 1 ) { $cb_cat_output .= ', '; }
                 $cb_cat_output .= ' <a href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", "cubell" ), $category->name ) ) . '">' . $category->cat_name.'</a>';
                 $i++;
            }
            $cb_cat_output .= '</div>';
        }

        if ( $cb_disqus_code == NULL ) {
             if ( get_comments_number( $cb_post_id ) > 0) {
                if ( $cb_short_comment_line == true ) {
                    $cb_comments = ' <div class="cb-comments cb-byline-element"><span><i class="fa fa-comment"></i><a href="' . get_comments_link( $cb_post_id ) . '">' . number_format_i18n( get_comments_number( $cb_post_id ) ) . '</a></span></div>';
                } else {

                    $cb_comment_line = __('Comment', 'cubell' );
                    $cb_comments_line =  __('Comments', 'cubell' );
                    $cb_comments = ' <div class="cb-comments cb-byline-element"><span><i class="fa fa-comment"></i><a href="' . get_comments_link( $cb_post_id ) . '">' . number_format_i18n( get_comments_number( $cb_post_id ) ) . ' ' . get_comments_number_text( $cb_comment_line, $cb_comment_line, $cb_comments_line ) . '</a></span></div>';

                }
            }
        } else {

            $cb_comments = ' <div class="cb-comments cb-byline-element"><span><i class="fa fa-comment"></i><a href="' . get_permalink( $cb_post_id ) . '#disqus_thread"></a></span></div>';

        }
        if ( $cb_post_views_off == false ) {

            $cb_post_view_count = cb_get_post_viewcount( $cb_post_id );
            if ( $cb_post_view_count > 0 ) {
                $cb_post_views = '<div class="cb-post-views cb-byline-element"><span><i class="fa fa-eye"></i> ' . $cb_post_view_count . '</span></div>';
            }
        } 

        $cb_author = '<div class="cb-author cb-byline-element">';
        if ( function_exists( 'coauthors_posts_links' ) ) {
            $cb_author .= '<i class="fa fa-user"></i> ' . coauthors_posts_links( null, null, null, null, false );
        } else {
           $cb_author .= '<i class="fa fa-user"></i> <a href="' . get_author_posts_url(get_the_author_meta( 'ID' )) . '">' . get_the_author() . '</a>';
        }
        $cb_author .= '</div>';

        $cb_date = ' <div class="cb-date cb-byline-element"><i class="fa fa-clock-o"></i> <time class="updated" datetime="' . get_the_time('Y-m-d', $cb_post_id) . '">' . date_i18n( get_option('date_format'), strtotime(get_the_time("Y-m-d", $cb_post_id )) ) . '</time></div>';

        if ( $cb_byline_date == 'off' ) {
            $cb_date = NULL;
        }
        if ( $cb_byline_author == 'off' ) {
            $cb_author = NULL;
        }
        if ( $cb_byline_category == 'off' ) {
            $cb_cat_output = NULL;
        }
        if ( $cb_byline_comments == 'off' ) {
            $cb_comments = NULL;
        }

        if ( $cb_byline_postviews == 'off' ) {
            $cb_post_views = NULL;
        }

        if ( ( $cb_meta_onoff == 'on' ) || ( $cb_posts_on == true ) ) {
            $cb_byline = '<div class="cb-byline">' . $cb_author . $cb_date . $cb_cat_output . $cb_comments . $cb_post_views . '</div>';
        }

        return $cb_byline;
    }
}

/*********************
 REVIEW SCORE BOXES
*********************/
if ( ! function_exists( 'cb_add_content' ) ) {
    function cb_add_content($content) {

        global $post, $multipage, $numpages, $page;
        $cb_post_id = $post->ID;
        $cb_post_types = get_post_type();
        $cb_review_placement = get_post_meta( $cb_post_id, 'cb_placement', true );

        if ( $multipage == true ) {

            if ( $page == $numpages ) {

                if ( $cb_review_placement == 'bottom' ){
                    $content .= cb_review_boxes($post);
                }
            }

            if ( $page == '1' ) {

                if ( ( $cb_review_placement == 'top' ) || ( $cb_review_placement == 'top-half' ) ){

                    $content = cb_review_boxes($post) . $content;

                }
            }

        } else {

            if ( ( $cb_review_placement == 'top' ) || ( $cb_review_placement == 'top-half' ) ){

                $content = cb_review_boxes($post) . $content;

            } elseif ( $cb_review_placement == 'bottom' ){
                $content .= cb_review_boxes($post);
            }
        }

        return $content;
    }
}
add_filter( 'the_content', 'cb_add_content' );

// Review Score Box
if ( ! function_exists( 'cb_review_boxes' ) ) {
    function cb_review_boxes($post){

        $cb_post_id = $post->ID;
        $cb_custom_fields = get_post_custom();
        $cb_rating_short_summary = $cb_score_subtitle = NULL;
        $cb_review_checkbox = get_post_meta( $cb_post_id, 'cb_review_checkbox', true );

        if ( $cb_review_checkbox == '1' ) {
             $cb_review_checkbox = 'on';
        } else {
             $cb_review_checkbox = 'off';
        }

        if ( $cb_review_checkbox == 'on' ) {

                $cb_pro_1 = $cb_pro_2 = $cb_pro_3 = $cb_con_1 = $cb_con_2 = $cb_con_3 = $cb_cons_title = $cb_pros_title = NULL;
                $cb_review_type = get_post_meta($cb_post_id, 'cb_user_score', 'cb-both' );
                $cb_score_display_type = get_post_meta($cb_post_id, 'cb_score_display_type', true );
                $cb_user_score = get_post_meta( $cb_post_id, 'cb_user_score_output', true);
                if ( isset ( $cb_custom_fields['cb_ct1'][0] ) ) { $cb_rating_1_title = $cb_custom_fields['cb_ct1'][0]; }
                if ( isset ( $cb_custom_fields['cb_cs1'][0] ) ) { $cb_rating_1_score = $cb_custom_fields['cb_cs1'][0]; }
                if ( isset ( $cb_custom_fields['cb_ct2'][0] ) ) { $cb_rating_2_title = $cb_custom_fields['cb_ct2'][0]; }
                if ( isset ( $cb_custom_fields['cb_cs2'][0] ) ) { $cb_rating_2_score = $cb_custom_fields['cb_cs2'][0]; }
                if ( isset ( $cb_custom_fields['cb_ct3'][0] ) ) { $cb_rating_3_title = $cb_custom_fields['cb_ct3'][0]; }
                if ( isset ( $cb_custom_fields['cb_cs3'][0] ) ) { $cb_rating_3_score = $cb_custom_fields['cb_cs3'][0]; }
                if ( isset ( $cb_custom_fields['cb_ct4'][0] ) ) { $cb_rating_4_title = $cb_custom_fields['cb_ct4'][0]; }
                if ( isset ( $cb_custom_fields['cb_cs4'][0] ) ) { $cb_rating_4_score = $cb_custom_fields['cb_cs4'][0]; }
                if ( isset ( $cb_custom_fields['cb_ct5'][0] ) ) { $cb_rating_5_title = $cb_custom_fields['cb_ct5'][0]; }
                if ( isset ( $cb_custom_fields['cb_cs5'][0] ) ) { $cb_rating_5_score = $cb_custom_fields['cb_cs5'][0]; }
                if ( isset ( $cb_custom_fields['cb_ct6'][0] ) ) { $cb_rating_6_title = $cb_custom_fields['cb_ct6'][0]; }
                if ( isset ( $cb_custom_fields['cb_cs6'][0] ) ) { $cb_rating_6_score = $cb_custom_fields['cb_cs6'][0]; }
                if ( isset ( $cb_custom_fields['cb_pros_title'][0] ) ) { $cb_pros_title = '<div class="cb-title">' . $cb_custom_fields['cb_pros_title'][0] . '</div>'; }
                if ( isset ( $cb_custom_fields['cb_cons_title'][0] ) ) { $cb_cons_title = '<div class="cb-title">' . $cb_custom_fields['cb_cons_title'][0] . '</div>'; }
                if ( isset ( $cb_custom_fields['cb_pro_1'][0] ) ) { $cb_pro_1 = '<li>' . $cb_custom_fields['cb_pro_1'][0] . '</li>'; }
                if ( isset ( $cb_custom_fields['cb_pro_2'][0] ) ) { $cb_pro_2 = '<li>' . $cb_custom_fields['cb_pro_2'][0] . '</li>'; }
                if ( isset ( $cb_custom_fields['cb_pro_3'][0] ) ) { $cb_pro_3 = '<li>' . $cb_custom_fields['cb_pro_3'][0] . '</li>'; }
                if ( isset ( $cb_custom_fields['cb_con_1'][0] ) ) { $cb_con_1 = '<li>' . $cb_custom_fields['cb_con_1'][0] . '</li>'; }
                if ( isset ( $cb_custom_fields['cb_con_2'][0] ) ) { $cb_con_2 = '<li>' . $cb_custom_fields['cb_con_2'][0] . '</li>'; }
                if ( isset ( $cb_custom_fields['cb_con_3'][0] ) ) { $cb_con_3 = '<li>' . $cb_custom_fields['cb_con_3'][0] . '</li>'; }

                $cb_summary = get_post_meta($cb_post_id, 'cb_summary', true );
                $cb_final_score = get_post_meta($cb_post_id, 'cb_final_score', true );
                $cb_final_score_override = get_post_meta($cb_post_id, 'cb_final_score_override', true );
                $cb_rating_short_summary = get_post_meta($cb_post_id, 'cb_rating_short_summary', true );
                $cb_rating_short_summary_in = get_post_meta($cb_post_id, 'cb_rating_short_summary_in', true );
                $cb_review_placement = get_post_meta($cb_post_id, 'cb_placement', true );

                if ( $cb_final_score_override != NULL ) {
                   $cb_final_score = $cb_final_score_override;
                }

                $cb_5_stars = '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
                $cb_ul = '<ul>';
                $cb_ul_closer = '</ul>';
                if ( ( $cb_pro_1 != NULL ) || ( $cb_pro_2  != NULL ) || ($cb_pro_3 != NULL ) ) {
                        $cb_pros = compact('cb_pros_title', 'cb_ul', 'cb_pro_1', 'cb_pro_2', 'cb_pro_3', 'cb_ul_closer' );
                }
                if ( ( $cb_con_1  != NULL ) || ($cb_con_2 != NULL ) || ($cb_con_3 != NULL )) {
                    $cb_cons = compact('cb_cons_title', 'cb_ul', 'cb_con_1', 'cb_con_2', 'cb_con_3', 'cb_ul_closer');
                }

                if ( $cb_review_placement == 'top-half' ) {
                    $cb_review_placement_ret = ' cb-half';
                }  elseif ( $cb_review_placement == 'top' ) {
                    $cb_review_placement_ret = ' cb-top-review-box';
                } else {
                    $cb_review_placement_ret =  NULL;
                }

                $cb_review_final_score = intval($cb_final_score);

                $cb_ratings = array();
            }

        if ( $cb_review_checkbox == 'on')  {

            $cb_star_overlay = $cb_star_bar = NULL;

            if ( $cb_score_display_type == 'percentage' ) {

                 $cb_best_rating = '100';
                 $cb_score_output = $cb_review_final_score . '<span class="h2">%</span>';
                 $cb_user_score_output =  $cb_user_score . '<span class="h2">%</span>';

                 for( $i = 1; $i < 7; $i++ ) {
                     if (isset(${"cb_rating_". $i."_score"})) { $cb_ratings[] =  ${"cb_rating_". $i."_score"} . '%';}
                  }
            }

            if ( $cb_score_display_type == 'points' ) {

                $cb_best_rating = '10';
                $cb_score_output = $cb_review_final_score / 10;
                $cb_user_score_output = $cb_user_score / 10;
                
                for ( $i = 1; $i < 7; $i++ ) {
                    if ( isset(${"cb_rating_". $i."_score"}) ) { $cb_ratings[] =  ${"cb_rating_". $i."_score"} / 10;}
                }
            }

            if ( $cb_score_display_type == 'stars' ) {

                $cb_star_overlay = '-stars';
                $cb_star_bar = ' cb-stars';
                $cb_best_rating = '5';
                $cb_review_final_score =  number_format( ( $cb_review_final_score / 20), 1 );
                $cb_user_score_output =  number_format( ( $cb_user_score / 20), 1 );
                $cb_score_output = $cb_review_final_score;
                for ( $i = 1; $i < 7; $i++ ) {
                    
                    if ( isset(${"cb_rating_". $i."_score"}) ) {
                        $cb_ratings[] = ${"cb_rating_". $i."_score"};
                    }
                 }
            }

            if ( $cb_rating_short_summary_in == NULL ) {
                $cb_rating_short_summary_in = __( 'Overall Score', 'cubell' );
            }

            if ( $cb_review_type == 'cb-readers' ) {
                $cb_final_score = $cb_user_score;
            }
            

            $cb_score_subtitle = '<span class="score-title">' .  $cb_rating_short_summary_in  . '</span>';

            if ( $cb_score_display_type == 'stars' ) { 
                $cb_score_subtitle .= '<span class="cb-overlay' . $cb_star_overlay . '">' . $cb_5_stars . '<span class="cb-opacity cb-zero-stars-trigger" style="width:' . (100 - $cb_final_score ) . '%"></span></span>'; 
            }

            $cb_review_ret = '<div class="cb-review-box' . $cb_review_placement_ret . ' ' . $cb_review_type . ' clearfix">';

            $cb_review_ret .= '<div id="cb-review-title" class="entry-title">' . $post->post_title . '</div>';

            if ( ( $cb_summary != NULL ) && ( $cb_review_placement == 'bottom' ) ) { $cb_review_ret .= '<div class="cb-summary"><div id="cb-conclusion">' . $cb_summary.'</div></div>'; }


             if ( $cb_review_type != 'cb-readers' )  {

                for ( $j = 1; $j < 7; $j++ ) {
                    
                    $k = ( $j - 1 );

                    if ( ( isset( ${"cb_rating_". $j."_title"}) ) && ( isset( ${"cb_rating_". $j."_score"}) ) ) {

                        $cb_review_ret .= '<div class="cb-bar' . $cb_star_bar . '"><span class="cb-criteria">' . ${"cb_rating_" . $j . "_title"} . '</span>';

                        if ( $cb_score_display_type != 'stars' ) {
                            $cb_review_ret .=  '<span class="cb-criteria-score">' . $cb_ratings[$k] . '</span>';
                            $cb_review_ret .= '<span class="cb-overlay"><span class="cb-zero-trigger" style="width:' . ( ${"cb_rating_". $j."_score"}) . '%"></span></span></div>';
                        } else {
                            $cb_review_ret .= '<span class="cb-overlay' . $cb_star_overlay . '">' . $cb_5_stars.'<span class="cb-opacity cb-zero-stars-trigger" style="width:' . ( 100 - ${"cb_rating_". $j."_score"}) . '%"></span></span></div>';
                        }
                    }
                }

                if ( isset( $cb_pros ) && ( $cb_review_placement != 'top-half' ) ) { 

                    $cb_review_ret .= '<div class="cb-pros-cons cb-pros-list">';
                    foreach ( $cb_pros as $cb_item ) { 
                        $cb_review_ret .= $cb_item; 
                    }
                    $cb_review_ret .= '</div>';
                }

                if ( isset( $cb_cons ) && ( $cb_review_placement != 'top-half' ) ) { 

                    $cb_review_ret .= '<div class="cb-pros-cons cb-cons-list">';
                    foreach ( $cb_cons as $cb_item ) { 
                        $cb_review_ret .= $cb_item; 
                    }
                    $cb_review_ret .= '</div>';
                }

                $cb_review_ret .= '<time class="updated cb-hide" datetime="' . get_the_time('Y-m-d', $cb_post_id) . '">' .  get_the_time('Y-m-d', $cb_post_id) . '</time><div class="cb-score-box' . $cb_star_bar . ' clearfix" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><meta itemprop="worstRating" content="0"><meta itemprop="bestRating" content="' . $cb_best_rating . '"><span class="score" itemprop="ratingValue">' . $cb_score_output . '</span>' . $cb_score_subtitle . '</div>';
            }
            
            if (  ( $cb_review_type == 'cb-both' ) || ( $cb_review_type == 'cb-readers' ) || ( $cb_review_type == 'on' ) ) {

                $cb_number_votes = get_post_meta( $cb_post_id, 'cb_votes', true );
                if ( $cb_number_votes == NULL) { $cb_number_votes = 0; }
                if ( $cb_user_score == NULL) { $cb_user_score = 0; }
                if ( $cb_score_display_type == "points" ) { $cb_average_score = '<div class="cb-criteria-score cb-average-score">' .  number_format(floatval($cb_user_score / 10 ), 1) . '</div>';  }
                if ( $cb_score_display_type == "percentage" ) { $cb_average_score = '<div class="cb-criteria-score cb-average-score">' . $cb_user_score . '%</div>'; }

                if ( isset($_COOKIE["cb_user_rating"]) ) {
                     $cb_class = " cb-voted";
                     $cb_tip_class = ' cb-tip-bot';
                     $cb_tip_title = 'title="' . __('You have already voted', 'cubell') . '"';
                } else {
                     $cb_class = $cb_tip_title = $cb_tip_class = NULL;
                }

                if ( $cb_number_votes == '1' ) {
                    $cb_vote_votes = __("Vote", "cubell");
                }  else {
                    $cb_vote_votes = __("Votes", "cubell");
                }

                if ( $cb_review_type == 'cb-readers' ) {

                    $cb_review_ret .= '<div class="cb-score-box' . $cb_star_bar . ' cb-readers-only clearfix" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"><meta itemprop="worstRating" content="0"><meta itemprop="bestRating" content="' . $cb_best_rating . '"><meta itemprop="reviewCount" content="' . $cb_number_votes . '"><span class="score" itemprop="ratingValue">' . $cb_user_score_output . '</span>' . $cb_score_subtitle . '</div>';
                }

                $cb_review_ret .= '<div class="cb-bar cb-user-rating' . $cb_star_bar . '"><div id="cb-vote" class="bg ' . $cb_score_display_type . $cb_class . '"><span class="cb-criteria">' . __("Reader Rating", "cubell"). ': (<span>' . $cb_number_votes . '</span> ' . $cb_vote_votes . ')</span>';

                if ($cb_score_display_type == 'stars') {
                         $cb_review_ret .= '<span class="cb-overlay' . $cb_star_overlay . ' cb' . $cb_star_overlay . $cb_tip_class. '"' . $cb_tip_title . '>' . $cb_5_stars . '<span class="cb-opacity" style="width:' . ( 100 - $cb_user_score).'%"></span></span></div></div>';
                 } else {
                         $cb_review_ret .= $cb_average_score. '<span class="cb-overlay' . $cb_tip_class . '"' . $cb_tip_title . '><span style="width:' . $cb_user_score . '%"></span></span></div></div>';
                 }

                 if ( function_exists('wp_nonce_field') ) { $cb_review_ret .= wp_nonce_field( 'voting_nonce', 'voting_nonce', true, false ); }
            }

            $cb_review_ret .= '</div><!-- /cb-review-box -->';

            return $cb_review_ret;
        }
    }
}

// Review Score Box Outside of Post
if ( ! function_exists( 'cb_review_ext_box' ) ) {
    function cb_review_ext_box($cb_post_id, $cb_category_color = NULL, $cb_small_box = false){

        $cb_rating_short_summary = $cb_score_subtitle = NULL;
        if ( $cb_category_color == NULL ) { $cb_category_color = '#222'; }

        $cb_rating_short_summary = $cb_score_subtitle = NULL;
        $cb_review_checkbox = get_post_meta($cb_post_id, 'cb_review_checkbox', true );

        if ( $cb_review_checkbox == '1' ) {
             $cb_review_checkbox = 'on';
        } else {
             $cb_review_checkbox = 'off';
        }

        if ( $cb_review_checkbox == 'on' ) {

                $cb_review_type = get_post_meta($cb_post_id, 'cb_user_score', 'cb-both' );
                $cb_score_display_type = get_post_meta($cb_post_id, 'cb_score_display_type', true );
                $cb_user_score = get_post_meta( $cb_post_id, 'cb_user_score_output', true);
                $cb_final_score = get_post_meta($cb_post_id, 'cb_final_score', true );
                $cb_final_score_override = get_post_meta($cb_post_id, 'cb_final_score_override', true );
                $cb_rating_short_summary = get_post_meta($cb_post_id, 'cb_rating_short_summary', true );

                if ( $cb_final_score_override != NULL ) {
                   $cb_final_score = $cb_final_score_override;
                }
                
                if ( $cb_review_type == 'cb-readers' ) {

                    $cb_final_score = $cb_user_score;

                }

                $cb_small_box_output = NULL;

                // Set final scores
                $cb_review_final_score = intval($cb_final_score);

                if ( $cb_score_display_type == 'percentage' ) {
                     $cb_score_output = $cb_review_final_score . '<span class="cb-percent-sign">%</span>';
                }

                if ( $cb_score_display_type == 'points' ) {
                    $cb_score_output = $cb_review_final_score / 10;
                }

                if ( $cb_score_display_type == 'stars' ) {
                    $cb_review_final_score =  $cb_review_final_score / 20;
                    $cb_score_output = number_format($cb_review_final_score, 1);
                }

                if ( isset( $cb_rating_short_summary ) ) { $cb_score_subtitle =  '<span class="cb-score-title">' . $cb_rating_short_summary . '</span>'; }
                if ( $cb_small_box == true ) { $cb_small_box_output = ' cb-small-box';}

                $cb_review_ret = '<div class="cb-review-ext-box' . $cb_small_box_output . '"><span class="cb-bg" style="background:' . $cb_category_color . ';"></span><span class="cb-score">' . $cb_score_output . '</span>' . $cb_score_subtitle . '</div>';

                return $cb_review_ret;
       }
    }
}

// User Rating System
if ( ! function_exists( 'cb_user_rating' ) ) {
    function cb_user_rating() {
        if ( is_single() ) {
            global $post;
            echo " <script type='text/javascript'>
            (function ($) {'use strict';
                var cbVote = $('#cb-vote'),
                    cbCriteriaAverage = $('.cb-criteria-score.cb-average-score'),
                    cbVoteCriteria = cbVote.find('.cb-criteria'),
                    cbVoteOverlay = cbVote.find('.cb-overlay'),
                    cbBodyRTL;

                    if ( $('body').hasClass('rtl') ) {
                        cbBodyRTL = true;
                    } else {
                        cbBodyRTL = false;
                    }

                    if  ($(cbVoteOverlay).length) {

                        var cbExistingOverlaySpan = cbVoteOverlay.find('span'),
                            cbNotVoted = cbVote.not('.cb-voted').find('.cb-overlay'),
                            cbExistingOverlay = cbExistingOverlaySpan.css('width');
                        cbExistingOverlaySpan.addClass('cb-zero-trigger');

                    } else {

                        var cbVoteOverlay = cbVote.find('.cb-overlay-stars'),
                            cbNotVote = cbNotVoted = cbVote.not('.cb-voted').find('.cb-overlay-stars'),
                            cbExistingOverlaySpan = cbVoteOverlay.find('span'),
                            cbExistingOverlay = cbExistingOverlaySpan.css('width');

                        if (cbExistingOverlay !== '125px') {  cbExistingOverlaySpan.addClass('cb-zero-stars-trigger'); }
                    }

                var cbExistingScore =  $(cbCriteriaAverage).text(),
                    cbWidthDivider = ($(cbVote).width() / 100),
                    cbStarWidthDivider = $(cbVoteOverlay).width() / 100,
                    cbExistingVoteLine = $(cbVoteCriteria).html(),
                    cbVoteAmount  = $(cbVote).find('.cb-criteria span').text();

                if ( typeof cbExistingVoteLine !== 'undefined' ) {
                    var cbExistingVotedLine = cbExistingVoteLine.substr(0, cbExistingVoteLine.length-1) + ')';
                }

                if ( (cbVoteAmount) === '0' ) {
                    var cbVotedLineChanged = '". __('Reader Rating', 'cubell') .": (' + (parseInt(cbVoteAmount) + 1) + ' ". __('Vote', 'cubell') .")';
                } else {
                    var cbVotedLineChanged = '". __('Reader Rating', 'cubell') .": (' + (parseInt(cbVoteAmount) + 1) + ' ". __('Votes', 'cubell') .")';
                }

                if (cbVote.hasClass('cb-voted')) {
                    cbVote.find('.cb-criteria').html(cbExistingVotedLine);
                }

                cbNotVoted.on('mousemove click mouseleave mouseenter', function(e) {

                    var cbParentOffset = $(this).parent().offset();
                    var cbStarOffset = $(this).offset();

                      if ( cbVote.hasClass('stars') ) {

                       if (Math.round(cbStarOffset.left) <= e.pageX) {

                            var cbBaseX = Math.round( ( ( e.pageX - Math.round(cbStarOffset.left) ) / cbStarWidthDivider )   );
                            var cbFinalX = (Math.round(cbBaseX * 10 / 20) / 10).toFixed(1);
                            if ((cbFinalX <= 5 && cbFinalX >= 0)) {

                                if ( cbExistingOverlaySpan.hasClass('cb-bar-ani-stars') ) {

                                    cbExistingOverlaySpan.removeClass('cb-bar-ani-stars').css( 'width', (100 - (cbBaseX) +'%') );;
                                }
                                if ( cbBodyRTL == true ) {
                                    cbFinalX = (Math.round(( 100 - cbBaseX) * 10 / 20) / 10).toFixed(1);
                                    cbExistingOverlaySpan.css( 'width', ((cbBaseX) +'%') );
                                } else {
                                    cbExistingOverlaySpan.css( 'width', (100 - (cbBaseX) +'%') );
                                }
                                
                            }

                           if (cbFinalX < 0) { var cbFinalX = 0; }
                           if (cbFinalX > 5) { var cbFinalX = 5; }
                        }

                    } else if ( cbVote.hasClass('percentage') ) {

                        var cbBaseX = Math.ceil((e.pageX - cbParentOffset.left) / cbWidthDivider),
                            cbFinalX;

                        if ( cbExistingOverlaySpan.hasClass('cb-bar-ani') ) {
                            cbExistingOverlaySpan.removeClass('cb-bar-ani');
                        }

                        if ( cbBodyRTL == true ) {
                            cbFinalX = ( 100 - cbBaseX ) + '%';
                            cbExistingOverlaySpan.css( 'width', ( cbFinalX ) );
                            cbCriteriaAverage.text( cbFinalX );
                        } else {
                            cbFinalX = cbBaseX + '%';
                            cbExistingOverlaySpan.css( 'width', cbFinalX );
                            cbCriteriaAverage.text( cbFinalX );
                        }

                    } else if( cbVote.hasClass('points') ) {

                        var cbBaseX = Math.ceil((e.pageX - cbParentOffset.left) / cbWidthDivider);
                        var cbFinalX;
                        
                        if ( cbExistingOverlaySpan.hasClass('cb-bar-ani') ) {
                            cbExistingOverlaySpan.removeClass('cb-bar-ani');
                        }

                        if ( cbBodyRTL == true ) {
                            cbFinalX = ( ( 100 - cbBaseX ) / 10).toFixed(1);
                            cbExistingOverlaySpan.css( 'width', ( ( 100 - cbBaseX ) +'%') );
                            cbCriteriaAverage.text( cbFinalX );
                        } else {
                            cbFinalX = (cbBaseX / 10).toFixed(1);
                            cbExistingOverlaySpan.css( 'width', (cbBaseX +'%') );
                            cbCriteriaAverage.text(cbFinalX);
                        }
                    }

                    if ( e.type == 'mouseenter' ) {
                        cbVoteCriteria.fadeOut(75, function () {
                            $(this).fadeIn(75).text('".__('Your Rating', 'cubell')."');
                        });
                    }
                    if ( e.type == 'mouseleave' ) {
                        cbExistingOverlaySpan.animate( {'width': cbExistingOverlay}, 300);
                        cbCriteriaAverage.text(cbExistingScore);
                        cbVoteCriteria.fadeOut(75, function () {
                            $(this).fadeIn(75).html(cbExistingVoteLine);
                        });
                    }

                    if ( e.type == 'click' ) {

                        if ( cbVote.hasClass('points') ) { var cbFinalX = cbFinalX * 10; }
                        if ( cbVote.hasClass('stars') ) { var cbFinalX = cbFinalX * 20; }

                        cbVoteCriteria.fadeOut(550, function () {  $(this).fadeIn(550).html(cbVotedLineChanged);  });

                        var cbParentOffset = $(this).parent().offset(),
                            nonce = $('input#voting_nonce').val(),
                            cb_data_votes = { action: 'cb_vote_counter', nonce: nonce, postid: '". $post->ID ."' },
                            cb_data_score = { action: 'cb_add_user_score', nonce: nonce, cbCurrentVotes: parseInt(cbVoteAmount), cbNewScore: cbFinalX, postid: '". $post->ID ."' };

                        cbVoteOverlay.off('mousemove click mouseleave mouseenter');

                        $.post('". admin_url('admin-ajax.php'). "', cb_data_votes, function(cb_votes) {
                            if ( cb_votes !== '-1' ) {

                                if ( cookie.enabled() ) {
                                    cookie.set('cb_user_rating', '1', { expires: 28, });
                                }
                            }
                        });

                        $.post('". admin_url('admin-ajax.php') ."', cb_data_score, function(cb_score) {

                            if ( ( cb_score !== '-1' ) && ( cb_score !=='null' ) ) {

                                var cbScoreOverlay = cb_score;

                                if ( cbVote.hasClass('points') ) {

                                    cbCriteriaAverage.html( (cb_score / 10).toFixed(1) );


                                } else if ( cbVote.hasClass('percentage') ) {

                                    cbCriteriaAverage.html(cb_score + '%');

                                } else {

                                    var cbScoreOverlay = 100 - cbScoreOverlay;
                                }

                                cbExistingOverlaySpan.css( 'width', cbScoreOverlay +'%' );
                                cbVote.addClass('cb-voted');
                                cbVoteOverlay.addClass('cb-tip-bot').attr('title', '". __('You have already voted', 'cubell') . "');
                                cbVote.off('click');
                            }
                        });

                        return false;
                   }
                });
            })(jQuery);
            </script>";
        }
    }
}

if ( ! function_exists( 'cb_vote_counter' ) ) {
    function cb_vote_counter() {
        if ( ! wp_verify_nonce($_POST['nonce'], 'voting_nonce') ) { return; }

        $cb_post_id = $_POST['postid'];
        $cb_current_votes = get_post_meta($cb_post_id, "cb_votes", true);

        if ($cb_current_votes == NULL) {
             $cb_current_votes = 0;
        }

        $cb_current_votes = intval($cb_current_votes);
        $cb_new_votes = $cb_current_votes + 1;

        update_post_meta($cb_post_id, 'cb_votes', $cb_new_votes);

        die(0);
    }
}
add_action('wp_ajax_cb_vote_counter', 'cb_vote_counter');
add_action('wp_ajax_nopriv_cb_vote_counter', 'cb_vote_counter');

if ( ! function_exists( 'cb_add_user_score' ) ) {
    function cb_add_user_score() {

        if ( ! wp_verify_nonce($_POST['nonce'], 'voting_nonce')) { return; }

        $cb_post_id = $_POST['postid'];
        $cb_latest_score = $_POST['cbNewScore'];
        $cb_current_votes = $_POST['cbCurrentVotes'];

        $current_score = get_post_meta($cb_post_id, "cb_user_score_output", true);
        $cb_score_type = get_post_meta($cb_post_id, "cb_score_display_type", true);

        if ($cb_current_votes == NULL) {
            $cb_current_votes = 0;
        }

        $cb_current_votes = intval($cb_current_votes);
        $current_score = intval($current_score);


        if ($cb_current_votes == 0) {
            $cb_new_score = intval( $cb_latest_score );
        }

        if ($cb_current_votes == 1) {
            $cb_new_score = (intval( $current_score + $cb_latest_score ) ) / 2;
        }
        if ($cb_current_votes > 1) {
            $current_score_total = ($current_score * $cb_current_votes );
            $cb_new_score = intval( ($current_score_total + $cb_latest_score) / ($cb_current_votes + 1) );
        }
        if ($cb_score_type == 'percentage') {
            $cb_new_score  = round($cb_new_score);
        }

        update_post_meta($cb_post_id, 'cb_user_score_output', $cb_new_score);
        echo $cb_new_score;
        die(0);
    }
}
add_action('wp_ajax_cb_add_user_score', 'cb_add_user_score');
add_action('wp_ajax_nopriv_cb_add_user_score', 'cb_add_user_score');

/*********************
AUTHOR FUNCTIONS
*********************/
if ( ! function_exists( 'cb_extra_profile_about_us' ) ) {
    function cb_extra_profile_about_us( $cb_user ) {

        $cb_saved = get_the_author_meta( 'cb_order', $cb_user->ID );
        $cb_current_user = get_current_user_id();
        $cb_user_info = get_userdata( $cb_current_user );

        if ( ( $cb_user_info->user_level ) > 8  && ( is_admin() == true ) ) {
?>
            <h3 class="cb-about-options-title">Meet The Team Page Template Options</h3>
            <table class="form-table cb-about-options">
                <tr>
                    <th><label>Show User On Template</label></th>
                    <td>
                        <input type="checkbox" name="cb_show_author" id="cb_show_author" value="true" <?php if (esc_attr( get_the_author_meta( "cb_show_author", $cb_user->ID )) == "true") echo "checked"; ?> />
                    </td>
                </tr>
                <tr>
                    <th><label for="dropdown">Template Order Override</label></th>
                    <td>
                        <select name="cb_order" id="cb_order">
                            <option value="0" <?php if ($cb_saved == "0") { echo  'selected="selected"'; } ?>>Alphabetical</option>
                            <option value="1" <?php if ($cb_saved == "1") { echo  'selected="selected"'; } ?>>1</option>
                            <option value="2" <?php if ($cb_saved == "2") { echo  'selected="selected"'; } ?>>2</option>
                            <option value="3" <?php if ($cb_saved == "3") { echo  'selected="selected"'; } ?>>3</option>
                            <option value="4" <?php if ($cb_saved == "4") { echo  'selected="selected"'; } ?>>4</option>
                            <option value="5" <?php if ($cb_saved == "5") { echo  'selected="selected"'; } ?>>5</option>
                        </select>
                    </td>
                </tr>
            </table>
<?php   }
    }
}
add_action( 'show_user_profile', 'cb_extra_profile_about_us' );
add_action( 'edit_user_profile', 'cb_extra_profile_about_us' );

if ( ! function_exists( 'cb_extra_profile_about_us_save' ) ) {
    function cb_extra_profile_about_us_save( $cb_user ) {

        $cb_current_user = get_current_user_id();
        $cb_user_info = get_userdata($cb_current_user);

        if ( ( $cb_user_info->user_level ) > 8 && ( is_admin() == true ) ) {

            if ( !current_user_can( 'edit_user', $cb_user ) ) { return false; }

            update_user_meta( $cb_user, 'cb_show_author', $_POST['cb_show_author'] );
            update_user_meta( $cb_user, 'cb_order', $_POST['cb_order'] );
        }
    }
}

add_action( 'personal_options_update', 'cb_extra_profile_about_us_save' );
add_action( 'edit_user_profile_update', 'cb_extra_profile_about_us_save' );

if ( ! function_exists( 'cb_contact_data' ) ) {
    function cb_contact_data($contactmethods) {

        unset($contactmethods['aim']);
        unset($contactmethods['yim']);
        unset($contactmethods['jabber']);
        if ( is_admin() == true ) {
            $contactmethods['publicemail'] = 'Public Email';
            $contactmethods['position'] = 'Position';
        }
        $contactmethods['twitter'] = 'Twitter Username';
        $contactmethods['googleplus'] = 'Google+ (Entire URL)';

        return $contactmethods;
    }
}
add_filter('user_contactmethods', 'cb_contact_data');

if ( ! function_exists( 'cb_author_details' ) ) {
    function cb_author_details($cb_author_id, $cb_desc = true) {

        $cb_author_email = get_the_author_meta('publicemail', $cb_author_id);
        $cb_author_name = get_the_author_meta('display_name', $cb_author_id);
        $cb_author_position = get_the_author_meta('position', $cb_author_id);
        $cb_author_tw = get_the_author_meta('twitter', $cb_author_id);
        $cb_author_go = get_the_author_meta('googleplus', $cb_author_id);
        $cb_author_www = get_the_author_meta('url', $cb_author_id);
        $cb_author_desc = get_the_author_meta('description', $cb_author_id);

        $cb_author_output = NULL;
        $cb_author_output .= '<div class="cb-author-details clearfix"><div class="cb-mask"><a href="' . get_author_posts_url($cb_author_id) . '">' . get_avatar($cb_author_id, '200').'</a></div><div class="cb-meta"><h3><a href="' . get_author_posts_url($cb_author_id) . '">' . $cb_author_name.'</a></h3>';

        if ( $cb_author_position != NULL ) { $cb_author_output .= '<div class="cb-author-position">' . $cb_author_position.'</div>';}

        if ( ( $cb_author_email != NULL ) || ( $cb_author_www != NULL ) || ( $cb_author_tw != NULL ) || ( $cb_author_go != NULL ) ) {$cb_author_output .= '<div class="cb-author-page-contact">';}
        if ( $cb_author_email != NULL ) { $cb_author_output .= '<a href="mailto:' . sanitize_email( $cb_author_email ) . '"><i class="fa fa-envelope-o cb-tip-bot" title="'.__('Email', 'cubell') . '"></i></a>'; }
        if ( $cb_author_www != NULL ) { $cb_author_output .= ' <a href="' . esc_url( $cb_author_www ) . '" target="_blank"><i class="fa fa-link cb-tip-bot" title="'.__('Website', 'cubell') . '"></i></a> '; }
        if ( $cb_author_tw != NULL ) { $cb_author_output .= ' <a href="//www.twitter.com/' . $cb_author_tw . '" target="_blank" ><i class="fa fa-twitter cb-tip-bot" title="Twitter"></i></a>'; }
        if ( $cb_author_go != NULL ) { $cb_author_output .= ' <a href="' . esc_url( $cb_author_go ) . '" rel="publisher" target="_top" title="Google+" class="cb-googleplus cb-tip-bot" ><img src="//ssl.gstatic.com/images/icons/gplus-32.png"  data-at2x="//ssl.gstatic.com/images/icons/gplus-64.png" alt="Google+" ></a>'; }
        if ( ( $cb_author_email != NULL ) || ( $cb_author_www != NULL ) || ( $cb_author_go != NULL ) || ( $cb_author_tw != NULL ) ) {$cb_author_output .= '</div>';}

        if ( ( $cb_author_desc != NULL ) && ( $cb_desc == true ) ) { $cb_author_output .= '<p class="cb-author-bio">' . $cb_author_desc . '</p>'; }
        $cb_author_output .= '</div></div>';

        return $cb_author_output;
    }
}

if ( ! function_exists( 'cb_author_box' ) ) {
    function cb_author_box( $post, $cb_author_id_sc = NULL, $cb_block_title = NULL ) {

        if ( $cb_author_id_sc == NULL ) {
            $cb_author_id = $post->post_author;
        } else {
            $cb_author_id = $cb_author_id_sc;
        }

        $cb_author_email = get_the_author_meta('publicemail', $cb_author_id);
        $cb_author_name = get_the_author_meta('display_name', $cb_author_id);
        $cb_author_position = get_the_author_meta('position', $cb_author_id);
        $cb_author_tw = get_the_author_meta('twitter', $cb_author_id);
        $cb_author_go = get_the_author_meta('googleplus', $cb_author_id);
        $cb_author_www = get_the_author_meta('url', $cb_author_id);
        $cb_author_desc = get_the_author_meta('description', $cb_author_id);

        $cb_author_output = NULL;

        if ( $cb_block_title == NULL ) {
            $cb_author_output .= '<div id="cb-author-box" class="clearfix"><h3 class="cb-block-title">' . __('About The Author', 'cubell') . '</h3><div class="cb-mask"><a href="' . get_author_posts_url( $cb_author_id ) . '">' . get_avatar( $cb_author_id, '120' ) . '</a>';
        } else {
            $cb_author_output .= '<div id="cb-author-box" class="clearfix"><h3 class="cb-block-title">' . $cb_block_title . '</h3><div class="cb-mask"><a href="' . get_author_posts_url( $cb_author_id ) . '">' . get_avatar( $cb_author_id, '120' ) . '</a>';
        }

        $cb_author_output .= '</div><div class="cb-meta"><div class="cb-info">';

        $cb_author_output .= '<div class="cb-author-title vcard" itemprop="author"><a href="' . get_author_posts_url( $cb_author_id ) . '"><span class="fn">' . $cb_author_name . '</span></a></div>';
        if ( $cb_author_position != NULL ) { $cb_author_output .= '<span class="cb-author-position"><i class="fa fa-long-arrow-right"></i>' . $cb_author_position.'</span>';}
        if ( ( $cb_author_email != NULL ) || ( $cb_author_www != NULL ) || ( $cb_author_tw != NULL ) || ( $cb_author_go != NULL ) ) { $cb_author_output .= '<div class="cb-author-contact">';}
        if ( $cb_author_email != NULL ) { $cb_author_output .= '<a href="mailto:' . sanitize_email( $cb_author_email ) . '"><i class="fa fa-envelope-o cb-tip-bot" title="'.__('Email', 'cubell') . '"></i></a>'; }
        if ( $cb_author_www != NULL ) { $cb_author_output .= ' <a href="' . esc_url( $cb_author_www ) . '" target="_blank"><i class="fa fa-link cb-tip-bot" title="'.__('Website', 'cubell') . '"></i></a> '; }
        if ( $cb_author_tw != NULL ) { $cb_author_output .= ' <a href="//www.twitter.com/' . $cb_author_tw. '" target="_blank" ><i class="fa fa-twitter cb-tip-bot" title="Twitter"></i></a>'; }
        if ( $cb_author_go != NULL ) { $cb_author_output .= ' <a href="' . esc_url( $cb_author_go ) . '?rel=author" rel="publisher" target="_top" title="Google+" class="cb-googleplus cb-tip-bot" ><img src="//ssl.gstatic.com/images/icons/gplus-16.png" data-at2x="//ssl.gstatic.com/images/icons/gplus-32.png" alt="Google+"></a>'; }

        if ( ( $cb_author_email != NULL ) || ( $cb_author_www != NULL ) || ( $cb_author_tw != NULL ) || ( $cb_author_go != NULL ) ) {$cb_author_output .= '</div>';}
        $cb_author_output .= '</div>';

        if ( $cb_author_desc != NULL ) { $cb_author_output .= '<p class="cb-author-bio">' . $cb_author_desc . '</p>'; }

        $cb_author_output .= '</div></div>';

        return $cb_author_output;
    }
}

if ( ! function_exists( 'cb_bbp_author_details' ) ) {
    function cb_bbp_author_details($cb_author_id, $cb_desc = true) {

        $cb_author_email = get_the_author_meta('publicemail', $cb_author_id);
        $cb_author_name = get_the_author_meta('display_name', $cb_author_id);
        $cb_author_position = get_the_author_meta('position', $cb_author_id);
        $cb_author_tw = get_the_author_meta('twitter', $cb_author_id);
        $cb_author_go = get_the_author_meta('googleplus', $cb_author_id);
        $cb_author_www = get_the_author_meta('url', $cb_author_id);
        $cb_author_desc = get_the_author_meta('description', $cb_author_id);

        $cb_author_output = NULL;
        $cb_author_output .= '<div class="cb-author-details cb-bbp clearfix"><div class="cb-mask"><a href="' . bbp_get_user_profile_url() . '" title="' . bbp_get_displayed_user_field( 'display_name' ) . '" rel="me">' . get_avatar( bbp_get_displayed_user_field( 'user_email', 'raw' ), apply_filters( 'bbp_single_user_details_avatar_size', 150 ) ) . '</a></div><div class="cb-meta"><h3><a href="' . bbp_get_user_profile_url() . '" title="' . bbp_get_displayed_user_field( 'display_name' ) . '">' . $cb_author_name.'</a></h3>';

        if ( $cb_author_position != NULL ) { $cb_author_output .= '<div class="cb-author-position">' . $cb_author_position.'</div>'; }
        if ( ( $cb_author_desc != NULL ) && ( $cb_desc == true ) ) { $cb_author_output .= '<p class="cb-author-bio">' . $cb_author_desc . '</p>'; }
        if ( ( $cb_author_email != NULL ) || ( $cb_author_www != NULL ) || ( $cb_author_tw != NULL ) || ( $cb_author_go != NULL ) ) { $cb_author_output .= '<div class="cb-author-page-contact">'; }
        if ( $cb_author_email != NULL ) { $cb_author_output .= '<a href="mailto:' . sanitize_email( $cb_author_email ) . '"><i class="fa fa-envelope-o cb-tip-bot" title="' . __('Email', 'cubell') . '"></i></a>'; }
        if ( $cb_author_www != NULL ) { $cb_author_output .= ' <a href="' . esc_url( $cb_author_www ) . '" target="_blank"><i class="fa fa-link cb-tip-bot" title="' . __('Website', 'cubell') . '"></i></a> '; }
        if ( $cb_author_tw != NULL ) { $cb_author_output .= ' <a href="//www.twitter.com/' . $cb_author_tw . '" target="_blank" ><i class="fa fa-twitter cb-tip-bot" title="Twitter"></i></a>'; }
        if ( $cb_author_go != NULL ) { $cb_author_output .= ' <a href="' . esc_url( $cb_author_go ) . '" rel="publisher" target="_top" title="Google+" class="cb-googleplus cb-tip-bot" ><img src="//ssl.gstatic.com/images/icons/gplus-32.png"  data-at2x="//ssl.gstatic.com/images/icons/gplus-64.png" alt="Google+" ></a>'; }
        if ( ( $cb_author_email != NULL) || ( $cb_author_www != NULL ) || ( $cb_author_go != NULL ) || ( $cb_author_tw != NULL ) ) { $cb_author_output .= '</div>'; }

        $cb_author_output .= '<div id="cb-user-nav"><ul>';


        if ( bbp_is_single_user_replies() ) { $cb_user_current = 'current'; }

        $cb_author_output .= '<li class="';
        if ( bbp_is_single_user_topics() ) { $cb_author_output .= 'current'; }
        $cb_author_output .= '"><span class="bbp-user-topics-created-link"><a href="' . bbp_get_user_topics_created_url() . '">' . __( 'Topics Started', 'bbpress' ) . '</a></span></li>';

        $cb_author_output .= '<li class="';
        if ( bbp_is_single_user_replies() ) { $cb_author_output .= 'current'; }
        $cb_author_output .= '"><span class="bbp-user-replies-created-link"><a href="' . bbp_get_user_replies_created_url() . '">' . __( 'Replies Created', 'bbpress' ) . '</a></span></li>';

        if ( bbp_is_favorites_active() ) {

            $cb_author_output .= '<li class="';
            if ( bbp_is_favorites() ) { $cb_author_output .= 'current'; }
            $cb_author_output .= '"><span class="bbp-user-favorites-link"><a href="' . bbp_get_favorites_permalink() . '">' . __( 'Favorites', 'bbpress' ) . '</a></span></li>';
         }

        if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) {

                     if ( bbp_is_subscriptions_active() ) {
                        $cb_author_output .= '<li class="';
                        if ( bbp_is_subscriptions() ) { $cb_author_output .= 'current'; }
                        $cb_author_output .= '"><span class="bbp-user-subscriptions-link"><a href="' . bbp_get_subscriptions_permalink() . '">' . __( 'Subscriptions', 'bbpress' ) . '</a></span></li>';
                    }

                    $cb_author_output .= '<li class="';
                    if ( bbp_is_single_user_edit() ) { $cb_author_output .= 'current'; }
                    $cb_author_output .= '"><span class="bbp-user-edit-link"><a href="' . bbp_get_user_profile_edit_url() . '">' . __( 'Edit', 'bbpress' ) . '</a></span></li>';
        }

        $cb_author_output .= '</ul></div><!-- #cb-user-nav -->';

        $cb_author_output .= '</div></div>';

        return $cb_author_output;
    }
}

if ( ! function_exists( 'cb_authors_filter' ) ) {
    function cb_authors_filter() {

        $cb_all_authors = array_merge( get_users( 'role=editor' ), get_users( 'role=administrator' ), get_users( 'role=author' ), get_users( 'role=contributor' ) );
        $cb_filtered = $cb_filtered_1 = $cb_filtered_2 = $cb_filtered_3 = $cb_filtered_4 = $cb_filtered_5 = array();

        foreach( $cb_all_authors as $cb_author )  {
            $cb_author_onoff = get_the_author_meta( 'cb_show_author', $cb_author->ID );
            $cb_author_order = get_the_author_meta( 'cb_order', $cb_author->ID );

              if ( ( $cb_author_onoff == 'true' ) && ( $cb_author_order == '0' ) ) {
                    array_push( $cb_filtered, $cb_author );
                }

              for( $i = 1; $i < 6; $i++ ) {

                   if ( ( $cb_author_onoff == 'true' ) && ( $cb_author_order == $i ) ) {
                       array_push( ${"cb_filtered_". $i.""}, $cb_author );
                   }
               }
        }

        $cb_filtered_authors = array_merge( $cb_filtered_1, $cb_filtered_2, $cb_filtered_3, $cb_filtered_4, $cb_filtered_5, $cb_filtered );
        return $cb_filtered_authors;
    }
}

if ( ! function_exists( 'cb_author_list' ) ) {
    function cb_author_list( $cb_full_width = false ) {

         $cb_authors = cb_authors_filter();
         $cb_authors_list = NULL;
         $i = 0;
         if ( $cb_full_width == true ) {
             $cb_line_amount = 4;
         } else {
              $cb_line_amount = 3;
         }

            if ( count( $cb_authors ) > 0) {

                    $cb_authors_list .= '<div class="cb-author-line clearfix">';
                    foreach ( $cb_authors as $cb_author ) {

                        if ( ( $i % $cb_line_amount == 0 ) && ( $i != 0 ) ) {
                            $cb_authors_list .= '</div><div class="cb-author-line clearfix">';
                        }

                        $cb_authors_list .=  cb_author_details( $cb_author->ID, false );
                        $i++;

                    }

                        $cb_authors_list .= '</div>';

            }  else {

                 $cb_authors_list .= '<h2>No Authors Enabled</h2><p>Tick the "Show On About Us Page Template" checkbox on each author profile you wish to showcase here.</p>';
            }

       return $cb_authors_list;
    }
}

/*********************
RELATED POSTS FUNCTION
*********************/
if ( ! function_exists( 'cb_related_posts' ) ) {
    function cb_related_posts() {
        global $post;
        $cb_post_id = $post->ID;
        $i = 1;
        $cb_related_posts_amount = floatval( ot_get_option( 'cb_related_posts_amount', '2' ) );
        $cb_related_posts_show = ot_get_option( 'cb_related_posts_show', 'both' );
        $cb_related_posts_order = ot_get_option( 'cb_related_posts_order', 'rand' );
        $cb_related_posts_amount_full = ( $cb_related_posts_amount * 1.5 );

        $cb_full_width_post = get_post_meta( $cb_post_id, 'cb_full_width_post', true );
        if ( $cb_full_width_post == 'nosidebar' ) { $cb_number_related = $cb_related_posts_amount_full; } else { $cb_number_related = $cb_related_posts_amount; }

            $cb_tags = wp_get_post_tags( $cb_post_id );
            $cb_tag_check = $cb_all_cats = $cb_related_args = $cb_related_posts = NULL;

            if ( ( $cb_related_posts_show == 'both' ) || ( $cb_related_posts_show == 'tags' ) ) {


                if ( $cb_tags != NULL ) {
                    foreach ( $cb_tags as $cb_tag ) { $cb_tag_check .= $cb_tag->slug . ','; }
                    $cb_related_args = array( 'numberposts' => $cb_number_related, 'tag' => $cb_tag_check, 'exclude' => $cb_post_id, 'post_status' => 'publish','orderby' => $cb_related_posts_order );
                    $cb_related_posts = get_posts( $cb_related_args );
                }

            }          

            if ( ( $cb_related_posts_show == 'both' ) || ( $cb_related_posts_show == 'cats' ) ) {

                if ( $cb_related_posts == NULL ) {
                    $cb_categories = get_the_category();
                    foreach ( $cb_categories as $cb_category ) { $cb_all_cats .= $cb_category->term_id  . ','; }
                    $cb_related_args = array( 'numberposts' => $cb_number_related, 'category' => $cb_all_cats, 'exclude' => $cb_post_id, 'post_status' => 'publish', 'orderby' => $cb_related_posts_order );
                    $cb_related_posts = get_posts( $cb_related_args );
                }

            }  

            if ( $cb_related_posts != NULL ) {

                echo '<div id="cb-related-posts" class="clearfix"><h3 class="cb-block-title">' . __('Related Posts', 'cubell') . '</h3><ul>';
                foreach ( $cb_related_posts as $post ) {

                    $cb_post_id = $post->ID;
                    $cb_global_color = ot_get_option('cb_base_color', '#eb9812');
                    $cb_cat_id = get_the_category();

                    if ( function_exists('get_tax_meta') ) {

                            $cb_current_cat_id = $cb_cat_id[0]->term_id;
                            $cb_category_color = get_tax_meta($cb_current_cat_id, 'cb_color_field_id');

                            if (($cb_category_color == "#") || ($cb_category_color == NULL)) {
                                $cb_parent_cat_id = $cb_cat_id[0]->parent;

                                if ($cb_parent_cat_id != '0') {
                                    $cb_category_color = get_tax_meta($cb_parent_cat_id, 'cb_color_field_id');
                                }

                                if (($cb_category_color == "#") || ($cb_category_color == NULL)) {
                                    $cb_category_color = $cb_global_color;
                                }
                            }
                    } else {
                         $cb_category_color = NULL;
                    }
                    setup_postdata($post);
?>
                            <li class="no-<?php echo $i;?>">
                                <div class="cb-mask" style="background-color:<?php echo $cb_category_color;?>;"><?php cb_thumbnail('360', '240'); echo cb_review_ext_box($cb_post_id, $cb_category_color );  ?></div>
                                 <div class="cb-meta">
                                     <h4><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h4>
                                     <?php echo cb_byline(false); ?>
                                </div>
                            </li>
<?php
                            $i++;
                }

                echo '</ul></div>';
                wp_reset_postdata();
            }
    }
}

/*********************
 CLEAN NEXT/PREVIOUS LINKS
*********************/
if ( ! function_exists( 'cb_previous_next_links' ) ) {
    function cb_previous_next_links() {

          $cb_previous = get_adjacent_post( false, '', true );
          $cb_next = get_adjacent_post( false, '', false );
          if ( ( $cb_next != NULL ) || ( $cb_previous != NULL ) ) {

            if ( is_rtl() ) {

                $cb_next_arrow = '<i class="fa fa-long-arrow-left"></i>';
                $cb_previous_arrow = '<i class="fa fa-long-arrow-right"></i>';

            } else {
                $cb_previous_arrow = '<i class="fa fa-long-arrow-left"></i>';
                $cb_next_arrow = '<i class="fa fa-long-arrow-right"></i>';
            }

              $cb_empty = '<div class="cb-empty">' . __("No More Stories", "cubell").'</div>';

              echo'<div id="cb-previous-next-links" class="cb-post-footer-block clearfix">';

              if ( $cb_previous != NULL ) {

                         echo '<div id="cb-previous-link"><a href="' . get_permalink($cb_previous) . '">' . $cb_previous_arrow . '</a>';
                         previous_post_link('%link');
                         echo '</div>';

              } else {
                  echo $cb_empty;
              }
               if ( $cb_next != NULL ) {
                         echo '<div id="cb-next-link"><a href="' . get_permalink($cb_next) . '">' . $cb_next_arrow . '</a>';
                         next_post_link('%link');
                         echo '</div>';

              } else {
                  echo $cb_empty;
              }

              echo '</div>';
           }
    }
}

/*********************
LOAD USER COLORS & BACKGROUNDS
*********************/
if ( ! function_exists( 'cb_user_colors' ) ) {
    function cb_user_colors() {

        if ( is_single() == true ) {
            global $post;
            $cb_post_type = get_post_type();
        } else {
            $cb_post_type = NULL;
        }
        $cb_override_background_color = NULL;
        $cb_base_color = ot_get_option('cb_base_color', '#eb9812');
        $cb_background_color = ot_get_option('cb_background_colour', '');
        $cb_background_image = ot_get_option('cb_background_image', '');
        $cb_background_image_setting = ot_get_option('cb_bg_image_setting', '1');
        $cb_featured_image_bg = $cb_global_or_local = NULL;
        $cb_bg_to = ot_get_option('cb_bg_to', 'off');
        $cb_bg_to_img = ot_get_option('cb_bg_to_img', NULL);

        $cb_mobile = new Mobile_Detect;
        $cb_phone = $cb_mobile->isMobile();
        $cb_tablet = $cb_mobile->isTablet();

        if ( ( $cb_tablet == true ) || ( $cb_phone == true ) ) {
            $cb_is_mobile = true;
        } else {
            $cb_is_mobile = false;
        }

        if ( is_category() ) {

            $cb_cat_id = get_query_var('cat');
            $cb_parents = get_category_parents($cb_cat_id, FALSE, '.' ,true);
            $cb_parent_slug = explode('.',$cb_parents);
            $cb_parent_cat = get_category_by_slug($cb_parent_slug[0]);
            $cb_parent_cat_id = $cb_parent_cat->cat_ID;

             if ( function_exists( 'get_tax_meta' ) ) {

                if ( $cb_parent_cat_id != '0' ) {
                    $cb_parent_cat_bg_color = get_tax_meta( $cb_parent_cat_id, 'cb_bg_color_field_id' );
                }

                if ( ( $cb_parent_cat_bg_color != '#' ) && ( $cb_parent_cat_bg_color != NULL ) ) {
                    $cb_background_color = $cb_parent_cat_bg_color;
                }

                $cb_base_color_cat = get_tax_meta($cb_parent_cat,'cb_color_field_id');
                $cb_override_background_color = get_tax_meta($cb_cat_id,'cb_bg_color_field_id');
                $cb_background_image_cat = get_tax_meta($cb_cat_id,'cb_bg_image_field_id');
                $cb_background_image_setting = get_tax_meta($cb_cat_id,'cb_bg_image_setting_op');

            } else {
                $cb_base_color_cat = $cb_override_background_color = $cb_background_image_cat = $cb_background_image_setting = NULL;
            }

            if ( ( $cb_base_color_cat != '#' ) && ( $cb_base_color_cat != NULL ) ) {
                 $cb_base_color = $cb_base_color_cat;
            }

            if ( ( $cb_background_image_cat == NULL ) &&  function_exists( 'get_tax_meta' ) ) {
                 $cb_background_image_cat = get_tax_meta($cb_parent_cat,'cb_bg_image_field_id');
            }

            if ( ( $cb_override_background_color != NULL ) && ( $cb_override_background_color != '#' ) ) {
                $cb_background_color = $cb_override_background_color;
                $cb_background_image = NULL;
            }

           
            if ( $cb_background_image_cat != NULL ) {
                 $cb_background_image   = $cb_background_image_cat['src'];
            } else {
                $cb_background_image_setting = ot_get_option('cb_bg_image_setting', '1');
            }

        } elseif ( is_tag() ) {

            $cb_tag_id = get_query_var('tag_id');

            if ( function_exists( 'get_tax_meta' ) ) {
                $cb_base_color_cat = get_tax_meta($cb_tag_id,'cb_color_field_id');
                $cb_override_background_color = get_tax_meta($cb_tag_id,'cb_bg_color_field_id');
                $cb_background_image_cat = get_tax_meta($cb_tag_id,'cb_bg_image_field_id');
                $cb_background_image_setting = get_tax_meta($cb_tag_id,'cb_bg_image_setting_op');

            } else {
                $cb_base_color_cat = $cb_override_background_color = $cb_background_image_cat = $cb_background_image_setting = NULL;
            }

            if ( ( $cb_base_color_cat != '#' ) && ( $cb_base_color_cat != NULL ) ) {
                 $cb_base_color = $cb_base_color_cat;
            }


            if ( ( $cb_override_background_color != NULL ) && ( $cb_override_background_color != '#' ) ) {
                $cb_background_color = $cb_override_background_color;
                $cb_background_image = NULL;
            }

            if ( $cb_background_image_cat != NULL ) {
                 $cb_background_image   = $cb_background_image_cat['src'];
            } else {
                $cb_background_image_setting = ot_get_option('cb_bg_image_setting', '1');
            }

        }  elseif ( function_exists( 'bbpress' ) && ( is_bbpress() == true ) )  {

            $cb_bbpress_background_image = ot_get_option('cb_bbpress_background_image', '');
            $cb_bbpress_background_color = ot_get_option('cb_bbpress_background_color', '');
            $cb_bbpress_background_image_setting = ot_get_option('cb_bbpress_bg_image_setting', '1');
            $cb_base_color = ot_get_option('cb_bbpress_global_color', '#eb9812');

            if ( $cb_bbpress_background_image != NULL ) {
                $cb_background_image = $cb_bbpress_background_image;
                $cb_background_image_setting = $cb_bbpress_background_image_setting;
            }

            if ( $cb_bbpress_background_color != NULL ) {
                $cb_background_color = $cb_bbpress_background_color;
            }

        } elseif ( function_exists( 'buddypress' ) && ( is_buddypress() == true ) )  {

            $cb_buddypress_background_image = ot_get_option('cb_buddypress_background_image', '');
            $cb_buddypress_background_color = ot_get_option('cb_buddypress_background_color', '');
            $cb_buddypress_background_image_setting = ot_get_option('cb_buddypress_bg_image_setting', '1');
            $cb_base_color = ot_get_option('cb_buddypress_global_color', '#eb9812');

            if ( $cb_buddypress_background_image != NULL ) {
                $cb_background_image = $cb_buddypress_background_image;
                $cb_background_image_setting = $cb_buddypress_background_image_setting;
            }

            if ( $cb_buddypress_background_color != NULL ) {
                $cb_background_color = $cb_buddypress_background_color;
            }

        } elseif ( ( class_exists('Woocommerce') )  && ( ( is_woocommerce() == true ) || ( is_cart() == true ) || ( is_account_page() == true ) || ( is_order_received_page() == true ) || ( is_checkout() == true ) ) )  {

            $cb_woocommerce_background_image = ot_get_option('cb_woocommerce_background_image', '');
            $cb_woocommerce_background_color = ot_get_option('cb_woocommerce_background_color', '');
            $cb_woocommerce_background_image_setting = ot_get_option('cb_woocommerce_bg_image_setting', '1');
            $cb_base_color = ot_get_option('cb_woocommerce_global_color', '#eb9812');

            if ( $cb_woocommerce_background_image != NULL ) {
                $cb_background_image = $cb_woocommerce_background_image;
                $cb_background_image_setting = $cb_woocommerce_background_image_setting;
            }

            if ( $cb_woocommerce_background_color != NULL ) {
                $cb_background_color = $cb_woocommerce_background_color;
            }

        } elseif ( $cb_post_type == 'post' )  {

            $cb_post_id = $post->ID;
            $cb_featured_image_bg = get_post_meta( $cb_post_id, 'cb_featured_image_style', true);
            $cb_cat_id = get_the_category($cb_post_id);
            $cb_current_cat_id = $cb_cat_id[0]->term_id;
            $cb_global_color = ot_get_option('cb_base_color', '#eb9812');
            $cb_parent_cat_id = $cb_cat_id[0]->parent;

            if ( function_exists( 'get_tax_meta' ) ) {

                 if ( $cb_parent_cat_id != '0' ) {
                    $cb_parent_cat_bg_color = get_tax_meta( $cb_parent_cat_id, 'cb_bg_color_field_id' );

                    if ( ( $cb_parent_cat_bg_color != '#' ) && ( $cb_parent_cat_bg_color != NULL ) ) {
                        $cb_background_color = $cb_parent_cat_bg_color;
                    }
                } 

                $cb_base_color_cat = get_tax_meta($cb_current_cat_id, 'cb_color_field_id');
                $cb_background_color_cat = get_tax_meta($cb_current_cat_id, 'cb_bg_color_field_id');

                if ( ( $cb_background_color_cat != '#' ) && ( $cb_background_color_cat != NULL ) ) {
                    $cb_background_color = $cb_background_color_cat;
                }

                if ( ( $cb_base_color_cat == '#' ) || ( $cb_base_color_cat == NULL ) ) {

                    if ($cb_parent_cat_id != '0') {
                        $cb_base_color_cat = get_tax_meta($cb_parent_cat_id, 'cb_color_field_id');
                    }
                    if ( ( $cb_base_color_cat == '#' ) || ( $cb_base_color_cat == NULL ) ) {
                        $cb_base_color_cat = $cb_global_color;
                    }
                }
            } else {
                $cb_base_color_cat = NULL;
            }

            $cb_override_background_color = get_post_meta( $cb_post_id,'cb_bg_color_post', true);
            $cb_background_image_single = get_post_meta( $cb_post_id,'cb_bg_image_post');
            $cb_background_image_setting = get_post_meta( $cb_post_id,'cb_bg_image_post_setting');

            if ( $cb_background_image_single != NULL ) {

                if ( is_array( $cb_background_image_single ) && count( $cb_background_image_single ) > 1 ) {
                    $cb_background_image = array();
                    $cb_global_or_local = true;
                    foreach ( $cb_background_image_single as $img ) {

                        $cb_img_src = wp_get_attachment_image_src($img, 'cb-1400-700');
                        $cb_background_image[] = $cb_img_src[0];
                    }

                } else {
                    $cb_img_src = wp_get_attachment_image_src($cb_background_image_single[0], 'full');
                    $cb_background_image = $cb_img_src[0];
                }

            }


            if ( ( $cb_override_background_color != NULL ) && ( $cb_override_background_color != '#' ) ) {
                $cb_background_color = $cb_override_background_color;
                if ( $cb_background_image_single == NULL ) {
                    $cb_background_image = NULL;
                }
            }


            if ( ( $cb_background_image_single == NULL) && ( $cb_override_background_color == NULL ) ) {

                if ( function_exists('get_tax_meta') ) {
                    $cb_override_background_color = get_tax_meta($cb_current_cat_id,'cb_bg_color_field_id');
                    $cb_background_image_cat = get_tax_meta($cb_current_cat_id,'cb_bg_image_field_id');
                    $cb_background_image_setting[] = get_tax_meta($cb_current_cat_id,'cb_bg_image_setting_op');

                } else {
                    $cb_override_background_color = $cb_background_image_setting[] = $cb_background_image_cat = NULL;
                }

                if ( ( $cb_background_image_cat == NULL ) &&  function_exists( 'get_tax_meta' ) ) {
                     $cb_background_image_cat = get_tax_meta( $cb_parent_cat_id, 'cb_bg_image_field_id' );
                }

                if ( $cb_override_background_color == '#' ) { $cb_override_background_color = NULL; }

                if ( $cb_background_image_cat != NULL ) {
                    $cb_background_image   = $cb_background_image_cat['src'];
                } else {
                    $cb_background_image_setting = ot_get_option('cb_bg_image_setting', '1');
                }
            }

            if ( ( $cb_base_color_cat != '#' ) && ( $cb_base_color_cat != NULL ) ) {
                    $cb_base_color = $cb_base_color_cat;
            }

            if ( ( $cb_background_image_single == NULL ) && ( ( $cb_featured_image_bg == 'parallax' ) || ( $cb_featured_image_bg == 'full-background' ) ) ) {
                $cb_global_or_local = true;
            }

            if ( ( $cb_background_image_single != NULL ) && ( ( $cb_featured_image_bg == 'standard' ) || ( $cb_featured_image_bg == 'full-width' ) || ( $cb_featured_image_bg == 'off' )  ) ) {
                $cb_global_or_local = NULL;
            }

       } elseif ( is_page() )  {

                $cb_page_id = get_the_ID();
                $cb_page_base_color = get_post_meta($cb_page_id , 'cb_overall_color_post', true );

                if ( $cb_page_base_color == '#' ) { $cb_page_base_color = NULL; }

                $cb_override_background_color = get_post_meta( $cb_page_id,'cb_bg_color_post');
                $cb_page_bg_image = get_post_meta( $cb_page_id,'cb_bg_image_post');
                $cb_background_image_setting = get_post_meta( $cb_page_id,'cb_bg_image_post_setting');

                if ( $cb_page_base_color != NULL ) { $cb_base_color = $cb_page_base_color; }
                if ( $cb_override_background_color != NULL ) { $cb_background_color = $cb_override_background_color[0]; }

                if ($cb_page_bg_image != NULL) {
                    if (is_array($cb_page_bg_image) && count($cb_page_bg_image) > 1) {
                        $cb_background_image = array();
                        foreach ($cb_page_bg_image as $img) {
                            $cb_img_src = wp_get_attachment_image_src($img, 'full');
                            $cb_background_image[] = $cb_img_src[0];
                        }
                    } else {
                        $cb_img_src = wp_get_attachment_image_src($cb_page_bg_image[0], 'full');
                        $cb_background_image = $cb_img_src[0];
                    }
                } elseif ( $cb_override_background_color != NULL ) {
                    $cb_background_image = NULL;
                }
                if ( $cb_background_image_setting == NULL ) { $cb_background_image_setting = ot_get_option('cb_bg_image_setting', '1'); }

      }  else {
        $cb_override_background_color = $cb_background_color;
      }

      if ( ( $cb_bg_to == 'off' ) || ( ( $cb_bg_to == 'only-hp' ) && ( is_front_page() == FALSE ) ) ) {

            if ( ( $cb_background_image != NULL ) && ( $cb_global_or_local == NULL ) && ( $cb_is_mobile == false ) ) {

                if ( is_array( $cb_background_image ) && count( $cb_background_image ) > 1 ) {
                    $cb_slideshow = true;
                    $cb_image = $cb_background_image[0];
                } else {
                    $cb_slideshow = false;
                    $cb_image = $cb_background_image;
                }

                if ( $cb_background_image_setting[0] == '1' ) {

                    echo '<script>jQuery(document).ready(function($){$.backstretch(';
                    if ( $cb_slideshow == true ) {
                        echo '[';
                        foreach ( $cb_background_image as $cb_bg_slide ) {
                            echo '"' . $cb_bg_slide . '", ';
                        }
                        echo '],  {fade: 750, duration: 6000}';
                    } else {
                        echo  '"' . $cb_background_image . '",  {fade: 750}';
                    }
                    echo '); }); </script>';

                } elseif ( $cb_background_image_setting[0] == '2' ) {
                    echo '<style type="text/css">body { background: url(' . $cb_image . ') repeat; }</style>';

                } elseif ( $cb_background_image_setting[0] == '3' ) {

                    echo '<style type="text/css">body { background: url(' . $cb_image . ') no-repeat; }</style>';

                }

            } elseif ( ( $cb_override_background_color == NULL ) && ( $cb_background_image != NULL ) && ( $cb_featured_image_bg == NULL ) ) {

                if ( $cb_background_image_setting == '1' ) {
                    echo '<script>jQuery(document).ready(function($){$.backstretch("' . $cb_background_image . '"); });</script>';
                } elseif ( $cb_background_image_setting == '2' ) {
                    echo '<style type="text/css">body {background: url(' . $cb_background_image . ') repeat; }</style>';
                } elseif ( $cb_background_image_setting == '3' ) {
                    echo '<style type="text/css">body {background: url(' . $cb_background_image . ') no-repeat; }</style>';
                }
            }
    }

    echo '<style>';

    if ( ( $cb_background_color != NULL ) && ( ( $cb_bg_to == 'off' ) || ( ( $cb_bg_to == 'only-hp' ) && ( is_front_page() == FALSE ) ) ) ) {

        echo 'body, #cb-parallax-featured #cb-parallax-bg { background-color: ';
        if ( $cb_background_image != NULL ) {
            if ( $cb_background_color != NULL ) {
                 echo $cb_background_color;
            } else {
                echo '#151515;';
            }
        } else {
             echo $cb_background_color;
        }

        echo ';}';
    }

    echo '.cb-overlay-stars .fa-star, #cb-vote .fa-star, #cb-to-top .fa-long-arrow-up, .cb-review-box .cb-score-box, .bbp-submit-wrapper button, .bbp-submit-wrapper button:visited, .buddypress .cb-cat-header #cb-cat-title a,  .buddypress .cb-cat-header #cb-cat-title a:visited, .woocommerce .star-rating:before, .woocommerce-page .star-rating:before, .woocommerce .star-rating span, .woocommerce-page .star-rating span, .woocommerce .stars a {
            color:' . $cb_base_color . ';
        }';

    echo '#cb-search-modal .cb-header, .cb-join-modal .cb-header, .lwa .cb-header, .cb-review-box .cb-score-box, .bbp-submit-wrapper button, #buddypress button:hover, #buddypress a.button:hover, #buddypress a.button:focus, #buddypress input[type=submit]:hover, #buddypress input[type=button]:hover, #buddypress input[type=reset]:hover, #buddypress ul.button-nav li a:hover, #buddypress ul.button-nav li.current a, #buddypress div.generic-button a:hover, #buddypress .comment-reply-link:hover, #buddypress .activity-list li.load-more:hover, #buddypress #groups-list .generic-button a:hover {
            border-color: ' . $cb_base_color . ';
        }';

    echo '.cb-sidebar-widget .cb-sidebar-widget-title, .cb-multi-widget .tabbernav .tabberactive, .cb-author-page .cb-author-details .cb-meta .cb-author-page-contact, .cb-about-page .cb-author-line .cb-author-details .cb-meta .cb-author-page-contact, .cb-page-header, .cb-404-header, .cb-cat-header, #cb-footer #cb-widgets .cb-footer-widget-title span, #wp-calendar caption, .cb-tabs ul .current, #bbpress-forums li.bbp-header, #buddypress #members-list .cb-member-list-box .item .item-title, #buddypress div.item-list-tabs ul li.selected, #buddypress div.item-list-tabs ul li.current, #buddypress .item-list-tabs ul li:hover, .woocommerce div.product .woocommerce-tabs ul.tabs li.active {
            border-bottom-color: ' . $cb_base_color . ' ;
        }';

    echo '#cb-main-menu .current-post-ancestor, #cb-main-menu .current-menu-item, #cb-main-menu .current-menu-ancestor, #cb-main-menu .current-post-parent, #cb-main-menu .current-menu-parent, #cb-main-menu .current_page_item, #cb-main-menu .current-page-ancestor, #cb-main-menu .current-category-ancestor, .cb-review-box .cb-bar .cb-overlay span, #cb-accent-color, .cb-highlight, #buddypress button:hover, #buddypress a.button:hover, #buddypress a.button:focus, #buddypress input[type=submit]:hover, #buddypress input[type=button]:hover, #buddypress input[type=reset]:hover, #buddypress ul.button-nav li a:hover, #buddypress ul.button-nav li.current a, #buddypress div.generic-button a:hover, #buddypress .comment-reply-link:hover, #buddypress .activity-list li.load-more:hover, #buddypress #groups-list .generic-button a:hover {
            background-color: ' . $cb_base_color . ';
        }';

    if ( class_exists('Woocommerce') ) {

        echo '.woocommerce ul.products li.product, .woocommerce-page ul.products li.product, .woocommerce ul.products li.product, .woocommerce-page ul.products li.product, .woocommerce .related ul.products li.product, .woocommerce .related ul li.product, .woocommerce .upsells.products ul.products li.product, .woocommerce .upsells.products ul li.product, .woocommerce-page .related ul.products li.product, .woocommerce-page .related ul li.product, .woocommerce-page .upsells.products ul.products li.product, .woocommerce-page .upsells.products ul li.product, .cb-woocommerce-page {
            border-bottom-color: ' . $cb_base_color . ' ;
        }';

        echo '.woocommerce a.button:hover, .woocommerce-page a.button:hover, .woocommerce button.button:hover, .woocommerce-page button.button:hover, .woocommerce input.button:hover, .woocommerce-page input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-page #respond input#submit:hover, .woocommerce #content input.button:hover, .woocommerce-page #content input.button:hover, .added_to_cart, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce .quantity .plus:hover, .woocommerce-page .quantity .plus:hover, .woocommerce #content .quantity .plus:hover, .woocommerce-page #content .quantity .plus:hover, .woocommerce .quantity .minus:hover, .woocommerce-page .quantity .minus:hover, .woocommerce #content .quantity .minus:hover, .woocommerce-page #content .quantity .minus:hover, .woocommerce a.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page #content input.button.alt:hover, .woocommerce input.checkout-button.button, .woocommerce input.button#place_order, .widget_shopping_cart .button.checkout, .woocommerce .cart-collaterals .shipping_calculator a:hover, .woocommerce-page .cart-collaterals .shipping_calculator a:hover, .woocommerce .shipping-calculator-button:hover {
                background: ' . $cb_base_color . ';
            }';
        echo '.woocommerce .woocommerce-message:before, .woocomerce-page .woocommerce-message:before, .woocommerce .woocommerce-info:before {
                background-color: ' . $cb_base_color . ';
            }';

        echo '.woocommerce div.product span.price, .woocommerce-page div.product span.price, .woocommerce #content div.product span.price, .woocommerce-page #content div.product span.price, .woocommerce div.product p.price, .woocommerce-page div.product p.price, .woocommerce #content div.product p.price, .woocommerce-page #content div.product p.price, .woocommerce .woocommerce-product-rating, .woocommerce-page .woocommerce-product-rating .woocommerce-review-link   {
                color: ' . $cb_base_color . ';
            }';
    }

    echo '</style>';

    }
}
add_action('wp_head', 'cb_user_colors');

/*********************
COMMENTS
*********************/
if ( ! function_exists( 'cb_comments' ) ) {
    function cb_comments($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment; ?>

        <li <?php comment_class(); ?>>

            <article id="comment-<?php comment_ID(); ?>" class="clearfix">
                    <?php $bgauthemail = get_comment_author_email(); ?>
                    <div class="cb-gravatar-image">
                        <?php echo get_avatar( $comment, 80 ); ?>
                    </div>

               <div class="cb-comment-body clearfix">
                 <header class="comment-author vcard">
                    <?php echo "<cite class='fn'>".get_comment_author_link()."</cite>"; ?>
                    <time datetime="<?php comment_date(); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_date(); ?> </a></time>
                    <?php edit_comment_link(__('(Edit)', 'cubell'),'  ','') ?>
                </header>
                <?php if ($comment->comment_approved == '0') : ?>
                    <div class="alert info">
                        <p><?php _e('Your comment is awaiting moderation.', 'cubell') ?></p>
                    </div>
                <?php endif; ?>
                <section class="comment_content clearfix">
                    <?php comment_text() ?>
                </section>
                <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
              </div>
            </article>
<?php
    }
}

/*********************
GET POST CATEGORY COLOR
*********************/
if ( ! function_exists( 'cb_get_cat_color' ) ) {
    function cb_get_cat_color( $cb_post_id) {

        $cb_category_color = NULL;
        $cb_cat_id_current = get_the_category( $cb_post_id );
        $cb_cat_parent = $cb_cat_id_current[0]->category_parent;

        if ( ( $cb_cat_parent == '0' ) && ( is_tag() == false ) ) {
            $cb_cat_id_current = $cb_cat_id_current[0]->cat_ID;
        } else {
            $cb_cat_id_current = $cb_cat_parent;
        }

        if ( function_exists( 'get_tax_meta' ) ) {
            $cb_category_color = get_tax_meta( $cb_cat_id_current, 'cb_color_field_id' );
        } 

        if ( ( $cb_category_color == NULL ) ||  ($cb_category_color == '#' ) ) {
            $cb_category_color =  ot_get_option( 'cb_base_color', '#eb9812' );
        }
        return $cb_category_color;
    }
}


/*********************
FEATURED IMAGE THUMBNAILS
*********************/
if ( ! function_exists( 'cb_thumbnail' ) ) {
    function cb_thumbnail( $width, $height, $cb_post_id = NULL ) {
        echo cb_get_thumbnail( $width, $height, $cb_post_id );
    }
}

/*********************
GET FEATURED IMAGE THUMBNAILS
*********************/
if ( ! function_exists( 'cb_get_thumbnail' ) ) {
    function cb_get_thumbnail( $width, $height, $cb_post_id = NULL, $cb_link = true ) {

        $cb_output = NULL;

        if  ( ( has_post_thumbnail( $cb_post_id ) ) && ( get_the_post_thumbnail( $cb_post_id ) != NULL ) ) {
            if ( $cb_link == true ) {
                $cb_output = '<a href="' . get_permalink( $cb_post_id ) . '">';
            }
            
            $cb_output .= get_the_post_thumbnail( $cb_post_id, 'cb-' . $width . '-' . $height ); 

            if ( $cb_link == true ) {
                $cb_output .= '</a>';
            }          

        } else {

            if ( $cb_link == true ) {
                $cb_output = '<a href="' . get_permalink( $cb_post_id ) . '">';
            }
            $cb_thumbnail = cb_file_location( 'library/images/thumbnail-' . $width . 'x' . $height . '.png' );
            $cb_retina_thumbnail = cb_file_location( 'library/images/thumbnail-' . $width . 'x' . $height . '@2x.png' );
            $cb_output .= '<img src="' . esc_url( $cb_thumbnail ) . '" alt="article placeholder" data-at2x="' . esc_url( $cb_retina_thumbnail ) . '">';
            if ( $cb_link == true ) {
                $cb_output .= '</a>';
            }
        }
        
        return $cb_output;
    }
}

/*********************
LOAD USER FONT
*********************/

if ( ! function_exists( 'cb_fonts' ) ) {
    function cb_fonts() {

        $cb_header_font = ot_get_option('cb_header_font', "'Oswald', sans-serif;");
        $cb_user_header_font = ot_get_option('cb_user_header_font', NULL);
        $cb_body_font = ot_get_option('cb_body_font', "'Open Sans', sans-serif;");
        $cb_user_body_font = ot_get_option('cb_user_body_font', NULL);
        $cb_font_latin = ot_get_option('cb_font_ext_lat', 'off');
        $cb_font_cyr = ot_get_option('cb_font_cyr', 'off');
        $cb_font_greek = ot_get_option('cb_font_greek', 'off');
        $cb_return = array();
        $cb_font_ext = NULL;

        if ( $cb_user_header_font != NULL ) {
            $cb_header_font = $cb_user_header_font;
        }

        if ( $cb_user_body_font != NULL ) {
            $cb_body_font = $cb_user_body_font;
        }

        if ( ( $cb_font_latin == 'on' ) && ( $cb_font_greek == 'on' ) ) {

            $cb_font_ext = '&subset=latin,latin-ext,greek,greek-ext';

        } elseif ( ( $cb_font_latin == 'on' ) && ( $cb_font_cyr == 'on' ) ) {

            $cb_font_ext = '&subset=latin,latin-ext,cyrillic,cyrillic-ext';

        } elseif ( $cb_font_latin == 'on' ) {

            $cb_font_ext = '&subset=latin,latin-ext';

        } elseif ( $cb_font_cyr == 'on' ) {

            $cb_font_ext = '&subset=cyrillic,cyrillic-ext';

        } elseif ( $cb_font_greek == 'on' ) {

            $cb_font_ext = '&subset=greek,greek-ext';
        }

        if ( ( $cb_body_font == 'none' ) && ( $cb_header_font == 'none' ) ) {
            $cb_return[] = NULL;

        } else {

            $cb_header_font_clean =  substr($cb_header_font, 0, strpos($cb_header_font, ',') );
            $cb_header_font_clean = str_replace("'", '', $cb_header_font_clean);
            $cb_header_font_clean = str_replace(" ", '+', $cb_header_font_clean);
            $cb_body_font_clean =  substr($cb_body_font, 0, strpos($cb_body_font, ',') );
            $cb_body_font_clean = str_replace("'", '', $cb_body_font_clean);
            $cb_body_font_clean = str_replace(" ", '+', $cb_body_font_clean);

            if ( $cb_body_font == 'none' ) {

                $cb_return[] = '//fonts.googleapis.com/css?family=' . $cb_header_font_clean . ':400,700,400italic' . $cb_font_ext;

            } elseif ( $cb_header_font == 'none' ) {

                $cb_return[] = '//fonts.googleapis.com/css?family=' . $cb_body_font_clean . ':400,700,400italic' . $cb_font_ext;
                
            } else {
                
                $cb_return[] = '//fonts.googleapis.com/css?family=' . $cb_header_font_clean . ':400,700,400italic|' . $cb_body_font_clean . ':400,700,400italic' . $cb_font_ext;
            }
        }      

        $cb_return[] =  '<style type="text/css">
                                                 body, #respond { font-family: ' . $cb_body_font . ' }
                                                 h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, #cb-nav-bar #cb-main-menu ul li > a, .cb-breaking-news span, .cb-grid-4 h2 a, .cb-grid-5 h2 a, .cb-grid-6 h2 a, .cb-author-posts-count, .cb-author-title, .cb-author-position, .search  .s, .cb-review-box .cb-bar, .cb-review-box .cb-score-box, .cb-review-box .cb-title, #cb-review-title, .cb-title-subtle, #cb-top-menu a, .tabbernav, .cb-byline, #cb-next-link a, #cb-previous-link a, .cb-review-ext-box .cb-score, .tipper-positioner, .cb-caption, .cb-button, #wp-calendar caption, .forum-titles, .bbp-submit-wrapper button, #bbpress-forums li.bbp-header, #bbpress-forums fieldset.bbp-form .bbp-the-content-wrapper input, #bbpress-forums .bbp-forum-title, #bbpress-forums .bbp-topic-permalink, .widget_display_stats dl dt, .cb-lwa-profile .cb-block, #buddypress #members-list .cb-member-list-box .item .item-title, #buddypress div.item-list-tabs ul li, #buddypress .activity-list li.load-more, #buddypress a.activity-time-since, #buddypress ul#groups-list li div.meta, .widget.buddypress div.item-options, .cb-activity-stream #buddypress .activity-header .time-since, .cb-font-header, .woocommerce table.shop_table th, .woocommerce-page table.shop_table th, .cb-infinite-scroll a, .cb-no-more-posts { font-family:' . $cb_header_font . ' }
                     </style>';
        return $cb_return;
    }
}

if ( ! function_exists( 'cb_font_styler' ) ) {
    function cb_font_styler() {
       $cb_output = cb_fonts();
       echo $cb_output[1];

    }
}
add_action('wp_head', 'cb_font_styler');

/*********************
LOAD CUSTOM CODE
*********************/
if ( ! function_exists( 'cb_custom_code' ) ) {
    function cb_custom_code(){

            $cb_custom_head = ot_get_option('cb_custom_head', NULL);
            $cb_custom_css = ot_get_option('cb_custom_css', NULL);
            $cb_custom_a_css = ot_get_option('cb_link_color', NULL);
            $cb_custom_breaking_css = ot_get_option('cb_breaking_news_color', NULL);
            $cb_custom_body_color_css = ot_get_option('cb_body_text_color', NULL);
            $cb_custom_header_bg = ot_get_option('cb_header_bg_image', NULL);
            $cb_module_zoom = ot_get_option('cb_module_zoom', 'on');
            $cb_module_underlines = ot_get_option('cb_module_underlines', 'on');
            $cb_bbp_sticky_background_color = ot_get_option('cb_bbp_sticky_background_color', NULL);
            $cb_logo_nav_left = ot_get_option( 'cb_logo_nav_left', '0' );

            if ( $cb_custom_header_bg != NULL ) {
                $cb_custom_header_bg_output = NULL;

                if ( $cb_custom_header_bg['background-color'] != NULL ) {
                    $cb_custom_header_bg_output .= 'background-color: ' . $cb_custom_header_bg['background-color'] . ';';
                }

                if ( $cb_custom_header_bg['background-image'] != NULL ) {
                    $cb_custom_header_bg_output .= 'background-image: url(' . $cb_custom_header_bg['background-image'] . ');';

                    if ( $cb_custom_header_bg['background-repeat'] != NULL ) {
                        $cb_custom_header_bg_output .= 'background-repeat: ' . $cb_custom_header_bg['background-repeat'] . ';';
                    }
                    if ( $cb_custom_header_bg['background-attachment'] != NULL ) {
                        $cb_custom_header_bg_output .= 'background-attachment: ' . $cb_custom_header_bg['background-attachment'] . ';';
                    }
                    if ( $cb_custom_header_bg['background-position'] != NULL ) {
                        $cb_custom_header_bg_output .= 'background-position: ' . $cb_custom_header_bg['background-position'] . ';';
                    }

                }
            }

            if ( $cb_custom_head != NULL ) { echo $cb_custom_head; }
            if ( $cb_module_underlines != 'on' ) { $cb_custom_css .= '.cb-grid-3 .cb-article-meta h2 a, .cb-grid-4 .cb-article-meta h2 a, .cb-grid-5 .cb-article-meta h2 a, .cb-grid-6 .cb-article-meta h2 a, .flexslider-widget .slides li .cb-meta h4 a, .flexslider-1-fw .slides li .cb-meta h2 a, .flexslider-1 .slides li .cb-meta h2 a, .flexslider-1-fw-menu .slides li .cb-meta h2 a, .flexslider-1-menu .slides li .cb-meta h2 a, .flexslider-2-fw .slides li .cb-meta h2 a, .flexslider-2 .slides li .cb-meta h2 a { border-bottom:none; }'; }
            if ( $cb_module_zoom != 'on' ) { $cb_custom_css .= '.flexslider-1-fw .slides li:hover img, .flexslider-1 .slides li:hover img, .flexslider-1-fw-menu .slides li:hover img, .flexslider-1-menu .slides li:hover img, .flexslider-2-fw .slides li:hover img, .flexslider-2 .slides li:hover img, .cb-grid-4 > div:hover img, .cb-grid-5 > div:hover img, .cb-grid-6 > div:hover img { -webkit-transform: none; -moz-transform: none; -ms-transform: none; -o-transform: none; transform: none; }'; }
            if ( $cb_bbp_sticky_background_color != NULL ) { $cb_custom_css .= '.bbp-topics-front ul.super-sticky, .bbp-topics ul.super-sticky, .bbp-topics ul.sticky, .bbp-forum-content ul.sticky {background-color: ' . $cb_bbp_sticky_background_color . '!important;}'; }
            if ( $cb_custom_a_css != NULL ) { $cb_custom_css .= '.entry-content a, .entry-content a:visited {color:' . $cb_custom_a_css . '; }'; }
            if ( $cb_custom_breaking_css != NULL ) { $cb_custom_css .= '#cb-top-menu .cb-breaking-news ul li a { color:' . $cb_custom_breaking_css. '; }'; }
            if ( $cb_custom_body_color_css != NULL ) { $cb_custom_css .= 'body {color:' . $cb_custom_body_color_css . '; }'; }
            if ( $cb_custom_header_bg != NULL ) { $cb_custom_css .= '.header { ' . $cb_custom_header_bg_output . ' }'; }
            if ( $cb_logo_nav_left != 0 ) {  $cb_custom_css .= '#cb-nav-logo { margin-right: ' . $cb_logo_nav_left . 'px!important; }'; }
            if ( $cb_custom_css != NULL ) { echo '<style type="text/css">' . $cb_custom_css . '</style><!-- end custom css -->'; }

    }
}
add_action('wp_head', 'cb_custom_code');

/*********************
LOAD CUSTOM FOOTER CODE
*********************/
if ( ! function_exists( 'cb_custom_footer_code' ) ) {
    function cb_custom_footer_code() {

            $cb_footer_code = ot_get_option('cb_custom_footer', NULL);
            $cb_disqus_code = ot_get_option('cb_disqus_shortname', NULL);

            $cb_disqus_output = "<script type='text/javascript'>var disqus_shortname = '" . $cb_disqus_code . "'; // required: replace example with your forum shortname
                                (function () {
                                    var s = document.createElement('script'); s.async = true;
                                    s.type = 'text/javascript';
                                    s.src = '//' + disqus_shortname + '.disqus.com/count.js';
                                    (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
                                }());
                                </script>";


            if ( $cb_footer_code != NULL ) { echo $cb_footer_code; }
            if ( $cb_disqus_code != NULL ) { echo $cb_disqus_output; }
    }
}
add_action('wp_footer', 'cb_custom_footer_code');

if ( ! function_exists( 'cb_get_image_id' ) ) {
    function cb_get_image_id( $cb_image ) {

        global $wpdb;
        $cb_image_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE guid = '$cb_image'" );
        return $cb_image_id;

    }
}

/*********************
FEATURED IMAGES
*********************/
if ( ! function_exists( 'cb_featured_image' ) ) {
    function cb_featured_image( $post, $cb_style ) {

        $cb_mobile = new Mobile_Detect;
        $cb_meta_onoff = ot_get_option('cb_meta_onoff', 'on');
        $cb_custom_fields = get_post_custom();
        $cb_post_format = get_post_format();
        $cb_post_id = $post->ID;
        $cb_phone = $cb_mobile->isMobile();
        $cb_tablet = $cb_mobile->isTablet();
        if ( ( $cb_tablet == true ) || ( $cb_phone == true ) ) {
            $cb_is_mobile = true;
        } else {
            $cb_is_mobile = false;
        }

        if ( $cb_meta_onoff == 'on_posts' ) {
            $cb_meta_onoff = 'on';
        }

        if ( ( isset($cb_custom_fields['cb_review_checkbox'][0])) && ( $cb_custom_fields['cb_review_checkbox'][0] == '1')) { $cb_review_checkbox = 'on'; } else { $cb_review_checkbox = 'off'; }
        if ( isset($cb_custom_fields['cb_featured_image_style'][0])) { $cb_featured_image_style = $cb_custom_fields['cb_featured_image_style'][0]; } else { $cb_featured_image_style = 'full-width'; }
        if ( isset($cb_custom_fields['cb_image_credit'][0])) { $cb_image_credit = $cb_custom_fields['cb_image_credit'][0]; } else { $cb_image_credit = NULL; }
        $cb_video_url = get_post_meta( $cb_post_id, 'cb_video_embed_code_post', true );
        $cb_audio_url = get_post_meta( $cb_post_id, 'cb_soundcloud_embed_code_post', true );

        if ( strpos( $cb_video_url, 'yout' ) !== false ) {
            preg_match( '([-\w]{11})', $cb_video_url, $cb_youtube_id );
            $cb_video_url = '<div id="cbplayer">' . $cb_youtube_id[0] . '</div>';
        }

        if ( $cb_review_checkbox == 'on' ) { $cb_item_type = 'itemprop="itemReviewed"'; } else { $cb_item_type = 'itemprop="headline"'; }
        $cb_featured_image = $cb_header = $cb_featured_image_url = $cb_image = NULL;

        if ( ( $cb_style == 'standard' ) || ( $cb_style == 'full-width' ) ) {
            $cb_media_bg = '<span id="cb-media-bg"></span>';
        } else {
            $cb_media_bg = NULL;
        }

        if ( $cb_style != 'off' ) {

            if ( $cb_post_format == 'video' ) {
                $cb_video_type = get_post_meta( $cb_post_id, 'cb_video_post_select', true );
                
                if ( $cb_video_type == '2' ) {
                    $cb_post_format_icon = '<div class="cb-media-icon cb-lb"><i class="fa fa-play"></i></div>';
                    $cb_post_format_icon .= '<div id="cb-media-overlay-lb" class="cb-video-lb"><div id="cb-media-frame-lb"><div class="cb-close-m cb-ta-right"><i class="fa cb-times"></i></div>' . do_shortcode( $cb_video_url ) . '</div></div>';
                } else {
                    $cb_post_format_icon = '<div class="cb-media-icon"><i class="fa fa-play"></i></div>';
                    $cb_post_format_icon .= '<div id="cb-media-overlay">' . $cb_media_bg . '<div id="cb-media-frame">' . do_shortcode( $cb_video_url ) . '</div></div>';
                }
                

            } elseif ( $cb_post_format == 'audio' ) {

                $cb_post_format_icon = '<div class="cb-media-icon"><i class="fa fa-headphones"></i></div>';
                $cb_post_format_icon .= '<div id="cb-media-overlay">' . $cb_media_bg.'<div id="cb-media-frame">' . do_shortcode( $cb_audio_url ) . '</div></div>';

            } else {

                $cb_post_format_icon = NULL;
            }

        } else {
            $cb_post_format_icon = $cb_image_credit = NULL;
        }

        if ( $cb_image_credit != NULL ) {  $cb_image_credit = '<div class="cb-image-credit"><i class="fa fa-camera"></i>' . $cb_image_credit.'</div>'; }

        if ( ( $cb_style == 'parallax' ) && ( $cb_is_mobile == true ) ) {
            $cb_header .= '<div class="cb-entry-header cb-style-full-background">';
        } else {
            $cb_header .= '<div class="cb-entry-header cb-style-' . $cb_style. '">';
        }

        if  ( $cb_style != 'standard' ) {
            $cb_header .= $cb_image_credit;
        }

        $cb_header .= '<span class="cb-title-fi"><h1 class="entry-title cb-entry-title cb-single-title" ' . $cb_item_type.'>' . get_the_title() . '</h1>';


        if ( $cb_meta_onoff == 'on' ) {
            $cb_header .= cb_byline(true, $cb_post_id, false, true);
        }

        $cb_header .= '</span>';

        if ($cb_style != 'standard') { $cb_header .= $cb_post_format_icon; }

        $cb_header .= '</div>';

        if ( $cb_style == 'off' ) {

            $cb_featured_image .= $cb_header;

        } elseif ( $cb_style == 'standard' ) {

            if ( has_post_thumbnail() ) {
                $cb_image = '<div class="cb-mask">' . get_the_post_thumbnail( $post->ID, 'cb-750-400', array('class' => 'cb-fi-standard') ) . $cb_image_credit . $cb_post_format_icon . '</div>';
            }
            $cb_featured_image .= '<header id="cb-standard-featured">';
            $cb_featured_image .= $cb_image;
            $cb_featured_image .= $cb_header;
            $cb_featured_image .= '</header>';

        } elseif ( $cb_style == 'page' ) {

            if ( has_post_thumbnail() ) {

                $cb_page_featured_style = get_post_meta( $cb_post_id, 'cb_page_featured_style', true );

                if ( ( $cb_page_featured_style == '1' ) || ( $cb_page_featured_style == NULL ) ) {

                    $cb_featured_image .= '<header id="cb-standard-featured"><div class="cb-mask">' . get_the_post_thumbnail( $post->ID, 'cb-750-400' ) . '</div></header>';

                } elseif ( $cb_page_featured_style == '2' ) {

                    $cb_page_featured_image_id = get_post_thumbnail_id( $cb_post_id );
                    $cb_page_featured_image_url = wp_get_attachment_image_src( $cb_page_featured_image_id, 'cb-1200-520' );

                    $cb_featured_image .= '<header id="cb-full-width-featured" class="wrap clearfix">';


                    if ( $cb_page_featured_image_url != NULL ) {
                       $cb_featured_image .= '<script type="text/javascript">jQuery(document).ready(function($){
                             $("#cb-full-width-featured").backstretch("' . $cb_page_featured_image_url[0] . '", {speed: 350});
                        });  </script>';
                    }

                    $cb_featured_image .= '</header>';

                } elseif ( $cb_page_featured_style == '4' ) {

                    $cb_featured_image .= '<header id="cb-parallax-featured" class="wrap clearfix">';
                    if ( has_post_thumbnail() ) {
                            $cb_featured_image_id = get_post_thumbnail_id( $post->ID );
                            $cb_featured_image_url = wp_get_attachment_image_src( $cb_featured_image_id, 'cb-thumb-1400' );
                    } else {
                            $cb_featured_image_url = array();
                            $cb_featured_image_url[] = cb_file_location( '/library/images/thumbnail-1400x700.png' );
                    }

                    $cb_featured_image .= '<div class="cb-image" data-type="background" style="background-image: url(' . $cb_featured_image_url[0].')"></div><div id="cb-parallax-bg"></div>';
                    $cb_featured_image .= '</header>';

                } elseif ( $cb_page_featured_style == '5' ) {

                    $cb_featured_image .= '<header id="cb-full-background-featured" class="clearfix">';
                    $cb_bg_slideshow = get_post_meta( $cb_post_id, "cb_bg_image_post");
                    if ( $cb_bg_slideshow != NULL ) {

                            $cb_featured_image .= '<script type="text/javascript">jQuery(document).ready(function($) {
                                                     $.backstretch(["';
                            $i = 0;
                            foreach ($cb_bg_slideshow as $cb_slide) {
                                if ($i != 0) { $cb_featured_image .= '", "'; }

                                $cb_featured_image_url = wp_get_attachment_image_src( $cb_slide, 'cb-thumb-1400' );
                                $cb_featured_image .= $cb_featured_image_url[0];
                                $i++;

                            }

                            $cb_featured_image .= '"], {fade: 750, duration: 5000} ); $(".backstretch").css("position", "absolute" ); }); </script>';

                    } else {

                           if ( has_post_thumbnail() ) {
                                    $cb_featured_image_id = get_post_thumbnail_id($post->ID);
                                    $cb_featured_image_url = wp_get_attachment_image_src( $cb_featured_image_id, 'cb-thumb-1400' );
                           } else {
                               $cb_featured_image_url = array();
                               $cb_featured_image_url[] = cb_file_location('/library/images/thumbnail-1400x700.png');
                           }

                           if ( $cb_featured_image_url != NULL ) {
                               $cb_featured_image .= '<script type="text/javascript">jQuery(document).ready(function($){
                                 $.backstretch("' . $cb_featured_image_url[0] . '", {speed: 350});
                                 $(".backstretch").css("position", "absolute" );
                            });  </script>';
                           }
                    }

                    $cb_featured_image .= '</header>';
                }

            }

        } elseif ( $cb_style == 'full-width' ) {

            if ( has_post_thumbnail() ) {
                $cb_featured_image_id = get_post_thumbnail_id( $post->ID );
                $cb_featured_image_url = wp_get_attachment_image_src( $cb_featured_image_id, 'cb-1200-520' );
            } else {
                $cb_featured_image_url = array();
                $cb_featured_image_url[] = cb_file_location( '/library/images/thumbnail-1200x520.png' );
            }

            $cb_featured_image .= '<header id="cb-full-width-featured" class="wrap clearfix">';

            if ( $cb_featured_image_url != NULL ) {
                $cb_featured_image .= '<script type="text/javascript">jQuery(document).ready(function($){
                $("#cb-full-width-featured").backstretch("' . $cb_featured_image_url[0] . '", {speed: 350});
                });  </script>';
            }
            $cb_featured_image .= $cb_header;
            $cb_featured_image .= '</header>';

        } elseif ( $cb_style == 'full-background' ) {

            $cb_featured_image .= '<header id="cb-full-background-featured" class="clearfix">';
            $cb_bg_slideshow = get_post_meta($cb_post_id, "cb_bg_image_post");

            $cb_featured_image .= '<div class="cb-mask clearfix wrap">' . $cb_header . '</div>';

            if ($cb_bg_slideshow != NULL ) {

                $cb_featured_image .= '<script type="text/javascript">jQuery(document).ready(function($) {
                $.backstretch(["';
                $i = 0;
                foreach ($cb_bg_slideshow as $cb_slide) {
                    if ($i != 0) { $cb_featured_image .= '", "'; }

                    $cb_featured_image_url = wp_get_attachment_image_src( $cb_slide, 'cb-thumb-1400' );
                    $cb_featured_image .= $cb_featured_image_url[0];
                    $i++;

                }

                $cb_featured_image .= '"], {fade: 750, duration: 5000} ); $(".backstretch").css("position", "absolute" ); }); </script>';

            } else {

                if ( ( has_post_thumbnail( $cb_post_id ) ) && ( get_the_post_thumbnail( $cb_post_id ) ) ) {
                    $cb_featured_image_id = get_post_thumbnail_id($cb_post_id);
                    $cb_featured_image_url = wp_get_attachment_image_src( $cb_featured_image_id, 'cb-thumb-1400' );
                } else {
                    $cb_featured_image_url = array();
                    $cb_featured_image_url[] = cb_file_location( '/library/images/thumbnail-1400x700.png' );
                }

                if ( $cb_featured_image_url != NULL ) {
                    $cb_featured_image .= '<script type="text/javascript">jQuery(document).ready(function($){
                    $.backstretch("' . $cb_featured_image_url[0] . '", {speed: 350});
                    $(".backstretch").css("position", "absolute" );
                    });  </script>';
                }
            }

            $cb_featured_image .= '</header>';

        } elseif ( ( $cb_style == 'parallax' ) && ( $cb_is_mobile == false ) ) {

                $cb_featured_image .= '<header id="cb-parallax-featured" class="wrap clearfix">';
                if ( ( has_post_thumbnail( $cb_post_id ) ) && ( get_the_post_thumbnail( $cb_post_id ) ) ) {
                        $cb_featured_image_id = get_post_thumbnail_id( $post->ID );
                        $cb_featured_image_url = wp_get_attachment_image_src( $cb_featured_image_id, 'cb-thumb-1400' );
                } else {
                        $cb_featured_image_url = array();
                        $cb_featured_image_url[] = cb_file_location( '/library/images/thumbnail-1400x700.png' );
                }

                $cb_featured_image .= $cb_header;
                $cb_featured_image .= '<div class="cb-image" data-type="background" style="background-image: url(' . $cb_featured_image_url[0].')"></div><div id="cb-parallax-bg"></div>';
                $cb_featured_image .= '</header>';

        } elseif ( ( $cb_style == 'parallax' ) && ( $cb_is_mobile == true ) ) {

                $cb_featured_image .= '<header id="cb-full-background-featured" class="clearfix">';
                $cb_bg_slideshow = get_post_meta( $cb_post_id, 'cb_bg_image_post' );

                $cb_featured_image .= '<div class="cb-mask clearfix wrap">' . $cb_header . '</div>';


                if ( ( $cb_bg_slideshow != NULL ) && ( count( $cb_bg_slideshow ) > 1 ) ) {

                        $cb_featured_image .= '<script type="text/javascript">jQuery(document).ready(function($) {
                                                 $.backstretch(["';
                        $i = 0;
                        foreach ( $cb_bg_slideshow as $cb_slide ) {
                            if ( $i != 0 ) { $cb_featured_image .= '", "'; }

                            $cb_featured_image_url = wp_get_attachment_image_src( $cb_slide, 'cb-thumb-1400' );
                            $cb_featured_image .= $cb_featured_image_url[0];
                            $i++;

                        }


                        $cb_featured_image .= '"], {fade: 750, duration: 5000} ); $(".backstretch").css("position", "absolute" ); }); </script>';

                } else {

                       if ( has_post_thumbnail() ) {
                                $cb_featured_image_id = get_post_thumbnail_id( $post->ID );
                                $cb_featured_image_url = wp_get_attachment_image_src( $cb_featured_image_id, 'cb-thumb-1400' );
                       } else {
                            $cb_featured_image_url = array();
                            $cb_featured_image_url[] = cb_file_location( '/library/images/thumbnail-1400x700.png' );
                        }

                       if ( $cb_featured_image_url != NULL ) {
                           $cb_featured_image .= '<script type="text/javascript">jQuery(document).ready(function($){
                             $.backstretch("' . $cb_featured_image_url[0] . '", {speed: 350});
                             $(".backstretch").backstretch("destroy", false);
                        });  </script>';
                       }
                }

                $cb_featured_image .= '</header>';

         }

     return $cb_featured_image;
    }
}

if ( ! class_exists( 'cb_walker_backend' ) ) {
    class cb_walker_backend extends Walker_Nav_Menu {
        function start_lvl( &$output, $depth = 0, $args = array() ) {}
        function end_lvl( &$output, $depth = 0, $args = array() ) {}

        function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
            global $_wp_nav_menu_max_depth;
            $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

            $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

            ob_start();
            $item_id = esc_attr( $item->ID );
            if (empty($item->cbmegamenu[0])) {
                $cb_item_megamenu = NULL;
            } else {
                $cb_item_megamenu = esc_attr ($item->cbmegamenu[0]);
            }
            $removed_args = array( 'action','customlink-tab', 'edit-menu-item', 'menu-item', 'page-tab',  '_wpnonce', );

            $original_title = '';
            if ( 'taxonomy' == $item->type ) {
                $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
                if ( is_wp_error( $original_title ) )
                    $original_title = false;
            } elseif ( 'post_type' == $item->type ) {
                $original_object = get_post( $item->object_id );
                $original_title = $original_object->post_title;
            }

            $classes = array(
                'menu-item menu-item-depth-' . $depth,
                'menu-item-' . esc_attr( $item->object ),
                'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
            );

            $title = $item->title;

            if ( ! empty( $item->_invalid ) ) {
                $classes[] = 'menu-item-invalid';
                /* translators: %s: title of menu item which is invalid */
                $title = sprintf( __( '%s (Invalid)' , 'cubell'), $item->title );
            } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
                $classes[] = 'pending';
                /* translators: %s: title of menu item in draft status */
                $title = sprintf( __('%s (Pending)' , 'cubell'), $item->title);
            }

            $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

            $submenu_text = '';
            if ( 0 == $depth )
                $submenu_text = 'style="display: none;"';

            ?>
            <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
                <dl class="menu-item-bar">
                    <dt class="menu-item-handle">
                        <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' , 'cubell'); ?></span></span>
                        <span class="item-controls">
                            <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
                            <span class="item-order hide-if-js">
                                <a href="<?php
                                    echo wp_nonce_url(
                                        add_query_arg(
                                            array(
                                                'action' => 'move-up-menu-item',
                                                'menu-item' => $item_id,
                                            ),
                                            remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                        ),
                                        'move-menu_item'
                                    );
                                ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up', 'cubell'); ?>">&#8593;</abbr></a>
                                |
                                <a href="<?php
                                    echo wp_nonce_url(
                                        add_query_arg(
                                            array(
                                                'action' => 'move-down-menu-item',
                                                'menu-item' => $item_id,
                                            ),
                                            remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                        ),
                                        'move-menu_item'
                                    );
                                ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down', 'cubell'); ?>">&#8595;</abbr></a>
                            </span>
                            <a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
                                echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                            ?>"><?php _e( 'Edit Menu Item' , 'cubell'); ?></a>
                        </span>
                    </dt>
                </dl>

                <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
                    <?php if( 'custom' == $item->type ) : ?>
                        <p class="field-url description description-wide">
                            <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                                <?php _e( 'URL' , 'cubell'); ?><br />
                                <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
                            </label>
                        </p>
                    <?php endif; ?>
                    <p class="description description-thin">
                        <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                            <?php _e( 'Navigation Label' , 'cubell'); ?><br />
                            <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
                        </label>
                    </p>
                    <p class="description description-thin">
                        <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                            <?php _e( 'Title Attribute' , 'cubell' ); ?><br />
                            <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
                        </label>
                    </p>
                    <p class="field-link-target description">
                        <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                            <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
                            <?php _e( 'Open link in a new window/tab' , 'cubell'); ?>
                        </label>
                    </p>
                    <p class="field-css-classes description description-thin">
                        <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                            <?php _e( 'CSS Classes (optional)' , 'cubell'); ?><br />
                            <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
                        </label>
                    </p>
                    <p class="field-xfn description description-thin">
                        <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                            <?php _e( 'Link Relationship (XFN)' , 'cubell'); ?><br />
                            <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
                        </label>
                    </p>
                    <p class="field-cbmegamenu description description-thin">
                         <label for="edit-menu-item-cbmegamenu-<?php echo $item_id; ?>">Valenti Megamenu Type</label>
                         <select id="edit-menu-item-cbmegamenu-<?php echo $item_id; ?>" name="menu-item-cbmegamenu[<?php echo $item_id; ?>]">
                            <option value="2" <?php if ( ( $cb_item_megamenu == '2' ) || ( $cb_item_megamenu == NULL ) ) echo 'selected="selected"'; ?>>Valenti Standard Dropdown</option>
                            <?php if ( $item->object == 'category' ) { ?>
                                <option value="1" <?php if ( $cb_item_megamenu == '1' ) echo 'selected="selected"'; ?>>Valenti Dropdown + Featured/Random + Recent Posts</option>
                                <option value="4" <?php if ( $cb_item_megamenu == '4' ) echo 'selected="selected"'; ?>>Valenti Dropdown + Slider</option>
                           <?php } ?>
                           <option value="3" <?php if ( $cb_item_megamenu == '3' ) echo 'selected="selected"'; ?>>Valenti Megamenu</option>
                         </select>
                    </p>
                    <p class="field-description description description-wide">
                        <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                            <?php _e( 'Description' , 'cubell'); ?><br />
                            <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]">
                                <?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                            <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.' , 'cubell'); ?></span>
                        </label>
                    </p>
                    <p class="field-move hide-if-no-js description description-wide">
                        <label>
                            <span><?php _e( 'Move' , 'cubell'); ?></span>
                            <a href="#" class="menus-move-up"><?php _e( 'Up one' , 'cubell'); ?></a>
                            <a href="#" class="menus-move-down"><?php _e( 'Down one' , 'cubell'); ?></a>
                            <a href="#" class="menus-move-left"></a>
                            <a href="#" class="menus-move-right"></a>
                            <a href="#" class="menus-move-top"><?php _e( 'To the top' , 'cubell'); ?></a>
                        </label>
                    </p>

                    <div class="menu-item-actions description-wide submitbox">
                        <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
                            <p class="link-to-original">
                                <?php printf( __('Original: %s' , 'cubell'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                            </p>
                        <?php endif; ?>
                        <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                        echo wp_nonce_url(
                            add_query_arg(
                                array(
                                    'action' => 'delete-menu-item',
                                    'menu-item' => $item_id,
                                ),
                                admin_url( 'nav-menus.php' )
                            ),
                            'delete-menu_item_' . $item_id
                        ); ?>"><?php _e( 'Remove' , 'cubell'); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
                            ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel' , 'cubell'); ?></a>
                    </div>

                    <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
                    <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
                    <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
                    <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
                    <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
                    <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
                </div><!-- .menu-item-settings-->
                <ul class="menu-item-transport"></ul>
            <?php
            $output .= ob_get_clean();
        }
    }
}

if ( ! function_exists( 'cb_megamenu_walker' ) ) {
    function cb_megamenu_walker($walker) {
            if ( $walker === 'Walker_Nav_Menu_Edit' ) {
                        $walker = 'cb_walker_backend';
                  }
           return $walker;
        }
}
add_filter( 'wp_edit_nav_menu_walker', 'cb_megamenu_walker');

if ( ! function_exists( 'cb_megamenu_walker_save' ) ) {
    function cb_megamenu_walker_save($menu_id, $menu_item_db_id) {

        if  (isset($_POST['menu-item-cbmegamenu'][$menu_item_db_id])) {
                update_post_meta( $menu_item_db_id, '_menu_item_cbmegamenu', $_POST['menu-item-cbmegamenu'][$menu_item_db_id]);
        } else {
            update_post_meta( $menu_item_db_id, '_menu_item_cbmegamenu', '1');
        }
    }
}
add_action( 'wp_update_nav_menu_item', 'cb_megamenu_walker_save', 10, 2 );

if ( ! function_exists( 'cb_megamenu_walker_loader' ) ) {
    function cb_megamenu_walker_loader($menu_item) {
            $menu_item->cbmegamenu = get_post_meta($menu_item->ID, '_menu_item_cbmegamenu', true);
            return $menu_item;
     }
}
add_filter( 'wp_setup_nav_menu_item', 'cb_megamenu_walker_loader' );

if ( ! function_exists( 'cb_gallery_post' ) ) {

    function cb_gallery_post($cb_post_id) {

        $cb_gallery = NULL;
        $cb_output = '<div id="cb-gallery-post" class="wrap clearfix">';

        if ( function_exists( 'rwmb_meta' ) ) {
            $cb_gallery = rwmb_meta( 'cb_gallery_content', $args = array('type' => 'image'), $cb_post_id );
        }

        if ( is_rtl() ) {
            $cb_slider_ltr_rtl = ' style="direction:ltr;"';
        } else {
            $cb_slider_ltr_rtl = NULL;
        }

        if ( $cb_gallery != NULL ) {

            $cb_output .= '<div id="cb-gallery" class="flexslider-gallery" ' . $cb_slider_ltr_rtl . '>';
            $cb_output .= '<ul class="slides">';

            foreach ( $cb_gallery as $cb_img ) {

                $cb_thumbnail_image = wp_get_attachment_image_src($cb_img['ID'], array(1200, 520));
                $cb_output .= '<li>';
                if ($cb_img['caption']!= NULL) {
                    $cb_output .= '<div class="cb-meta"><div class="cb-caption">' . $cb_img['caption'] . '</div></div>';
                }
                $cb_output .= '<a href="' . esc_url( $cb_img['full_url'] ) . '" class="cb-lightbox" title="' . esc_attr( $cb_img['title'] ) . '" rel="cb-gallery-arrows"><img src="' . esc_url( $cb_thumbnail_image[0] ) . '" alt="' . esc_attr( $cb_img['alt'] ) . '"><i class="fa fa-search"></i></a>';
                $cb_output .= '</li>';
            }

           $cb_output .= '</ul></div>';

           $cb_output .= '<div id="cb-carousel" class="flexslider-gallery"' . $cb_slider_ltr_rtl . '>';
           $cb_output .= '<ul class="slides">';

            foreach ( $cb_gallery as $cb_img ) {

                $cb_thumbnail_image = wp_get_attachment_image_src($cb_img['ID'], array(282, 232));
                $cb_output .= '<li>';
                $cb_output .= '<img src="' . esc_url( $cb_thumbnail_image[0] ) . '">';
                $cb_output .= '</li>';

            }

           $cb_output .= '</ul></div>';
        }
        $cb_output .= '</div>';
        return $cb_output;
    }
}

/*********************
GET CUSTOM POST TYPES
*********************/
if ( ! function_exists( 'cb_get_custom_post_types' ) ) {

    function cb_get_custom_post_types() {

        $cb_cpt_list = ot_get_option( 'cb_cpt', NULL );

        $cb_cpt_output = array( 'post' );

        if ( $cb_cpt_list != NULL ) {
            $cb_cpt = explode(',', str_replace(' ', '', $cb_cpt_list ) );

            foreach ( $cb_cpt as $cb_cpt_single ) {
                $cb_cpt_output[] = $cb_cpt_single;
            }
        }

        return $cb_cpt_output;
    }

}


/*********************
NUMERIC PAGINATION
*********************/
if ( ! function_exists( 'cb_page_navi' ) ) {
    function cb_page_navi() {

        $cb_pagination_type = 'off';

        if ( is_home() ) {
            $cb_pagination_type = ot_get_option( 'cb_hp_infinite', 'off' );
        } elseif ( is_category() ) {

            if ( function_exists( 'get_tax_meta' ) ) {
                $cb_cat_id = get_query_var('cat');
                $cb_pagination_type = get_tax_meta( $cb_cat_id, 'cb_cat_infinite' );
            }

        } elseif ( is_tag() ) {

            if ( function_exists( 'get_tax_meta' ) ) {
                $cb_cat_id = get_query_var('tag_id');
                $cb_pagination_type = get_tax_meta( $cb_cat_id, 'cb_cat_infinite' );
            }

        }

         if ( ( $cb_pagination_type == NULL ) || ( $cb_pagination_type == 'off' ) || ( $cb_pagination_type == 'cb-off' ) ) {

            global $wp_query;

            $cb_pagination = paginate_links( array(
                'base'     => str_replace( 99999, '%#%', esc_url( get_pagenum_link(99999) ) ),
                'format'   => '',
                'total'    => $GLOBALS['wp_query']->max_num_pages,
                'current'  => max( 1, get_query_var('paged') ),
                'mid_size' => 2,
                'prev_text' => '<i class="fa fa-long-arrow-left"></i>',
                'next_text' => '<i class="fa fa-long-arrow-right"></i>',
                'type' => 'list',
            ) );

            echo '<nav class="cb-page-navigation"><ol class="cb-page-navi clearfix">' . $cb_pagination . '</ol></nav>';

        } elseif ( $cb_pagination_type == 'infinite-load' ) {

            if (  get_next_posts_link() != NULL ) {
                $cb_load_more_text = __('Load More', 'cubell');
                echo '<nav id="cb-blog-infinite-load" class="cb-infinite-scroll">' . get_next_posts_link( $cb_load_more_text ) . '</nav>';
            } else {
                echo '<div class="cb-no-more-posts">' . __('No more articles', 'cubell') . '</div>';
            }

        } elseif ( $cb_pagination_type == 'infinite-scroll' ) {

            if (  get_next_posts_link() != NULL ) {

                echo '<nav id="cb-blog-infinite-scroll" class="cb-infinite-scroll cb-hidden">' . get_next_posts_link() . '</nav>';
            } else {
                echo '<div class="cb-no-more-posts">' . __('No more articles', 'cubell') . '</div>';
            }
        }
    }
}

/*********************
CATEGORY PAGINATION WITH OFFSET
*********************/
if ( ! function_exists( 'cb_category_offset' ) ) {
    function cb_get_category_offset() {

        $cb_return = NULL;

        if ( function_exists( 'get_tax_meta' ) ) {

            $cb_cat_id = get_query_var('cat');
            $cb_offset = get_tax_meta( $cb_cat_id, 'cb_cat_offset' );

            if ( $cb_offset == 'on' ) {

                $cb_grid_onoff = get_tax_meta( $cb_cat_id, 'cb_cat_featured_op' );
                $cb_grid_size = substr( $cb_grid_onoff, - 1);

                if ( is_numeric( $cb_grid_size ) == true ) {
                    $cb_return = $cb_grid_size;
                }

                if ( ( $cb_grid_onoff == 'full-slider' ) || ( $cb_grid_onoff == 'slider' ) ) {
                    $cb_return = 12;
                }

            }
        }

        return $cb_return;
    }
}


/*********************
BLOG HOMEPAGE PAGINATION WITH OFFSET
*********************/
if ( ! function_exists( 'cb_get_bloghome_offset' ) ) {
    function cb_get_bloghome_offset() {

        $cb_return = NULL;
        $cb_offset = ot_get_option( 'cb_hp_offset', 'off' );

        if ( $cb_offset == 'on' ) {

            $cb_grid_onoff = ot_get_option( 'cb_hp_gridslider', 'cb_full_off' );
            $cb_grid_size = substr( $cb_grid_onoff, - 1);

            if ( is_numeric( $cb_grid_size ) == true ) {
                $cb_return = $cb_grid_size;
            }

            if ( ( $cb_grid_onoff == 'full-slider' ) || ( $cb_grid_onoff == 'slider' ) || ( $cb_grid_onoff == 's-1' ) ) {
                $cb_return = 12;
            }

        }

        return $cb_return;
    }
}



/*********************
PAGINATION WITH OFFSET
*********************/
if ( ! function_exists( 'cb_pagination_offset' ) ) {
    function cb_pagination_offset($found_posts, $query) {

        if ( is_category() == true ) {

            $cb_grid_size = cb_get_category_offset();
            $found_posts = $found_posts - $cb_grid_size;

        }

        if ( is_home() == true ) {

            $cb_grid_size = cb_get_bloghome_offset();
            $found_posts = $found_posts - $cb_grid_size;

        }

        return $found_posts ;
    }
}
add_filter('found_posts', 'cb_pagination_offset', 1, 2 );

/*********************
OFFSETTING QUERY VARIABLE['cb_offset_loop']
*********************/
if ( ! function_exists( 'cb_offset_loop_pre_get_posts' ) ) {
    function cb_offset_loop_pre_get_posts( $query ){

        if ( isset( $query->query_vars['cb_offset_loop'] ) && ( $query->query_vars['cb_offset_loop'] == 'on' ) ) {

            if ( is_category() == true ) { $cb_grid_size = cb_get_category_offset(); }
            if ( is_home() == true ) { $cb_grid_size = cb_get_bloghome_offset(); }

            $cb_posts_per_page = get_option('posts_per_page');

            if ( $query->is_paged == true ) {

                $cb_page_offset = $cb_grid_size + ( ( $query->query_vars['paged'] - 1 ) * $cb_posts_per_page );
                $query->set( 'offset', $cb_page_offset );

            } else {

                $query->set( 'offset', $cb_grid_size );

            }
        }

         if ( ( is_category() || is_tag() || is_home() ) && $query->is_main_query() && ( ! is_admin() ) ) {

            $cb_cpt_output = cb_get_custom_post_types();
            $query->set( 'post_type', $cb_cpt_output );

        }

        return $query;
    }
}
add_action( 'pre_get_posts', 'cb_offset_loop_pre_get_posts' );

/*********************
ADD QUERY VAR FOR OFFSET WP_QUERY
*********************/
if ( ! function_exists( 'cb_add_query_variable' ) ) {
    function cb_add_query_variable( $query_vars ){

        array_push($query_vars, 'cb_offset_loop');
        return $query_vars;

    }
}

add_filter( 'query_vars', 'cb_add_query_variable' );


/*********************
WOOCOMMERCE DISQUS
*********************/
if ( ! function_exists( 'cb_disqus_woocommerce' ) ) {
    function cb_disqus_woocommerce( $post ) {

        $cb_post_id = $post->ID;
        $cb_post_title = $post->post_title;
        $cb_disqus_forum_shortname = ot_get_option('cb_disqus_shortname', NULL);

        wp_enqueue_script( 'cb_disqus', '//' . $cb_disqus_forum_shortname . '.disqus.com/embed.js' );
        echo '<div id="disqus_thread"></div>
        <script type="text/javascript">
            var disqus_shortname = "' . $cb_disqus_forum_shortname . '";
            var disqus_title = "' . $cb_post_title . '";
            var disqus_url = "' . get_permalink( $cb_post_id ) . '";
            var disqus_identifier = "' . $cb_disqus_forum_shortname . '-' . $cb_post_id . '";
        </script>';
    }
}

if ( ! function_exists( 'cb_disqus_woocommerce_fix' ) ) {

    function cb_disqus_woocommerce_fix(  $file = '/comments.php', $separate_comments = false ) {

        $cb_current_post_type = get_post_type();

        if ( $cb_current_post_type == 'product' ) {

            remove_filter( 'comments_template', 'dsq_comments_template' );
        }

        return $file;
    }
}

add_filter( 'comments_template' , 'cb_disqus_woocommerce_fix', 2 );

if ( ! function_exists( 'cb_ajax_post_search' ) ) {
    function cb_ajax_post_search() {

        $args = NULL;
        if ( ! current_user_can( 'edit_post', $args ) ) {
            die();
        }

        global $wpdb;
        $cb_current_string = trim( stripslashes( sanitize_text_field( $_GET['q'] ) ) );
        $cb_cpt_output = cb_get_custom_post_types();

        $cb_featured_qry = array( 's' => $cb_current_string, 'post_type' => $cb_cpt_output, 'posts_per_page' => -1,  'post_status' => 'publish' );
        $cb_qry = new WP_Query( $cb_featured_qry );
        $cb_post_array = array();

        if ( $cb_qry->have_posts() ) {

            $cb_output = wp_list_pluck( $cb_qry->posts, 'post_title' );
            echo join( $cb_output, "\n" );
        }

        wp_die();
    }
}

add_action( 'wp_ajax_cb-ajax-post-search', 'cb_ajax_post_search' );

if ( ! function_exists( 'ot_type_text' ) ) {

  function ot_type_text( $args = array() ) {

    /* turns arguments array into variables */
    extract( $args );

    /* verify a description */
    $has_desc = $field_desc ? true : false;

    /* format setting outer wrapper */
    echo '<div class="format-setting type-text ' . ( $has_desc ? 'has-desc' : 'no-desc' ) . '">';

      /* description */
      echo $has_desc ? '<div class="description">' . htmlspecialchars_decode( $field_desc ) . '</div>' : '';

      /* format setting inner wrapper */
      echo '<div class="format-setting-inner">';


        if ( ( esc_attr( $field_class ) ) == 'cb-aj-input' ) {
            echo '<input type="text" name="' . esc_attr( $field_name ) . '" id="cbaj_' . esc_attr( $field_id ) . '" value="" class="widefat option-tree-ui-input ' . esc_attr( $field_class ) . '" placeholder="Add Post" />';
            echo '<input type="text" name="' . esc_attr( $field_name ) . '" id="cbraj_' . esc_attr( $field_id ) . '" value="' . esc_attr( $field_value ) . '" class="cb-pb-hidden widefat option-tree-ui-input ' . esc_attr( $field_class ) . '" />';

        } else {
            echo '<input type="text" name="' . esc_attr( $field_name ) . '" id="' . esc_attr( $field_id ) . '" value="' . esc_attr( $field_value ) . '" class="widefat option-tree-ui-input ' . esc_attr( $field_class ) . '" />';
        }

      echo '</div>';

    echo '</div>';

  }

}


/*********************
FILE LOCATION CHECK
*********************/
if ( ! function_exists( 'cb_file_location' ) ) {
    function cb_file_location( $cb_file_name ) {

        $cb_file_name_ext = substr( $cb_file_name, -3 );

        if ( $cb_file_name_ext == 'php' ) {

            $cb_get_stylesheet = get_stylesheet_directory();
            $cb_get_template = get_template_directory();

        } else {

            $cb_get_stylesheet = get_stylesheet_directory_uri();
            $cb_get_template = get_template_directory_uri();
        }

        if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $cb_file_name ) ) {

            $cb_file_url = trailingslashit( $cb_get_stylesheet ) . $cb_file_name;
            return $cb_file_url;

        } elseif ( file_exists( trailingslashit( get_template_directory() ) . $cb_file_name ) ) {

            $cb_file_url = trailingslashit( $cb_get_template ) . $cb_file_name;
            return $cb_file_url;

        }

    }
}

/*********************
ADD META OF FEATURED IMAGE
*********************/
if ( ! function_exists( 'cb_meta_image_head' ) ) {
    function cb_meta_image_head() {

        if ( ( is_single() == true ) && ( ! class_exists( 'WPSEO_Admin' ) ) ) {
            if ( has_post_thumbnail() ) {
                global $post;
                $cb_featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
                echo '<meta property="og:image" content="' . $cb_featured_image[0] . '">';

            }
        }
    }
}
add_action('wp_head', 'cb_meta_image_head');


/*********************
GET POST VIEW COUNT IN POST
*********************/
if ( ! function_exists( 'cb_get_post_viewcount' ) ) {
    function cb_get_post_viewcount( $cb_post_id ) {
        $cb_stats_src = ot_get_option('cb_misc_stats_source', 'theme');

        if ( $cb_stats_src != 'theme' ) {
            if ( function_exists( 'stats_get_csv' ) ) {

                if ( $cb_args == NULL ) {
                    $cb_args = 'period=month&days=104&post_id=' . $cb_post_id;
                    $cb_args = 'days=-1&post_id=' . $cb_post_id; /* CBTEMP */
                }

                $cb_post_stats = 'cb-post-views-t-' . $cb_post_id;

                if ( ( $cb_post_view_count = get_transient( $cb_post_stats ) ) === false ) {
                    $cb_post_view_count = stats_get_csv( 'postviews', $cb_args );
                    set_transient( $cb_post_stats, $cb_post_view_count, 180 );  
                }

                if ( $cb_post_view_count[0]['views'] != NULL ) {
                    return $cb_post_view_count[0]['views'];
                } else {
                    return '0';
                }
                
            } else {
                return '0';
            }

        } else {
            $cb_visits = get_post_meta( $cb_post_id, 'cb_visit_counter', true );
        }
        
        return $cb_visits;
    }
}

/*********************
VISIT COUNTER
*********************/
if ( ! function_exists( 'cb_visit_counter' ) ) {
    function cb_visit_counter( $cb_post_id ) {

        $cb_visits = get_post_meta( $cb_post_id, 'cb_visit_counter', true );

        if  ( strlen( $cb_visits ) == 0 ) {

            delete_post_meta( $cb_post_id, 'cb_visit_counter' );
            add_post_meta( $cb_post_id, 'cb_visit_counter', 1 );
        } else {

            update_post_meta( $cb_post_id, 'cb_visit_counter', $cb_visits + 1 );

        }

    }
}

if ( ! function_exists( 'ot_type_numeric_slider' ) ) {

  function ot_type_numeric_slider( $args = array() ) {
    
    /* turns arguments array into variables */
    extract( $args );
    
    /* verify a description */
    $has_desc = $field_desc ? true : false;
    
    $_options = explode( ',', $field_min_max_step );
    $min = isset( $_options[0] ) ? $_options[0] : 0;
    $max = isset( $_options[1] ) ? $_options[1] : 100;
    $step = isset( $_options[2] ) ? $_options[2] : 1;

    if ( ( $args['field_id'] == 'cb_logo_nav_left' ) && ( $field_value == NULL ) ) {
        $field_value = '0';
    }

    /* format setting outer wrapper */
    echo '<div class="format-setting type-numeric-slider ' . ( $has_desc ? 'has-desc' : 'no-desc' ) . '">';
      
      /* description */
      echo $has_desc ? '<div class="description">' . htmlspecialchars_decode( $field_desc ) . '</div>' : '';
      
      /* format setting inner wrapper */
      echo '<div class="format-setting-inner">';

        echo '<div class="ot-numeric-slider-wrap">';

          echo '<input type="hidden" name="' . esc_attr( $field_name ) . '" id="' . esc_attr( $field_id ) . '" class="ot-numeric-slider-hidden-input" value="' . esc_attr( $field_value ) . '" data-min="' . esc_attr( $min ) . '" data-max="' . esc_attr( $max ) . '" data-step="' . esc_attr( $step ) . '">';

          echo '<input type="text" class="ot-numeric-slider-helper-input widefat option-tree-ui-input ' . esc_attr( $field_class ) . '" value="' . esc_attr( $field_value ) . '" readonly>';

          echo '<div id="ot_numeric_slider_' . esc_attr( $field_id ) . '" class="ot-numeric-slider"></div>';

        echo '</div>';
      
      echo '</div>';
      
    echo '</div>';
  }

}

if ( ! function_exists( 'woocommerce_template_single_title' ) ) {

    function woocommerce_template_single_title() {
       return;
    }
}



add_filter( 'ot_post_formats', '__return_true' );

if ( ! function_exists( 'cb_ot_meta_box_post_format_video' ) ) {
    function cb_ot_meta_box_post_format_video() { 

        $cb_cpt_output = cb_get_custom_post_types();
        return array(
            'id'        => 'ot-post-format-video',
            'title'     => 'Valenti Post Format: Video',
            'desc'      => '',
            'pages'     => $cb_cpt_output,
            'context'   => 'side',
            'priority'  => 'low',
            'fields'    => array(
                array(
                'id'          => 'cb_video_post_select',
                'label'       => '',
                'desc'        => '',
                'std'         => '1',
                'section'     => 'option_types',
                'type'        => 'select',
                'rows'        => '1',
                'post_type'   => '',
                'taxonomy'    => '',
                'min_max_step'=> '',
                'class'       => '',
                'condition'   => '',
                'operator'    => 'and',
                'choices'     => array(
                          array(
                            'value'       => '1',
                            'label'       => 'Video Embed limited by featured image size',
                            'src'         => ''
                          ),
                          array(
                            'value'       => '2',
                            'label'       => 'Video embed opens in lightbox',
                            'src'         => ''
                          ),
                        ),
                ),
              array(
                'id'      => 'cb_video_embed_code_post',
                'label'   => '',
                'desc'    => 'Video iframe embed code. NOTE: You need a featured image for Valenti Video Post play button to appear.',
                'std'     => '',
                'type'    => 'textarea',
              )
            )
        );
    }
}
add_filter( 'ot_meta_box_post_format_video', 'cb_ot_meta_box_post_format_video' );

if ( ! function_exists( 'cb_ot_meta_box_post_format_gallery' ) ) {
    function cb_ot_meta_box_post_format_gallery() { 

        $cb_cpt_output = cb_get_custom_post_types();
        return array(

            'id'        => 'ot-post-format-gallery',
            'title'     => 'Valenti Post Format: Gallery',
            'desc'      => '',
            'pages'     => $cb_cpt_output,
            'context'   => 'side',
            'priority'  => 'low',
            'fields'    => array(
              
            )
        );
    }
}
add_filter( 'ot_meta_box_post_format_gallery', 'cb_ot_meta_box_post_format_gallery' );

if ( ! function_exists( 'cb_ot_meta_box_post_format_audio' ) ) {
    function cb_ot_meta_box_post_format_audio() { 

        $cb_cpt_output = cb_get_custom_post_types();
        return array(
            'id'        => 'ot-post-format-audio',
            'title'     => __( 'Valenti Post Format: Audio', 'option-tree' ),
            'desc'      => '',
            'pages'     => $cb_cpt_output,
            'context'   => 'side',
            'priority'  => 'low',
            'fields'    => array(
              array(
                'id'      => 'cb_soundcloud_embed_code_post',
                'label'   => '',
                'desc'    => 'Audio Embed Code',
                'std'     => '',
                'type'    => 'textarea'
              )
            )
        );
    }
}
add_filter( 'ot_meta_box_post_format_audio', 'cb_ot_meta_box_post_format_audio' );

/*********************
ADMIN IMAGES URL
*********************/
if ( ! function_exists( 'cb_ot_type_radio_image_src' ) ) {
    function cb_ot_type_radio_image_src( $src ) { 
        return  get_template_directory_uri() . '/library/admin/images' . $src; 
    }
}
add_filter( 'ot_type_radio_image_src', 'cb_ot_type_radio_image_src' );

/*********************
INSERT TEXT
*********************/
if ( ! function_exists( 'cb_ot_upload_text' ) ) {
    function cb_ot_upload_text() { 
        return 'Insert'; 
    }
}
add_filter( 'ot_upload_text', 'cb_ot_upload_text' );

/*********************
OT VERSION
*********************/
if ( ! function_exists( 'cb_ot_header_version_text' ) ) {
    function cb_ot_header_version_text() { 
        return ''; 
    }
}
add_filter( 'ot_header_version_text', 'cb_ot_header_version_text' );

/*********************
ADMIN LOGO
*********************/
if ( ! function_exists( 'cb_ot_header_logo_link' ) ) {
    function cb_ot_header_logo_link() { 
        return '<img src="' . get_template_directory_uri() . '/library/admin/images/logo.png">';
    }
}
add_filter( 'ot_header_logo_link', 'cb_ot_header_logo_link' );


/*********************
ADMIN OT CSS
*********************/
if ( ! function_exists( 'cb_ot_css' ) ) {
    function cb_ot_css($hook) {

        global $wp_styles;
        wp_register_style( 'cb-ot-admin-css',  get_template_directory_uri(). '/library/admin/css/ot-admin.css', array(), '4.2' );
        wp_enqueue_style('cb-ot-admin-css'); // enqueue it
        $wp_styles->add_data( 'cb-ot-admin-css', 'rtl', true );
    }
}

add_action( 'ot_admin_styles_after', 'cb_ot_css' );

/*********************
LOGO
*********************/
if ( ! function_exists( 'cb_logo' ) ) {
    function cb_logo() {

        $cb_logo = ot_get_option( 'cb_logo_url', NULL );
        $cb_retina_logo = ot_get_option( 'cb_logo_retina_url', NULL );
        $cb_retina_logo_src = $cb_logo_classes = NULL;
        $cb_banner = ot_get_option( 'cb_banner_selection', false );
        if ( $cb_banner == 'cb_banner_728' ) { $cb_logo_classes = 'cb-with-large'; }
        
        if ( $cb_logo != NULL ) {

            if ( $cb_retina_logo != NULL ) {
                $cb_retina_logo_src = 'data-at2x="' . $cb_retina_logo . '"';
            }

            ?>
                <div id="logo" <?php if ( $cb_logo_classes == 'cb-with-large' ) { echo 'class="cb-with-large"'; } ?>>
                    <a href="<?php echo esc_url( home_url() );?>">
                        <img src="<?php  echo $cb_logo; ?>" alt="<?php bloginfo('name'); ?> logo" <?php echo $cb_retina_logo_src; ?>>
                    </a>
                </div>
            <?php
        }
    }
}

/*********************
HEADER BANNER
*********************/
if ( ! function_exists( 'cb_header_banner' ) ) {
    function cb_header_banner() {
        $cb_banner = ot_get_option( 'cb_banner_selection', NULL );
        $cb_banner_code = ot_get_option( 'cb_banner_code', NULL );
        $cb_output = NULL;

        if ( is_home() || is_category() || is_tag() || is_singular() || is_archive() ) {

            if ( $cb_banner == 'cb_banner_468' ) {

                $cb_output = '<div class="cb-medium cb-block">' . do_shortcode( $cb_banner_code ) . '</div>';

            } elseif ( $cb_banner == 'cb_banner_728' ) {

                $cb_output =  '<div class="cb-large cb-block">'. do_shortcode( $cb_banner_code ) . '</div>';

            }
        }

        echo $cb_output;
    }
}

/*********************
WOOCOMMERCE PAGINATION
*********************/
if ( ! function_exists( 'cb_woocommerce_pagi' ) ) {
    function cb_woocommerce_pagi() {
        global $wp_query;

        if ( $wp_query->max_num_pages <= 1 ) {
            return;
        }
        return array(
            'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
            'format'       => '',
            'add_args'     => '',
            'current'      => max( 1, get_query_var( 'paged' ) ),
            'total'        => $wp_query->max_num_pages,
            'prev_text'     => '<i class="fa fa-long-arrow-left"></i>',
            'next_text'     => '<i class="fa fa-long-arrow-right"></i>',
            'type'         => 'list',
            'end_size'     => 3,
            'mid_size'     => 3
        );
    }
}

add_filter('woocommerce_pagination_args', 'cb_woocommerce_pagi' );

/*********************
GET FEATURED IMAGE STYLE
*********************/
if ( ! function_exists( 'cb_get_post_fis' ) ) {
    function cb_get_post_fis( $cb_post_id ) {

        $cb_featured_image_style = get_post_meta( $cb_post_id, 'cb_featured_image_style', true );

        return ' cb-fis-type-' . $cb_featured_image_style;
    }
}

/*********************
POST FOOTER AD
*********************/
if ( ! function_exists( 'cb_post_footer_ad' ) ) {
    function cb_post_footer_ad() {

        $cb_ad = ot_get_option('cb_post_footer_ad', NULL);
        if ( $cb_ad != NULL ) {
            echo '<div class="cb-post-large cb-post-footer-block clearfix">' . $cb_ad . '</div>';
        }
        

    }
}

?>