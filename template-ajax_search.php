<?php
/**
 * Template Name: Course Search - Dev
 */

 	get_header();

	while ( have_posts() ) : the_post();
	
	?>
	
		<h2><?php echo get_the_title(); ?></h2>
	
	    <div id="search_filters">
	        <form id="course_search" method="post">
				<div class="row no-gutter">
	        		<?php
	            		if( function_exists('get_genre_filters') ) {
	            			echo get_genre_filters();
	            		}
	            	?>
	    		</div>
	        </form>
	    </div>
	
	    <div id="courses_loading">
			<div class="spinner">
				<div class="rect1"></div>
				<div class="rect2"></div>
				<div class="rect3"></div>
				<div class="rect4"></div>
				<div class="rect5"></div>
			</div>
	    </div>
	    
	    <div id="search_results"></div>
	
			
	<?php
	endwhile;
	get_footer();
?>