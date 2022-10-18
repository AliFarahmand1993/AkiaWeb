<?php
global $rankie_ch;
global $rankie_last_log;
global $rankie_currentPage;

//ch handler 
if (function_exists ( 'curl_init' )) {
	$rankie_ch = curl_init ();
	curl_setopt ( $rankie_ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $rankie_ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $rankie_ch, CURLOPT_CONNECTTIMEOUT, 20 );
	curl_setopt ( $rankie_ch, CURLOPT_TIMEOUT, 60 );
	curl_setopt ( $rankie_ch, CURLOPT_REFERER, 'http://www.whatsmyserp.com/' );
	curl_setopt ( $rankie_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/41.0.2272.76 Chrome/41.0.2272.76 Safari/537.36' );
	curl_setopt ( $rankie_ch, CURLOPT_MAXREDIRS, 5 ); // Good leeway for redirections.
	curl_setopt ( $rankie_ch, CURLOPT_COOKIEJAR, "cookie.txt" );
	curl_setopt ( $rankie_ch, CURLOPT_SSL_VERIFYPEER, false );
}

/**
 * Function opt_selected
 */
if (! function_exists ( 'rankie_opt_selected' )) {
	function rankie_opt_selected($src, $val) {
		if (trim ( $src ) == trim ( $val ))
			echo ' selected="selected" ';
	}
}

/**
 * Function rankie_fetch_rank : switch between rank fetching methods and uses one selected in settings
 */
function rankie_fetch_rank($itemId) {
	
	// get what method to use
	$method = get_option ( 'wp_rankie_method', 'googledirect' );
	
	rankie_log_new('آپديت رنک','تلاش براي به روزرساني رتبه براي کلمه کليدي #'.$itemId );														 
	
	if ($method == 'googledirect') {
		return rankie_fetch_rank_google ( $itemId );
	} elseif ($method == 'ajax') {
		return rankie_fetch_rank_google ( $itemId );
	} elseif ($method == 'googlecustom') {
		return rankie_fetch_rank_googlecustom ( $itemId );
	} 

}

/**
 * Google fetch rank using Google Custom API
 */
function rankie_fetch_rank_googlecustom($id) {
	
	// INI
	global $wpdb;
	global $rankie_ch;
	global $rankie_currentPage;
	$debug = false;
	
	$wp_rankie_ezmlm_gl = get_option ( 'wp_rankie_ezmlm_gl', 'com' );
	$wp_rankie_ezmlm_gl = 'google.' . $wp_rankie_ezmlm_gl;
	$wp_rankie_options = get_option ( 'wp_rankie_options', array () );
	
	$return ['id'] = $id;
	$return ['status'] = 'error';
	$return ['rank'] = 0;
	$reutrn ['message'] = '';
	$reutrn ['link'] = '';
	
	// Validate key and engine id are added
	$wp_rankie_googlecustom_id = get_option ( 'wp_rankie_googlecustom_id', '' );
	$wp_rankie_googlecustom_key = get_option ( 'wp_rankie_googlecustom_key', '' );
	
	if (trim ( $wp_rankie_googlecustom_id ) == '' || trim ( $wp_rankie_googlecustom_key ) == '') {
		
		$message = 'لطفاً به صفحه تنظيمات افزونه مراجعه کرده و کليد مورد نياز و شناسه موتور جستجو را اضافه کنيد';		
		rankie_log_new ( 'گوگل سفارشي', $message );
		
		$reutrn ['message'] = $message;
		return $return;
	}
	
	// Now get one key out of many if applicable
	$wp_rankie_googlecustom_keys = explode ( ',', $wp_rankie_googlecustom_key );
	$wp_rankie_googlecustom_keys = array_filter ( $wp_rankie_googlecustom_keys );
	$now = time ();
	
	$validWorkingKey = '';
	foreach ( $wp_rankie_googlecustom_keys as $current_key ) {
		
		if (trim ( $current_key ) != '') {
			
			// check if key is disabled or not
			$current_keyMd5 = md5 ( $current_key );
			$disabledTill = get_option ( 'wp_rankie_' . $current_keyMd5, '1463843434' );
			
			if ($disabledTill > $now) {
				continue;
			} else {
				$validWorkingKey = $current_key;
				break;
			}
		}
	}
	
	if (trim ( $validWorkingKey ) == '') {
		$message = 'کليدهاي استفاده شده به حداکثر حد مجاز درخواست در هر روز 100 برابر هستند. بعد از يک ساعت دوباره بررسي خواهد کرد';
		rankie_log_new('سفارشي Google', $message);
		
		$reutrn ['message'] = $message;
		return $return;
	} else {
		$wp_rankie_googlecustom_key = $validWorkingKey;
		rankie_log_new('سفارشي Google', 'استفاده از کليد:'.$validWorkingKey);
	}
	
	// GET RECORD
	$query = "SELECT * FROM {$wpdb->prefix}rankie_keywords where keyword_id=$id";
	$rows = $wpdb->get_results ( $query );
	$row = $rows [0];
	$keyword = $row->keyword;
	$keyword_site = $row->keyword_site;
	@$last_checked_page = $row->last_checked_page;
	$keyword_rank = $row->keyword_rank;
	
	$keyword_site_full = ''; // full url if the keyword site is an exact url
	
	if (stristr ( $keyword_site, 'http:' ) || stristr ( $keyword_site, 'https:' ) || stristr ( $keyword_site, 'www.' )) {
		
		$keyword_site_full = $keyword_site;
		
		// www without http, add http
		if (stristr ( $keyword_site, 'www.' ) && ! stristr ( $keyword_site, 'http' )) {
			$keyword_site = 'http://' . $keyword_site;
		}
		
		// getting the domain
		$host = parse_url ( $keyword_site, PHP_URL_HOST );
		$host = preg_replace ( '{^www\.}', '', $host );
		$keyword_site = $host;
		
		// not a domain but a full url without http or www
	} elseif (preg_match ( '{/.}', $keyword_site )) {
		
		$keyword_site_full = $keyword_site;
		
		$keyword_site = 'http://' . $keyword_site;
		$host = parse_url ( $keyword_site, PHP_URL_HOST );
		$keyword_site = $host;
	}
	
	if ($debug)
		echo ' KeywordSiteFull:' . $keyword_site_full;
	if ($debug)
		echo ' KeywordSite:' . $keyword_site;
	
	// get the startIndex from the last_checked_page
	$startIndex = 1;
	$rankie_currentPage = 1;
	
	if (! in_array ( 'OPT_PAGINATE', $wp_rankie_options )) {
		
		// get currentPage to check
		if ($keyword_rank == 0) {
			
			$rankie_currentPage = $last_checked_page + 1;
		} else {
			
			$rankie_currentPage = $last_checked_page;
			
			if ($rankie_currentPage == 0)
				$rankie_currentPage = 1;
		}
		
		// if passed limit reset
		if ($rankie_currentPage > 10)
			$rankie_currentPage = 1;
		
		// startIndex from the current page
		$startIndex = ($rankie_currentPage - 1) * 10 + 1;
	}
	
	// Process request
	$gurl = "https://www.googleapis.com/customsearch/v1?key=" . urlencode ( trim ( $wp_rankie_googlecustom_key ) ) . "&cx=" . urlencode ( trim ( $wp_rankie_googlecustom_id ) ) . "&q=" . urlencode ( trim ( $keyword ) ) . '&googlehost=' . urlencode ( $wp_rankie_ezmlm_gl ) . '&start=' . $startIndex;
	
	if ($debug)
		print_r ( $gurl );
	
	// curl get
	$x = 'error';
	$url = $gurl;
	curl_setopt ( $rankie_ch, CURLOPT_HTTPGET, 1 );
	curl_setopt ( $rankie_ch, CURLOPT_URL, trim ( $url ) );
	
	$exec = curl_exec ( $rankie_ch );
	$x = curl_error ( $rankie_ch );
	
	// validate returned content
	if (trim ( $exec ) == '') {
		$message = 'هيچ محتوا از پردازش درخواست api خطاي احتمالي حلقوي بازگردانده نشده است:'.$x;
		rankie_log_new('Google سفارشي', $message);
		$return ['message'] = $message;
		return $return;
	}
	
	// validate json
	if (! stristr ( $exec, '{' )) {
		$message = 'هيچ پاسخ Json به  مظنون پاسخ نداد';
		$return ['message'] = $message;
		return $return;
	}
	
	// good let's get results
	$jsonReply = json_decode ( $exec );
	
	if (isset ( $jsonReply->error )) {
		
		$jsonErr = $jsonReply->error->errors [0];
		
		$errReason = $jsonErr->reason;
		$errMessage = $jsonErr->message;
		
		$message = 'Api خطايي را برگشت داد: '.$errReason.' '.$errMessage;
		rankie_log_new('سفارشي Google', $message);
		
		// disable limited keys
		if ($errReason == 'dailyLimitExceeded') {
			update_option ( 'wp_rankie_' . $current_keyMd5, $now + 60 * 60 );
		}
		
		$return ['message'] = $message;
		return $return;
	}
	
	$foundLinks = $jsonReply->items;
	
	if ($debug)
		print_r ( $foundLinks );
	
	if (count ( $foundLinks ) == 0) {
		
		// no results apear here
		rankie_update_rank ( $id, 0, '' );
		$return ['status'] = 'success';
		$return['message']='پيوندي در صفحه گوگل برگشته يافت نشد';
		rankie_log_new('سفارشي Google', 'پيوندي يافت نشد');
		return $return;
	} else {
		
		// good news we have links
		$i = 0;
		
		foreach ( $foundLinks as $foundLink ) {
			
			$finalUrl = $foundLink->link;
			
			if ($debug)
				echo "\nChecking URL:" . $finalUrl;
			
			$i ++;
			
			// compare final url host with site host
			if (stristr ( $finalUrl, $keyword_site )) {
				// verify
				
				$parsedUrl = parse_url ( $finalUrl );
				$parsedHost = $parsedUrl ['host'];
				
				if ($debug)
					echo "\n  parsedHost:" . $parsedHost;
				
				$parsedHost = preg_replace ( '/^www\./', '', $parsedHost );
				
				if ($debug)
					echo "\n  parsedHostwithoutwww:" . $parsedHost;
				
				if (trim ( $parsedHost ) == trim ( $keyword_site )) {
					
					// fix rank according to the page
					if ($rankie_currentPage > 1) {
						$rank = $i + ($rankie_currentPage - 1) * 10;
					} else {
						$rank = $i;
					}
					
					// Now same domain check if exact match
					if (trim ( $keyword_site_full ) != '') {
						
						// exact match let's match
						
						// clean original url
						$cleanOriginalUrl = preg_replace ( '{http[s]?://}', '', $keyword_site_full );
						$cleanOriginalUrl = preg_replace ( '{^www\.}', '', $cleanOriginalUrl );
						$cleanOriginalUrl = preg_replace ( '{/$}', '', $cleanOriginalUrl );
						
						// clean final url
						$cleanFinalUrl = preg_replace ( '{http[s]?://}', '', $finalUrl );
						$cleanFinalUrl = preg_replace ( '{^www\.}', '', $cleanFinalUrl );
						$cleanFinalUrl = preg_replace ( '{/$}', '', $cleanFinalUrl );
						
						if ($debug)
							echo "\n Clean FinalUrl:" . $cleanFinalUrl;
						
						if ($debug)
							echo "\n Clean OriginalUrl:" . $cleanOriginalUrl;
						
						if ($cleanFinalUrl != $cleanOriginalUrl)
							continue;
					}
					
					$return ['rank'] = $rank;
					$return ['link'] = $finalUrl;
					$return ['status'] = 'success';
					
					rankie_update_rank ( $id, $i, $finalUrl );
					
					return $return;
				}
			}
		}
	}
	
	// oh loop ended without a match let's update it to 0
	
	if (count ( $foundLinks ) > 0) {
		$return ['count'] = count ( $foundLinks );
	}
	
	rankie_update_rank ( $id, 0, '', count ( $foundLinks ) );
	$return ['status'] = 'success';
	return $return;
}

/**
 * Google ajax rank
 */
function rankie_fetch_rank_ajax($id) {
	
	// INI
	global $wpdb;
	global $rankie_ch;
	
	// GET RECORD
	$query = "SELECT * FROM {$wpdb->prefix}rankie_keywords where keyword_id=$id";
	$rows = $wpdb->get_results ( $query );
	$row = $rows [0];
	$keyword = $row->keyword;
	$keyword_site = $row->keyword_site;
	
	// print_r($row);
	
	// language
	$gl = get_option ( 'wp_rankie_google_gl', 'us' );
	
	return rankie_fetch_rank_ajax_call ( $id, $keyword, $keyword_site, 0, $gl );
}
function rankie_fetch_rank_ajax_call($itemId, $itemText, $itemSite, $searchIndex, $gl) {
	global $rankie_ch;
	
	$return ['id'] = $itemId;
	$return ['status'] = 'error';
	$return ['rank'] = 0;
	$reutrn ['message'] = '';
	$reutrn ['link'] = '';
	
	$glink = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=" . rawurlencode ( $itemText ) . "&rsz=8&start=" . $searchIndex * 8 . "&gl=" . $gl;
	
	rankie_log_new('google ajax api', 'تماس با ايجکس api براي صفحه '.$searchIndex);
	
	// curl get
	$x = 'error';
	$url = $glink;
	curl_setopt ( $rankie_ch, CURLOPT_HTTPGET, 1 );
	curl_setopt ( $rankie_ch, CURLOPT_URL, trim ( $url ) );
	
	$exec = curl_exec ( $rankie_ch );
	$x = curl_error ( $rankie_ch );
	
	if (trim ( $exec ) == '') {
		
		$return['message'] == 'پاسخي خالي از تماس گوگل آژاکس';
		rankie_log_new('google ajax api', 'پاسخ خالي از google ajax api دريافت کرد');
	} else {
		
		// good we got a reply
		if (substr ( $exec, 0, 1 ) != '{') {
			$return['message'] == 'پاسخي خالي از تماس google ajax';
			rankie_log_new('google ajax api', 'پاسخ json را از گوگل دريافت نکردم');
		} else {
			// cool we have a json reply
			$jsonReply = json_decode ( $exec );
			
			if (! isset ( $jsonReply->responseData )) {
				$return['message'] == 'برگشت داده ها در پاسخ يافت نشد';
				rankie_log_new('google ajax api', 'برگشت داده ها در پاسخ يافت نشد');
			} else {
				// good we have a response data check if cursor
				if (! isset ( $jsonReply->responseData->cursor ) || ! isset ( $jsonReply->responseData->results )) {
					// oops not results update to 0
					$return ['status'] = 'success';
					
					// UPDATE TO 0
					rankie_update_rank ( $itemId, 0, '' );
					
					return $return;
				} else {
					// cool we have cursor so results
					$results = $jsonReply->responseData->results;
					
					$rankExist = rankie_rank_exists ( $itemSite, $results );
					
					if ($rankExist) {
						// good we got a rank
						$finalRank = $searchIndex * 8 + $rankExist;
						
						$return ['status'] = 'success';
						$return ['rank'] = $finalRank;
						
						// update rank
						rankie_update_rank ( $itemId, $finalRank, $results [$rankExist - 1]->unescapedUrl );
						
						return $return;
					} else {
						
						// hmm site not withen the reults try another page if available
						$searchIndex = $searchIndex + 1;
						
						if ($searchIndex < count ( $jsonReply->responseData->cursor->pages )) {
							
							return rankie_fetch_rank_ajax_call ( $itemId, $itemText, $itemSite, $searchIndex, $gl );
						} else {
							// reached end without result let's set to 0
							$return ['status'] = 'success';
							
							// UPDATE TO 0
							rankie_update_rank ( $itemId, 0, '' );
							
							return $return;
						}
					}
				}
			}
		}
	} // trim exec
	
	return $return;
}

/**
 * Function : rank exists in list of url
 */
function rankie_rank_exists($site, $results) {
	$i = 1;
	
	foreach ( $results as $result ) {
		
		if (stristr ( $result->unescapedUrl, $site )) {
			
			$parse = parse_url ( $result->unescapedUrl );
			
			$parse ['host'] = preg_replace ( '/^www\./', '', $parse ['host'] );
			
			if ($parse ['host'] == $site) {
				// weekweek we fount the rank
				return $i;
			}
		}
		
		$i ++;
	}
	
	return false;
}
 

/**
 * Fetch RANK from googld directly
 * 
 * @param unknown $id
 */
function rankie_fetch_rank_google($id) {
	
	// INI
	global $wpdb;
	$wp_rankie_ezmlm_gl = get_option ( 'wp_rankie_ezmlm_gl', 'com' );
	$wp_rankie_google_county = 'N';
	$wp_rankie_options = get_option ( 'wp_rankie_options', array () );
	$wp_rankie_proxies = get_option ( 'wp_rankie_proxies', '' );
	$wp_rankie_lat = get_option ( 'wp_rankie_lat', '' );
	$wp_rankie_long = get_option ( 'wp_rankie_long', '' );
	
	$debug = false;
	
	$return ['id'] = $id;
	$return ['status'] = 'error';
	$return ['rank'] = 0;
	$reutrn ['message'] = '';
	$reutrn ['link'] = '';
	
	// GET RECORD
	$query = "SELECT * FROM {$wpdb->prefix}rankie_keywords where keyword_id=$id";
	$rows = $wpdb->get_results ( $query );
	$row = $rows [0];
	$keyword = $row->keyword;
	$keyword_site = trim ( $row->keyword_site );
	
	$keyword_site_full = ''; // full url if the keyword site is an exact url
	
	if (stristr ( $keyword_site, 'http:' ) || stristr ( $keyword_site, 'https:' ) || stristr ( $keyword_site, 'www.' )) {
		
		$keyword_site_full = $keyword_site;
		
		// www without http, add http
		if (stristr ( $keyword_site, 'www.' ) && ! stristr ( $keyword_site, 'http' )) {
			$keyword_site = 'http://' . $keyword_site;
		}
		
		if ($debug)
			echo ' FullSite:' . $keyword_site_full;
		
		// getting the domain
		$host = parse_url ( $keyword_site, PHP_URL_HOST );
		$host = preg_replace ( '{^www\.}', '', $host );
		$keyword_site = $host;
		
		if ($debug)
			echo ' Host:' . $keyword_site;
		
		// not a domain but a full url without http or www
	} elseif (preg_match ( '{/.}', $keyword_site )) {
		
		$keyword_site_full = $keyword_site;
		
		$keyword_site = 'http://' . $keyword_site;
		$host = parse_url ( $keyword_site, PHP_URL_HOST );
		$keyword_site = $host;
	}
	
	// SERP CALL
	
	// curl ini
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );
	curl_setopt ( $ch, CURLOPT_REFERER, 'https://www.google.' . $wp_rankie_ezmlm_gl );
	// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:28.0) Gecko/20100101 Firefox/28.0');
	//curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/601.5.17 (KHTML, like Gecko) Version/9.1 Safari/601.5.17' );
	
	curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36' );
	curl_setopt ( $ch, CURLOPT_MAXREDIRS, 5 ); // Good leeway for redirections.
	@curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 ); // Many login forms redirect at least once.
	@curl_setopt ( $ch, CURLOPT_COOKIEJAR, "cookie.txt" );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
	
	  
	
	// curl get
	$x = 'error';
	$url = 'https://www.google.' . $wp_rankie_ezmlm_gl . '/search?q=' . urlencode ( $keyword ) . '&btnG=Search&client=ubuntu&channel=fs&num=100';
	
	// if google .com ncr google first
	if ($wp_rankie_ezmlm_gl == 'com') {
		$url = $url . '&ie=utf-8&oe=utf-8&gfe_rd=cr&ei=sw9CVbCuPKaA8QfX0ICYBA&gws_rd=cr';
	}
	
	// specific country
	if ($wp_rankie_google_county != 'N') {
		$url .= '&tbs=ctr:country' . $wp_rankie_google_county . '&cr=country' . $wp_rankie_google_county;
	}
	
	// long , lat specific location
	if (stristr ( $wp_rankie_lat, '.' ) && stristr ( $wp_rankie_long, '.' )) {
		$long = str_replace ( '.', '', number_format ( $wp_rankie_long, 7 ) );
		$lat = str_replace ( '.', '', number_format ( $wp_rankie_lat, 7 ) );
		
		if (is_numeric ( $long ) && is_numeric ( $lat )) {
			
			$loc = "role:1 producer:12 provenance:6 latlng{latitude_e7:$lat longitude_e7:$long} radius:93000";
			$uule = "a+" . base64_encode ( $loc );
			
			$headers [] = "Cookie: UULE=$uule;";
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		}
	}
	
	if ($debug)
		echo ' gurl:' . $url;
	
	curl_setopt ( $ch, CURLOPT_HTTPGET, 1 );
	curl_setopt ( $ch, CURLOPT_URL, trim ( $url ) );
	
	// proxification
	$proxified = in_array ( 'OPT_PROXY', $wp_rankie_options );
	if (in_array ( 'OPT_PROXY', $wp_rankie_options )) {
		
		if (trim ( $wp_rankie_proxies ) != '') {
			
			// parsing proxies
			$proxies = array_filter ( explode ( "\n", $wp_rankie_proxies ) );
			
			foreach ( $proxies as $proxy ) {
				
				if (trim ( $proxy ) != '' && stristr ( $proxy, ':' )) {
					$validProxies [] = $proxy;
				}
			}
			
			foreach ( $validProxies as $validProxy ) {
				
				rankie_log_new('گوگل مستقيماً پروکسي مي کند', 'تلاش براي استفاده از پروکسي'.$validProxy);
				
				$proxy = $validProxy;
				curl_setopt ( $ch, CURLOPT_HTTPPROXYTUNNEL, 0 );
				
				$proxy_parts = explode ( ':', $proxy );
				
				if (count ( $proxy_parts ) > 2) {
					
					// authentication
					$loginpassw = trim ( $proxy_parts [2] ) . ':' . trim ( $proxy_parts [3] );
					curl_setopt ( $ch, CURLOPT_PROXY, trim ( $proxy_parts [0] . ':' . $proxy_parts [1] ) );
					curl_setopt ( $ch, CURLOPT_PROXYUSERPWD, $loginpassw );
				} else {
					curl_setopt ( $ch, CURLOPT_PROXY, $proxy );
				}
				
				$exec = curl_exec ( $ch );
				$x = curl_error ( $ch );
				
				if (trim ( $exec ) == '') {
					$return['message']='صفحه خالي گوگل';
					rankie_rotate_proxyies ( $validProxies, $validProxy );
					
					rankie_log_new('پروکسي مستقيم Google', 'پاسخ را خالي با عدم موفقيت در اتصال کنيد '.$x);
				} else {
					
					if (stristr ( $exec, 'protect our users' ) || stristr ( $exec, 'answer/86640' )) {
						
						$return['message']='Google اين درخواست را مسدود کرد ...';
						rankie_rotate_proxyies ( $validProxies, $validProxy );
						rankie_log_new('پروکسي مستقيم گوگل', 'گوگل يک captcha به ما نشان داد.');
					} else {
						
						if (stristr ( $exec, 'CaptchaRedirect' )) {
							
							rankie_log_new('پروکسي مستقيم Google', 'Google خواستار پر کردن يک captcha است');
							
							$return['message']='گوگل از حل مسئله captcha پرسيد ...';
							rankie_rotate_proxyies ( $validProxies, $validProxy );
						} else {
							
							// seems as valid proxy let's break here
							rankie_log_new('پروکسي مستقيم Google', 'به نظر مي رسد پروکسي کار مي کند');
							break;
						}
					}
				}
			}
		} else {
			rankie_log_new ( 'پروکسي مستقيم گوگل', 'پروکسي پيدا نشد' );
			$return ['message'] = 'هيچ پراکسي اي يافت نشد ...';
		}
	} else {
		
		$exec = curl_exec ( $ch );
		$x = curl_error ( $ch );
	}
	 
	
	// verify valid google search reply
	if (trim ( $exec ) == '') {
		$return['message']='صفحه خالي گوگل';
		if (! $proxified)
			rankie_log_new('گوگل دايرکت', 'پاسخ خالي از Google '.$x);
	} else {
		
		if (stristr ( $exec, 'protect our users' ) || stristr ( $exec, 'answer/86640' )) {
			
			$return['message']='Google اين درخواست را مسدود کرد ... ';
			if (! $proxified)
				rankie_log_new('گوگل دايرکت', 'Google اگر شما مي خواهيد از روش مستقيم Google استفاده کنيد ، و captcha  را به ما نشان داد.');
		} else {
			
			if (stristr ( $exec, 'CaptchaRedirect' )) {
				
				$return['message']='گوگل از حل مسئله captcha پرسيد ... ';
				if (! $proxified)
					rankie_log_new('گوگل دايرکت', 'Google براي بدست آوردن نتايج درخواست حل يک captcha را داده است');
			} else {
				
				// good we have a reply
				if (stristr ( $exec, 'did not match any documents' )) {
					// no results for that term
					$return ['message'] = 'اصطلاح اصلاً نتيجه اي ندارد';
					rankie_log_new ( 'مستقيم گوگل', 'اصطلاح جستجو هيچ نتيجه اي ندارد' );
					rankie_update_rank ( $id, 0, '' );
					$return ['status'] = 'success';
				} else {
					// goo d news term may have results let's get links
					// <li class="g"><h3 class="r"><a href="
					
					// <li class\="g">
					//preg_match_all ( '/ class\="r"><a href\="(.*?)"/', $exec, $matches );
					preg_match_all ( '{<div class="g".*?<div class=".*?"><a href="(.*?)"}', $exec, $matches );
					
					
					$foundLinks = $matches [1];
					
					// print
					if ($debug)
						print_r ( $foundLinks );
					
					if (count ( $foundLinks ) == 0) {
						// no results apear here
						rankie_update_rank ( $id, 0, '' );
						$return ['status'] = 'success';
						$return['message']='پيوندي در صفحه گوگل برگشته يافت نشد';
						rankie_log_new('گوگل دايرکت', 'پيوندي يافت نشد');
					} else {
						// good news we have links
						$i = 0;
						foreach ( $foundLinks as $foundLink ) {
							
							$i ++;
							
							if (stristr ( $foundLink, '/url?' )) {
								
								$foundUrl_arr = explode ( '=', $foundLink );
								
								$foundUrl = $foundUrl_arr [1];
								
								$foundUrl_arr2 = explode ( '&amp;', $foundUrl );
								
								$finalUrl = $foundUrl_arr2 [0];
							} else {
								$finalUrl = $foundLink;
							}
							 
							
							// compare final url host with site host
							if (stristr ( $finalUrl, $keyword_site )) {
								// verify
								
								$parsedUrl = parse_url ( $finalUrl );
								$parsedHost = $parsedUrl ['host'];
								
								$parsedHost = preg_replace ( '/^www\./', '', $parsedHost );
								
								if (trim ( $parsedHost ) == trim ( $keyword_site )) {
									
									// Now same domain check if exact match
									if (trim ( $keyword_site_full ) != '') {
										
										// exact match let's match
										
										// clean original url
										$cleanOriginalUrl = preg_replace ( '{http[s]?://}', '', $keyword_site_full );
										$cleanOriginalUrl = preg_replace ( '{^www\.}', '', $cleanOriginalUrl );
										$cleanOriginalUrl = preg_replace ( '{/$}', '', $cleanOriginalUrl );
										
										// clean final url
										$cleanFinalUrl = preg_replace ( '{http[s]?://}', '', $finalUrl );
										$cleanFinalUrl = preg_replace ( '{^www\.}', '', $cleanFinalUrl );
										$cleanFinalUrl = preg_replace ( '{/$}', '', $cleanFinalUrl );
										
										if ($cleanFinalUrl != $cleanOriginalUrl)
											continue;
									}
									
									$return ['rank'] = $i;
									$return ['link'] = $finalUrl;
									$return ['status'] = 'success';
									
									rankie_update_rank ( $id, $i, $finalUrl );
									
									return $return;
								}
							}
						}
						
						// oh loop ended without a match let's update it to 0
						rankie_update_rank ( $id, 0, '', count ( $foundLinks ) );
						$return ['status'] = 'success';
					} // links found
				} // no match
			} // captcha
		} // protect our users
	} // trim content
	
	return $return;
}


/**
 * Curl exec follow
 * 
 * @param unknown $ch
 * @return mixed
 */
function rankie_curl_exec_follow(&$ch) {
	$max_redir = 5;
	
	for($i = 0; $i < $max_redir; $i ++) {
		
		$exec = curl_exec ( $ch );
		
		$info = curl_getinfo ( $ch );
		
		if ($info ['http_code'] == 301 || $info ['http_code'] == 302) {
			
			curl_setopt ( $ch, CURLOPT_URL, trim ( $info ['redirect_url'] ) );
			
			$exec = curl_exec ( $ch );
		} else {
			
			// no redirect just return
			break;
		}
	}
	
	return $exec;
}

/**
 * Rotate Proxies
 */
function rankie_rotate_proxyies($proxies, $currentProxy) {
	foreach ( $proxies as $proxy ) {
		if (trim ( $proxy ) != trim ( $currentProxy )) {
			$newProxies [] = $proxy;
		}
	}
	
	$newProxies [] = $currentProxy;
	
	update_option ( 'wp_rankie_proxies', implode ( "\n", $newProxies ) );
}

/**
 * Function : rankie_update_rank
 * 
 * @param unknown $id
 * @param unknown $rank
 * @param unknown $link
 */
function rankie_update_rank($id, $rank, $link, $linksCount = 0) {
	
	// ini
	$now = time ();
	global $wpdb;
	global $rankie_currentPage;
	
	// if currentPage exists update it
	if (is_numeric ( $rankie_currentPage )) {
		$currentPageUpdate = ',last_checked_page=' . $rankie_currentPage;
		
		// fix rank according to the page
		if ($rankie_currentPage > 1 && $rank != 0) {
			$rank = $rank + ($rankie_currentPage - 1) * 10;
		}
		
		$currentPageLogPart = ' at page ' . $rankie_currentPage;
	} else {
		$currentPageUpdate = '';
		$currentPageLogPart = '';
	}
	
	// update date last update to now
	$query = "update {$wpdb->prefix}rankie_keywords set keyword_rank = $rank , date_updated = '$now' $currentPageUpdate where keyword_id=$id";
	$wpdb->query ( $query );
	
	$logMessage = 'رتبه بندي کلمه کليدي  #'.$id.' با موفقيت در رتبه  '.$rank.' بروز شد.'.$currentPageLogPart;
	
	if ($rank == 0 && $linksCount != 0) {
		$logMessage.= " نتايج از".$linksCount. " خارج شده.";
	}
	
	rankie_log_new('بروزرساني رتبه', $logMessage );
	
	// add a new record if there is a record change
	$query = "SELECT * FROM {$wpdb->prefix}rankie_ranks  where keyword_id='$id' order by id DESC limit 1 ";
	$rows = $wpdb->get_results ( $query );
	
	$updaterank = false;
	
	if (count ( $rows ) != 0) {
		$row = $rows [0];
		$lastrank = $row->rank;
		
		if (trim ( $rank ) != trim ( $lastrank )) {
			
			// update rank
			$updaterank = true;
			
			// record a rank change
			if ($rank == 0) {
				// lost old rank i.e negative change
				$rank_change = $rank - $lastrank;
			} else {
				
				// current rank is not zero
				if ($lastrank != 0) {
					
					$rank_change = $lastrank - $rank;
				} else {
					
					// last rank =0
					$rank_change = $rank;
				}
			}
			
			if ($rank != 0 && $lastrank != 0) {
				
				$query = "insert into  {$wpdb->prefix}rankie_changes(keyword_id,rank_change) values ($id,$rank_change )";
				$wpdb->query ( $query );
			}
		}
	} else {
		
		if ($rank != 0) {
			// update rank
			$updaterank = true;
		}
	}
	
	if ($updaterank && $rank != 0) {
		// add a new rank record
		$query = "insert into  {$wpdb->prefix}rankie_ranks(keyword_id,rank ,rank_link) values ($id,$rank,'" . addslashes ( $link ) . "')";
		$wpdb->query ( $query );
	}
}

/**
 * Function : rankie_generate_report
 */
function rankie_generate_report($args) {
	global $wpdb;
	
	$month = $args ['month'];
	$year = $args ['year'];
	
	// site and group criteria
	$criteria = '';
	$chartTag = '';
	
	if (trim ( $args ['site'] ) != 'all') {
		$criteria = " and keyword_site = '{$args['site']}' ";
		$chartTag = $args ['site'] . ' ';
	}
	
	if (trim ( $args ['group'] ) != 'all') {
		$criteria = $criteria . " and keyword_group = '{$args['group']}' ";
		$chartTag .= '(' . $args ['group'] . ' group) ';
	}
	
	$chartTag.= ' رتبه ';
	
	// getting live ranks for current criteria top 3
	$query = " select count(*)  as count from {$wpdb->prefix}rankie_keywords where keyword_rank > 0 and keyword_rank < 4  $criteria  ";
	$rows = $wpdb->get_results ( $query );
	$row = $rows [0];
	$topThreeCount = ( int ) $row->count;
	
	// top 10
	$query = " select count(*)  as count from {$wpdb->prefix}rankie_keywords where keyword_rank > 0 and keyword_rank < 11  $criteria  ";
	$rows = $wpdb->get_results ( $query );
	$row = $rows [0];
	$topTenCount = ( int ) $row->count;
	
	// top 100
	$query = " select count(*)  as count from {$wpdb->prefix}rankie_keywords where keyword_rank > 0 and keyword_rank < 101  $criteria  ";
	$rows = $wpdb->get_results ( $query );
	$row = $rows [0];
	$topHunderedCount = ( int ) $row->count;
	
	// out rank =0
	$query = " select count(*)  as count from {$wpdb->prefix}rankie_keywords where keyword_rank = 0  or keyword_rank >100  $criteria  ";
	$rows = $wpdb->get_results ( $query );
	$row = $rows [0];
	$topOutRank = ( int ) $row->count;
	
	$summaryHtml ='<h3>خلاصه</h3><table class="widefat"> <thead><th>موقعيت</th> <th>تعداد کلمه کليدي</th></thead> <tbody> <tr><td> رتبه بالاي 3 </td><td>'.$topThreeCount.'</td></tr> <tr><td>رتبه بالاي 10</td><td>'.$topTenCount.'</td></tr> <tr><td>رتبه بالاي 100</td><td>'.$topHunderedCount.'</td></tr> <tr><td>رتبه پايين 100</td><td>'.$topOutRank.'</td></tr> </tbody> </table>';
	
	// get all ranking for keywords ordered by rank
	$query = " select * from {$wpdb->prefix}rankie_keywords where keyword_rank > 0   $criteria order by keyword_rank ASC ";
	$rows = $wpdb->get_results ( $query );
	
	$allRankedKeys = '<h3>همه رتبه هاي فعلي</h3><table class="widefat"> <thead> <th>کلمه کليدي</th>  <th>رتبه</th>  <th>دامنه</th>  <th>گروه</th>  </thead> <tbody> ';
	
	foreach ( $rows as $row ) {
		$tr = '<tr> <td>' . $row->keyword . '</td> <td>' . $row->keyword_rank . '</td><td>' . $row->keyword_site . '</td><td>' . $row->keyword_group . '</td></tr>';
		$allRankedKeys .= $tr;
	}
	
	$allRankedKeys .= '</tbody></table>';
	
	// DAILY REPORT
	if ($args ['type'] == 'day') {
		
		$chartTag .= ' در ' . $month . ' - ' . $year;
		
		// get rank changes by foreach day
		$query = "SELECT distinct( day(date) ) as single_day , sum(rank_change) as rank_change  , keyword_site , keyword_group  FROM `{$wpdb->prefix}rankie_changes` ,`{$wpdb->prefix}rankie_keywords`   where {$wpdb->prefix}rankie_changes.keyword_id =  {$wpdb->prefix}rankie_keywords.keyword_id  and   year(date) ='{$year}' and month(date) = '{$month}' $criteria group by single_day";
		
		// get dates that have rank change
		$rows = $wpdb->get_results ( $query );
		foreach ( $rows as $row ) {
			$recorded_ranks [$row->single_day] = $row->rank_change;
		}
		
		// getting dates in selected month
		$today = strtotime ( date ( "Y-m-d" ) );
		
		if (function_exists ( 'cal_days_in_month' )) {
			$num = cal_days_in_month ( CAL_GREGORIAN, $month, $year );
		} else {
			$num = rankie_days_in_month ( $month, $year );
		}
		
		// get days in current month
		for($i = 1; $i <= $num; $i ++) {
			
			$month_days [] = $i;
			$thisDate = strtotime ( $year . "-" . $month . "-" . $i );
			
			if ($thisDate == $today)
				break;
		}
		
		$starting_rank = 0;
		
		// get final records for all days in the month
		$final_ranks [] = array (
				'Day',
				'Rank' 
		);
		$final_ranks [] = array (
				0,
				0 
		);
		foreach ( $month_days as $month_day ) {
			
			if (isset ( $recorded_ranks [$month_day] )) {
				// got a new record
				$starting_rank = $starting_rank + $recorded_ranks [$month_day];
			}
			
			$final_ranks [] = array (
					$month_day,
					$starting_rank 
			);
		}
		
		// getting moving forward & backward keywords
		$moving_forware_html = '<br><h3>صعودي </h3><table class="widefat"><thead><tr><th>کلمه کليدي</th><th>رتبه فعلي</th><th>تغيير رتبه</th><th>نام دامنه</th><th>گروه</th> </tr></thead>';
		$moving_backward_html = '<br><h3>نزولي</h3><table class="widefat"><thead><tr><th>کلمه کليدي</th><th>رتبه فعلي</th><th>تغيير رتبه</th><th>نام دامنه</th><th>گروه</th> </tr></thead>';
		
		$query = "SELECT distinct( {$wpdb->prefix}rankie_changes.keyword_id ) as single_keyword , keyword , keyword_rank , sum(rank_change) as total_rank_change  , keyword_site , keyword_group  FROM `{$wpdb->prefix}rankie_changes` ,`{$wpdb->prefix}rankie_keywords`   where {$wpdb->prefix}rankie_changes.keyword_id =  {$wpdb->prefix}rankie_keywords.keyword_id  and   year(date) ='{$year}' and month(date) = '{$month}'  $criteria    group by single_keyword order by total_rank_change DESC";
		$rows = $wpdb->get_results ( $query );
		
		$printedKeys = array ();
		$positiveSum = 0;
		$negativeSum = 0;
		
		foreach ( $rows as $row ) {
			
			$printedKeys [] = $row->single_keyword;
			
			if ($row->total_rank_change > 0) {
				$tr = '<tr><td>' . $row->keyword . '</td><td>' . $row->keyword_rank . '</td><td>+' . $row->total_rank_change . '</td><td>' . $row->keyword_site . '</td><td>' . $row->keyword_group . '</td></tr>';
				$moving_forware_html .= $tr;
				$positiveSum = $positiveSum + $row->total_rank_change;
			} elseif($row->total_rank_change < 0) {
				$tr = '<tr><td>' . $row->keyword . '</td><td>' . $row->keyword_rank . '</td><td>' . $row->total_rank_change . '</td><td>' . $row->keyword_site . '</td><td>' . $row->keyword_group . '</td></tr>';
				$moving_backward_html .= $tr;
				$negativeSum = $negativeSum + $row->total_rank_change;
			}
		}
		
		$negativeSum = $negativeSum * - 1;
		
		$moving_forware_html .= '</table>';
		$moving_backward_html .= '</table>';
		
		$total_html = $moving_forware_html . $moving_backward_html . $summaryHtml . $allRankedKeys;
		
		$pie [0] = array (
				array (
						'UP',
						'Down' 
				),
				array (
						'UP',
						$positiveSum 
				),
				array (
						'Down',
						$negativeSum 
				) 
		);
		$pie [1] = array (
				array (
						'Position',
						'Keyword Count' 
				),
				array (
						'In top 3',
						$topThreeCount 
				),
				array (
						'in top 10',
						$topTenCount 
				),
				array (
						'in top 100',
						$topHunderedCount 
				),
				array (
						'not in top 100',
						$topOutRank 
				) 
		);
		
		return array (
				$final_ranks,
				$total_html,
				$pie,
				$chartTag 
		);
	} elseif ($args ['type'] == 'month') {
		
		$chartTag .= ' on ' . $year;
		
		$query = "SELECT distinct( month(date) ) as single_month , sum(rank_change) as rank_change  , keyword_site , keyword_group  FROM `{$wpdb->prefix}rankie_changes` ,`{$wpdb->prefix}rankie_keywords`   where {$wpdb->prefix}rankie_changes.keyword_id =  {$wpdb->prefix}rankie_keywords.keyword_id  and   year(date) ='{$year}'  $criteria group by single_month";
		$rows = $wpdb->get_results ( $query );
		
		foreach ( $rows as $row ) {
			$recorded_ranks [$row->single_month] = $row->rank_change;
		}
		
		// getting months in selected Year
		$tomonth = strtotime ( date ( "Y-m" ) );
		
		$num = 12;
		for($i = 1; $i <= $num; $i ++) {
			
			$year_months [] = $i;
			
			$thisDate = strtotime ( $year . "-" . $i );
			
			if ($thisDate == $tomonth)
				break;
		}
		
		$starting_rank = 0;
		
		// get final records
		$final_ranks [] = array (
				'Month',
				'Rank' 
		);
		$final_ranks [] = array (
				0,
				0 
		);
		foreach ( $year_months as $year_month ) {
			
			if (isset ( $recorded_ranks [$year_month] )) {
				// got a new record
				$starting_rank = $starting_rank + $recorded_ranks [$year_month];
			}
			
			$final_ranks [] = array (
					$year_month,
					$starting_rank 
			);
		}
		
		// getting moving forward & backward keywords
		$moving_forware_html = '<br><h3>صعودي </h3><table class="widefat"><thead><tr><th>کلمه کليدي</th><th>رتبه فعلي</th><th>تغيير رتبه</th><th>نام دامنه</th><th>گروه</th> </tr></thead>';
		$moving_backward_html = '<br><h3>نزولي </h3><table class="widefat"><thead><tr><th>کلمه کليدي</th><th>رتبه فعلي</th><th>تغيير رتبه</th><th>نام دامنه</th><th>گروه</th> </tr></thead>';
		
		$query = "SELECT distinct( {$wpdb->prefix}rankie_changes.keyword_id ) as single_keyword , keyword , keyword_rank , sum(rank_change) as total_rank_change  , keyword_site , keyword_group  FROM `{$wpdb->prefix}rankie_changes` ,`{$wpdb->prefix}rankie_keywords`   where {$wpdb->prefix}rankie_changes.keyword_id =  {$wpdb->prefix}rankie_keywords.keyword_id  and   year(date) ='{$year}'   $criteria    group by single_keyword order by total_rank_change DESC";
		$rows = $wpdb->get_results ( $query );
		
		$printedKeys = array ();
		$positiveSum = 0;
		$negativeSum = 0;
		
		foreach ( $rows as $row ) {
			
			$printedKeys [] = $row->single_keyword;
			
			if ($row->total_rank_change > 0) {
				$tr = '<tr><td>' . $row->keyword . '</td><td>' . $row->keyword_rank . '</td><td>+' . $row->total_rank_change . '</td><td>' . $row->keyword_site . '</td><td>' . $row->keyword_group . '</td></tr>';
				$moving_forware_html .= $tr;
				$positiveSum = $positiveSum + $row->total_rank_change;
			} else {
				$tr = '<tr><td>' . $row->keyword . '</td><td>' . $row->keyword_rank . '</td><td>' . $row->total_rank_change . '</td><td>' . $row->keyword_site . '</td><td>' . $row->keyword_group . '</td></tr>';
				$moving_backward_html .= $tr;
				$negativeSum = $negativeSum + $row->total_rank_change;
			}
		}
		
		$negativeSum = $negativeSum * - 1;
		
		$moving_forware_html .= '</table>';
		$moving_backward_html .= '</table>';
		
		$total_html = $moving_forware_html . $moving_backward_html . $summaryHtml . $allRankedKeys;
		
		$pie [0] = array (
				array (
						'UP',
						'Down' 
				),
				array (
						'UP',
						$positiveSum 
				),
				array (
						'Down',
						$negativeSum 
				) 
		);
		$pie [1] = array (
				array (
						'Position',
						'Keyword Count' 
				),
				array (
						'In top 3',
						$topThreeCount 
				),
				array (
						'in top 10',
						$topTenCount 
				),
				array (
						'in top 100',
						$topHunderedCount 
				),
				array (
						'not in top 100',
						$topOutRank 
				) 
		);
		
		return array (
				$final_ranks,
				$total_html,
				$pie,
				$chartTag 
		);
	} elseif ($args ['type'] == 'year') {
		
		$query = "SELECT distinct( year(date) ) as single_year , sum(rank_change) as rank_change  , keyword_site , keyword_group  FROM `{$wpdb->prefix}rankie_changes` ,`{$wpdb->prefix}rankie_keywords`   where {$wpdb->prefix}rankie_changes.keyword_id =  {$wpdb->prefix}rankie_keywords.keyword_id    $criteria group by single_year";
		$rows = $wpdb->get_results ( $query );
		
		$recorded_ranks = array ();
		foreach ( $rows as $row ) {
			$recorded_ranks [$row->single_year] = $row->rank_change;
		}
		
		$starting_rank = 0;
		
		// get final records
		$final_ranks [] = array (
				'Month',
				'Rank' 
		);
		$final_ranks [] = array (
				2012,
				0 
		);
		
		foreach ( $recorded_ranks as $key => $val ) {
			
			// got a new record
			$starting_rank = $starting_rank + $val;
			
			$final_ranks [] = array (
					$key,
					$val 
			);
		}
		
		// getting moving forward & backward keywords
		$moving_forware_html = '<br><h3>صعودي</h3><table class="widefat"><thead><tr><th>کلمه کليدي</th><th>رتبه فعلي</th><th>تغيير رتبه</th><th>نام دامنه</th><th>گروه</th> </tr></thead>';
		$moving_backward_html = '<br><h3>نزولي</h3><table class="widefat"><thead><tr><th>کلمه کليدي</th><th>رتبه فعلي</th><th>تغيير رتبه</th><th>نام دامنه</th><th>گروه</th> </tr></thead>';
		
		if (trim ( $criteria ) != '')
			$criteria = ' and ' . $criteria;
		$query = "SELECT distinct( {$wpdb->prefix}rankie_changes.keyword_id ) as single_keyword , keyword , keyword_rank , sum(rank_change) as total_rank_change  , keyword_site , keyword_group  FROM `{$wpdb->prefix}rankie_changes` ,`{$wpdb->prefix}rankie_keywords`   where {$wpdb->prefix}rankie_changes.keyword_id =  {$wpdb->prefix}rankie_keywords.keyword_id    $criteria    group by single_keyword order by total_rank_change DESC";
		$rows = $wpdb->get_results ( $query );
		
		$printedKeys = array ();
		$positiveSum = 0;
		$negativeSum = 0;
		
		foreach ( $rows as $row ) {
			
			$printedKeys [] = $row->single_keyword;
			
			if ($row->total_rank_change > 0) {
				$tr = '<tr><td>' . $row->keyword . '</td><td>' . $row->keyword_rank . '</td><td>+' . $row->total_rank_change . '</td><td>' . $row->keyword_site . '</td><td>' . $row->keyword_group . '</td></tr>';
				$moving_forware_html .= $tr;
				$positiveSum = $positiveSum + $row->total_rank_change;
			} else {
				$tr = '<tr><td>' . $row->keyword . '</td><td>' . $row->keyword_rank . '</td><td>' . $row->total_rank_change . '</td><td>' . $row->keyword_site . '</td><td>' . $row->keyword_group . '</td></tr>';
				$moving_backward_html .= $tr;
				$negativeSum = $negativeSum + $row->total_rank_change;
			}
		}
		
		$negativeSum = $negativeSum * - 1;
		
		$moving_forware_html .= '</table>';
		$moving_backward_html .= '</table>';
		
		$total_html = $moving_forware_html . $moving_backward_html . $summaryHtml . $allRankedKeys;
		
		$pie [0] = array (
				array (
						'UP',
						'Down' 
				),
				array (
						'UP',
						$positiveSum 
				),
				array (
						'Down',
						$negativeSum 
				) 
		);
		$pie [1] = array (
				array (
						'Position',
						'Keyword Count' 
				),
				array (
						'In top 3',
						$topThreeCount 
				),
				array (
						'in top 10',
						$topTenCount 
				),
				array (
						'in top 100',
						$topHunderedCount 
				),
				array (
						'not in top 100',
						$topOutRank 
				) 
		);
		
		return array (
				$final_ranks,
				$total_html,
				$pie,
				$chartTag 
		);
	}
}

/**
 * function random_letter: returns a random letter
 * 
 * @return string letter
 */
function rankie_random_letter() {
	$letters = 'a,A,b,B,c,C,d,D,e,E,f,F,g,G,h,H,i,I,j,J,k,K,l,L,m,M,n,N,o,O,p,P,q,Q,r,R,s,S,t,T,u,U,v,V,w,W,x,X,y,Y,z,Z';
	$lettersArr = explode ( ',', $letters );
	$randIndex = rand ( 0, count ( $lettersArr ) - 1 );
	return $lettersArr [$randIndex];
}

/*
 * days_in_month($month, $year)
 * Returns the number of days in a given month and year, taking into account leap years.
 *
 * $month: numeric month (integers 1-12)
 * $year: numeric year (any integer)
 *
 * Prec: $month is an integer between 1 and 12, inclusive, and $year is an integer.
 * Post: none
 */
// corrected by ben at sparkyb dot net
function rankie_days_in_month($month, $year) {
	// calculate number of days in a month
	return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
} 