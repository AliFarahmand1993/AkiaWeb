<?php

//Add custom schedule for once each two minutes 
add_filter ( 'cron_schedules', 'rankie_once_6_minute' );
function rankie_once_6_minute($schedules) {
	
	// Adds once weekly to the existing schedules.
	$schedules ['rankie_once_6_minute'] = array (
			'interval' => 360,
			'display' => __ ( 'هر 6 دقيقه يکبار' ) 
	);
	return $schedules;
}

// Schedule the internal cron function
if (! wp_next_scheduled ( 'rankie_rank_hook' )) {
	
	// check if internal cron enabled or not
	$rankie_options=get_option('wp_rankie_options' , array());
	
	if(  in_array('OPT_EXTERNAL', $rankie_options)){
		return;
	}
	
	// Schedule the function	
	wp_schedule_event ( time (), 'rankie_once_6_minute', 'rankie_rank_hook' );
	
}

add_action ( 'rankie_rank_hook', 'rankie_update_rank_function_wrap_hook' );

function rankie_update_rank_function_wrap(){
	
	$wp_rankie_options=get_option('wp_rankie_options' , array('OPT_AUTO_UPDATE',));
	$disable_untill= get_option('wp_rankie_disabled_till','1401802146');
	$timeBetweenCalls = 360;
	
	if(time() < $disable_untill ){
		
		$diff_time = $disable_untill - time() ;
		
		rankie_log_new('Cron', "رتبه بندي به روزرساني از طريق UI در حال کار است $diff_time ثانیه");
		echo 'رتبه به روزرساني از طريق UI کار مي کند cron خواهد خوابيد' ;
		return;
	}
 	
	if( ! in_array('OPT_AUTO_UPDATE', $wp_rankie_options)){
		rankie_log_new('کرون','به روزرساني هاي رتبه بندي خودکار خاموش هستند ، اکنون به خواب مي روند.');
		return;
	}
	
	// Wrap function that run updater no less than 120 seconds
	$now=time();
	$lastRun =  get_option('wp_rankie_last_run') ;
	$diff = $now - $lastRun ;
	
	if($diff > $timeBetweenCalls - 2 ) {
		
		//run it now
		update_option('wp_rankie_last_run', $now);
		rankie_update_rank_function();
		
	}else{
		
		// not yet passed interval
		$message = 'کرون آخرين اجرا از'.$diff. ' ثانيه منتظر خواهد ماند '.( $timeBetweenCalls - $diff ).' ثانيه.';
		rankie_log_new('کرون',$message);
		echo $message ;
		
	}
	
}

function rankie_update_rank_function_wrap_hook(){
	
	$wp_rankie_options=get_option('wp_rankie_options' , array('OPT_AUTO_UPDATE'));
	
	if(  in_array('OPT_EXTERNAL', $wp_rankie_options)){
		return;
	}
	
	rankie_log_new('کرون داخلي','دستور کرون داخلي فراخواني شده.');
	
	// check if external cron is active, disable internal cron
	$wp_rankie_last_external_cron = get_option('wp_rankie_last_external_cron','1464176068');
	$now = time();
	
	// if external cron triggerd during last two minutes don't update
	if($now < $wp_rankie_last_external_cron + 360 ){
		rankie_log_new('کرون داخلي','قبلاً کمتر از دو دقيقه قبل يک کرون خارجي ايجاد شده بود. خارج شدن');
		return;
	}
	
	rankie_log_new('کرون داخلي','کرون داخلي اکنون يک بروزرساني رتبه را ايجاد مي کند.');
	 
	rankie_update_rank_function_wrap();
	
	
}

function rankie_update_rank_function() {

 	echo 'Lets update one rank ';
 	
 	$currentTime = time();
 	$wp_rankie_update_interval = get_option('wp_rankie_update_interval','1');
 	$yesterDay = $currentTime - ( $wp_rankie_update_interval * 24 * 60 * 60 );
 	
 	 
 	//get records that was not updated from yesterday 
 	//keywords that are ranking have higher periority
 	global $wpdb;
    $query="SELECT * FROM {$wpdb->prefix}rankie_keywords where date_updated < $yesterDay and keyword_rank > 0 order by keyword_rank ASC limit 1";
    $rows=$wpdb->get_results($query);
    
    
    if(count($rows) == 0 ){
    	$query="SELECT * FROM {$wpdb->prefix}rankie_keywords where date_updated < $yesterDay and keyword_rank = 0 limit 1";
    	$rows=$wpdb->get_results($query);
    	
    }
    
    if(count($rows) > 0 ){
    	
    	rankie_log_new('راه انداز کرون', 'Cron فقط اجرا شده است و به روزرساني رتبه کلمات کليدي واجد شرايط وجود دارد');
    	
    	//good we have a record to update 
    	$row = $rows[0];
    	
    	echo ' به روزرساني رکورد:'.$row->keyword_id;
    	
    	$rank= rankie_fetch_rank ( $row->keyword_id );
    
    	 
    	if (isset ($rank['status']) ){
    		if($rank['status'] == 'success'  ){
    			echo ' با موفقيت به روز شد:'.$rank['rank'];
    			
    		}
    		
    	}else{
    		echo ' بدون پاسخ معتبر';
    	}
    	
    }
	
}