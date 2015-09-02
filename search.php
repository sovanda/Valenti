<?php 
    
    get_header(); 
    $cb_global_color = ot_get_option('cb_base_color', '#eb9812'); 
    $cb_theme_style = ot_get_option('cb_theme_style', 'cb_boxed');
    $cb_blog_style = cb_get_blog_style();
?>

<div class="cb-cat-header<?php if ($cb_theme_style == 'cb_boxed') echo ' wrap'; ?>" style="border-bottom-color:<?php echo $cb_global_color;?>;">
    <h1 id="cb-search-title"><?php _e('Search Results for:', 'cubell'); ?> <span style="color:<?php echo $cb_global_color; ?>">"<?php echo esc_attr(get_search_query()); ?>"</span></h1>
</div>
            
<div id="cb-content" class="cb-search-page wrap clearfix">
    

    <div id="main" class="<?php if ( $cb_blog_style == 'style-c' ) { echo 'cb-full-width '; } ?>clearfix" role="main">
       
    <?php if ( have_posts() ) { 
            get_template_part('cat', $cb_blog_style);
        } else { ?>
    
            <article id="post-not-found" class="hentry clearfix">
                    <h2><?php _e('Sorry, nothing found.', 'cubell'); ?></h2>
                
                <section class="entry-content">
                    <p><?php _e('Please try searching again, but with different keywords.', 'cubell'); ?></p>
                </section>
                <footer class="article-footer">
                    <p><?php get_search_form(); ?></p>
                </footer>
            </article>
    
        <?php } ?>
        
    </div> <!-- end #main -->

    <?php if ( $cb_blog_style != 'style-c' ) { get_sidebar(); } ?>
    
</div> <!-- end #cb-inner-content -->
                
<?php get_footer(); ?>