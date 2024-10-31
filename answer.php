<?php 
	if ( ! defined( 'ABSPATH' ) ) 
	{
		exit;
	}
	// wp_verify_nonce( $_REQUEST['_wpnonce'], 'wdd_edit' );
	$nonce = sanitize_text_field($_REQUEST['_wpnonce']);
	if ( ! wp_verify_nonce( $nonce, 'wdd_edit' ) ) 
	{		
		die();
	}
	
	$que_id =  sanitize_text_field($_GET['id']);
	global $wpdb;
	$ap_status = 1;
	
	$table_name = $wpdb->prefix . "qa_question";
	$question12 = $wpdb->get_results("SELECT * FROM $table_name where q_ID = $que_id ");	

 ?>
<div class="aspl_qacls_que_line">
	<div>
		<h1><span>Question : </span><?php  echo esc_html($question12[0]->question); ?></h1>
	</div>
	<div>
	 	<div class="aspl_qacls_que_img">
	 		<img src="<?php echo esc_html($question12[0]->img_path); ?>">
	 	</div>
	 	<div class="aspl_qacls_que_detail">
	 		<span>User Name : <?php echo esc_html($question12[0]->c_name); ?></span><br>
	 		<span>Email : <?php echo esc_html($question12[0]->email); ?></span><br>
	 		<span>Post Status :<span style="color:red;"><?php 
	 								$status = $question12[0]->approve;
	 								if ($status == 1){
	 									echo esc_html("Approve");
	 								}
	 								else{
	 									echo esc_html("Disapprove");
	 								}
									?>
								</span>
			</span><br>
			<span>On Product : <?php echo esc_html($question12[0]->p_name); ?></span><br>
			<span>Posted On : <?php echo esc_html($question12[0]->create_at); ?></span>
	 	</div>
	 </div>
</div>
<?php 
	$q_id = $question12[0]->q_ID;
	$table_name1 = $wpdb->prefix . "qa_answer_data";
	$question12 = $wpdb->get_results("SELECT * FROM $table_name1 where q_ID_r = $q_id order by a_ID DESC ");

	foreach ($question12 as $ans) {
?>
		<div class="aspl_qacls_ans_line">
			<div><h1><span>Answer : </span><?php  echo esc_html($ans->answer); ?></h1></div>
			<div>
		 		<div class="aspl_qacls_ans_img" >
		 			<img src="<?php echo esc_html($ans->img_path); ?>">
				</div>
				<div class="approve_parent aspl_qacls_ans_detail">
					<input type="hidden" name="test" class="hidden_a_id" value="<?php echo esc_html($ans->a_ID); ?>">
					<span>User Name : <?php echo esc_html($ans->c_name); ?></span><br>
					<span>Email : <?php echo esc_html($ans->a_email); ?></span><br>
					<span>Post Status :<span style="color:red;"><?php 
												$status = $ans->approve;
												if ($status == 1){
													$path = plugin_dir_url( __FILE__ ) . 'image/dis-approve-icon.png';?>
													Approve <br>
													For Disapprove click hear &nbsp <span class='approve_icon'><img src='<?php echo esc_html($path); ?>' style='width:20px;'></span><?
												}
												else{
													$path = plugin_dir_url( __FILE__ ) . 'image/approve-icon.png';?>
													Disapprove <br>
													For Approve click hear &nbsp <span class='dis_approve_icon'><img src='<?php echo esc_html($path); ?>' style='width:20px;'></span><?
												}
												?></span>
					</span><br>
					<span>Posted On : <?php echo esc_html($ans->create_at); ?></span>
				</div>
			</div>
		</div>
<?php 
	}

?>

