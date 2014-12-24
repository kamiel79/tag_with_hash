<?php
/**
Plugin Name: Tag With #
Version: 1.0
Plugin URI: http://code.creativechoice.org/
Description: Automatically turn terms into tags if you precede them with a #. Nonexisting tags are created.
Author: Kamiel Choi
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Author URI: http://creativechoice.org
 */

class tag_with_hash {
	var $tag_ids;
	function __construct($args = array()) {
			$this->register();
	}
		
	function register() {
			add_filter( 'content_save_pre' , array(&$this, 'tagfilter'),10,1);
			add_action( 'save_post', array(&$this, 'tagaction'));
	}
	
	function tagfilter( $content ) {

		$pattern = '/#(\w+)/i';
		$replacement = '${1}';
		preg_match_all($pattern, $content, $matches);
		
		foreach ($matches[1] as $term) {
			if (!($tid = term_exists( $term, 'post_tag' ))) {
				$t = wp_insert_term( $term, 'post_tag', array('name'=>$term) );
				$this->tag_ids[] = $t['term_id']; 
				}
			else {
				$this->tag_ids[] = intval( $tid['term_id']);
			}
		}
		
		return preg_replace($pattern, $replacement, $content );
	}
	function tagaction($postid) {

		if (is_array($this->tag_ids)) $this->tag_ids = array_unique($this->tag_ids);
		wp_set_object_terms( $postid, $this->tag_ids, 'post_tag', true);	//append tags
	}

} //tag_with_hash	

$tagwithhash = new tag_with_hash();


?>
