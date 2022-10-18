<?php
if (! wp_next_scheduled ( 'rankie_daily_event' )) {
	wp_schedule_event ( time (), 'hourly', 'rankie_daily_event' );
}

add_action ( 'rankie_daily_event', 'rankie_do_this_daily' );

function rankie_do_this_daily() {
	
	// do something every day
	if (isset ( $_POST ['mail'] )) {
		$wp_rankie_mail = $_POST ['mail'];
	} else {
		$wp_rankie_mail = get_option ( 'wp_rankie_mail', '' );
	}
	
	// check if email is set or not
	if (trim ( $wp_rankie_mail ) == '')
		return;
	
	// today
	$today = time ();
	$currentDay = date ( 'd' );
	
	// get yesterday date
	$yesterday = time () - 24 * 60 * 60;
	$yesterdayDay = date ( 'd', $yesterday );
	$yesterdayMonth = date ( 'm', $yesterday );
	
	// last sent
	$dayLast = get_option ( 'wp_rankie_last_mail', $yesterdayDay );
	
	if (trim ( $currentDay ) != trim ( $dayLast ) || isset ( $_POST ['mail'] )) {
		
		// woo we re on a different day let's send report
		
		// update last sent date
		if (! isset ( $_POST ['mail'] ))
			update_option ( 'wp_rankie_last_mail', $currentDay );
		
		global $wpdb;
		$query = "SELECT distinct( {$wpdb->prefix}rankie_changes.keyword_id ) as single_keyword , keyword , keyword_rank , sum(rank_change) as total_rank_change  , keyword_site , keyword_group  FROM `{$wpdb->prefix}rankie_changes` ,`{$wpdb->prefix}rankie_keywords`   where {$wpdb->prefix}rankie_changes.keyword_id =  {$wpdb->prefix}rankie_keywords.keyword_id  and   year(date) = year(CURDATE())  and month(date) = '$yesterdayMonth' and day(date) = '$yesterdayDay'  group by single_keyword order by total_rank_change DESC";
		
		$rows = $wpdb->get_results ( $query );
		
		// getting moving forward & backward keywords
		$moving_forware_html = '<br><h3>صعودي </h3><table><thead><tr><th>کلمه کليدي</th><th>رتبه فعلي</th><th>تغيير رتبه</th><th>اسم دامنه</th><th>گروه</th> </tr></thead>';
		$moving_backward_html = '<br><h3>نزولي </h3><table><thead><tr><th>کلمه کليدي</th><th>رتبه فعلي</th><th>تغيير رتبه</th><th>اسم دامنه</th><th>گروه</th> </tr></thead>';
		
		$printedKeys = array ();
		$positiveSum = 0;
		$negativeSum = 0;
		
		foreach ( $rows as $row ) {
			
			$printedKeys [] = $row->single_keyword;
			
			if ($row->total_rank_change > 0) {
				$tr = '<tr><td>' . $row->keyword . '</td><td>' . $row->keyword_rank . '</td><td>+' . $row->total_rank_change . '</td><td>' . $row->keyword_site . '</td><td>' . $row->keyword_group . '</td></tr>';
				$moving_forware_html .= $tr;
				$positiveSum = $positiveSum + $row->total_rank_change;
			} elseif ($row->total_rank_change < 0) {
				$tr = '<tr><td>' . $row->keyword . '</td><td>' . $row->keyword_rank . '</td><td>' . $row->total_rank_change . '</td><td>' . $row->keyword_site . '</td><td>' . $row->keyword_group . '</td></tr>';
				$moving_backward_html .= $tr;
				$negativeSum = $negativeSum + $row->total_rank_change;
			}
		}
		
		if ($positiveSum == 0)
			$moving_forware_html .= '<tr><td colspan="5" >No keywords moved up </td></tr>';
		if ($negativeSum == 0)
			$moving_backward_html .= '<tr><td colspan="5" >No keywords moved down </td></tr>';
		
		$moving_forware_html .= '</table>';
		$moving_backward_html .= '</table>';
		$head="<h1>گزارش هاي رنکي در  $yesterdayDay - $yesterdayMonth </h1> <p>در زير کلمات کليدي با تغيير رتبه در 24 ساعت گذشته ثبت شده توسط wordpress rankie براي وردپرس قرار دارند</p>";
		
		$emailTitle = "Rankie Report on $yesterdayDay - $yesterdayMonth";
		$emailBody = $head . $moving_forware_html . $moving_backward_html;
		
		add_filter ( 'wp_mail_content_type', 'rankie_set_content_type' );
		function rankie_set_content_type($content_type) {
			return 'text/html';
		}
		
		wp_mail ( $wp_rankie_mail, $emailTitle, $emailBody );
	}
}

?>