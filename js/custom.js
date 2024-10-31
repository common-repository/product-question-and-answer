jQuery(document).ready(function($){
		  $(document).on("click", ".ans_btn_user" , function() {
		  // $(".ans_btn_user").click(function(){
		    $(this).parents(".parent_cl").find(".ans_section_pop").toggle();
		    var t = $(this).parents(".parent_cl").find(".ans_btn_user").text();
		    if (t == 'Give Answer') {
		    	$(this).parents(".parent_cl").find(".ans_btn_user").text('X');
		    }
		    else{
		    	$(this).parents(".parent_cl").find(".ans_btn_user").text('Give Answer');
		    }
		  });
	
		  	
		  	$(document).on("click", ".ans_btn_qa" , function() {
		  		var ajaxurl = document.getElementById('test').innerHTML;
		  		var pop_textarea = $(this).parents(".ans_section_pop").find(".pop_textarea").val();
		  		if (pop_textarea == '') {
		  			alert("Please Enter Answer. ");
		  			return;
		  		}
		  		var pop_hidden = $(this).parents(".ans_section_pop").find(".pop_hidden").val();

		  		$.ajax({    
		                type: "POST",
		                dataType: "json",
		                url: ajaxurl,
		                data: {
		                    action: 'aspl_qa_fuction_give_ans',
		                    question:pop_textarea,
		                    // email:pop_email,
		                    id:pop_hidden,
		                },
		                success: function (data) {
		                	if (data == "error") {
		                		alert("Please Login Your Account.");
		                	}else{
			                	alert("Your Ans Is Approve Soon.Thankyou.");
		                	}

		                }
            	});


		  		$(this).parents(".parent_cl").find(".ans_section_pop").toggle();
			    var t = $(this).parents(".parent_cl").find(".ans_btn_user").text();
			    if (t == 'Give Answer') {
			    	$(this).parents(".parent_cl").find(".ans_btn_user").text('X');
			    }
			    else{
			    	$(this).parents(".parent_cl").find(".ans_btn_user").text('Give Answer');
			    }


		  	});


		$(document).on("click", ".dis_approve_icon" , function() {

			var pop_textarea = $(this).parents(".approve_parent").find(".hidden_a_id").val();

			$.ajax({    
		                type: "POST",
		                dataType: "json",
		                url: ajaxurl,
		                data: {
		                    action: 'aspl_qafuction_ans_approve',
		                    approve:pop_textarea,
		                    // email:pop_email,
		                    // id:pop_hidden,
		                },
		                success: function (data) {
		                	 location.reload(true);
		                }
            	});
			

		});
		$(document).on("click", ".approve_icon" , function() {
			var pop_textarea = $(this).parents(".approve_parent").find(".hidden_a_id").val();

			$.ajax({    
		                type: "POST",
		                dataType: "json",
		                url: ajaxurl,
		                data: {
		                    action: 'aspl_qa_fuction_ans_dis_approve',
		                    approve:pop_textarea,
		                },
		                success: function (data) {
		                	 location.reload(true);
		                }
            	});


		});

		$(document).on('click','.show_more',function(){
		  		var ajaxurl = document.getElementById('test').innerHTML;
                    var ID = $(this).attr('id_i');
                    var p_ID = $(this).attr('p_id');

                    $('.show_more').hide();
                    $('.less_data').show();
                    $.ajax({
                        type: "POST",
		                url: ajaxurl,
		                data: {
		                    action: 'aspl_qa_fuction_read_more',
		                    read:ID,
		                    p_id:p_ID,
		                },
		                beforeSend: function() {
				            $("#loading-image").show();
				        },
		                success: function (data) {
		                	   $('.blog_list').append(data);
		                	   $("#loading-image").hide();
		                }
                    });
                });

		$(document).on('click','.less_data_btn',function(){
			 $('.show_more').show();
			 $('.blog_list').find('.blog_more').remove();
			 $('.less_data').hide();

		});
		
		$(document).on('click','.qa_view_ans',function(){
			$(this).parents(".view_ans_parent").find(".qa_close_ans").show();
			$(this).parents(".view_ans_parent").find(".answer_data1").show();
			$(this).parents(".view_ans_parent").find(".qa_view_ans").hide();

			// Collapse all answers qa_close_ans view_ans_parent
		});

		$(document).on('click','.qa_close_ans',function(){

			$(this).parents(".view_ans_parent").find(".qa_view_ans").show();
			$(this).parents(".view_ans_parent").find(".qa_close_ans").hide();
			$(this).parents(".view_ans_parent").find(".answer_data1").hide();

		});

	});

