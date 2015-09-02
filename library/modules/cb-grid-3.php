<?php /* Grid of 3 Articles */

$i = 1;
$cb_cpt_output = cb_get_custom_post_types();
if ( is_home() == true ) {

        $cb_featured_qry = array( 'post_type' => $cb_cpt_output, 'cat' => $cb_cat_id, 'meta_key' => 'cb_featured_post', 'posts_per_page' => '3', 'orderby' => 'date', 'order' => 'DESC',  'post_status' => 'publish', 'meta_value' => 'featured',  'meta_compare' => '==', 'ignore_sticky_posts' => true );
        $cb_qry = new WP_Query( $cb_featured_qry );

        if ( ( $cb_qry->post_count == 0 ) || ( $cb_qry->post_count == NULL ) ) {
            $cb_qry = NULL;
            $cb_qry = new WP_Query(array( 'posts_per_page' => '3', 'cat' => $cb_cat_id, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'post_status' => 'publish', 'ignore_sticky_posts' => true )  );
        }

} elseif ( is_category() == true) {

    $cb_title = $cb_module_style = NULL;

    $cb_current_cat = get_query_var('cat');
    $cb_featured_qry = array( 'post_type' => $cb_cpt_output, 'meta_key' => 'cb_featured_cat_post', 'cat' => $cb_current_cat, 'posts_per_page' => '3', 'orderby' => 'date', 'order' => 'DESC',  'post_status' => 'publish', 'meta_value' => 'featured',  'meta_compare' => '==', 'ignore_sticky_posts' => true );

    $cb_qry = new WP_Query( $cb_featured_qry );
    if ( $cb_qry->post_count == 0 ) {
        $cb_qry = NULL;
        $cb_qry = new WP_Query(array( 'posts_per_page' => '3', 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'cat' => $cb_current_cat, 'post_status' => 'publish', 'ignore_sticky_posts' => true )  );
    }

} else {
    $cb_qry = NULL;
    $cb_qry = new WP_Query( array( 'posts_per_page' => '3', 'cat' => $cb_cat_id, 'tag__in' => $cb_tag_id, 'post__in' => $cb_post_ids, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'offset' => $cb_offset, 'order' => $cb_order, 'orderby' => $cb_orderby ) );
}

if ( $cb_qry->have_posts() ) : while ( $cb_qry->have_posts() ) : $cb_qry->the_post();

$cb_post_id = $post->ID;
$cb_category_color = cb_get_cat_color( $cb_post_id );

if ($cb_title != NULL) {
    $cb_title_header = '<div class="cb-module-header" style="border-bottom-color:' . $cb_category_color . ';"><h2 class="cb-module-title" >' . $cb_title . '</h2>' . $cb_subtitle . '</div>';
} else {
    $cb_title_header = NULL;
}

$cb_feature_width = '400';
$cb_feature_height = '250';

if ( $i  == 1 ) {
    $cb_feature_width = '750';
    $cb_feature_height = '400';
    echo '<div class="cb-grid-block ' . $cb_module_style . ' clearfix">' . $cb_title_header . '<div class="cb-grid-3 cb-module-block clearfix">';
}
?>
<div class="cb-feature-<?php echo $i; ?>">

    <div class="cb-grid-img">
        <?php cb_thumbnail( $cb_feature_width, $cb_feature_height ); ?>
    </div>

    <div class="cb-article-meta">

        <h2><a href="<?php the_permalink() ?>"><?php echo get_the_title(); ?></a></h2>
        <?php echo cb_byline(); ?>

    </div>

    <a href="<?php the_permalink() ?>" class="cb-link"></a>

</div>

<?php

$i++;
endwhile; endif;
echo '</div></div>';
wp_reset_postdata();  // Restore global post data

?>