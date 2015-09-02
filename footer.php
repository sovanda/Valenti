 <?php
		 $cb_footer_copyright = ot_get_option('cb_footer_copyright', false);
         $cb_theme_style = ot_get_option('cb_theme_style', 'cb_boxed');
         $cb_footer_layout = ot_get_option('cb_footer_layout', 'cb-footer-a');
?>
    			<footer id="cb-footer" class="<?php if ( $cb_theme_style == 'cb_boxed' ) echo ' wrap'; ?>" role="contentinfo">

    				<div id="cb-widgets" class="<?php echo $cb_footer_layout; ?> wrap clearfix<?php if ( $cb_theme_style != 'cb_boxed' ) echo ' cb-fw'; ?>">

                        <?php if ( is_active_sidebar( 'footer-1' ) ) { ?>
                            <div class="cb-one cb-column clearfix">
                                <?php dynamic_sidebar('footer-1'); ?>
                            </div>
                        <?php } ?>
                        <?php if ( is_active_sidebar( 'footer-2' ) ) { ?>
                            <div class="cb-two cb-column clearfix">
                                <?php dynamic_sidebar('footer-2'); ?>
                            </div>
                        <?php } ?>
                        <?php if ( is_active_sidebar( 'footer-3' ) ) { ?>
                            <div class="cb-three cb-column clearfix">
                                <?php dynamic_sidebar('footer-3'); ?>
                            </div>
                        <?php } ?>
                        <?php if (( is_active_sidebar( 'footer-4' ) ) && ( $cb_footer_layout == 'cb-footer-b' ) ) { ?>
                            <div class="cb-four cb-column clearfix">
                                <?php dynamic_sidebar('footer-4'); ?>
                            </div>
                        <?php } ?>

                    </div>

                    <?php if ( ( $cb_footer_copyright != NULL ) || ( has_nav_menu( 'footer' ) ) ) { ?>

                        <div class="cb-footer-lower clearfix">

                            <div class="wrap clearfix">

                                <div class="cb-copyright"><?php echo $cb_footer_copyright; ?></div>

        						<?php if ( has_nav_menu( 'footer' ) ) { footer_nav(); } ?>

           					</div>

        				</div>
    				<?php } ?>

    			</footer> <!-- end footer -->

    		</div> <!-- end #cb-container -->

		</div> <!-- end #cb-outer-container -->

        <span id="cb-overlay"></span>

		<?php wp_footer(); ?>

	</body>

</html> <!-- The End. what a ride! -->