<?php namespace reportattacksWPSettings;

$mypage = new Page('Report Attacks', array('type' => 'menu'));
     
$settings = array();

require_once (REPORTATTACKSPATH. "guide/guide.php");


$settings['Startup Guide']['Startup Guide'] = array('info' => $reportattacks_help );
$fields = array();   

        
$settings['Startup Guide']['Startup Guide']['fields'] = $fields;

$msg2 = '<big>';
$msg2 .= __('You need only check yes or no below.','reportattacks');
 
$msg2 .= '<br />';

$msg2 .= __('Then click SAVE CHANGES.', 'reportattacks');
$msg2 .= '</big>'; 


$settings['General Settings'][__('Instructions', 'reportattacks')] = array('info' => $msg2);
$fields = array();
   

$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_record_active',
	'label' => __('Record all failed login not included at IP White List?', 'reportattacks'),
	'radio_options' => array(
		array('value'=>'1', 'label' => __('yes', 'reportattacks')),
		array('value'=>'0', 'label' => __('no', 'reportattacks'))
		)			
	);
    
 $fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_email_display',
	'label' => __('Add extra login protection when ip is not withelisted? (Will request your WordPress email at login)', 'reportattacks'),
	'radio_options' => array(
		array('value'=>'1', 'label' => __('yes', 'reportattacks')),
		array('value'=>'0', 'label' => __('no', 'reportattacks'))
		)			
	);
 $fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_block_rep',
	'label' => __("Hides all the site (included the login page) to reported IP's?", "ReportAttacks"),
	'radio_options' => array(
		array('value'=>'1', 'label' => __('yes', 'reportattacks')),
		array('value'=>'0', 'label' => __('no', 'reportattacks'))
		)			
	);    

$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_radio_xml_rpc',
	'label' => 'Disable xml-rpc API. Take a look our faq page (at our site) for details.',
	'radio_options' => array(
		array('value'=>'Yes', 'label' => __('Yes, disable All', 'reportattacks')),
  		array('value'=>'Pingback', 'label' => __('Yes, disable only Ping Back', 'reportattacks' )),
		array('value'=>'No', 'label' => __('No', 'reportattacks')),
		)			
	);
    
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_rest_api',
	'label' => __('Disable Json WordPress Rest API (also new WordPress 4.7 Rest API). Take a look our faq page (at our site) for details.', 'reportattacks'),
	'radio_options' => array(
		array('value'=>'Yes', 'label' => __('Yes, disable', 'reportattacks' )),
		array('value'=>'No', 'label' => __('No', 'reportattacks' )),
		)			
	);
    
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_automatic_plugins',
	'label' => __('Sets WordPress to automatically download and install plugin updates.', "reportattacks"),
	'radio_options' => array(
		array('value'=>'yes', 'label' => __('Yes', "reportattacks")),
		array('value'=>'no', 'label' => __('No', "reportattacks")),
		)			
	); 
 
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_automatic_themes',
	'label' => __('Sets WordPress to automatically download and install themes updates.', "reportattacks"),
	'radio_options' => array(
		array('value'=>'yes', 'label' => __('Yes', "reportattacks")),
		array('value'=>'no', 'label' => __('No', "reportattacks")),
		)			
	);     
    
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_replace_login_error_msg',
	'label' => __('Sets WordPress to replace the login error message to Wrong Username or Password', "reportattacks"),
	'radio_options' => array(
		array('value'=>'yes', 'label' => __('Yes', "reportattacks")),
		array('value'=>'no', 'label' => __('No', "reportattacks")),
		)			
	);

$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_disallow_file_edit',
	'label' => __('Disable file editing within the WordPress dashboard', "reportattacks"),
	'radio_options' => array(
		array('value'=>'yes', 'label' => __('Yes', "reportattacks")),
		array('value'=>'no', 'label' => __('No', "reportattacks")),
		)			
	);
 
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_debug_is_true',
	'label' => __('Enable dashboard warning message when Debug is true', "reportattacks"),
	'radio_options' => array(
		array('value'=>'yes', 'label' => __('Yes', "reportattacks")),
		array('value'=>'no', 'label' => __('No', "reportattacks")),
		)			
	);


                
$settings['General Settings']['']['fields'] = $fields;

$myip = reportattacks_findip2(); 

$msg2 = __('Add your current ip to your whitelist, then click SAVE CHANGES.', "reportattacks");
$msg2 .= '<br />';
$msg2 .= __('If necessary add more than one, use only one ip by line.', "reportattacks");
$msg2 .= '<br />';
$msg2 .=  '<b>';
$msg2 .=  __('Your current ip is: ', "reportattacks" );
$msg2 .= $myip;
$msg2 .=  '</b>';


$settings['My IP White List'][__('Customized whitelist', 'reportattacks')] = array('info' => $msg2);
$fields = array();   
$fields[] = array(
	'type' 	=> 'textarea',
	'name' 	=> 'reportattacks_whitelist',
	'label' => __('My IP White List', 'reportattacks')
	);
        
$settings['My IP White List']['']['fields'] = $fields;

$reportattacks_admin_email = get_option( 'admin_email' ); 
$msg_email = __('Fill out the email address to send messages. Left Blank to use your default Wordpress email.', 'reportattacks');
$msg_email .= '<br /> (';
$msg_email .= $reportattacks_admin_email ;
$msg_email .= ')<br />';
$msg_email .= __('Then, click save changes.', 'reportattacks');

 
$settings['Email Settings']['email'] = array('info' => $msg_email );
$fields = array();
$fields[] = array(
	'type' 	=> 'text',
	'name' 	=> 'reportattacks_my_email',
	'label' => 'email'
	);
$settings['Email Settings']['email']['fields'] = $fields;




$notificatin_msg = __('Do you want receive email alerts for each login attempt?', 'reportattacks');
$notificatin_msg .= '<br /><strong>';
$notificatin_msg .= __('If you under bruteforce attack, you will receive a lot of emails.', 'reportattacks');
$notificatin_msg .= '</strong>';
 
$settings['Notifications Settings']['Notifications'] = array('info' => $notificatin_msg );
$fields = array();

    
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_all_failed_logins',
	'label' => 'Alert me by email each Failed Login',
	'radio_options' => array(
		array('value'=>'1', 'label' => __('Yes.', 'reportattacks')),
		array('value'=>'0', 'label' => __('No.', 'reportattacks')),
		)			
	);    
    

$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_all_logins',
	'label' => __('Alert me All Successfull Login', 'reportattacks'),
	'radio_options' => array(
		array('value'=>'Yes', 'label' => __('Yes, All', 'reportattacks')),
		array('value'=>'No', 'label' => __('No, Alert me Only Not White listed', 'reportattacks')),
		)			
	); 
    
        
$settings['Notifications Settings']['email']['fields'] = $fields;
   
    
$settings['Blocklist Settings'][__('Blocklist Instructions', 'reportattacks')]['fields'] = $fields;


$blocklist_msg = __('To get your free Blocklist API Key, go to Request API Key page under the Repport Attacks menu and follow the instructions there.', 'reportattacks');

$settings['Blocklist Settings'][__('Blocklist Instructions', 'reportattacks')] = array('info' => $blocklist_msg );
$fields = array();

          
$fields[] = array(
	'type' 	=> 'text',
	'name' 	=> 'reportattacks_my_blocklist_api',
	'label' => __('Blocklist API KEY', 'reportattacks')
	);
    
$fields[] = array(
	'type' 	=> 'text',
	'name' 	=> 'reportattacks_my_blocklist_server',
	'label' => __('Blocklist Sender-Address', 'reportattacks')
	);     
    
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'reportattacks_radio_report_attacks',
	'label' => __('Report Attacks (failed logins) to Blocklist.de ?', 'reportattacks'),
	'radio_options' => array(
		array('value'=>'yes', 'label' => __('Yes.', 'reportattacks')),
		array('value'=>'no', 'label' => __('No.', 'reportattacks')),
		)			
	);
        
$settings['Blocklist Settings']['Blocklist Settings and Notifications']['fields'] = $fields;



new OptionPageBuilderTabbed($mypage, $settings);


function reportattacks_findip2()
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

function reportattacks_validate_ip2($reportattacks_ip)
{
    if (filter_var($reportattacks_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}

