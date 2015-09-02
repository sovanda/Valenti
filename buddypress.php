<?php
        get_header();
        $cb_user_page = $cb_group_creator = $cb_title_prefix = $cb_bp_class = $cb_breadcrumbs_override = NULL;
        global $bb_current_user;
        $cb_breadcrumbs = ot_get_option('cb_breadcrumbs', 'on');
        $cb_theme_style = ot_get_option('cb_theme_style', 'cb_boxed');
        $cb_buddypress_sidebar = ot_get_option('cb_buddypress_sidebar', 'sidebar');
        $cb_buddypress_global_color = ot_get_option('cb_buddypress_global_color', '#eb9812');
        $cb_current_user = bp_displayed_user_id();
        $cb_title = get_the_title();
        $cb_bp_current_component = bp_current_component();
        $cb_bp_current_action = bp_current_action();

        if ( ( ( $cb_bp_current_component == 'activity' ) || ( $cb_bp_current_component == 'profile' ) ) && ( bp_is_directory() == false ) && ( $cb_bp_current_action != NULL ) ) {
            $cb_title_prefix = '<span>'. __( 'Member', 'buddypress' ) . ' <i class="fa fa-long-arrow-right"></i></span> ';
        }

        if ( ( $cb_bp_current_component == 'groups' )   && ( $cb_bp_current_action != NULL ) ) {
            $cb_title_prefix = '<span>'. __( 'Group', 'buddypress' ) . ' <i class="fa fa-long-arrow-right"></i></span> ';
        }

        if ( ( $cb_bp_current_component == 'groups' )   && ( ( $cb_bp_current_action == 'my-groups' ) || ( $cb_bp_current_action == 'invites' ) ) ) {
            $cb_title_prefix = NULL;
            $cb_title = __( 'Groups', 'buddypress' );
        }

        if ( ( $cb_bp_current_component == 'settings' ) ) {
            $cb_title_prefix = '<span>'. __( 'Settings', 'buddypress' ) . ' <i class="fa fa-long-arrow-right"></i></span> ';
        }

        if ( ( $cb_bp_current_component == 'forums' ) ) {
            $cb_title_prefix = '<span>'. __( 'Forums', 'buddypress' ) . ' <i class="fa fa-long-arrow-right"></i></span> ';
        }

        if ( ( $cb_bp_current_component == 'activity' ) && ( $cb_bp_current_action == NULL ) ) {
            $cb_title = __( 'Activity', 'buddypress' );
            $cb_breadcrumbs_override = true;
        }

        if ( ( $cb_bp_current_component == 'groups' ) && ( $cb_bp_current_action == NULL ) ) {
            $cb_title = '<span>'. __( 'Groups', 'buddypress' ) . ' ' . '<a class="cb-group-create cb-tip-bot" title="' . __( 'Create a Group', 'buddypress' ) . '" href="' . trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ) . '"><i class="fa fa-plus"></i></a>';
            $cb_group_creator = NULL;
        }

        if ( ( $cb_bp_current_component == 'groups' ) && ( $cb_bp_current_action == 'create' ) ) {
            $cb_title_prefix = '<span>'. __( 'Groups', 'buddypress' ) . ' <i class="fa fa-long-arrow-right"></i></span> ';
            $cb_title =  __( 'Create a Group', 'buddypress' );
        }

        if ( (string)(int) $cb_bp_current_action == $cb_bp_current_action && ( $cb_bp_current_component == 'activity' ) ) {
            $cb_bp_class = 'cb-activity-stream ';
            $cb_title_prefix = NULL;
            $cb_title = __( 'Activity', 'buddypress' );
        }

       	$cb_title = '<h1 id="cb-cat-title">' . $cb_title_prefix . $cb_title . '</h1>';
       	$cb_user_page = 'cb-author-page ';
        $cb_sidebar_position = NULL;

        if ( $cb_buddypress_sidebar == 'sidebar_left' ) {

            $cb_sidebar_position = ' cb-sidebar-left ';

        } elseif ( $cb_buddypress_sidebar == 'nosidebar' ) {

             $cb_sidebar_position = ' cb-full-width';

        }

?>
				<div id="cb-content" class="<?php echo $cb_bp_class; ?>wrap clearfix">

				<div class="cb-cat-header<?php if ( $cb_theme_style == 'cb_boxed' ) { echo ' wrap'; } ?>" style="border-bottom-color:<?php echo $cb_buddypress_global_color; ?>;">

                <?php
                        echo $cb_title;

                        if ( $cb_group_creator != NULL ) {
                            echo  $cb_group_creator;
                        }
                ?>

                </div>

                <?php if ( ( $cb_breadcrumbs == 'on' ) && ( $cb_breadcrumbs_override == NULL ) ) { echo cb_breadcrumbs(); } ?>

						<div id="main" class="clearfix<?php  echo $cb_sidebar_position; ?>" role="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

								<section class="entry-content clearfix" itemprop="articleBody">
									<?php the_content(); ?>
						     	</section> <!-- end article section -->

                                <?php comments_template(); ?>

							</article> <!-- end article -->

							<?php endwhile; endif; ?>

						</div> <!-- end #main -->

                <?php if ( ( $cb_buddypress_sidebar != 'nosidebar' ) ) { get_sidebar(); } ?>

				</div> <!-- end #cb-content -->

<?php get_footer(); ?>