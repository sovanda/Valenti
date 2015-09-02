<?php /* Category/Blog Style B */

$i = 0;

if ( is_home() || is_category() ) {

  $cb_current_cat = get_query_var('cat');
  $cb_cpt_output = cb_get_custom_post_types();
  $cb_paged = get_query_var('paged');
  $cb_grid_size = NULL;

  if ( $cb_paged == false ) {
    $cb_paged = 1;
  }

  if ( is_category() == true ) {
    $cb_grid_size = cb_get_category_offset();
  } elseif ( is_home() == true ) {
    $cb_grid_size = cb_get_bloghome_offset();
  }

  if ( $cb_grid_size != NULL ) {
    $cb_offset_loop = 'on';
  } else {
    $cb_offset_loop = NULL;
  }

  $cb_featured_qry = array( 'post_type' => $cb_cpt_output, 'cat' => $cb_current_cat, 'offset' => $cb_grid_size, 'orderby' => 'date', 'order' => 'DESC',  'post_status' => 'publish', 'cb_offset_loop' => $cb_offset_loop, 'paged' => $cb_paged );
  $cb_qry = new WP_Query( $cb_featured_qry );

} else {

  global $wp_query;
  $cb_qry = $wp_query;

}

if ( ! isset( $cb_category_color_style ) ) {
  $cb_category_color_style = NULL;
}


if ( $cb_qry->have_posts() ) : while ( $cb_qry->have_posts() ) : $cb_qry->the_post();

    $cb_meta_onoff = ot_get_option('cb_meta_onoff', 'on');
    $cb_cat_id = get_the_category($post->ID);
    $cb_post_id = $post->ID;
    $cb_post_format_icon = cb_post_format_check($cb_post_id);
    $cb_category_color = cb_get_cat_color($cb_post_id);

    if ($i  % 2 == 0) {
        $cb_side = NULL;
        $cb_closer = true;
        echo '<div class="cb-double clearfix">';
    } else {
        $cb_side = ' cb-last';
        $cb_closer = false;
    }
?>

<article id="post-<?php the_ID(); ?>" class="cb-blog-style-b clearfix<?php if (is_sticky()) echo ' sticky'; echo $cb_side;  if ( $cb_category_color_style != NULL ) { echo ' ' . $cb_category_color_style; }  ?>" role="article">

  <div class="cb-mask" style="background-color:<?php echo $cb_category_color;?>;">
       <?php
                    cb_thumbnail('360', '240');
                    echo cb_review_ext_box($cb_post_id, $cb_category_color);
                    echo $cb_post_format_icon;
      ?>
  </div>

  <div class="cb-meta">

     <h2 class="h4"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
   	 <?php echo cb_byline(); ?>
     <div class="cb-excerpt"><?php echo cb_clean_excerpt(150, false); ?></div>

  </div>

</article>

<?php
        if ($i  % 2 != 0) { echo '</div>'; }
        $i++;
        endwhile;
        if ($cb_closer == true) { echo '</div>'; }
        cb_page_navi();
        endif;
?>