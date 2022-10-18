<?php

$rankie_ch = curl_init();
curl_setopt($rankie_ch, CURLOPT_HEADER,0);
curl_setopt($rankie_ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($rankie_ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($rankie_ch, CURLOPT_TIMEOUT,20);
curl_setopt($rankie_ch, CURLOPT_REFERER, 'http://www.bing.com/');
curl_setopt($rankie_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8');
curl_setopt($rankie_ch, CURLOPT_MAXREDIRS, 5); // Good leeway for redirections.
curl_setopt($rankie_ch, CURLOPT_FOLLOWLOCATION, 1); // Many login forms redirect at least once.
curl_setopt($rankie_ch, CURLOPT_COOKIEJAR , "cookie.txt");

//curl get
$x='error';
$url='http://www.whatismyip.com/';

curl_setopt($rankie_ch, CURLOPT_HTTPGET, 0);
curl_setopt($rankie_ch, CURLOPT_URL, trim($url));

$exec=curl_exec($rankie_ch);
$x=curl_error($rankie_ch);

if(strpos($exec,'<span>Your IP</span>')){
	preg_match('{<span>Your IP</span>:(.*?)<}', $exec , $ip_matches);
	print_r($ip_matches[1]);
}else{
	
	echo $exec.$x;
}
exit;