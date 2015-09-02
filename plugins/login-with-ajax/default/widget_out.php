<?php /* This is the page users will see logged out. */

        $cb_nav_style = ot_get_option('cb_menu_style', 'cb_dark');

        if ( $cb_nav_style == 'cb_light' ) {
            $cb_menu_color = 'cb-light-menu';
        } else {
             $cb_menu_color = 'cb-dark-menu';
        }
?>

<div class="cb-login-modal clearfix <?php echo $cb_menu_color; ?>">
    <div class="lwa lwa-default clearfix">
        <form class="lwa-form clearfix" action="<?php echo esc_attr(LoginWithAjax::$url_login); ?>" method="post">

            <div class="cb-header">
                <div class="cb-title"><?php esc_html_e('Log In', 'login-with-ajax'); ?></div>
                <div class="cb-close"><span class="cb-close-modal"><i class="fa fa-times"></i></span></div>
            </div>

            <div class="cb-form-body">
                <div class="cb-username"><i class="fa fa-user"></i><input type="text" name="log" placeholder="<?php esc_html_e( 'Username', 'login-with-ajax' ); ?>"></div>
                <div class="cb-password"><i class="fa fa-lock"></i><input type="password" name="pwd" placeholder="<?php esc_html_e( 'Password', 'login-with-ajax' ) ?>"></div>
                <?php do_action('login_form'); ?>

                <div class="cb-submit">
                      <input type="submit" name="wp-submit" class="lwa_wp-submit" value="<?php esc_html_e('Log In', 'login-with-ajax'); ?>" tabindex="100" />
                      <input type="hidden" name="lwa_profile_link" value="<?php echo esc_attr($lwa_data['profile_link']); ?>" />
                      <input type="hidden" name="login-with-ajax" value="login" />
                </div>
                <span class="lwa-status"></span>
                <div class="cb-lost-password">
                     <?php if( !empty($lwa_data['remember']) ): ?>
                     <a class="lwa-links-remember" href="<?php echo esc_attr(LoginWithAjax::$url_remember); ?>" title="<?php esc_html_e('Lost your password?','login-with-ajax') ?>"><?php esc_html_e('Lost your password?','login-with-ajax') ?></a>
                     <?php endif; ?>
                </div>
                <?php if ( get_option('users_can_register') && !empty($lwa_data['registration']) ) : ?>
                    <div class="cb-register">
                        <a href="#" class="cb-login-modal-closer" data-reveal-id="cb-register-modal"><?php esc_html_e('Register','login-with-ajax') ?></a>
                    </div>
                <?php endif; ?>
           </div>
        </form>

        <?php if( !empty($lwa_data['remember']) ): ?>
        <form class="lwa-remember clearfix" action="<?php echo esc_attr(LoginWithAjax::$url_remember) ?>" method="post" style="display:none;">
            <div class="cb-header">
                        <div class="cb-title"><?php esc_html_e('Lost your password?', 'login-with-ajax'); ?></div>
                        <div class="cb-close"><span class="cb-close-modal lwa-links-remember-cancel"><i class="fa fa-times"></i></span></div>
            </div>
            <div class="cb-form-body">
                <div class="cb-email">
                        <?php $msg = __("Enter username or email", 'login-with-ajax'); ?>
                        <i class="fa fa-envelope-o"></i><input type="text" name="user_login" class="lwa-user-remember" placeholder="<?php esc_html_e( 'Enter username or email', 'login-with-ajax' ) ?>">
                         <?php do_action('lostpassword_form'); ?>
                 </div>
                 <div class="cb-remember-buttons">
                        <input type="submit" value="<?php esc_html_e("Get New Password", 'login-with-ajax'); ?>" class="lwa-button-remember" />
                        <input type="hidden" name="login-with-ajax" value="remember" />
                 </div>
                 <span class="lwa-status"></span>
            </div>

        </form>
        <?php endif; ?>
    </div>
</div>
<?php if( get_option('users_can_register') && !empty($lwa_data['registration']) ): ?>
	<div class="cb-register-modal <?php echo $cb_menu_color; ?> clearfix">
    		<div class="cb-join-modal lwa-register lwa-register-default <?php echo $cb_menu_color; ?>">
    		    <form class="lwa-register-form clearfix" action="<?php echo esc_attr(LoginWithAjax::$url_register); ?>" method="post">
        		    <div class="cb-header">
                        <div class="cb-title"><?php esc_html_e('Register','login-with-ajax'); ?></div>
                        <div class="cb-close"><span class="cb-close-modal"><i class="fa fa-times"></i></span></div>
                    </div>
                    <div class="cb-form-body">

                        <div class="cb-username"><i class="fa fa-user"></i><input type="text" name="user_login" class="user_login input" placeholder="<?php esc_html_e( 'Username', 'login-with-ajax' ); ?>"></div>
                        <div class="cb-email"><i class="fa fa-envelope-o"></i><input type="text" name="user_email" class="user_email input" placeholder="<?php esc_html_e( 'E-mail', 'login-with-ajax' ); ?>"></div>
            			<?php do_action('register_form'); ?>
            			<?php do_action('lwa_register_form'); ?>

            			<div class="cb-submit">
                              <input type="submit" name="wp-submit" class="wp-submitbutton-primary" value="<?php esc_html_e('Register', 'login-with-ajax'); ?>" tabindex="100" />
                              <input type="hidden" name="login-with-ajax" value="register" />
                        </div>

            			<div class="cb-register-tip"><?php esc_html_e('A password will be e-mailed to you.','login-with-ajax'); ?></div>

            			<span class="lwa-status"></span>
            		</div>
        		</form>
    		</div>
	</div>
<?php endif; ?>