<?php

//Sanitize form data
if(!function_exists('sanitize_form_data')){
	function sanitize_form_data(){
		$keeptags = false;
		
		if(count($_REQUEST) > 0){
			foreach($_REQUEST as $key=>$data){
				if(is_array($data)){
					foreach($data as $key2=>$data2){
						if(is_array($data2)){
							foreach($data2 as $key3=>$data3){
								$keeptags = (isset($_POST['keep_tags']) && in_array($key2, $_POST['keep_tags']) ? true : false);
								$_REQUEST[$key][$key2][$key3] = strip_data($data3,$keeptags);
							}
						}else{
							$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
							$_REQUEST[$key][$key2] = strip_data($data2,$keeptags);
						}
					}
				}else{
					$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
					$_REQUEST[$key] = strip_data($data,$keeptags);
				}
			}
		}
		if(count($_GET) > 0){
			foreach($_GET as $key=>$data){
				if(is_array($data)){
					foreach($data as $key2=>$data2){
						if(is_array($data2)){
							foreach($data2 as $key3=>$data3){
								$keeptags = (isset($_POST['keep_tags']) && in_array($key2, $_POST['keep_tags']) ? true : false);
								$_GET[$key][$key2][$key3] = strip_data($data3,$keeptags);
							}
						}else{
							$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
							$_GET[$key][$key2] = strip_data($data2,$keeptags);
						}
					}
				}else{
					$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
					$_GET[$key] = strip_data($data,$keeptags);
				}
			}
		}
		if(count($_POST) > 0){
			foreach($_POST as $key=>$data){
				if(is_array($data)){
					foreach($data as $key2=>$data2){
						if(is_array($data2)){
							foreach($data2 as $key3=>$data3){
								$keeptags = (isset($_POST['keep_tags']) && in_array($key2, $_POST['keep_tags']) ? true : false);
								$_POST[$key][$key2][$key3] = strip_data($data3,$keeptags);
							}
						}else{
							$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
							$_POST[$key][$key2] = strip_data($data2,$keeptags);
						}
					}
				}else{
					$keeptags = (isset($_POST['keep_tags']) && in_array($key, $_POST['keep_tags']) ? true : false);
					$_POST[$key] = strip_data($data,$keeptags);
				}
			}
		}
	}
}

//Strip harmful elements
if(!function_exists('strip_data')){
	function strip_data($string, $keeptags=false){
		$string = (trim($string) != '' ? str_replace("'", "&rsquo;", stripslashes(trim($string))) : '');
		return (!$keeptags ? strip_tags($string) : $string);
	}
}

//Generate random hash
if(!function_exists('gen_random_string')){
	function gen_random_string(){
		$length = 50;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';    
		for ($p = 0; $p < $length; $p++) {
			@$string .= $characters[mt_rand(0, strlen($characters))];
		}
		return md5($string);
	}
}

//Validate email
if(!function_exists('checkmail')){
	function checkmail($string){
		return preg_match("/^[^\s()<>@,;:\"\/\[\]?=]+@\w[\w-]*(\.\w[\w-]*)*\.[a-z]{2,}$/i",$string);
	}
}

//generate timezone list
if(!function_exists('timezone_list')){
	function timezone_list() {
	    static $timezones = null;
	
	    if ($timezones === null) {
	        $timezones = [];
	        $offsets = [];
	        $now = new DateTime();
	
	        foreach (DateTimeZone::listIdentifiers() as $timezone) {
	            $now->setTimezone(new DateTimeZone($timezone));
	            $offsets[] = $offset = $now->getOffset();
	            $timezones[$timezone] = '(' . format_GMT_offset($offset) . ') ' . format_timezone_name($timezone);
	        }
	
	        array_multisort($offsets, $timezones);
	    }
	
	    return $timezones;
	}
}
if(!function_exists('format_GMT_offset')){
	function format_GMT_offset($offset) {
	    $hours = intval($offset / 3600);
	    $minutes = abs(intval($offset % 3600 / 60));
	    return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
	}
}
if(!function_exists('format_timezone_name')){
	function format_timezone_name($name) {
	    $name = str_replace('/', ', ', $name);
	    $name = str_replace('_', ' ', $name);
	    $name = str_replace('St ', 'St. ', $name);
	    return $name;
	}
}

//Sort pages array
if(!function_exists('build_pages_sitemap')){
	function build_pages_sitemap($pages_arr=array(), $parent_id=0){	
		global $lvl; //track depth of pages array
		$row = array();
		foreach($pages_arr as $page){
			if($page['parent_id'] == $parent_id){
				$lvl++;
				$children = build_pages_sitemap($pages_arr, $page['page_id']);
				if($children){
					$row[$page['page_id']]['sub_pages'] = $children;
					foreach($children as $child){
						$row[$child['page_id']] = $child;
					}
				}
				$lvl--;
				$row[$page['page_id']] = $page;
				$row[$page['page_id']]['lvl'] = $lvl;
			}
		}
		return $row;
	}
}

//Delete directory (recursive)
if(!function_exists('delete_directory')){
	function delete_directory($dirname) {
		if (is_dir($dirname))
			chmod($dirname, 0777);
			$dir_handle = opendir($dirname);
		if (!$dir_handle)
			return false;
		while($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				chmod($dirname."/".$file, 0777);
				if (!is_dir($dirname."/".$file))
				unlink($dirname."/".$file);
				else
				delete_directory($dirname.'/'.$file);    
			}
		}
		chmod($dir_handle, 0775);
		closedir($dir_handle);
		rmdir($dirname);
	}
}

//Generate clean url from string
if(!function_exists('clean_url')){
	function clean_url($str){
		$clean = trim($str);
		$clean = str_replace("&rsquo;", "", $clean);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = removeStopWords($clean);
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
		$clean = strtolower(trim($clean, '-'));
	
		return $clean;
	}
}

//Remove stop words for SEO purposes
if(!function_exists('removeStopWords')){
	function removeStopWords($input){
		$stopwords = array("a", "am", "and", "any", "are", "at", "be", "but", "by", "can", "cant", "do", "else", "etc", "for", "go", "had", "has", "he", "her", "how", "ie", "if", "in", "is", "it", "its", "my", "not", "now", "of", "on", "or", "put", "so", "than", "that", "the", "then", "there", "this", "those", "to", "too", "us", "very", "was", "well", "what", "when", "who", "why", "with", "you", "your", "the");
		return preg_replace('/\b('.implode('|',$stopwords).')\b/','',$input);
	}
}

//Check for special characters and spaces
if(!function_exists('check_special_chars')){
	function check_special_chars($string){
		if(!preg_match("#^[-A-Za-z\&0-9\&\_;' .]*$#",$string)){
			return true;
		}
		if(strstr($string, " ")){
			return true;
		}
		return false;	
	}
}

//Gravatar image
if(!function_exists('renderGravatar')) {
	function renderGravatar($image){
		global $path;
		if(is_file($image)){
			$dims = getimagesize($image);
			if ($dims[0]>=$dims[1]){
				$img = "<img src='".$path.$image."' style='max-height:40px' />";						
			}else{					
				$img = "<img src='".$path.$image."' style='max-width: 40px;' />";
			}		
		}else{
			$img = '';
		}
		return "<div class='gravatar'><div>" .$img. "</div></div>";
	}	
}

//Sitemap xml
function sitemapXML() {
			
	global $db;
	global $siteurl;
	global $root;
		
	$sitepages = array();
	
	$filename = $_SERVER['DOCUMENT_ROOT'].$root."sitemap.xml";	
	$doc = new DOMDocument('1.0');
	$doc->formatOutput = true;
	
	$segment = $doc->createElement("urlset");
	$segment = $doc->appendChild($segment);
	$segment->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
	$segment->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
	$segment->setAttribute("xsi:schemaLocation", "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd");
	
	$query = $db->query("SELECT * FROM pages WHERE page_id > 2 ORDER BY ordering");
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$pages[$row['page_id']] = $row;
			$pages[$row['page_id']]['sub_pages'] = array();
			$pages[$row['page_id']]['page_url'] = $siteurl.$root.$row['page'].'/';
		}
		$lvl = 1; //tracking depth of pages array
		$pages = build_pages_sitemap($pages);
		foreach($pages as $page_id => &$page){
			if($page['showhide'] != 2 && (!$page['parent_id'] || $pages[$page['parent_id']]['showhide'] != 2)){
				if($page['parent_id'] && array_key_exists($page['parent_id'], $pages)){
					$pages[$page['parent_id']]['sub_pages'][$page_id] = &$page;
					$pages[$page['parent_id']]['sub_pages'][$page_id]['page_url'] = $pages[$page['parent_id']]['page_url'].$page['page'].'/';
					$pages[$page['parent_id']]['sub_pages'][$page_id]['meta_title'] = $page['meta_title'].' | '.$pages[$page['parent_id']]['meta_title'];
				}
								
				$sitepage = array();
				$sitepage['url'] = $page['page_url'];
				$sitepage['lastmod'] = date('c',strtotime($page['last_modified']));
				$sitepage['priority'] = ($page['parent_id'] ? "0.64" : "0.80");
				if($page['page_id'] == 3){
					$sitepage['url'] = $siteurl;
					$sitepage['priority'] = "1.00";
				}
				if($page['type'] == 0){
					array_push($sitepages, $sitepage);
				}
				
				//Dynamic elements here....
												
			}
		}
		
	}
			
	//build xml
	foreach($sitepages as $s){
		$url = $doc->createElement("url");
		$url = $segment->appendChild($url);
		
		$loc = $doc->createElement("loc");
		$loc = $url->appendChild($loc);
		$text = $doc->createTextNode($s['url']);
		$text = $loc->appendChild($text);
		
		$lastmod = $doc->createElement("lastmod");
		$lastmod = $url->appendChild($lastmod);
		$text = $doc->createTextNode($s['lastmod']);
		$text = $lastmod->appendChild($text);
		
		$changefreq = $doc->createElement("changefreq");
		$changefreq = $url->appendChild($changefreq);
		$text = $doc->createTextNode("weekly");
		$text = $changefreq->appendChild($text);
		
		$priority = $doc->createElement("priority");
		$priority = $url->appendChild($priority);
		$text = $doc->createTextNode($s['priority']);
		$text = $priority->appendChild($text);
	}
	
	$doc->save($filename);
			
}

/*if(!function_exists('smtpEmail')){
	function smtpEmail ($to, $subject, $message) {
		require_once "Mail.php";
			
		$from 		= "Pixel Army (Strategic Website Design) <noreply@pixelarmy.ca>";
		$to 		= $to;
		$subject 	= $subject;
		$body 		= $message;
		
		$host 		= "mail.emailsrvr.com";
		$username 	= "noreply@pixelarmy.ca";
		$password 	= "rebefredeS3e";
		
		
		
		$headers = array ('From' => $from,
		  'To' => $to,
		  'Subject' => $subject,
		  'MIME-Version' => '1.0', 'Content-Type' => 'text/html;charset=iso-8859-1', 'Content-Transfer-Encoding' => '8bit', 'X-Priority' => '3', 'Importance' => 'Normal');
		
		$smtp = Mail::factory('smtp',
		  array ('host' => $host,
			'auth' => true,
			'username' => $username,
			'password' => $password));
		
		
		
		$mail = $smtp->send($to, $headers, $body);
		
		if (PEAR::isError($mail)) {
		  echo("<p><b>Error: </b>" . $mail->getMessage() . "</p>");
		} else {
		  //echo("<p>Message successfully sent!</p>");
		}
	}
}*/

if(!function_exists('str_limit_characters')) {
	function str_limit_characters($string, $maxlength=140) {
		if(trim(strip_tags($string)) != '') {
			$string = strip_tags($string);
			if(strlen($string) > $maxlength) {
			    $string = substr($string, 0, $maxlength); // truncate string
			    $string = substr($string, 0, strrpos($string, ' ')).' &hellip;'; // make sure it ends in a word
			}
		} else {
			$string = '';
		}

		return $string;
	}
}

if(!function_exists('get_page_url')) {
	function get_page_url($page_id, $page_url='') {
		global $db;

		$query = $db->query("SELECT page_id, parent_id, page, slug, type, url FROM pages WHERE page_id = ".$page_id);
		if($query && !$db->error() && $db->num_rows() > 0) {
			$result = $db->fetch_array();
			$row = $result[0];

			$page_type = 'page';

			// external link, get url
			if($row['type'] == 1) {
				$page_type = 'url';
			}

			// getting url of parent, always get the page
			if(trim($page_url) != '') {
				$page_type = 'page';
			} else {
				if($page_type == 'url') {
					$row['parent_id'] = '';
				}
			}

			// Normal Page
			if($page_type == 'page') {
				$page_url = (trim($row['slug']) != '' ? $row['slug'] : $row['page']).'/';
			// External Link
			} else if($page_type == 'url') {
				$page_url = $row['url'];
			}

			// No Parent
			if($row['parent_id'] == '' || $row['parent_id'] == 0) {
				return $page_url;
			// Has Parent
			} else {
				return get_page_url($row['parent_id'], $page_url).$page_url;
			}
		} else {
			return $page_url;
		}
	}
}

?>