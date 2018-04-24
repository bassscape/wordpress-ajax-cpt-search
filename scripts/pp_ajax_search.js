jQuery(function($) {

	//-----------------------------------------------------------------------------------//
	// Genre Ajax Filtering
	//-----------------------------------------------------------------------------------//=

	//
	// Load posts on page load
	//
	genre_get_posts();

	//
	// Hide some search components on load
	//
	$("#search_text_input").hide();
	$('#courses_loading').hide();

	//
	// Reset the course search form with a link click
	//
	$('#course_search_reset').on('click', function(e){
		e.preventDefault();
		$(this).closest('form').find("input[type=text]").val("");
		$(this).closest('form').find('select').prop('selectedIndex',0);
	});

	$('select.filter_courses').change(function() {

	});
	$("select#course-sector").change(function() {
		genre_get_posts(1);
	});
	$("select#broad_areas").change(function() {
		genre_get_posts(1);
	});
	$("select#course_levels").change(function() {
		genre_get_posts(1);
	});
	//
	// Get values from selected dropdowns
	//
	// course-sector
	function getSelectedFiltersSectors() {
		var filters = [];
		var filters = $('select#course-sector').val();
		//console.log('Selected course-sector filters: ', filters);
		return filters;
	}
	// broad_areas
	function getSelectedFiltersAreas() {
		var filters = [];
		var filters = $('select#broad_areas').val();
		//console.log('Selected broad_areas filters : ', filters);
		return filters;
	}
	// course_levels
	function getSelectedFiltersLevels() {
		var filters = [];
		var filters = $('select#course_levels').val();
		//console.log('Selected course_levels filters : ', filters);
		return filters;
	}

	//
	//
	//
	//user is "finished typing," do something
	function doneTyping () {
		genre_get_posts();
	}
	//setup before functions
	var typingTimer;                //timer identifier
	var doneTypingInterval = 2000;  //time in ms (5 seconds)
	//on keyup, start the countdown
	$('input.text-search').keyup(function(){
	clearTimeout(typingTimer);
		if ($('input.text-search').val()) {
			typingTimer = setTimeout(doneTyping, doneTypingInterval);
		}
	});
	$("input.text-search").blur(function(){
		doneTyping();
	});
	//
	// Fire ajax request when typing in search
	//
	// Search when the search button is clicked
	$('#submit-search').on('click', function(e){
		e.preventDefault();
		genre_get_posts(); //Load Posts
	});
	// Get Search Form Values
	function getSearchValue() {
		var searchValue = $('input.text-search').val();
		return searchValue;
	}

	//
	// Main ajax function
	//
	function genre_get_posts(paging) {
		var reset = paging;
		var ajax_url = ajax_genre_params.ajax_url;
		var page_id = ajax_genre_params.page_id;
		var coursesearch_page = ajax_genre_params.coursesearch_page;

		$.ajax({
			type: 'GET',
			url: ajax_url,
			data: {
				action: 'genre_filter',
				reset: reset,
				page_id: page_id,
				coursesearch_page: coursesearch_page,
				filtersSector: getSelectedFiltersSectors,
				filtersArea: getSelectedFiltersAreas,
				filtersLevel: getSelectedFiltersLevels,
				search: getSearchValue()
			},
			beforeSend: function () {
				$('#search_results').hide();
				$('.no_results').hide();
				$('#courses_loading').show();

			},
			success: function(data) {
				$('#courses_loading').hide();
				$('#search_results').show();
				$('#search_results').html(data);
			},
			error: function() {
				$('#courses_loading').hide();
				$('#search_results').show();
				$("#search_results").html('<p>There has been an error</p>');
			}
		});
	}

});