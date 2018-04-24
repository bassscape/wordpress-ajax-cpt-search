<?php
	//-----------------------------------------------------------------------------------//
	// Get Genre Filters
	//-----------------------------------------------------------------------------------//
	function get_genre_filters() {
	
		$filters_html = '';
	
		$query_data = $_GET;
		//$filters_html .= '<pre>' . var_export($query_data, true) . '</pre>'; // For visual testing
	
		$request_course_type = array();
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			//$filters_html .= '<pre>' . var_export($_REQUEST, true) . '</pre>'; // For visual testing
	
			$request_course_type = $_REQUEST["filter_courses"];
			$request_course_type = explode(',',$request_course_type);
	
			$searchtxt_value = "";
			if(isset($_REQUEST['searchterm']) && $_REQUEST['searchterm']!='') {
				$searchtxt = $_REQUEST['searchterm'];
				$searchtxt_value = ' value="'.$searchtxt.'"';
			}
		}
	
		$filter_sectors_ids = array();
		$filter_area_ids = array();
		$filter_level_ids = array();
		if ( '' != $query_data['filtersSector'] ) { $filter_sectors_ids = ($query_data['filtersSector']) ? explode(',',$query_data['filtersSector']) : false; }
		if ( '' != $query_data['filtersArea'] ) { $filter_area_ids = ($query_data['filtersArea']) ? explode(',',$query_data['filtersArea']) : false; }
		if ( '' != $query_data['filtersLevel'] ) { $filter_level_ids = ($query_data['filtersLevel']) ? explode(',',$query_data['filtersLevel']) : false; }
	
		$filter_ids = array_merge($filter_sectors_ids, $filter_area_ids, $filter_level_ids, $request_course_type);
		//$filters_html .= '<pre>' . var_export($filter_ids, true) . '</pre>'; // For visual testing
	
		$filters_html .= '<div class="col-xs-12 col-sm-2">';
			$filters_html .= '<input type="text" class="search_label" placeholder="I want to find" disabled/>';
		$filters_html .= '</div>';
	
		$taxonomy = 'course-sector';
		$args = array(
			'orderby'           => 'name',
			'order'             => 'ASC',
			'hide_empty'        => true
		);
		$terms = get_terms($taxonomy, $args);
		if( $terms ):
			$filters_html .= '<div class="col-xs-12 col-sm-4">';
				$filters_html .= '<select id="course-sector" class="filter_courses styled-select" name="filter_courses">';
					$filters_html .= '<option value="">... select a type of course ...</option>';
	
					foreach( $terms as $term ) {
						$term_id = $term->term_id;
						$term_name = $term->name;
						if (in_array($term_id, $filter_ids) ) { $selected = 'selected'; } else { $selected = ''; }
						if ( 'ELICOS' == $term_name ) { $term_name = 'English as a second language'; }
						$filters_html .= '<option value="'.$term_id.'" '.$selected.'>'.$term_name.'</option>';
					}
				$filters_html .= '</select>';
			$filters_html .= '</div>';
		endif;
	
		$filters_html .= '<div class="col-xs-12 col-sm-2">';
			$filters_html .= '<input type="text" class="search_label" placeholder="courses related to" disabled/>';
		$filters_html .= '</div>';
	
		$filters_html .= '<div class="col-xs-12 col-sm-3">';
			$filters_html .= '<input type="text" name="searchterm"'.$searchtxt_value.' class="text-search" placeholder="type a search term" />';
		$filters_html .= '</div>';
	
		$filters_html .= '<div class="col-xs-12 col-sm-1">';
			$search_button_id = '';
			if ( 'study/course-search-dev' == $_SERVER['REQUEST_URI'] ) { $search_button_id = ' id="submit-search"'; }
			$filters_html .= '<input type="submit" value="Go!" '.$search_button_id.'/>';
			$filters_html .= '<p><a href="'.get_the_permalink().'" id="course_search_reset">Reset</a></p>';
		$filters_html .= '</div>';
	
		return $filters_html;
	}
	
	//-----------------------------------------------------------------------------------//
	// Add Ajax Actions
	//-----------------------------------------------------------------------------------//
	add_action('wp_ajax_genre_filter', 'ajax_genre_filter');
	add_action('wp_ajax_nopriv_genre_filter', 'ajax_genre_filter');
	
	//Construct Loop & Results
	function ajax_genre_filter() {
	
		//echo '<p>'.count($meta_query_build).' rows of search parameters.</p>'; // For visual testing
	
		$query_data = $_GET;
		//echo '<pre>' . var_export($query_data, true) . '</pre>'; // For visual testing
	
		$sector_object = get_term( $query_data['filtersSector'], 'course-sector' );
		$args = array( // Add out new query parameters
			'post_type' => 'providers',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'course-sector',
					'field'    => 'name',
					'terms'    => $sector_object->name,
				),
			),
			'orderby' => 'menu_order title',
			'order'   => 'ASC'
		);
	
		$query_posts = NULL;
		$query_posts = new WP_Query( $args );
	
		$posts_count = $query_posts->found_posts;
		//echo '<pre>$query_args: <br/>' . var_export($args, true) . '</pre>'; // For visual testing
		//echo '<pre>Providers Found: <br/>' . var_export($posts_count, true) . '</pre>'; // For visual testing
	
		$output = '';
	
		if ( $query_posts->have_posts() ) :
	
			echo '<div id="accordion">';
	
			$provider_counter = 0;
	
			while ( $query_posts->have_posts() ) : $query_posts->the_post();
	
				$provider_counter++;
	
				if ( get_field('cricos_code') ) {


					$filter_sectors_ids = ($query_data['filtersSector']) ? explode(',',$query_data['filtersSector']) : false;
					//echo '</p>Search filtersSector:</p>';
					//echo '<pre>' . var_export($filter_sectors_ids, true) . '</pre>'; // For visual testing


					$meta_query_build[] = array(
						'key' => 'provider_code',
						'value' => get_field('cricos_code')
					);


					if ( 1 < count($meta_query_build) ) {
						$meta_query_relation = array( 'relation' => 'OR' );
					} else {
						$meta_query_relation = array();
					}

					$meta_query_args = array_merge(
						$meta_query_relation,
						$meta_query_build
					);

					$filter_area_ids = ($query_data['filtersArea']) ? explode(',',$query_data['filtersArea']) : false;

					$tax_areas_query_build = array();
					if ( 'undefined' != $query_data['filtersArea'] ) {
						foreach ($filter_area_ids as $item) {
							$term = get_term( $item, 'broad_areas' );
							$areas[] = $term->name;
							//echo '</p>'.$name = $term->name.'</p>';
							$tax_areas_query_build[] = array(
								'taxonomy' => 'broad_areas',
								'field'    => 'term_id',
								'terms'    => $term->term_id
							);
						}
					}

					if ( 1 < count($tax_areas_query_build) ) {
						$tax_areas_query_relation = array( 'relation' => 'OR' );
					} else {
						$tax_areas_query_relation = array();
					}

					$tax_areas_query_args = array_merge(
						$tax_areas_query_relation,
						$tax_areas_query_build
					);



					$filter_level_ids = ($query_data['filtersLevel']) ? explode(',',$query_data['filtersLevel']) : false;
					//echo '</p>Search filtersLevel:</p>';
					//echo '<pre>' . var_export($filter_level_ids, true) . '</pre>'; // For visual testing

					$tax_levels_query_build = array();
					if ( 'undefined' != $query_data['filtersLevel'] ) {
						foreach ($filter_level_ids as $item) {
							$term = get_term( $item, 'course_levels' );
							$levels[] = $term->name;
							//echo '</p>'.$name = $term->name.'</p>';
							$tax_levels_query_build[] = array(
								'taxonomy' => 'course_levels',
								'field'    => 'term_id',
								'terms'    => $term->term_id
							);
						}
					}

					if ( 1 < count($tax_levels_query_build) ) {
						$tax_levels_query_relation = array( 'relation' => 'OR' );
					} else {
						$tax_levels_query_relation = array();
					}

					$tax_levels_query_args = array_merge(
						$tax_levels_query_relation,
						$tax_levels_query_build
					);



					if ( 1 <= count($tax_areas_query_build) && 1 <= count($tax_levels_query_build) ) {
						$tax_query_relation = array( 'relation' => 'AND' );
					} else {
						$tax_query_relation = array();
					}
					$tax_query_args = array_merge(
						$tax_query_relation,
						$tax_areas_query_build,
						$tax_levels_query_build
					);


					//echo '<pre>$meta_query_args: <br/>' . var_export($meta_query_args, true) . '</pre>'; // For visual testing
					//echo '<pre>$tax_query_args: <br/>' . var_export($tax_query_args, true) . '</pre>'; // For visual testing


					$search_value = ($query_data['search']) ? $query_data['search'] : '';
					//echo '<pre>$search_value: <br/>' . var_export($search_value, true) . '</pre>'; // For visual testing

					if (in_array($term_id, $filter_sectors_ids) ) { $selected = 'selected'; } else { $selected = ''; }
					$page = $query_data['coursesearch_page']; // Next, get the current page
					if ( '' == $page || 1 == $query_data['reset'] ) { $page = 1; }

					$display_count = 200; // First, initialize how many posts to render per page
					$offset = ( $page - 1 ) * $display_count; // After that, calculate the offset

					$book_args_sector = array(
						'post_type' => 'course',
						'meta_query' => $meta_query_args,
						/*
						'meta_query' => array(
							'relation' => 'OR',
							array(
								'key'     => 'course_sector',
								'value'   => 'ELICOS'
							),
							array(
								'key'     => 'course_sector',
								'value'   => 'VET'
							),
						),
						/**/
						//'tax_query' => $tax_query_args,
						/*
						'tax_query' => array(
							'relation' => 'OR',
							array(
								'taxonomy' => 'broad_areas',
								'field'    => 'term_id',
								'terms'    => 211,
							),
						),
						/**/
						'orderby' => 'title',
						'order'   => 'ASC',
						//'page'       =>  $page, // For use with custom loop paging
						//'offset'     =>  $offset, // For use with custom loop paging
						'posts_per_page' => $display_count,
					);
					if ( '' != $search_value ) {
						$book_args_search = array(
							's' => $search_value
						);
						$book_args = array_merge(
							$book_args_sector,
							$book_args_search
						);
						// Relevanssi Query
						$book_loop = new WP_Query();
						$book_loop->query_vars = $book_args;
						relevanssi_do_query($book_loop);
					} else {
						$book_args = $book_args_sector;
						$book_loop = new WP_Query($book_args); // Standard WordPress Query
					}

					$book_count = $book_loop->found_posts;
					//echo '<pre>$query_args: <br/>' . var_export($book_loop->query_vars, true) . '</pre>'; // For visual testing
					//echo '<pre>Course result count: <br/>' . var_export($book_count, true) . '</pre>'; // For visual testing


					if( $book_loop->have_posts() ):

						$provide_name = get_the_title();
						$provide_page_url = get_the_permalink();

						$courses_single_plural = 'courses';
						if ( 1 == $book_count ) { $courses_single_plural = 'course'; }

						echo '<button class="accordion">';
							echo get_the_title();
							//echo ' ['. get_field('cricos_code').']';
							echo ' - '.$book_count.' '.$courses_single_plural;
						echo '</button>';
						echo '<div id="accordion-'.$provider_counter.'" class="panel">';

							echo '<div id="courses">';
								//echo '<p>Filter courses by keywords: <input class="search" placeholder="Search" /></p>';

								echo '<div class="row">';
									$provider_links_class = '12';
									if ( get_field('short_introduction') ) {
										$provider_links_class = '3';
										echo '<div class="col-xs-12 col-sm-9">';
												echo '<p><b>About '.$provide_name.':</b><br/>';
												echo get_field('short_introduction').'</p>';
										echo '</div>';
										echo '<div class="col-xs-12 col-sm-'.$provider_links_class.'">';
											echo '<p><i class="fa fa-info-circle" aria-hidden="true"></i> <a title="'.$provide_name.'" href="'.$provide_page_url.'">Learn more about '.$provide_name.'</a>.</p>';
											echo '<p><i class="fa fa-book" aria-hidden="true"></i> <a title="'.$provide_name.'" href="'.$provide_page_url.'#courses">View all '.$provide_name.' courses</a>.</p>';
										echo '</div>';
										echo '<hr/>';
									}

								echo '</div>';

							if ( $book_count < $display_count ) {
								$results_on_page = $book_count;
							} else {
								$results_on_page = $display_count.' of '.$book_count;
							}
							echo '<p>Displaying '.$results_on_page.' '.$courses_single_plural.' offered by '.$provide_name.' matching your search criteria.</p>';

								echo '<table id="isotope_posts">';
									echo '<tbody class="list">';
										while( $book_loop->have_posts() ): $book_loop->the_post();
											get_template_part( 'includes/content', 'ajax_search' );
										endwhile;
									echo '</tbody>';
								echo '</table>';

							echo '</div>';

							//echo '<hr/>';
							echo '<p>';
								echo '<i class="fa fa-book" aria-hidden="true"></i> <a title="'.$provide_name.'" href="'.$provide_page_url.'#courses">View all '.$provide_name.' courses</a>.';
								echo '<br/>';
								echo '<i class="fa fa-info-circle" aria-hidden="true"></i> <a title="'.$provide_name.'" href="'.$provide_page_url.'">Learn more about '.$provide_name.'</a>.';
							echo '</p>';

						echo '</div><!--end .accordion-section-content-->';

					else:
						//get_template_part('content-none');
					endif;

					unset($meta_query_build);
					$book_loop->reset_postdata();

				} else {
					//echo '<p>cricos code doesn\'t exist</p>';
				}
	
			endwhile;
	
			echo '</div><!--end .accordion-->';
	
		else:
	
			$no_results_html = '';
	
			$no_results_html .= '<div class="row no-gutter no_results">';
				$no_results_html .= '<div class="col-xs-12 col-sm-2">';
					$no_results_html .= '';
				$no_results_html .= '</div>';
	
				$no_results_html .= '<div class="col-xs-12 col-sm-4">';
					$no_results_html .= '<p class="search_warning"><i class="fa fa-hand-o-up fa-3x" aria-hidden="true"></i><br/>';
					$no_results_html .= 'Select a course type.</p>';
				$no_results_html .= '</div>';
	
				$no_results_html .= '<div class="col-xs-12 col-sm-2">';
					$no_results_html .= '';
				$no_results_html .= '</div>';
	
				$no_results_html .= '<div class="col-xs-12 col-sm-3">';
					$no_results_html .= '';
				$no_results_html .= '</div>';
	
				$no_results_html .= '<div class="col-xs-12 col-sm-1">';
					$no_results_html .= '';
				$no_results_html .= '</div>';
			$no_results_html .= '</div>';
	
			echo $no_results_html;
	
		endif;
	
		$query_posts->reset_postdata();
	
		?>
			<script>
				var acc = document.getElementsByClassName("accordion");
				var i;
			
				for (i = 0; i < acc.length; i++) {
				    acc[i].onclick = function(){
				        this.classList.toggle("active");
				        this.nextElementSibling.classList.toggle("show");
				  }
				}
			</script>
		<?php
		die();
	}
?>