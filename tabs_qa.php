<?php 

	if ( ! defined( 'ABSPATH' ) ) 
	{
		exit;
	}

 ?>

<div class="test" id="test" style="display:none;"><?php echo esc_html(admin_url('admin-ajax.php'));?></div> 
<div class="main_tab_class">
	<form method="POST">
		<table class="qa_table">
			<?php 
				if (is_user_logged_in()) {
			?>
			<tr>
				<td>
					<label><i class="fa fa-question-circle" aria-hidden="true"></i> Question : </label>		
				</td>
			</tr>
				<tr>
					<td>
						<textarea class="qa_textarea" name="qa_textarea"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="qa_btn" value="Post Question">					
					</td>
				</tr>
			<?php  
				}else{
					?>
					<tr><td>
					<span style='color:red;'><i class='fa fa-info-circle' aria-hidden='true'></i> &nbspYou have to first Login to Asked Question and Give Answer.</span>
					</td></tr>
					<?
				}
			?>
		</table>
	</form>
</div>

<div class="main_tab_class">
	<div>
		<label>Asked Question :</label>
	</div>
	<div>
		<div class="less_data" style="display:none;"><span class="less_data_btn" >Collapse All Question</span></div>
		<?php 
			global $post;
			global $wpdb;
			$id = $post->ID;
			$ap_status = 1;

			$table_name = $wpdb->prefix . "qa_question";
			$question12 = $wpdb->get_results("SELECT * FROM $table_name where p_id = '$id' and approve = '$ap_status' order by q_ID desc limit 2 ");	
			$question_count_for_read_more = $wpdb->get_results("SELECT * FROM $table_name where p_id = '$id' and approve = '$ap_status' order by q_ID desc");	

			if ( $question12 != null) {

				?>
			<div class="blog_list">
				<?
				foreach ($question12 as $value12) {
					$q_id = $value12->q_ID;
					$table_name1 = $wpdb->prefix . "qa_answer_data";
					$answer_count = $wpdb->get_results("SELECT * FROM $table_name1 where q_ID_r = $q_id and approve = '$ap_status' order by a_ID DESC ");

					?>
						<div class="aspl_qa_tab_line_main">
							<div class="aspl_qa_tab_count_box">
								<p><?php echo esc_html(count($answer_count));?><br>Answers</p>
							</div>
							<div class="aspl_qa_tab_content_box">
								<b>&nbsp<?php echo esc_html($value12->question); ?></b>
								<br>
								<p style="font-size: 14px;"> &nbsp&nbsp <i class="fa fa-clock" aria-hidden="true"></i>&nbsp<?php 
								 		$s = $value12->create_at;
								 		$date = strtotime($s);
								 		echo esc_html(date('F d \, Y', $date));
								 	?> &nbsp &nbsp <i class="fa fa-user" aria-hidden="true"></i> <?php echo esc_html($value12->c_name);?>
								</p>
								<span class="view_ans_parent">	 
									<div class='parent_cl' >
										<a class='ans_btn_user'>Give Answer</a>
										<div class="ans_section_pop">
											<textarea class="pop_textarea"></textarea><br>
											<input type="hidden" name="" class="pop_hidden" value="<?php echo esc_html($value12->q_ID); ?>">
											<button class="ans_btn_qa" >Send</button>
										</div>
									</div>	
									<span class="aspl_qa_view_btn"><p class="qa_view_ans link_class">View All Answers</p></span>
									<span class="aspl_qa_collapse_btn"><p class="qa_close_ans link_class" style="display: none;">Collapse All Answers</p></span>
									<div class="answer_data1" style="display:none;">
									 	<?php 
											if ($answer_count == null) {
												?>
												<div class="aspl_qa_notice" >
													 
													<span style='color:red;'><i class='fa fa-info-circle' aria-hidden='true'></i> &nbsp No Answer available right now.</span>
														 
												</div>
												<?php
											}else{
												foreach ($answer_count as $ans) {
												?>
													<div class="aspl_qa_tab_ans_line">
														<div class="aspl_qa_tab_ans_detail">
															<b><?php echo esc_html($ans->answer); ?></b>
															<br>
															<p style="font-size: 14px;"><i class="fa fa-clock" aria-hidden="true"></i>&nbsp <?php 
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
					$q_id = $question12[0]->q_ID;
					$blog_id = $value12->q_ID;
				}
				$ans_con = count($question_count_for_read_more);

				if ( $ans_con > 2 ) {
					$more_container_id = 'show_more_container'.$blog_id;
					
				?>
					<div class="show_more_container" id="<?php echo esc_html($more_container_id); ?>"> <span id_i = "<?php echo esc_html($blog_id) ; ?>"  class="show_more" title="Load more Question" p_id = "<?php echo esc_html($post->ID); ?>" >Read More Question...</span> </div>
					<?
				}
				?>
				<div id='loading-image' style='display:none;'><div class='loader'></div></div>
			</div>
				<?

			}
			else{
				?>
			   <h6 style='color:red;'><i class='fa fa-info-circle' aria-hidden='true'></i> &nbsp No Question available right now.</h6>
			   <?
			}

		 ?>

	</div>
									
	<?php 
		
	if (isset($_POST['qa_btn'])) {
		$question =  sanitize_text_field($_POST['qa_textarea']);
		$current_user = wp_get_current_user();
		$email = $current_user->user_email;

		global $post;
		$id = $post->ID;
		$p_name = get_the_title($id);
		$c_id = $current_user->ID;	
		$c_name	= $current_user->display_name;
		$c_date = date("Y/m/d");
		$approve = 0;
		$image_path = get_avatar_url($current_user->ID);

		global $wpdb;
		$table_name = $wpdb->prefix . "qa_question";
		$q = $wpdb->insert($table_name, array(
			    'question' => $question,
			    'email' => $email,
			    'p_id' => $id, // ... and so on
			    'p_name' => $p_name,
			    'c_id' => $c_id,
			    'c_name' => $c_name,
			    'create_at' => $c_date,
			    'approve' => $approve,
			    'img_path' => $image_path
			));
		if ($q == true) {
			?>
			<script type='text/javascript'>alert('Your Question Is Approve Soon. Thankyou.');</script>
			<?
		}
		else{
			?>
			<script type='text/javascript'>alert('Some Things Want to Worng. Thankyou.');</script>
			<?	
		}
	}

	 ?>
</div>