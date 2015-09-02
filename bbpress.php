<?php
        get_header();
        $cb_user_page = NULL;
        global $bb_current_user;
        $cb_breadcrumbs = ot_get_option('cb_breadcrumbs', 'on');
        $cb_theme_style = ot_get_option('cb_theme_style', 'cb_boxed');
        $cb_bbpress_sidebar = ot_get_option('cb_bbpress_sidebar', 'sidebar');
        $cb_bbpress_global_color = ot_get_option('cb_bbpress_global_color', '#eb9812');
        $cb_current_user = bbp_get_user_id();

        $cb_forum_id =  bbp_get_forum_id();
        $cb_title = get_the_title();

        if ( ( bbp_is_single_user() == true ) || bbp_is_favorites() == true ) {
        	$cb_title = '<h1 id="cb-cat-title"><span>'. __( 'Member', 'cubell' ) . ' <i class="fa fa-long-arrow-right"></i></span> '. get_the_title() .'</h1>';
        	$cb_user_page = 'cb-author-page ';
        }

        $cb_sidebar_position = NULL;
        if ( $cb_bbpress_sidebar == 'sidebar_left' ) {
            $cb_sidebar_position = ' cb-sidebar-left ';
        } elseif ( $cb_bbpress_sidebar == 'nosidebar' ) {
             $cb_sidebar_position = ' cb-full-width';
        }

?>
				<div id="cb-content" class="<?php if ( $cb_user_page != NULL ) { echo $cb_user_page; } ?>wrap clearfix">

				<div class="cb-cat-header<?php if ( $cb_theme_style == 'cb_boxed' ) { echo ' wrap'; } ?>" style="border-bottom-color:<?php echo $cb_bbpress_global_color; ?>;">

                    <h1 id="cb-cat-title"><?php echo $cb_title; ?></h1>
                    <p><?php if ( ( $cb_forum_id != 0 ) &&  ( bbp_is_single_topic() == false ) && ( bbp_is_single_reply() == false )  ) { bbp_forum_content(); } ?></p>

                </div>

                <?php if ( $cb_user_page != NULL ) {  echo cb_bbp_author_details($cb_current_user); } ?>

                <?php if ( ( $cb_forum_id != 0 ) && ( $cb_breadcrumbs == 'on' ) ) { bbp_breadcrumb(); } ?>

						<div id="main" class="clearfix<?php  echo $cb_sidebar_position; ?>" role="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

								<section class="entry-content clearfix" itemprop="articleBody">
									<?php the_content(); ?>
						     	</section> <!-- end article section -->

							</article> <!-- end article -->

							<?php endwhile; endif; ?>

						</div> <!-- end #main -->

                <?php if ( ( $cb_user_page == NULL ) && ( $cb_bbpress_sidebar != 'nosidebar' ) ) {  get_sidebar(); } ?>

				</div> <!-- end #cb-content -->

<?php get_footer(); ?>