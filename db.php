<?php 

if ( ! defined( 'ABSPATH' ) ) 
{
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . "qa_question";
// $my_products_db_version = '1.0.0';
$charset_collate = $wpdb->get_charset_collate();

if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) {

    $sql = "CREATE TABLE $table_name (
            `q_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
            `question` text NOT NULL,
            `email` text NOT NULL,
            `p_id` mediumint(9) NOT NULL,
            `p_name` text NOT NULL,
            `c_id` mediumint(9) NOT NULL,
            `c_name` text NOT NULL,
            `create_at` text NOT NULL,
            `approve` text NOT NULL,
            `img_path` text NOT NULL,
            PRIMARY KEY  (q_ID)
    )    $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    // add_option( my_db_version', $my_products_db_version );
}

$table_name1 = $wpdb->prefix . "qa_answer_data";
// $my_products_db_version = '1.0.0';
$charset_collate = $wpdb->get_charset_collate();

if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name1}'" ) != $table_name1 ) {

    $sql1 = "CREATE TABLE $table_name1 (
            `a_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
            `q_ID_r` mediumint(9) NOT NULL,
            `answer` text NOT NULL,
            `a_email` text NOT NULL,
            `c_id` mediumint(9) NOT NULL,
            `c_name` text NOT NULL,
            `create_at` text NOT NULL,
            `approve` text NOT NULL,
            `img_path` text NOT NULL,
            PRIMARY KEY  (a_ID)
    )    $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql1 );
    // add_option( my_db_version', $my_products_db_version );
}




 ?>