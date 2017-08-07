<?php

/**
 * @author William Sergio Minozzi
 * @copyright 2016
 */

// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
 


$reportattacks_option_name[0] = 'reportattacks_record_active';
$reportattacks_option_name[1] = 'reportattacks_whitelist';
$reportattacks_option_name[2] = 'reportattacks_my_email';
$reportattacks_option_name[3] = 'reportattacks_all_logins';
$reportattacks_option_name[4] = 'reportattacks_radio_report_attacks';
$reportattacks_option_name[5] = 'reportattacks_my_blocklist_api';
$reportattacks_option_name[6] = 'reportattacks_my_blocklist_server';
$reportattacks_option_name[7] = 'reportattacks_email_display';
$reportattacks_option_name[8] = 'reportattacks_block_rep';


for ($i = 0; $i < 9; $i++)
{
 delete_option( $reportattacks_option_name[$i] );
 // For site options in Multisite
 delete_site_option( $reportattacks_option_name[$i] );    
}
 
 
// Drop a custom db table
global $wpdb;
$reportattacks_current_table = $wpdb->prefix . 'reportattacks_loginlog';
$wpdb->query( "DROP TABLE IF EXISTS $reportattacks_current_table" );

?>