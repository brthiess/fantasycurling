<?php

/*-----------------------------------/
* Builder for CMS base, sections, and settings
* @author	Pixel Army
* @date		15-06-04
* @file		CMSBuilder.class.php
*/

class CMSBuilder{
	
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
	* Mysqli database object utilizing Database class
	*/
	private $db;
	
	/*-----------------------------------/
	* @var account
	* Account object utilizing Accounts class
	*/
	private $account;
	
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
	* @var cms_settings
	* Array of CMS settings data
	*/
	private $cms_settings;
	
	/*-----------------------------------/
	* @var widgets
	* Array of dashboard widget data
	*/
	private $widgets;
	
	/*-----------------------------------/
	* Public constructor function
	*
	* @author	Pixel Army
	* @param	$path		Relative path to top of the site
	* @return	CMSBuilder	New CMSBuilder object
	* @throws	Exception
	*/
	public function __construct($path='/'){	
						
		//Set database instance
		if(class_exists('Database')){
			$this->db = new Database();
		}else{
			throw new Exception('Missing class file `Database`');
		}
		
		//Set account instance
		if(class_exists('Account')){
			$this->account = new Account();
		}else{
			throw new Exception('Missing class file `Account`');
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
			$this->fetch_sections();
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
		
		//Load CMS settings
		$this->cms_settings = array();
		try{
			$this->fetch_cms_settings();
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
		
		//Set widget array
		$this->widgets = array();
	
    }
	
	/*-----------------------------------/
	* Loads the sitemap and navigation data into this object
	*
	* @author	Pixel Army
	* @throws	Exception
	*/
	private function fetch_sections(){
		$params = array('Enabled');
		$query = $this->db->query("SELECT `section_id`, `parent_id`, `name`, `page`, `filelocation`, `icon`, `showhide` FROM `cms_sections` WHERE `status` = ? ORDER BY `ordering` IS NULL, `ordering`", $params);
		if($query && !$this->db->error()){
			$result = $this->db->fetch_array();
			
			$this->sitemap = $this->build_sitemap($result);
			$this->navigation = $this->build_navigation($this->sitemap);
			
		}else{
			throw new Exception('Error retrieving site map: '.$this->db->error());
		}
	}
	
	/*-----------------------------------/
	* Reorders sections based on hierarchy and builds flat array of sections
	*
	* @author	Pixel Army
	* @param	$sections_arr	Array of sections with section ID as key
	*/
	private function build_sitemap($sections_arr=array(), $parent_id=0){
		$row = array();
		foreach($sections_arr as $section){
			if($section['parent_id'] == $parent_id){
				$row[$section['section_id']] = $section;
				$row[$section['section_id']]['page_url'] = $this->path.$section['page'].'/';
				
				$children = $this->build_sitemap($sections_arr, $section['section_id']);
				if($children){
					foreach($children as $child){
						$row[$child['section_id']] = $child;
						$row[$child['section_id']]['page_url'] = $row[$child['parent_id']]['page_url'].$child['page'].'/';
					}
				}
			}
		}
		return $row;
	}
	
	/*-----------------------------------/
	* Builds nested array of sections excluding hidden sections
	*
	* @author	Pixel Army
	* @param	$sections_arr	Array of sections with section ID as key
	*/
	private function build_navigation($sections_arr=array(), $parent_id=0){
		$row = array();
		foreach($sections_arr as $section){
		if($section['parent_id'] == $parent_id && $section['showhide'] == 0 && $section['section_id'] > 1){
				$children = $this->build_navigation($sections_arr, $section['section_id']);
				if($children){
					$section['sub_sections'] = $children;
				}else{
					$section['sub_sections'] = array();	
				}
				$row[$section['section_id']] = $section;
			}
		}
		return $row;	
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
				$result[0]['global_social'] = $result4;
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
	* Loads CMS settings into this object
	*
	* @author	Pixel Army
	* @throws	Exception
	*/
	private function fetch_cms_settings(){
		$query = $this->db->query("SELECT * FROM `cms_settings`");
		if($query && !$this->db->error()){
			$result = $this->db->fetch_array();
			$this->cms_settings = $result[0];			
		}else{
			throw new Exception('Error retrieving CMS settings: '.$this->db->error());
		}
	}
	
	/*-----------------------------------/
	* Gets full site map for CMS
	*
	* @author	Pixel Army
	* @return	Array of sections
	*/
	public function get_sitemap(){
		return $this->sitemap;
	}
	
	/*-----------------------------------/
	* Gets navigation structure for CMS
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
	* Gets CMS settings
	*
	* @author	Pixel Army
	* @return	Array of CMS setting data
	*/
	public function cms_settings(){
		return $this->cms_settings;
	}
	
	/*-----------------------------------/
	* Checks to see if a user has permission to view/edit a section
	*
	* @author	Pixel Army
	* @param	$section_id		The section ID to be checked
	* @param	$account_id		The account ID to be checked
	* @return	Array of global data
	*/
	public function check_permissions($section_id, $account_id=NULL){
		if(is_numeric($section_id)){
		
			//Default to current user
			if(is_null($account_id) || empty($account_id)){
				$account_id = $this->account->login_status();
			}
			
			//Auto access to system sections
			if($section_id <= 4){
				return true;
			}
			
			//Check if section exists in account permissions
			$permissions = $this->account->get_account_permissions($account_id);
			if(array_key_exists($section_id, $permissions)){
				return true;
			}
		}
		return false;
	}
	
	/*-----------------------------------/
	* Gets all data for specified section
	*
	* @author	Pixel Army
	* @param	$section	Url segment of section (ie. /dashboard/settings/) OR section ID
	* @param	$dir		Directory location of section file
	* @return	Array		Array of all section data
	*/
	public function get_section($section, $dir='sections/'){
		
		//Get section id
		if(!is_numeric($section)){
			$section_id = $this->get_section_id($section);
		}else{
			$section_id = $section;	
		}
		
		//Retrieve page data
		$query = $this->db->query("SELECT * FROM `cms_sections` WHERE `section_id` = ? && `status` = ?", array($section_id, 'Enabled'));
		if($query && !$this->db->error() && $this->db->num_rows() > 0){
			$results = $this->db->fetch_array();
			$row = $results[0];
			
			//Return section data if found
			if(trim($row['filelocation']) != '' && file_exists($dir.$row['filelocation'])){
				
				//Set page url
				$row['page_url'] = $this->sitemap[$section_id]['page_url'];
				
				//Not found
				$row['error404'] = false;
						
				return $row;
			}	
		}
		
		//section not found
		$error['error404'] = true;
		$error['section_id'] = '';
		$error['name'] = 'Not Found';
		$error['page'] = 'notfound';
		$error['parent_id'] = $this->get_parent_id($this->pageurl);
		$error['page_url'] = $this->pageurl;
		
		return $error;
		
	}
	
	/*-----------------------------------/
	* Gets all data for current section user is on
	*
	* @author	Pixel Army
	* @return	Array	Array of section data
	*/
	public function curr_section(){
		return $this->get_section($this->pageurl);
	}
	
	/*-----------------------------------/
	* Gets section id based on url
	*
	* @author	Pixel Army
	* @param	$page_url		Full page url
	* @param	$sections_arr	Array of sections to search
	* @return	Integer			Section id
	*/
	public function get_section_id($page_url, $sections_arr=NULL){
		$section_id = NULL;
		if(is_null($sections_arr)){
			$sections_arr = $this->sitemap;
		}
		foreach($sections_arr as $section){
			if($section['page_url'] === $page_url){
				$section_id = $section['section_id'];
				break;
			}else if(isset($section['sub_sections']) && is_array($section['sub_sections']) && count($section['sub_sections']) > 0){
				$section_id = $this->get_section_id($page_url, $section['sub_sections']);
				if(!is_null($section_id)){
					break;
				}
			}
		}
		return $section_id;
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
			$parent_id = $this->get_section_id(implode('/', $pagebits).'/');
			if(!empty($parent_id)) break;
		}
		
		return $parent_id;
	}
	
	/*-----------------------------------/
	* Gets breadcrumb info for current section user is on
	*
	* @author	Pixel Army
	* @return	Array of breadcrumb sections
	*/
	public function get_breadcrumb(){
		$breadcrumb = array();
		$url = $this->path;
		
		for($i=1; $i<count($this->pathbits); $i++){
			if($this->pathbits[$i] != ''){
				$url .= $this->pathbits[$i]. '/';
				foreach($this->sitemap as $id=>$section) {
					if($section['page_url'] == $url)
						array_push($breadcrumb, array('url' => $url, 'name' => $section['name']));
				}
			}
		}
		
		return $breadcrumb;
	}
	
	/*-----------------------------------/
	* Gets total row count for given table
	*
	* @author	Pixel Army
	* @param	$table	Name of database table
	* @return	Number	Total rows in table
	*/
	public function get_record_count($table){
		$count = 0;
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `$table`");	
		if($query && !$this->db->error()){
			$result = $this->db->fetch_array();
			$count = $result[0]['total'];
		}
		return $count;
	}
	
	/*-----------------------------------/
	* Sets dashboard widgets
	*
	* @author	Pixel Army
	* @param	$section_id		The section ID for the widget
	* @param	$title			Title of row
	* @param	$value			Value of row
	* @param	$icon			FontAwesome icon
	*/
	public function set_widget($section_id, $title=NULL, $value=NULL, $icon=NULL){
		$widget = $this->get_section($section_id);
		if(!$widget['error404']){
			$this->widgets[] = array(
				'section_id' => $section_id, 
				'title' => (!is_null($title) ? $title : $widget['name']), 
				'value' => (!is_null($value) ? $value : 0),
				'icon' => (!is_null($icon) ? $icon : $widget['icon'])
			);
		}
	}
	
	/*-----------------------------------/
	* Retrieves dashboard widgets
	*
	* @author	Pixel Army
	* @returns	Array of data
	*/
	public function get_widgets(){
		return $this->widgets;
	}
	
	/*-----------------------------------/
	* Sets system alert session
	*
	* @author	Pixel Army
	* @param	$message	Alert message (text/html)
	* @param	$status		Boolean Success/Error
	*/
	public function set_system_alert($message, $status){
		if(!isset($_SESSION['system_alert'])){
			$_SESSION['system_alert'] = array();
		}
		array_push($_SESSION['system_alert'], array(
			'message' => $message, 
			'status' => $status
		));
	}
	
	/*-----------------------------------/
	* Retrieves system alert session and unsets it
	*
	* @author	Pixel Army
	* @returns	Array of alert session data or NULL
	*/
	public function system_alert(){
		if(isset($_SESSION['system_alert'])){
			$alert = $_SESSION['system_alert'];
			unset($_SESSION['system_alert']);
			return $alert;
		}
		return NULL;
	}
	
	/*-----------------------------------/
	* Sets section search session
	*
	* @author	Pixel Army
	* @param	$searchterm		The search term
	* @param	$section		The relevant section id
	*/
	public function set_system_search($searchterm, $section){
		if(is_null($searchterm)){
			unset($_SESSION['system_search'][$section]);
		} else {
			$_SESSION['system_search'][$section] = $searchterm;
		}
	}
	
	/*-----------------------------------/
	* Retrieves system search for section
	*
	* @author	Pixel Army
	* @returns	Array of alert session data or NULL
	*/
	public function system_search($section){
		if(isset($_SESSION['system_search'][$section]) && !empty($_SESSION['system_search'][$section])){
			$searchterm = $_SESSION['system_search'][$section];
			return $searchterm;
		}
		return NULL;
	}
	
	/*-----------------------------------/
	* Generates tooltip
	*
	* @author	Pixel Army
	* @param	$title		Tooltip title
	* @param	$content	Tooltip content (supports html tags)
	* @returns	HTML output for tooltip
	*/
	public function tooltip($title, $content){
		return '<span class="tooltip" title="<h4>' .$title. '</h4>' .$content. '">?</span>';	
	}
	
	/*-----------------------------------/
	* Generates important information alert
	*
	* @author	Pixel Army
	* @param	$message	Important content (supports html tags)
	
	* @returns	HTML output for important alert
	*/
	public function important($message){
		return '<div class="panel system-alert important">
			<div class="title"><i class="fa fa-bell"></i>Important Notice! <span class="f_right"><a class="panel-toggle fa fa-chevron-up"></a></span></div>
			<div class="panel-content message">'.$message.'</div>
		</div>';	
	}
	
	/*-----------------------------------/
	* Generates show/hide toggle switch for table list items
	*
	* @author	Pixel Army
	* @param	$record_db		The name of the table being updated
	* @param	$record_id		The field name of the primary key for the table
	* @param	$item_id		The list item id to be updated
	* @param	$item_status	The current show/hide status of the item
	* @param	$item_col		The column to be updated in the table (default = showhide)
	* @returns	HTML output for tooltip
	*/
	public function showhide_toggle($record_db, $record_id, $item_id, $item_status, $item_col = "showhide"){
		return '<span class="switch-sorter">' .(isset($item_status) && $item_status ? "Hidden" : "Visible"). '</span>
		<div class="onoffswitch">
			<input type="checkbox" name="'.$item_col.$item_id.'" id="'.$item_col.$item_id.'" value="0"' .(isset($item_status) && $item_status ? "" : " checked"). ' class="ajax-showhide" data-table="'.$record_db.'" data-tableid="'.$record_id.'" data-itemid="'.$item_id.'" data-itemcol="'.$item_col.'" />
			<label for="'.$item_col.$item_id.'">
				<span class="inner"></span>
				<span class="switch"></span>
			</label>
		</div>';	
	}
	
	/*-----------------------------------/
	* Generates mini alert (ajax style)
	*
	* @author	Pixel Army
	* @param	$message	Alert content (supports html tags)
	* @param	$status		Boolean Success/Error
	
	* @returns	HTML output for mini alert
	*/
	public function mini_alert($message, $status){
		return '<div class="system-alert mini '.($status ? "success" : "error").'">
			<div class="title">' .($status ? '<i class="fa fa-check"></i>Success' : '<i class="fa fa-close"></i>Error'). '!</div>
			<div class="message">'.$message.'</div>
		</div>';	
	}
	
	/*-----------------------------------/
	* Generates pager for tablesorter table
	*
	* @author	Pixel Army
	* @param	$prevnext	True/false to display previous and next buttons
	* @param	$firstlast	True/false to display first and last buttons
	* @param	$gotopage	True/false to display page selector
	* @param	$pagesize	Number of results to display per page
	* @returns	HTML output for tooltip
	*/
	public function tablesorter_pager($pagesize=10, $prevnext=true, $firstlast=true, $gotopage=true){
		
		$pager = '<div class="pager clearfix" data-pagesize="' .$pagesize. '">';
		$pager .= '<span class="pagedisplay"></span>';
		$pager .= '<div class="pagebuttons clearfix">';
			$pager .= ($firstlast ? '<span class="button-sm first">&laquo;</span>' : '');
			$pager .= ($prevnext ? '<span class="button-sm prev">&lsaquo;</span>' : '');
			$pager .= ($gotopage ? '<select class="gotoPage select"></select>' : '');
			$pager .= ($prevnext ? '<span class="button-sm next">&rsaquo;</span>' : '');
			$pager .= ($firstlast ? '<span class="button-sm last">&raquo;</span>' : '');
		$pager .= '</div>';
		$pager .= '</div>';	
		
		echo $pager;
	}
	
}

?>