<?php

/*-----------------------------------/
* SEO Analyzer for static pages and dynamic modules
* @author	Pixel Army
* @date		15-10-29
* @file		SEOAnalyzer.class.php
*/

class SEOAnalyzer{
		
	/*-----------------------------------/
	* @var db
	* Mysqli database object utilizing Database class
	*/
	private $db;
	
	/*-----------------------------------/
	* @var $page
	* Array of current page data
	*/
	private $page;
	
	/*-----------------------------------/
	* @var $score
	* Analysis score for the page
	*/
	private $score;
	
	/*-----------------------------------/
	* @var $grade
	* Grade used to determine overall ranking
	*/
	private $grade;
	
	/*-----------------------------------/
	* @var $keyword_stats
	* Flag for showing keyword specific statistics
	*/
	private $keyword_stats;
	
	/*-----------------------------------/
	* @var summary
	* Array of summary items
	*/
	private $summary;
	
	/*-----------------------------------/
	* Public constructor function
	*
	* @author	Pixel Army
	* @param	$page		Current page data to analyze
	* @return	CMSBuilder	New CMSBuilder object
	* @throws	Exception
	*/
	public function __construct(){	
						
		//Set database instance
		if(class_exists('Database')){
			$this->db = new Database();
		}else{
			throw new Exception('Missing class file `Database`');
		}		
		
		//set variables	
		$this->page = array();			
		$this->score = 0;
		$this->grade = 0;
		$this->keyword_stats = false;
		
		//Set summary item array
		$this->summary = array();
	
    }
    
    /*-----------------------------------/
	* Sets page details
	*
	* @author	Pixel Army
	* @param	$keyword				The focus keyword
	* @param	$page_url				The full url to the page - used to retrieve real-time content
	* @param	$slug					The url for the page (slug)
	* @param	$page_title				The page title
	* @param	$item_table				The table to check unique keyword and save final score
	* @param	$item_id				The item id to check unique keyword and save final score
	*/
	public function set_page($keyword, $page_url, $slug, $page_title, $item_id = NULL, $item_table = "pages", $item_key = "page_id"){
		$this->page['focus_keyword'] = $keyword;
		$this->page['slug'] = $slug;
		$this->page['page_title'] = $page_title;
		$this->page['item_id'] = $item_id;
		$this->page['table_id'] = $item_key;
		$this->page['table'] = $item_table;
		
		$this->page['meta_title'] = "";
	    $this->page['meta_description'] = "";
	    $this->page['content'] = "";
		
		//set real-time page data
		try{
			$this->fetch_page($page_url);
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
				
		if(isset($this->page['focus_keyword']) && $this->page['focus_keyword'] != ""){
			$this->keyword_stats = true;
		}	
	}
	
	/*-----------------------------------/
	* Fetches the real-time content on a page and sets the appropriate variables
	*
	* @author	Pixel Army
	* @param	$url				The full url to the page
	*/
	private function fetch_page($url){
		
		$body_copy = "";
		$meta_title = "";
		$meta_description = "";
				
		//retrieve page data
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        $page_source = curl_exec($ch);
        curl_close($ch);
       		
		if($page_source != ""){
			libxml_use_internal_errors(true); //Prevents Warnings, remove if desired
			$dom = new DOMDocument();
			$dom->loadHTML($page_source);
		
			//get meta data
			$meta_title = $dom->getElementsByTagName('title');
			$meta_title = $meta_title->item(0)->nodeValue;
			$metas = $dom->getElementsByTagName('meta');
			foreach ($metas as $meta) {
				if (strtolower($meta->getAttribute('name')) == 'description') {
					$meta_description = $meta->getAttribute('content');
				}
			}
			
			$body_copy = $page_source;
		}				
	    
	    //set page data
	    $this->page['meta_title'] = $meta_title;
	    $this->page['meta_description'] = $meta_description;
	    $this->page['content'] = $body_copy;
	    
	    if(is_null($page_source) || $page_source == ""){
		    throw new Exception('There was an error retrieving the content for this page. Page analysis could not be completed.');
	    }
	    
	}
    
    /*-----------------------------------/
	* Calculates the overall page ranking
	*
	* @author	Pixel Army
	*/
	public function analyze_page(){
		$this->get_keyword_density();		
		$this->get_missing_alts();
		$this->detect_slug_length();
		$this->detect_description_length();
		$this->count_heading_tags();
		$this->count_outbound_links();
		$this->calculate_flesch();
		$this->check_page_title();
		$this->check_slug();
		$this->check_first_paragraph();
		$this->check_meta_description();
		$this->check_content_length();
		if(isset($this->page['item_id']) && $this->page['item_id'] != ""){
			$this->check_unique_focus();
		}
		
		if(!$this->keyword_stats){
			throw new Exception('Focus keyword missing. Not all data was able to be analyzed. Please save a focus keyword to get a complete analysis for this page.');
		}
		
	}
	
	/*-----------------------------------/
	* Check if focus keyword is unique to this page
	*
	* @author	Pixel Army
	*/
	private function check_unique_focus(){
		if($this->keyword_stats){
			$is_unique = true;
			$params = array($this->page['focus_keyword'],$this->page['item_id']);
			$query = $this->db->query("SELECT focus_keyword FROM ".$this->page['table']." WHERE focus_keyword = ? AND ".$this->page['table_id']." != ?",$params);
			if($query && !$this->db->error() && $this->db->num_rows() > 0){
				$is_unique = false;
			}
			if($is_unique){
				$score = 1;
				$grade = 3;
				$message = "You've never used this focus keyword before, very good.";
			} else {
				$score = 0;
				$grade = 1;
				$message = "There are <strong>".$this->db->num_rows()."</strong> page(s) using this focus keyword already. Try creating a unique keyword for this page.";
			}
			$this->set_summary($score,1,$grade,$message);
		} else {
			$this->set_grade(1);
			$this->set_score(0);
		}
	}
	
	/*-----------------------------------/
	* Check length of body content
	*
	* @author	Pixel Army
	*/
	private function check_content_length(){
		$text = $this->cleanText($this->page['content']);
		$body_words = str_word_count($text);
		if($body_words >= 300){
			$score = 2;
			$grade = 3;
			$message = "There are <strong>$body_words</strong> words contained in the body copy, this is more than the 300 word recommended minimum.";
		} else if($body_words >= 200){
			$score = 1;
			$grade = 2;
			$message = "There are <strong>$body_words</strong> words contained in the body copy, this is less than the 300 word recommended minimum.";
		} else {
			$score = 0;
			$grade = 1;
			$message = "There are <strong>$body_words</strong> words contained in the body copy, this is less than the 300 word recommended minimum.";
		}
		$this->set_summary($score,1,$grade,$message);
	}
	
	/*-----------------------------------/
	* Check if keyword exists in meta description
	*
	* @author	Pixel Army
	*/
	private function check_meta_description(){
		if($this->keyword_stats){
			$keyword_exists = $this->count_keyword($this->page['meta_description']);
			if($keyword_exists == 0){
				$score = 0;
				$grade = 1;
				$message = "The focus keyword does not appear in the meta description.";
			} else {
				$score = 1;
				$grade = 3;
				$message = "The focus keyword appears in the meta description.";
			}
			$this->set_summary($score,1,$grade,$message);
		} else {
			$this->set_grade(1);
			$this->set_score(0);
		}
	}
	
	/*-----------------------------------/
	* Check if keyword exists in first paragraph
	*
	* @author	Pixel Army
	*/
	private function check_first_paragraph(){
		if($this->keyword_stats){
			$first_p = preg_match("/<p>(.*?)<\/p>/", $this->page['content'], $matches);
			if(!empty($matches)){
				$first_p = $matches[1];
				$keyword_exists = $this->count_keyword($first_p);
				if($keyword_exists == 0){
					$score = 0;
					$grade = 1;
					$message = "The focus keyword does not appear in the first paragraph of copy.";
				} else {
					$score = 1;
					$grade = 3;
					$message = "The focus keyword appears in the first paragraph of copy.";
				}
				$this->set_summary($score,1,$grade,$message);
			}
		} else {
			$this->set_grade(1);
			$this->set_score(0);
		}
	}
	
	/*-----------------------------------/
	* Counts the number of outgoing links in the body copy
	*
	* @author	Pixel Army
	*/
	private function count_outbound_links(){
		$total_links = 0;
		$doc = new DOMDocument();
		@$doc->loadHTML($this->page['content']);
		$links = $doc->getElementsByTagName('a');
		foreach ($links as $link) {
			$href = $link->getAttribute('href');
			if($href != "" && substr($href, 0, 1) != '/' && substr($href, 0, 1) != '#' && strpos($href,$_SERVER['HTTP_HOST']) === false) {
				$total_links++;
			}
		}
		if($total_links == 0){
			$score = 0;
			$grade = 1;
			$message = "No outbound links appear in the copy of this page. Try adding a few more (2-3) to increase your page ranking.";
		} else if($total_links > 0 && $total_links <= 2){
			$score = 1;
			$grade = 2;
			$message = "There are <strong>".$total_links."</strong> outbound links that appear in the copy of this page. Try adding a few more (1-2) to increase your page ranking.";
		} else if($total_links > 2){
			$score = 2;
			$grade = 3;
			$message = "There are <strong>".$total_links."</strong> outbound links that appear in the copy of this page, very good!";
		}
		$this->set_summary($score,2,$grade,$message);
	}
	
	/*-----------------------------------/
	* Counts the number of subheadings included in the body copy, and checks if there is more than 1 H1
	*
	* @author	Pixel Army
	* @throws	Exception
	*/
	private function count_heading_tags(){
		$tag_count = 0;
		$tags = array("<h2>","<h3>","<h4>","<h5>","<h6>");
		foreach($tags as $tag){
			$tag_count += substr_count($this->page['content'], $tag);
		}
		if($tag_count == 0){
			$score = 0;
			$grade = 1;
			$message = "No subheading tags (like an H2) appear in the copy of this page. Try adding a few more headers (2-3) to increase your page ranking.";
		} else if($tag_count > 0 && $tag_count <= 2){
			$score = 1;
			$grade = 2;
			$message = "There are <strong>".$tag_count."</strong> subheading tag(s) (like an H2) that appear in the copy of this page. Try adding a few more (1-2) to increase your page ranking.";
		} else if($tag_count > 2){
			$score = 2;
			$grade = 3;
			$message = "There are <strong>".$tag_count."</strong> subheading tags that appear in the copy of this page, very good!";
		}
		$this->set_summary($score,2,$grade,$message);
	
		//check for h1 tags
		$tag_count = substr_count($this->page['content'], "<h1>");
		if ($tag_count > 1){
			$score = 1;
			$grade = 2;
			$message = "<strong>".($tag_count)."</strong> H1 tag(s) were found on the page. H1 tags may be dynamically added to each page. For best results, only 1 H1 tag is recommended.";
		} else if($tag_count == 1){
			$score = 2;
			$grade = 3;
			$message = "<strong>".($tag_count)."</strong> H1 tag was found on the page, this is ideal.";
		} else {
			$score = 0;
			$grade = 1;
			$message = "The page does not contain any H1 tags. Add an H1 within your content area to increase your page ranking.";
		}
		$this->set_summary($score,1,$grade,$message);
	}
	
	/*-----------------------------------/
	* Checks the length of the meta description
	*
	* @author	Pixel Army
	*/
	private function detect_description_length(){
		$desc_length = strlen($this->page['meta_description']);
		if($desc_length == 0){
			$score = 0;
			$grade = 1;
			$message = "There is no meta description set for this page. The optimal range for a meta description on this page is within 110 - 160 characters.";
		} else if ($desc_length > 110 && $desc_length < 160){
			$score = 2;
			$grade = 3;
			$message = "The meta description for this page is within the optimal range of 110 - 160 characters.";
		} else if($desc_length < 110){
			$score = 1;
			$grade = 2;
			$message = "The meta description for this page is a bit short (<strong>$desc_length</strong> characters), consider increasing it to ~130 characters.";
		} else if($desc_length > 160){
			$score = 0;
			$grade = 1;
			$message = "The meta description for this page is greater than the 160 character limit (<strong>$desc_length</strong> characters) and search results may not display the full description. Try decreasing the length of the description to ~130 characters. ";
		}
		$this->set_summary($score,2,$grade,$message);
	}
	
	/*-----------------------------------/
	* Checks the length of the slug (page url)
	*
	* @author	Pixel Army
	*/
	private function detect_slug_length(){
		if (strlen($this->page['slug']) < 115){
			$score = 2;
			$grade = 3;
			$message = "The slug for this page is within 115 characters.";
		} else {
			$score = 1;
			$grade = 2;
			$message = "The slug for this page is a bit long (<strong>".strlen($this->page['slug'])."</strong> characters), consider shortening it to ~115 characters. Update the Button Text for this page to change the slug.";
		}
		$this->set_summary($score,2,$grade,$message);
	}
	
	/*-----------------------------------/
	* Checks the existence of the keyword in the page url
	*
	* @author	Pixel Army
	*/
	private function check_slug(){
		if($this->keyword_stats){
			$keyword_exists = $this->count_keyword(str_replace("-"," ",$this->page['slug']));
			if($keyword_exists == 0){
				$score = 0;
				$grade = 1;
				$message = "The focus keyword does not appear in the URL for this page. Include the focus keyword in the Button Text for this page to update the URL.";
			} else {
				$score = 1;
				$grade = 3;
				$message = "The focus keyword appears in the URL for this page.";
			}
			$this->set_summary($score,1,$grade,$message);
		} else {
			$this->set_grade(1);
			$this->set_score(0);
		}
	}
	
	/*-----------------------------------/
	* Checks the existence and position of keyword in page title, as well as the page title length
	*
	* @author	Pixel Army
	*/
	private function check_page_title(){
		if($this->keyword_stats){
			$keyword_exists = stripos($this->page['meta_title'], $this->page['focus_keyword']);
			if ($keyword_exists === false){
				$score = 0;
				$grade = 1;
				$message = "The meta page title does not contain the focus keyword. The meta page title should contain the focus keyword and it should reside at the beginning.";
			} else if($keyword_exists > 0){
				$score = 1;
				$grade = 2;
				$message = "The meta page title contains the focus keyword, but it does not appear at the beginning; try and move it to the beginning.";
			} else {
				$score = 2;
				$grade = 3;
				$message = "The meta page title contains the focus keyword at the beginning.";
			}
			$this->set_summary($score,2,$grade,$message);
		} else {
			$this->set_grade(2);
			$this->set_score(0);
		}
		
		$title_length = strlen($this->page['meta_title']);
		if($title_length > 70){
			$score = 0;
			$grade = 1;
			$message = "The meta page title is <strong>$title_length</strong> characters long, which is over the max limit of 70. Some of the title may not appear in search results.";
		} else if($title_length >= 11 && $title_length <= 20) {
			$score = 1;
			$grade = 2;
			$message = "The meta page title is <strong>$title_length</strong> characters long, which is under the recommended limit of 55-60.";
		} else if($title_length > 20 && $title_length <= 70) {
			$score = 2;
			$grade = 3;
			$message = "The meta page title is <strong>$title_length</strong> characters long, very good!";
		} else if($title_length <= 10) {
			$score = 0;
			$grade = 1;
			$message = "The meta page title is <strong>$title_length</strong> character(s) long, which is fairly short.";
		}
		$this->set_summary($score,2,$grade,$message);
	}
	
	/*-----------------------------------/
	* Determines if there are missing alt tags
	*
	* @author	Pixel Army
	* @throws	Exception
	*/
	private function get_missing_alts(){
		$has_alt = 0;
		$total_tags = 0;
		$keyword_alt = 0;
		$doc = new DOMDocument();
		@$doc->loadHTML($this->page['content']);
		$tags = $doc->getElementsByTagName('img');
		foreach ($tags as $tag) {
			$img_alt = $tag->getAttribute('alt');
			if(isset($img_alt) && trim($img_alt) != ""){
				$has_alt++;
				//check for keyword in alt tag
				if($this->keyword_stats){
					if($this->count_keyword($img_alt) > 0){
						$keyword_alt++;
					}
				}
			}
			$total_tags++;
		}
		
		//get percentage of missing alt tags
		$ratio = ($total_tags > 0 ? number_format(($has_alt/$total_tags)*100,2) : 100);
		if($total_tags > 0){
			if($ratio >= 75){
				$score = 2;
				$grade = 3;
				$message = "<strong>$has_alt out of $total_tags</strong> image tags contain an alt text.";
			} else if($ratio >= 50 && $ratio < 75){
				$score = 1;
				$grade = 2;
				$message = "<strong>$has_alt out of $total_tags</strong> images on this page contain alt text. Consider adding alt text to the remaining images to increase your rank.";
			} else {
				$score = 0;
				$grade = 1;
				$message = "<strong>$has_alt out of $total_tags</strong> images on this page contain alt text. This page content will rank lower in searches without alt text on images.";
			}
			$this->set_summary($score,2,$grade,$message);
		}
		
		//checking alt tags for focus keyword
		if($this->keyword_stats && $total_tags > 0){
			if($keyword_alt == $total_tags){
				$score = 2;
				$grade = 3;
				$message = "All images on this page contain the focus keyword in the alt text.";
			} else if($ratio >= 50 && $ratio < 75){
				$score = 1;
				$grade = 2;
				$message = "<strong>$keyword_alt out of $total_tags</strong> images on this page have alt text containing the focus keyword.";
			} else {
				$score = 0;
				$grade = 1;
				$message = "The images on this page do not have alt text containing the focus keyword. All images should contain alt text (including the focus keyword will also increase page ranking).";
			}
			$this->set_summary($score,2,$grade,$message);
		} else {
			$this->set_grade(2);
			$this->set_score(0);
		}
	}
	
	/*-----------------------------------/
	* Calculates the keyword density
	*
	* @author	Pixel Army
	*/
	private function get_keyword_density(){
		if($this->keyword_stats){
			$text = strip_tags(str_replace("&nbsp;"," ",$this->cleanText($this->page['content'])));
			$keyword = $this->page['focus_keyword'];
			$word_count = explode(' ', $text);
			$word_count = count($word_count);
			$keyword_count = preg_match_all("#{$keyword}#si", $text, $matches);
			$keyword_count = count($matches[0]);
			$density = number_format(($keyword_count/$word_count)*100,2);
			if($density > 3){
				$score = 1;
				$grade = 2;
				$message = "Your keyword density is <strong>$density%</strong>, which is high and might be read as spam. The focus keyword was found <strong>$keyword_count</strong> time(s); try to reduce the keyword or add additional content to get a better rating. Optimal keyword density is 1%-3%.";
			} else if($density >= 1 && $density <= 3){
				$score = 2;
				$grade = 3;
				$message = "Your keyword density is <strong>$density%</strong>, which is within the optimal range (1%-3%). The focus keyword was found <strong>$keyword_count</strong> time(s).";
			} else {
				$score = 0;
				$grade = 1;
				$message = "Your keyword density is <strong>$density%</strong>, which is low. Optimal keyword density is 1%-3%. The focus keyword was found <strong>$keyword_count</strong> time(s).";
			}
			$this->set_summary($score,2,$grade,$message);	
		} else {
			$this->set_grade(2);
			$this->set_score(0);
		}
	}
	
	/*-----------------------------------/
	* Counts the number of times the keyword is found in a string
	*
	* @author	Pixel Army
	* @param	$keyword			The keyword to find
	* @param	$text				The content to be tested
	* @throws	Exception
	*/
	private function count_keyword($text){
		if($this->keyword_stats){
			$keyword = $this->page['focus_keyword'];
			$text = strip_tags(str_replace("&nbsp;"," ",$text));
			$keyword_count = preg_match_all("#{$keyword}#si", $text, $matches);
			$keyword_count = count($matches[0]);
			return $keyword_count;
		}
	}
			
	/*-----------------------------------/
	* Retrieves the Flesch reading ease score for a block of content
	*
	* @author	Pixel Army
	* @param	$text			The content to be tested
	* @returns	Average of calculated text
	*/
	function calculate_flesch() {
		$text = $this->cleanText($this->page['content']);
	    $flesch_score = (206.835 - (1.015 * $this->average_words_sentence($text)) - (84.6 * $this->average_syllables_word($text)));
	    $flesch_score = number_format($flesch_score,1);
	    $tip = "<a href='https://en.wikipedia.org/wiki/Flesch%E2%80%93Kincaid_readability_tests' target='_blank'><span class='tooltip nopadding' title='<h4>Flesch Reading Ease</h4><p>Based on a 0-100 scale. A high score means the text is easier to read. Low scores suggest the text is complicated to understand.</p><hr/><strong>90.0-100.0</strong> Easily understood by an average 11-year-old student<br/><strong>60.0-70.0</strong> Easily understood by 13- to 15-year-old students.<br/><strong>0.0.-30.0</strong> best understood by university graduates.'>Flesch Reading Ease</span></a>";
	    if($flesch_score < 30){
		    $score = 0;
			$grade = 1;
			$message = "This page copy scores <strong>$flesch_score</strong> in the $tip test, which may be considered difficult to read by the average user.";
	    } else if($flesch_score >= 50 && $flesch_score < 80){
		    $score = 2;
			$grade = 3;
			$message = "This page copy scores <strong>$flesch_score</strong> in the $tip test. This page copy can be easily understood by the average user.";
	    } else if($flesch_score >=30 && $flesch_score < 50){
		    $score = 1;
			$grade = 2;
			$message = "This page copy scores <strong>$flesch_score</strong> in the $tip test, which is considered fairly easy to read.";
	    } else if($flesch_score >= 80 && $flesch_score < 90){
		    $score = 1;
			$grade = 2;
			$message = "This page copy scores <strong>$flesch_score</strong> in the $tip test, which is considered to be just above the average reading level.";
	    } else if($flesch_score > 90){
		    $score = 0;
			$grade = 1;
			$message = "This page copy scores <strong>$flesch_score</strong> in the $tip test, which is considered extremely easy to read.";
	    }
	    $this->set_summary($score,2,$grade,$message);
	}
	
	/*-----------------------------------/
	* Retrieves average words per sentence - used to get the Flesch reading ease score
	*
	* @author	Pixel Army
	* @param	$text			The content to be tested
	* @returns	Average of calculated text
	*/
	private function average_words_sentence($text) {
	    $sentences = strlen(preg_replace('`[^\.!?]`', '', $text));  
	    $words = 1 + strlen(preg_replace('`[^ ]`', '', $text)); // Space count + 1 is word count
	    return number_format(($words/$sentences),2);
	}
	
	/*-----------------------------------/
	* Retrieves average syllables per word - used to get the Flesch reading ease score
	*
	* @author	Pixel Army
	* @param	$text			The content to be tested
	* @returns	Average of calculated text
	*/
	private function average_syllables_word($text) {
	    $words = explode(' ', $text);
	    $syllables = 0;
	    for ($i = 0; $i < count($words); $i++) {
	        $syllables += $this->count_syllables($words[$i]);
	    }
	    return number_format(($syllables/count($words)),2);
	}
	
	/*-----------------------------------/
	* Retrieves the number of syllables for a word - used to get the Flesch reading ease score
	*
	* @author	Code snippets taken from - TextStatistics Project
	*		    https://github.com/DaveChild/Text-Statistics
	*		    Released under New BSD license
	*		    http://www.opensource.org/licenses/bsd-license.php
	* @param	$word			The word to be tested
	* @returns	Number of syllables for the entered word
	*/
	function count_syllables($word) {
	    $intSyllableCount = 0;
	    
	    // Specific common exceptions that don't follow the rule set below are handled individually.
		// Array of problem words (with word as key, syllable count as value).
		$arrProblemWords = array(
			'simile'    => 3,
			'forever'   => 3,
			'shoreline' => 2,
		);
		
		if(isset($arrProblemWords[$word])) {
			return $arrProblemWords[$word];
		}
	    
	    // These syllables would be counted as two but should be one.
		$arrSubSyllables = array(
			'cial',
			'tia',
			'cius',
			'cious',
			'giu',
			'ion',
			'iou',
			'sia$',
			'[^aeiuoyt]{2,}ed$',
			'.ely$',
			'[cg]h?e[rsd]?$',
			'rved?$',
			'[aeiouy][dt]es?$',
			'[aeiouy][^aeiouydt]e[rsd]?$',
			// Sorts out deal, deign etc.
			'^[dr]e[aeiou][^aeiou]+$',
			// Purse, hearse.
			'[aeiouy]rse$',
		);
	
	    // These syllables would be counted as one but should be two
	    $arrAddSyllables = array(
	         '([^s]|^)ia'
	        ,'riet'
	        ,'dien' // audience
	        ,'iu'
	        ,'io'
	        ,'eo($|[b-df-hj-np-tv-z])'
	        ,'ii'
	        ,'[ou]a$'
	        ,'[aeiouym]bl$'
	        ,'[aeiou]{3}'
	        ,'[aeiou]y[aeiou]'
	        ,'^mc'
	        ,'ism$'
	        ,'asm$'
	        ,'thm$'
	        ,'([^aeiouy])\1l$'
	        ,'[^l]lien'
	        ,'^coa[dglx].'
	        ,'[^gq]ua[^auieo]'
	        ,'dnt$'
	        ,'uity$'
	        ,'[^aeiouy]ie(r|st|t)$'
	        ,'eings?$'
	        ,'[aeiouy]sh?e[rsd]$'
	        ,'iell'
	        ,'dea$'
	        ,'real' // real, cereal
	        ,'[^aeiou]y[ae]' // bryan, byerley
	        ,'gean$' // aegean
	        ,'uen' // influence, affluence
	    );
	
	    // Single syllable prefixes and suffixes
	    $arrAffix = array(
	         '`^un`'
	        ,'`^fore`'
	        ,'`^ware`'
	        ,'`^none?`'
	        ,'`^out`'
	        ,'`^post`'
	        ,'`^sub`'
	        ,'`^pre`'
	        ,'`^pro`'
	        ,'`^dis`'
	        ,'`^side`'
	        ,'`ly$`'
	        ,'`less$`'
	        ,'`some$`'
	        ,'`ful$`'
	        ,'`ers?$`'
	        ,'`ness$`'
	        ,'`cians?$`'
	        ,'`ments?$`'
	        ,'`ettes?$`'
	        ,'`villes?$`'
	        ,'`ships?$`'
	        ,'`sides?$`'
	        ,'`ports?$`'
	        ,'`shires?$`'
	        ,'`tion(ed)?$`'
	    );
	
	    // Double syllable prefixes and suffixes
	    $arrDoubleAffix = array(
	         '`^above`'
	        ,'`^ant[ie]`'
	        ,'`^counter`'
	        ,'`^hyper`'
	        ,'`^afore`'
	        ,'`^agri`'
	        ,'`^in[ft]ra`'
	        ,'`^inter`'
	        ,'`^over`'
	        ,'`^semi`'
	        ,'`^ultra`'
	        ,'`^under`'
	        ,'`^extra`'
	        ,'`^dia`'
	        ,'`^micro`'
	        ,'`^mega`'
	        ,'`^kilo`'
	        ,'`^pico`'
	        ,'`^nano`'
	        ,'`^macro`'
	        ,'`berry$`'
	        ,'`woman$`'
	        ,'`women$`'
	    );
	
	    // Triple syllable prefixes and suffixes
	    $arrTripleAffix = array(
	         '`ology$`'
	        ,'`ologist$`'
	        ,'`onomy$`'
	        ,'`onomist$`'
	    );
		
		// Based on Greg Fast's Perl module Lingua::EN::Syllables
	    $word = preg_replace('/[^a-z]/is', '', strtolower($word));
	            
        // Remove prefixes and suffixes and count how many were taken
        $word = preg_replace($arrAffix, '', $word, -1, $intAffixCount);
        $word = preg_replace($arrDoubleAffix, '', $word, -1, $intDoubleAffixCount);
        $word = preg_replace($arrTripleAffix, '', $word, -1, $intTripleAffixCount);
	    
	    // Removed non-word characters from word
        $arrWordParts = preg_split('`[^aeiouy]+`', $word);
        $intWordPartCount = 0;
        foreach ($arrWordParts as $strWordPart) {
            if ($strWordPart <> '') {
                $intWordPartCount++;
            }
        }
	    
	    // Some syllables do not follow normal rules - check for them
        $intSyllableCount = $intWordPartCount + $intAffixCount + (2 * $intDoubleAffixCount) + (3 * $intTripleAffixCount);

        foreach ($arrSubSyllables as $strSyllable) {
            $intSyllableCount -= preg_match('`' . $strSyllable . '`', $word);
        }
        foreach ($arrAddSyllables as $strSyllable) {
            $intSyllableCount += preg_match('`' . $strSyllable . '`', $word);
        }
        $intSyllableCount = ($intSyllableCount == 0) ? 1 : $intSyllableCount;
	    
	    return $intSyllableCount;
	    
	}
	
	/*-----------------------------------/
	* Trims, removes line breaks, multiple spaces and generally cleans text before processing.
	*
	* @author	Pixel Army
	* @param	$strText			The text to be sanitized
	* @returns	string				The inputted text cleaned up
	*/
    public static function cleanText($strText)
    {

        // Check for boolean before processing as string
        if (is_bool($strText)) {
            return '';
        }       

        $strText = utf8_decode(str_replace("&nbsp;"," ",$strText));

        // Curly quotes etc
        $strText = str_replace(
            array(
                "\xe2\x80\x98",
                "\xe2\x80\x99",
                "\xe2\x80\x9c",
                "\xe2\x80\x9d",
                "\xe2\x80\x93",
                "\xe2\x80\x94",
                "\xe2\x80\xa6"
            ),
            array(
                "'",
                "'",
                '"',
                '"',
                '-',
                '--',
                '...'
            ),
            $strText
        );
        $strText = str_replace(
            array(
                chr(145),
                chr(146),
                chr(147),
                chr(148),
                chr(150),
                chr(151),
                chr(133)
            ),
            array(
                "'",
                "'",
                '"',
                '"',
                '-',
                '--',
                '...'
            ),
            $strText
        );

        // Replace periods within numbers
        $strText = preg_replace('`([^0-9][0-9]+)\.([0-9]+[^0-9])`mis', '${1}0$2', $strText);

        // Handle HTML. Treat block level elements as sentence terminators and
        // remove all other tags.
        $strText = preg_replace('`<script(.*?)>(.*?)</script>`is', '', $strText);
        $strText = preg_replace('`\</?(address|blockquote|center|dir|div|dl|dd|dt|fieldset|form|h1|h2|h3|h4|h5|h6|menu|noscript|ol|p|pre|table|ul|li)[^>]*>`is', '.', $strText);
        $strText = html_entity_decode($strText);
        $strText = strip_tags($strText);

        // Assume blank lines (i.e., paragraph breaks) end sentences (useful
        // for titles in plain text documents) and replace remaining new
        // lines with spaces
        $strText = preg_replace('`(\r\n|\n\r)`is', "\n", $strText);
        $strText = preg_replace('`(\r|\n){2,}`is', ".\n\n", $strText);
        $strText = preg_replace('`[ ]*(\n|\r\n|\r)[ ]*`', ' ', $strText);

        // Replace commas, hyphens, quotes etc (count as spaces)
        $strText = preg_replace('`[",:;()/\`-]`', ' ', $strText);

        // Unify terminators and spaces
        $strText = trim($strText, '. ') . '.'; // Add final terminator.
        $strText = preg_replace('`[\.!?]`', '.', $strText); // Unify terminators
        $strText = preg_replace('`([\.\s]*\.[\.\s]*)`mis', '. ', $strText); // Merge terminators separated by whitespace.
        $strText = preg_replace('`[ ]+`', ' ', $strText); // Remove multiple spaces
        $strText = preg_replace('`([\.])[\. ]+`', '$1', $strText); // Check for duplicated terminators
        $strText = trim(preg_replace('`[ ]*([\.])`', '$1 ', $strText)); // Pad sentence terminators

        // Lower case all words following terminators (for gunning fog score)
        $strText = preg_replace_callback('`\. [^\. ]`', create_function('$matches', 'return strtolower($matches[0]);'), $strText);

        $strText = trim($strText);

        return $strText;
    }
    
    /*-----------------------------------/
	* Updates the overall score for this page
	*
	* @author	Pixel Army
	* @param	$score			The score for this reading
	*/
	private function set_score($score=0){
		$this->score += $score;
	}
	
	/*-----------------------------------/
	* Retrieves the overall score for this page
	*
	* @author	Pixel Army
	* @returns	Overall score based on grade
	*/
	public function get_score(){
		if($this->score == 0){
			return 0;
		}
		return number_format(($this->score/$this->grade)*100,1);
	}
	
	/*-----------------------------------/
	* Saves the overall score for this page
	*
	* @author	Pixel Army
	*/
	public function save_score($score = NULL, $item_id = NULL){
		$score = (isset($score) && $score != "" ? $score : $this->get_score());
		$item_id = (isset($item_id) && $item_id != "" ? $item_id : $this->page['item_id']);
		$query = $this->db->query("UPDATE ".$this->page['table']." SET seo_score = ? WHERE ".$this->page['table_id']." = ?",array($score,$item_id));
	}
	
	/*-----------------------------------/
	* Updates the overall grade for determining the score of this page
	*
	* @author	Pixel Army
	* @param	$grade			The grade the item is worth
	*/
	private function set_grade($grade=0){
		$this->grade += $grade;
	}
	
	/*-----------------------------------/
	* Sets analysis summary list and saves overall score
	*
	* @author	Pixel Army
	* @param	$score			The score for this reading
	* @param	$worth			The worth of this item (makes 
	* @param	$grade			The grade given to determine items requiring attention (1=red,2=orange,3=green)
	* @param	$message		The summary message
	*/
	private function set_summary($score,$worth,$grade=1,$message=""){
		$this->set_grade($worth);
		$this->set_score($score);
		$this->summary[] = array(
			'grade' => $grade, 
			'message' => $message,
		);
	}
	
	/*-----------------------------------/
	* Retrieves summary items
	*
	* @author	Pixel Army
	* @returns	Array of data
	*/
	public function get_summary(){
		return $this->summary;
	}
	
	/*-----------------------------------/
	* Retrieves pages with a score of less than 50
	*
	* @author	Pixel Army
	* @param	$table			The table to fetch scores from
	* @returns	Array of data
	*/
	public function get_problem_pages($table = "pages"){
		$response = array();
		$query = $this->db->query("SELECT * FROM $table WHERE seo_score < 50 ORDER BY seo_score");
		if($query && !$this->db->error()){
			$response = $this->db->fetch_array();
		}
		return $response;
	}
		
}

?>