<?php

/*-----------------------------------/
* Deals with all functionality associated with pages and content
* @author	Pixel Army
* @date		13-09-06
* @file		SiteBuilder.class.php
*/

class SiteBuilder{
	
	/*-----------------------------------/
	* @var path
	* Relative path to top of the site
	*/
	public $path;
	
	/*-----------------------------------/
	* @var pathbits
	* Array of url segments
	*/
	public $pathbits;
	
	/*-----------------------------------/
	* @var db
	* Mysqli database object for this class
	*/
	private $db;
	
	/*-----------------------------------/
	* @var sitemap
	* Non-recursive array of all pages in site map
	*/
	private $sitemap;
	
	/*-----------------------------------/
	* @var navigation
	* Recursive array of navigation links
	*/
	private $navigation;
	
	/*-----------------------------------/
	* @var pageurl
	* Url of current page
	*/
	private $pageurl;
	
	/*-----------------------------------/
	* @var settings
	* Array of global settings data
	*/
	private $settings;
	
	/*-----------------------------------/
	* Public constructor function
	*
	* @author	Pixel Army
	* @param	$path		Relative path to top of the site
	* @return	SiteBuilder	New SiteBuilder object
	* @throws	Exception
	*/
	public function __construct($path='/'){		
	
		//Set database instance
		if(class_exists('Database')){
			$this->db = Database::get_instance();
		}else{
			throw new Exception('Missing class file `Database`');	
		}				
		
		//Get path variables
		$pageurl = $_SERVER['REQUEST_URI'];
		if($pageurl == $path || $pageurl == $path.'index.php'){
			$pageurl = $path.'home/';
		}
		if(empty($pageurl)){
			$pathbits = array('');
		}else{
			$pathbits = explode("/",  $pageurl);
		}
		$shifts = explode("/", $path);
		for($i=0; $i<count($shifts)-2; $i++){
			array_shift($pathbits);
		}
		foreach($pathbits as $key => $bit){
			if($pathbits[$key] == "?".$_SERVER['QUERY_STRING']){
				$pageurl = str_replace($pathbits[$key], '', $pageurl);
				$pathbits[$key] = "";
			}
		}
		
		//Set path variables
		$this->path = $path;
		$this->pathbits = $pathbits;
		$this->pageurl = $pageurl;
		
		//Load sitemap and navigation
		$this->sitemap = array();
		$this->navigation = array();
		try{
			$this->fetch_pages();
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
		
		//Load global settings
		$this->settings = array();
		try{
			$this->fetch_settings();
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
    }
	
	/*-----------------------------------/
	* Loads the sitemap and navigation data into this object
	*
	* @author	Pixel Army
	* @throws	Exception
	*/
	private function fetch_pages(){
		$query = $this->db->query("SELECT `page_id`, `parent_id`, `name`, `page`, `slug`, `type`, `theme`, `url`, `urltarget`, `page_title`, `meta_title`, `showhide`, `image`, `image_alt` FROM `pages` ORDER BY `ordering`");
		if($query && !$this->db->error()){
			$result = $this->db->fetch_array();
			
			$this->sitemap = $this->build_sitemap($result);
			$this->navigation = $this->build_navigation($this->sitemap);
			
		}else{
			throw new Exception('Error retrieving site map: '.$this->db->error());
		}
	}
	
	/*-----------------------------------/
	* Reorders pages based on hierarchy and builds flat array of pages
	*
	* @author	Pixel Army
	* @param	$pages_arr	Array of pages with page ID as key
	*/
	private function build_sitemap($pages_arr=array(), $parent_id=0){
		$row = array();
		foreach($pages_arr as $page){
			if($page['parent_id'] == $parent_id){
				$row[$page['page_id']] = $page;
				$row[$page['page_id']]['page_url'] = $this->path.($page['slug'] != NULL ? $page['slug'] : $page['page']).'/';
				if($page['slug'] != NULL){
					$row[$page['page_id']]['meta_canonical'] = $this->path.$page['page'].'/';
				}
				$row[$page['page_id']]['seo_title'] = (!empty($page['meta_title']) ? $page['meta_title'] : $page['page_title']);
				$row[$page['page_id']]['banner_image'] = $page['image'];
				$row[$page['page_id']]['banner_image_alt'] = $page['image_alt'];
				$row[$page['page_id']]['theme'] = $page['theme'];
				
				$children = $this->build_sitemap($pages_arr, $page['page_id']);
				if($children){
					foreach($children as $child){
						$row[$child['page_id']] = $child;
						$row[$child['page_id']]['page_url'] = $row[$child['parent_id']]['page_url'].($child['slug'] != NULL ? $child['slug'] : $child['page']).'/';
						if($child['slug'] != NULL){
							$row[$child['page_id']]['meta_canonical'] = $row[$child['parent_id']]['page_url'].$child['page'].'/';
						}
						$row[$child['page_id']]['seo_title'] = (!empty($child['meta_title']) ? $child['meta_title'] : $child['page_title']).' | '.$row[$child['parent_id']]['seo_title'];
						$row[$child['page_id']]['banner_image'] = (!empty($child['image']) ? $child['image'] : $row[$child['parent_id']]['banner_image']);
						$row[$child['page_id']]['banner_image_alt'] = (!empty($child['image_alt']) ? $child['image_alt'] : $row[$child['parent_id']]['banner_image_alt']);
						$row[$child['page_id']]['theme'] = (!empty($child['theme']) ? $child['theme'] : $row[$child['parent_id']]['theme']);
					}
				}
			}
		}
		return $row;
	}
	
	/*-----------------------------------/
	* Builds nested array of pages excluding hidden pages
	*
	* @author	Pixel Army
	* @param	$pages_arr	Array of pages with page ID as key
	*/
	private function build_navigation($pages_arr=array(), $parent_id=0){
		$row = array();
		foreach($pages_arr as $page){
		if($page['parent_id'] == $parent_id && $page['showhide'] == 0 && $page['page_id'] > 2){
				$children = $this->build_navigation($pages_arr, $page['page_id']);
				if($children){
					$page['sub_pages'] = $children;
				}else{
					$page['sub_pages'] = array();	
				}
				$row[$page['page_id']] = $page;
			}
		}
		return $row ;	
	}
	
	/*-----------------------------------/
	* Loads global website settings into this object
	*
	* @author	Pixel Army
	* @throws	Exception
	*/
	private function fetch_settings(){
		$query = $this->db->query("SELECT * FROM `global_settings`");
		if($query && !$this->db->error()){
			$result = $this->db->fetch_array();
			
			$query2 = $this->db->query("SELECT * FROM `global_numbers`");
			if($query2 && !$this->db->error()){
				$result2 = $this->db->fetch_array();
				$result[0]['global_numbers'] = $result2;
			}

			$query3 = $this->db->query("SELECT * FROM `global_hours` ORDER BY FIELD(`day`, ?, ?, ?, ?, ?, ?, ?)", array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'));
			if($query3 && !$this->db->error()){
				$result3 = $this->db->fetch_array();
				$result[0]['global_hours'] = $result3;
			}
			
			$query4 = $this->db->query("SELECT * FROM `global_social`");
			if($query4 && !$this->db->error()){
				$result4 = $this->db->fetch_array();
				foreach($result4 as $social){
					$result[0]['social'][$social['service']] = $social['url'];
				}
			}
			
			$this->settings = $result[0];
			
			//set timezone if available
			if($this->settings['timezone'] != NULL){
				date_default_timezone_set($this->settings['timezone']);
			}
			
		}else{
			throw new Exception('Error retrieving global settings: '.$this->db->error());
		}
	}
	
	/*-----------------------------------/
	* Gets full site map for website
	*
	* @author	Pixel Army
	* @return	Array of pages
	*/
	public function get_sitemap(){
		return $this->sitemap;
	}
	
	/*-----------------------------------/
	* Gets navigation structure for website
	*
	* @author	Pixel Army
	* @return	Array of navigation links
	*/
	public function get_navigation(){
		return $this->navigation;
	}
	
	/*-----------------------------------/
	* Gets global website settings
	*
	* @author	Pixel Army
	* @return	Array of global data
	*/
	public function global_settings(){
		return $this->settings;
	}
	
	/*-----------------------------------/
	* Gets all data for requested page
	*
	* @author	Pixel Army
	* @param	$page	Unique page id or relative page url (ie. /about-us/our-team/)
	* @return	Array of page data. If page is not found returns 404 data
	*/
	public function get_page_content($page){
		
		//Get page id
		if(!is_numeric($page)){
			$page_id = $this->get_page_id($page);
		}else{
			$page_id = $page;	
		}
	
		//Get global settings
		$global = $this->settings;
	
		//Retrieve page data
		$query = $this->db->query("SELECT * FROM `pages` WHERE `page_id` = ? && `showhide` < 2", array($page_id));
		if($query && !$this->db->error() && $this->db->num_rows() > 0){
			$results = $this->db->fetch_array();
			$row = $results[0];
			
			//Set target string
			$target = $row['urltarget'];
			if($target == 0){ $target = "_self"; }else{ $target = "_blank"; }
			
			//Get default meta data
			if(trim($row['meta_title']) == ""){
				$row['meta_title'] = $row['page_title']. ' | ' .$global['meta_title'];
			}
			if(trim($row['meta_description']) == ""){
				$row['meta_description'] = $global['meta_description'];
			}
			
			//should we redirect to slug?
			$row['redirect_to_slug'] = false;
			
			if($row['slug'] != NULL){
				//only redirect if we are not already on this page
				if($this->pageurl != $this->sitemap[$page_id]['page_url']){
					$row['redirect_to_slug'] = true;
				}
				$row['meta_canonical'] = $this->sitemap[$page_id]['meta_canonical'];
			}
			if(trim($row['meta_canonical']) == ''){
				$row['meta_canonical'] = $this->sitemap[$page_id]['page_url'];
			}
			
			//Set page url
			$row['page_url'] = $this->sitemap[$page_id]['page_url'];
			
			//Set banner image
			$row['banner_image'] = $this->sitemap[$page_id]['banner_image'];
			$row['banner_image_alt'] = $this->sitemap[$page_id]['banner_image_alt'];
			if(empty($row['banner_image'])){
				$row['banner_image'] = $global['banner_image'];	
				$row['banner_image_alt'] = $global['banner_image_alt'];	
			}
			
			//Set theme
			$row['theme'] = $this->sitemap[$page_id]['theme'];
			
			//Not found
			$row['error404'] = false;
							
			return $row;
		
		}
		
		//404 error
		$query = $this->db->query("SELECT * FROM `pages` WHERE page_id = 1");
		if($query && !$this->db->error()){
			$results = $this->db->fetch_array();
			$row = $results[0];
			
			//Set variables
			$row['error404'] = true;
			$row['page_id'] = '';
			$row['parent_id'] = $this->get_parent_id($this->pageurl);
			$row['banner_image'] = $global['banner_image'];
			$row['banner_image_alt'] = $global['banner_image_alt'];	
			$row['page_url'] = $this->pageurl;
			$row['canonical'] = $this->pageurl;
		
			return $row;
			
		}else{
			trigger_error('Error retrieving 404 data: '.$this->db->error());
		}
		
	}
	
	/*-----------------------------------/
	* Gets all data for current page
	*
	* @author	Pixel Army
	* @return	Array of page data. If page is not found returns 404 data
	*/
	public function curr_page_content(){
		return $this->get_page_content($this->pageurl);
	}
	
	/*-----------------------------------/
	* Gets page id based on url
	*
	* @author	Pixel Army
	* @param	$page_url	Full page url
	* @param	$pages_arr	Array of pages to search
	* @return	Integer		Page id
	*/
	public function get_page_id($page_url, $pages_arr=NULL){
		$page_id = NULL;
		if(is_null($pages_arr)){
			$pages_arr = $this->sitemap;
		}
		foreach($pages_arr as $page){
			if($page['page_url'] === $page_url || (isset($page['meta_canonical']) && $page['meta_canonical'] === $page_url)){
				$page_id = $page['page_id'];
				break;
			}else if(isset($page['sub_pages']) && is_array($page['sub_pages']) && count($page['sub_pages']) > 0){
				$page_id = $this->get_page_id($page_url, $page['sub_pages']);
				if(!is_null($page_id)){
					break;
				}
			}
		}
		return $page_id;
	}
	
	/*-----------------------------------/
	* Gets closest parent id based on url
	*
	* @author	Pixel Army
	* @param	$page_url	Full page url
	* @return	Integer		Parent id
	*/
	public function get_parent_id($page_url){
		$parent_id = NULL;
		$pathbits = explode('/', $page_url);
		
		for($i=2; $i<count($pathbits); $i++){
			$pagebits = array_slice($pathbits, 0, count($pathbits)-$i);
			$parent_id = $this->get_page_id(implode('/', $pagebits).'/');
			if(!empty($parent_id)) break;
		}
		
		return $parent_id;
	}
	
	/*-----------------------------------/
	* Gets breadcrumb info for current page user is on
	*
	* @author	Pixel Army
	* @return	Array of breadcrumb pages
	*/
	public function get_breadcrumb(){
		$breadcrumb = array();
		$url = $this->path;
		
		for($i=1; $i<count($this->pathbits); $i++){
			if($this->pathbits[$i] != ''){
				$notfound = true;
				$url .= $this->pathbits[$i]. '/';
				foreach($this->sitemap as $id=>$page){
					if($page['showhide'] < 2 && $page['page_url'] == $url){
						$notfound = false;
						array_push($breadcrumb, array('url' => $url, 'name' => $page['name']));
					}
				}
				if($notfound){
					array_push($breadcrumb, array('url' => $url, 'name' => '404 Error'));
					break;
				}
			}
		}
		
		return $breadcrumb;
	}
	
}

?>