<?php 

add_action('admin_menu', 'reportattacks_add_admin_menu');


function reportattacks_add_admin_menu() {
    add_submenu_page(
        'report-attacks', // $parent_slug
        'Request API Key', // string $page_title
        'Request API Key', // string $menu_title
        'manage_options', // string $capability
        'reportattacks_getapi',
        'reportattacks_options_page' );
}


function goblocklist($username,$email)
{

    $url = "https://www.blocklist.de/en/register.html?sid=dc0ee45968bb28a202485377b99a9a70&reporthacks=true";
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
            'name' => $username,
            'email' => $email,
            'agb' => '1',
            'send' => 'send'),
        'cookies' => array()));


    if (is_wp_error($response)) {
         $error_message = $response->get_error_message();
        //echo "Something went wrong: $error_message";
        $error_msg = __('Registration fail. Please, try again. Error:  ', 'reportattacks').$error_message;
        reportattacks_failMsg($error_msg);    
        return;
        
    } else {
   
         $r = '';
         if(isset($response['body'] ))
         { 
            $ret = unserialize($response['body']);
            
            if (is_array($ret)){
               
               if (isset($ret['status']))
                 {
                       $status = $ret['status'];
                       $r = strip_tags($status);
                       if($r != 'ok')
                       {
                           if (isset($ret['msgs']))
                           {
                               $name = $ret['msgs'];
                               if (isset($name['name']))
                               { 
                                   $error_name = $name['name'];
                                   $error_msg = strip_tags($error_name); 
                                   // A Registration with the Name is allready in process.
                                   //die(substr($error_msg,0,51 ) ); //  Send the Registration-Mail again.
                                   if(substr($error_msg,0,52 ) == 'A Registration with the Name is allready in process.')
                                      reportattacks_failMsg('A Registration with this user name is allready in process');    
                                   // A Registration with the Name is allready in process.
                                   return;
                               
                              } // isset($name['name'] 
                               
                          } // isset($ret['msgs']
                       } 
                   
               } // isset($ret['status'])              
             } // (is_array($ret)) 
         } // if(isset($response['body']
         
         if($r == 'ok')
                    reportattacks_okMsg( __('Check your eMail account for email from Blocklist.de','reportattacks'));
         else
                   reportattacks_failMsg( __('Registration fail. Please, try again','reportattacks'));    
      
         return;
  
    }

}



if (isset($_POST['get_api'])) {
    
  
    if (! isset($_POST['reportattacks_blocklistterms'])) {
       reportattacks_failMsg( __('You need fill out all fields and accept the Blocklist Terms of Service','reportattacks'));
       return;
      }
  

    if (isset($_POST['reportattacks_emailblocklist'])) {
         $reportattacks_emailblocklist = trim(sanitize_text_field($_POST['reportattacks_emailblocklist']));
        }
    else
        {
           reportattacks_failMsg( __('Empty eMail. Please fill out eMail field','reportattacks'));
           return;
        }    


   if(empty($reportattacks_emailblocklist)){
        reportattacks_failMsg( __('Empty eMail. Please fill out eMail field','reportattacks'));
        return;
       } 
   if (!is_email($reportattacks_emailblocklist)) {
           reportattacks_failMsg( __('Invalid eMail','reportattacks'));
           return;
      }


    if (isset($_POST['reportattacks_usernameblocklist'])) {
        $reportattacks_usernameblocklist = trim(sanitize_text_field($_POST['reportattacks_usernameblocklist']));
    }
    else
    {
        
      reportattacks_failMsg( __('Empty Username. Please, fill out username field','reportattacks'));
      return;      
        
    }
    
    if(empty($reportattacks_usernameblocklist))
    {
        reportattacks_failMsg( __('Empty Username. Please, fill out username field','reportattacks'));
        return;
    }
    if(strlen($reportattacks_usernameblocklist) < 5)
    {
        reportattacks_failMsg( __('Username size need be between 5 and 20','reportattacks'));
        return;
    }


   if (!preg_match("/^[a-zA-Zà-ýÀ-Ýß\-0-9\.,@_ ]{3,100}$/i", $reportattacks_usernameblocklist))
    {
        reportattacks_failMsg( __('Invalid Username. You can use only normal characters - A to Z - (also numbers and underscore) of the English alphabet','reportattacks'));
        return;
    }
    goblocklist($reportattacks_usernameblocklist,$reportattacks_emailblocklist);


} // clicked get ...



function reportattacks_failMsg($txt)
{
    global $reportattacks_txt;
    $reportattacks_txt = $txt;  
    function report_attacks_showfail()
    {
      global $reportattacks_txt;
      echo '<div class="error notice"><br />';
      echo $reportattacks_txt;
      echo '.<br /><br /></div>';        
    }

    add_action( 'admin_notices', 'report_attacks_showfail' );

}

function reportattacks_okMsg($txt)
{
    global $reportattacks_txt;
    $reportattacks_txt = $txt; 
    
    function report_attacks_shownok()
    {
      global $reportattacks_txt;
      echo '<div class="updated"><br />';
      echo $reportattacks_txt;
      echo '.<br /><br /></div>';        
    }

    add_action( 'admin_notices', 'report_attacks_shownok' );
    

}



function reportattacks_options_page()
{ 
   
    ?>
	
    <form action='<?php echo $_SERVER['PHP_SELF'];?>?page=reportattacks_getapi' method='post'>

        <h1>Report Attacks</h1>
        <h4>
        <?php echo __('Fill Out this form to Get your free Blocklist API KEY', 'reportattacks'); ?>
        </h4>
         <em>
         <?php echo __('Request once, then check your eMail account for email from Blocklist.de', 'reportattacks'); ?>       
         <br />
         <?php echo __('Check also your spam folder.', 'reportattacks'); ?>
        </em>
        <br /> <br /> 
        <hr />
        <br /> <br /> 
        <?php echo __('Blocklist User Name *', 'reportattacks'); ?>
        :&nbsp;<input name="reportattacks_usernameblocklist" type="text" value="" placeholder="<?php echo __('Type here your username', 'reportattacks'); ?>" size="20" />
        <br />
        <em>
        <?php echo __('To create your user name, you can use only normal characters - A to Z - (also numbers and underscore) of the English alphabet.', 'reportattacks'); ?>.
        <br />
        <?php echo __('Min size 5 max 20', 'reportattacks'); ?>.
        <!-- // (!preg_match("/^[a-zA-Zà-ýÀ-Ýß\-0-9\.,@_ ]{3,100}$/i", $_POST['name'])) -->
        
        </em>
        <br /><br />
        <?php echo __('Your eMail *', 'reportattacks'); ?>
        :&nbsp;<input name="reportattacks_emailblocklist" type="text" value="" placeholder="<?php echo __('Type here your email', 'reportattacks'); ?>" size="50" />
        
        <br />
        <em>
        <?php echo __('Valid email address where you will receive your API KEY from Blocklist.de', 'reportattacks'); ?>.
        </em>
        <br />
        <br />
        <input type="checkbox" name="reportattacks_blocklistterms" />
        <em> *
        <?php echo __('I read and agree with the terms of service (below) of Blocklist.de', 'reportattacks'); ?>.
        </em>
        <br />
        <br />
                <em> *
        <?php echo __('All 3 fields are required', 'reportattacks'); ?>.
        </em>

        <br />
        <br />
        
               
        <input type="submit" name="get_api" class="button button-primary" value="Request API Key" />
        <br />
        <br />
        <br />
        <hr />
        <br />
        <br />
       <em> 
       <?php echo __('You can also request your free API KEY at: https://www.blocklist.de/en/register.html', 'reportattacks'); ?>
       </em>
       
        <br />
        <br />
        
        <textarea cols="60" rows="8" wrap="virtual" maxlength="100" readonly="readonly">
        BLOCKLIST TERMS OF SERVICE
        
        By activating the account, the user no obligations or rights goes against a blocklist.de. 
        
        1. Processing of the reports. 
        
        Once blocklist.de reports obtained, they are processed automatically. 
        That is, it is checked by which Return-Path / From address the mail comes and whether certain items are present. 
        Does the e-mail is not the criteria (not activated address, no log files exist, incorrect service ...), then this is not processed and the Return-Path address obtained depending on the nature of the error, an automated response. 
        
        
        2. Transfer the log files. 
        
        The passing of the log files is important for Abuse Departments, as these may need to take over the polluters accountable. 
        Reports without logfiles are, therefore not processed. 
        Email addresses are automatically replaced by "<X>". Other words can be entered and exchanged via the profile. 
        In profile under "Replace" may, for example, the host name will ausgeXt. 
        Data from various services such as Reg-Bot, BadBot we give to automate www.stopforumspam.com / abuseipdb.com on. 
        
        
        3. Storage of the data. 
        
        The emails from Fail2Ban be stored for 14 days in case questions arise or require Abuse Departments log files again and also to present, if anyone objecting to the report complaint. 
        The IP address of the attacker is up to 14 days displayed publicly. 
        14 day automatically extended by any other attack. 
        In the export list of IP addresses on the past 48 hours are displayed when they have run during the period an attack. 
        The display is used to recognize the providers, such as unsafe / tried their network. 
        
        A deletion is of course at any time on request and after the Vulnerability fix (remove the malware / virus / worm, hedge scripts / web server, disabling IP) possible. 
        
        The operator of blocklist.de also maintains the right to delete data / user / server / reports or edit should they violate rights, requirements or the like. 
        
        
        4. Format of reports. 
        
        Our reports are in X-ARF generated, whereby the provider reports can also automatically process. 
        This also a very fine ranking of reports is possible. 
        Report examples can be found on the page in the left menu. 
        
        
        5. Profile / Account 
        
        The use of the service of blocklist.de as "reporter" for free (except download the lists in to large volume)! 
        The account must be made by double-opt-in to be confirmed by the mailbox owner. 
        The user can then place an order with the profile of the newsletter, make display settings, app server, edit or delete Server server, change your credentials (which are stored by sha512 via salted hash). 
        
        Should the user not wish to use the service, so it can even delete his account and all data (server, profile data ...). 
        Data, which are required for the reports already created will be automatically deleted by the above deadline. 
        
        Accounts where the recipient address does not exist or where the domain contain no MX records after multiple delivery attempts will be blocked and deleted after an additional delivery attempts. About the contact form you can unlock it. 
        
        
        Legal validity of this disclaimer
        If sections or individual formulations of this text are not, no longer or not completely correct, the remaining parts of the document remain unaffected in their content and validity. 
        </textarea>

</form>
<?php } ?>