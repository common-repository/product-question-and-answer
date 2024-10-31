<?php 
/*Plugin Name: Product Question and Answer 
	Plugin URI: https://acespritech.com/services/wordpress-extensions/
	Description: This plugin helps you to get product Question and answer from customer and manage it easily. 
	Author: Acespritech Solutions Pvt. Ltd.
	Author URI: https://acespritech.com/
	Version: 1.1.0
	Domain Path: /languages/
*/
// include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( ! defined( 'ABSPATH' ) ) 
	{
		exit;
	}

add_action('admin_menu', 'aspl_qa_my_menu_pages');
function aspl_qa_my_menu_pages(){
	if ( current_user_can( 'edit_others_posts' ) ) {
	    add_menu_page('Question Answer', 'Question Answer', 'manage_options', 'Question_Answer', 'aspl_question_answer','dashicons-editor-help' );
	    add_submenu_page( 
	    	null,
	        'Question Answer',
	        'Question Answer',
	        'manage_options',
	        'aspl-qa-my-custom-submenu-page',
	        'aspl_qa_my_custom_submenu_page_callback'
	    );
	}
}

function aspl_qa_my_custom_submenu_page_callback(){
	include 'answer.php';
}

add_filter( 'submenu_file', function($submenu_file){
    $screen = get_current_screen();
    if($screen->id === 'id-of-page-to-hide'){
        $submenu_file = 'id-of-page-to-higlight';
    }
    return $submenu_file;
});

function aspl_question_answer(){	
	 include 'mainqa.php';
}

function aspl_qa_installer(){

	if ( current_user_can( 'edit_others_posts' ) ) {
	    include('db.php');
	}
}
register_activation_hook( __file__, 'aspl_qa_installer' );

/**
* Tab for product page user side
**/
add_filter( 'woocommerce_product_tabs', 'aspl_qa_my_simple_custom_tab' );

function aspl_qa_my_simple_custom_tab( $tabs ) {
	$wc_setting_check_qa = esc_attr(get_option( 'aspl_question_answer_auto_insert' ));

	global $post;
	if ($wc_setting_check_qa == 'yes') {

		global $wpdb;
		$id = $post->ID;
		$ap_status = 1;
		
		$table_name = $wpdb->prefix . "qa_question";
		$question12 = $wpdb->get_results("SELECT * FROM $table_name where p_id = '$id' and approve = '$ap_status' order by q_ID desc");
		$que_coun = count($question12);
		$tabs['my_custom_tab'] = array(
			    'title'     => __( 'Product Q/A ('.$que_coun.')', 'textdomain' ),
			    'callback'  => 'aspl_qa_woo_new_product_tab_content',
			    'priority'  => 50
			);
		return $tabs;

	}else{

		if ( is_product() && get_post_meta( $post->ID, '_enableqa', true ) == 'yes' ){

			global $wpdb;
			$id = $post->ID;
			$ap_status = 1;
			$table_name = $wpdb->prefix . "qa_question";
			$question12 = $wpdb->get_results("SELECT * FROM $table_name where p_id = '$id' and approve = '$ap_status' order by q_ID desc");
			$que_coun = count($question12);
			$tabs['my_custom_tab'] = array(
						'title'     => __( 'Product Q/A ('.$que_coun.')', 'textdomain' ),
						'callback'  => 'aspl_qa_woo_new_product_tab_content',
						'priority'  => 50
					);
			return $tabs;
		}
	}
}
function aspl_qa_woo_new_product_tab_content($slug , $tab){
		include 'tabs_qa.php';
}

/**
* Create the section beneath the products tab
**/
add_filter( 'woocommerce_get_sections_products', 'aspl_wc_question_answer_add_section' );
function aspl_wc_question_answer_add_section( $sections ) {
		
	$sections['aspl_question_answer'] = __( 'Question and Answer', 'woocommerce-qa' );
	return $sections;
		
}

add_filter( 'woocommerce_get_settings_products', 'aspl_wc_question_answer_all_settings', 10, 2 );
function aspl_wc_question_answer_all_settings( $settings, $current_section ) {
	/**
	* Check the current section is what we want
 	**/
	if ( $current_section == 'aspl_question_answer' ) {

		$settings_qa = array();
		$settings_qa[] = array( 'name' => __( 'Woocommerce Product Question Answer', 'woocommerce-qa' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure Woocommerce Product Question and Answer.', 'woocommerce-qa' ), 'id' => 'aspl_question_answer' );
			
		// Add first checkbox option
		$settings_qa[] = array(
			'name'     => __( 'Insert Woocommerce Product Question and Answer Option for All Product', 'woocommerce-qa' ),
			'desc_tip' => __( 'This will automatically insert Product Question and Answer Product tab in product detail page.', 'woocommerce-qa' ),
			'id'       => 'aspl_question_answer_auto_insert',
			'type'     => 'checkbox',
			'css'      => 'width:300px;',
			'desc'     => __( 'Select Option for All Product ', 'woocommerce-qa' )
				 
		);

		$settings_qa[] = array( 'type' => 'sectionend', 'id' => 'aspl_question_answer' );

		return $settings_qa;
		
	}else{
		return $settings;
	}
}
 
/* *
* Tab for for product admin side
**/	
function aspl_qa_custom_product_tabs($tabs12){
	$tabs12['ASPL'] = array(
			'label' => __('ASPL', 'woocommerce12'),
			'target' => 'aspl_options',
			'class' => array('show_if_simple', 'show_if_variable'),
		);
	return $tabs12;
}
add_filter('woocommerce_product_data_tabs','aspl_qa_custom_product_tabs');

function aspl_options_product_tab_content(){
		
	global $post;
		
	?>
			
		<div id='aspl_options' class='panel woocommerce_options_panel'>
			<div>
				<h1>Question and Answer</h1>
			</div>
			<div class='options_group'>
				<?php
					woocommerce_wp_checkbox( array(
						'id' 		=> '_enableqa',
						'label' 	=> __( 'Enable/Disable Q and A Module', 'woocommerce' ),
					) );
				?>

			</div>
		</div>
	<?
}
add_filter('woocommerce_product_data_panels','aspl_options_product_tab_content');

// save data

function aspl_qa_save_question_answer_option_fields( $post_id ) {

	$allow_personal_message = isset( $_POST['_enableqa'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_enableqa', $allow_personal_message );

}

add_action( 'woocommerce_process_product_meta_simple', 'aspl_qa_save_question_answer_option_fields'  );
add_action( 'woocommerce_process_product_meta_variable', 'aspl_qa_save_question_answer_option_fields'  );

// end

add_action('wp_enqueue_scripts', 'aspl_qa_question_answer_user_script');
function aspl_qa_question_answer_user_script($hook) {

	wp_enqueue_script('jquery');
    wp_enqueue_style('aspl_qa_fa_icon_war', plugins_url('/css/font-awesome.min.css', __FILE__)) ;
    wp_enqueue_style('aspl_qa_custom_css', plugins_url('/css/aspl_qa_custom_css.css', __FILE__)) ;
    wp_enqueue_script( 'aspl_wdd_custom_js', plugin_dir_url(__FILE__) . 'js/custom.js', array('jquery'), '', true);
   /* $ajax_data = array(
        'url'   => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'aspl-custom-script-nonce' ),
    );
    wp_localize_script( 'aspl_wdd_custom_js', 'aspl_custom_ajax_data', $ajax_data  );*/

    wp_enqueue_script( 'aspl_qa_font_js', plugin_dir_url(__FILE__) . 'js/fontawesome.min.js', array('jquery'), '', true); 

}

function aspl_qa_question_answer_enqueue_custom_admin_style() {

	wp_enqueue_script('jquery');
    wp_enqueue_style('aspl_qa_fa_icon_war', plugins_url('/css/font-awesome.min.css', __FILE__)) ;
    wp_enqueue_style('aspl_qa_custom_css', plugins_url('/css/aspl_qa_custom_css.css', __FILE__)) ;
    wp_enqueue_script( 'custom_js_admin', plugin_dir_url(__FILE__) . 'js/custom.js', array('jquery'), '', true); 
    wp_enqueue_script( 'aspl_qa_font_js', plugin_dir_url(__FILE__) . 'js/fontawesome.min.js', array('jquery'), '', true); 

}
add_action( 'admin_enqueue_scripts', 'aspl_qa_question_answer_enqueue_custom_admin_style' );

add_action('wp_ajax_aspl_qa_fuction_give_ans', 'aspl_qa_fuction_give_ans');
add_action('wp_ajax_nopriv_aspl_qa_fuction_give_ans', 'aspl_qa_fuction_give_ans');
function aspl_qa_fuction_give_ans(){

	if (is_user_logged_in()) {

		$question = sanitize_text_field($_POST['question']);
		$current_user = wp_get_current_user();
		$q_id = sanitize_text_field($_POST['id']);
		$answer = $question;
		$current_user = wp_get_current_user();
		$a_email =  $current_user->user_email;
		$c_id = $current_user->ID;
		$a_name = $current_user->user_firstname." ".$current_user->user_lastname;
		$a_date = date("Y/m/d");
		$approve = 0;
		$image_path = get_avatar_url($current_user->ID);
			
		global $wpdb;
		$table_name1 = $wpdb->prefix . "qa_answer_data";
		$q = $wpdb->insert($table_name1, array(
				    'q_ID_r' => $q_id,
				    'answer' => $answer,
				    'a_email' => $a_email,
				    'c_id' => $c_id,
				    'c_name' => $a_name,
				    'create_at' => $a_date,
				    'approve' => $approve,
				    'img_path' => $image_path
				));
		if ($q == true) {
			$jsonarray = 'done';
			$myJSON = json_encode($jsonarray);
			return $myJSON;
		    die();
		}
		else{
			$jsonarray = 'error db';
			$myJSON = json_encode($jsonarray);
			return $myJSON;
		    die();
		}

	}else{

		$error = 'error';
		$myJSON = json_encode($error);
		return $myJSON;
        die();

	}
}

add_action('wp_ajax_aspl_qafuction_ans_approve', 'aspl_qafuction_ans_approve');
add_action('wp_ajax_nopriv_aspl_qafuction_ans_approve', 'aspl_qafuction_ans_approve');
function aspl_qafuction_ans_approve(){
	$approve_req = sanitize_text_field($_POST['approve']);

	global $wpdb;
	$table_name = $wpdb->prefix . "qa_answer_data";
	$ap = '1';
 	$execut= $wpdb->get_results(" UPDATE $table_name SET approve = $ap WHERE a_ID = $approve_req ");

 	if ($execut == true) {
		$jsonarray = 'done';
		$myJSON = json_encode($jsonarray);
		return $myJSON;
		die();
	}else{
		$jsonarray = 'error db';
		$myJSON = json_encode($jsonarray);
		return $myJSON;
	    die();
	}

}

add_action('wp_ajax_aspl_qa_fuction_ans_dis_approve', 'aspl_qa_fuction_ans_dis_approve');
add_action('wp_ajax_nopriv_aspl_qa_fuction_ans_dis_approve', 'aspl_qa_fuction_ans_dis_approve');
function aspl_qa_fuction_ans_dis_approve(){
	$approve_req = sanitize_text_field($_POST['approve']);

	global $wpdb;
	$table_name = $wpdb->prefix . "qa_answer_data";
	$ap = '0';
 	$execut= $wpdb->get_results(" UPDATE $table_name SET approve = $ap WHERE a_ID = $approve_req ");

 	if ($execut == true) {
		$jsonarray = '1';
		$myJSON = json_encode($jsonarray);
		return $myJSON;
		die();
	}else{
		$jsonarray = 'error';
		$myJSON = json_encode($jsonarray);
		return $myJSON;
		die();
	}

}


add_action('wp_ajax_aspl_qa_fuction_read_more', 'aspl_qa_fuction_read_more');
add_action('wp_ajax_nopriv_aspl_qa_fuction_read_more', 'aspl_qa_fuction_read_more');
function aspl_qa_fuction_read_more(){
	/*check_ajax_referer( 'aspl-custom-script-nonce', 'nonce_ajax' );*/
	$read = sanitize_text_field($_POST['read']);

	global $post;
	global $wpdb;
		
	$id = sanitize_text_field($_POST['p_id']);
	
	$ap_status = 1;
				
	$table_name = $wpdb->prefix . "qa_question";
	$question12 = $wpdb->get_results("SELECT * FROM $table_name where p_id = $id and approve = '$ap_status' and q_ID < $read  order by q_ID desc");

	foreach ($question12 as $value12) {

		$q_id = $value12->q_ID;
		$table_name1 = $wpdb->prefix . "qa_answer_data";
		$answer_count = $wpdb->get_results("SELECT * FROM $table_name1 where q_ID_r = $q_id and approve = '$ap_status' order by a_ID DESC ");

		?>
			<div class="aspl_qa_tab_line_main blog_more">
				<div class="aspl_qa_tab_count_box">
					<p><?php echo esc_html(count($answer_count));?><br>Answers</p>
				</div>
				<div class="aspl_qa_tab_content_box">
					<b>&nbsp <?php 
								echo esc_html($value12->question);
							 ?></b>
					 <br>
					 <p style="font-size: 14px;"> &nbsp&nbsp <i class="fa fa-clock" aria-hidden="true"></i>&nbsp
					 	<?php 
							$s = $value12->create_at;
							$date = strtotime($s);
							echo esc_html(date('F d \, Y', $date));
						?> &nbsp &nbsp <i class="fa fa-user" aria-hidden="true"></i> <?php echo esc_html($value12->c_name);  ?>
					</p>

					<span class="view_ans_parent">
					 	
							<div class='parent_cl'>
								<a class='ans_btn_user'>Give Answer</a>
						
								<div class="ans_section_pop">
									<textarea class="pop_textarea"></textarea><br>
									<input type="hidden" name="" class="pop_hidden" value="<?php echo esc_html($value12->q_ID); ?>">
									<button class="ans_btn_qa">Send</button>
								</div>
						
							</div>
						
						<span class="aspl_qa_view_btn"><p class="qa_view_ans link_class">View All Answers</p></span>
						<span class="aspl_qa_collapse_btn"><p class="qa_close_ans link_class" style="display: none;">Collapse All Answers</p></span>
						 	<div class="answer_data1" style="display:none;">
								<?php 
									if ($answer_count == null) {
								?>
										<div class="aspl_qa_notice">
												<span style='color:red;'><i class='fa fa-info-circle' aria-hidden='true'></i> &nbsp No Answer available right now.</span>
										</div>
								<?php
									}else{
										foreach ($answer_count as $ans) {
								?>
											<div class="aspl_qa_tab_ans_line" >
												<div class="aspl_qa_tab_ans_detail">
													<b><?php 
															echo esc_html($ans->answer);
														?></b>
													<br>
													<p style="font-size: 14px;"><i class="fa fa-clock" aria-hidden="true"></i>&nbsp  
														<?php 
															$s = $value12->create_at;
															$date = strtotime($s);
															echo esc_html(date('F d \, Y', $date));
														?>&nbsp &nbsp <i class="fa fa-user" aria-hidden="true"></i> <?php echo esc_html($ans->c_name);  ?>
													</p>
												</div>
											</div>
								<?php
										}
									}
								?>
							 </div>
					</span>
				</div>
			</div>
			<?php
	}

	die();

}
