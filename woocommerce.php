<?php
        get_header();
        $cb_theme_style = ot_get_option('cb_theme_style', 'cb_boxed');
        $cb_woocommerce_sidebar = ot_get_option('cb_woocommerce_sidebar', 'sidebar');
        $cb_woocommerce_sidebar_override = ot_get_option('cb_woocommerce_sidebar_override', 'sidebar');
        $cb_woocommerce_comments_onoff = ot_get_option('cb_woocommerce_comments_onoff', 'cb_comments_off');
        $cb_sidebar_position = NULL;
        $cb_breadcrumbs = ot_get_option('cb_breadcrumbs', 'on');

        if ( $cb_woocommerce_sidebar == 'sidebar_left' ) {
            $cb_sidebar_position = ' cb-sidebar-left ';
        } elseif ( $cb_woocommerce_sidebar == 'nosidebar' ) {
             $cb_sidebar_position = ' cb-full-width';
        }

        if ( ( $cb_woocommerce_sidebar_override == 'cb_no_posts' ) && ( is_product() == true ) ) {
            $cb_woocommerce_sidebar = 'nosidebar';
            $cb_sidebar_position = ' cb-full-width';
        }

        if ( ( $cb_woocommerce_sidebar_override == 'cb_no_shop' ) && ( is_shop() == true ) ) {
            $cb_woocommerce_sidebar = 'nosidebar';
            $cb_sidebar_position = ' cb-full-width';
        }

?>
		<div id="cb-content" class="wrap clearfix">

            <div class="cb-cat-header<?php if ( $cb_theme_style == 'cb_boxed' ) { echo ' wrap'; } ?> cb-woocommerce-page">

                <h1 id="cb-cat-title" >
                    <?php
                        if ( is_shop() == true ) {
                            woocommerce_page_title();
                        } elseif ( ( is_product_category() == true ) || ( is_product_tag() == true ) ) {

                            global $wp_query;
                            $cb_current_object = $wp_query->get_queried_object();
                            echo $cb_current_object->name;

                        } else {
                            the_title();
                        }
                    ?>
                </h1>

            </div>

            <?php if ( $cb_breadcrumbs != 'off'  ) { echo cb_breadcrumbs(); } ?>
			<div id="main" class="entry-content clearfix<?php  echo $cb_sidebar_position; ?>" role="main">

				<?php woocommerce_content(); ?>

                <?php
                        if ( $cb_woocommerce_comments_onoff != 'cb_comments_off' ) {

                            if ( $cb_woocommerce_comments_onoff == 'cb_disqus_comments_on' ) {

                                cb_disqus_woocommerce( $post );

                            } else {

                                comments_template();
                            }
                        }
                ?>

			</div> <!-- end #main -->

			<?php if ( $cb_woocommerce_sidebar != 'nosidebar'  ) {  get_sidebar(); } ?>

		</div> <!-- end #cb-content -->

<?php get_footer(); ?>