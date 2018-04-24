<?php

 	$output_course = '';
	$output_course .= '<tr class="item '.$broad_field_class.' '.$course_level_class.'">';
		$output_course .= '<td class="course">';
			$output_course .= '<a title="'.get_the_title().'" href="'.get_permalink().'">';
				$output_course .= '<span class="course">'.get_the_title().'</span>';
			$output_course .= '</a>';
		$output_course .= '</td>';
		$output_course .= '<td class="duration">';
			if ( get_field('duration_in_weeks') ) { $output_course .= '<p>Duration: '.get_field('duration_in_weeks').' weeks</p>'; }
		$output_course .= '</td>';
	$output_course .= '</tr>';
	
	echo $output_course;

?>
