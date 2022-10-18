<?php

/*
 * New Log Entry
 */
function rankie_log_new($type,$data){
	
	global $wpdb;
	global $wp_rankie_last_log;
	
	$now = date( 'Y-m-d H:i:s');
	$data=addslashes($data);
	 
	$query="INSERT INTO {$wpdb->prefix}rankie_log (action,date,data) values('$type','$now','$data')";
	$wpdb->query($query);
	
	$wp_rankie_last_log = $data;
	
	//delete more than 100 log 
	$insert = $wpdb->insert_id;
	
	$insert_below_100 = $insert -100 ;
	
	if($insert_below_100 > 0){
		
		//delete 
		$query="delete from {$wpdb->prefix}rankie_log where id < $insert_below_100";
		$wpdb->query($query);
	}
	
}


/*
 * log page of the plugin
 */
function rankie_log(){
 
	//Table version check
	rankie_check_table_version();
	
	// DB
	global $wpdb;
	 
	//FILTER
	$filter="";
	if( isset( $_POST['action_type']) ){
		$act=$_POST['action_type'];
		if ($act == 'Error' ){
			$action=" action like '%Error%' ";
		}elseif($act == 'approved'){
			$action = " action like 'Comment approved%'";
		}
	}else{
		$action='';
			
	}

	if ($action != ''){
		if($filter == ''){
			$filter=" where $action";
		}else{
			$filter.=" and $action";
		}
	}

	// records number
	if(isset($_POST['number'])){
		$num=$_POST['number'];
	}else{
		$num='100';
	}

	// define limit
	$limit='';
	if (is_numeric($num)) $limit=" limit $num ";

	$qdate='';
	// finally date filters `date` >= str_to_date( '07/03/11', '%m/%d/%y' )
	if(isset($_POST['from']) && $_POST['from'] !='' ){
		$from=$_POST['from'];
		$qdate=" `date` >= str_to_date( '$from', '%m/%d/%y' )";
	}

	if(isset($_POST['to']) && $_POST['to'] !=''){
		$to=$_POST['to'];
		if($qdate == ''){
			$qdate.=" `date` <= str_to_date( '$to', '%m/%d/%y' )";
		}else{
			$qdate.=" and `date` <= str_to_date( '$to', '%m/%d/%y' )";
		}
	}

	if($qdate != ''){
		if($filter == ''){
			$filter=" where $qdate";
		}else{
			$filter.="and $qdate";
		}
	}
	 
	$query="SELECT * FROM {$wpdb->prefix}rankie_log $filter ORDER BY id DESC $limit";
	$res=$wpdb->get_results( $query);

	?>

 

<div class="wrap">
	<div class="icon32" id="icon-edit-comments">
		<br>
	</div>
	<h2>گزارش وقايع</h2>
	
	<form method="post" action="">
		<div class="tablenav top">

		 

			<div class="alignleft actions">
				<select name="number">
					<option <?php rankie_opt_selected($num,'50') ?>
						value="999">شماره سوابق</option>
					<option <?php rankie_opt_selected($num,'100') ?>
						value="100">100</option>
					<option <?php rankie_opt_selected($num,'500') ?>
						value="500">500</option>
					<option <?php rankie_opt_selected($num,'1000') ?>
						value="1000">1000</option>
					<option <?php rankie_opt_selected($num,'all') ?>
						value="all">همه</option>
				</select> <select name="action_type">
					<option <?php @rankie_opt_selected($act,'') ?>
						value="">نمايش همه ي رويدادها</option>
					<option <?php @rankie_opt_selected($act,'Error') ?>
						value="Error">خطا</option>

				</select>
			</div>
			<div class="clear"></div>
			<div class="alignleft actions" style="margin: 11px 0 11px 0">

				<label for="field1"> فرمت زمان ها: <small><i>(اختياري)</i> </small> </label>
				<input class="ttw-date date" name="from" id="field1" type="date"
					autocomplete="off"> <label for="field2"> از تاريخ: </label> <input
					class="ttw-date date" name="to" id="field2" type="date"
					autocomplete="off"> <input type="submit" value="فيلتر"
					class="button-secondary" id="post-query-submit" name="submit">
			</div>




		</div>

		</form>
		
		<div class="clear"></div>
		<?php $lastrun=get_option('wp_rankie_last_run',1392146043); ?>
		<div id="welcome-panel" class="welcome-panel">
			<p style="margin-top: -13px;"><strong> زمان </strong>فعلي سرور <strong>( <?php echo date('h:i:s') ?> )</strong>مي باشد، دستور کرون  آخرين بار در <strong>( <?php echo date("h:i:s",$lastrun ) ?> )</strong>اجرا شد. اين دستور در <strong> ( <?php echo $timdiff = time() - $lastrun ?> )</strong>ثانيه قبل اجرا شده. در هر <strong>( 360 )</strong>ثانيه يک مورد از صف اجرا مي شود. پردازش بعدي در<strong>( <?php echo( 360 - $timdiff )  ?> )</strong> ثانيه دوباره اجرا خواهد شد.</p>
		</div>
	
		
		<table class="widefat fixed">
			<thead>
				<tr>
					<th class="column-date">رديف</th>
					<th class="column-response">تاريخ</th>
					<th class="column-response">نوع عمل</th>
					<th>پردازش داده ها</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>رديف</th>
					<th>تاريخ</th>
					<th>نوع عمل</th>
					<th>پردازش داده ها</th>
				</tr>
			</tfoot>
			<tbody>

			<?php
			$i=1;
			foreach ($res as $rec){
				$action=$rec->action;
				//filter the data strip keyword
				$datas=explode(';',$rec->data);
				$data=$datas[0];


				if (stristr($action , 'Posted:')){
					$url = plugins_url().'/wp-auto-spinner';
					$action = 'New Post';
					//restoring link

				}elseif(stristr($action , 'Processing')){
					$action = 'Processing Campaign';
				}
				
				if(stristr($data,'html')){
					 $data='<textarea>'.htmlspecialchars( ($data) ).'</textarea>';
				}else{
					//$data=htmlspecialchars( ($data) );
				}
				

				echo'<tr class="'.$rec->action.'"><td class="column-date">'.$i.'</td><td  class="column-response" style="padding:5px">'.urldecode($rec->date).'</td><td  class="column-response" style="padding:5px">'. $action.'</td><td  style="padding:5px">' .urldecode($data).' </td></tr>';
				$i++;
			}


			?>
			</tbody>
		</table>

</div>
 


			<?php
}