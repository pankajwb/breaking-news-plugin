<?php
/* 
* Breaking News Admin Options page
* 
*/
class BnAdminClass{
	/**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'wpbn_options_page' ) );
        add_action( 'admin_init', array( $this, 'wpbn_page_init' ) );
        add_action( 'admin_enqueue_scripts', array($this,'wpbn_enqueue_color_picker' ));

    }

    public function wpbn_enqueue_color_picker( $hook_suffix ) {
	    wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script( 'wpbn-admin-options-js', plugins_url('/assets/js/wpbn-admin-options.js',__FILE__), array( 'wp-color-picker' ), false, true );
	}

    /**
     * Add options page
     */
    public function wpbn_options_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Breaking News Settings', 
            'manage_options', 
            'wpbn-setting-admin', 
            array( $this, 'wpbn_create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function wpbn_create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'wpbn_option_name' );
        $args = array(
			'post_type' => 'post',
			'posts_per_page' => -1,
			'meta_key' => 'is_breaking_news',
			'meta_value' => 'on',
			'orderby'	=> 'modified'
		);
		$latest_bn = new WP_Query($args);

        ?>
        <div class="wrap">
            <h1>Breaking News Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'wpbn_option_group' );
                do_settings_sections( 'wpbn-setting-admin' );
                submit_button();
            ?>
            </form>
            <div class="current-bn">
            	<?php
            	if($latest_bn->have_posts()){
					$i = 0;
					while($latest_bn->have_posts()){
						$latest_bn->the_post();
						$is_exp_date = get_post_meta(get_the_ID(),'is_bn_exp',true);
						$exp_date = strtotime(get_post_meta(get_the_ID(),'bn_exp_date',true));
						$this_date = time();
						if($is_exp_date == 'on' && $this_date < $exp_date && $i==0){
							echo '<h3>Current Breaking News Post: '.get_the_title().'<a href="'.get_edit_post_link().'">(Edit Post)</a></h3>';
							$i++;
						}elseif(empty($is_exp_date) && $i == 0){
							echo '<h3>Current Breaking News Post: '.get_the_title().'<a href="'.get_edit_post_link().'">(Edit Post)</a></h3>';
							$i++;
						}
					}
				}else{
					echo 'No active breaking news found!';
				}

            	?>
            </div>
            <div class="info">
            	<h3>Frontend Display: </h3>
            	<p>The section is automatically added at start of Body tag on every page.</p>
            	<p>You can also use the following shortcode to place the Breaking news section anywhere in you theme or content</p>
            	<pre>[breaking_news]</pre>

            </div>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function wpbn_page_init()
    {        
        register_setting(
            'wpbn_option_group', // Option group
            'wpbn_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Breaking News Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'wpbn-setting-admin' // Page
        );  

        add_settings_field(
            'bn_title', 
            'Title for Breaking News Area', 
            array( $this, 'bn_title_callback' ), 
            'wpbn-setting-admin', 
            'setting_section_id'
        );      

        add_settings_field(
            'bn_bg_color', 
            'Backgroung color for Breaking News Area', 
            array( $this, 'bn_bg_color_callback' ), 
            'wpbn-setting-admin', 
            'setting_section_id'
        ); 

        add_settings_field(
            'bn_text_color', 
            'Text color for Breaking News Area', 
            array( $this, 'bn_text_color_callback' ), 
            'wpbn-setting-admin', 
            'setting_section_id'
        ); 
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['bn_title'] ) )
            $new_input['bn_title'] = sanitize_text_field( $input['bn_title'] );
        if( isset( $input['bn_bg_color'] ) )
            $new_input['bn_bg_color'] = sanitize_text_field( $input['bn_bg_color'] );
        if( isset( $input['bn_text_color'] ) )
            $new_input['bn_text_color'] = sanitize_text_field( $input['bn_text_color'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Customize Breaking News Options:';
    }


    /** 
     * Get the settings option array and print one of its values
     */
    public function bn_title_callback()
    {
        printf(
            '<input type="text" id="bn_title" name="wpbn_option_name[bn_title]" value="%s" />',
            isset( $this->options['bn_title'] ) ? esc_attr( $this->options['bn_title']) : ''
        );
    }

    public function bn_bg_color_callback()
    {
        printf(
            '<input type="text" id="bn_bg_color" name="wpbn_option_name[bn_bg_color]" value="%s" />',
            isset( $this->options['bn_bg_color'] ) ? esc_attr( $this->options['bn_bg_color']) : ''
        );
    }

    public function bn_text_color_callback()
    {
        printf(
            '<input type="text" id="bn_text_color" name="wpbn_option_name[bn_text_color]" value="%s" />',
            isset( $this->options['bn_text_color'] ) ? esc_attr( $this->options['bn_text_color']) : ''
        );


  	}
}
