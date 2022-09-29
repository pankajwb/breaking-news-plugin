<?php

class BnMetaboxClass{
	/*
	* Start Up
	*/
	public function __construct(){
		//enqueue js script 
    	add_action('admin_enqueue_scripts', array($this,'wpbn_admin_scripts'));
    	// shortcode for property listing
    	add_action('admin_menu', array($this,'wpbn_add_metabox'));
    	add_action('save_post',array($this,'wpbn_save_metabox'));
    
  	}

  	/*
  	* Enqueue scripts and styles
  	*/
	public function wpbn_admin_scripts(){
		// Enqueue Datepicker (Flatpickr)
		wp_enqueue_script('flatpickr-js','https://cdn.jsdelivr.net/npm/flatpickr');
		wp_register_script('wpbn-admin-js', plugins_url('/assets/js/wpbn-admin-script.js',__FILE__), array('jquery') );
		wp_localize_script( 'wpbn-admin-js', 'wpbn_ajax_params', array(
		        'ajaxurl' => admin_url('admin-ajax.php'),
		        //'someVariable' => 'These are my socks'
		    ));
		wp_enqueue_script('wpbn-admin-js');
		wp_enqueue_style('flatpicker-css','https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
		wp_enqueue_style('wpbn-frontend-css',plugins_url('/assets/css/wpbn-backend.css',__FILE__));

	}

	/*
	* Add custom fields  
	*/
	public function wpbn_add_metabox() {

		add_meta_box(
			'wpbn_metabox', // metabox ID
			'Breaking News Settings:', // title
			array($this,'wpbn_metabox_callback'), // callback function
			'post', // post type or post types in array
			'normal', // position (normal, side, advanced)
			'default' // priority (default, low, high, core)
		);

	}

	/*
	* Custom fields display
	*/
	public function wpbn_metabox_callback( $post ) {
		//get pre saved values 
		$is_breaking_news = get_post_meta($post->ID,'is_breaking_news',true);
		$is_bn_checked = $is_breaking_news == 'on' ? 'checked="checked"' : '';
		$bn_title = get_post_meta($post->ID,'bn_title',true);
		$bn_exp_date = get_post_meta($post->ID,'bn_exp_date',true);
		$is_bn_exp = get_post_meta($post->ID,'is_bn_exp',true);
		$is_bn_exp_checked = $is_bn_exp == 'on' ? 'checked="checked"' : '';
		//output form
		$html = '<div class="bn-main-metabox-wrap">';
		$html .= '<div class="bn-field-metabox-wrap">';
		$html .= '<label for="is_breaking_news">Check the box to mark this post as a Breaking News</label>';
		$html .= ' <input type="checkbox" name="is_breaking_news" id="is_breaking_news" '.$is_bn_checked.'/>';
		$html .= '</div>';
		$html .= '<br/>';
		$html .= '<div class="bn-field-metabox-wrap">';
		$html .= '<label for"bn_title">Custom title for Breaking News (Leave empty to display post title) </label>';
		$html .= ' <input type="text" name="bn_title" id="bn_title" value="'.$bn_title.'" placeholder="Add custom title"/>';
		$html .= '</div>';
		$html .= '<br/>';
		$html .= '<div class="bn-field-metabox-wrap">';
		$html .= '<label for="bn_is_exp">Check the box to have this post removed as a Breaking News after mentioned date & time</label>';
		$html .= ' <input type="checkbox" name="is_bn_exp" id="is_bn_exp" '.$is_bn_exp_checked.'/>';
		$html .= '</div>';
		$html .= '<br/>';
		$html .= '<div class="bn-field-metabox-wrap hidden bn-exp-date">';
		$html .= '<label for"bn_exp_date">Expiration Date & Time</label>';
		$html .= ' <input type="text" name="bn_exp_date" id="bn_exp_date" value="'.$bn_exp_date.'" placeholder=""/>';
		$html .= '</div>'; // last field wrap
		$html .= '</div>'; //main wrap ends

		echo $html;
	}

	/*
	* Save custom fields data when post is published or updated
	*/
	public function wpbn_save_metabox($post_id){
		// Do not save the data if autosave
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// Save checkbox
		if( isset( $_POST[ 'is_breaking_news' ] ) ) {
			update_post_meta( $post_id, 'is_breaking_news', sanitize_text_field( $_POST[ 'is_breaking_news' ] ) );
		} else {
			delete_post_meta( $post_id, 'is_breaking_news' );
		}

		// Save title
		if( isset( $_POST[ 'bn_title' ] ) ) {
			update_post_meta( $post_id, 'bn_title', sanitize_text_field( $_POST[ 'bn_title' ] ) );
		} else {
			delete_post_meta( $post_id, 'bn_title' );
		}
		
		// Save expiration date time checkbox
		if( isset( $_POST[ 'is_bn_exp' ] ) ) {
			update_post_meta( $post_id, 'is_bn_exp', sanitize_text_field( $_POST[ 'is_bn_exp' ] ) );
		} else {
			delete_post_meta( $post_id, 'is_bn_exp' );
		}

		// Save expiration date time
		if( isset( $_POST[ 'bn_exp_date' ] ) ) {
			update_post_meta( $post_id, 'bn_exp_date', sanitize_text_field( $_POST[ 'bn_exp_date' ] ) );
		} else {
			delete_post_meta( $post_id, 'bn_exp_date' );
		}
	}
		
}