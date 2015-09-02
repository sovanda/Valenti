<?php
    get_header();
    $cb_full_feature = ot_get_option( 'cb_hp_gridslider', 'cb_full_off' );
    $cb_blog_style = ot_get_option( 'cb_blog_style', 'style-a' );
    $cb_hp_ad = ot_get_option( 'cb_hp_ad', NULL );
    $cb_full_feature_cats = ot_get_option( 'cb_gridslider_category', '' );

    if ( $cb_blog_style == 'style-c' ) {

        $cb_blog_width = 'cb-full-width';

    } else {

        $cb_blog_width = 'cb-standard';
    }
?>

<div id="cb-content" class="wrap clearfix">

    <?php if ( $cb_full_feature != 'cb_full_off' ) {

        if ( $cb_full_feature == 's-2' ) {
            echo '<div id="main" class="' . $cb_blog_width .' clearfix" role="main">';
            $cb_section = 'b';
        } else {
            $cb_section = 'a';
        }

                    $cb_flipped = $cb_title = $cb_module_style = $cb_offset = $cb_order = $cb_orderby = $cb_filter = $cb_tag_id = $cb_post_ids = NULL;
                    $j = 0;
                    if ( $cb_full_feature_cats == NULL ) {
                         $cb_full_feature_cats = get_terms( 'category', array('fields' => 'ids') );
                    }
                    $cb_cat_id = implode( ',', $cb_full_feature_cats );
                    include( locate_template( 'library/modules/cb-' . $cb_full_feature . '.php' ) );
     } ?>

    <?php  if ( $cb_full_feature != 's-2' ) { ?>
        <div id="main" class="<?php echo $cb_blog_width; ?> clearfix" role="main">
    <?php } ?>

    <?php
        if ( $cb_hp_ad != NULL ) {
            echo '<div class="cb-category-top">' . do_shortcode( $cb_hp_ad ) . '</div>';
        }
    ?>

      <?php get_template_part( 'cat', $cb_blog_style ); ?>

    </div> <!-- end #main -->

    <?php if ( $cb_blog_style != 'style-c' ) { get_sidebar(); } ?>

</div> <!-- end #cb-content -->

<?php get_footer(); ?>