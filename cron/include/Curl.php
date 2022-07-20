<?php	
class Curl
{
	public static $cookieFolder = ROOTDIR . '/data/cookies';
	public static $curl_options = 
		array(
			CURLOPT_RETURNTRANSFER    => 1,
			CURLOPT_BINARYTRANSFER    => 1,
			CURLOPT_CONNECTTIMEOUT    => 45,
			CURLOPT_TIMEOUT            => 90,
			CURLOPT_USERAGENT        => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36 OPR/26.0.1656.60',
			CURLOPT_VERBOSE            => 0,
//			CURLOPT_STDERR            => null,
			CURLOPT_HEADER            => 0,
			CURLOPT_FOLLOWLOCATION    => 1,
			CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_0,  // лечение от transfer closed with 10088805 bytes remaining to read
			CURLOPT_SSL_VERIFYPEER    => 0,
			CURLOPT_SSL_VERIFYHOST    => 0,
			CURLOPT_MAXREDIRS        => 7, 
			CURLOPT_AUTOREFERER        => 1,
			CURLOPT_HTTPHEADER        => array(
				"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
				"Accept-Language: en-US,en;q=0.5",
				"Connection: keep-alive",
			)
		);

	public static function exec($url, $cParam = false, $postdata='', $posttype='post', $saveCookies = false, &$redirect=null)
	{
		$ch = self::init($url, $cParam, $postdata, $posttype, $saveCookies);
		$res = curl_exec($ch);
		if (curl_errno ($ch)) 
		{
			echo curl_error($ch);
			curl_close($ch);
			return false;
		}
		$redirect = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);
		return $res;
	}
	
	public static function init($url, $cParam = false, $postdata='', $posttype='post', $saveCookies = true)
	{
		$ch = curl_init();
		$options = (is_array($cParam)) 
				? $cParam + self::$curl_options 
				: self::$curl_options;
		$options[CURLOPT_URL] = $url; 
		if (!isset($options[CURLOPT_USERAGENT]))
			$options[CURLOPT_USERAGENT] = self::pickUserAgent();
		if ($postdata)  {
			if ($posttype == 'post') 
			{
				if (is_array($postdata)) 		
					$postdata = http_build_query($postdata);
				$options[CURLOPT_HTTPHEADER][] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"; 
				$options[CURLOPT_HTTPHEADER][] = "Content-Length: " . strlen($postdata); 
				$options[CURLOPT_POST] = 1; 				
				$options[CURLOPT_POSTFIELDS] = $postdata; 	
			} 
			elseif (strtolower($posttype) == 'get') 
			{
				if (is_array($postdata))
					$postdata = json_encode($postdata);
				$options[CURLOPT_POST] = 0;
				//$options[CURLOPT_CUSTOMREQUEST] = "POST";	
				$options[CURLOPT_POSTFIELDS] = $postdata; 	
				$options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
				$options[CURLOPT_HTTPHEADER][] = 'Content-Length: ' . strlen($postdata);
			}
			else
			{
				if (is_array($postdata))
					$postdata = json_encode($postdata);
				$options[CURLOPT_POST] = 1; 				
				$options[CURLOPT_CUSTOMREQUEST] = "POST";	
				$options[CURLOPT_POSTFIELDS] = $postdata; 	
				$options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
				$options[CURLOPT_HTTPHEADER][] = 'Content-Length: ' . strlen($postdata);
			}
		}
		//echo "</pre>";print_r($postdata);
		curl_setopt_array($ch, $options);
		if (VERBOSE_CURL_PARAM === true)
			print_r($options);
		return $ch;
	}
	
}