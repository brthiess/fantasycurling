<?php

//Dashboard widget
if(SECTION_ID == 4){
	$total_pages = $CMSBuilder->get_record_count('pages');
	$CMSBuilder->set_widget(9, 'Total Page Count', $total_pages,'file-text');
}

if(SECTION_ID == 9){
	
	//Define vars
	$imagedir = "../images/banners/";
	$cropimages = array();
	$errors = false;
	$required = array();
	
	//Get pages array
	$pages = array();
	
	$query = $db->query("SELECT * FROM pages WHERE page_id > 2 ORDER BY ordering");
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$pages[$row['page_id']] = $row;
			$pages[$row['page_id']]['sub_pages'] = array();
			$pages[$row['page_id']]['page_url'] = $root.($row['slug'] != NULL ? $row['slug'] : $row['page']).'/';
		}
		
		$lvl = 1; //tracking depth of pages array
		$pages = build_pages_sitemap($pages);
		foreach($pages as $page_id => &$page){
			if($page['parent_id'] && array_key_exists($page['parent_id'], $pages)){
				$pages[$page['parent_id']]['sub_pages'][$page_id] = &$page;
				$pages[$page['parent_id']]['sub_pages'][$page_id]['parent_page'] = $pages[$page['parent_id']]['name'];
				$pages[$page['parent_id']]['sub_pages'][$page_id]['page_url'] = $pages[$page['parent_id']]['page_url'].($page['slug'] != NULL ? $page['slug'] : $page['page']).'/';
			}
		}				
		
	}else{
		$CMSBuilder->set_system_alert('Unable to retrieve page data. '.$db->error(), false);	
	}
	
	//get pages related to search
	if(ACTION == '') {
		$_GET['search'] = $CMSBuilder->system_search(SECTION_ID);
		if(isset($_GET['search']) && $_GET['search'] != ""){
			$pages_result = array();
			//search pages array
			foreach($pages as $key => $search_page){
				if(stripos($search_page['page_title'],$_GET['search']) !== false || 
					stripos($search_page['name'],$_GET['search']) !== false || 
					stripos($search_page['meta_title'],$_GET['search']) !== false || 
					stripos($search_page['content'],$_GET['search']) !== false){
					$pages_result[$key] = $search_page;
				}
			}
			$pages = $pages_result;
		}
	}
		
	//Not found
	if(ACTION == 'edit'){
		if(!array_key_exists(ITEM_ID, $pages)){
			$CMSBuilder->set_system_alert('Requested item was not found. Please select from the list below.', false);
			header('Location:' .PAGE_URL);
			exit();
		}else{
			//set vars
			$row = $pages[ITEM_ID];
			//set page tabs
			$query = $db->query("SELECT * FROM pages_tabs WHERE page_id = ".ITEM_ID." ORDER BY ordering, tab_id ASC");
			$pages[ITEM_ID]['page_tabs'] = $db->fetch_array();
			//set page promos
			$pages[ITEM_ID]['page_promos'] = array();
			$query = $db->query("SELECT * FROM pages_promo WHERE page_id = ".ITEM_ID);
			$page_promos = $db->fetch_array();
			foreach($page_promos as $promo){
				$pages[ITEM_ID]['page_promos'][] = $promo['promo_id'];
			}
		}
	}
	
	//Delete item
	if(isset($_POST['delete'])){
		
		//Must be deletable
		if($row['deletable'] && !$row['system_page']){
			
			//Multiple queries so utilize transactions
			$db->new_transaction();
			
			//Delete main page
			$delete = $db->query("DELETE FROM `pages` WHERE `page_id` = ?", array(ITEM_ID));
			
			//If subpages are system pages or aren't deletable, set to main level and hide
			$update = $db->query("UPDATE `pages` SET `parent_id` = NULL, `showhide` = 1 WHERE `parent_id` = ? && (`deletable` = false || `system_page` = true)", array(ITEM_ID));
			
			//Loop through all subpages and delete applicable
			if(isset($pages[ITEM_ID]['sub_pages']) && !empty($pages[ITEM_ID]['sub_pages'])){
				foreach($pages[ITEM_ID]['sub_pages'] as $sub_page){
					$delete2 = $db->query("DELETE FROM `pages` WHERE `page_id` = ? && deletable = true && system_page = false", array($sub_page['page_id']));
					if(isset($sub_page['sub_pages']) && !empty($sub_page['sub_pages'])){
						foreach($sub_page['sub_pages'] as $sub_page2){
							$delete3 = $db->query("DELETE FROM `pages` WHERE `page_id` = ? && deletable = true && system_page = false", array($sub_page2['page_id']));
							if(isset($sub_page2['sub_pages']) && !empty($sub_page2['sub_pages'])){
								foreach($sub_page2['sub_pages'] as $sub_page3){
									$delete4 = $db->query("DELETE FROM `pages` WHERE `page_id` = ? && deletable = true && system_page = false", array($sub_page3['page_id']));
									if(isset($sub_page3['sub_pages']) && !empty($sub_page3['sub_pages'])){
										foreach($sub_page3['sub_pages'] as $sub_page4){
											$delete5 = $db->query("DELETE FROM `pages` WHERE `page_id` = ? && deletable = true && system_page = false", array($sub_page4['page_id']));
										}
									}//lvl4
								}
							}//lvl3
						}
					}//lvl2
				}
			}//lvl1
			
			if(!$db->error()){
				$db->commit();
				if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
					unlink($imagedir.$_POST['old_image']);
				}
				$CMSBuilder->set_system_alert('Page was successfully deleted.', true);
			}else{
				$CMSBuilder->set_system_alert('Unable to delete record. ' .$db->error(),false);	
			}
		
		//Not deletable
		}else{
			$CMSBuilder->set_system_alert('Unable to delete record. Page contains dynamic content.', false);	
		}
		header("Location: " .PAGE_URL);
		exit();
	
	//Save item
	}else if(isset($_POST['save'])){
		
		if($_POST['name'] == ""){
			$errors[] = 'Button text is required.';
			array_push($required, 'name');
		}
				
		if(trim($_POST['page_title']) == ""){ $_POST['page_title'] = $_POST['name']; }
				
		$content = str_replace("<p>&nbsp;</p>", "", $_POST['TINYMCE_Editor']);
		$sidebar = (isset($_POST['TINYMCE_Editor2']) ? str_replace("<p>&nbsp;</p>", "", $_POST['TINYMCE_Editor2']) : NULL);
		
		//create safe page name
		$pagename = clean_url($_POST['name']);
				
		//pages that are not deletable cannot change url
		if(ITEM_ID != "" && !$pages[ITEM_ID]['deletable']){
			$pagename = $pages[ITEM_ID]['page'];
		}
		
		//system pages cannot be moved
		if(ITEM_ID != "" && $pages[ITEM_ID]['system_page']){
			$_POST['parent_id'] = $pages[ITEM_ID]['parent_id'];
		}
		
		//validate page url
		$is_unique = true;
		$unique_url = (isset($_POST['slug']) && $_POST['slug'] != "" ? clean_url($_POST['slug']) : $pagename);
		if($_POST['parent_id'] == 0){ //check top level pages
			foreach($pages as $check_page){ 
				if($check_page['page_id'] != ITEM_ID && ($check_page['slug'] == $unique_url || $check_page['page'] == $unique_url)){
					$is_unique = false;
					break;
				}
			}
		} else { //check sub pages of parent
			if(isset($pages[$_POST['parent_id']]['sub_pages'])){
				foreach($pages[$_POST['parent_id']]['sub_pages'] as $check_page){ 
					if($check_page['page_id'] != ITEM_ID && ($check_page['slug'] == $unique_url || $check_page['page'] == $unique_url)){
						$is_unique = false;
						break;
					}
				}
			}
		}
		
		if(!$is_unique){ //url validation failed
			$errors[] = 'A page already exists with the same name/url.';
			array_push($required, 'name');
			array_push($required, 'slug');
		}
				
		if(!$errors){
						 
			//Upload image
			$image = ($_POST['old_image'] != '' ? $_POST['old_image'] : NULL);
			if(!empty($_FILES['image']['name'])){
				$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
				$newname = date("ymdhis").'.'.$ext;
				$imageUpload = new ImageUpload();
				$imageUpload->load($_FILES['image']['tmp_name']);
				$imageUpload->fit(1920,1920);
				$imageUpload->save($imagedir, $newname);
				if(file_exists($imagedir.$newname)){
					$image = $newname;
					if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
						unlink($imagedir.$_POST['old_image']);
					}
					array_push($cropimages, array('dir' => $imagedir, 'src' => $image, 'width' => 1920, 'height' => 500, 'label' => 'Page Banner Image'));
				}
			}else{
				if(isset($_POST['deleteimage']) && $_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
					unlink($imagedir.$_POST['old_image']);
					$image = "";
				}
			}
	
			//Insert to db
			$params = array(
				ITEM_ID, 
				($_POST['parent_id'] == 0 ? NULL : $_POST['parent_id']), 
				$_POST['name'], 
				$pagename, 
				(isset($_POST['slug']) ? clean_url($_POST['slug']) : (isset($pages[ITEM_ID]) ? $pages[ITEM_ID]['slug'] : NULL)), 
				$_POST['type'], 
				$_POST['page_title'], 
				$_POST['meta_title'], 
				$_POST['meta_description'],
				(isset($_POST['meta_canonical']) ? $_POST['meta_canonical'] : ""), 
				$_POST['focus_keyword'],
				$content, 
				(isset($sidebar) ? $sidebar : ""), 
				$image, 
				$_POST['image_alt'], 
				$_POST['url'], 
				$_POST['urltarget'], 
				$_POST['ordering'],
				(isset($_POST['showhide']) ? $_POST['showhide'] : 0), 
				$_POST['google_map'], 
				($_POST['cta_id'] == 0 ? NULL : $_POST['cta_id']),
				($_POST['parent_id'] == 0 ? NULL : $_POST['parent_id']), 
				$_POST['name'], 
				$pagename, 
				(isset($_POST['slug']) ? clean_url($_POST['slug']) : (isset($pages[ITEM_ID]) ? $pages[ITEM_ID]['slug'] : NULL)), 
				$_POST['type'], 
				$_POST['page_title'], 
				$_POST['meta_title'], 
				$_POST['meta_description'],
				(isset($_POST['meta_canonical']) ? $_POST['meta_canonical'] : ""), 
				$_POST['focus_keyword'], 
				$content, 
				(isset($sidebar) ? $sidebar : ""), 
				$image, 
				$_POST['image_alt'], 
				$_POST['url'], 
				$_POST['urltarget'], 
				$_POST['ordering'], 
				$_POST['google_map'], 
				($_POST['cta_id'] == 0 ? NULL : $_POST['cta_id'])
			);													
			$insert = $db->query("INSERT INTO pages (page_id, parent_id, name, page, slug, type, page_title, meta_title, meta_description, meta_canonical, focus_keyword, content, sidebar, image, image_alt, url, urltarget, ordering, showhide, google_map, cta_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE parent_id = ?, name = ?, page = ?, slug = ?, type = ?, page_title = ?, meta_title = ?, meta_description = ?, meta_canonical = ?, focus_keyword = ?, content = ?, sidebar = ?, image = ?, image_alt = ?, url = ?, urltarget = ?, ordering = ?, google_map = ?, cta_id = ?",$params);
			if($insert && !$db->error()){
				$item_id = (ITEM_ID != "" ? ITEM_ID : $db->insert_id());
			
				//tabs section
				$pages_tabs = $_POST['tab_id'];
				foreach($pages_tabs as $key=>$tab_id){
					
					$panel_title = str_replace("'", "&rsquo;", stripslashes($_POST['panel_title'][$key]));
					$panel_page = clean_url($panel_title);  
					$panel_showhide = str_replace("'", "&rsquo;", stripslashes($_POST['panel_showhide'][$key])); 
					$panel_ordering = str_replace("'", "&rsquo;", stripslashes($_POST['panel_ordering'][$key]));  
					$panel_content = str_replace("'", "&rsquo;", stripslashes($_POST['panel_content'][$key]));
					$panel_content = str_replace(">rn<", "><", $panel_content);
					$panel_content = str_replace("<p>&nbsp;</p>", "", $panel_content); 
					
					if(trim($panel_title) != '' || trim($panel_content) != ''){
						$params = array(
							$tab_id, 
							$item_id, 
							$panel_title, 
							$panel_page, 
							$panel_content, 
							$panel_showhide, 
							$panel_ordering, 
							$panel_title, 
							$panel_page, 
							$panel_content, 
							$panel_showhide, 
							$panel_ordering
						);
						$panelquery = $db->query("INSERT INTO pages_tabs (tab_id, page_id, title, page, content, showhide, ordering) VALUES(?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = ?, page = ?, content = ?, showhide = ?, ordering = ?",$params);
						if(!$panelquery || $db->error()){
							$CMSBuilder->set_system_alert('Unable to update tabs panel: '.$db->error(), false);
						}
					}
					
				}
		
				//promos
				$promos = (isset($_POST['promo']) ? $_POST['promo'] : array());
				$delete = $db->query("DELETE FROM pages_promo WHERE page_id = ".$item_id. (!empty($promos) ? " AND promo_id NOT IN (".implode(",", $promos).")" : ""));
				if(count($promos) > 0) {
					foreach($promos as $key => $promo_id) {
						$promoquery = $db->query("INSERT INTO pages_promo (page_id, promo_id) VALUES ($item_id, $promo_id) ON DUPLICATE KEY UPDATE page_id = $item_id");
						if(!$promoquery || $db->error()){
							$CMSBuilder->set_system_alert('Unable to update promo boxes: '.$db->error(), false);
						}
					}
				}
				
				//save sitemap
				sitemapXML();
				
				//save SEO score
				if($cms_settings['enhanced_seo'] && $_POST['type'] == 0){
					//set new page_url
					if($_POST['meta_canonical'] != ""){
						$page_url = $_POST['meta_canonical'];
					} else {
						$page_url = $_SERVER['HTTP_HOST'];
						if(isset($_POST['parent_id']) && isset($pages[$_POST['parent_id']])){
							$page_url .= $pages[$_POST['parent_id']]['page_url'];
						} else {
							$page_url .= $root;
						}
						$page_url .= $unique_url."/";
					}
					
					try{
						$Analyzer->set_page($_POST['focus_keyword'], $page_url, $unique_url, $_POST['page_title'], $item_id);
						$Analyzer->analyze_page();
					}catch(Exception $e){
						unset($e);
					}
					
					$Analyzer->save_score();
					if(array_key_exists(3, $Account->roles) || array_key_exists(4, $Account->roles)){
						$new_score = $Analyzer->get_score();
						if(ITEM_ID != '') {
							if($pages[ITEM_ID]['seo_score'] != $new_score){
								$seo_message = "<br/><small>Page SEO score has been updated from ".(ITEM_ID == "" ? 0 : number_format($pages[ITEM_ID]['seo_score'],1))." to <strong>".$new_score."</strong>.</small>";
							} else {
								$seo_message = "<br/><small>Page SEO score has not changed from <strong>".$new_score."</strong>.</small>";
							}
						}
					}
				}
				
				if(count($cropimages) == 0){
					$CMSBuilder->set_system_alert('Page was successfully saved.', true);
					header("Location: " .PAGE_URL);
					exit();
				}
			}else{
				$CMSBuilder->set_system_alert('Unable to update record. '.$db->error(), false);
			}
		}else{
			$CMSBuilder->set_system_alert(implode('<br />', $errors), false);
			foreach($_POST AS $key=>$data){
				$row[$key] = $data;
			}	
		}
				
	//Crop images
	}else if(isset($_POST['crop'])){
		include("includes/jcropimages.php");
		$CMSBuilder->set_system_alert('Page was successfully saved.', true);
		header("Location: " .PAGE_URL);
		exit();
	}
	
}

?>