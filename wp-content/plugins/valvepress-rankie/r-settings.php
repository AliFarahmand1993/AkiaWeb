<div class="wrap">
<form  method="post" novalidate="" autocomplete="off">
<?php

	//Table version check
	rankie_check_table_version();
 
		
	$rankie_licenseactive=get_option('wp_rankie_license_active','');
		//purchase check
		if(isset($_POST['wp_rankie_license']) && trim($_POST['wp_rankie_license']) != '' && trim($rankie_licenseactive) == '' ){
		
			//save it
			update_option('wp_rankie_license' , $_POST['wp_rankie_license'] );
		
			//activating
			//curl ini
			$rankie_ch = curl_init();
			curl_setopt($rankie_ch, CURLOPT_HEADER,0);
			curl_setopt($rankie_ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($rankie_ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($rankie_ch, CURLOPT_TIMEOUT,20);
			curl_setopt($rankie_ch, CURLOPT_REFERER, 'http://www.bing.com/');
			curl_setopt($rankie_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8');
			curl_setopt($rankie_ch, CURLOPT_MAXREDIRS, 5); // Good leeway for redirections.
			@curl_setopt($rankie_ch, CURLOPT_FOLLOWLOCATION, 1); // Many login forms redirect at least once.
			curl_setopt($rankie_ch, CURLOPT_COOKIEJAR , "cookie.txt");
		
			//curl get
			$x='error';
		
			//change domain ?
			$append='';
		
			if( isset($_POST['wp_rankie_options']) && in_array('OPT_CHANGE_DOMAIN', $_POST['wp_rankie_options']) ){
				$append='&changedomain=yes';
			}
		
			$url='https://deandev.com/license/index.php?itm=7605032&domain='.$_SERVER['HTTP_HOST'].'&purchase='.trim($_POST['wp_rankie_license']).$append;
			curl_setopt($rankie_ch, CURLOPT_HTTPGET, 1);
			curl_setopt($rankie_ch, CURLOPT_URL, trim($url));
			$exec=curl_exec($rankie_ch);
			$x=curl_error($rankie_ch);
				 
			$resback=$exec;
			
			if(trim($exec) == ''){
	
				$url='http://deandev-proxy.appspot.com/license/index.php?itm=7605032&domain='.$_SERVER['HTTP_HOST'].'&purchase='.trim($_POST['wp_rankie_license']).$append;
				
				curl_setopt($rankie_ch, CURLOPT_HTTPGET, 1);
				curl_setopt($rankie_ch, CURLOPT_URL, trim($url));
				
				$exec=curl_exec($rankie_ch);
				$x=curl_error($rankie_ch);
				
				$resback=$exec;
				
			}
			
			$resarr=json_decode($resback);
		
			  
			if(isset($resarr->message)){
				$wp_rankie_active_message=$resarr->message;
		
				//activate the plugin
				
		 		
				update_option('wp_rankie_license_active', 'active');
				update_option('wp_rankie_license_active_date', time());
				$rankie_licenseactive=get_option('wp_rankie_license_active','');
		
			}else{
			 	
				if(isset($resarr->error))
					$wp_rankie_active_error=$resarr->error;
			}
	
		}
 
 
	// SAVE DATA
	if (isset ( $_POST ['wp_rankie_google_gl'] )) {
	
		
		foreach ( $_POST as $key => $val ) {
			update_option ( $key, $val );
		}
		
		//reset the disablilty of whatmyserp may be a username and pass was added
		update_option('whatsmysert_disabled_till',time());
		
		echo '<div class="updated"><p>Changes saved</p></div>';
	}

	//INI
	
	require_once 'r-letters.php';
	
	$rankie_countries_codes="N,AF,AX,AL,DZ,AS,AD,AO,AI,AQ,AG,AR,AM,AW,AU,AT,AZ,BS,BH,BD,BB,BY,BE,BZ,BJ,BM,BT,BO,BQ,BA,BW,BV,BR,IO,BN,BG,BF,BI,KH,CM,CA,CV,KY,CF,TD,CL,CN,CX,CC,CO,KM,CG,CD,CK,CR,CI,HR,CU,CW,CY,CZ,DK,DJ,DM,DO,EC,EG,SV,GQ,ER,EE,ET,FK,FO,FJ,FI,FR,GF,PF,TF,GA,GM,GE,DE,GH,GI,GR,GL,GD,GP,GU,GT,GG,GN,GW,GY,HT,HM,VA,HN,HK,HU,IS,IN,ID,IR,IQ,IE,IM,IL,IT,JM,JP,JE,JO,KZ,KE,KI,KP,KR,KW,KG,LA,LV,LB,LS,LR,LY,LI,LT,LU,MO,MK,MG,MW,MY,MV,ML,MT,MH,MQ,MR,MU,YT,MX,FM,MD,MC,MN,ME,MS,MA,MZ,MM,NA,NR,NP,NL,NC,NZ,NI,NE,NG,NU,NF,MP,NO,OM,PK,PW,PS,PA,PG,PY,PE,PH,PN,PL,PT,PR,QA,RE,RO,RU,RW,BL,SH,KN,LC,MF,PM,VC,WS,SM,ST,SA,SN,RS,SC,SL,SG,SX,SK,SI,SB,SO,ZA,GS,SS,ES,LK,SD,SR,SJ,SZ,SE,CH,SY,TW,TJ,TZ,TH,TL,TG,TK,TO,TT,TN,TR,TM,TC,TV,UG,UA,AE,GB,US,UM,UY,UZ,VU,VE,VN,VG,VI,WF,EH,YE,ZM,ZW";
	$rankie_countries_names="International ,Afghanistan,Aland Islands !Åland Islands,Albania,Algeria,American Samoa,Andorra,Angola,Anguilla,Antarctica,Antigua and Barbuda,Argentina,Armenia,Aruba,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Barbados,Belarus,Belgium,Belize,Benin,Bermuda,Bhutan,Bolivia Plurinational State of,Bonaire Sint Eustatius and Saba,Bosnia and Herzegovina,Botswana,Bouvet Island,Brazil,British Indian Ocean Territory,Brunei Darussalam,Bulgaria,Burkina Faso,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Cayman Islands,Central African Republic,Chad,Chile,China,Christmas Island,Cocos (Keeling) Islands,Colombia,Comoros,Congo,Congo the Democratic Republic of the,Cook Islands,Costa Rica,Cote d'Ivoire !Côte d'Ivoire,Croatia,Cuba,Curacao !Curaçao,Cyprus,Czech Republic,Denmark,Djibouti,Dominica,Dominican Republic,Ecuador,Egypt,El Salvador,Equatorial Guinea,Eritrea,Estonia,Ethiopia,Falkland Islands (Malvinas),Faroe Islands,Fiji,Finland,France,French Guiana,French Polynesia,French Southern Territories,Gabon,Gambia,Georgia,Germany,Ghana,Gibraltar,Greece,Greenland,Grenada,Guadeloupe,Guam,Guatemala,Guernsey,Guinea,Guinea-Bissau,Guyana,Haiti,Heard Island and McDonald Islands,Holy See (Vatican City State),Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran Islamic Republic of,Iraq,Ireland,Isle of Man,Israel,Italy,Jamaica,Japan,Jersey,Jordan,Kazakhstan,Kenya,Kiribati,Korea Democratic People's Republic of,Korea Republic of,Kuwait,Kyrgyzstan,Lao People's Democratic Republic,Latvia,Lebanon,Lesotho,Liberia,Libya,Liechtenstein,Lithuania,Luxembourg,Macao,Macedonia the former Yugoslav Republic of,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Marshall Islands,Martinique,Mauritania,Mauritius,Mayotte,Mexico,Micronesia Federated States of,Moldova Republic of,Monaco,Mongolia,Montenegro,Montserrat,Morocco,Mozambique,Myanmar,Namibia,Nauru,Nepal,Netherlands,New Caledonia,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Northern Mariana Islands,Norway,Oman,Pakistan,Palau,Palestine State of,Panama,Papua New Guinea,Paraguay,Peru,Philippines,Pitcairn,Poland,Portugal,Puerto Rico,Qatar,Reunion !Réunion,Romania,Russian Federation,Rwanda,Saint Barthelemy !Saint Barthélemy,Saint Helena Ascension and Tristan da Cunha,Saint Kitts and Nevis,Saint Lucia,Saint Martin (French part),Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Senegal,Serbia,Seychelles,Sierra Leone,Singapore,Sint Maarten (Dutch part),Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,South Georgia and the South Sandwich Islands,South Sudan,Spain,Sri Lanka,Sudan,Suriname,Svalbard and Jan Mayen,Swaziland,Sweden,Switzerland,Syrian Arab Republic,Taiwan Province of China,Tajikistan,Tanzania United Republic of,Thailand,Timor-Leste,Togo,Tokelau,Tonga,Trinidad and Tobago,Tunisia,Turkey,Turkmenistan,Turks and Caicos Islands,Tuvalu,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States,United States Minor Outlying Islands,Uruguay,Uzbekistan,Vanuatu,Venezuela Bolivarian Republic of,Viet Nam,Virgin Islands British,Virgin Islands U.S.,Wallis and Futuna,Western Sahara,Yemen,Zambia,Zimbabwe";
	$rankie_domains="com,ad,ae,com.af,com.ag,com.ai,am,co.ao,com.ar,as,at,com.au,az,ba,com.bd,be,bf,bg,com.bh,bi,bj,com.bn,com.bo,com.br,bs,co.bw,by,com.bz,ca,cd,cf,cg,ch,ci,co.ck,cl,cm,cn,com.co,co.cr,com.cu,cv,cz,de,dj,dk,dm,com.do,dz,com.ec,ee,com.eg,es,com.et,fi,com.fj,fm,fr,ga,ge,gg,com.gh,com.gi,gl,gm,gp,gr,com.gt,gy,com.hk,hn,hr,ht,hu,co.id,ie,co.il,im,co.in,iq,is,it,je,com.jm,jo,co.jp,co.ke,com.kh,ki,kg,co.kr,com.kw,kz,la,com.lb,li,lk,co.ls,lt,lu,lv,com.ly,co.ma,md,me,mg,mk,ml,mn,ms,com.mt,mu,mv,mw,com.mx,com.my,co.mz,com.na,com.nf,com.ng,com.ni,ne,nl,no,com.np,nr,nu,co.nz,com.om,com.pa,com.pe,com.ph,com.pk,pl,pn,com.pr,ps,pt,com.py,com.qa,ro,ru,rw,com.sa,com.sb,sc,se,com.sg,sh,si,sk,com.sl,sn,so,sm,st,com.sv,td,tg,co.th,com.tj,tk,tl,tm,tn,to,com.tr,tt,com.tw,co.tz,com.ua,co.ug,co.uk,com.uy,co.uz,com.vc,co.ve,vg,co.vi,com.vn,vu,ws,rs,co.za,co.zm,co.zw,cat,com.cy,al";
	$rankie_countries_codes = array_filter( explode(',', $rankie_countries_codes) );
	$rankie_countries_names = array_filter( explode(',', $rankie_countries_names) );
	$rankie_domains_arr= array_filter(explode(',', $rankie_domains));
	
	$rankie_whatsmyserpnames = "Global (www.google.com),Afghanistan (www.google.com.af),American Samoa (www.google.as),Anguilla (www.google.com.ai),Antigua and Barbuda (www.google.com.ag),Argentina (www.google.com.ar),Armenia (www.google.am),Australia (www.google.com.au),Austria (www.google.at),Azerbaijan (www.google.az),Bangladesh (www.google.com.bd),Belgium (www.google.be),Belize (www.google.com.bz),Bolivia (www.google.com.bo),Bosnia &amp; Herzegovina (www.google.ba),Brasil (www.google.com.br),British Virgin Islands (www.google.vg),Bulgaria (www.google.bg),Canada (www.google.ca),Chile (www.google.cl),Colombia (www.google.com.co),Croatia (www.google.hr),Cuba (www.google.com.cu),Czech (www.google.cz),Denmark (www.google.dk),Dominican Republic (www.google.com.do),Ecuador (www.google.com.ec),El Salvador (www.google.com.sv),Finland (www.google.fi),France (www.google.fr),Germany (www.google.de),Greece (www.google.gr),Guatemala (www.google.com.gt),Honduras (www.google.com.hn),Hong Kong (www.google.com.hk),Hungary (www.google.hu),India (www.google.co.in),Indonesia (www.google.co.id),Ireland (www.google.ie),Israel (www.google.co.il),Italia (www.google.it),Japan (www.google.co.jp),Kenya (www.google.co.ke),Lithuania (www.google.lt),Malaysia (www.google.com.my),Mexico (www.google.com.mx),Netherlands (www.google.nl),New Zealand (www.google.co.nz),Nicaragua (www.google.com.ni),Norway (www.google.no),Pakistan (www.google.com.pk),Panama (www.google.com.pa),Paraguay (www.google.com.py),Philippines (www.google.com.ph),Poland (www.google.pl),Portugal (www.google.pt),Puerto Rico (www.google.com.pr),Romania (www.google.ro),Russia (www.google.ru),Samoa (www.google.ws),Saudi Arabia (www.google.com.sa),Singapore (www.google.com.sg),Slovak (www.google.sk),Slovenia (www.google.si),South Africa (www.google.co.za),South Korea (www.google.co.kr),Spain (www.google.es),Sweden (www.google.se),Switzerland (www.google.ch),Taiwan (www.google.com.tw),Thailand (www.google.co.th),Turkey (www.google.com.tr),UAE (www.google.ae),United Kingdom (www.google.co.uk),Uruguay (www.google.com.uy),Vanuatu (www.google.vu),VietNam (www.google.com.vn),Virgin Islands (www.google.co.vi),Yugoslavia (www.google.co.yu),Zambia (www.google.co.zm),Zimbabwe (www.google.co.zw),Cyprus (www.google.com.cy)";
	$rankie_whatsmyserpurls  = "www.google.com,www.google.com.af,www.google.as,www.google.com.ai,www.google.com.ag,www.google.com.ar,www.google.am,www.google.com.au,www.google.at,www.google.az,www.google.com.bd,www.google.be,www.google.com.bz,www.google.com.bo,www.google.ba,www.google.com.br,www.google.vg,www.google.bg,www.google.ca,www.google.cl,www.google.com.co,www.google.hr,www.google.com.cu,www.google.cz,www.google.dk,www.google.com.do,www.google.com.ec,www.google.com.sv,www.google.fi,www.google.fr,www.google.de,www.google.gr,www.google.com.gt,www.google.com.hn,www.google.com.hk,www.google.hu,www.google.co.in,www.google.co.id,www.google.ie,www.google.co.il,www.google.it,www.google.co.jp,www.google.co.ke,www.google.lt,www.google.com.my,www.google.com.mx,www.google.nl,www.google.co.nz,www.google.com.ni,www.google.no,www.google.com.pk,www.google.com.pa,www.google.com.py,www.google.com.ph,www.google.pl,www.google.pt,www.google.com.pr,www.google.ro,www.google.ru,www.google.ws,www.google.com.sa,www.google.com.sg,www.google.sk,www.google.si,www.google.co.za,www.google.co.kr,www.google.es,www.google.se,www.google.ch,www.google.com.tw,www.google.co.th,www.google.com.tr,www.google.ae,www.google.co.uk,www.google.com.uy,www.google.vu,www.google.com.vn,www.google.co.vi,www.google.co.yu,www.google.co.zm,www.google.co.zw,www.google.com.cy" ;
	
	$rankie_whatsmyserpnames = array_filter( explode(',', $rankie_whatsmyserpnames) );
	$rankie_whatsmyserpurls = array_filter( explode(',', $rankie_whatsmyserpurls) );
	
	$rankie_google_gl=get_option('wp_rankie_google_gl','N');

	
	$rankie_whatsmyserp_g = get_option('wp_rankie_whatsmyserp_g','www.google.com');
	
	$rankie_ezmlm_gl = get_option('wp_rankie_ezmlm_gl','com');
	
	$rankie_method=get_option('wp_rankie_method','whatsmyserp');
	
	$rankie_proxies=get_option('wp_rankie_proxies','');
	
	$rankie_whatsmysert_pass = get_option('whatsmysert_pass' , '');
	$rankie_whatsmysert_user =  get_option('whatsmysert_user' , '');
		
	$rankie_options=get_option('wp_rankie_options' , array('OPT_AUTO_UPDATE',));
	
	$rankie_research_gl = get_option('wp_rankie_research_gl' , 'google.com');
	$rankie_mail = get_option('wp_rankie_mail' , '');
	$rankie_screen= get_option('wp_rankie_screen' , '200');
	$rankie_lat= get_option('wp_rankie_lat' , '');
	$rankie_long= get_option('wp_rankie_long' , '');

	$rankie_wp_keyword_tool_alphabets = get_option('wp_keyword_tool_alphabets' , 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z');

	// Disconnect codes and countries names
	$rankie_disconnect_c = get_option('wp_rankie_disconnect_c','US');
	$rankie_disconnect_codes = "AF,AL,DZ,AS,AD,AO,AI,AG,AR,AM,0AC,AU,AT,AZ,BS,BH,BD,BY,BE,BZ,BJ,BT,BO,BA,BW,BR,IO,VG,BN,BG,BF,MM,BI,KH,CM,CA,CV,0CA,CF,TD,CL,CN,CC,CO,CK,CR,HR,CU,CY,CZ,CD,DK,DJ,DM,DO,EC,EG,SV,EE,ET,FM,FJ,FI,FR,GF,GA,GM,GE,DE,GH,GI,GR,GL,GP,GT,0GG,GY,HT,HN,HK,HU,IS,IN,ID,IR,IQ,IE,IM,IL,IT,CI,JM,JP,0JE,JO,KZ,KE,KI,KW,KG,LA,LV,LB,LS,LY,LI,LT,LU,MK,MG,MW,MY,MV,ML,MT,MU,MX,MD,MN,ME,MS,MA,MZ,NA,NR,NP,NL,NZ,NI,NE,NG,NU,NF,NO,OM,PK,PS,PA,PG,PY,PE,PH,PN,PL,PT,PR,QA,CG,RO,RU,RW,SH,LC,VC,WS,SM,ST,SA,SN,RS,SC,SL,SG,SK,SI,SB,SO,ZA,KR,ES,LK,SE,CH,TW,TJ,TZ,TH,TL,TG,TK,TO,TT,TN,TR,TM,UG,UA,AE,UK,US,VI,UY,UZ,VU,VE,VN,ZM,ZW";
	$rankie_disconnect_countries = "Afghanistan,Albania,Algeria,American Samoa,Andorra,Angola,Anguilla,Antigua and Barbuda,Argentina,Armenia,Ascension Island,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Belarus,Belgium,Belize,Benin,Bhutan,Bolivia,Bosnia and Herzegovina,Botswana,Brazil,British Indian Ocean Territory,British Virgin Islands,Brunei,Bulgaria,Burkina Faso,Burma,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Catalonia Catalan Countries,Central African Republic,Chad,Chile,China,Cocos (Keeling) Islands,Colombia,Cook Islands,Costa Rica,Croatia,Cuba,Cyprus,Czech Republic,Democratic Republic of the Congo,Denmark,Djibouti,Dominica,Dominican Republic,Ecuador,Egypt,El Salvador,Estonia,Ethiopia,Federated States of Micronesia,Fiji,Finland,France,French Guiana,Gabon,Gambia,Georgia,Germany,Ghana,Gibraltar,Greece,Greenland,Guadeloupe,Guatemala,Guernsey,Guyana,Haiti,Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,Isle of Man,Israel,Italy,Ivory Coast,Jamaica,Japan,Jersey,Jordan,Kazakhstan,Kenya,Kiribati,Kuwait,Kyrgyzstan,Laos,Latvia,Lebanon,Lesotho,Libya,Liechtenstein,Lithuania,Luxembourg,Macedonia,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Mauritius,Mexico,Moldova,Mongolia,Montenegro,Montserrat,Morocco,Mozambique,Namibia,Nauru,Nepal,Netherlands,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Norway,Oman,Pakistan,Palestine,Panama,Papua New Guinea,Paraguay,Peru,Philippines,Pitcairn Islands,Poland,Portugal,Puerto Rico,Qatar,Republic of the Congo,Romania,Russia,Rwanda,Saint Helena,Saint Lucia,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Senegal,Serbia,Seychelles,Sierra Leone,Singapore,Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,South Korea,Spain,Sri Lanka,Sweden,Switzerland,Taiwan,Tajikistan,Tanzania,Thailand,Timor-Leste,Togo,Tokelau,Tonga,Trinidad and Tobago,Tunisia,Turkey,Turkmenistan,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States,United States Virgin Islands,Uruguay,Uzbekistan,Vanuatu,Venezuela,Vietnam,Zambia,Zimbabwe";
	$rankie_disconnect_codesArr = explode(',', $rankie_disconnect_codes);
	$rankie_disconnect_countriesArr = explode(',', $rankie_disconnect_countries);

	// Custom Search
	$rankie_googlecustom_id = get_option('wp_rankie_googlecustom_id','013156076200156289477:aavh3lmtysa');
	$rankie_googlecustom_key = get_option('wp_rankie_googlecustom_key','');
	
	
	// Update Interval
	$rankie_update_interval = get_option('wp_rankie_update_interval','1');
	
	
?>

<h2>
	تنظیمات رنکی <input type="submit" class="button-primary" value="ذخیره تغییرات" name="save">
</h2>

<br class="clear">

<div class="metabox-holder columns-1" id="dashboard-widgets">
	<div style="width:100%!important" class="postbox-container" id="postbox-container-1" >
	
		<div class="meta-box-sortables ui-sortable" id="normal-sortables">
		
			<div class="postbox">
				<div title="Click to toggle" class="handlediv">
					<br>
				</div>
				<h2 class="hndle">
					<span>تنظیمات پایه ای</span>
				</h2>
				<div class="inside">
					<table class="form-table">
						<tbody>
							
							<tr>
								
								<th scope="row"><label> روش رتبه بندی   </label></th>
								
								<td>
								  
								 		
								 		<div id="field-wp-rankie-method-container" class="field f_100" >
								 			 
								 			<select name="wp_rankie_method" id="wp_rankie_method">

								 				<option    value="googledirect"  <?php rankie_opt_selected('googledirect',$rankie_method) ?> >مستقیما از گوگل ( در 100 رتبه برتر) (دقیق ترین حالت)</option>
								 				<option    value="googlecustom"  <?php rankie_opt_selected('googlecustom',$rankie_method) ?> >API جستجوی سفارشی Google (در 10 تا 100 مورد برتر)</option>
								 				
								 			</select>
								 			
								 		</div>
								 		
								 		<div class="description">روشی را که افزونه برای بدست آوردن رتبه برای کلمه کلیدی شما استفاده می کند ، انتخاب کنید</div>
								 		
								  
								</td>
							</tr>
							
						 
							
							<tr>
								
								<th scope="row"><label for="field-wp_rankie_user"> کشور گوگل </label></th>
								
								<td>
								 
								 	<div class="google_selector fsecure  hidden" >
								 		
								 		این روش فقط در Google.com کار می کند
								 		
								 	</div>
								 
								 	<select  class="google_selector whatsmyserp  hidden" name="wp_rankie_whatsmyserp_g" >
								 		  
										 
										 <?php 
										 $n=0;
											 
										 	foreach ($rankie_whatsmyserpurls as $rankie_country_code ){
										 		?>
										 			<option  value="<?php echo $rankie_country_code ?>"  <?php rankie_opt_selected($rankie_country_code,$rankie_whatsmyserp_g) ?> ><?php echo $rankie_whatsmyserpnames[$n] ?></option>
										 		<?php  
												$n++;
										 	}
										 	
										 ?>
								 	</select>
								 	
								 	
								 	
								 	<select class="google_selector ajax hidden"  name="wp_rankie_google_gl" >
								 		  
										 
										 <?php 
										 $n=0;
											 
										 	foreach ($rankie_countries_codes as $rankie_country_code ){
										 		?>
										 			<option  value="<?php echo $rankie_country_code ?>"  <?php rankie_opt_selected($rankie_country_code,$rankie_google_gl) ?> ><?php echo $rankie_countries_names[$n] ?></option>
										 		<?php  
												$n++;
										 	}
										 	
										 ?>
								 	</select>
								 	
								 	<select class="google_selector googledirect kproxy ezmlm googlecustom hidden"  name="wp_rankie_ezmlm_gl" >
								 		  
										 
										 <?php 
										 $n=0;
											 
										 	foreach ($rankie_domains_arr as $rankie_country_code ){
										 		?>
										 			<option  value="<?php echo $rankie_country_code ?>"  <?php rankie_opt_selected($rankie_country_code,$rankie_ezmlm_gl) ?> ><?php echo 'google.'.$rankie_country_code ?></option>
										 		<?php  
												$n++;
										 	}
										 	
										 ?>
								 	</select>
								 	
								 	<select class="google_selector  disconnect hidden"  name="wp_rankie_disconnect_c" >
								 		  
										 <?php 
										 $n=0;
											 
										 	foreach ($rankie_disconnect_codesArr as $rankie_country_code ){
										 		?>
										 			<option  value="<?php echo $rankie_country_code ?>"  <?php rankie_opt_selected($rankie_country_code,$rankie_disconnect_c) ?> ><?php echo $rankie_disconnect_countriesArr[$n] ; ?></option>
										 		<?php  
												$n++;
										 	}
										 	
										 ?>
								 	</select>
								 	
								 	
								 	
								</td>
							</tr>
							
							<tr class="google_selector googledirect  hidden" >
								
								<th scope="row"><label>جستجو از یک مکان خاص</label></th>
								
								<td>
								
								<div class="field f_100" >
						 			     		عرض جغرافیایی  <input width="120" class="" value="<?php echo $wp_rankie_lat ?>" name="rankie_lat"   type="text">
						 			     		طول جغرافیایی  <input width="120" class="" value="<?php echo $wp_rankie_long ?>" name="rankie_long"   type="text">
						 		</div>
								  
           
								 	<div class="description">این آموزش راجع به<a href="http://valvepress.com/how-to-make-rankie-search-google-from-a-custom-location/" target="_blank" > نحوه بدست آوردن این مقادیر و تنظیم مکان سفارشی </a>  را بررسی کنید</div>
								</td>
							</tr>
							
							
							
							<tr class="google_selector googlecustom fsecure hidden">
								
								<th scope="row"><label>صفحه بندی را غیرفعال کنید؟   </label></th>
								 
								<td>
								  
								   <div class="field f_100" >
						 			     
						 			     <div class="option clearfix">
						 			         <input     name="wp_rankie_options[]"  value="OPT_PAGINATE" type="checkbox">
						 			         <label>به طور پیش فرض افزونه صفحه اول را بررسی می کند ، اگر سایتی پیدا نشد ، افزونه فردا صفحه 2 را بررسی می کند و غیره. اگر سایت پیدا کند ، آخرین صفحه را که روزانه در سایت پیدا می شود ، بررسی می کند تا اینکه دیگر یافت نشود ، دوباره از صفحه 1 بررسی می کند.</label>     
						 			     </div>
						 			     
						 			</div> 
								 	 
								</td>
							</tr>	
							
							
							<tr class="google_selector googlecustom hidden">
								
								<th scope="row"><label>شناسه موتور جستجوی سفارشی    </label></th>
								 
								<td>
								  
								  <div   class="field f_100" >
						 			     <div class="option clearfix">
						 			     	 
						 			     	<div class="field f_100" >
						 			     		<input class="widefat" value="<?php echo $rankie_googlecustom_id  ?>" name="wp_rankie_googlecustom_id"   type="text">
						 			     	</div>
						 			     	
						 			     	<div class="description">
						 			     		از این "013156076200156289477: aavh3lmtysa" استفاده کنید یا اگر دوست دارید خود را <a href="https://developers.google.com/custom-search/docs/tutorial/creatingcse"/>ایجاد کنید </a> (توصیه نمی شود)
						 			     	</div>
						 			     	   
						 			     </div>
						 			</div> 
								 	 
								</td>
							</tr>	
							
							<tr class="google_selector googlecustom hidden">
								
								<th scope="row"><label>کلید جستجوی Google کلید API    </label></th>
								 
								<td>
								  
								  <div   class="field f_100" >
						 			     <div class="option clearfix">
						 			     	 
						 			     	<div class="field f_100" >
						 			     		<input class="widefat" value="<?php echo $rankie_googlecustom_key  ?>" name="wp_rankie_googlecustom_key"   type="text">
						 			     	</div>
						 			     	
						 			     	<div class="description">
						 			     		برای تهیه یک کلید api به این <a href="http://valvepress.com/how-to-create-a-google-custom-search-api-key/">آموزش </a> مراجعه کنید.
						 			     		<br>* یک کلید 100 درخواست / روز را در اختیار ما قرار می دهد
						 			     		<br>* می توانید چندین کلید اضافه کرده و آنها را با کاما از هم جدا کنید اما هر کلید باید از یک حساب Google متفاوت باشد.
																																	  
						 			     	</div>
						 			     	   
						 			     </div>
						 			</div> 
								 	 
								</td>
							</tr>
							  
							<tr>
								
								<th scope="row"><label> فاصله زمانی بروزرسانی کلمات کلیدی </label></th>
								
								<td>
								 	<select  name="wp_rankie_update_interval" >
								 		 
								 		  <option  value="1"  <?php rankie_opt_selected('1',$rankie_update_interval) ?> >روزانه</option>
								 		  <option  value="2"  <?php rankie_opt_selected('2',$rankie_update_interval) ?> >هر دو روز یک بار</option>
								 		  <option  value="3"  <?php rankie_opt_selected('3',$rankie_update_interval) ?> >هر سه روز یک بار</option>
								 		  <option  value="4"  <?php rankie_opt_selected('4',$rankie_update_interval) ?> >هر چهار روز یک بار</option>
								 		  <option  value="5"  <?php rankie_opt_selected('5',$rankie_update_interval) ?> >هر پنج روز یک بار</option>
								 		  <option  value="6"  <?php rankie_opt_selected('6',$rankie_update_interval) ?> >هر شش روز یک بار</option>
								 		  <option  value="7"  <?php rankie_opt_selected('7',$rankie_update_interval) ?> >هر هفت روز یک بار</option>
								 	
								 	</select>
								 	<div class="description">* این افزونه هر 6 دقیقه یک کلمه کلیدی را به روز می کند ، بنابراین برای بروزرسانی "Daily" ، مرتباً کلمات کلیدی روزانه به روز می شوند و افزونه می تواند تا 240 کلمه کلیدی را بروزرسانی کند
										<br>* برای بازه زمانی به روزرسانی شده "دو روز" ، این افزونه هر دو روز یکبار رتبه کلمه کلیدی را بروزرسانی می کند و می تواند تا 488 کلمه کلیدی را بروزرسانی کند
										<br>* 3 روز تا 720 کلمه کلیدی.
										<br>* 4 روز تا 960 کلمه کلیدی.
										<br>* 5 روز تا 1200 کلمه کلیدی.
										<br>* 6 روز تا 1440 کلمه کلیدی.
										<br>* 7 روز تا 1680 کلمه کلیدی. </div>	
								</td>
							</tr>

							<tr>
								<th scope="row"><label> گوگل جستجوی کلمات کلمات  </label></th>
								<td>
								 	<select  name="wp_rankie_research_gl" >
										 <?php 
										 $n=0;
											 
										 	foreach ($rankie_domains_arr as $rankie_country_code ){
										 		?>
										 			<option  value="<?php echo 'google.'.$rankie_country_code ?>"  <?php rankie_opt_selected('google.'.$rankie_country_code,$rankie_research_gl) ?> ><?php echo 'google.'.$rankie_country_code ?></option>
										 		<?php  
												$n++;
										 	}
										 	
										 ?>
								 	</select>	
								 		
								 		
								</td>
							</tr>
							
					 
							
							<tr>
								
								<th scope="row"><label> حروف زبان جستجوی گوگل </label></th>
								
								<td>
								 	
								 	  <div style="margin-top:10px;position:relative;display: flex;flex-direction: row-reverse;justify-content: flex-end;">
										
										<div style="width:33%" id="field-wp_keyword_tool_alphabets-container" class="field f_100" >
											 
											  <textarea style="width:100%; height: 125px; margin: 10px 0px;" rows="5" cols="20" name="wp_keyword_tool_alphabets" id="field-wp_keyword_tool_alphabets"><?php echo $$rankie_wp_keyword_tool_alphabets ?></textarea>
										</div> 
										
										
										<div style="width:30%; margin-left:10px;"  id="field-language-container" class="field f_100" >
											
											<select style="width:100%;height:125px" name="language" size="6" id="field1zz">
												 
											</select>
										</div>
								 		
								</td>
							</tr>							
							
							<tr>
								
								<th scope="row"><label> گرفتن عبارات جستجوی گوگل   </label></th>
								
								<input type="hidden" value="none" name="wp_rankie_options[]" >
								
								<td>
								  
								 		 
						 			<div id="field-wp_rankie_options-container" class="field f_100" >
						 			     <div class="option clearfix">
						 			     	
						 			         <input     name="wp_rankie_options[]" id="field-wp_rankie_options-1" value="OPT_CATCH" type="checkbox">
						 			         <label>گرفتن عبارات جستجوی از این سایت و اضافه کردن ردیابی رتبه گوگل</label>     
						 			     </div>
						 			</div> 
								 	
								</td>
							</tr>
							
							<tr>
								
								<th scope="row"><label> بروزرسانی خودکار رتبه؟   </label></th>
								 
								<td>
								  
								 		 
						 			<div  class="field f_100" >
						 			     <div class="option clearfix">
						 			     	
						 			         <input     name="wp_rankie_options[]"  value="OPT_AUTO_UPDATE" type="checkbox">
						 			         <label>برای بروز رسانی خودکار رتبه کلمات کلیدی علامت بزنید.</label>     
						 			     </div>
						 			</div> 
								 	
								</td>
							</tr>
							
							<tr>
								
								<th scope="row"><label>استفاده از کرون جاب خارجی   </label></th>
								 
								<td>
								  
								 		 
						 			<div  class="field f_100" >
						 			     <div class="option clearfix">
						 			     	
						 			         <input     name="wp_rankie_options[]"  value="OPT_EXTERNAL" type="checkbox">
						 			         <label>اگر از یک کار cron خارجی استفاده نکنید ، این افزونه از WordPress Cron داخلی استفاده شده توسط بازدید کنندگان سایت استفاده می کند</label>          
						 			     </div>
						 			</div> 
								 	
								</td>
							</tr>

							<tr>
								
								<th scope="row"><label>دستور کردن کرون جاب  <br><a target="_blank" href="<?php echo plugins_url('r-cron.php',__FILE__) ?>">(اجرا کن)</a>  </label></th>
								 
								<td>
								  
								 		 
						 			<div  class="field f_100" >
						 			     <div class="option clearfix">
						 			     	
						 			         <?php echo 'curl '.plugins_url('r-cron.php',__FILE__) ?>
						 			         <div style="padding-top:10px" class="description">این دستور را در crontab میزبان خود بگزارید. اگر نمی دانید چگونه آن را تنظیم کنید ، به اسناد مراجعه کنید.</div>          
						 			     </div>
						 			</div> 
								 	
								</td>
							</tr>
							
							<tr>
								
								<th scope="row"><label>دستور جایگزین کرون جاب </label></th>
								 
								<td>
								  
								 		 
						 			<div  class="field f_100" >
						 			     <div class="option clearfix">
						 			     	
						 			         <?php echo 'php '. dirname(__FILE__) .   '/r-cron.php'  ?>
						 			         <div style="padding-top:10px" class="description">اگر دستور اولی کار نکرد این دستور کار کنید.</div>     
						 			     </div>
						 			</div> 
								 	
								</td>
							</tr>
							<tr>
								
								<th scope="row"><label>استفاده از پراکسی</label></th>
								 
								<td>
								  
								  <div  class="field f_100" >
						 			     <div class="option clearfix">
						 			     	
						 			         <input     name="wp_rankie_options[]" id="field-wp_rankie_options-1" value="OPT_PROXY" type="checkbox">
						 			         <label>با استفاده از پراکسی ها فعال شوید.</label>     
						 			     </div>
						 			</div> 
								 	 
								</td>
							</tr>
							
							<tr>
								
								<th scope="row"><label>لیست پراکسی   </label></th>
								 
								<td>
								  
								  <div   class="field f_100" >
						 			     <div class="option clearfix">
						 			     	 
						 			     	<div id="field-wp_rankie_proxies-container" class="field f_100" >
						 			     		 
						 			     		<textarea class="widefat" rows="5" cols="20" name="wp_rankie_proxies" id="field-wp_rankie_proxies"><?php echo $rankie_proxies ?></textarea>
						 			     	</div>
						 			     	
						 			     	<div class="description">
						 			     	
						 			     	اطمینان حاصل کنید که پروکسی های شما با پورت 80 یا 8080 هستند که برای ارتباط با اکثر سرورها باز هستند یا از هر پستی که برای اتصال روی سرور شما باز باشد استفاده می کنند. 
						 			     	 <br> فرمت:   ip:port 
						 			     	 <br> فرمت دیگر : ip:port:username:password   for proxies with authentication
						 			     	 <br> هرپروکسی در یک خط
						 			     	 <br> *بعضی از سرویس های پروکسی نیاز به بررسی آیپی سرور برای احراز هویت هستند <a target="_blank" href="<?php echo plugins_url('wp-rankie/show_ip.php') ?>"><strong>اینجا کلیک کنید</strong></a> تا آی پی سرور خود را بفهمید</strong>
							 			     <br> *ار پروکسی های عمومی استفاده نکنید
						 			     	
						 			     	</div>
						 			     	   
						 			     </div>
						 			</div> 
								 	 
								</td>
							</tr>
							
							
						
							<tr>
								
								<th scope="row"><label>گزارش روزانه به ایمیل :   </label></th>
								 
								<td>
								  
								  <div   class="field f_100" >
						 			     <div class="option clearfix">
						 			     	 
						 			     	<div class="field f_100" >
						 			     		<input id="wp_rankie_mailtxt" class="widefat" value="<?php echo $rankie_mail  ?>" name="wp_rankie_mail"   type="text">
						 			     		<button class="wp_rankie_test">ارسال یک نمونه</button>
						 			     		
						 			     		<?php 
						 			     		
						 			     		 	$wp_rankie_last_mail = get_option('wp_rankie_last_mail' , '');
						 			     		 	
						 			     		 	if(trim($wp_rankie_last_mail) != ''){
						 			     		 		echo '<br><i>*  Last report mail was sent at day '.$wp_rankie_last_mail.'</i>';
						 			     		 	}
						 			     		 	
						 			     		
						 			     		?>
						 			     	</div>
						 			     	
						 			     	<div class="description">
						 			     		اگر دوست ندارید تغییرات روزانه ثبت شده برای موقعیت های کلمات کلیدی را دریافت کنید ، خالی بگذارید
						 			     	</div>
						 			     	   
						 			     </div>
						 			</div> 
								 	 
								</td>
							</tr>
							
							<tr>
								
								<th scope="row"><label>ارتفاع ردیف کلمات کلیدی در پیشخوان </label></th>
								 
								<td>
								  
								  <div   class="field f_100" >
						 			     <div class="option clearfix">
						 			     	 
						 			     	<div class="field f_100" >
						 			     		<input class="widefat" value="<?php echo $rankie_screen  ?>" name="wp_rankie_screen"   type="text">
						 			     	</div>
						 			     	
						 			     	<div class="description">
						 			     		مقدار این عدد را افزایش دهید تا نمایش کلمات کلیدی را اسکرول نباشد.
						 			     	</div>
						 			     	   
						 			     </div>
						 			</div> 
								 	 
								</td>
							</tr>
						
						  

						</tbody>
					</table>
				</div>
			</div>
			 
			 
	<input style="margin-left:10px" type="submit" name="save" value="ذخیره تغییرات" class="button-primary">
	
</div>
</form>
</div><!-- wrap -->
 <script type="text/javascript">
    var $vals = '<?php echo  implode('|', $rankie_options) ?>';
    $val_arr = $vals.split('|');
    jQuery('input:checkbox').removeAttr('checked');
    jQuery.each($val_arr, function (index, value) {
    	 "use strict";
        if (value != '') {
            jQuery('input:checkbox[value="' + value + '"]').prop('checked', true); 
        }
    });

     
    var alllangs= <?php echo json_encode($allletters) ?>;
    jQuery.each(alllangs,function(k,v){
    	 "use strict";
        
        jQuery('#field1zz').append('<option  value="'+ k +'" >'+k+'</option>')  ;

    });
        
    jQuery('.wp_rankie_test').on( 'click' , function(){
    	 "use strict";

    	jQuery.ajax({
            url: ajaxurl,
            type: 'POST',

            data: {
                action: 'send_test_mail',
                mail: jQuery('#wp_rankie_mailtxt').val()
            },
            
            success:function(data){
				alert(data);
				return false;
            }
    	});

        return false;	
		
    }); 

    </script>
 
	
 