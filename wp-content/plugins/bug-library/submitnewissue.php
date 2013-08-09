<head>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $blpluginpath; ?>stylesheet.css"/>
</head>
<body style='background-color: #FFFFFF;'>

<?php 
	require_once('../../../wp-load.php');
	require_once('bug-library.php');
	
	global $wpdb;
	$blpluginpath = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';
	
	$genoptions = get_option('BugLibraryGeneral');

	if (isset($_GET['bugcatid']))
	{
		$bugcatid = $_GET['bugcatid'];
	}
	else
	{
		$bugcatid = -1;
	}
	
	$valid = -1;
	
	if (isset($_POST['new-bug-submit']))
	{
		if ($_POST['new-bug-title'] != '' && $_POST['new-bug-version'] != '' && $_POST['new-bug-desc'] != '' && (($genoptions['requirename'] == false) || ($genoptions['requirename'] == true && $_POST['new-bug-reporter-name'] != '') && ($genoptions['requireemail'] == false || ($genoptions['requireemail'] == true && $_POST['new-bug-reporter-email'] != ''))))
		{
			if ($genoptions['showcaptcha'] == true)
			{
				if (empty($_REQUEST['confirm_code']))
				{
					$valid = 0;
					$validmessage = __('Confirm code not given', 'bug-library') . ".";
				}
				else
				{
					if ( isset($_COOKIE['Captcha']) )
					{
						list($Hash, $Time) = explode('.', $_COOKIE['Captcha']);
						if ( md5("HFDJUJRPOSKKKLKUEB".$_REQUEST['confirm_code'].$_SERVER['REMOTE_ADDR'].$Time) != $Hash )
						{
							$valid = 0;
							$validmessage = __('Captcha code is wrong', 'bug-library') . ".";
						}
						elseif( (time() - 5*60) > $Time)
						{
							$valid = 0;
							$validmessage = __('Captcha code is only valid for 5 minutes', 'bug-library') . ".";
						}
						else
						{
							$valid = 1;					
						}
					}
					else
					{
						$valid = 0;
						$validmessage = __('No captcha cookie given. Make sure cookies are enabled', 'bug-library') . ".";
					}
				}
			}
			else
			{
				$valid = 1;
			}
			
			if ($valid == 1)
			{			
				if ($genoptions['moderatesubmissions'] == true)
					$bugvisible = 'private';
				elseif ($genoptions['moderatesubmissions'] == false)
					$bugvisible = 'publish';
					
				$new_bug_data = array(
					'post_status' => $bugvisible, 
					'post_type' => 'bug-library-bugs',
					'post_author' => '',
					'ping_status' => get_option('default_ping_status'), 
					'post_parent' => 0,
					'menu_order' => 0,
					'to_ping' =>  '',
					'pinged' => '',
					'post_password' => '',
					'guid' => '',
					'post_content_filtered' => '',
					'post_excerpt' => '',
					'import_id' => 0,
					'comment_status' => 'open',
					'post_content' => wp_specialchars(stripslashes($_POST['new-bug-desc'])),
					'post_date' => date("Y-m-d H:i:s", current_time('timestamp')),
					'post_date_gmt' => date("Y-m-d H:i:s", current_time('timestamp', 1)),
					'post_excerpt' => '',
					'post_title' => wp_specialchars(stripslashes($_POST['new-bug-title'])));

				$newbugid = wp_insert_post( $new_bug_data );
				
				$productterm = get_term_by( 'id', $_POST['new-bug-product'], "bug-library-products");
				if ($productterm)
				{
					wp_set_post_terms( $newbugid, $productterm->name, "bug-library-products" );
				}
				
				wp_set_post_terms( $newbugid, $genoptions['defaultuserbugstatus'], "bug-library-status" );
                                
                                wp_set_post_terms( $newbugid, $genoptions['defaultuserbugpriority'], "bug-library-priority" );
				
				$typeterm = get_term_by( 'id', $_POST['new-bug-type'], "bug-library-types");
				if ($typeterm)
				{
					wp_set_post_terms( $newbugid, $typeterm->name, "bug-library-types" );
				}
				
				if ($_POST['new-bug-version'] != '')
				{
					update_post_meta($newbugid, "bug-library-product-version", $_POST['new-bug-version']);
				}
				
				if ($_POST['new-bug-reporter-name'] != '')
				{
					update_post_meta($newbugid, "bug-library-reporter-name", $_POST['new-bug-reporter-name']);
				}
				
				if ($_POST['new-bug-reporter-email'] != '')
				{
					update_post_meta($newbugid, "bug-library-reporter-email", $_POST['new-bug-reporter-email']);
				}
				
				update_post_meta($newbugid, "bug-library-resolution-date", "");
				update_post_meta($newbugid, "bug-library-resolution-version", "");
				
				$uploads = wp_upload_dir();
				
				if(array_key_exists('attachimage', $_FILES))
				{
					$target_path = $uploads['basedir'] . "/bug-library/bugimage-" . $newbugid. ".jpg";
					$file_path = $uploads['baseurl'] . "/bug-library/bugimage-" . $newbugid . ".jpg";
					
					if (move_uploaded_file($_FILES['attachimage']['tmp_name'], $target_path))
					{
						update_post_meta($newbugid, "bug-library-image-path", $file_path);
					}					
				}
				
				if ($genoptions['newbugadminnotify'] == true)
				{
					$adminmail = get_option('admin_email');
					$headers = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
					
					$message = __('A user submitted a new bug to your Wordpress Bug database.', 'bug-library') . "<br /><br />";
					$message .= __('Bug Title', 'bug-library') . ": " . wp_specialchars(stripslashes($_POST['new-bug-title'])) . "<br />";
					$message .= __('Bug Description', 'bug-library') . ": " . wp_specialchars(stripslashes($_POST['new-bug-desc'])) . "<br />";
					$message .= __('Bug Product', 'bug-library') . ": " . wp_specialchars(stripslashes($productterm->name)) . "<br />";
					$message .= __('Bug Version', 'bug-library') . ": " . wp_specialchars(stripslashes($_POST['new-bug-version'])) . "<br />";
					$message .= __('Bug Type', 'bug-library') . ": " . wp_specialchars(stripslashes($typeterm->name)) . "<br />";					
					$message .= __('Reporter Name', 'bug-library') . ": " . wp_specialchars(stripslashes($_POST['new-bug-reporter-name'])) . "<br />";
					$message .= __('Reporter E-mail', 'bug-library') . ": " . $_POST['new-bug-reporter-email'] . "<br /><br />";
					
					if ($genoptions['moderatesubmissions'] == true)
						$message .= "<a href='" . WP_ADMIN_URL . "/edit.php?post_status=private&post_type=bug-library-bugs'>Moderate new bugs</a>";
					elseif ($genoptions['moderatesubmissions'] == false)
						$message .= "<a href='" . WP_ADMIN_URL . "/edit.php?post_type=bug-library-bugs'>View bugs</a>";
						
					$message .= "<br /><br />" . __('Message generated by', 'bug-library') . " <a href='http://yannickcorner.nayanna.biz/wordpress-plugins/bug-library/'>Bug Library</a> for Wordpress";
					
					if ($genoptions['bugnotifytitle'] != '')
					{
						$emailtitle = stripslashes($genoptions['bugnotifytitle']);
						$emailtitle = str_replace('%bugtitle%', wp_specialchars(stripslashes($_POST['new-bug-title'])), $emailtitle);
					}
					else
					{
						$emailtitle = htmlspecialchars_decode(get_option('blogname'), ENT_QUOTES) . " - " . __('New bug added', 'bug-library') . ": " . htmlspecialchars($_POST['new-bug-title']);
					}
					
					wp_mail($adminmail, $emailtitle, $message, $headers);
				}
			}
		}
		else
		{
			$valid = 0;
			$validmessage = __("Missing required field(s). Please complete form.", 'bug-library');
		}
	}		
	
	if (!isset($_POST['new-bug-submit']) || $valid == 0): ?>

<div id='bug-library-newissue-form'>
	
	<?php if ($valid == 0): ?>
	
		<div id='bug-library-invalid'><?php echo $validmessage; ?></div>
	
	<?php endif; ?>
	
	

<form name="input" action="<?php echo $blpluginpath; ?>submitnewissue.php" enctype="multipart/form-data" method="POST">
<div id='new-bug-form-title'><h2>Submit a new issue</h2></div>

<div id='new-bug-title-section'>Issue Title <span id='required'>*</span><br />
<input type='text' id='new-bug-title' name='new-bug-title' size='80' <?php if ($valid == 0) echo "value='" . $_POST['new-bug-title'] . "'"; ?> />
</div>

<div id='new-bug-product-section'>Issue Product <span id='required'>*</span><br />
<?php 	$products = get_terms('bug-library-products', 'orderby=name&hide_empty=0');
		
		if ($products) : ?>
			<select id='new-bug-product' name='new-bug-product'>
				<?php foreach ($products as $product):
					if ($product->term_id == $bugcatid) { $selectedstring = " selected='selected'";} else {$selectedstring = '';} ?>
					<option value="<?php echo $product->term_id; ?>" <?php echo $selectedstring; ?>><?php echo $product->name; ?></option>
				<?php endforeach; ?>
			</select>
		<?php endif; ?>
</div>

<div id='new-bug-version-section'>Version Number <span id='required'>*</span><br />
	<input type='text' id='new-bug-version' name='new-bug-version' size='16' <?php if ($valid == 0) echo "value='" . $_POST['new-bug-version'] . "'"; ?> />
</div>
		
<div id='new-bug-type-section'>Issue Type <span id='required'>*</span><br />
<?php $types = get_terms('bug-library-types', 'orderby=name&hide_empty=0');

		if ($types) : ?>
			<select id='new-bug-type' name='new-bug-type'>
				<?php foreach ($types as $type): ?>
					<option value="<?php echo $type->term_id; ?>"><?php echo $type->name; ?></option>
				<?php endforeach; ?>
			</select>		
		<?php endif; ?>
</div>

<div id='new-bug-desc-section'>
Description <span id='required'>*</span><br />
<textarea cols="60" rows="10" name="new-bug-desc"><?php if ($valid == 0) echo $_POST['new-bug-desc']; ?></textarea>
</div>

<div id='new-bug-name-section'>
Issue Reporter Name<?php if ($genoptions['requirename'] == false) echo " (optional)"; else echo " <span id='required'>*</span>"; ?><br />
<input type='text' id='new-bug-reporter-name' name='new-bug-reporter-name' size='60' <?php if ($valid == 0) echo "value='" . $_POST['new-bug-reporter-name'] . "'"; ?> />
</div>

<div id='new-bug-email-section'>
Issue Reported E-mail<?php if ($genoptions['requireemail'] == false) echo " (optional, for update notifications only)"; else echo " <span id='required'>*</span>";?><br />
<input type='text' id='new-bug-reporter-email' name='new-bug-reporter-email' size='60' <?php if ($valid == 0) echo "value='" . $_POST['new-bug-reporter-email'] . "'"; ?> />
</div>

<?php if ($genoptions['allowattach']): ?>
Attach File<br />
<input type="file" name="attachimage" id="attachimage" /> 
<?php endif; ?>

<?php if ($genoptions['showcaptcha']): ?>
	<div id='new-bug-captcha'><span id='captchaimage'><img src='<?php echo $blpluginpath . "captcha/easycaptcha.php"; ?>' /></span><br />
	<?php _e('Enter code from above image', 'bug-library'); ?><input type='text' name='confirm_code' />
	</div>
<?php endif; ?>

<input type="submit" id='new-bug-submit' name='new-bug-submit' value="Submit" />

</form>
</div>
<?php elseif ($valid == 1): ?>
<div id='bug-library-submissionaccepted'>
<h2>Thank you for your submission.</h2><br /><br />
<?php if ($genoptions['moderatesubmissions'] == 'true') echo "Your new issue will appear on the site once it has been moderated.<br /><br />"; ?>
Click <a href='<?php echo $blpluginpath; ?>submitnewissue.php'>here</a> to submit a new issue or close the window to go continue browsing the database.
</div>
<?php endif; ?>
</body>