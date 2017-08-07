<?php /**
 * @author Bill Minozzi
 * @copyright 2016
 */
 
// reportattacks_chk_status();
// Blocks reporteds ip ...

$reportattacks_block_rep = trim(get_site_option('reportattacks_block_rep', 'No'));
$reportattacks_ip = trim(reportattacks_findip());
if ($reportattacks_block_rep == '1' and strlen($reportattacks_ip) > 8) {
    global $wpdb;
    $table_name = $wpdb->prefix . "reportattacks_loginlog";
    $query = "select * from " . $table_name . " WHERE reported =  'yes'  
             and IP = '$reportattacks_ip' ";
    $result = $wpdb->get_results($query);
    $countrep = $wpdb->num_rows;
    if ($countrep > 5)
        die();
}
if (is_admin()) {
    // Report new plugin installed...
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    function reportattacks_save_name_plugins()
    {
        $all_plugins = get_plugins();
        $all_plugins_keys = array_keys($all_plugins);
        if (count($all_plugins) < 1)
            return;
        $my_plugins = '';
        $loopCtr = 0;
        foreach ($all_plugins as $plugin_item) {
            if ($my_plugins != '')
                $my_plugins .= PHP_EOL;
            $plugin_title = $plugin_item['Name'];
            $my_plugins .= $plugin_title;
            $loopCtr++;
        }
        if (!update_site_option('reportattacks_name_plugins', $my_plugins))
            add_site_option('reportattacks_name_plugins', $my_plugins);
    }
    function reportattacks_q_plugins_now()
    {
        $all_plugins = get_plugins();
        $all_plugins_keys = array_keys($all_plugins);
        return count($all_plugins);
    }
    function reportattacks_q_plugins()
    {
        $nplugins = get_site_option('reportattacks_name_plugins', '');
        $nplugins = explode(PHP_EOL, $nplugins);
        return count($nplugins);
    }
    function reportattacks_alert_plugin()
    {
        global $reportattacks_admin_email, $reportattacks_new_plugin;
        $dt = date("Y-m-d H:i:s");
        $dom = $_SERVER['SERVER_NAME'];
        $url = $_SERVER['PHP_SELF'];
        $msg = __('Alert: New Plugin was installed.', 'reportattacks');
        $msg .= '<br>';
        $msg .= __('New Plugin Name', 'reportattacks'). ': ' . $reportattacks_new_plugin;
        $msg .= '<br>';
        $msg .= __('Date', 'reportattacks') . ': ' . $dt . '<br>';
        $msg .= __('Domain','reportattacks') . ': ' . $dom . '<br>';
        $msg .= '<br>';
        $msg .= __('This email was sent from your website', 'reportattacks'). ': '. $dom .' ';
        $msg .= __('by Report Attacks plugin.', 'reportattacks'). '<br>';
        $email_from = 'wordpress@' . $dom;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "From: " . $email_from . "\r\n" . 'Reply-To: ' . $reportattacks_admin_email .
            "\r\n" . 'X-Mailer: PHP/' . phpversion();
        $to = $reportattacks_admin_email;
        $subject = __('Alert: New Plugin was installed at', 'reportattacks'). ': ' . $dom;
        // double check before send...
        $nplugins = get_site_option('reportattacks_name_plugins', '');
        $nplugins = explode(PHP_EOL, $nplugins);
        if (!in_array($reportattacks_new_plugin, $nplugins)) {
            if (!function_exists('wp_mail'))
                require_once (ABSPATH . 'wp-includes/pluggable.php');
            wp_mail($to, $subject, $msg, $headers, '');
        }
        return 1;
    }
    $qpluginsnow = reportattacks_q_plugins_now();
    $qplugins = reportattacks_q_plugins();
    if (($qplugins == 0 and $qpluginsnow > 0) or ($qplugins > $qpluginsnow)) {
        reportattacks_save_name_plugins();
        $qplugins = reportattacks_q_plugins();
    }
    if ($qpluginsnow > $qplugins) {
        $nplugins = get_site_option('reportattacks_name_plugins', '');
        $nplugins = explode(PHP_EOL, $nplugins);
        $all_plugins = get_plugins();
        $all_plugins_keys = array_keys($all_plugins);
        if (count($all_plugins) < 1)
            return;
        $my_plugins_now = '';
        $loopCtr = 0;
        foreach ($all_plugins as $plugin_item) {
            $plugin_title = $plugin_item['Name'];
            $my_plugins_now[$loopCtr] = $plugin_title;
            $loopCtr++;
        }
        $reportattacks_new_plugin = '';
        for ($i = 0; $i < $qpluginsnow; $i++) {
            $plugin_name = $my_plugins_now[$i];
            if (!in_array($plugin_name, $nplugins)) {
                $reportattacks_new_plugin = $plugin_name;
                break;
            }
        }
        reportattacks_alert_plugin();
        reportattacks_save_name_plugins();
    } //  if ($qpluginsnow > $qplugins)
    if ($qpluginsnow < $qplugins) {
        reportattacks_save_name_plugins();
    }
} // End  Report new plugin installed...
function reportattacks_chk_status()
{
    $reportattacks_my_blocklist_server = trim(get_site_option('reportattacks_my_blocklist_server',
        ''));
    $reportattacks_my_blocklist_api = trim(get_site_option('reportattacks_my_blocklist_api',
        ''));
    $reportattacks_radio_report_attacks = trim(get_site_option('reportattacks_radio_report_attacks',
        ''));
    $url = 'https://www.blocklist.de/reportattacks/api.php?email=' . $reportattacks_my_blocklist_server .
        '&action=verifyaccount&format=php&reportattacks=true';
    $url = esc_url_raw($url);
    $response = wp_remote_get(esc_url_raw($url));
    if (is_wp_error($response)) {
        //$error_message = $response->get_error_message();
        //echo "Something went wrong: $error_message";
    } else {
        // die('2');
    }
    $body = $response['body'];
    $api_response = unserialize($body);
    echo $api_response['status']; // ok - error
    echo '<hr>';
    echo $api_response['account']; // veriefed - blocklist.de-Account for Address a@b.com is not verified yet.
    die();
}
function reportattacks_success_login($user_login)
{
    global $reportattacks_whitelist;
    global $reportattacks_all_logins;
    global $reportattacks_ip;
    global $reportattacks_admin_email;
    if (reportattacks_whitelisted($reportattacks_ip, $reportattacks_whitelist) and $reportattacks_all_logins <>
        'Yes') {
        return 1;
    }
    $dt = date("Y-m-d H:i:s");
    $dom = $_SERVER['SERVER_NAME'];
    $url = $_SERVER['PHP_SELF'];
    $msg = __('This email was sent from your website', 'reportattacks');
    $msg .= ': ' . $dom . '  ';
    $msg .= __('by Report Attacks plugin.', 'reportattacks');
    $msg .= '<br>';
    $msg .= __('Date', 'reportattacks').': ' . $dt . '<br>';
    $msg .= __('Ip', 'reportattacks'). ': ' . $reportattacks_ip . '<br>';
    $msg .= __('Domain', 'reportattacks') .': ' . $dom . '<br>';
    $msg .= __('User', 'reportattacks') .': ' . $user_login;
    $msg .= '<br>';
    $msg .= 'URL: ' . $url;
    $msg .= '<br>';
    $msg .= __('Add this IP to your withelist to stop this email and change your Notification Settings.', 'reportattacks');
    $email_from = 'wordpress@' . $dom;
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: " . $email_from . "\r\n" . 'Reply-To: ' . $user_login . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $to = $reportattacks_admin_email;
    $subject = __('Login Successful at', 'reportattacks') . ': ' . $dom;
    wp_mail($to, $subject, $msg, $headers, '');
    return 1;
}
function reportattacks_add_menu_items()
{
    $reportattacks_table_page = add_submenu_page('report-attacks', // $parent_slug
        'Failed Logins Table', // string $page_title
        'Failed Logins Table', // string $menu_title
        'manage_options', // string $capability
        'ra_my-custom-submenu-page', 'reportattacks_render_list_page');
    add_action("load-$reportattacks_table_page", 'reportattacks_screen_options');
}
function reportattacks_screen_options()
{
    global $reportattacks_table_page, $option;
    $screen = get_current_screen();
    if (trim($screen->id) != 'report-attacks_page_ra_my-custom-submenu-page')
        return;
    $def = get_site_option('reportattacks_per_page', 20);
    $args = array(
        'label' => __('Quantity per page', 'reportattacks'),
        'default' => $def,
        'option' => 'reportattacks_per_page');
    add_screen_option('per_page', $args);
}
function reportattacks_set_screen_options($status, $option, $value)
{
    if ('reportattacks_per_page' == $option) {
        if (!add_site_option($option, $value))
            update_site_option($option, $value);
        return $value;
    }
}
function reportattacks_do_report()
{
    global $wpdb, $reportattacks_server, $reportattacks_whitelist;
    $table_name = $wpdb->prefix . "reportattacks_loginlog";
    $charset_collate = $wpdb->get_charset_collate();
    $service = 'wp-bruteforce';
    $minimo = 6;
    $reportattacks_my_blocklist_server = trim(get_site_option('reportattacks_my_blocklist_server',
        ''));
    $reportattacks_my_blocklist_api = trim(get_site_option('reportattacks_my_blocklist_api',
        ''));
    $reportattacks_radio_report_attacks = trim(get_site_option('reportattacks_radio_report_attacks',
        ''));
    if ($reportattacks_radio_report_attacks <> 'yes')
        return;
    $query = "select * from " . $table_name . " WHERE reported <>  'yes' 
             GROUP BY IP
             HAVING COUNT( * ) > 5 ";
    $result = $wpdb->get_results($query);
    $count = $wpdb->num_rows;
    if ($count < 1)
        return;
    foreach ($result as $row) {
        $reportattacks_ip = $row->ip;
        break;
    }
    // Double check if is whitelisted...
    if (reportattacks_whitelisted($reportattacks_ip, $reportattacks_whitelist)) {
        return;
    }
    $query = "select * from " . $table_name . " WHERE reported <>  'yes' 
             and ip = '" . $reportattacks_ip . "'";
    $result = $wpdb->get_results($query);
    $count = count($result);
    if ($count < 6)
        return;
    $logs = 'Here more information about ' . $reportattacks_ip;
    $logs .= chr(13) . chr(10);
    $logs .= 'Our Server: ' . $reportattacks_server;
    $logs .= chr(13) . chr(10);
    $logs .= 'Service: ' . $service;
    $logs .= chr(13) . chr(10);
    foreach ($result as $row) {
        $logs .= "----------------------------------";
        $logs .= chr(13) . chr(10);
        $logs .= "Time: " . $row->time . "  ";
        $logs .= chr(13) . chr(10);
        $logs .= "User: " . $row->user . "  ";
        $logs .= chr(13) . chr(10);
        $logs .= "User Agent: " . $row->ua . "  ";
        $logs .= chr(13) . chr(10);
        $logs .= "URL: " . $row->url . "  ";
        $logs .= chr(13) . chr(10);
        $logs .= "----------------------------------";
    }
    $logs = urlencode($logs);
    $url = "https://www.blocklist.de/de/httpreports.html";
    $domain_name = get_site_url();
    $urlParts = parse_url($domain_name);
    $domain_name = preg_replace('/^www\./', '', $urlParts['host']);
    $response = wp_remote_post($url, array(
        'method' => 'POST',
        'timeout' => 15,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => array(
            'a' => 'b',
            'server' => $reportattacks_my_blocklist_server,
            'apikey' => $reportattacks_my_blocklist_api,
            'ip' => $reportattacks_ip,
            'service' => 'wp-bruteforce',
            'format' => 'php',
            'logs' => $logs),
        'cookies' => array()));
    if (is_wp_error($response)) {
    } else {
        $query = "UPDATE " . $table_name . " set reported = 'yes' where ip = '" . $reportattacks_ip .
            "'";
        $r = $wpdb->get_results($query);
    }
}
function reportattacks_my_deactivation()
{
    wp_clear_scheduled_hook('reportattacks_my_hourly_event');
    global $reportattacks_admin_email;
    global $reportattacks_ip;
    $current_user = wp_get_current_user();
    $user_login = $current_user->user_login;
    $dt = date("Y-m-d H:i:s");
    $dom = $_SERVER['SERVER_NAME'];
    $url = $_SERVER['PHP_SELF'];
    $msg = __('Alert: the Report Attacks plugin was been deactivated from plugins page.', 'reportattacks');
    $msg .= '<br>';
    $msg .= __('Date', 'reportattacks') .': ' . $dt . '<br>';
    $msg .= __('Ip', 'reportattacks').': ' . $reportattacks_ip . '<br>';
    $msg .= __('Domain', 'reportattacks').': ' . $dom . '<br>';
    $msg .= __('User', 'reportattacks').': ' . $user_login;
    $msg .= '<br>';
    $msg .= __('This email was sent from your website', 'reportattacks') .': '. $dom . ' ';
    $msg .= __('by Report Attacks plugin.', 'reportattacks'). '<br>';
    $email_from = 'wordpress@' . $dom;
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: " . $email_from . "\r\n" . 'Reply-To: ' . $user_login . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $to = $reportattacks_admin_email;
    $subject = 'Plugin Deactivated at: ' . $dom;
    wp_mail($to, $subject, $msg, $headers, '');
    return 1;
}

if (is_admin()) {
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        if ($page == 'report-attacks' or $page == 'reportattacks_getapi' or $page ==
            'my-custom-submenu-page' or $page == 'ra_my-custom-submenu-page') {
            add_filter('contextual_help', 'reportattacks_contextual_help', 10, 3);
            function reportattacks_contextual_help($contextual_help, $screen_id, $screen)
            {
                $myhelp = '<br>';
                $myhelp .= __('Improve system security and report login brute force attack.', 'reportattacks');
                $myhelp .= '<br />';
                $myhelp .= __('Read the StartUp guide at Report Attacks Settings page.', 'reportattacks');
                
                $myhelp .= '<br />';
                $myhelp .= __('Visit the', 'reportattacks').' ';
                $myhelp .= '<a href="http://reportattacks.com" target="_blank">';
                $myhelp .= __('plugin site', 'reportattacks');
                $myhelp .= '</a> ';
                $myhelp .= __('for more details and online guide.', 'reportattacks');
                
                $screen->add_help_tab(array(
                    'id' => 'ra-overview-tab',
                    'title' => __('Overview', 'reportattacks'),
                    'content' => '<p>' . $myhelp . '</p>',
                    ));
                return $contextual_help;
            }
        }
    }
}
function reportattacks_whitelisted($reportattacks_ip, $areportattacks_whitelist)
{
    for ($i = 0; $i < count($areportattacks_whitelist); $i++) {
        if (trim($areportattacks_whitelist[$i]) == trim($reportattacks_ip))
            return 1;
    }
    return 0;
}
function reportattacks_failed_login($user_login)
{
    global $reportattacks_whitelist;
    global $reportattacks_all_failed_logins;
    global $reportattacks_ip;
    global $reportattacks_admin_email;
    if (reportattacks_whitelisted($reportattacks_ip, $reportattacks_whitelist)) {
        return;
    }
        if ($reportattacks_all_failed_logins == '1') {
        $dt = date("Y-m-d H:i:s");
        $dom = $_SERVER['SERVER_NAME'];
        $url = $_SERVER['PHP_SELF'];
        $msg = __('This email was sent from your website ', 'reportattacks').': ' . $dom . ' ';
        $msg .= __('by ReportAttacks plugin.', 'reportattacks'). '<br> ';
        $msg .= __('Date', 'reportattacks') .': ' . $dt . '<br>';
        $msg .= __('Ip', 'reportattacks'). ': ' . $reportattacks_ip . '<br>';
        $msg .= __('Domain', 'reportattacks').': ' . $dom . '<br>';
        $msg .= __('User', 'reportattacks').': ' . $user_login;
        $msg .= '<br>';
        $msg .= 'URL: ' . $url;
        $msg .= '<br>';
        $msg .= __('Failed login', 'reportattacks');
        
        $msg .= '<br>';
        $msg .= '<br>';
        $msg .= __('You can stop emails at the Notifications Settings Tab.', "reportattacks");
        $msg .= '<br>';
        $msg .= __('Dashboard => Report Attacks => Notifications Settings.', "reportattacks");
     
        
        
        $email_from = 'wordpress@' . $dom;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "From: " . $email_from . "\r\n" . 'Reply-To: ' . $user_login . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        $to = $reportattacks_admin_email;
        $subject = __('Failed Login at', 'reportattacks').': ' . $dom;
        wp_mail($to, $subject, $msg, $headers, '');
    }
    reportattacks_add_try($reportattacks_ip, $user_login);
    return;
}
function reportattacks_add_try($reportattacks_ip, $user)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "reportattacks_loginlog";
    $charset_collate = $wpdb->get_charset_collate();
    $reportattacks_record_active = get_site_option('reportattacks_record_active',
        '0');
    if ($reportattacks_record_active <> '1')
        return;
    $time = time();
    $time = date("Y-m-d H:m:s", $time);
    $ua = sanitize_text_field(trim($_SERVER['HTTP_USER_AGENT']));
    if (substr($ua, 0, 6) == 'Parser')
        $ua = "Unknow User Agent";
    $url = esc_url($_SERVER['REQUEST_URI']);
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referrer = esc_url($_SERVER['HTTP_REFERER']);
    } else
        $referrer = '';
    // %d (integer), %f (float), and %s (string).
    $r = $wpdb->query($wpdb->prepare("INSERT INTO " . $table_name .
        " (time, ip, user, ua, url, referrer) 
        VALUES ( %s, %s, %s, %s, %s, %s)", array($time, $reportattacks_ip, $user,
            $ua, $url, $referrer)));
    return $r;
}
function reportattacks_findip()
{
    $reportattacks_ip = '';

		$headers = array(
            'HTTP_CLIENT_IP',        // Bill
            'HTTP_X_REAL_IP',        // Bill
            'HTTP_X_FORWARDED',      // Bill
            'HTTP_FORWARDED_FOR',    // Bill 
            'HTTP_FORWARDED',        // Bill
            'HTTP_X_CLUSTER_CLIENT_IP', //Bill
			'HTTP_CF_CONNECTING_IP', // CloudFlare
			'HTTP_X_FORWARDED_FOR',  // Squid and most other forward and reverse proxies
			'REMOTE_ADDR',           // Default source of remote IP
		);
		for ( $x = 0; $x < 8; $x++ ) {
			foreach ( $headers as $header ) {
				if ( ! isset( $_SERVER[$header] ) ) {
					continue;
				}
				$ip = trim( $_SERVER[$header] );
				if ( empty( $ip ) ) {
					continue;
				}
				if ( false !== ( $comma_index = strpos( $_SERVER[$header], ',' ) ) ) {
					$ip = substr( $ip, 0, $comma_index );
				}
    			// First run through. Only accept an IP not in the reserved or private range.
				if($ip == '127.0.0.1')
                       {
                        $ip='';
                         continue;
                       }
				if ( 0 === $x ) {
					$ip = filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE );
				} else {
					$ip = filter_var( $ip, FILTER_VALIDATE_IP );
				}
				if ( ! empty( $ip ) ) {
					break;
				}
			}
			if ( ! empty( $ip ) ) {
				break;
			}
		}
    if (!empty($ip))
        return $ip;
    else
        return 'unknow';
}    
function reportattacks_create_db()
{
    global $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    // creates my_table in database if not exists
    $table = $wpdb->prefix . "reportattacks_loginlog";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          `ip` varchar(30) NOT NULL,
          `user` varchar(100) NOT NULL,
          `ua` text NOT NULL,
          `url` text NOT NULL,
          `referrer` text NOT NULL,
          `reported` varchar(3) NOT NULL,
          `flag` char(1) NOT NULL,
    UNIQUE (`id`)
    ) $charset_collate;";
    dbDelta($sql);
}
function reportattacks_plugin_was_activated()
{
    global $wp_sbb_blacklist;
    add_option('rh_was_activated', '1');
    update_option('rh_was_activated', '1');
    reportattacks_create_db();
    reportattacks_addmyip();
    wp_schedule_event(current_time('timestamp'), 'hourly',
        'reportattacks_my_hourly_event');
    reportattacks_save_name_plugins();
    $reportattacks_installed = trim(get_option( 'reportattacks_installed',''));
    if(empty($reportattacks_installed)){
      add_option( 'reportattacks_installed', time() );
      update_option( 'reportattacks_installed', time() );
    }
}
function reportattacks_addmyip()
{
    $reportattacks_whitelist = trim(get_site_option('reportattacks_whitelist', ''));
    if (empty($reportattacks_whitelist))
        add_site_option('reportattacks_whitelist', reportattacks_findip());
}
function reportattacks_validate_ip($reportattacks_ip)
{
    if (filter_var($reportattacks_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 |
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}
function reportattacks_email_display()
{ 
        echo __('My WordPress user email', 'reportattacks').':' ?>
        <br />
        <input type="text" id="reportattacks_myemail" name="reportattacks_myemail" value="" placeholder="" size="100" />
        <br />
        <?php }
function reportattacks_validate_email_field($user, $password)
{
    global $reportattacks_myemail;
    if (!is_email($reportattacks_myemail))
        return new WP_Error('wrong_email', 'Please, fill out the email field!');
    else {
        // The Query
        $user_query = new WP_User_Query(array('orderby' => 'registered', 'order' =>
                'ASC'));
        // User Loop
        if (!empty($user_query->results)) {
            foreach ($user_query->results as $user) {
                if (strtolower(trim($user->user_email)) == $reportattacks_myemail)
                    return $user;
            }
        } else {
            // echo 'No users found.';
        }
        return new WP_Error('wrong_email', 'email not found!');
    }
    return $user;
}
if (get_site_option('reportattacks_radio_xml_rpc', 'No') == 'Yes')
    add_filter('xmlrpc_enabled', '__return_false');
if (get_site_option('reportattacks_radio_xml_rpc', 'No') == 'Pingback')
    add_filter('xmlrpc_methods', 'raremove_xmlrpc_pingback_ping');
function raremove_xmlrpc_pingback_ping($methods)
{
    unset($methods['pingback.ping']);
    return $methods;
} 
/////////////////////////////////////////
// Disable Json WordPress Rest API (also embed from WordPress 4.7). 
// Take a look our faq page (at our site) for details.'
function reportattacks_after_inic()
{
     $ra_current_WP_version = get_bloginfo('version');
     function ra_Force_Auth_Error() {
        add_filter( 'rest_authentication_errors', 'ra_only_allow_logged_in_rest_access' );
     }
     function ra_Disable_Via_Filters() {
    	// Filters for WP-API version 1.x
        add_filter( 'json_enabled', '__return_false' );
        add_filter( 'json_jsonp_enabled', '__return_false' );
        // Filters for WP-API version 2.x
        add_filter( 'rest_enabled', '__return_false' );
        add_filter( 'rest_jsonp_enabled', '__return_false' );
        // Remove REST API info from head and headers
        remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
        remove_action( 'template_redirect', 'rest_output_link_header', 11 );
    }
    function ra_only_allow_logged_in_rest_access( $access ) {
        	if( ! is_user_logged_in() ) {
              return new WP_Error( 'rest_cannot_access', __( 'Only authenticated users can access API.', 'disable-json-api' ), array( 'status' => rest_authorization_required_code() ) );
            }
            return $access;
    	} 
    if ( version_compare( $ra_current_WP_version, '4.7', '>=' ) ) {
        ra_Force_Auth_Error();
    } else {
        ra_Disable_Via_Filters();
    }
}
   $reportattacks_rest_api = trim(get_site_option('reportattacks_rest_api', 'No'));
   if($reportattacks_rest_api <> 'No')
        add_action( 'plugins_loaded', 'reportattacks_after_inic' );


function rh_activ_message()
{
        echo '<div class="updated"><p>';
        
        $bd_msg = '<img src="'.REPORTATTACKSURL.'images/infox350.png" />';
      
        
        $bd_msg .= '<h2>';
        $bd_msg .= __('Report Attacks Plugin was activated!', 'reportattacks');
        $bd_msg .= '</h2>';
        $bd_msg .= '<h3>';
        
        
        $bd_msg .= __('For details and help, take a look at Report Attacks at your left menu', 'reportattacks') .'<br />';
          
        $bd_msg .= ' <a <a class="button button-primary"  href="admin.php?page=report-attacks">';
        $bd_msg .= __('or click here', "reportattacks");
        $bd_msg .= '</a>';       
        
        
        
        echo $bd_msg;
        echo "</p></h3></div>";
} 


        
if(is_admin())
{
   if(get_option('rh_was_activated', '0') == '1')
   {
     add_action( 'admin_notices', 'rh_activ_message' );
     $r =  update_option('rh_was_activated', '0'); 
     if ( ! $r )
        add_option('rh_was_activated', '0');
   } 
}

function report_attacks_debug_enabled()
   {
            echo '<div class="notice notice-warning is-dismissible">';
     
            echo '<br /><b>';
            echo __('Message from Report Attacks Plugin','reportattacks');
            echo ':</b><br />';
            echo __('Looks like Debug mode is enabled. (WP_DEBUG is true)','reportattacks');
            echo '.<br />';
            
            echo __('if enabled on a production website, it might cause information disclosure, allowing malicious users to view errors and additional logging information', 'reportattacks');
            
            echo '.<br />';       
            echo __('Please, take a look in our site, FAQ page, item => Wordpress Debug Mode or disable this message at General Settings Tab. ', 'reportattacks');
            echo '<br /><br /></div>';
        
    }
    
?>