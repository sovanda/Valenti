<?php 
        get_header();
        $cb_comments_onoff = ot_get_option('cb_comments_onoff', 'on'); 
        $cb_social_sharing = ot_get_option('cb_social_sharing', 'on'); 
        $cb_post_id = $post->ID;
        $cb_attachment_thumb = wp_get_attachment_image_src( $cb_post_id, array(750, 400) ); 
        $cb_attachment_full = wp_get_attachment_image_src( $cb_post_id, '' ); 

        if (have_posts()) : while (have_posts()) : the_post(); 
?>
				<div id="cb-content" class="wrap clearfix">
				    				    
					<div id="main" class="clearfix" role="main">
					    
							<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
                                  
                                 <?php 
                                        echo '<div class="cb-entry-header cb-style-off"><h1 class="cb-entry-title cb-single-title">'. get_the_title().'</h1>';
                                        echo cb_byline(false);
                                        echo '</div>'; 
                                ?>

								<section class="entry-content clearfix" itemprop="articleBody">
									<a href="<?php echo $cb_attachment_full[0]; ?>">
									   <img src="<?php echo $cb_attachment_thumb[0]; ?>" alt="">
									</a>
								</section> <!-- end article section -->

								<footer class="article-footer">
								    
									<?php  if ($cb_social_sharing != 'off') { echo cb_social_sharing($post, 'beside'); } ?>

								</footer> <!-- end article footer -->

								<?php if ($cb_comments_onoff == 'cb_comments_on') { comments_template(); } ?>

							</article> <!-- end article -->

						<?php endwhile; ?>

						<?php endif; ?>

					</div> <!-- end #main -->

					<?php get_sidebar(); ?>

			</div> <!-- end #cb-content -->

<?php get_footer(); ?>