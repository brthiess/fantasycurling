<?php
	
//Table listing
if(ACTION == ''){
	
	include("includes/widgets/searchform.php");
	echo "<p class='f_right'><a href='" .PAGE_URL. "?action=add' class='button'><i class='fa fa-plus'></i>Add New</a></p>";
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Site Pages
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content nopadding'>";
			echo "<table cellpadding='0' cellspacing='0' border='0' id='site-pages' class='stickyheader'>";
		
			echo "<thead>";
				echo "<th width='40px' class='{sorter:false}'></th>";	
				echo "<th width='25px' class='nopadding {sorter:false}'></th>";
				echo "<th width='350px' class='{sorter:false}'>Page Name</th>";
				echo "<th class='{sorter:false}'>Parent Page</th>";
				echo "<th width='40px' class='center nopadding {sorter:false}'>Status " .$CMSBuilder->tooltip('Status', '<p><i class=\'fa fa-eye\'></i> Show &nbsp; <i class=\'fa fa-link\'></i> Hide &nbsp; <i class=\'fa fa-eye-slash\'></i> Disable</p>If you <strong>hide</strong> a page, it will be hidden from the navigation but you can still navigate to it directly. If you <strong>disable</strong> a page, it will be hidden from the navigation and you will NOT be able to navigate to it.'). "</th>";
				echo "<th align='right' class='{sorter:false}'>&nbsp;</th>";
				echo "<th width='15px' align='right' class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
						
			echo "<tbody>";
			foreach($pages as $row){
				
				//display seo score
				if($row['type'] == 0 && $cms_settings['enhanced_seo'] && (array_key_exists(3, $Account->roles) || array_key_exists(4, $Account->roles))){ //SEO and master permissions
					if($row['seo_score'] > 80){
						$seo_class = "seo-3 ";
					} else if($row['seo_score'] >= 50 && $row['seo_score'] <= 80){
						$seo_class = "seo-2 ";
					} else {
						$seo_class = "seo-1 ";
					}
					$seo_tooltip = "<span class='seo-tool tooltip' title='<h4>SEO Score: <strong>".number_format($row['seo_score'],1)."</strong></h4>'>&nbsp;</span>";
				} else {
					$seo_tooltip = "";
					$seo_class = "";
				}
				
				$pages[$row['parent_id']]['sub_pages'] = (!isset($pages[$row['parent_id']]['sub_pages']) ? array() : $pages[$row['parent_id']]['sub_pages']);
				
				//determine child level/parent status of page
				echo "<tr class='".$seo_class.($row['parent_id'] != "" ? "push lvl".$row['lvl'] : "").(!empty($row['sub_pages']) ? " has_sub" : "").($row['parent_id'] != "" && array_search($row['page_id'], array_keys($pages[$row['parent_id']]['sub_pages'])) == (count($pages[$row['parent_id']]['sub_pages'])-1) && empty($row['sub_pages']) ? " last_child" : "")."'>";
					echo "<td>$seo_tooltip" .($row['image'] != "" ? "<a href='" .$path.$imagedir.$row['image']. "' rel='prettyPhoto' title='" .$row['name']. "'>" .renderGravatar($imagedir.$row['image']). "</a>" : ""). "</td>";
					echo "<td class='center nopadding'><small>" .($row['ordering'] == 101 ? "" : $row['ordering']). "</small></td>";
					echo "<td>" .$row['name']. "</td>";
					echo "<td>" .(isset($row['parent_page']) ? $row['parent_page'] : ''). "</td>";
					echo "<td class='center nopadding'>
						<div class='page-status' data-id='".$row['page_id']."'>
							<button type='button' name='showhide' class='button center f_left" .(isset($row['showhide']) && $row['showhide'] == 0 ? " active" : ""). "' value='0'><i class='fa fa-eye'></i></button>
							<button type='button' name='showhide' class='button center f_left" .(isset($row['showhide']) && $row['showhide'] == 1 ? " active" : ""). "' value='1'><i class='fa fa-link'></i></button>".($row['page_id'] != 3 ? "<button type='button' name='showhide' class='button center f_left" .(isset($row['showhide']) && $row['showhide'] == 2 ? " active" : ""). "' value='2'><i class='fa fa-eye-slash'></i></button>" : "")."
						</div>
					</td>";
					echo "<td class='right'><a href='" .PAGE_URL. "?action=edit&item_id=" .$row['page_id']. "' class='button-sm'><i class='fa fa-pencil'></i>Edit</a></td>";
					echo "<td class='right'><a href='".($row['type'] == 0 ? $row['page_url'] : $row['url'])."' target='_blank'><i class='fa fa-external-link'></i></a></td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
					
		echo "</div>";	
	echo "</div>";
	

//Image cropping
}else if(count($cropimages) > 0){
	include("includes/jcropimages.php");

//Display form	
}else{
	
	if(ACTION == 'edit'){
		$data = $pages[ITEM_ID];
		$image = $data['image'];	
		if(!isset($_POST['save'])){
			$row = $data;
		}
		
	}else if(ACTION == 'add' && !isset($_POST['save'])){
		$image = '';		
		unset($row);
	}
	
	if(ITEM_ID != ""){
		echo "<p class='right'><small><strong>Link to Page:</strong> ".($row['type'] == 0 ? $siteurl.$row['page_url'] : $row['url'])."</small> &nbsp; <a href='".($row['type'] == 0 ? $siteurl.$row['page_url'] : $row['url'])."' target='_blank'><i class='fa fa-external-link'></i></a></p><hr/>";
	}
	
	if(ITEM_ID != "" && isset($row) && $row['type'] == 0){
		$p = $row['page_url'];
		include("includes/widgets/page_statistics.php");
	}
	
	echo "<form action='' method='post' enctype='multipart/form-data'>";
	
	if(ITEM_ID != ""){
		echo $CMSBuilder->important("<strong>Page Deletion:</strong> If you delete a page, all subpages under that section will also be deleted. <strong>This action is not undoable.</strong>");
	}
	
	//general information
	echo "<div class='panel'>";
		echo "<div class='panel-header'>General Information
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			<div class='panel-switch'>
				<label>Status " .$CMSBuilder->tooltip('Status', '<p><i class=\'fa fa-eye\'></i> Show &nbsp; <i class=\'fa fa-link\'></i> Hide &nbsp; <i class=\'fa fa-eye-slash\'></i> Disable</p>If you <strong>hide</strong> a page, it will be hidden from the navigation but you can still navigate to it directly. If you <strong>disable</strong> a page, it will be hidden from the navigation and you will NOT be able to navigate to it.'). "</label>
				<div class='page-status".(ITEM_ID == "" ? " no-ajax" : "")."' data-id='".ITEM_ID."'>
					<button type='button' name='showhide' class='button center f_left" .(!isset($row['showhide']) || (isset($row['showhide']) && $row['showhide'] == 0) ? " active" : ""). "' value='0'><i class='fa fa-eye'></i></button>
					<button type='button' name='showhide' class='button center f_left" .(isset($row['showhide']) && $row['showhide'] == 1 ? " active" : ""). "' value='1'><i class='fa fa-link'></i></button>
					".(!isset($row['page_id']) || (isset($row['page_id']) && $row['page_id'] != 3) ? "<button type='button' name='showhide' class='button center f_left" .(isset($row['showhide']) && $row['showhide'] == 2 ? " active" : ""). "' value='2'><i class='fa fa-eye-slash'></i></button>" : "")."
				</div>";
				if(ITEM_ID == ""){
					echo "<input type='hidden' name='showhide' value='0' />";
				}
			echo "</div>
		</div>";
		echo "<div class='panel-content clearfix'>";
			echo "<div class='form-field'>
				<label>Button Text <span class='required'>*</span></label>
				<input id='button-text' type='text' name='name' value='" .(isset($row['name']) ? $row['name'] : ''). "' class='input" .(in_array('name', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Parent Page" .(isset($row['system_page']) && $row['system_page'] == true ? $CMSBuilder->tooltip('Parent Page', 'Current page is a dynamic system page and cannot be moved.') : ''). "</label>
				<select name='parent_id' class='select'" .(isset($row['system_page']) && $row['system_page'] == true ? ' disabled' : ''). ">
					<option value='0'>-- None --</option>";
					
					//get all pages
					foreach($pages as $parent){
						if($parent['page_id'] != ITEM_ID && $parent['parent_id'] == "" && $parent['page_id'] > 3){
							echo "<option value='" .$parent['page_id']. "'" .(isset($row['parent_id']) && $parent['page_id'] == $row['parent_id'] ? " selected" : ""). ">" .$parent['name']. "</option>";
							//get children
							foreach($parent['sub_pages'] as $parent2){
								if($parent2['page_id'] != ITEM_ID){
									echo "<option value='" .$parent2['page_id']. "'" .(isset($row['parent_id']) && $parent2['page_id'] == $row['parent_id'] ? " selected" : ""). ">&nbsp;&nbsp; &rsaquo; " .$parent2['name']. "</option>";
									foreach($parent2['sub_pages'] as $parent3){
										if($parent3['page_id'] != ITEM_ID){
											echo "<option value='" .$parent3['page_id']. "'" .(isset($row['parent_id']) && $parent3['page_id'] == $row['parent_id'] ? " selected" : ""). "> &nbsp;&nbsp;&nbsp;&nbsp; &rsaquo; " .$parent3['name']. "</option>";
											foreach($parent3['sub_pages'] as $parent4){
												if($parent4['page_id'] != ITEM_ID){
													echo "<option value='" .$parent4['page_id']. "'" .(isset($row['parent_id']) && $parent4['page_id'] == $row['parent_id'] ? " selected" : ""). "> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &rsaquo; " .$parent4['name']. "</option>";
												}
											}
										}
									}
								}
							}
						}
					}
					
				echo "</select>
			</div>";
			echo "<div class='form-field'>
				<label>Numerical Order" .$CMSBuilder->tooltip('Numerical Order', 'Items will be displayed in the order they were added unless specified here. Items set to &quot;Default&quot; will appear after items with numerical ordering.'). "</label>
				<select name='ordering' class='select'>
					<option value='101'>Default</option>";
					for($i=1; $i<101; $i++){
						echo "<option value='" .$i. "' " .(isset($row['ordering']) && $row['ordering'] == $i ? "selected" : ""). ">" .$i. "</option>";	
					}
				echo "</select>
			</div>";
			
			echo "<br class='clear'/>";
			
			echo "<label>I would like to</label>
				<p><input type='radio' id='type1' class='radio toggle-pagecontent' name='type' value='0' " .(!isset($row['type']) || $row['type'] == 0 ? " checked" : ""). " /> <label for='type1'>Add content to this page</label> &nbsp; &nbsp; &nbsp;
				<input type='radio' id='type2' class='radio toggle-pagecontent' name='type' value='1' " .(isset($row['type']) && $row['type'] == 1 ? " checked" : ""). " /> <label for='type2'>Link this page to another page</label></p>";
			
		echo "</div>";
	echo "</div>"; //general information
	
	//external url
	echo "<div class='panel page-url" .(isset($row['type']) && $row['type'] == 1 ? "" : " hidden"). "'>";
		echo "<div class='panel-header'>Page Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
					
			echo "<div class='form-field'>
				<label>Full Page/Site URL <span class='required'>*</span></label>
				<input type='text' name='url' value='" .(isset($row['url']) ? $row['url'] : ''). "' class='input" .(in_array('url', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Open Link in</label>
				<select name='urltarget' class='select'>
					<option value='0' " .(isset($row['urltarget']) && $row['urltarget'] == '0' ? "selected" : ""). ">Same Window</option>
					<option value='1' " .(isset($row['urltarget']) && $row['urltarget'] == '1' ? "selected" : ""). ">New Window</option>
				</select>
			</div>";
		echo "</div>";
	echo "</div>"; //page details
	
	//page details
	echo "<div class='panel page-content" .(isset($row['type']) && $row['type'] == 1 ? " hidden" : ""). "'>";
		echo "<div class='panel-header'>Page Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
			echo "<div class='form-field'>
				<label>Main Header <span class='required'>*</span></label>
				<input type='text' name='page_title' value='" .(isset($row['page_title']) ? $row['page_title'] : ''). "' class='input" .(in_array('page_title', $required) ? ' required' : ''). "' />
			</div>";
			
			echo "<div class='form-field'>
				<label>Show/Hide Footer Map</label>
				<select name='google_map' class='select'>";
					echo "<option value='-1'>Use Global Website Settings</option>";
					echo "<option value='1'" .((isset($row['google_map']) && $row['google_map'] == 1) ? " selected" : ""). ">Show</option>";
					echo "<option value='0'" .((isset($row['google_map']) && $row['google_map'] == 0) ? " selected" : ""). ">Hide</option>";
				echo "</select>
			</div>";
			
			echo "<div class='form-field'>
				<label>Call to Action</label>
				<select name='cta_id' class='select'>";
					echo "<option value='0'>-- None --</option>";
					$cta_query = $db->query("SELECT * FROM pages_cta ORDER BY title");
					$cta_boxes = $db->fetch_array();
					foreach($cta_boxes as $cta){
						echo "<option value='".$cta['cta_id']."'" .(isset($row['cta_id']) && $row['cta_id'] == $cta['cta_id'] ? " selected" : ""). ">".$cta['title'].($cta['showhide'] > 0 ? " (Hidden)" : "")."</option>";
					}
				echo "</select>
			</div>";
			
		echo "</div>";
	echo "</div>"; //page details
	
	//page banner
	echo "<div class='panel page-content" .(isset($row['type']) && $row['type'] == 1 ? " hidden" : ""). "'>";
		echo "<div class='panel-header'>Page Banner
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
			if(isset($image) && $image != '' && file_exists($imagedir.$image)){
				echo "<div class='img-holder'>
					<a href='" .$path.$imagedir.$row['image']. "' rel='prettyphoto' target='_blank' title=''>
						<img src='" .$path.$imagedir.$image. "' alt='' />
					</a>
					<input type='checkbox' class='checkbox' name='deleteimage' id='deleteimage' value='1'>
  					<label for='deleteimage'>Delete Current Image</label>
				</div>";
			}
			echo "<div class='form-field'>
				<label>Upload Image" .$CMSBuilder->tooltip('Upload Image', 'Image must be smaller than 20MB.'). "</label>
				<input type='file' class='input" .(in_array('image', $required) ? ' required' : ''). "' name='image' value='' />
				<input type='hidden' name='old_image' value='" .(isset($image) && $image != '' && file_exists($imagedir.$image) ? $image : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Alt Text: <small>(SEO)</small> " .$CMSBuilder->tooltip('Alt Text', 'Provide a brief description of this image.'). "</label>
				<input type='text' name='image_alt' value='" .(isset($row['image_alt']) ? $row['image_alt'] : ''). "' class='input' />
			</div>";
		echo "</div>";
	echo "</div>"; //page banner
	
	//content, tabs, promo boxes
	echo "<div class='page-content" .(isset($row['type']) && $row['type'] == 1 ? " hidden" : ""). "'>";
		echo "<div class='tabs tab-ui'>";
			echo "<ul>";
				echo "<li><a href='#content'>Page Content</a></li>";
				echo "<li><a href='#pagetabs'>Tabbed Content</a></li>";
				echo "<li><a href='#promos'>Promo Boxes</a></li>";
			echo "</ul>";
			
			//Page Content
			echo "<div id='content'>";		
				echo "<textarea name='TINYMCE_Editor' class='tinymceEditor'>" .(isset($row['content']) ? $row['content'] : ""). "</textarea>";
			echo "</div>";//content
			
			//Page Tabbed Content
			echo "<div id='pagetabs'>";
				if(isset($row['page_tabs'])){
					foreach($row['page_tabs'] as $index => $panel){
						
						echo "<div class='tabspanel'>";
							
							echo "<div class='form-field'>
								<label>Tab Label <span class='required'>*</span></label>
								<input type='text' name='panel_title[]' value='" .$panel['title']. "' class='input" .(in_array("panel_title$index", $required) ? ' required' : ''). "' />
							</div>";
							
							echo "<div class='form-field'>
								<label>Show/Hide</label>
								<select name='panel_showhide[]' class='select'>
									<option value='0'" .(isset($panel['showhide']) && $panel['showhide'] == 0 ? " selected" : ""). ">Show</option>
									<option value='1'" .(isset($panel['showhide']) && $panel['showhide'] == 1 ? " selected" : ""). ">Hide</option>
									</select>
							</div>";
							
							echo "<div class='form-field'>
								<label>Numerical Order" .$CMSBuilder->tooltip('Numerical Order', 'Items will be displayed in the order they were added unless specified here. Items set to &quot;Default&quot; will appear after items with numerical ordering.'). "</label>
								<select name='panel_ordering[]' class='select'>
									<option value='101'>Default</option>";
									for($i=1; $i<101; $i++){
										echo "<option value='" .$i. "' " .(isset($panel['ordering']) && $panel['ordering'] == $i ? "selected" : ""). ">" .$i. "</option>";	
									}
								echo "</select>
							</div>";
							
							echo "<button type='button' class='button-sm delete-tab f_right'><i class='fa fa-trash'></i>Delete Tab</button>";
							
							echo "<br class='clear' />";
							echo "<textarea name='panel_content[]' class='tinymceEditor'>".str_replace('\r\n', '', $panel['content'])."</textarea>";
							echo "<input type='hidden' name='tab_id[]' class='tab_id' value='" .$panel['tab_id']. "' />";
																			
							echo "<br/><hr /><br/>";
				
						echo "</div>";
							
					}
				}
				
				echo "<p id='addpanel'><button type='button' class='button-sm'><i class='fa fa-plus'></i>Add New Tab</button></p><br />";
				
				//panel template
				echo "<div id='tabs_panel_template' style='display:none;'>";
					echo "<div class='form-field'>
						<label>Tab Label <span class='required'>*</span></label>
						<input type='text' name='panel_title[]' value='' class='input' />
					</div>";
					
					echo "<div class='form-field'>
						<label>Show/Hide</label>
						<select name='panel_showhide[]' class='select'>
							<option value='0'>Show</option>
							<option value='1'>Hide</option>
							</select>
					</div>";
					
					echo "<div class='form-field'>
						<label>Numerical Order" .$CMSBuilder->tooltip('Numerical Order', 'Items will be displayed in the order they were added unless specified here. Items set to &quot;Default&quot; will appear after items with numerical ordering.'). "</label>
						<select name='panel_ordering[]' class='select'>
							<option value='101'>Default</option>";
							for($i=1; $i<101; $i++){
								echo "<option value='" .$i. "'>" .$i. "</option>";	
							}
						echo "</select>
					</div>";
					
					echo "<button type='button' class='button-sm delete-tab f_right'><i class='fa fa-trash'></i>Delete Tab</button>";
										
					echo "<br class='clear' />";
					echo "<textarea name='panel_content[]' class='tinymceDynamic'></textarea>";
					echo "<input type='hidden' name='tab_id[]' class='tab_id' value='' />";
					
					echo "<br/><hr/><br/>";
					
				echo "</div>"; //end template
				
			echo "</div>";//pagetabs
			
			//Promos
			echo "<div id='promos'>";
				
				$promo_query = $db->query("SELECT * FROM promo_boxes ORDER BY ordering");
				$promo_boxes = $db->fetch_array();
				foreach($promo_boxes as $promo){
					echo "<input type='checkbox' value='" .$promo['promo_id']. "' id='promo_" .$promo['promo_id']. "' name='promo[]' class='checkbox'" .(isset($row['page_promos']) && in_array($promo['promo_id'], $row['page_promos']) ? " checked" : ""). "><label for='promo_" .$promo['promo_id']. "'>".$promo['title'].($promo['ordering'] == 101 ? "" : " <small>(".$promo['ordering'].")</small>").($promo['showhide'] > 0 ? " <small>(Hidden)</small>" : ""). "</label><br />";
				}
				
				if(empty($promo_boxes)){
					echo "<p>You must create a promo box before attaching it to a page. &nbsp; <a href='".$path."pages/promo-boxes/?action=add'>Add new promo box now</a></p>";
				}
				
			echo "</div>";//promos			
		echo "</div>";//tab-ui
	echo "</div>";//page-content
	
	//SEO Content/Analysis
	echo "<div class='page-content" .(isset($row['type']) && $row['type'] == 1 ? " hidden" : ""). "'>";
		echo "<div class='tabs tab-ui page-content" .(isset($row['type']) && $row['type'] == 1 ? " hidden" : ""). "'>";
				echo "<ul>";
					echo "<li><a href='#seo'>SEO Content</a></li>";
					if(ITEM_ID != "" && $cms_settings['enhanced_seo'] && (array_key_exists(3, $Account->roles) || array_key_exists(4, $Account->roles))){ //SEO and master permissions
						echo "<li><a href='#seoanalysis'>Page Analysis</a></li>";
						//echo "<li><a href='#conversions'>Conversion Tags</a></li>";
					}
				echo "</ul>";
		
			//SEO
			echo "<div id='seo' class='clearfix'>";
				
				$default_meta_title = (isset($row['page_title']) && $row['page_title'] != "" ? $row['page_title']." | " : "").(trim($global['meta_title']) != "" ? $global['meta_title'] : $global['company_name']);
				echo "<div class='form-field'>
					<label>SEO Title " .$CMSBuilder->tooltip('SEO Title', 'A keyword-rich page title that will appear at the very top of your browser window.'). "</label>
					<input id='seo-title' type='text' name='meta_title' value='" .(isset($row['meta_title']) && $row['meta_title'] != "" ? $row['meta_title'] : ''). "' class='input' />";
					
					if($cms_settings['enhanced_seo'] && (array_key_exists(3, $Account->roles) || array_key_exists(4, $Account->roles))){ //SEO and master permissions
						echo "<label>Slug ".$CMSBuilder->tooltip('Slug', '<p>The slug is a user- and SEO-friendly short text used to identify and describe the URL which identifies a page using human-readable keywords.</p><p>If left empty, the <strong>Button Text</strong> will be used to create the slug for this page.</p>')."</label>
							<input type='text' id='seo-slug' name='slug' value='" .(isset($row['slug']) ? $row['slug'] : ""). "' class='input" .(in_array('slug', $required) ? ' required' : ''). "' />";
					}
					
					if(array_key_exists(3, $Account->roles) || array_key_exists(4, $Account->roles)){ //SEO and master permissions
						echo "<label>Canonical URL " .$CMSBuilder->tooltip('Canonical URL', 'Set a preferred URL for your content.'). "</label>
						<input type='text' name='meta_canonical' value='" .(isset($row['meta_canonical']) ? $row['meta_canonical'] : ''). "' class='input' />";					
					} else {
						echo "<input type='hidden' name='meta_canonical' value='" .(isset($row['meta_canonical']) ? $row['meta_canonical'] : ''). "' />";
					}
					$maxchars = 160;
					echo "<label>SEO Description" .$CMSBuilder->tooltip('SEO Description', 'A keyword-rich description of the page used for search engine optimization. Will default to global website settings description if left blank.'). " <small class='f_right'><span id='count-seo-description'".(isset($row['meta_description']) && strlen($row['meta_description']) > $maxchars ? " class='error'" : "").">".(isset($row['meta_description']) ? strlen($row['meta_description']) : 0)."</span>/$maxchars</small></label>
					<textarea id='seo-description' name='meta_description' class='textarea char-count-$maxchars'>" .(isset($row['meta_description']) ? $row['meta_description'] : ''). "</textarea>
				</div>";
				
				echo "<div class='google-preview seo-preview'>
					<p>This Page in Google Search Results:</p>
					<div>";
						echo "<h2 class='seo-title'>" .(isset($row['meta_title']) && $row['meta_title'] != "" ? $row['meta_title'] : $default_meta_title). "</h2>";
						echo "<h6 class='seo-slug'>" .(isset($row['page_url']) ? $siteurl.$row['page_url'] : ""). "</h6>";
						echo "<p class='seo-description'>".(isset($row['meta_description']) ? str_limit_characters($row['meta_description'], 160) : '')."</p>";
						echo "<input id='default-url' type='hidden' name='default-url' value='".(isset($row['page_url']) ? $siteurl.$row['page_url'] : "")."' />";
						echo "<input id='default-meta-title' type='hidden' name='default-meta-title' value='".$default_meta_title."' />";
					echo "</div>
				</div>";
				
				if($cms_settings['enhanced_seo'] && (array_key_exists(3, $Account->roles) || array_key_exists(4, $Account->roles))){ //SEO and master permissions
					echo "<br class='clear'/>
					<div class='form-field'>
						<label>Focus Keyword " .$CMSBuilder->tooltip('Focus Keyword', 'The focus keyword will be used to determine the SEO ranking in the Page Analysis tab. It is recommended to use a unique keyword that is not common to any other page.'). "</label>
						<input type='text' name='focus_keyword' value='" .(isset($row['focus_keyword']) ? $row['focus_keyword'] : ''). "' class='input' />
					</div>";
				} else {
					echo "<input type='hidden' name='focus_keyword' value='" .(isset($row['focus_keyword']) ? $row['focus_keyword'] : ''). "' />";
				}
											
			echo "</div>";//seo
			
			if(ITEM_ID != "" && $cms_settings['enhanced_seo'] && (array_key_exists(3, $Account->roles) || array_key_exists(4, $Account->roles))){ //SEO and master permissions
				echo "<div id='seoanalysis' class='clearfix'>";
					if($row['type'] == 0){
						//get appropriate pages to determine average
						$records_arr = $pages;
						foreach($records_arr as $subKey => $subArray){
							if($subArray['type'] == 1){
								unset($records_arr[$subKey]);
							}
						}
						//get appropriate page url
						if($row['meta_canonical'] != "" && $row['slug'] == NULL){
							$page_url = $row['meta_canonical'];
							echo "<p class='alert'><i class='fa fa-exclamation-triangle'></i> &nbsp;<strong>Notice:</strong> This page has a canonical link set. Any changes made to this page will not reflect in the SEO score.</p>";
						} else {
							$page_url = $siteurl.$row['page_url'];
						}
						$item_table = "pages";
						$item_key = "page_id";
						include("includes/widgets/seosummary.php");
					} else {
						echo "<p class='alert'><i class='fa fa-exclamation-triangle'></i> &nbsp;<strong>Notice:</strong> This page is linked to another page and cannot be analyzed for SEO.</p>";
					}
				echo "</div>"; //seoanalysis
			}
		
		echo "</div>";//tab-iu SEO
	echo "</div>";//page-content
	
	//Sticky footer
	echo "<footer id='cms-footer' class='resize'>";
		echo "<button type='submit' class='button f_right' name='save' value='save'><i class='fa fa-check'></i>Save Changes</button>";
		if(ITEM_ID != ""){
			echo (!$row['deletable'] ? $CMSBuilder->tooltip('Delete Page', 'Due to the dynamic nature of this page, it cannot be deleted. If you wish to remove this page, hide or disable it instead.').'&nbsp;' : '');
			echo "<button type='button' name='delete' value='delete' class='button delete'" .(!$row['deletable'] ? " disabled" : ""). "><i class='fa fa-trash'></i>Delete</button>";
		}
		
		echo "<a href='" .PAGE_URL. "' class='cancel'>Cancel</a>";
	echo "</footer>";
		
	//fields with tags
	echo "<input type='hidden' name='keep_tags[]' value='TINYMCE_Editor' />";
	echo "<input type='hidden' name='keep_tags[]' value='panel_content' />";
	
	echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid'] ."' />";
	echo "</form>";
	
}

?>