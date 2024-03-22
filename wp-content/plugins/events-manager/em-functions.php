<?php

if(!function_exists('em_paginate')){ //overridable e.g. in you mu-plugins folder.
/**
 * Takes a few params and determines a pagination link structure
 * @param string $link
 * @param int $total
 * @param int $limit
 * @param int $page
 * @param array $data If supplied and EM_USE_DATA_ATTS is true/defined, this set of data will be stripped from the URL and added as a data-em-ajax attribute containing data AJAX can use
 * @return string
 */
function em_paginate($link, $total, $limit, $page=1, $data=array(), $ajax = null){
	if($limit > 0){
		$ajax = $ajax === null ? EM_AJAX : $ajax;
		$pagesToShow = defined('EM_PAGES_TO_SHOW') ? EM_PAGES_TO_SHOW : 11;
		$centered = defined('EM_PAGINATION_CENTERED') ? EM_PAGINATION_CENTERED : true;
		$url_parts = explode('?', $link);
		$base_link = $url_parts[0];
		$base_querystring = '';
		$data_atts = '';
    	//Get querystring for first page without page
    	if( count($url_parts) > 0 ){
	    	$query_arr = array();
	    	parse_str($url_parts[1], $query_arr);
	    	//if $data was passed, strip any of these vars from both the $query_arr and $link for inclusion in the data-em-ajax attribute
	    	if( !empty($data) && is_array($data) && (!defined('EM_USE_DATA_ATTS') || EM_USE_DATA_ATTS) ){
	    		//remove the data attributes from $query_arr
	    		foreach( $data as $key => $value ){
	    			if( array_key_exists($key, $query_arr) ){
	    				unset($query_arr[$key]);
	    			}
					$data[$key] = urlencode($value);
	    		}
	    		//rebuild the master link, without these data attributes
	    		if( count($query_arr) > 0 ){
	    			$link = $base_link .'?'. build_query($query_arr);
	    		}else{
	    			$link = $base_link;
	    		}
	    		$data_atts = 'data-em-ajax="'.esc_attr(build_query($data)).'"'; //for inclusion later on
	    	}
	    	//proceed to build the base querystring without pagination arguments
	    	unset($query_arr['page']); unset($query_arr['pno']);
	    	$base_querystring = esc_attr(build_query($query_arr));
	    	if( !empty($base_querystring) ) $base_querystring = '?'.$base_querystring;
    	}
    	//calculate pages to show, totals etc.
		$maxPages = ceil($total/$limit); //Total number of pages, i.e. also the last page to show
		if( $centered ){
			$startPage = $page - floor($pagesToShow / 2);
			if( $startPage < 1 ) $startPage = 1;
		}else{
			$startPage = ($page <= $pagesToShow) ? 1 : $pagesToShow * (floor($page / $pagesToShow)); //Which page to start the pagination links from (in case we're on say page 12 and $pagesToShow is 10 pages)
		}
		$placeholder = urlencode('%PAGE%');
		$link = str_replace('%PAGE%', $placeholder, esc_url($link)); //To avoid url encoded/non encoded placeholders
	    //Add the back and first buttons
		    $string = ($page>1 && $startPage != 1) ? '<a class="prev first page-numbers" href="'.str_replace($placeholder,1,$link).'" title="1">&lt;&lt;</a> ' : '';
		    if($page == 2){
		    	$string .= ' <a class="prev page-numbers" href="'.esc_url($base_link.$base_querystring).'" title="2">&lt;</a> ';
		    }elseif($page > 2){
		    	$string .= ' <a class="prev page-numbers" href="'.str_replace($placeholder,$page-1,$link).'" title="'.($page-1).'">&lt;</a> ';
		    }
		//Loop each page and create a link or just a bold number if its the current page
			// 10 11 12 13 14 15 16 17 18 19 20
			$thisLastPage = $startPage + $pagesToShow < $maxPages ? $startPage + $pagesToShow : $maxPages;
			$responsive = $thisLastPage - $startPage > 5;
			for ($i = $startPage ; $i <= $thisLastPage ; $i++){
			    if( $responsive && $i == $startPage + 1 && $i + 2 <= $page) {
				    // second number onwards should be wrapped with a span and hidden for responsivenes if it's not next to the current number
				    // in other words, if there's only two page numbers before current, always visible, more - hide 2nd till current
				    $nc_open = true;
				    $string .= '<span class="not-current first-half">';
			    }
	            if($i == $page || (empty($page) && $startPage == $i)) {
					if( !empty($nc_open) ){
						// not first page in list, so we should close previous active current
						$string .= '</span>';
						$nc_open = false;
					}
	                $string .= ' <span class="page-numbers current">'.$i.'</span>';
					if( $responsive && $i + 2 < $thisLastPage ) {
						$string .= '<span class="not-current second-half">';
						$nc_open = true;
					}
	            }elseif($i=='1'){
	                $string .= ' <a class="page-numbers" href="'.esc_url($base_link.$base_querystring).'" title="'.$i.'">'.$i.'</a> ';
	            }else{
	                $string .= ' <a class="page-numbers" href="'.str_replace($placeholder,$i,$link).'" title="'.$i.'">'.$i.'</a> ';
	            }
				// leave last number unwrapped
			    if( !empty($nc_open) && $i + 2 == $thisLastPage ){
				    $string .= '</span>';
			    }
		    }
		//Add the forward and last buttons
		    $string .= ($page < $maxPages) ? ' <a class="next page-numbers" href="'.str_replace($placeholder,$page+1,$link).'" title="'.($page+1).'">&gt;</a> ' :' ' ;
		    $string .= ($i-1 < $maxPages) ? ' <a class="next last page-numbers" href="'.str_replace($placeholder,$maxPages,$link).'" title="'.$maxPages.'">&gt;&gt;</a> ' : ' ';
		// add ajax flag
			$ajax_class = $ajax ? 'em-ajax':'';
		//Return the string
		    return apply_filters('em_paginate', '<div class="em-pagination '.$ajax_class.'" '.$data_atts.'>'.$string.'</div>');
	}
}
}

/**
 * Creates a wp-admin style navigation.
 * @param string $link
 * @param int $total
 * @param int $limit
 * @param int $page
 * @param int $pagesToShow
 * @return string
 * @uses paginate_links()
 * @uses add_query_arg()
 */
function em_admin_paginate($total, $limit, $page=1, $vars=false, $base = false, $format = ''){
	$return = '<div class="tablenav-pages em-tablenav-pagination">';
	$base = !empty($base) ? $base:esc_url_raw(add_query_arg( 'pno', '%#%' ));
	$events_nav = paginate_links( array(
		'base' => $base,
		'format' => $format,
		'total' => ceil($total / $limit),
		'current' => $page,
		'add_args' => $vars
	));
	$return .= sprintf( '<span class="displaying-num">' . __( 'Displaying %1$s&#8211;%2$s of %3$s', 'events-manager') . ' </span>%4$s',
		number_format_i18n( ( $page - 1 ) * $limit + 1 ),
		number_format_i18n( min( $page * $limit, $total ) ),
		number_format_i18n( $total ),
		$events_nav
	);
	$return .= '</div>';
	return apply_filters('em_admin_paginate',$return,$total,$limit,$page,$vars);
}

/**
 * Takes a url and appends GET params (supplied as an assoc array), it automatically detects if you already have a querystring there
 * @param string $url
 * @param array $params
 * @param bool $html
 * @param bool $encode
 * @return string
 */
function em_add_get_params($url, $params=array(), $html=true, $encode=true){
	//Splig the url up to get the params and the page location
	$url_parts = explode('?', $url);
	$url = $url_parts[0];
	$url_params_dirty = array();
	if(count($url_parts) > 1){
		$url_params_dirty = $url_parts[1];
		//get the get params as an array
		if( !is_array($url_params_dirty) ){
			if( strstr($url_params_dirty, '&amp;') !== false ){
				$url_params_dirty = explode('&amp;', $url_params_dirty);
			}else{
				$url_params_dirty = explode('&', $url_params_dirty);
			}
		}
		//split further into associative array
		$url_params = array();
		foreach($url_params_dirty as $url_param){
			if( !empty($url_param) ){
				$url_param = explode('=', $url_param);
				if(count($url_param) > 1){
					$url_params[$url_param[0]] = $url_param[1];
				}
			}
		}
		//Merge it together
		$params = array_merge($url_params, $params);
	}
	//Now build the array back up.
	$count = 0;
	foreach($params as $key=>$value){
		if( $value !== null ){
			if( is_array($value) ) $value = implode(',',$value);
			$value = ($encode) ? urlencode($value):$value;
			if( $count == 0 ){
				$url .= "?{$key}=".$value;
			}else{
				$url .= ($html) ? "&amp;{$key}=".$value:"&{$key}=".$value;
			}
			$count++;
		}
	}
	return $html ? esc_url($url):esc_url_raw($url);
}

/**
 * Get a array of countries, translated. Keys are 2 character country iso codes. If you supply a string or array that will be the first value in the array (if array, the array key is the first key in the returned array)
 * @param mixed $add_blank
 * @return array
 */
function em_get_countries($add_blank = false, $sort = true){
	global $em_countries_array;
	if( !is_array($em_countries_array) ){
		$em_countries_array_i18n = array();
		$em_countries_array_i18n['cs'] = array ('AF' => 'Afghanistan', 'AL' => 'Albánie',  'DZ' => 'Alžírsko', 'AS' => 'Americká Samoa',     'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarctica',  'AG' => 'Antigua a Barbuda',   'AR' => 'Argentina',   'AM' => 'Arménie',      'AW' => 'Aruba', 'AU' => 'Austrálie',  'AT' => 'Rakousko',   'AZ' => 'Ázerbájdžán',   'BS' => 'Bahamy',  'BH' => 'Bahrajn', 'BD' => 'Bangladéš',   'BB' => 'Barbados', 'BY' => 'Belorusko',    'BE' => 'Belgie',   'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bhútán',  'BO' => 'Bolívie',  'BA' => 'Bosna a Hercegovina',     'BW' => 'Botswana', 'BR' => 'Brazílie',  'VG' => 'Britské Panenské ostrovy',    'BN' => 'Brunej',            'BG' => 'Bulharsko',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Pobreží slonoviny',                 'KH' => 'Kambodža',   'CM' => 'Kamerun',  'CA' => 'Kanada', 'CV' => 'Kapverdy',            'KY'=>'Kajmanské ostrovy',  'CF' => 'Stredoafrická republika',           'TD' => 'Cad',    'CL' => 'Chile', 'CN' => 'Cína',  'CO' => 'Kolumbie',  'KM' => 'Komory',          'CR' => 'Kostarika',  'HR' => 'Chorvatsko',   'CU' => 'Kuba', 'CY' => 'Kypr',   'CZ' => 'Česká republika',       'KP' => 'Severní Korea',   'CD' => 'Democratic Republic of the Congo', 'DK' => 'Dánsko', 'DJ' => 'Džibuti', 'DM' => 'Dominika', 'DO' => 'Dominikánská republika', 'EC' => 'Ekvádor', 'EG' => 'Egypt', 'SV' => 'Salvador', 'XE' => 'England', 'GQ' => 'Rovníková Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonsko', 'ET' => 'Etiopie','FO' => 'Faerské','FJ' => 'Fidži', 'FI' => 'Finsko', 'FR' => 'Francie', 'PF' => 'Francouzská Polynésie', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Gruzie', 'DE' => 'Nemecko', 'GH' => 'Ghana', 'GR' => 'Recko', 'GL' => 'Grónsko', 'GD' => 'Grenada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guiné-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Madarsko', 'IS' => 'Island', 'IN' => 'Indie', 'ID' => 'Indonésie', 'IR' => 'Írán', 'IQ' => 'Irák', 'IE' => 'Irsko', 'IL' => 'Izrael', 'IT' => 'Itálie', 'JE'=>'Jersey', 'JM' => 'Jamajka', 'JP' => 'Japonsko', 'JO' => 'Jordánsko', 'KZ' => 'Kazachstán', 'KE' => 'Kena', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuvajt', 'KG' => 'Kyrgyzstán', 'LA' => 'Laos', 'LV' => 'Lotyšsko', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Libérie', 'LY' => 'Libye', 'LI' => 'Lichtenštejnsko', 'LT' => 'Litva', 'LU' => 'Lucembursko', 'MO' => 'Macao', 'MK' => 'Makedonie', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Malajsie', 'MV' => 'Maledivy', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshallovy ostrovy', 'MQ' => 'Martinik', 'MU' => 'Mauricius', 'MR' => 'Mauritánie', 'MX' => 'Mexiko', 'FM' => 'Micronésie', 'MD' => 'Moldávie', 'MC' => 'Monako', 'MN' => 'Mongolsko', 'ME' => 'Cerná Hora', 'MA' => 'Maroko', 'MZ' => 'Mozambik', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibie', 'NR' => 'Nauru', 'NP' => 'Nepál', 'NL' => 'Holandsko', 'AN' => 'Nizozemské Antily', 'NC' => 'Nová Kaledonie', 'NZ' => 'Nový Zéland', 'NI' => 'Nikaragua', 'NE' => 'Niger', 'NG' => 'Nigérie', 'XI' => 'Northern Ireland', 'MP' => 'Severní Mariany', 'NO' => 'Norsko', 'OM' => 'Omán', 'PK' => 'Pákistán', 'PW' => 'Palau', 'PS' => 'Palestina', 'PA' => 'Panama', 'PG' => 'Papua-Nová Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Filipíny', 'PL' => 'Polsko', 'PT' => 'Portugalsko', 'PR' => 'Portoriko', 'QA' => 'Katar', 'CG' => 'Republic of the Congo', 'RN' => 'Réunion', 'RO' => 'Rumunsko', 'RU' => 'Rusko', 'RW' => 'Rwanda', 'ST' => 'Svatý Tomáš a Princuv ostrov', 'KN' => 'Svatý Kryštof a Nevis', 'LC' => 'Svatá Lucie', 'VC' => 'Svatý Vincenc a Grenadiny', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saúdská Arábie', 'XS' => 'Skotsko', 'SN' => 'Senegal', 'RS' => 'Srbsko', 'SC' => 'Seychely', 'SL' => 'Sierra Leone', 'SG' => 'Singapur', 'SK' => 'Slovensko', 'SI' => 'Slovinsko', 'SB' => 'Šalamounovy ostrovy', 'SO' => 'Somálsko', 'ZA' => 'Jihoafrická republika', 'KR' => 'Jižní Korea', 'ES' => 'Španělsko', 'LK' => 'Srí Lanka', 'SD' => 'Súdán', 'SR' => 'Surinam', 'SZ' => 'Svazijsko', 'SE' => 'Švédsko', 'CH' => 'Švýcarsko', 'SY' => 'Sýrie', 'TW' => 'Tchaj-wan', 'TJ' => 'Tádžikistán', 'TZ' => 'Tanzanie', 'TH' => 'Thajsko', 'TL' => 'Východní Timor', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad a Tobago', 'TN' => 'Tunisko', 'TR' => 'Turecko', 'TM' => 'Turkmenistán', 'TV' => 'Tuvalu', 'VI' => 'Americké Panenské ostrovy', 'UG' => 'Uganda', 'UA' => 'Ukrajina', 'AE' => 'Spojené arabské emiráty', 'GB' => 'Spojené království', 'US' => 'Spojené státy', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistán', 'VU' => 'Nové Hebridy', 'VA' => 'Vatikán', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Jemen', 'ZM' => 'Zambie', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['da'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanien', 'DZ' => 'Algeriet', 'AS' => 'Amerikansk Samoa',   'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarktis',   'AG' => 'Antigua og Barbuda',  'AR' => 'Argentina',   'AM' => 'Armenien',     'AW' => 'Aruba', 'AU' => 'Australien', 'AT' => 'Østrig',     'AZ' => 'Aserbajdsjan',  'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Belarus',      'BE' => 'Belgien',  'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivia',  'BA' => 'Bosnien-Hercegovina',     'BW' => 'Botswana', 'BR' => 'Brasilien', 'VG' => 'De Britiske Jomfruøer',       'BN' => 'Brunei',            'BG' => 'Bulgarien',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Côte d\'Ivoire',                    'KH' => 'Cambodja',   'CM' => 'Cameroun', 'CA' => 'Canada', 'CV' => 'Kap Verde',           'KY'=>'Caymanøerne',        'CF' => 'Den Centralafrikanske Republik',    'TD' => 'Tchad',  'CL' => 'Chile', 'CN' => 'Kina',  'CO' => 'Colombia',  'KM' => 'Comorerne',       'CR' => 'Costa Rica', 'HR' => 'Kroatien',     'CU' => 'Cuba', 'CY' => 'Cypern', 'CZ' => 'Tjekkiet',              'KP' => 'Nordkorea',       'CD' => 'Congo', 'DK' => 'Danmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Den Dominikanske Republik', 'EC' => 'Ecuador', 'EG' => 'Egypten', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Ækvatorialguinea', 'ER' => 'Eritrea', 'EE' => 'Estland', 'ET' => 'Etiopien','FO' => 'Færøerne', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'Frankrig', 'PF' => 'Fransk Polynesien', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgien', 'DE' => 'Tyskland', 'GH' => 'Ghana', 'GR' => 'Grækenland', 'GL' => 'Grønland (Kalaallit Nunaat)', 'GD' => 'Grenada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hongkong', 'HU' => 'Ungarn', 'IS' => 'Island', 'IN' => 'Indien', 'ID' => 'Indonesien', 'IR' => 'Iran', 'IQ' => 'Irak', 'IE' => 'Irland', 'IL' => 'Israel', 'IT' => 'Italien', 'JE'=>'Jersey', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordan', 'KZ' => 'Kasakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirgisistan', 'LA' => 'Laos', 'LV' => 'Letland', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyen', 'LI' => 'Liechtenstein', 'LT' => 'Litauen', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Makedonien', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldiverne', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshalløerne', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauretanien', 'MX' => 'Mexico', 'FM' => 'Mikronesien', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongoliet', 'ME' => 'Montenegro', 'MA' => 'Marokko', 'MZ' => 'Mozambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Nederlandene', 'AN' => 'De Nederlandske Antiller', 'NC' => 'Ny Kaledonien', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Nordmarianerne', 'NO' => 'Norge', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua Ny Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Filippinerne', 'PL' => 'Polen', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RN' => 'Réunion', 'RO' => 'Rumænien', 'RU' => 'Rusland', 'RW' => 'Rwanda', 'ST' => 'São Tomé og Príncipe', 'KN' => 'Saint Kitts og Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent og Grenadinerne', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi-Arabien', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychellerne', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakiet', 'SI' => 'Slovenien', 'SB' => 'Salomonøerne', 'SO' => 'Somalia', 'ZA' => 'Sydafrika', 'KR' => 'Sydkorea', 'ES' => 'Spanien', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Surinam', 'SZ' => 'Swaziland', 'SE' => 'Sverige', 'CH' => 'Schweiz', 'SY' => 'Syrien', 'TW' => 'Taiwan', 'TJ' => 'Tadsjikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Østtimor', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad og Tobago', 'TN' => 'Tunesien', 'TR' => 'Tyrkiet', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'De Amerikanske Jomfruøer', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'De Forenede Arabiske Emirater', 'GB' => 'Det Forenede Kongerige', 'US' => 'USA', 'UY' => 'Uruguay', 'UZ' => 'Usbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vatikanstaten', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['es'] = array ('AF' => 'Afganistán',  'AL' => 'Albania',  'DZ' => 'Argelia',  'AS' => 'Samoa Americana',    'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antártida',   'AG' => 'Antigua y Barbuda',   'AR' => 'Argentina',   'AM' => 'Armenia',      'AW' => 'Aruba', 'AU' => 'Australia',  'AT' => 'Austria',    'AZ' => 'Azerbaiyán',    'BS' => 'Bahamas', 'BH' => 'Bahráin', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Bielorrusia',  'BE' => 'Bélgica',  'BZ' => 'Belice', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bután',   'BO' => 'Bolivia',  'BA' => 'Bosnia y Hercegovina',    'BW' => 'Botsuana', 'BR' => 'Brasil',    'VG' => 'Islas Vírgenes Británicas',   'BN' => 'Brunéi',            'BG' => 'Bulgaria',   'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Costa de Marfil',                   'KH' => 'Camboya',    'CM' => 'Camerún',  'CA' => 'Canadá', 'CV' => 'Cabo Verde',          'KY'=>'Islas Caimán',       'CF' => 'República Centroafricana',          'TD' => 'Chad',   'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Colombia',  'KM' => 'Comoras',         'CR' => 'Costa Rica', 'HR' => 'Croacia',      'CU' => 'Cuba', 'CY' => 'Chipre', 'CZ' => 'República Checa',       'KP' => 'Corea del Norte',                      'CD' => 'República Democrática del Congo', 'DK' => 'Dinamarca', 'DJ' => 'Yibuti', 'DM' => 'Dominica', 'DO' => 'República Dominicana', 'EC' => 'Ecuador', 'EG' => 'Egipto', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Guinea Ecuatorial', 'ER' => 'Eritrea','FO' => 'Islas Faroe', 'EE' => 'Estonia', 'ET' => 'Etiopía', 'FJ' => 'Fiyi', 'FI' => 'Finlandia', 'FR' => 'Francia', 'PF' => 'Polinesia Francesa', 'GA' => 'Gabón', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Alemania', 'GH' => 'Ghana', 'GR' => 'Grecia', 'GL' => 'Groenlandia', 'GD' => 'Granada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haití', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungría', 'IS' => 'Islandia', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Irán', 'IQ' => 'Iraq', 'IE' => 'Irlanda', 'IL' => 'Israel', 'IT' => 'Italia', 'JE'=>'Jersey', 'JM' => 'Jamaica', 'JP' => 'Japón', 'JO' => 'Jordania', 'KZ' => 'Kazajistán', 'KE' => 'Kenia', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirguizistán', 'LA' => 'Laos', 'LV' => 'Letonia', 'LB' => 'Líbano', 'LS' => 'Lesoto', 'LR' => 'Liberia', 'LY' => 'Libia', 'LI' => 'Liechtenstein', 'LT' => 'Lituania', 'LU' => 'Luxemburgo', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malaui', 'MY' => 'Malasia', 'MV' => 'Maldivas', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Islas Marshall', 'MQ' => 'Martinica', 'MU' => 'Mauricio', 'MR' => 'Mauritania', 'MX' => 'México', 'FM' => 'Micronesia', 'MD' => 'Moldavia', 'MC' => 'Mónaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MA' => 'Marruecos', 'MZ' => 'Mozambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Países Bajos', 'AN' => 'Antillas Neerlandesas', 'NC' => 'Nueva Caledonia', 'NZ' => 'Nueva Zelanda', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Islas Marianas del Norte', 'NO' => 'Noruega', 'OM' => 'Omán', 'PK' => 'Pakistán', 'PW' => 'Palaos', 'PS' => 'Palestine', 'PA' => 'Panamá', 'PG' => 'Papúa-Nueva Guinea', 'PY' => 'Paraguay', 'PE' => 'Perú', 'PH' => 'Filipinas', 'PL' => 'Polonia', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RN' => 'Réunion', 'RO' => 'Rumania', 'RU' => 'Russia', 'RW' => 'Ruanda', 'ST' => 'Santo Tomé y Príncipe', 'KN' => 'San Cristóbal y Nieves', 'LC' => 'Santa Lucía', 'VC' => 'San Vicente y las Granadinas', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Arabia Saudí', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leona', 'SG' => 'Singapur', 'SK' => 'Eslovaquia', 'SI' => 'Eslovenia', 'SB' => 'Islas Salomón', 'SO' => 'Somalia', 'ZA' => 'Sudáfrica', 'KR' => 'Corea del Sur', 'ES' => 'España', 'LK' => 'Sri Lanka', 'SD' => 'Sudán', 'SR' => 'Surinam', 'SZ' => 'Suazilandia', 'SE' => 'Suecia', 'CH' => 'Suiza', 'SY' => 'Siria', 'TW' => 'Taiwán', 'TJ' => 'Tayikistán', 'TZ' => 'Tanzania', 'TH' => 'Tailandia', 'TL' => 'Timor Oriental', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad y Tobago', 'TN' => 'Túnez', 'TR' => 'Turquía', 'TM' => 'Turkmenistán', 'TV' => 'Tuvalu', 'VI' => 'Islas Vírgenes Americanas', 'UG' => 'Uganda', 'UA' => 'Ucrania', 'AE' => 'Emiratos Árabes Unidos', 'GB' => 'Reino Unido', 'US' => 'Estados Unidos', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistán', 'VU' => 'Vanuatu', 'VA' => 'El Vaticano', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabue' );
		$em_countries_array_i18n['fr'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanie',  'DZ' => 'Algérie',  'AS' => 'Samoa américaines',  'AD' => 'Andorre', 'AO' => 'Angola', 'AQ' => 'Antarctique', 'AG' => 'Antigua-et-Barbuda',  'AR' => 'Argentine',   'AM' => 'Arménie',      'AW' => 'Aruba', 'AU' => 'Australie',  'AT' => 'Autriche',   'AZ' => 'Azerbaïdjan',   'BS' => 'Bahamas', 'BH' => 'Bahreïn', 'BD' => 'Bangladesh',  'BB' => 'Barbade',  'BY' => 'Biélorussie',  'BE' => 'Belgique', 'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Bénin', 'BT' => 'Bhoutan', 'BO' => 'Bolivie',  'BA' => 'Bosnie-Herzégovine',      'BW' => 'Botswana', 'BR' => 'Brésil',    'VG' => 'Iles Vierges britanniques',   'BN' => 'Brunei',            'BG' => 'Bulgarie',   'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'C&ocirc;te D\'Ivoire',              'KH' => 'Cambodge',   'CM' => 'Cameroun', 'CA' => 'Canada', 'CV' => 'Cap-Vert',            'KY'=>'Iles Cayman',        'CF' => 'République centrafricaine',         'TD' => 'Tchad',  'CL' => 'Chili', 'CN' => 'Chine', 'CO' => 'Colombie',  'KM' => 'Comores',         'CR' => 'Costa Rica', 'HR' => 'Croatie',      'CU' => 'Cuba', 'CY' => 'Chypre', 'CZ' => 'République tchèque',    'KP' => 'Corée du Nord',                        'CD' => 'République démocratique du Congo', 'DK' => 'Danemark', 'DJ' => 'Djibouti', 'DM' => 'Dominique', 'DO' => 'République dominicaine', 'EC' => 'Équateur', 'EG' => 'Égypte', 'SV' => 'Salvador', 'XE' => 'England', 'GQ' => 'Guinée équatoriale', 'ER' => 'Érythrée', 'EE' => 'Estonie', 'ET' => 'Éthiopie','FO' => 'Îles Féroé', 'FJ' => 'Iles Fidji', 'FI' => 'Finlande', 'FR' => 'France', 'PF' => 'Polynésie française', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Géorgie', 'DE' => 'Allemagne', 'GH' => 'Ghana', 'GR' => 'Grèce', 'GL' => 'Groenland', 'GD' => 'Grenade', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinée', 'GW' => 'Guinée-Bissao', 'GY' => 'Guyana', 'HT' => 'Haïti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hongrie', 'IS' => 'Islande', 'IN' => 'Inde', 'ID' => 'Indonésie', 'IR' => 'Iran', 'IQ' => 'Iraq', 'IE' => 'Irlande', 'IL' => 'Israël', 'IT' => 'Italie', 'JE'=>'Jersey', 'JM' => 'Jamaïque', 'JP' => 'Japon', 'JO' => 'Jordanie', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Koweït', 'KG' => 'Kirghizistan', 'LA' => 'Laos', 'LV' => 'Lettonie', 'LB' => 'Liban', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libye', 'LI' => 'Liechtenstein', 'LT' => 'Lituanie', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macédoine', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaisie', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malte', 'MH' => 'Iles Marshall', 'MQ' => 'Martinique', 'MU' => 'Maurice', 'MR' => 'Mauritanie', 'MX' => 'Mexique', 'FM' => 'Micronésie', 'MD' => 'Moldavie', 'MC' => 'Monaco', 'MN' => 'Mongolie', 'ME' => 'Montenegro', 'MA' => 'Maroc', 'MZ' => 'Mozambique', 'MM' => 'le Myanmar', 'NA' => 'Namibie', 'NR' => 'Nauru', 'NP' => 'Népal', 'NL' => 'Pays-Bas', 'AN' => 'Antilles néerlandaises', 'NC' => 'Nouvelle-Calédonie', 'NZ' => 'Nouvelle-Zélande', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Mariannes du Nord', 'NO' => 'Norvège', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papouasie-Nouvelle-Guinée', 'PY' => 'Paraguay', 'PE' => 'Pérou', 'PH' => 'Philippines', 'PL' => 'Pologne', 'PT' => 'Portugal', 'PR' => 'Porto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RN' => 'Réunion', 'RO' => 'Roumanie', 'RU' => 'Russie', 'RW' => 'Rwanda', 'ST' => 'Sao Tomé-et-Principe', 'KN' => 'Saint-Christophe-et-Niévès', 'LC' => 'Sainte-Lucie', 'VC' => 'Saint-Vincent-et-les-Grenadines', 'WS' => 'Samoa', 'SM' => 'Saint-Marin', 'SA' => 'Arabie saoudite', 'XS' => 'Scotland', 'SN' => 'Sénégal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapour', 'SK' => 'Slovaquie', 'SI' => 'Slovénie', 'SB' => 'Iles Salomon', 'SO' => 'Somalie', 'ZA' => 'Afrique du Sud', 'KR' => 'Corée du Sud', 'ES' => 'Espagne', 'LK' => 'Sri Lanka', 'SD' => 'Soudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Suède', 'CH' => 'Suisse', 'SY' => 'Syrie', 'TW' => 'Taïwan', 'TJ' => 'Tadjikistan', 'TZ' => 'Tanzanie', 'TH' => 'Thaïlande', 'TL' => 'Timor Oriental', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinité-et-Tobago', 'TN' => 'Tunisie', 'TR' => 'Turquie', 'TM' => 'Turkménistan', 'TV' => 'Tuvalu', 'VI' => 'Iles Vierges américaines', 'UG' => 'Ouganda', 'UA' => 'Ukraine', 'AE' => 'Émirats arabes unis', 'GB' => 'Royaume-Uni', 'US' => 'États-Unis', 'UY' => 'Uruguay', 'UZ' => 'Ouzbékistan', 'VU' => 'Vanuatu', 'VA' => 'Saint-Siège', 'VE' => 'Venezuela', 'VN' => 'Viêt Nam', 'XW' => 'Wales', 'YE' => 'Yémen', 'ZM' => 'Zambie', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['de'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanien', 'DZ' => 'Algerien', 'AS' => 'Amerikanisch-Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarktis',   'AG' => 'Antigua und Barbuda', 'AR' => 'Argentinien', 'AM' => 'Armenien',     'AW' => 'Aruba', 'AU' => 'Australien', 'AT' => 'Österreich', 'AZ' => 'Aserbaidschan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesch', 'BB' => 'Barbados', 'BY' => 'Belarus',      'BE' => 'Belgien',  'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivien', 'BA' => 'Bosnien und Herzegowina', 'BW' => 'Botsuana', 'BR' => 'Brasilien', 'VG' => 'Britische Jungferninseln',    'BN' => 'Brunei Darussalam', 'BG' => 'Bulgarien',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'C&ocirc;te D\'Ivoire',              'KH' => 'Kambodscha', 'CM' => 'Kamerun',  'CA' => 'Kanada', 'CV' => 'Kap Verde',           'KY'=>'Kaimaninseln',       'CF' => 'Zentralafrikanische Republik',      'TD' => 'Tschad', 'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Kolumbien', 'KM' => 'Komoren',         'CR' => 'Costa Rica', 'HR' => 'Kroatien',     'CU' => 'Kuba', 'CY' => 'Zypern', 'CZ' => 'Tschechische Republik', 'KP' => 'Demokratische Volksrepublik Korea',    'CD' => 'Demokratische Republik Kongo', 'DK' => 'Dänemark', 'DJ' => 'Dschibuti', 'DM' => 'Dominica', 'DO' => 'Dominikanische Republik', 'EC' => 'Ecuador', 'EG' => 'Ägypten', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Äquatorialguinea', 'ER' => 'Eritrea', 'EE' => 'Estland', 'ET' => 'Äthiopien','FO' => 'Färöer Inseln', 'FJ' => 'Fidschi', 'FI' => 'Finnland', 'FR' => 'Frankreich', 'PF' => 'Französisch-Polynesien', 'GA' => 'Gabun', 'GM' => 'Gambia', 'GE' => 'Georgien', 'DE' => 'Deutschland', 'GH' => 'Ghana', 'GR' => 'Griechenland', 'GL' => 'Grönland', 'GD' => 'Grenada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'HongKong', 'HU' => 'Ungarn', 'IS' => 'Island', 'IN' => 'Indien', 'ID' => 'Indonesien', 'IR' => 'Iran', 'IQ' => 'Irak', 'IE' => 'Irland', 'IL' => 'Israel', 'IT' => 'Italien', 'JE'=>'Jersey', 'JM' => 'Jamaika', 'JP' => 'Japan', 'JO' => 'Jordanien', 'KZ' => 'Kasachstan', 'KE' => 'Kenia', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirgisistan', 'LA' => 'Laos', 'LV' => 'Lettland', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyen', 'LI' => 'Liechtenstein', 'LT' => 'Litauen', 'LU' => 'Luxemburg', 'MO' => 'Macau', 'MK' => 'Mazedonien', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Malediven', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshallinseln', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauretanien', 'MX' => 'Mexiko', 'FM' => 'Mikronesien', 'MD' => 'Republik Moldau', 'MC' => 'Monaco', 'MN' => 'Mongolei', 'ME' => 'Montenegro', 'MA' => 'Marokko', 'MZ' => 'Mosambik', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Niederlande', 'AN' => 'Niederländische Antillen', 'NC' => 'Neukaledonien', 'NZ' => 'Neuseeland', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Nördliche Marianen', 'NO' => 'Norwegen', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua-Neuguinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippinen', 'PL' => 'Polen', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Katar', 'CG' => 'Kongo', 'RN' => 'Réunion', 'RO' => 'Rumänien', 'RU' => 'Russische Föderation', 'RW' => 'Ruanda', 'ST' => 'São Tomé und Príncipe', 'KN' => 'St. Kitts und Nevis', 'LC' => 'St. Lucia', 'VC' => 'St. Vincent und die Grenadinen', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi-Arabien', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychellen', 'SL' => 'Sierra Leone', 'SG' => 'Singapur', 'SK' => 'Slowakei', 'SI' => 'Slowenien', 'SB' => 'Salomonen', 'SO' => 'Somalia', 'ZA' => 'Südafrika', 'KR' => 'Republik Korea', 'ES' => 'Spanien', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swasiland', 'SE' => 'Schweden', 'CH' => 'Schweiz', 'SY' => 'Syrien', 'TW' => 'Taiwan', 'TJ' => 'Tadschikistan', 'TZ' => 'Tansania', 'TH' => 'Thailand', 'TL' => 'Osttimor', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad und Tobago', 'TN' => 'Tunesien', 'TR' => 'Türkei', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'Amerikanische Jungferninseln', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'Vereinigte Arabische Emirate', 'GB' => 'Vereinigtes Königreich', 'US' => 'Vereinigte Staaten', 'UY' => 'Uruguay', 'UZ' => 'Usbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vatikanstadt', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Jemen', 'ZM' => 'Sambia', 'ZW' => 'Simbabwe' );
		$em_countries_array_i18n['hu'] = array ('AF' => 'Afganisztán', 'AL' => 'Albánia',  'DZ' => 'Algéria',  'AS' => 'Amerikai Szamoa',    'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarktisz',  'AG' => 'Antigua és Barbuda',  'AR' => 'Argentína',   'AM' => 'Örményország', 'AW' => 'Aruba', 'AU' => 'Ausztrália', 'AT' => 'Ausztria',   'AZ' => 'Azerbajdzsán',  'BS' => 'Bahamas', 'BH' => 'Bahrein', 'BD' => 'Banglades',   'BB' => 'Barbados', 'BY' => 'Belorusszia',  'BE' => 'Belgium',  'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bhután',  'BO' => 'Bolívia',  'BA' => 'Bosznia-Hercegovina',     'BW' => 'Botswana', 'BR' => 'Brazília',  'VG' => 'Brit Virgin-szigetek',        'BN' => 'Brunei Darussalam', 'BG' => 'Bulgária',   'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Côte d\'Ivoire (Elefántcsontpart)', 'KH' => 'Kambodzsa',  'CM' => 'Kamerun',  'CA' => 'Kanada', 'CV' => 'Zöld-foki-szigetek',  'KY'=>'Kajmán-szigetek',    'CF' => 'Közép-Afrikai Köztársaság',         'TD' => 'Csád',   'CL' => 'Chile', 'CN' => 'Kína',  'CO' => 'Kolumbia',  'KM' => 'Comore-szigetek', 'CR' => 'Costa Rica', 'HR' => 'Horvátország', 'CU' => 'Kuba', 'CY' => 'Ciprus', 'CZ' => 'Cseh Köztársaság',      'KP' => 'Koreai Népi Demokratikus Köztársaság', 'CD' => 'Kongói Demokratikus Köztársaság', 'DK' => 'Dánia', 'DJ' => 'Dzsibuti', 'DM' => 'Dominika', 'DO' => 'Dominikai Köztársaság', 'EC' => 'Ecuador', 'EG' => 'Egyiptom', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Egyenlítõi Guinea', 'ER' => 'Eritrea', 'EE' => 'Észtország', 'ET' => 'Etiópia','FO' => 'Faroe Szigetek', 'FJ' => 'Fidzsi', 'FI' => 'Finnország', 'FR' => 'Franciaország', 'PF' => 'Francia Polinézia', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'GrLzia', 'DE' => 'Németország', 'GH' => 'Ghána', 'GR' => 'Görögország', 'GL' => 'Grönland', 'GD' => 'Grenada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Bissau-Guinea', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Magyarország', 'IS' => 'Izland', 'IN' => 'India', 'ID' => 'Indonézia', 'IR' => 'Irán', 'IQ' => 'Irak', 'IE' => 'Írország', 'IL' => 'Izrael', 'IT' => 'Olaszország', 'JE'=>'Jersey', 'JM' => 'Jamaica', 'JP' => 'Japán', 'JO' => 'Jordánia', 'KZ' => 'Kazahsztán', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuvait', 'KG' => 'Kirgizisztán', 'LA' => 'Lao Népi Demokratikus Köztársaság', 'LV' => 'Litvánia', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Libéria', 'LY' => 'Líbia', 'LI' => 'Liechtenstein', 'LT' => 'Litvánia', 'LU' => 'Luxemburg', 'MO' => 'Makaó', 'MK' => 'Macedónia', 'MG' => 'Madagaszkár', 'MW' => 'Malawi', 'MY' => 'Malajzia', 'MV' => 'Maldív-szigetek', 'ML' => 'Mali', 'MT' => 'Málta', 'MH' => 'Marshall-szigetek', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauritánia', 'MX' => 'Mexikó', 'FM' => 'Mikronéziai Szövetségi Államok', 'MD' => 'Moldva', 'MC' => 'Monaco', 'MN' => 'Mongólia', 'ME' => 'Montenegro', 'MA' => 'Marokkó', 'MZ' => 'Mozambik', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namíbia', 'NR' => 'Nauru', 'NP' => 'Nepál', 'NL' => 'Hollandia', 'AN' => 'Holland Antillák', 'NC' => 'Új-Kaledónia', 'NZ' => 'Új-Zéland', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigéria', 'XI' => 'Northern Ireland', 'MP' => 'Észak-Mariana-szigetek', 'NO' => 'Norvégia', 'OM' => 'Omán', 'PK' => 'Pakisztán', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Pápua Új-Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Fülöp-szigetek', 'PL' => 'Lengyelország', 'PT' => 'Portugália', 'PR' => 'Puerto Rico', 'QA' => 'Katar', 'CG' => 'Kongó', 'RN' => 'Réunion', 'RO' => 'Románia', 'RU' => 'Orosz Föderáció', 'RW' => 'Ruanda', 'ST' => 'Sao Tome és Principe', 'KN' => 'Saint Kitts és Nevis', 'LC' => 'Szent Lucia', 'VC' => 'Saint Vincent és Grenadines', 'WS' => 'Szamoa', 'SM' => 'San Marino', 'SA' => 'SzaLd-Arábia', 'XS' => 'Scotland', 'SN' => 'Szenegál', 'RS' => 'Serbia', 'SC' => 'Seychelle-szigetek', 'SL' => 'Sierra Leone', 'SG' => 'SzingapLr', 'SK' => 'Szlovákia', 'SI' => 'Szlovénia', 'SB' => 'Salamon-szigetek', 'SO' => 'Szomália', 'ZA' => 'Dél-Afrika', 'KR' => 'Koreai Köztársaság', 'ES' => 'Spanyolország', 'LK' => 'Srí Lanka', 'SD' => 'Szudán', 'SR' => 'Suriname', 'SZ' => 'Szváziföld', 'SE' => 'Svédország', 'CH' => 'Svájc', 'SY' => 'Szíriai Arab Köztársaság', 'TW' => 'Tajvan', 'TJ' => 'Tádzsikisztán', 'TZ' => 'Tanzániai Egyesült Köztársaság', 'TH' => 'Thaiföld', 'TL' => 'Kelet-Timor', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad és Tobago', 'TN' => 'Tunézia', 'TR' => 'Törökország', 'TM' => 'Türkmenisztán', 'TV' => 'Tuvalu', 'VI' => 'Amerikai Virgin-szigetek', 'UG' => 'Uganda', 'UA' => 'Ukrajna', 'AE' => 'Egyesült Arab Emirátusok', 'GB' => 'Egyesült Királyság', 'US' => 'Egyesült Államok', 'UY' => 'Uruguay', 'UZ' => 'Üzbegisztán', 'VU' => 'Vanuatu', 'VA' => 'Vatikán', 'VE' => 'Venezuela', 'VN' => 'Vietnám', 'XW' => 'Wales', 'YE' => 'Jemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['it'] = array ('AF' => 'Afghanistan', 'AL' => 'Albania',  'DZ' => 'Algeria',  'AS' => 'Samoa americane',    'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antartide',   'AG' => 'Antigua e Barbuda',   'AR' => 'Argentina',   'AM' => 'Armenia',      'AW' => 'Aruba', 'AU' => 'Australia',  'AT' => 'Austria',    'AZ' => 'Azerbaigian',   'BS' => 'Bahamas', 'BH' => 'Bahrein', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Bielorussia',  'BE' => 'Belgio',   'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivia',  'BA' => 'Bosnia-Erzegovina',       'BW' => 'Botswana', 'BR' => 'Brasile',   'VG' => 'Isole Vergini britanniche',   'BN' => 'Brunei',            'BG' => 'Bulgaria',   'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Costa d\'Avorio',                   'KH' => 'Cambogia',   'CM' => 'Camerun',  'CA' => 'Canada', 'CV' => 'Capo Verde',          'KY'=>'Isole Cayman',       'CF' => 'Repubblica Centrafricana',          'TD' => 'Ciad',   'CL' => 'Cile',  'CN' => 'Cina',  'CO' => 'Colombia',  'KM' => 'Comore',          'CR' => 'Costa Rica', 'HR' => 'Croazia',      'CU' => 'Cuba', 'CY' => 'Cipro',  'CZ' => 'Repubblica ceca',       'KP' => 'Corea del Nord', 'CD' => 'Congo', 'DK' => 'Danimarca', 'DJ' => 'Gibuti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egitto', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Guinea Equatoriale', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Etiopia','FO' => 'Isole Faroe', 'FJ' => 'Figi', 'FI' => 'Finlandia', 'FR' => 'Francia', 'PF' => 'Polinesia francese', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germania', 'GH' => 'Ghana', 'GR' => 'Grecia', 'GL' => 'Groenlandia', 'GD' => 'Grenada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Ungheria', 'IS' => 'Islanda', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran', 'IQ' => 'Iraq', 'IE' => 'Irlanda', 'IL' => 'Israele', 'IT' => 'Italia', 'JE'=>'Jersey', 'JM' => 'Giamaica', 'JP' => 'Giappone', 'JO' => 'Giordania', 'KZ' => 'Kazakistan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirghizistan', 'LA' => 'Laos', 'LV' => 'Lettonia', 'LB' => 'Libano', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libia', 'LI' => 'Liechtenstein', 'LT' => 'Lituania', 'LU' => 'Lussemburgo', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malesia', 'MV' => 'Maldive', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Isole Marshall', 'MQ' => 'Martinica', 'MU' => 'Maurizio', 'MR' => 'Mauritania', 'MX' => 'Messico', 'FM' => 'Micronesia', 'MD' => 'Moldavia', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MA' => 'Marocco', 'MZ' => 'Mozambico', 'MM' => 'Myanmar(Birmania)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Paesi Bassi', 'AN' => 'Antille olandesi', 'NC' => 'Nuova Caledonia', 'NZ' => 'Nuova Zelanda', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Marianne settentrionali', 'NO' => 'Norvegia', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua Nuova Guinea', 'PY' => 'Paraguay', 'PE' => 'Perù', 'PH' => 'Filippine', 'PL' => 'Polonia', 'PT' => 'Portogallo', 'PR' => 'Portorico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RN' => 'Réunion', 'RO' => 'Romania', 'RU' => 'Russia', 'RW' => 'Ruanda', 'ST' => 'São Tomé e Príncipe', 'KN' => 'Saint Christopher e Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent e Grenadine', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Arabia Saudita', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seicelle', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovacchia', 'SI' => 'Slovenia', 'SB' => 'Isole Salomone', 'SO' => 'Somalia', 'ZA' => 'Sudafrica', 'KR' => 'Corea del Sud', 'ES' => 'Spagna', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Svezia', 'CH' => 'Svizzera', 'SY' => 'Siria', 'TW' => 'Taiwan', 'TJ' => 'Tagikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailandia', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad e Tobago', 'TN' => 'Tunisia', 'TR' => 'Turchia', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'Isole Vergini americane', 'UG' => 'Uganda', 'UA' => 'Ucraina', 'AE' => 'Emirati arabi uniti', 'GB' => 'Regno Unito', 'US' => 'Stati Uniti', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vaticano', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['nl'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanië',  'DZ' => 'Algerije', 'AS' => 'Amerikaans-Samoa',   'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarctica',  'AG' => 'Antigua en Barbuda',  'AR' => 'Argentinië',  'AM' => 'Armenië',      'AW' => 'Aruba', 'AU' => 'Australië',  'AT' => 'Oostenrijk', 'AZ' => 'Azerbeidzjan',  'BS' => 'Bahamas', 'BH' => 'Bahrein', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Belarus',      'BE' => 'België',   'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivia',  'BA' => 'Bosnië en Herzegovina',   'BW' => 'Botswana', 'BR' => 'Brazilië',  'VG' => 'Britse Maagdeneilanden',      'BN' => 'Brunei',            'BG' => 'Bulgarije',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Ivoorkust',                         'KH' => 'Cambodja',   'CM' => 'Kameroen', 'CA' => 'Canada', 'CV' => 'Kaapverdië',          'KY'=>'Caymaneilanden',     'CF' => 'Centraal-Afrikaanse Republiek',     'TD' => 'Tsjaad', 'CL' => 'Chili', 'CN' => 'China', 'CO' => 'Colombia',  'KM' => 'Comoren',         'CR' => 'Costa Rica', 'HR' => 'Kroatië',      'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Tsjechië',              'KP' => 'Noord-Korea', 'CD' => 'Democratische Republiek Congo', 'DK' => 'Denemarken', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominicaanse Republiek', 'EC' => 'Ecuador', 'EG' => 'Egypte', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Equatoriaal-Guinea', 'ER' => 'Eritrea', 'EE' => 'Estland', 'ET' => 'Ethiopië','FO' => 'Faeröer', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'Frankrijk', 'PF' => 'Frans-Polynesië', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgië', 'DE' => 'Duitsland', 'GH' => 'Ghana', 'GR' => 'Griekenland', 'GL' => 'Groenland', 'GD' => 'Grenada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinee', 'GW' => 'Guinee-Bissau', 'GY' => 'Guyana', 'HT' => 'Haïti', 'HN' => 'Honduras', 'HK' => 'HongKong', 'HU' => 'Hongarije', 'IS' => 'IJsland', 'IN' => 'India', 'ID' => 'Indonesië', 'IR' => 'Iran', 'IQ' => 'Irak', 'IE' => 'Ierland', 'IL' => 'Israël', 'IT' => 'Italië', 'JE'=>'Jersey', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordanië', 'KZ' => 'Kazachstan', 'KE' => 'Kenia', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Koeweit', 'KG' => 'Kirgizië', 'LA' => 'Laos', 'LV' => 'Letland', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libië', 'LI' => 'Liechtenstein', 'LT' => 'Litouwen', 'LU' => 'Luxemburg', 'MO' => 'Macau', 'MK' => 'Macedonië', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Maleisië', 'MV' => 'Maldiven', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshalleilanden', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauritanië', 'MX' => 'Mexico', 'FM' => 'Micronesia', 'MD' => 'Moldavië', 'MC' => 'Monaco', 'MN' => 'Mongolië', 'ME' => 'Montenegro', 'MA' => 'Marokko', 'MZ' => 'Mozambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibië', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Nederland', 'AN' => 'Nederlandse Antillen', 'NC' => 'Nieuw-Caledonië', 'NZ' => 'Nieuw-Zeeland', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Noordelijke Marianen', 'NO' => 'Noorwegen', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papoea-Nieuw-Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Filipijnen', 'PL' => 'Polen', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RN' => 'Réunion', 'RO' => 'Roemenië', 'RU' => 'Rusland', 'RW' => 'Rwanda', 'ST' => 'Sao Tomé en Principe', 'KN' => 'Saint Kitts en Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent en de Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi-Arabië', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychellen', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slowakije', 'SI' => 'Slovenië', 'SB' => 'Salomonseilanden', 'SO' => 'Somalië', 'ZA' => 'Zuid-Afrika', 'KR' => 'Zuid-Korea', 'ES' => 'Spanje', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Zweden', 'CH' => 'Zwitserland', 'SY' => 'Syrië', 'TW' => 'Taiwan', 'TJ' => 'Tadzjikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad en Tobago', 'TN' => 'Tunesië', 'TR' => 'Turkije', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'Amerikaanse Maagdeneilanden', 'UG' => 'Uganda', 'UA' => 'Oekraïne', 'AE' => 'Verenigde Arabische Emiraten', 'GB' => 'Verenigd Koninkrijk', 'US' => 'Verenigde Staten', 'UY' => 'Uruguay', 'UZ' => 'Oezbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vaticaanstad', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Jemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['pt'] = array ('AF' => 'Afeganistão', 'AL' => 'Albânia',  'DZ' => 'Argélia',  'AS' => 'Samoa Americana',    'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antárctida',  'AG' => 'Antígua e Barbuda',   'AR' => 'Argentina',   'AM' => 'Arménia',      'AW' => 'Aruba', 'AU' => 'Austrália',  'AT' => 'Áustria',    'AZ' => 'Azerbaijão',    'BS' => 'Baamas',  'BH' => 'Barém',   'BD' => 'Bangladeche', 'BB' => 'Barbados', 'BY' => 'Bielorrússia', 'BE' => 'Bélgica',  'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benim', 'BT' => 'Butão',   'BO' => 'Bolívia',  'BA' => 'Bósnia e Herzegovina',    'BW' => 'Botsuana', 'BR' => 'Brasil',    'VG' => 'Ilhas Virgens Britânicas',    'BN' => 'Brunei',            'BG' => 'Bulgária',   'BF' => 'Burquina Faso', 'BI' => 'Burúndi', 'CI' => 'Costa do Marfim',                   'KH' => 'Camboja',    'CM' => 'Camarões', 'CA' => 'Canadá', 'CV' => 'Cabo Verde',          'KY'=>'Ilhas Caimão',       'CF' => 'República Centro-Africana',         'TD' => 'Chade',  'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Colômbia',  'KM' => 'Comores',         'CR' => 'Costa Rica', 'HR' => 'Croácia',      'CU' => 'Cuba', 'CY' => 'Chipre', 'CZ' => 'República Checa',       'KP' => 'Coreia do Norte', 'CD' => 'Congo-Kinshasa', 'DK' => 'Dinamarca', 'DJ' => 'Jibuti', 'DM' => 'Domínica', 'DO' => 'República Dominicana', 'EC' => 'Equador', 'EG' => 'Egipto', 'SV' => 'Salvador', 'XE' => 'England', 'GQ' => 'Guiné Equatorial', 'ER' => 'Eritreia', 'EE' => 'Estónia', 'ET' => 'Etiópia','FO' => 'ilhas Faroe', 'FJ' => 'Fiji', 'FI' => 'Finlândia', 'FR' => 'França', 'PF' => 'Polinésia Francesa', 'GA' => 'Gabão', 'GM' => 'Gambia', 'GE' => 'Geórgia', 'DE' => 'Alemanha', 'GH' => 'Gana', 'GR' => 'Grécia', 'GL' => 'Gronelândia', 'GD' => 'Granada', 'GP' =>'Guadeloupe', 'GU' => 'Guame', 'GT' => 'Guatemala', 'GN' => 'Guiné', 'GW' => 'Guiné-Bissau', 'GY' => 'Guiana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungria', 'IS' => 'Islândia', 'IN' => 'Índia', 'ID' => 'Indonésia', 'IR' => 'Irão', 'IQ' => 'Iraque', 'IE' => 'Irlanda', 'IL' => 'Israel', 'IT' => 'Itália', 'JE'=>'Jersey', 'JM' => 'Jamaica', 'JP' => 'Japão', 'JO' => 'Jordânia', 'KZ' => 'Cazaquistão', 'KE' => 'Quénia', 'KI' => 'Quiribáti', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Quirguizistão', 'LA' => 'Laos', 'LV' => 'Letónia', 'LB' => 'Líbano', 'LS' => 'Lesoto', 'LR' => 'Libéria', 'LY' => 'Líbia', 'LI' => 'Listenstaine', 'LT' => 'Lituânia', 'LU' => 'Luxemburgo', 'MO' => 'Macau', 'MK' => 'Macedónia', 'MG' => 'Madagáscar', 'MW' => 'Malávi', 'MY' => 'Malásia', 'MV' => 'Maldivas', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Ilhas Marshall', 'MQ' => 'Martinica', 'MU' => 'Maurícia', 'MR' => 'Mauritânia', 'MX' => 'México', 'FM' => 'Micronésia', 'MD' => 'Moldávia', 'MC' => 'Mónaco', 'MN' => 'Mongólia', 'ME' => 'Montenegro', 'MA' => 'Marrocos', 'MZ' => 'Moçambique', 'MM' => 'Birmânia', 'NA' => 'Namíbia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Países Baixos', 'AN' => 'Antilhas Neerlandesas', 'NC' => 'Nova Caledónia', 'NZ' => 'Nova Zelândia', 'NI' => 'Nicarágua', 'NE' => 'Níger', 'NG' => 'Nigéria', 'XI' => 'Northern Ireland', 'MP' => 'Marianas do Norte', 'NO' => 'Noruega', 'OM' => 'Omã', 'PK' => 'Paquistão', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panamá', 'PG' => 'Papua-Nova Guiné', 'PY' => 'Paraguai', 'PE' => 'Peru', 'PH' => 'Filipinas', 'PL' => 'Polónia', 'PT' => 'Portugal', 'PR' => 'Porto Rico', 'QA' => 'Catar', 'CG' => 'Republic of the Congo', 'RN' => 'Réunion', 'RO' => 'Roménia', 'RU' => 'Rússia', 'RW' => 'Ruanda', 'ST' => 'São Tomé e Príncipe', 'KN' => 'São Cristóvão e Neves', 'LC' => 'Santa Lúcia', 'VC' => 'São Vicente e Granadinas', 'WS' => 'Samoa', 'SM' => 'São Marinho', 'SA' => 'Arábia Saudita', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seicheles', 'SL' => 'Serra Leoa', 'SG' => 'Singapura', 'SK' => 'Eslováquia', 'SI' => 'Eslovénia', 'SB' => 'Ilhas Salomão', 'SO' => 'Somália', 'ZA' => 'África do Sul', 'KR' => 'Coreia do Sul', 'ES' => 'Espanha', 'LK' => 'Sri Lanca', 'SD' => 'Sudão', 'SR' => 'Suriname', 'SZ' => 'Suazilândia', 'SE' => 'Suécia', 'CH' => 'Suíça', 'SY' => 'Síria', 'TW' => 'Taiwan', 'TJ' => 'Tajiquistão', 'TZ' => 'Tanzânia', 'TH' => 'Tailândia', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trindade e Tobago', 'TN' => 'Tunísia', 'TR' => 'Turquia', 'TM' => 'Turquemenistão', 'TV' => 'Tuvalu', 'VI' => 'Ilhas Virgens Americanas', 'UG' => 'Uganda', 'UA' => 'Ucrânia', 'AE' => 'Emiratos Árabes Unidos', 'GB' => 'Reino Unido', 'US' => 'Estados Unidos', 'UY' => 'Uruguai', 'UZ' => 'Usbequistão', 'VU' => 'Vanuatu', 'VA' => 'Vaticano', 'VE' => 'Venezuela', 'VN' => 'Vietname', 'XW' => 'Wales', 'YE' => 'Iémen', 'ZM' => 'Zâmbia', 'ZW' => 'Zimbabué' );
		$em_countries_array_i18n['ru'] = array ('AF' => 'Афганистан',  'AL' => 'Албания',  'DZ' => 'Алжир',    'AS' => 'Самоа (США)',        'AD' => 'Андорра', 'AO' => 'Ангола', 'AQ' => 'Антарктика',  'AG' => 'Антигуа и Барбуда',   'AR' => 'Аргентина',   'AM' => 'Армения',      'AW' => 'Аруба', 'AU' => 'Австралия',  'AT' => 'Австрия',    'AZ' => 'Азербайджан',   'BS' => 'Багамы',  'BH' => 'Бахрейн', 'BD' => 'Бангладеш',   'BB' => 'Барбадос', 'BY' => 'Беларусь',     'BE' => 'Бельгия',  'BZ' => 'Белиз',  'BM' => 'Bermuda', 'BJ' => 'Бенин', 'BT' => 'Бутан',   'BO' => 'Боливия',  'BA' => 'Босния-Герцеговина',      'BW' => 'Ботсвана', 'BR' => 'Бразилия',  'VG' => 'Вирджинские острова (Брит.)', 'BN' => 'Бруней',            'BG' => 'Болгария',   'BF' => 'Буркина-Фасо',  'BI' => 'Бурунди', 'CI' => 'Берег Слоновой Кости',              'KH' => 'Камбоджа',   'CM' => 'Камерун',  'CA' => 'Канада', 'CV' => 'Кабо-Верде',          'KY'=>'Каймановы острова',  'CF' => 'Центрально-Африканская Республика', 'TD' => 'Чад',    'CL' => 'Чили',  'CN' => 'Китай', 'CO' => 'Колумбия',  'KM' => 'Коморос',         'CR' => 'Коста Рикa', 'HR' => 'Хорватия',     'CU' => 'Куба', 'CY' => 'Кипр',   'CZ' => 'Чешская Республика',    'KP' => 'Северная Корея', 'CD' => 'Демократическая Республика Конго', 'DK' => 'Дания', 'DJ' => 'Джибути', 'DM' => 'Доминика', 'DO' => '	Доминиканская Республика', 'EC' => 'Эквадор', 'EG' => 'Египет', 'SV' => 'Сальвадор', 'XE' => 'Англия', 'GQ' => 'Экваториальная Гвинея', 'ER' => 'Эритрея', 'EE' => 'Эстония', 'ET' => 'Эфиопия','FO' => 'Фарерские острова', 'FJ' => 'Фиджи', 'FI' => 'Финляндия', 'FR' => 'Франция', 'PF' => '	Полинезия (Фр.)', 'GA' => 'Габон', 'GM' => 'Гамбия', 'GE' => 'Грузия', 'DE' => 'Германия', 'GH' => 'Гана', 'GR' => 'Греция', 'GL' => 'Гренландия', 'GD' => 'Гренада', 'GP' =>'Guadeloupe', 'GU' => 'Гуам (США)', 'GT' => 'Гватемала', 'GN' => 'Гвинея', 'GW' => 'Гвинея-Бисау', 'GY' => 'Гайана', 'HT' => 'Гаити', 'HN' => 'Гондурас', 'HK' => 'Гонг Конг', 'HU' => 'Венгрия', 'IS' => 'Исландия', 'IN' => 'Индия', 'ID' => 'Индонезия', 'IR' => 'Иран', 'IQ' => 'Ирак', 'IE' => 'Ирландия', 'IL' => 'Израйль', 'IT' => 'Италия', 'JE'=>'Jersey', 'JM' => 'Ямайка', 'JP' => 'Япония', 'JO' => 'Иордания', 'KZ' => 'Казахстан', 'KE' => 'Кения', 'KI' => 'Кирибати', 'KV' => 'Косово', 'KW' => 'Кувейт', 'KG' => 'Киргизия', 'LA' => 'Лаос', 'LV' => 'Латвия', 'LB' => 'Ливан', 'LS' => 'Лесото', 'LR' => 'Либерия', 'LY' => 'Либия', 'LI' => 'Лихтейнштейн', 'LT' => 'Литва', 'LU' => 'Люксембург', 'MO' => 'Макау', 'MK' => 'Македония', 'MG' => 'Мадагаскар', 'MW' => 'Малави', 'MY' => 'Малайзия', 'MV' => 'Мальдивы', 'ML' => 'Мали', 'MT' => 'Мальта', 'MH' => 'Маршальские острова', 'MQ' => 'Мартиника (Фр.)', 'MU' => 'Мавритий', 'MR' => 'Мавритания', 'MX' => 'Мексика', 'FM' => 'Микронезия', 'MD' => 'Молдова', 'MC' => 'Монако', 'MN' => 'Монголия', 'ME' => 'Черногория', 'MA' => 'Морокко', 'MZ' => 'Мозамбик', 'MM' => 'Бирма (Мьянма)', 'NA' => 'Намибия', 'NR' => 'Науру', 'NP' => 'Непал', 'NL' => 'Нидерланды', 'AN' => 'Антиллы (Нидерланды)', 'NC' => 'Новая Каледония (Фр.)', 'NZ' => 'Новая Зеландия', 'NI' => 'Никарагуаa', 'NE' => 'Нигер', 'NG' => 'Нигерия', 'XI' => 'Северной Ирландии', 'MP' => 'Северные Марианские острова', 'NO' => 'Норвегия', 'OM' => 'Оман', 'PK' => 'Пакистан', 'PW' => 'Палау', 'PS' => 'Палестина', 'PA' => 'Панама', 'PG' => 'Папуа Новая Гвинея', 'PY' => 'Парагвай', 'PE' => 'Перу', 'PH' => 'Филиппины', 'PL' => 'Польша', 'PT' => 'Португалия', 'PR' => 'Пуэрто-Рико', 'QA' => 'Катар', 'CG' => 'Конго', 'RN' => 'Réunion', 'RO' => 'Румыния', 'RU' => 'Россия', 'RW' => 'Руанда', 'ST' => 'Сан-Томе и Принсипи', 'KN' => 'Сент-Киттс Нэвис Ангуилла', 'LC' => 'Санта Лючия', 'VC' => 'Сент-Висент и Гренадины', 'WS' => 'Самоа', 'SM' => 'Сан-Марино', 'SA' => 'Саудовская Аравия', 'XS' => 'Шотландия', 'SN' => 'Сенегал', 'RS' => 'Сербии', 'SC' => 'Сейшеллы', 'SL' => 'Сьерра-Леоне', 'SG' => 'Сингапур', 'SK' => 'Словакия', 'SI' => 'Словения', 'SB' => 'Соломоновы острова', 'SO' => 'Сомали', 'ZA' => 'Южная Африка', 'KR' => 'Южная Корея', 'ES' => 'Испания', 'LK' => 'Шри Ланка', 'SD' => 'Судан', 'SR' => 'Суринам', 'SZ' => 'Свазиленд', 'SE' => 'Швеция', 'CH' => 'Швейцария', 'SY' => 'Сирия', 'TW' => 'Тайвань', 'TJ' => 'Таджикистан', 'TZ' => 'Танзания', 'TH' => 'Таиланд', 'TL' => 'Восточный Тимор', 'TG' => 'Того', 'TO' => 'Тонга', 'TT' => 'Тринидад и Тобаго', 'TN' => 'Тунис', 'TR' => 'Турция', 'TM' => 'Туркменистан', 'TV' => 'Тувалу', 'VI' => 'Вирджинские острова (США)', 'UG' => 'Уганда', 'UA' => 'Украина', 'AE' => 'Объединенные Арабские Эмираты', 'GB' => 'Великобритания', 'US' => 'США', 'UY' => 'Uruguay', 'UZ' => 'Узбекистан', 'VU' => 'Вануату', 'VA' => 'Ватикан', 'VE' => 'Венесуэла', 'VN' => 'Вьетнам', 'XW' => 'Уэльс', 'YE' => 'Йемен', 'ZM' => 'Замбия', 'ZW' => 'Зимбабве' );
		$em_countries_array_i18n['sv'] = array ('AF' => 'Afghanistan', 'AL' => 'Albanien', 'DZ' => 'Algeriet', 'AS' => 'Amerikanska Samoa',  'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarktis',   'AG' => 'Antigua och Barbuda', 'AR' => 'Argentina',   'AM' => 'Armenien',     'AW' => 'Aruba', 'AU' => 'Australien', 'AT' => 'Österrike',  'AZ' => 'Azerbajdzjan',  'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh',  'BB' => 'Barbados', 'BY' => 'Vitryssland',  'BE' => 'Belgien',  'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bhutan',  'BO' => 'Bolivia',  'BA' => 'Bosnien och Hercegovina', 'BW' => 'Botswana', 'BR' => 'Brasilien', 'VG' => 'Brittiska Jungfruöarna',      'BN' => 'Brunei',            'BG' => 'Bulgarien',  'BF' => 'Burkina Faso',  'BI' => 'Burundi', 'CI' => 'Elfenbenskusten',                   'KH' => 'Kambodja',   'CM' => 'Kamerun',  'CA' => 'Kanada', 'CV' => 'Kap Verde',           'KY'=>'Caymanöarna',        'CF' => 'Centralafrikanska republiken',      'TD' => 'Tchad',  'CL' => 'Chile', 'CN' => 'Kina',  'CO' => 'Colombia',  'KM' => 'Komorerna',       'CR' => 'Costa Rica', 'HR' => 'Kroatien',     'CU' => 'Kuba', 'CY' => 'Cypern', 'CZ' => 'Tjeckien',              'KP' => 'Nordkorea',  'CD' => 'Demokratiska republiken Kongo', 'DK' => 'Danmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominikanska republiken', 'EC' => 'Ecuador', 'EG' => 'Egypten', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Ekvatorialguinea', 'ER' => 'Eritrea', 'EE' => 'Estland', 'ET' => 'Etiopien','FO' => 'Färöarna', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'Frankrike', 'PF' => 'Franska Polynesien', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgien', 'DE' => 'Tyskland', 'GH' => 'Ghana', 'GR' => 'Grekland', 'GL' => 'Grönland', 'GD' => 'Grenada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Ungern', 'IS' => 'Island', 'IN' => 'Indien', 'ID' => 'Indonesien', 'IR' => 'Iran', 'IQ' => 'Irak', 'IE' => 'Irland', 'IL' => 'Israel', 'IT' => 'Italien', 'JE'=>'Jersey', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordanien', 'KZ' => 'Kazakstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirgizistan', 'LA' => 'Laos', 'LV' => 'Lettland', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyen', 'LI' => 'Liechtenstein', 'LT' => 'Litauen', 'LU' => 'Luxemburg', 'MO' => 'Macao', 'MK' => 'Makedonien', 'MG' => 'Madagaskar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldiverna', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshallöarna', 'MQ' => 'Martinique', 'MU' => 'Mauritius', 'MR' => 'Mauretanien', 'MX' => 'Mexiko', 'FM' => 'Mikronesien', 'MD' => 'Moldavien', 'MC' => 'Monaco', 'MN' => 'Mongoliet', 'ME' => 'Montenegro', 'MA' => 'Marocko', 'MZ' => 'Moçambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Nederländerna', 'AN' => 'Nederländska Antillerna', 'NC' => 'Nya Kaledonien', 'NZ' => 'Nya Zeeland', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Nordmarianerna', 'NO' => 'Norge', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua Nya Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Filippinerna', 'PL' => 'Polen', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Kongo', 'RN' => 'Réunion', 'RO' => 'Rumänien', 'RU' => 'Ryssland', 'RW' => 'Rwanda', 'ST' => 'São Tomé och Príncipe', 'KN' => '	Saint Christopher och Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent och Grenadinerna', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudiarabien', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychellerna', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakien', 'SI' => 'Slovenien', 'SB' => 'Salomonöarna', 'SO' => 'Somalia', 'ZA' => 'Sydafrika', 'KR' => 'Sydkorea', 'ES' => 'Spanien', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Surinam', 'SZ' => 'Swaziland', 'SE' => 'Sverige', 'CH' => 'Schweiz', 'SY' => 'Syrien', 'TW' => 'Taiwan', 'TJ' => 'Tadzjikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad och Tobago', 'TN' => 'Tunisien', 'TR' => 'Turkiet', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'Amerikanska Jungfruöarna', 'UG' => 'Uganda', 'UA' => 'Ukraina', 'AE' => 'Förenade Arabemiraten', 'GB' => 'Förenade kungariket', 'US' => 'Förenta staterna', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VA' => '	Heliga stolen', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['en'] = array ('AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BR' => 'Brazil', 'VG' => 'British Virgin Islands', 'BN' => 'Brunei', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'CI' => 'C&ocirc;te D\'Ivoire', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY'=>'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CR' => 'Costa Rica', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'KP' => 'Democratic People\'s Republic of Korea', 'CD' => 'Democratic Republic of the Congo', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia','FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'PF' => 'French Polynesia', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IL' => 'Israel', 'IT' => 'Italy', 'JE'=>'Jersey', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Laos', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Mauritania', 'MU' => 'Mauritius', 'MR' => 'Mauritania', 'MX' => 'Mexico', 'FM' => 'Micronesia', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RN' => 'Réunion', 'RO' => 'Romania', 'RU' => 'Russia', 'RW' => 'Rwanda', 'ST' => 'S&agrave;o Tom&eacute; And Pr&iacute;ncipe', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent and the Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi Arabia', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'KR' => 'South Korea', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syria', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'US Virgin Islands', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vatican', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		$em_countries_array_i18n['fi'] = array ('AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'Amerikan Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarctica', 'AG' => 'Antigua ja Barbuda', 'AR' => 'Argentiina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Itävalta', 'AZ' => 'Azerbaidžan', 'BS' => 'Bahama', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Valko-Venäjä', 'BE' => 'Belgia', 'BZ' => 'Belize', 'BM' => 'Bermuda', 'BJ' => 'Benin', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia ja Hertsegovina', 'BW' => 'Botswana', 'BR' => 'Brasilia', 'VG' => 'Brittiläiset Neitsytsaaret', 'BN' => 'Brunei', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'CI' => 'Norsunluurannikko', 'KH' => 'Kambodža', 'CM' => 'Kamerun', 'CA' => 'Kanada', 'CV' => 'Kap Verde', 'KY'=>'Caymansaaret', 'CF' => 'Keski-Afrikan tasavalta', 'TD' => 'Tšad', 'CL' => 'Chile', 'CN' => 'Kiina', 'CO' => 'Kolumbia', 'KM' => 'Komorit', 'CR' => 'Costa Rica', 'HR' => 'Kroatia', 'CU' => 'Kuuba', 'CY' => 'Kypros', 'CZ' => 'Tšekki', 'KP' => 'Korean demokraattinen kansantasavalta', 'CD' => 'Kongon demokraattinen tasavalta', 'DK' => 'Tanska', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominikaaninen tasavalta', 'EC' => 'Ecuador', 'EG' => 'Egypti', 'SV' => 'El Salvador', 'XE' => 'Englanti', 'GQ' => 'Päiväntasaajan Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Etiopia','FO' => 'Färsaaret', 'FJ' => 'Fiji', 'FI' => 'Suomi', 'FR' => 'Ranska', 'PF' => 'Ranskan Polynesia', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Saksa', 'GH' => 'Ghana', 'GR' => 'Kreikka', 'GL' => 'Grönlanti', 'GD' => 'Grenada', 'GP' =>'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Unkari', 'IS' => 'Islanti', 'IN' => 'Intia', 'ID' => 'Indonesia', 'IR' => 'Iran', 'IQ' => 'Irak', 'IE' => 'Irlanti', 'IL' => 'Israel', 'IT' => 'Italia', 'JE'=>'Jersey', 'JM' => 'Jamaika', 'JP' => 'Japani', 'JO' => 'Jordania', 'KZ' => 'Kazakstan', 'KE' => 'Kenia', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kirgisistan', 'LA' => 'Laos', 'LV' => 'Latvia', 'LB' => 'Libanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libya', 'LI' => 'Liechtenstein', 'LT' => 'Lietua', 'LU' => 'Luxemburg', 'MO' => 'Macao', 'MK' => 'Makedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malesia', 'MV' => 'Maldiivit', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshallinsaaret', 'MQ' => 'Mauritania', 'MU' => 'Mauritius', 'MR' => 'Mauritania', 'MX' => 'Mexico', 'FM' => 'Micronesia', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MA' => 'Morokko', 'MZ' => 'Mozambik', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Alankomaat', 'AN' => 'Alankomaiden Antillit', 'NC' => 'Uusi-Kaledonia', 'NZ' => 'Uusi-Seelanti', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Pohjois-Irlanti', 'MP' => 'Pohjois-Mariaanit', 'NO' => 'Norja', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestina', 'PA' => 'Panama', 'PG' => 'Papua Uusi-Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Filippiinit', 'PL' => 'Puola', 'PT' => 'Portugali', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Kongo', 'RN' => 'Réunion', 'RO' => 'Romania', 'RU' => 'Venäjä', 'RW' => 'Ruanda', 'ST' => 'São Tomé ja Príncipe', 'KN' => 'Saint Kitts jad Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent ja Grenadiinit', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi Arabia', 'XS' => 'Skotlanti', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Salomonsaaret', 'SO' => 'Somalia', 'ZA' => 'Etelä-Afrikka', 'KR' => 'Etelä Korea', 'ES' => 'Espanja', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swazimaa', 'SE' => 'Ruotsi', 'CH' => 'Sveitsi', 'SY' => 'Syria', 'TW' => 'Taiwan', 'TJ' => 'Tadžikistan', 'TZ' => 'Tansania', 'TH' => 'Thaimaa', 'TL' => 'Itä Timor', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad ja Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkki', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'Yhdysvaltain Neitsytsaaret', 'UG' => 'Uganda', 'UA' => 'Ukraina', 'AE' => 'Arabiemiirikunnat', 'GB' => 'Yhdistynyt kuningaskunta', 'US' => 'Yhdysvallat', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vatikaani', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Sambia', 'ZW' => 'Zimbabwe' );
		$lang = substr(get_locale(), 0, 2);
		if( array_key_exists($lang, $em_countries_array_i18n) ){
			$em_countries_array = $em_countries_array_i18n[$lang];
		}else{
			$em_countries_array = $em_countries_array_i18n['en'];
		}
	}
	if($sort){ asort($em_countries_array); }
	if($add_blank !== false){
		if(is_array($add_blank)){
			$em_countries_array = $add_blank + $em_countries_array;
		}else{
		    $em_countries_array = array(0 => $add_blank) + $em_countries_array;
		}
	}
	return apply_filters('em_get_countries', $em_countries_array);
}

/**
 * Returns an array of scopes available to events manager. Hooking into this function's em_get_scopes filter will allow you to add scope options to the event pages.
 */
function em_get_scopes(){
	global $wp_locale;
	$start_of_week = get_option('start_of_week');
	$end_of_week_name = $start_of_week > 0 ? $wp_locale->get_weekday($start_of_week-1) : $wp_locale->get_weekday(6);
	$start_of_week_name = $wp_locale->get_weekday($start_of_week);
	$scopes = array(
		'all' => __('All events','events-manager'),
		'future' => __('Future events','events-manager'),
		'past' => __('Past events','events-manager'),
		'today' => __('Today\'s events','events-manager'),
		'tomorrow' => __('Tomorrow\'s events','events-manager'),
		'week' => sprintf(__('Events this whole week (%s to %s)','events-manager'), $wp_locale->get_weekday_abbrev($start_of_week_name), $wp_locale->get_weekday_abbrev($end_of_week_name)),
		'this-week' => sprintf(__('Events this week (today to %s)','events-manager'), $wp_locale->get_weekday_abbrev($end_of_week_name)),
		'month' => __('Events this month','events-manager'),
		'this-month' => __('Events this month (today onwards)', 'events-manager'),
		'next-month' => __('Events next month','events-manager'),
		'1-months'  => __('Events current and next month','events-manager'),
		'2-months'  => __('Events within 2 months','events-manager'),
		'3-months'  => __('Events within 3 months','events-manager'),
		'6-months'  => __('Events within 6 months','events-manager'),
		'12-months' => __('Events within 12 months','events-manager')
	);
	return apply_filters('em_get_scopes',$scopes);
}

function em_get_currencies(){
	$currencies = new stdClass();
	$currencies->names = array('EUR' => 'EUR - Euros','USD' => 'USD - U.S. Dollars','GBP' => 'GBP - British Pounds','CAD' => 'CAD - Canadian Dollars','AUD' => 'AUD - Australian Dollars','BRL' => 'BRL - Brazilian Reais','CZK' => 'CZK - Czech koruna','DKK' => 'DKK - Danish Kroner','HKD' => 'HKD - Hong Kong Dollars','HUF' => 'HUF - Hungarian Forints','ILS' => 'ILS - Israeli New Shekels','JPY' => 'JPY - Japanese Yen','MYR' => 'MYR - Malaysian Ringgit','MXN' => 'MXN - Mexican Pesos','TWD' => 'TWD - New Taiwan Dollars','NZD' => 'NZD - New Zealand Dollars','NOK' => 'NOK - Norwegian Kroner','PHP' => 'PHP - Philippine Pesos','PLN' => 'PLN - Polish Zlotys','SGD' => 'SGD - Singapore Dollars','SEK' => 'SEK - Swedish Kronor','CHF' => 'CHF - Swiss Francs','THB' => 'THB - Thai Baht','TRY' => 'TRY - Turkish Liras', 'RUB'=>'RUB - Russian Ruble');
	$currencies->symbols = array( 'EUR' => '&euro;','USD' => '$','GBP' => '&pound;','CAD' => '$','AUD' => '$','BRL' => 'R$','CZK' => 'K&#269;','DKK' => 'kr','HKD' => '$','HUF' => 'Ft','JPY' => '&#165;','MYR' => 'RM','MXN' => '$','TWD' => '$','NZD' => '$','NOK' => 'kr','PHP' => 'Php', 'PLN' => '&#122;&#322;','SGD' => '$','SEK' => 'kr','CHF' => 'CHF','TRY' => 'TL','RUB'=>'&#8381;');
	$currencies->true_symbols = array( 'EUR' => '€','USD' => '$','GBP' => '£','CAD' => '$','AUD' => '$','BRL' => 'R$','CZK' => 'Kč','DKK' => 'kr','HKD' => '$','HUF' => 'Ft','JPY' => '¥','MYR' => 'RM','MXN' => '$','TWD' => '$','NZD' => '$','NOK' => 'kr','PHP' => 'Php','PLN' => 'zł','SGD' => '$','SEK' => 'kr','CHF' => 'CHF','TRY' => 'TL', 'RUB'=>'₽');
	return apply_filters('em_get_currencies',$currencies);
}

function em_get_currency_formatted($price, $currency=false, $format=false, $precision = 2){
	$formatted_price = '';
	if(!$format) $format = get_option('dbem_bookings_currency_format','@#');
	if(!$currency) $currency = get_option('dbem_bookings_currency');
	if( empty($price) ) $price = 0;
	$formatted_price = str_replace('#', number_format( $price, $precision, get_option('dbem_bookings_currency_decimal_point','.'), get_option('dbem_bookings_currency_thousands_sep',',') ), $format);
	$formatted_price = str_replace('@', em_get_currency_symbol(true,$currency), $formatted_price);
	return apply_filters('em_get_currency_formatted', $formatted_price, $price, $currency, $format);
}

function em_get_currency_symbol($true_symbol = false, $currency = false){
	if( !$currency ) $currency = get_option('dbem_bookings_currency');
	if($true_symbol){
		return em_get_currencies()->true_symbols[$currency];
	}
	return apply_filters('em_get_currency_symbol', em_get_currencies()->symbols[$currency]);
}

function em_get_currency_name($currency = false){
	if( !$currency ) $currency = get_option('dbem_bookings_currency');
	return apply_filters('em_get_currency_name', em_get_currencies()->names[$currency]);
}

function em_get_hour_format(){
	return get_option('dbem_time_24h') ? "H:i":"h:i A";
}

function em_get_days_names(){
	return array (1 => translate( 'Mon' ), 2 => translate( 'Tue' ), 3 => translate( 'Wed' ), 4 => translate( 'Thu' ), 5 => translate( 'Fri' ), 6 => translate( 'Sat' ), 0 => translate( 'Sun' ) );
}

/**
 * Works like check_admin_referrer(), but also in public mode. If in admin mode, it triggers an error like in check_admin_referrer(), if outside admin it just exits with an error.
 * @param string $action
 */
function em_verify_nonce($action, $nonce_name='_wpnonce'){
	if( is_admin() ){
		if( !wp_verify_nonce($_REQUEST[$nonce_name], $action) ) check_admin_referer('trigger_error');
	}else{
		if( !wp_verify_nonce($_REQUEST[$nonce_name], $action) ) exit( __('Trying to perform an illegal action.','events-manager') );
	}
}

/**
 * Since WP 4.5 em_wp_get_referer() returns false if URL is the same. We use it to get a safe referrer url, so we use the new wp_get_raw_referer() argument instead.
 * @since 5.6.3
 * @return string 
 */
function em_wp_get_referer(){
	if( function_exists('wp_get_raw_referer') ){
		//do essentially what em_wp_get_referer does, but potentially returning the same url as before
		return wp_validate_redirect(wp_get_raw_referer(), false );
	}else{
		return wp_get_referer();
	}
}

/**
 * Gets all WP users
 * @return array
 */
function em_get_wp_users( $args = array(), $extra_users = array() ) {
	global $wpdb;
	if( !empty($args) ){
	    $users = get_users($args);
	}else{
	    //added as a temp fix for http://core.trac.wordpress.org/ticket/23609, we need to make some sort of autocompleter search for users instead
	    $users = $wpdb->get_results("SELECT ID, display_name FROM {$wpdb->users} ORDER BY display_name");
	}
	$indexed_users = array();
	foreach($users as $user){
		$indexed_users[$user->ID] = $user->display_name;
	}
 	return $extra_users + $indexed_users;
}

function em_get_attributes($lattributes = false){
	$attributes = array('names'=>array(), 'values'=>array());
	if( !$lattributes && !get_option('dbem_attributes_enabled') ) return $attributes;
	if( $lattributes && !get_option('dbem_location_attributes_enabled') ) return $attributes;
	//We also get a list of attribute names and create a ddm list (since placeholders are fixed)
	$formats =
		get_option ( 'dbem_placeholders_custom' ).
		get_option ( 'dbem_location_placeholders_custom' ).
		get_option ( 'dbem_full_calendar_event_format' ).
		get_option ( 'dbem_rss_description_format' ).
		get_option ( 'dbem_rss_title_format' ).
		get_option ( 'dbem_map_text_format' ).
		get_option ( 'dbem_location_baloon_format' ).
		get_option ( 'dbem_location_event_list_item_format' ).
		get_option ( 'dbem_location_page_title_format' ).
		get_option ( 'dbem_event_list_item_format' ).
		get_option ( 'dbem_event_page_title_format' ).
		get_option ( 'dbem_single_event_format' ).
		get_option ( 'dbem_calendar_large_pill_format' ).
		get_option ( 'dbem_single_location_format' );
	//We now have one long string of formats, get all the attribute placeholders
	if( $lattributes ){
		preg_match_all('/#_LATT\{([^}]+)\}(\{([^}]+)\})?/', $formats, $matches);
	}else{
		preg_match_all('/#_ATT\{([^}]+)\}(\{([^}]+)\})?/', $formats, $matches);
	}
	//Now grab all the unique attributes we can use in our event.
	foreach($matches[1] as $key => $attribute) {
		if( !in_array($attribute, $attributes['names']) ){
			$attributes['names'][] = $attribute ;
			$attributes['values'][$attribute] = array();
		}
		//check if there's ddm values
		if( !empty($matches[3][$key]) ){
		    $new_values = explode('|',$matches[3][$key]);
		    if( count($new_values) > count($attributes['values'][$attribute]) ){
		    	foreach($new_values as $key => $value){
		    	    $new_values[$key] = trim($value);
		    	}
				$attributes['values'][$attribute] = apply_filters('em_get_attributes_'.$attribute, $new_values, $attribute, $matches);
		    }
		}
	}
	return apply_filters('em_get_attributes', $attributes, $matches, $lattributes);
}

/**
 * Decides whether to register a user based on a certain booking that is to be added
 * @param EM_Booking $EM_Booking 
 */
function em_booking_add_registration( $EM_Booking ){
    global $EM_Notices;
    //Does this user need to be registered first?
    $registration = true;
    if( ((!is_user_logged_in() && get_option('dbem_bookings_anonymous')) || EM_Bookings::is_registration_forced()) && !get_option('dbem_bookings_registration_disable') ){
		// Check if this is a tentative booking, meaning the booking should be made, user checks should be done, but an account isn't created until moved out of this status into an approved or pending status.
    	//find random username - less options for user, less things go wrong
    	$user_email = trim(wp_unslash($_REQUEST['user_email'])); //otherwise may fail validation
    	$username_root = explode('@', wp_kses_data($user_email));
    	$username_root = $username_rand = sanitize_user($username_root[0], true);
    	while( username_exists($username_rand) ) {
    		$username_rand = $username_root.rand(1,1000);
    	}
    	$_REQUEST['dbem_phone'] = (!empty($_REQUEST['dbem_phone'])) ? wp_kses_data($_REQUEST['dbem_phone']):''; //fix to prevent warnings
    	$_REQUEST['user_name'] = (!empty($_REQUEST['user_name'])) ? wp_kses_data($_REQUEST['user_name']):''; //fix to prevent warnings
    	$user_data = array('user_login' => $username_rand, 'user_email'=> $user_email, 'user_name'=> $_REQUEST['user_name'], 'dbem_phone'=> $_REQUEST['dbem_phone']);
    	$id = em_register_new_user($user_data);
    	if( is_numeric($id) ){
    		$EM_Person = new EM_Person($id);
    		$EM_Booking->person_id = $id;
    		$feedback = get_option('dbem_booking_feedback_new_user');
    		$EM_Notices->add_confirm( $feedback );
    		add_action('em_bookings_added', 'em_new_user_notification');
    	}else{
    		$registration = false;
    		if( is_object($id) && get_class($id) == 'WP_Error'){
    			/* @var $id WP_Error */
    			if( $id->get_error_code() == 'email_exists' ){
    				$EM_Notices->add_error( get_option('dbem_booking_feedback_email_exists') );
    			}else{
    				$EM_Notices->add_error( $id->get_error_messages() );
    			}
    		}else{
    			$EM_Notices->add_error( get_option('dbem_booking_feedback_reg_error') );
    		}
    	}
    }elseif( (!is_user_logged_in() || EM_Bookings::is_registration_forced()) && get_option('dbem_bookings_registration_disable') ){
    	//Validate name, phone and email
    	if( $EM_Booking->get_person_post() ){
	    	//Save default person to booking
	    	$EM_Booking->person_id = 0;
    	}else{
    	    $registration = false;
    	}
    }elseif( !is_user_logged_in() ){
    	$registration = false;
    	$EM_Notices->add_error( get_option('dbem_booking_feedback_log_in') );
    }elseif( empty($EM_Booking->person_id) ){ //user must be logged in, so we make this person the current user id
    	$EM_Booking->person_id = get_current_user_id();
    }
    return apply_filters('em_booking_add_registration_result', $registration, $EM_Booking, $EM_Notices);
}

/**
 * Copied straight from wp-login.php, only change atm is a function renaming.
 * Handles registering a new user.
 *
 * @param array associative array of user values to insert
 * @return int|WP_Error Either user's ID or error on failure.
 */
function em_register_new_user( $user_data, $tentative = false ) {
	$user_data = apply_filters('em_register_new_user_pre',$user_data);
	$errors = new WP_Error();
	if( !empty($user_data['user_name']) ){
		$name = explode(' ', $user_data['user_name']);
		$user_data['first_name'] = array_shift($name);
		$user_data['last_name'] = implode(' ',$name);
	}
	$sanitized_user_login = sanitize_user( $user_data['user_login'] );
	$user_data['user_login'] = $sanitized_user_login;
	$user_email = apply_filters( 'user_registration_email', $user_data['user_email'] );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.', 'events-manager') );
	} elseif ( ! validate_username( $user_data['user_login'] ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'events-manager') );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered, please choose another one.', 'events-manager') );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.', 'events-manager') );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'events-manager') );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.', 'events-manager') );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	//custom registration filter to prevent things like SI Captcha and other plugins of this kind interfering with EM
	$errors = apply_filters( 'em_registration_errors', $errors, $sanitized_user_login, $user_email );
	
	if ( $errors->get_error_code() ) return $errors;

	
	if(empty($user_data['user_pass'])){
		$user_data['user_pass'] =  wp_generate_password( 12, false);
	}

	if( $tentative ) {
		// return user data which can be used later for registration
		return $user_data;
	}
	
	$user_id = wp_insert_user( $user_data );
	if ( is_numeric( $user_id ) && ! empty( $user_data['dbem_phone'] ) ) {
		update_user_meta( $user_id, 'dbem_phone', $user_data['dbem_phone'] );
	}
	
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'events-manager' ), get_option( 'admin_email' ) ) );
		
		return $errors;
	}
	
	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.
	
	global $em_temp_user_data;
	$em_temp_user_data            = $user_data; //for later useage
	$em_temp_user_data['user_id'] = $user_id;
	
	return apply_filters('em_register_new_user',$user_id);
}

/**
 * Notify the blog admin of a new user, normally via email.
 *
 * @since 2.0
 */
function em_new_user_notification() {
	global $em_temp_user_data;
	$user_id = $em_temp_user_data['user_id'];
	$plaintext_pass = $em_temp_user_data['user_pass'];

	//if you want you can disable this email from going out, and will still consider registration as successful.
	if( get_option('dbem_email_disable_registration') ){ return true;  }

	//Copied out of /wp-includes/pluggable.php
	$user = new WP_User($user_id);

	$user_login = wp_unslash($user->user_login);
	$user_email = wp_unslash($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your blog %s:', 'events-manager'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s', 'events-manager'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s', 'events-manager'), $user_email) . "\r\n";
	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration', 'events-manager'), $blogname), $message);

	if ( empty($plaintext_pass) )
		return;

	//send email to user
	$message = get_option('dbem_bookings_email_registration_body');
	if( em_locate_template('emails/new-user.php') ){
		ob_start();
		em_locate_template('emails/new-user.php', true);
		$message = ob_get_clean();
	}
	//for WP 4.4, regenerate password link can be used
	$set_password_url = '';
	if( function_exists('get_password_reset_key')){
	    $key = get_password_reset_key( $user );
	    if( is_wp_error($key) ){
	    	$set_password_url = __('Contact a site administrator for your password.', 'events-manager');
	    }else{
		    $set_password_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
	    }
	}
    $message  = str_replace(array('%password%','%username%','%passwordurl%'), array($plaintext_pass, $user_login, $set_password_url), $message);
	global $EM_Mailer;
	return $EM_Mailer->send(get_option('dbem_bookings_email_registration_subject'), $message, $user_email);
}

/**
 * Transitional function to handle WP's eventual move away from the is_super_user() function 
 */
function em_wp_is_super_admin( $user_id = false ){
	$user = ( ! $user_id || $user_id == get_current_user_id() ) ? wp_get_current_user() : get_userdata( $user_id );

	if ( ! $user || ! $user->exists() ) return false;

	if ( is_multisite() ) {
		if( $user->has_cap('manage_network_options') ) return true;
	} else {
		if ( $user->has_cap('delete_users') ) return true;
	}
	return false;
}

/**
 * Returns an array of flags that are used in search forms.
 * @return array
 */
function em_get_search_form_defaults($base_args = array(), $context = 'events') {
	if (!is_array($base_args)) $base_args = array();
	$search_args = array();
	$search_args['ajax'] = EM_AJAX_SEARCH;
	$search_args['id'] = rand();
	$search_args['css'] = get_option('dbem_css_search'); // deprecated
	$search_args['search_action'] = 'search_events';
	$search_args['search_advanced_text'] = get_option('dbem_search_form_advanced_show');
	$search_args['search_text_show'] = get_option('dbem_search_form_advanced_show'); // deprecated
	$search_args['search_text_hide'] = get_option('dbem_search_form_advanced_hide'); // deprecated
	$search_args['search_button'] = get_option('dbem_search_form_submit');
	$search_args['saved_searches'] = get_option('dbem_search_form_saved_searches', true);
	$search_args['search_advanced_style'] = get_option('dbem_search_form_advanced_style', 'accordion'); // how to show the dropdowns in the advanced section
	$search_args['search_multiselect_style'] = $search_args['search_advanced_style'] === 'accordion' ? 'always-open' : 'multidropdown' ; // how to show the dropdowns in the advanced section
	// sorting options
	$search_args['sorting'] = get_option('dbem_search_form_sorting'); // is sorting enabled
	//search text
	$search_args['search'] = ''; //default search term
	$search_args['search_term'] = $search_args['search_term_main'] = get_option('dbem_search_form_text');
	$search_args['search_term_label'] = get_option('dbem_search_form_text_label'); //field label
	$search_args['search_term_advanced'] = get_option('dbem_search_form_text_advanced'); // show in main form?
	$search_args['search_term_label_advanced'] = get_option('dbem_search_form_text_label_advanced'); //field label
	//geo and units
	$search_args['geo'] = '';  //default geo search term (requires 'near' as well for it to make sense)
	$search_args['near'] = ''; //default near search params
	$search_args['search_geo'] = get_option('dbem_search_form_geo'); // show geo search?
	$search_args['geo_label'] = get_option('dbem_search_form_geo_label'); //field label
	$search_args['search_geo_advanced'] = get_option('dbem_search_form_geo_advanced'); // show geo search in advanced?
	$search_args['geo_label_advanced'] = get_option('dbem_search_form_geo_label_advanced'); // show geo search?
	$search_args['search_geo_units'] = get_option('dbem_search_form_geo_units'); //field label
	$search_args['geo_units_label'] = get_option('dbem_search_form_geo_units_label'); //field label
	$search_args['near_unit'] = get_option('dbem_search_form_geo_unit_default'); //default distance unit
	$search_args['near_distance'] = get_option('dbem_search_form_geo_distance_default'); //default distance amount
	$search_args['geo_distance_values'] = explode(',', get_option('dbem_search_form_geo_distance_options')); //possible distance values
	//scope
	$search_args['scope'] = array('', '', 'name' => 'all'); //default scope term
	$search_args['search_scope'] = get_option('dbem_search_form_dates'); // show in main form
	$search_args['scope_label'] = get_option('dbem_search_form_dates_label'); //field label
	$search_args['scope_seperator'] = get_option('dbem_search_form_dates_separator'); //field label
	$search_args['scope_format'] = get_option('dbem_search_form_dates_format'); //field label
	$search_args['search_scope_advanced'] = get_option('dbem_search_form_dates_advanced'); // show in advanced form?
	$search_args['scope_label_advanced'] = get_option('dbem_search_form_dates_label_advanced'); // advanced field label
	$search_args['scope_seperator_advanced'] = get_option('dbem_search_form_dates_separator_advanced'); // advanced field label
	$search_args['scope_format_advanced'] = get_option('dbem_search_form_dates_format_advanced'); // advanced field label
	//eventful locations
	$search_args['search_eventful_main'] = true;
	$search_args['search_eventful'] = true;
	$search_args['search_eventful_locations_label'] = esc_html__('Eventful Locations?', 'events-manager');
	$search_args['search_eventful_locations_tooltip'] = esc_html__('Display only locations with upcoming events.', 'events-manager');
	//categories
	$search_args['category'] = 0; //default search term
	$search_args['search_categories'] = get_option('dbem_search_form_categories');
	$search_args['category_label'] = get_option('dbem_search_form_category_label'); //field label
	$search_args['categories_label'] = get_option('dbem_search_form_categories_label'); //select default
	$search_args['categories_placeholder'] = get_option('dbem_search_form_categories_placeholder'); // advanced search placeholder
	$search_args['categories_clear_text'] = esc_html__('Clear Selected'); // advanced search placeholder
	$search_args['categories_count_text'] = esc_html__('%d Selected'); // advanced search placeholder
	$search_args['categories_include'] = get_option('dbem_search_form_categories_include'); // include/exclude filters of categories to show
	$search_args['categories_exclude'] = get_option('dbem_search_form_categories_exclude'); // include/exclude filters of categories to hide
	// tags
	$search_args['tag'] = 0; //default search term
	$search_args['search_tags'] = get_option('dbem_search_form_tags');
	$search_args['tag_label'] = get_option('dbem_search_form_tag_label'); //field label
	$search_args['tags_label'] = get_option('dbem_search_form_tags_label'); //select default
	$search_args['tags_placeholder'] = get_option('dbem_search_form_tags_placeholder'); // advanced search placeholder
	$search_args['tags_clear_text'] = esc_html__('Clear Selected'); // advanced search placeholder
	$search_args['tags_count_text'] = esc_html__('%d Selected'); // advanced search placeholder
	$search_args['tags_include'] = get_option('dbem_search_form_tags_include'); // include/exclude filters of tags to show
	$search_args['tags_exclude'] = get_option('dbem_search_form_tags_exclude'); // include/exclude filters of tags to hide
	//countries
	$search_args['search_countries'] = get_option('dbem_search_form_countries');
	$search_args['country'] = $search_args['search_countries'] ? get_option('dbem_search_form_default_country') : ''; //default country
	$search_args['country_label'] = get_option('dbem_search_form_country_label'); //field label
	$search_args['countries_label'] = get_option('dbem_search_form_countries_label'); //select default
	//regions
	$search_args['region'] = ''; //default region
	$search_args['search_regions'] = get_option('dbem_search_form_regions');
	$search_args['region_label'] = get_option('dbem_search_form_region_label'); //field label
	//states
	$search_args['state'] = ''; //default state
	$search_args['search_states'] = get_option('dbem_search_form_states');
	$search_args['state_label'] = get_option('dbem_search_form_state_label'); //field label
	//towns
	$search_args['town'] = ''; //default state
	$search_args['search_towns'] = get_option('dbem_search_form_towns');
	$search_args['town_label'] = get_option('dbem_search_form_town_label'); //field label
	//sections to show
	$search_args['show_main'] = get_option('dbem_search_form_main');
	$search_args['show_advanced'] = get_option('dbem_search_form_advanced') && ( $search_args['search_categories'] || $search_args['search_tags'] || $search_args['search_countries'] || $search_args['search_regions'] || $search_args['search_states'] || $search_args['search_towns']);
	$search_args['advanced_mode'] = get_option('dbem_search_form_advanced_mode') === 'inline' ? 'inline':'modal';
	$search_args['advanced_hidden'] = $search_args['show_advanced'] && get_option('dbem_search_form_advanced_hidden');
	$search_args['advanced_trigger'] = !( $search_args['advanced_hidden'] && get_option('dbem_search_form_advanced_trigger') && $search_args['advanced_mode'] === 'inline' ) && $search_args['show_advanced'];
	
	// disable certain things based on context, can be overriden by $base_args, not necessarily recommended
	if( $context == 'locations' ){
		$location_views = em_get_location_search_views();
		$search_args['views'] = array_intersect(array_keys($location_views), get_option('dbem_search_form_views'));
		$search_args['view'] = get_option('dbem_search_form_view');
		if( empty($location_views[$search_args['view']]) ){
			$search_args['view'] = key($location_views);
		}
		$search_args['search_categories'] = false;
		$search_args['search_scope_main'] = false;
		$search_args['search_scope'] = false;
		$search_args['search_tags'] = false;
	}else{
		// default is events
		$search_args['views'] = get_option('dbem_search_form_views');
		$search_args['view'] = get_option('dbem_search_form_view');
		// disable these non-event searches
		$search_args['search_eventful_main'] = false;
		$search_args['search_eventful'] = false;
	}
	
	//merge defaults with supplied arguments
	$args = array_merge($search_args, $base_args);
	
	// forcse some settings if supplied settings require display decisions
	if( $args['show_main'] ) {
		$show_main = ! empty( $args['search_term'] ) || ! empty( $args['search_geo'] ) || ! empty( $args['search_scope'] ); //decides whether or not to show main area and collapseable advanced search options
		$args['show_main'] = $show_main;
	}
	if( !$args['show_main'] && !empty($args['show_search']) ){
		// we're not showing main but are showing the form itself, therefore we must show advanced search
		$args['advanced_mode'] = 'inline';
		$args['advanced_hidden'] = false;
	}elseif( $args['advanced_mode'] === 'modal' ){
		// modal always starts as hidden
		$args['advanced_hidden'] = true;
	}
	
	
	// sanitize views option
	$search_views = em_get_search_views();
	if( !is_array($args['views']) ) $args['views'] = explode(',', str_replace(' ', '', $args['views']));
	$args['views'] = array_intersect( $args['views'], array_keys($search_views) );
	if( empty($search_views[$args['view']]) ) $args['view'] = 'list';
	
	//add specific classes for wrapper dependent on settings
	if (empty($args['css_classes'])){
		$args['css_classes'] = array();
	}elseif( !is_array($args['css_classes']) ){
		$args['css_classes'] = explode(',', $args['css_classes']);
	}
	if (empty($args['main_classes'])){ // deprecated - legacy backcompat
		$args['main_classes'] = array();
	}elseif( !is_array($args['main_classes']) ){
		$args['main_classes'] = explode(',', $args['main_classes']);
	}
	if( !empty($args['css']) ){
		$args['main_classes'][] = 'css-search'; // deprecated
	}
	
	// legacy backwards compatible classes
	$args['main_classes'][] = 'em-search-legacy';
	if( !empty($args['search_term']) ) $args['main_classes'][] = 'has-search-term';
	if( !empty($args['search_geo']) ) $args['main_classes'][] = 'has-search-geo';
	$args['main_classes'][] = $args['show_main'] ? 'has-search-main':'no-search-main';
	$args['main_classes'][] = $args['show_advanced'] ? 'has-advanced':'no-advanced';
	$args['main_classes'][] = $args['advanced_hidden'] ? 'advanced-hidden':'advanced-visible';
	
	// new classes
	$main_search_count = 0; // number of search fields
	$args['css_classes'][] = $args['show_main'] ? 'has-search-main':'no-search-main';
	$args['css_classes'][] = !empty($args['views']) && count($args['views']) > 1 ? 'has-views':'no-views';
	$args['css_classes'][] = $args['sorting'] ? 'has-sorting':'no-sorting';
	$args['css_classes'][] = $args['show_advanced'] ? 'has-advanced':'no-advanced';
	if( $args['show_advanced'] ){
		$args['css_classes'][] = 'advanced-mode-' . $args['advanced_mode'];
		$args['css_classes'][] = $args['advanced_hidden'] ? 'advanced-hidden':'advanced-visible';
		$args['css_classes'][] = $args['advanced_trigger'] ? 'has-advanced-trigger' : 'no-advanced-trigger';
	}

	if( isset($args['show_search']) && !$args['show_search'] ){
		$args['css_classes'][] = 'is-hidden';
	}
	$args['css_classes_advanced'] = array();
	if( empty($args['advanced_hidden']) ){
		$args['css_classes_advanced'][] = ' visible';
	}
	$args['css_classes'][] = get_option('dbem_search_form_responsive', 'one-line');
	
	//overwrite with $_REQUEST defaults in event of a submitted search
	if( isset($_REQUEST['view_id']) ) $args['id'] = absint($_REQUEST['view_id']); // id used for element ids
	if( isset($_REQUEST['id']) ) $args['id'] = absint($_REQUEST['id']); // id used for element ids
	if( isset($_REQUEST['geo']) ) $args['geo'] = sanitize_text_field($_REQUEST['geo']);
	if( isset($_REQUEST['near']) ) $args['near'] = sanitize_text_field(wp_unslash($_REQUEST['near']));
	if( isset($_REQUEST['em_search']) ) $args['search'] = sanitize_text_field(wp_unslash($_REQUEST['em_search']));
	if( isset($_REQUEST['category']) ) $args['category'] = sanitize_text_field($_REQUEST['category']);
	if( isset($_REQUEST['country']) ) $args['country'] = sanitize_text_field(wp_unslash($_REQUEST['country']));
	if( isset($_REQUEST['region']) ) $args['region'] = sanitize_text_field(wp_unslash($_REQUEST['region']));
	if( isset($_REQUEST['state']) ) $args['state'] = sanitize_text_field(wp_unslash($_REQUEST['state']));
	if( isset($_REQUEST['town']) ) $args['town'] = sanitize_text_field(wp_unslash($_REQUEST['town']));
	if( isset($_REQUEST['near_unit']) ) $args['near_unit'] = sanitize_text_field($_REQUEST['near_unit']);
	if( isset($_REQUEST['near_distance']) ) $args['near_distance'] = sanitize_text_field($_REQUEST['near_distance']);
	// fix scope so it's search-friendly, scopes must have a 0 and 1 key for start/end dates, and optionally an associated name
	if( !empty($args['scope']) ){
		if( !is_array($args['scope']) ){
			// convert currently supported scope into array, future conversions should be done elsewhere in another function
			if( $args['scope'] == 'future' ){
				// future consideration... but currently the commented lines would set a date on search form by default
				//$EM_DateTime = new EM_DateTime;
				//$args['scope'] = array( 0 => $EM_DateTime->format('Y-m-d'), 1 => '', 'name' => 'future', );
				$args['scope'] = array('', '', 'name' => 'future'); //default scope term
			}else{
				$args['scope'] = array('', '', 'name' => 'all'); //default scope term
			}
		}elseif( empty($args['scope']['name']) ){
			$scope_array = array($args['scope'][0]);
			if( !empty($args['scope'][1]) ) $scope_array[1] = $args['scope'][1];
			$args['scope']['name'] = implode(',', $scope_array);
		}
	}else{
		if( !empty($_REQUEST['scope']) && !is_array($_REQUEST['scope'])){
			$args['scope'] = explode(',',sanitize_text_field($_REQUEST['scope'])); //convert scope to an array in event of pagination
		}elseif( !empty($_REQUEST['scope']) ){
			$args['scope'] = array(); // reset and populate sanitized
			foreach( $_REQUEST['scope'] as $k => $v ){
				$args['scope'][absint($k)] = sanitize_text_field($v);
			}
		}
	}
	return apply_filters('em_get_search_form_defaults', $args, $base_args, $context);
}

/**
 * Adds hidden inputs to the search form in the event certain fields are hidden but also required (e.g. a category but no category search enabled)
 * @param $args
 *
 * @since 6.4
 * @return void
 */
function em_search_form_footer( $args ){
	$show_advanced = !empty($args['show_advanced']);
	$i = array();
	// search terms on main bar and advanced main section
	if( empty($args['search_term']) && $show_advanced && empty($args['search_term_advanced']) && !empty($args['search']) ){
		$i['search'] = $args['search'];
	};
	if( empty($args['search_geo']) && $show_advanced && empty($args['search_geo_advanced']) ){
		$i['geo'] = !empty($args['geo']) ? $args['geo'] : null;
		$i['near'] = !empty($args['near']) ? $args['near'] : null;
		$i['near_distance'] = !empty($args['near_distance']) ? $args['near_distance'] : null;
		$i['near_unit'] = !empty($args['near_unit']) ? $args['near_unit'] : null;
	}
	if( empty($args['search_scope']) && $show_advanced && empty($args['search_scope_advanced']) && !empty($args['scope']) ){
		$scope = array();
		if( is_array($args['scope']) ){
			if( !empty($args['scope'][0]) ){
				$scope[] = $args['scope'][0];
			}
			if( !empty($args['scope'][1]) ){
				if( empty($scope) ) {
					$scope[] = '';
				}
				$scope[] = $args['scope'][1];
			}
			if( empty($scope) && !empty($args['scope']['name']) ){
				$scope = $args['scope']['name'];
			}
		}else{
			$scope = $args['scope'];
		}
		$i['scope'] = $scope;
	}
	// location fields
	$address_fields = array('countries' => 'country', 'regions' => 'region', 'states' => 'state', 'towns' => 'town');
	foreach( $address_fields as $search => $field )
	if( ($show_advanced || empty($args['search_'.$search])) && !empty($args[$field]) ){
		$i[$field] = $args[$field];
	}
	// taxonomies
	if( (!$show_advanced || empty($args['search_categories'])) && !empty($args['category']) ){
		$i['category'] = $args['category'];
	}
	if( (!$show_advanced || empty($args['search_tags'])) && !empty($args['tag']) ){
		$i['tag'] = $args['tag'];
	}
	// put it all together, output hidden inputs, escaped etc.
	foreach( $i as $name => $value ){
		if( $value !== null ) {
			if( is_array($value) ){
				$value = implode(',', $value);
			}
			echo '<input type="hidden" name="' . $name . '" value="' . esc_attr( $value ) . '">';
		}
	}
}
add_action('em_search_form_footer', 'em_search_form_footer', 10, 1);

function em_get_search_views(){
	$search_views = array(
		'list' => array(
			'name' => __('List', 'events-manager'),
		),
		'list-grouped' => array(
			'name' => __('Grouped Lists', 'events-manager'),
		),
		'grid' => array(
			'name' => __('Grid', 'events-manager'),
		),
		'map' => array(
			'name' => __('Map', 'events-manager'),
		),
		'calendar' => array(
			'name' => __('Calendar', 'events-manager'),
		),
	);
	return apply_filters('em_get_search_views', $search_views);
}

function em_output_events_view( $args, $view = null ){
	if( $view === null ){
		$view = empty($args['view']) ? get_option('dbem_search_form_view') : $args['view'];
	}
    do_action('em_output_events_view_header', $args, $view);
	switch( $view ){
		case 'list-grouped':
			if( empty($args['date_format']) ){
				$args['date_format'] = get_option('dbem_event_list_groupby_format');
			}
			em_locate_template('templates/events-list-grouped.php', true, array('args'=>$args)); //if successful, this template overrides the settings and defaults, including search
			break;
		case 'list':
			em_locate_template('templates/events-list.php', true, array('args'=>$args)); //if successful, this template overrides the settings and defaults, including search
			break;
		case 'grid':
			// add default grid formats
			if( empty($args['format']) ){
				$args['format'] = get_option( 'dbem_event_grid_item_format' );
			}
			if( empty($args['format_header']) ){
				$args['format_header'] = get_option('dbem_event_grid_format_header');
			}
			if( empty($args['format_footer']) ){
				$args['format_footer'] = get_option('dbem_event_grid_format_footer');
			}
			em_locate_template('templates/events-grid.php', true, array('args'=>$args)); //if successful, this template overrides the settings and defaults, including search
			break;
		case 'map':
			$args['width'] = '100%';
			$args['height'] = 0;
			echo em_get_events_map_shortcode( $args );
			break;
		case 'calendar':
			$args['has_search'] = false; // prevent view and search getting output again
			echo EM_Calendar::output( $args );
			break;
		default:
			if( has_action('em_events_search_view_'.$view) ){
				do_action('em_events_search_view_'.$view, $args);
			}else{
				// last resort we're showing a list
				em_locate_template('templates/events-list.php', true, array('args'=>$args)); //if successful, this template overrides the settings and defaults, including search
			}
			break;
	}
	do_action('em_output_events_view_footer', $args, $view);
}

function em_get_location_search_views(){
	$search_views = array(
		'list' => array(
			'name' => __('List', 'events-manager'),
		),
		'grid' => array(
			'name' => __('Grid', 'events-manager'),
		),
		'map' => array(
			'name' => __('Map', 'events-manager'),
		),
	);
	return apply_filters('em_get_location_search_views', $search_views);
}

function em_output_locations_view( $args, $view = null ){
	if( $view === null ){
		$view = empty($args['view']) ? get_option('dbem_search_form_view') : $args['view'];
		$location_views = em_get_location_search_views();
		if( empty($location_views[$view]) ){
			$args['view'] = key($location_views);
			$view = $args['view'];
		}
	}
	$args['limit'] = !empty($args['limit']) ? $args['limit'] : get_option('dbem_locations_default_limit');
	switch( $view ){
		case 'list':
			em_locate_template('templates/locations-list.php', true, array('args'=>$args)); //if successful, this template overrides the settings and defaults, including search
			break;
		case 'grid':
			// add default grid formats
			if( empty($args['format']) ){
				$args['format'] = get_option( 'dbem_location_grid_item_format' );
			}
			if( empty($args['format_header']) ){
				$args['format_header'] = get_option('dbem_location_grid_format_header');
			}
			if( empty($args['format_footer']) ){
				$args['format_footer'] = get_option('dbem_location_grid_format_footer');
			}
			em_locate_template('templates/locations-grid.php', true, array('args'=>$args)); //if successful, this template overrides the settings and defaults, including search
			break;
		case 'map':
			$args['width'] = '100%';
			$args['height'] = 0;
			$args['limit'] = 0;
			echo em_get_locations_map_shortcode( $args );
			break;
		default:
			if( has_action('em_locations_search_view_'.$view) ){
				do_action('em_locations_search_view_'.$view, $args);
			}else{
				// last resort we're showing a list
				em_locate_template('templates/locations-list.php', true, array('args'=>$args)); //if successful, this template overrides the settings and defaults, including search
			}
			break;
	}
}

/*
 * UI Helpers
 * previously dbem_UI_helpers.php functions
 */

function em_option_items($array, $saved_value) {
	$output = "";
	foreach($array as $key => $item) {
		$selected ='';
		if ($key == $saved_value)
			$selected = "selected='selected'";
		$output .= "<option value='".esc_attr($key)."' $selected >".esc_html($item)."</option>\n";

	}
	echo $output;
}

function em_checkbox_items($name, $array, $saved_values, $horizontal = true) {
	$output = "";
	foreach($array as $key => $item) {
		$checked = "";
		if (in_array($key, $saved_values)) $checked = "checked";
		$output .= "<label><input type='checkbox' name='".esc_attr($name)."' value='".esc_attr($key)."' $checked > ".esc_html($item)."</label>&nbsp; ";
		if(!$horizontal)
			$output .= "<br/>\n";
	}
	echo $output;

}

function em_options_input_get_value( $name, $default = '' ){
	if( preg_match('/^([^\[]+)\[([^\]]+)?\]$/', $name, $matches) ){
		$value = EM_Options::get($matches[2], $default, $matches[1]);
	}elseif( preg_match('/^([^\[]+)\[([^\]]+)\]\[([^\]]+)?\]$/', $name, $matches) ){
		$value_array = EM_Options::get($matches[2], array(), $matches[1]);
		$value = isset($value_array[$matches[3]]) ? $value_array[$matches[3]]:$default;
	}else{
		$value = get_option($name, $default);
	}
	return $value;
}

function em_options_input_text($title, $name, $description ='', $default='', $resetable = false) {
    $translate = EM_ML::is_option_translatable($name);
	$value = em_options_input_get_value( $name, $default );
	?>
	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
		<th scope="row">
			<?php echo esc_html($title); ?>
			<?php if( $resetable ): ?>
				<a href="#" class="em-option-resettable em-tooltip" aria-label="<?php esc_attr_e('Reset to default value?', 'events-manager'); ?>" data-name="<?php echo esc_attr($name) ?>" data-nonce="<?php echo wp_create_nonce('option-default-'.$name); ?>">
					<span class="dashicons dashicons-undo"></span>
				</a>
			<?php endif; ?>
		</th>
	    <td>
			<input name="<?php echo esc_attr($name) ?>" type="text" id="<?php echo esc_attr($name) ?>" value="<?php echo esc_attr($value, ENT_QUOTES); ?>" size="45" />
	    	<?php if( $translate ): ?><span class="em-translatable dashicons dashicons-admin-site"></span><?php endif; ?>
	    	<br />
			<?php 
				if( $translate ){
					echo '<div class="em-ml-options"><table class="form-table">';
					foreach( EM_ML::get_langs() as $lang => $lang_name ){
						if( $lang != EM_ML::$wplang ){
							?>
							<tr>
								<td class="lang"><?php echo $lang_name; ?></td>
								<td class="lang-text"><input name="<?php echo esc_attr($name) ?>_ml[<?php echo $lang ?>]" type="text" id="<?php echo esc_attr($name.'_'.$lang) ?>" style="width: 100%" value="<?php echo esc_attr(EM_ML::get_option($name, $lang, false), ENT_QUOTES); ?>" size="45" /></td>
							</tr>
							<?php
						}else{
							$default_lang = '<input name="'.esc_attr($name).'_ml['.EM_ML::$wplang.']" type="hidden" id="'. esc_attr($name.'_'. EM_ML::$wplang) .'" value="'. esc_attr($value, ENT_QUOTES).'" />';
						}
					}
					echo '</table>';
					echo '<em>'.__('If translations are left blank, the default value will be used.','events-manager').'</em>';
					echo $default_lang.'</div>';
				}
			?>
			<em><?php echo $description; ?></em>
		</td>
	</tr>
	<?php
}

function em_options_input_password($title, $name, $description ='') {
	$value = em_options_input_get_value( $name );
	?>
	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
		<th scope="row"><?php echo esc_html($title); ?></th>
	    <td>
			<input name="<?php echo esc_attr($name) ?>" type="password" id="<?php echo esc_attr($title) ?>" style="width: 95%" value="<?php echo esc_attr($value); ?>" size="45" /><br />
			<em><?php echo $description; ?></em>
		</td>
	</tr>
	<?php
}

function em_options_textarea($title, $name, $description ='', $resetable = false) {
	$translate = EM_ML::is_option_translatable($name);
	?>
	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
		<th scope="row">
			<?php echo esc_html($title); ?>
			<?php if( $resetable ): ?>
			<a href="#" class="em-option-resettable em-tooltip" aria-label="<?php esc_attr_e('Reset to default value?', 'events-manager'); ?>" data-name="<?php echo esc_attr($name) ?>" data-nonce="<?php echo wp_create_nonce('option-default-'.$name); ?>">
				<span class="dashicons dashicons-undo"></span>
			</a>
			<?php endif; ?>
		</th>
		<td>
			<textarea name="<?php echo esc_attr($name) ?>" id="<?php echo esc_attr($name) ?>" rows="6"><?php echo esc_attr(get_option($name), ENT_QUOTES);?></textarea>
	        <?php if( $translate ): ?><span class="em-translatable  dashicons dashicons-admin-site"></span><?php endif; ?>
	        <br />
			<?php
				if( $translate ){
					echo '<div class="em-ml-options"><table class="form-table">';
					foreach( EM_ML::get_langs() as $lang => $lang_name ){
						if( $lang != EM_ML::$wplang ){
							?>
							<tr>
								<td class="lang"><?php echo $lang_name; ?></td>
								<td class="lang-text"><textarea name="<?php echo esc_attr($name) ?>_ml[<?php echo $lang ?>]" id="<?php echo esc_attr($name.'_'.$lang) ?>" style="width: 100%" size="45"><?php echo esc_attr(EM_ML::get_option($name, $lang, false), ENT_QUOTES); ?></textarea></td>
							</tr>
							<?php
						}else{
							$default_lang = '<input name="'.esc_attr($name).'_ml['.EM_ML::$wplang.']" type="hidden" id="'. esc_attr($name.'_'. EM_ML::$wplang) .'" value="'. esc_attr(get_option($name), ENT_QUOTES).'" />';
						}
					}
					echo '</table>';
					echo '<em>'.__('If left blank, the default value will be used.','events-manager').'</em>';
					echo $default_lang.'</div>';
				}
			?>
			<em><?php echo $description; ?></em>
		</td>
	</tr>
	<?php
}

function em_options_radio($name, $options, $title='') {
		$option = get_option($name);
		?>
	   	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
	   		<?php if( !empty($title) ): ?>
	   		<th scope="row"><?php  echo esc_html($title); ?></th>
	   		<td>
	   		<?php else: ?>
	   		<td colspan="2">
	   		<?php endif; ?>
	   			<table>
	   			<?php foreach($options as $value => $text): ?>
	   				<tr>
	   					<td><input id="<?php echo esc_attr($name) ?>_<?php echo esc_attr($value); ?>" name="<?php echo esc_attr($name) ?>" type="radio" value="<?php echo esc_attr($value); ?>" <?php if($option == $value) echo "checked"; ?> /></td>
	   					<td><?php echo $text ?></td>
	   				</tr>
				<?php endforeach; ?>
				</table>
			</td>
	   	</tr>
<?php
}

function em_options_radio_binary($title, $name, $description='', $option_names = '', $trigger='', $untrigger=false) {
	if( empty($option_names) ) $option_names = array(0 => __('No','events-manager'), 1 => __('Yes','events-manager'));
	if( preg_match('/^(.+)\[([a-z_A-Z0-9\-]+)\]$/', $name, $match ) ){
		// deal with an option stored as an array
		$name_data = get_option($match[1]);
		$value = !empty($name_data[$match[2]]);
		$id = $match[1].'-'.$match[2];
		$class = $match[1];
	}else{
		$id = $name;
		$class = $name;
		if( substr($name, 0, 7) == 'dbem_ms' ){
			$value = get_site_option($name);
		}else{
			$value = get_option($name);
		}
	}
	if( $untrigger ){
		$trigger_att = ($trigger) ? ' data-trigger="'.esc_attr($trigger).'" class="em-untrigger"':'';
	}else{
		$trigger_att = ($trigger) ? ' data-trigger="'.esc_attr($trigger).'" class="em-trigger"':'';
	}
	?>
   	<tr valign="top" class="<?php echo $class ?>_row" id='<?php echo $id;?>_row'>
   		<th scope="row"><?php echo esc_html($title); ?></th>
   		<td class="<?php echo $class; ?>">
   			<?php echo $option_names[1]; ?> <input id="<?php echo esc_attr($id) ?>_yes" name="<?php echo esc_attr($name) ?>" type="radio" value="1" <?php if($value) echo "checked"; echo $trigger_att; ?> />&nbsp;&nbsp;&nbsp;
			<?php echo $option_names[0]; ?> <input id="<?php echo esc_attr($id) ?>_no" name="<?php echo esc_attr($name) ?>" type="radio" value="0" <?php if(!$value) echo "checked"; echo $trigger_att; ?> />
			<br/><em><?php echo $description; ?></em>
		</td>
   	</tr>
	<?php
}

function em_options_select($title, $name, $list, $description='', $default='', $triggers = array(), $options = array() ) {
	$option_value = get_option($name, $default);
	if( $name == 'dbem_events_page' && !is_object(get_page($option_value)) ){
		$option_value = 0; //Special value
	}
	$select_classes = array();
	if( !empty($triggers) ) $select_classes[] = 'em-trigger';
	if( !empty($options['selectize']) ) $select_classes[] = 'em-selectize';
	if( !empty($options['multiple']) ){
		$name .= '[]';
		if( !is_array($option_value) ){
			$option_value = array($option_value);
		}
	}
	?>
   	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
   		<th scope="row"><?php echo esc_html($title); ?></th>
   		<td>
			<select name="<?php echo esc_attr($name); ?>" class="<?php echo implode(' ', $select_classes); ?>" <?php if( !empty($options['multiple']) ) echo 'multiple'; ?>>
				<?php 
				foreach($list as $key => $value) {
					if( is_array($value) ){
						?><optgroup label="<?php echo $key; ?>"><?php
						foreach( $value as $key_group => $value_group ){
							$trigger = !empty( $triggers[$key_group] ) ? $triggers[$key_group] : '';
							?>
			 				<option value='<?php echo esc_attr($key_group) ?>' <?php echo ("$key_group" == $option_value) ? "selected='selected' " : ''; ?> data-trigger="<?php echo esc_attr($trigger); ?>">
			 					<?php echo esc_html($value_group); ?>
			 				</option>
							<?php 
						}
						?></optgroup><?php
					}else{
						$trigger = !empty( $triggers[$key] ) ? $triggers[$key] : '';
						if( !empty($options['multiple']) ) {
							$selected = in_array($key, $option_value) ? "selected='selected' ":'';
						} else {
							$selected = ("$key" == $option_value) ? "selected='selected' " : '';
						}
						?>
		 				<option value='<?php echo esc_attr($key) ?>' <?php echo $selected; ?> data-trigger="<?php echo esc_attr($trigger); ?>">
		 					<?php echo esc_html($value); ?>
		 				</option>
						<?php 
					} 
				}
				?>
			</select> <br/>
			<em><?php echo $description; ?></em>
		</td>
   	</tr>
	<?php
}
// got from http://davidwalsh.name/php-email-encode-prevent-spam
function em_ascii_encode($e){
	$output = '';
    for ($i = 0; $i < strlen($e); $i++) { $output .= '&#'.ord($e[$i]).';'; }
    return $output;
}

/**
 * Handy shortcut similar to constant() which checks if a constant exists and returns the value but does not trigger an error if not defined.
 * @param $constant
 *
 * @return mixed|null
 */
function em_constant( $constant ){
	if( defined($constant) ){
		return constant($constant);
	}
	return null;
}

if( !function_exists( 'is_main_query' ) ){
	/**
	 * Substitutes the original function in 3.3 onwards, for backwards compatability (only created if not previously defined)
	 * @return bool
	 */
	function is_main_query(){ global $wp_query; return $wp_query->in_the_loop == true; }
}

/**
 * deprecated
 * @return string
 */
function em_get_date_format(){
	return get_option('dbem_date_format');
}

/**
 * Backwards compatibility fuction to trigger the deprecated em_bookings_form_footer action if the newly introduced action em_booking_form_buttons_header is fired.
 * This will ensure any previous code using this action will still trigger appropriately, because we can assume the template was replaced with a new one containing em_booking_form_buttons_header and not em_booking_form_footer
 * @param $EM_Event
 * @return void
 */
function em_bookings_form_footer_backcompat( $EM_Event ){
	/**
	 * Do not use, this is for backwards compatibility only
	 * @deprecated
	 */
	do_action('em_booking_form_footer', $EM_Event);
}
add_action('em_booking_form_buttons_header', 'em_bookings_form_footer_backcompat', 10, 1);

/**
 * @param EM_Event $EM_Event
 * @return void
 */
function em_bookings_form_footer_polyfill( $EM_Event ) {
	if( !doing_action('em_booking_form_buttons_header') ){
		// we're in backcompat mode for a template, so let's output the intent just in case
		echo $EM_Event->get_bookings()->get_intent_default()->output_intent_html();
	}
}
add_action('em_booking_form_footer', 'em_bookings_form_footer_polyfill');

/**
 * Backwards compatibility for functionality added in EM Pro 3.1 which was moved into EM, ensuring versions of EM Pro removing this code will not duplicate headers.
 * If you still want this header to be handled via EM Pro (in the event you override booking form templates and use an outdated Pro version), remove the action below at the plugins_loaded action like so:
 * add_action('plugins_loaded', function(){ remove_action('em_pro_loaded', 'em_bookings_form_confirm_header_backcompat'); });
 * @return void
 */
function em_bookings_form_confirm_header_backcompat(){
	remove_action('em_checkout_form_footer',  array('EM_Booking_Form', 'booking_form_section_confirm_mb'), 1);
	remove_action('em_booking_form_before_confirm', array('EM_Booking_Form', 'booking_form_section_confirm'), 1);
	remove_action('em_booking_form_footer', array('EM_Booking_Form', 'booking_form_section_confirm'), 1);
	add_action('em_booking_form_before_buttons', function( $EM_Event ){
		do_action('em_booking_form_footer_before_buttons', $EM_Event);
	});
	add_action('em_booking_form_before_confirm', function( $EM_Event ){
		do_action('em_booking_form_before_confirmation', $EM_Event);
	});
	add_action('em_booking_form_after_confirm', function( $EM_Event ){
		do_action('em_booking_form_after_confirmation', $EM_Event);
	});
}
add_action('em_pro_loaded', 'em_bookings_form_confirm_header_backcompat');
?>