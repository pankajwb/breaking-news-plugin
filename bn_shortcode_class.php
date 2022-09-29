<?php
/* 
* Breaking News Shortcode class
*/
class BnShortcodeClass{
	/*
	* Start up
	*/
	public function __construct(){
		add_shortcode('breaking_news',array($this,'wpbn_shortcode'));
		add_action('wp_body_open',array($this,'wpbn_add_html'));
		add_action('wp_enqueue_scripts', array($this,'wpbn_frontend_scripts'));
		add_action('wp_head', array($this,'wpbn_update_flag'));
	}

	/* 
	* Enqueue Styles
	*/
	public function wpbn_frontend_scripts(){
		wp_enqueue_style('wpbn-frontend-css',plugins_url('/assets/css/wpbn-frontend.css',__FILE__));
	}

	/*
	* Shortcode callback
	*/
	public function wpbn_shortcode($atts){
		// get latest post with Breaking news flag
		$args = array(
			'post_type' => 'post',
			'posts_per_page' => 1,
			'meta_key' => 'is_breaking_news',
			'meta_value' => 'on',
			'orderby'	=> 'modified'
		);
		$latest_bn = new WP_Query($args);
		//print_r($latest_bn);
		if($latest_bn->have_posts()){
			$i = 0;
			while($latest_bn->have_posts()){
				$latest_bn->the_post();
				$is_exp_date = get_post_meta(get_the_ID(),'is_bn_exp',true);
				$exp_date = strtotime(get_post_meta(get_the_ID(),'bn_exp_date',true));
				$this_date = time();
				$html = '';
				// Get custom options set in plugin settings
        		$this->options = get_option( 'wpbn_option_name' ); 
				$bn_title = $this->options['bn_title'] ? $this->options['bn_title'] : 'Breaking News:';
				$bn_bg_color = $this->options['bn_bg_color'] ? $this->options['bn_bg_color'] : '#fff';
				$bn_text_color = $this->options['bn_text_color'] ? $this->options['bn_text_color'] : '#000';
				if($is_exp_date == 'on' && $this_date < $exp_date && $i == 0){
					$html = '<div class="breaking-news-wrap" style="background-color: '.$bn_bg_color.'; color: '.$bn_text_color.';">['.$bn_title.' ';
					$news_title = get_post_meta(get_the_ID(),'bn_title',true) ? get_post_meta(get_the_ID(),'bn_title',true) : get_the_title();
					$html .= $news_title.' ]</div>';
					$i++;
				}elseif(empty($is_exp_date) && $i == 0){
					$html = '<div class="breaking-news-wrap" style="background-color: '.$bn_bg_color.'; color: '.$bn_text_color.';">['.$bn_title.' ';
					$news_title = get_post_meta(get_the_ID(),'bn_title',true) ? get_post_meta(get_the_ID(),'bn_title',true) : get_the_title();
					$html .= $news_title.' ]</div>';
					$i++;
				}
				
				
				return $html;
			}
		}else{
			return 'nothing found';
		}
		
	}

	/*
	* Add breaking news section on frontend at the start of body tag. 
	*/
	public function wpbn_add_html(){
		// we can just call the shortcode
		echo do_shortcode('[breaking_news]');
	}

	/*
	* Check the breaking news posts for expiration date and remove the flag for expired posts
	*/
	public function wpbn_update_flag(){
		// get latest post with Breaking news flag
		$args = array(						//TODO: have the query in a reusable plugin
			'post_type' => 'post',
			'posts_per_page' => -1,
			'meta_key' => 'is_breaking_news',
			'meta_value' => 'on',
			'orderby'	=> 'modified'
		);
		$latest_bn = new WP_Query($args);
		if($latest_bn->have_posts()){
			$i = 0;
			while($latest_bn->have_posts()){
				$latest_bn->the_post();
				$is_exp_date = get_post_meta(get_the_ID(),'is_bn_exp',true);
				$exp_date = strtotime(get_post_meta(get_the_ID(),'bn_exp_date',true));
				$this_date = time();
				if($is_exp_date == 'on' && $this_date > $exp_date){
					delete_post_meta( get_the_ID(), 'is_breaking_news' );
				}
			}
		}
	}
}