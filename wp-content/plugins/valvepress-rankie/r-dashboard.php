<?php

// Dashboard
function rankie_dashboard_fn() {
	
	//INI
	global $wpdb;
	
	//Table verision check
	rankie_check_table_version();
	
	?>
<div class="wrap">
	<h2>
		داشبورد رتبه گوگل <a class="add-new-h2" href="#">افزودن کلمات کليدي</a>
	</h2>

	<?php 
		//get counts 
		
		// total count
		$query="SELECT count(*) as count FROM {$wpdb->prefix}rankie_keywords";
		$rows=$wpdb->get_results($query);
		$totalCount=$rows[0]->count;
		
		// manual count
		$query="SELECT count(*) as count FROM {$wpdb->prefix}rankie_keywords where keyword_type = 'Manual'";
		$rows=$wpdb->get_results($query);
		$manualCount=$rows[0]->count;
		
		// Auto count
		$query="SELECT count(*) as count FROM {$wpdb->prefix}rankie_keywords where keyword_type = 'Auto'";
		$rows=$wpdb->get_results($query);
		$autoCount=$rows[0]->count;
		
		// last log message
		$query="SELECT *  FROM {$wpdb->prefix}rankie_log order by id desc limit 1";
		$rows=$wpdb->get_results($query);
		$lastLog = $rows[0]->data;
		
		//Google Country
		$wp_rankie_google_gl=get_option('wp_rankie_google_gl','N');
		if(trim($wp_rankie_google_gl) == 'N'){
			$wp_rankie_google_gl ='';
		}else{
			$wp_rankie_google_gl = '&gl='.$wp_rankie_google_gl;
		}
		
		$wp_rankie_whatsmyserp_g = get_option('wp_rankie_whatsmyserp_g','www.google.com');
		
		$wp_rankie_method=get_option('wp_rankie_method','whatsmyserp');
		$wp_rankie_screen= get_option('wp_rankie_screen' , '200');
	
	?>

	<ul class="subsubsub">
		<li class="all"><a class="current" href="#">همه <span id="totalCount" class="count">(<?php echo $totalCount ; ?>)</span></a> |</li>
		<li class="manual"><a href="#">دستي <span id="manualCount" class="count">(<?php echo $manualCount ?>)</span></a> |</li>
		<li class="auto"><a href="#">خودکار <span class="count">(<?php echo $autoCount ?>)</span></a></li>

	</ul>
	<div class="clear"></div>
	<div id="field-site-container" class="tablenav top">



		<div class="alignleft actions bulkactions">
			<select class="action_select" name="action">
				<option selected="selected" value="-1">تنظيمات دسته جمعي</option>
				<option value="trash">حذف</option>
				<option value="change">تعويض گروه</option>
			</select> <input type="submit" value="اعمال" class="button action" id="doaction" name="">
		</div>

		<div style="display: none; margin-left: -10px;" class="spinner spinner-bulk"></div>

		<select name="site" id="wp-rankie-select-site">
			<option value="all">همه سايت ها</option>
			
				<?php 

					//get disnct groups 
					global $wpdb;
					$query="SELECT distinct keyword_site  FROM {$wpdb->prefix}rankie_keywords ";
					$sites=$wpdb->get_results($query);
					
					foreach ($sites as $row){
						echo '<option  value="'. $row->keyword_site .'"  >'. $row->keyword_site .'</option>'; 
					}
					
				?> 			
			
			 
		</select> <select name="site" id="wp-rankie-group">
			<option value="all">همه گروه ها</option>
			<?php 
				//get disnct groups
				global $wpdb;
				$query="SELECT distinct keyword_group  FROM {$wpdb->prefix}rankie_keywords ORDER BY  keyword_group";
				$groups=$wpdb->get_results($query);
					
				foreach ($groups as $row){
					echo '<option  value="'. $row->keyword_group .'"  >'. $row->keyword_group .'</option>';
				}
			?>
			

		</select>
		
		<p class="wp-rankie-search-box">
			<label for="post-search-input" class="screen-reader-text">جست و جوي کلمات کليدي:</label> <input placeholder="کلمات کليدي..." type="search" value="" name="s" id="post-search-input"> <input type="submit" value="جست و جوي کلمه کليدي" class="button" id="search-submit" name="">
		</p>

		</div>
		
		<div class="clear"></div>

	<table id="rankie-keywords" class="widefat">
		<thead>
			<tr>
				<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><label for="cb-select-all-1" class="screen-reader-text">انتخاب همه</label><input type="checkbox" id="cb-select-all-1" style="margin-top: 15px; margin-right: 12px;"></th>
				<th class="column-response">کلمه کليدي</th>
				<th class="column-response">رتبه</th>
				<th class="column-response">سايت</th>
				<th class="column-response">به روز کردن</th>
				<th class="column-response">حذف</th>
				<th class="column-response">نوع</th>
				<th class="column-response">گروه</th>
				<th class="column-response"></th>



			</tr>
		</thead>
		<tfoot>
			<tr>
				<th style="" class="manage-column column-cb check-column" scope="col"><label for="cb-select-all-1" class="screen-reader-text">انتخاب همه</label><input type="checkbox" id="cb-select-all-1"></th>
				<th class="column-response">کلمه کليدي</th>
				<th class="column-response">رتبه</th>
				<th class="column-response">سايت</th>
				<th class="column-response">به روز کردن</th>
				<th class="column-response">حذف</th>
				<th class="column-response">نوع</th>
				<th class="column-response">گروه</th>
				<th class="column-response"></th>


			</tr>
		</tfoot>
		<tbody>

			<?php 

				// RENDERING ROWS
				
				$query="SELECT * FROM {$wpdb->prefix}rankie_keywords";
				$rows=$wpdb->get_results($query);
				$wp_rankie_update_interval = get_option('wp_rankie_update_interval','1');
				$wp_rankie_update_threshold = $wp_rankie_update_interval * 24 *60 *60;
				
				foreach($rows as $row){
					?>
					
						<tr>

							<?php 
									$flag = 0;
									$date_updated =   is_numeric( $row->date_updated ) ?   $row->date_updated : 1396170942 ;
									$diff =  time() - $date_updated;
									 
									
									
									if($diff > $wp_rankie_update_threshold ) $flag = 1 ;
							?>
			
							<td><input type="checkbox" class="wp-rankie-keyword-id" value="<?php echo $row->keyword_id ?>" name="post[]" id="cb-select-<?php echo $row->keyword_id ?>"></td>
			
							<td> <span class="rankie-keyword-text"><?php echo $row->keyword ?></span></td>
							
							<td>  <?php  
							
							echo $row->keyword_rank ;
							 
							?> </td>
							
							<td> <span class="wp-rankie-keyword-site" ><?php echo $row->keyword_site ?></span></td>
							
							<td> <div class="spinner spinner-<?php echo $row->keyword_id ?>" style="display: none;"></div>
								<a class="wp-rankie-update-row" href="#">
									<div class="updatedz updated-<?php echo $row->keyword_id ?> dashicons <?php if($flag == 1){echo 'dashicons-clock' ; }else{echo 'dashicons-yes' ;}?>"></div>
								</a>
							</td>
							
							<td><a  class="wp-rankie-delete-row"  href="#"><div class="dashicons dashicons-no-alt"></div></a></td>
							
							<td><?php echo $row->keyword_type ?></td>
							
							<td><?php echo $row->keyword_group ?></td>
							
							
							<td><input type="hidden" class="wp-rankie-updated" value="<?php echo $flag ?>" /></td>
			
						</tr>		

					<?php 
				}
			
			?>
			 
		</tbody>
	</table>
	
	<div class="tablenav rankie-bottom">
	<div class="alignleft actions bulkactions">
			<select name="action2">
					<option selected="selected" value="-1">تنظيمات دسته جمعي</option>
					<option value="trash">حذف</option>
			</select>
			<input type="ثبت" name="" id="doaction2" class="button action" value="Apply">
			<div style="display: none; " class="spinner spinner-bulk"></div>
		</div>
	</div>
	
	<div class="clear"></div>
	<div>* اين افزونه هر 6 دقيقه يک کلمه کليدي را به روز مي کند و سعي مي کند پس از <span class="next_key"></span></span> ثانيه يک کلمه جديد را به روز کنيد. لطفاً صبور باشيد زيرا اين افزونه به منظور ايجاد ايمن در نرخ درخواست در Google ، در طول روز طراحي شده است. <p>* آخرين وقايع اتفاق افتاده -> <span class="wp_rankie_last_log" ><?php echo $lastLog ?> </span><br></p></div>
	<div class="categorydiv" id="taxonomy-category">
	    <ul class="category-tabs" id="category-tabs">
	        <li class="tabs"><a href="#">نمودار هاي رتبه</a>
	        </li>
	        <li class=""><a href="#">رکورد هاي رتبه</a>
	        </li>
	    </ul>
	
	    <div style="display: block;" class="tabs-panel" id="category-pop">
	         
	           
	         
	         <div id="chart_div"  ><p>براي نشان دادن نمودار رتبه بندي آن در اينجا ، روي هر کلمه کليدي بالا کليک کنيد ...</p></div>
	         
	         
	        
	         
	    </div>
	
	    <div class="tabs-panel" id="category-all" style="display: none;">
	         
	         <div class="rankie-chart-contain">
		         <table class="widefat"  id="rakie-chart" >
					<caption>رکورد هاي منحصر به فرد رتبه بندي از "<span class="wp-rakie-chart-site">سايت</span>" روي کلمه کليدي "<span class="wp-rakie-chart-keyword">کلمه کليدي</span>" </caption>
					<thead>
						<tr>
							<th>تاريخ</th> 
							<th>رتبه</th>
							<th>لينک</th>
							 
						</tr>
					</thead>
					<tbody>
						  
					</tbody>
				</table>
				<div class="description" style="margin-top:20px;"><strong>مهم:</strong> اگر رتبه امروز برابر با آخرين رتبه منحصر به فرد است ، ممکن است در اينجا ثبت نشده باشد. اگر علامت بررسي موفقيت را در زير قسمت به روز داريد ، بدان معني است که رتبه امروز به روز شده است.</div>
			</div>
	         
	    </div>
	    
 
	
	</div>
	
	
</div>

<div style="display:none" id="keywordsDialog">

	<table id="wp-rankie-table-add">
		
		<tbody>
			<tr>
				<td>سايت</td>
				<td><input class="fwidth" id="wp-rankie-keywords-site" type="text" placeholder="your site" value="<?php echo $_SERVER['HTTP_HOST']; ?>" /></td>
			</tr>
			<tr>
				<td>کلمه کليدي</td>
				<td><textarea id="wp-rankie-keywords-add"></textarea><br><div class="description">کلمه کليدي را در هر خط وارد کنيد</div></td>
				
			</tr>
			<tr>
				<td>
					نوع تطبيق
				</td>
				<td>
					 <input checked="checked" type="radio" name="matchType" value="domain">از هر صفحه دامنه<br>
	 				 <input type="radio" name="matchType" value="url">دقيقا مانند آدرس url وارد شود<br>
				</td>
				
			</tr>
			
			<tr>
				<td>گروه</td>
				<td>
					<select name="wp-rankie-group-select" id="wp-rankie-group-select">
						<option  value="General"  >عمومي</option>
						<?php 
							//get disnct groups 
							global $wpdb;
							$query="SELECT distinct keyword_group  FROM {$wpdb->prefix}rankie_keywords ";
							$groups=$wpdb->get_results($query);
							
							foreach ($groups as $row){
								if($row->keyword_group != "General")

								echo '<option  value="'. $row->keyword_group .'"  >'. $row->keyword_group .'</option>'; 
							}
							
						?> 
						
						
						<option  value="wp-rankie-group-new"  >گروه جديد</option>
					</select>
					
					<input  class="fwidth"  style="display:none;" placeholder="نام گروه جديد ..." id="wp-rankie-group-new-text" name="wp-rankie-group-new-text" type="text" value="" >
					
				<br><div class="description">کلمات کليدي خود را يک کلمه کليدي در هر خط اضافه کنيد  </div></td>
			</tr>
			
			<tr>
				<td></td>
				<td><div class="spinner spinner-btn-add" style="float: right !important;display: none; margin-bottom: -25px;"></div>
				<input   class="fwidth"  class="button"  type="submit" value="افزودن کلمه کليدي" id="wp-rankie-keywords-add-btn" /></td>
			</tr>
		</tbody>
	
	</table>
	
	
	
	

</div>


<div style="display:none" id="groupChange">

	<table id="wp-rankie-table-change">
		
		<tbody>
			 
			
			<tr>
				<td>گروه</td>
				<td>
					<select name="wp-rankie-group-select-change" id="wp-rankie-group-select-change">
						<option  value="General"  >عمومي</option>
						<?php 
							//get disnct groups 
							global $wpdb;
							$query="SELECT distinct keyword_group  FROM {$wpdb->prefix}rankie_keywords ";
							$groups=$wpdb->get_results($query);
							
							foreach ($groups as $row){
								
								if($row->keyword_group != "General")
								echo '<option  value="'. $row->keyword_group .'"  >'. $row->keyword_group .'</option>';
								
							}
							
						?>
						 
						<option  value="wp-rankie-group-new-change"  >گروه جديد</option>
						
					</select>
					
					<input style="display:none;" placeholder="نام گروه جديد ..." id="wp-rankie-group-new-text-change" name="wp-rankie-group-new-text-change" type="text" value="" >
					
				<br></td>
			</tr>
			
			<tr>
				<td></td>
				<td><div class="spinner spinner-btn-change" style="float: right !important;display: none; margin-bottom: -25px;"></div>
				<input  class="button"  type="submit" value="Change Group" id="wp-rankie-keywords-change-btn" /></td>
			</tr>
		</tbody>
	
	</table>
	
	
	
	

</div>


<script type="text/javascript">

						var totalCount = <?php echo $totalCount ?>;
						var manualCount = <?php echo $manualCount ?>;
						var googleL='<?php echo $wp_rankie_google_gl ?>';
						var googleWhatsmyserp='<?php echo $wp_rankie_whatsmyserp_g ?>';
						
						var googleMethod = '<?php echo $wp_rankie_method ?>';
						var sScroll = '<?php echo $wp_rankie_screen ?>';
						
</script>

<?php
}