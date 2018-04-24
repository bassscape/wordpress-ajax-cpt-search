<?php
	//-----------------------------------------------------------------------------------//
	// [1] Include/Require files
	//-----------------------------------------------------------------------------------//
	$path = dirname(__FILE__) . '/includes/pp_ajax_search.php';
	if (realpath($path)) { include_once($path); $path = NULL; }
	
	
	//-----------------------------------------------------
	// [2] Enqueue scripts and add localized parameters
	//-----------------------------------------------------
	add_action( 'wp_enqueue_scripts', 'pp_custom_scripts_enqueue' );
	function pp_custom_scripts_enqueue() {
	
		$theme = wp_get_theme(); // Get the current theme version numbers for bumping scripts to load
	
		// Make sure jQuery is being enqueued, otherwise you will need to do this.
	
		// Register custom scripts
		wp_register_script( 'pp_ajax_search', get_stylesheet_directory_uri() . '/scripts/pp_ajax_search.js', array( 'jquery' ), $theme['Version'], true); // Register script with depenancies and version in the footer
	
		// Enqueue scripts
		wp_enqueue_script('pp_ajax_search');
		
		// Use wp_localize_script to output some variables in the html of each WordPress page
		// These variables/parameters are accessible to the load_more.js file we enqueued above
		$coursesearch_page = get_query_var('coursesearch', '');
		if(!isset($coursesearch_page)) { $coursesearch_page = '1'; }
		$localize_var_args = array(
			'page_id' => get_the_ID(),
			'coursesearch_page' => $coursesearch_page,
			'ajaxurl' => admin_url( 'admin-ajax.php' )
	
		);
		wp_localize_script( 'pp_ajax_search', 'pp_js_params', $localize_var_args );
	
	}
	
	
	//-----------------------------------------------------
	// [3] Enqueue styles
	//-----------------------------------------------------
	add_action('wp', 'custom_childtheme_css_enqueue');
	function custom_childtheme_css_enqueue() {
	 	if ( !is_admin() ) { // Only enqueue these styles if is not the wordpress admin
	
			$theme = wp_get_theme(); // Get the current theme version numbers for bumping scripts to load
	
			// Register styles
			wp_register_style( 'css_for_ajax_search', get_stylesheet_directory_uri() . '/css/pp_ajax_search.css', array(), $theme['Version'], 'all' );
	
			// Enqueue styles
			wp_enqueue_style( 'css_for_ajax_search' );
		}
	}

	
	//-----------------------------------------------------
	// [4] Make sure template-ajax_search.php is a page template
	//-----------------------------------------------------
	
	
?>