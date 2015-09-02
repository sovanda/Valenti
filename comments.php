<?php /* Comments page */
    $cb_commenter = wp_get_current_commenter();
    $cb_require_name_email = get_option( 'require_name_email' );
    $cb_require_aria = ( $cb_require_name_email ? " aria-required='true'" : '' );

  if ( !empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
    die ('Please do not load this page directly. Thanks!');

  if ( post_password_required() ) { ?>
    <div class="alert help">
        <p class="nocomments"><?php _e( "This post is password protected. Enter the password to view comments.", "cubell" ); ?></p>
    </div>
  <?php
    return;
  }
?>

<?php if ( have_comments() ) : ?>

    <h3 id="comments" class="cb-block-title"><?php comments_number(__( 'No Responses', 'cubell' ), __( 'One Response', 'cubell' ), _n( '% Response', '% Responses', get_comments_number(),'cubell') );?></h3>

    <nav id="comment-nav">
        <ul class="clearfix">
            <li><?php previous_comments_link() ?></li>
            <li><?php next_comments_link() ?></li>
        </ul>
    </nav>

    <ol class="commentlist">
        <?php wp_list_comments('type=all&callback=cb_comments&max_depth=3'); ?>
    </ol>

    <nav id="comment-nav">
        <ul class="clearfix">
            <li><?php previous_comments_link() ?></li>
            <li><?php next_comments_link() ?></li>
        </ul>
    </nav>

<?php endif; ?>

<?php if ( comments_open() ) : ?>


<?php comment_form(
                    array(
                                'title_reply' => __( 'Leave a Reply', 'cubell'),
                                'title_reply_to' =>  __( 'Leave a Reply to %s', 'cubell' ),
                                'cancel_reply_link' => __( 'Cancel Reply', 'cubell' ),
                                'label_submit' => __( 'Submit', 'cubell' ),
                                'comment_notes_after' => '',
                                'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.', 'cubell') .'</p>',
                                'comment_field' =>  '<p class="comment-form-comment"><label for="comment">' . __( 'Comment', 'cubell' ) .
                                '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">' .
                                '</textarea></p>',

                                'must_log_in' => '<p class="must-log-in">' .
                                                    sprintf(
                                                      __( 'You must be <a href="%s" data-reveal-id="cb-login-modal">logged in</a> to post a comment.', 'cubell' ),
                                                      wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
                                                    ) . '</p>',

                                 'logged_in_as' => '<p class="logged-in-as">' .
                                                    sprintf(
                                                    __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'cubell' ),
                                                      admin_url( 'profile.php' ),
                                                      $user_identity,
                                                      wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
                                                    ) . '</p>',

                                  'fields' => apply_filters( 'comment_form_default_fields', array(

                                            'author' =>
                                              '<p class="comment-form-author">' .
                                              '<label for="author">' . __( 'Name', 'cubell' ) .
                                              ( $cb_require_name_email ? '<span class="required">*</span>' : '' ) . '</label> ' .
                                              '<input id="author" name="author" type="text" value="' . esc_attr( $cb_commenter['comment_author'] ) .
                                              '" size="30"' . $cb_require_aria . ' /></p>',

                                            'email' =>
                                              '<p class="comment-form-email"><label for="email">' . __( 'Email', 'cubell' ) .
                                              ( $cb_require_name_email ? '<span class="required">*</span>' : '' ) . '</label> ' .
                                              '<input id="email" name="email" type="text" value="' . esc_attr(  $cb_commenter['comment_author_email'] ) .
                                              '" size="30"' . $cb_require_aria . ' /></p>',

                                            'url' =>
                                              '<p class="comment-form-url"><label for="url">' .
                                              __( 'Website', 'cubell' ) . '</label>' .
                                              '<input id="url" name="url" type="text" value="' . esc_attr( $cb_commenter['comment_author_url'] ) .
                                              '" size="30" /></p>'
                                        )
                            ),
                )
    );

endif;

?>