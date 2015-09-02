<?php
        get_header();
        $cb_page_id = get_the_ID();
        $cb_breadcrumbs = ot_get_option('cb_breadcrumbs', 'on');
        $cb_theme_style = ot_get_option('cb_theme_style', 'cb_boxed');
        $cb_page_base_color = get_post_meta($cb_page_id , 'cb_overall_color_post', true );
        $cb_page_featured_style = get_post_meta( $cb_page_id, 'cb_page_featured_style', true );
        $cb_page_comments = get_post_meta( $cb_page_id, 'cb_page_comments', 'off' );

        if ( ( $cb_page_base_color == '#' ) || ( $cb_page_base_color == NULL ) ) {
            $cb_page_base_color = ot_get_option('cb_base_color', '#eb9812');
        }

        if ( ( class_exists('Woocommerce') ) && ( ( is_cart() == true ) || ( is_checkout() == true ) || ( is_order_received_page() == true ) || ( is_account_page() == true ) ) ) {
        	$cb_page_base_color = ot_get_option('cb_woocommerce_global_color', '#eb9812');
        }

        if ( ( $cb_page_featured_style == NULL ) || ( $cb_page_featured_style == '4' ) || ( $cb_page_featured_style == '5' ) ) {
         	echo cb_featured_image( $post, 'page' );
         }

?>
        <div id="cb-content" class="wrap clearfix">

	        <div class="cb-cat-header" style="border-bottom-color:<?php echo $cb_page_base_color; ?>;">

	                <h1 id="cb-cat-title" ><?php echo the_title(); ?></h1>

	        </div>

	        <?php  if ( $cb_breadcrumbs != 'off' ) { echo cb_breadcrumbs(); } ?>

	        <?php
				         if (have_posts()) : while (have_posts()) : the_post();

				         if ( $cb_page_featured_style == '2' ) {
                         	echo cb_featured_image( $post, 'page' );
	                     }
			?>

			<div id="main" class="entry-content clearfix" role="main">

				<?php
				         if ( ( $cb_page_featured_style == NULL ) || ( $cb_page_featured_style == '1' )) {
                         	echo cb_featured_image( $post, 'page' );
	                     }
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

					<section class="entry-content clearfix" itemprop="articleBody">
						<?php the_content(); ?>
			     	</section> <!-- end article section -->

					<footer class="article-footer">

                        <?php                                            

							wp_link_pages('before=<div class="cb-post-pagination clearfix">&after=</div>&next_or_number=number&pagelink=<span class="wp-link-pages-number">%</span>');
							the_tags('<p class="cb-tags"><span class="tags-title">' . __('Tags:', 'cubell') . '</span> ', '', '</p>');
							if ( $cb_page_comments == 'on' ) { comments_template(); } 

                        ?>

					</footer> <!-- end article footer -->

					<?php   ?>

				</article> <!-- end article -->

				<?php endwhile; endif; ?>

			</div> <!-- end #main -->

			<?php get_sidebar(); ?>

		</div> <!-- end #cb-content -->

<?php get_footer(); ?>