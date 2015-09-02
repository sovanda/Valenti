<?php 
    get_header(); 
    $cb_theme_style = ot_get_option('cb_theme_style', 'cb_boxed');
?>
	<div class="cb-404-header<?php if ($cb_theme_style == 'cb_boxed') echo ' wrap'; ?>">
           <h1 id="cb-cat-title"><?php _e("Page not found", "cubell"); ?></h1>
    </div>
    
	<div id="cb-content" class="wrap clearfix">
	    
         <div id="main" class="cb-full-width clearfix" role="main">
	
			<article id="post-not-found" class="hentry clearfix">
	      	              
				<section class="entry-content">			
					
	                <h2><?php _e("Oops! The page you were looking for was not found.", "cubell"); ?></h2>
                    <img src="<?php echo get_template_directory_uri();?>/library/images/cb-404.png" data-at2x="<?php echo get_template_directory_uri();?>/library/images/cb-404@2x.png" class="cloud-404" alt="<?php bloginfo('name');?> 404"/>
		
				</section> <!-- end article section -->
	
				<section class="search">
	
				    <p><?php get_search_form(); ?></p>
	
				</section> <!-- end search section -->
					
			</article> <!-- end article -->
	
		</div> <!-- end #main -->
		
	</div> <!-- end #cb-content -->
    
<?php get_footer(); ?>
