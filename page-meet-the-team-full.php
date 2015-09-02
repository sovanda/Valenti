<?php /* Template Name: Meet The Team (No Sidebar) */

        get_header(); 
        $cb_page_id = get_the_ID();
        $cb_theme_style = ot_get_option('cb_theme_style', 'cb_boxed');
        $cb_page_base_color = get_post_meta($cb_page_id , 'cb_overall_color_post', true );
        if ( ( $cb_page_base_color == '#' ) || ( $cb_page_base_color == NULL ) ) {
            $cb_page_base_color = ot_get_option('cb_base_color', '#eb9812'); 
        }      
?>
    <div class="cb-cat-header<?php if ($cb_theme_style == 'cb_boxed') echo ' wrap'; ?>" style="border-bottom-color:<?php echo $cb_page_base_color;?>;">
            <h1 id="cb-cat-title"><?php echo the_title(); ?></h1>
    </div>
    
	<div id="cb-content" class="wrap clearfix">
    
	    <div id="main" class="entry-content cb-about-page cb-full-width wrap clearfix" role="main">
                
<?php 				
		while (have_posts()) : the_post(); the_content(); endwhile; 

		echo cb_author_list(true); 
?>
	    </div> <!-- end #main -->
	    
	</div> <!-- end #cb-inner-content -->
    
            
<?php get_footer(); ?>
