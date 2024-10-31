<?php 
  
  if ( ! defined( 'ABSPATH' ) ) 
  {
    exit;
  }

  if(!class_exists('WP_List_Table')){
      require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
  }

  class aspl_qa_List_Table extends WP_List_Table {

        function __construct(){
          global $status, $page;
                
          //Set parent defaults
          parent::__construct( array(
              'singular'  => 'id',     //singular name of the listed records
              'plural'    => 'ids',    //plural name of the listed records
              'ajax'      => false        //does this table support ajax?
          ) );
        
        }


        function table_data1(){
            global $wpdb;
            $table_name = $wpdb->prefix . "qa_question";
            $question12 = $wpdb->get_results("SELECT * FROM $table_name");
            $admin_data = array();
            $admin_data1 = array();
            foreach ($question12 as $temp) {
                    $admin_data['q_ID']         = $temp->q_ID;
                    $admin_data['question']     = $temp->question;
                    $admin_data['email']        = $temp->email;
                    $admin_data['product_name'] = $temp->p_name;
                    $admin_data['user_name']    = $temp->c_name;
                    $admin_data['create_at']    = $temp->create_at;
                    $admin_data['approve1']     = $temp->approve;
                    $admin_data1[] = $admin_data;
                    
            }
            return $admin_data1;
    }

        function column_default($item, $column_name){
          switch($column_name){
              case 'question':
              case 'email':
              case 'product_name':
              case 'user_name':
              case 'create_at':
              case 'approve1':
              // case 'teac':
                  return $item[$column_name];
              default:
                  return print_r($item,true); //Show the whole array for troubleshooting purposes
          }
        }

        function column_question($item){
            $page_na = 'aspl-qa-my-custom-submenu-page';
            $nonce = wp_create_nonce( 'wdd_edit' );

            //Build row actions
            $actions = array(
                'id' => sprintf('<span style="color:silver">(id:%1$s)</span>',$item['q_ID']),
                'edit'      => sprintf('<a href="?page=%s&action=%s&_wpnonce=%s&id=%s">Edit</a>',$page_na,'edit',$nonce,$item['q_ID']),
            );
            
            //Return the title contents
            return sprintf('%1$s %2$s',$item['question'],$this->row_actions($actions));

        }


        function column_cb($item){
            return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
                /*$2%s*/ $item['q_ID']                //The value of the checkbox should be the record's id
            );
        }

        function get_columns(){
            $columns = array(
                'cb'           => '<input type="checkbox" />', //Render a checkbox instead of text
                'question'     => 'Question',
                'email'        => 'Email',
                'product_name' => 'Product Name',
                'user_name'    => 'User Name',
                'create_at'    => 'Create At',
                'approve1'     => 'Approve',
                // 'teac'     => 'Action',
            );
            return $columns;
        }

        function get_sortable_columns() {
            $sortable_columns = array(
                'question'     => array('question',false),     //true means it's already sorted
                'create_at'    => array('create_at',false),
                'email'        => array('email',false),
                'product_name' => array('product_name',false),
                'user_name'        => array('user_name',false),
                'approve1'        => array('approve1',false)


            );
            return $sortable_columns;
        }

        function get_bulk_actions() {
            $actions = array(
                'delete'    => 'Delete',
                'approve'    => 'Approve',
                'disapprove'    => 'Disapprove',

            );
            return $actions;
        }

        function process_bulk_action() {
            
            //Detect when a bulk action is being triggered...
            if( 'delete'===$this->current_action() ) {
              
                global $wpdb;
                $aspl_qa_tr = array_map('sanitize_text_field',$_GET['id']);
                $table_name = $wpdb->prefix . "qa_question";
                foreach ($aspl_qa_tr as $key23) {
                    $execut= $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE q_ID = %s", $key23 ) );
                }


            }
            if ('approve'===$this->current_action()) {

                $aspl_qa_tr = array_map('sanitize_text_field',$_GET['id']);
                // var_dump($aspl_qa_tr);

                global $wpdb;
                $table_name = $wpdb->prefix . "qa_question";
                foreach ($aspl_qa_tr as $key23) {
                    $execut= $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET approve = %d WHERE q_ID = %s", "1", $key23 ) );
                }
            }
            if ('disapprove'===$this->current_action()) {
              $aspl_qa_tr = array_map('sanitize_text_field',$_GET['id']);
              // $aspl_qa_tr = $_GET['id'];
              // var_dump($aspl_qa_tr);
                global $wpdb;
                $table_name = $wpdb->prefix . "qa_question";
                foreach ($aspl_qa_tr as $key23) {

                    $execut = $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET approve = %d WHERE q_ID = %s", "0", $key23 ) );

                }
              
            }
            
        }

        function prepare_items() {
          global $wpdb; //This is used only if making any database queries

          $per_page = 10;
          

          $columns = $this->get_columns();
          $hidden = array();
          $sortable = $this->get_sortable_columns();
          

          $this->_column_headers = array($columns, $hidden, $sortable);
          
          
          $this->process_bulk_action();
          
          $data = $this->table_data1();
                  

          function usort_reorder($a,$b){
              $request_orderby = sanitize_text_field($_REQUEST['orderby']);
              $request_order = sanitize_text_field($_REQUEST['order']);
              $orderby = (!empty($request_orderby)) ? $request_orderby : 'question'; //If no sort, default to title
              $order = (!empty($request_order)) ? $request_order : 'asc'; //If no order, default to asc
              $result = strnatcmp($a[$orderby], $b[$orderby]); //Determine sort order
              return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
          }
          usort($data, 'usort_reorder');
        
          
          $current_page = $this->get_pagenum();
          
          $total_items = count($data);
          
          

          $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
          
          $this->items = $data;

          $this->set_pagination_args( array(
              'total_items' => $total_items,                  //WE have to calculate the total number of items
              'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
              'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
          ) );
      }


  }


  function aspl_qa_render_list_page(){
      
      //Create an instance of our package class...
      $testListTable = new aspl_qa_List_Table();
      //Fetch, prepare, sort, and filter our data...
      $testListTable->prepare_items();
      
      ?>
      <div class="wrap">
          
          <div id="icon-users" class="icon32"><br/></div>
          <h2>Question List</h2>          
          <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
          <form id="movies-filter" method="get">
              <!-- For plugins, we also need to ensure that the form posts back to our current page -->
              <input type="hidden" name="page" value="<?php echo esc_html($_REQUEST['page']); ?>" />
              <!-- Now we can render the completed list table -->
              <?php $testListTable->display(); ?>

          </form>
          
      </div>
      <?php
  }

aspl_qa_render_list_page();




?>