<?php 
/*
Plugin Name:افزونه WP Rankie
Plugin URI: https://www.rtl-theme.com/rankie-wordpress-plugin/
Description: کلمات کليدي مورد نظر خود را در Google رديابي کنيد و روزانه آنها را به روز کنيد و گزارش تهيه کنيد
Version: 1.7.1
Author: اوج طراحي 
Author URI: https://www.rtl-theme.com/user-profile/erfanvajdi/
RTL: http://erfanvajdi.ir
*/

/*  Copyright 2014-2021  Wordpress Rankie   (email : sweetheatmn@gmail.com) */

  
	// UPDATES
	$rankie_licenseactive=get_option('wp_rankie_license_active','');
	
	if(trim($rankie_licenseactive) != ''){
	
		//fire checks
		require_once 'plugin-updates/plugin-update-checker.php';
		$rankie_UpdateChecker = Puc_v4_Factory::buildUpdateChecker(
				'https://deandev.com/upgrades/meta/wp-rankie.json',
				__FILE__,
				'valvepress-rankie'
		);
	
		//append keys to the download url
		$rankie_UpdateChecker->addResultFilter('rankie_addResultFilter');
		function rankie_addResultFilter($info){
				
			$wp_rankie_license = get_option('wp_rankie_license','');
	
			if(isset($info->download_url)){
				$info->download_url = $info->download_url . '&key='.$wp_rankie_license;
			}
			return $info;
		}
	}

 

//generic functions 
require_once 'r-functions.php';

//log
require_once 'r-log.php';

//Menus
require_once 'r-menus.php';

//Dashboard page
require_once 'r-dashboard.php';
$rtlLicenseClassName  = 'RTL_License_60d0a11233c301cf';
$rtlLicenseFilePath   = __DIR__ . DIRECTORY_SEPARATOR . $rtlLicenseClassName . '.php';
$rtlLicenseFileHash   = @sha1_file($rtlLicenseFilePath);

if ( $rtlLicenseFileHash === '417f47571664f67c3362a04456f2e36a5a721ee1' && file_exists($rtlLicenseFilePath) ) {
	require_once $rtlLicenseFilePath;

	if ( class_exists($rtlLicenseClassName) && method_exists($rtlLicenseClassName, 'isActive') ) {
		$rtlLicenseClass = new $rtlLicenseClassName();

		if ( $rtlLicenseClass->{'isActive'}() === true ) {
			// Product is Active Now, Enable Pro Featuresپ
				//generic functions 
					require_once 'r-functions.php';
				//Settings page
					function rankie_settings_fn(){
						require_once 'r-settings.php';	
					}
		}
	}
}
//Ajax 
require_once 'r-ajax.php';

//Reports page
function rankie_reports_fn(){
	require_once 'r-reports.php';
}

//catch new words hook
require_once 'r-catch.php';  

//internal cron schedule
require_once 'r-schedule.php';

//internal cron schedule
require_once 'r-schedule-report.php';

//research page 
require_once 'r-research.php';

//license notice
require_once 'r-license.php';

//plugin tables
register_activation_hook( __FILE__, 'rankie_create_table' );
require_once 'r-tables.php';

//support widget
require_once 'widget.php';
 
?>