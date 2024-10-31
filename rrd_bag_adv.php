<?php
/*
Plugin Name: RRD Advertisement sponser
Plugin URI: http://rockeyboy.wordpress.com/
Description: Plugin For business directory
Version: 1.0
Author: Rahul Dhamecha
Author URI: http://rockeyboy.wordpress.com/
*/

//*********** for Debugging ********************//
// Hides errors from being displayed on-screen
@ini_set('display_errors', 0);
$nic_version = '1.0.0';
//*********** End for Debugging ********************//

//*********** Start Plugin Update ********************//
if (get_option(NIC_VERSION_NUM) != $nic_version) 
{
    // Execute your upgrade logic here
	nicadv_update_database_table();
    // Then update the version value
    update_option(NIC_VERSION_NUM, $nic_version);
}
//*********** End Plugin Update ********************//

//*********** for install/uninstall actions (optional) ********************//
register_activation_hook(__FILE__,'nicadv_install');
register_deactivation_hook(__FILE__, 'nicadv_uninstall');
register_uninstall_hook(__FILE__, 'nicadv_delete');
function nicadv_install()
{
     nicadv_uninstall();//force to uninstall option
	 add_option(NIC_VERSION_KEY, NIC_VERSION_NUM);
	 nicadv_update_database_table();	
}
function nicadv_uninstall()
{
    if(get_option('nic_version'))
	{
    	delete_option("nic_version");
	}	
}
function nicadv_delete()
{
	nicadv_delete_database_table();
}
//*********** end of install/uninstall actions (optional) ********************//

//*********** Start Update table ********************//
function nicadv_update_database_table() 
{
}
//*********** End Update table ********************//

//*********** Start Delete table ********************//
function nicadv_delete_database_table() 
{
}
//*********** End Delete table ********************//

class iNIC_Bag_Adv 
{	
	public function __construct() 
	{	
		//*********** Start Plugin Global Variables ********************//	
		if (!defined('MYPLUGIN_THEME_DIR'))
		define('MYPLUGIN_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());
		
		if (!defined('NIC_PLUGIN_NAME'))
		define('NIC_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
		
		if (!defined('NIC_PLUGIN_DIR'))
		define('NIC_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . NIC_PLUGIN_NAME);
		
		if (!defined('NIC_PLUGIN_URL'))
		define('NIC_PLUGIN_URL', WP_PLUGIN_URL . '/' . NIC_PLUGIN_NAME);
		
		if (!defined('NIC_PLUGIN_IMAGES'))
		define('NIC_PLUGIN_IMAGES', NIC_PLUGIN_URL.'/images/');
		
		if (!defined('NIC_PLUGIN_IMAGES'))
		define('NIC_PLUGIN_IMAGES', NIC_PLUGIN_URL.'/css/');
		
		if (!defined('NIC_PLUGIN_JS'))
		define('NIC_PLUGIN_JS', NIC_PLUGIN_URL.'/js/');	
		
		if (!defined('NIC_VERSION_KEY'))
		define('NIC_VERSION_KEY', 'nic_version');
		
		if (!defined('NIC_VERSION_NUM'))
		define('NIC_VERSION_NUM', $nic_version);	
		//*********** End Plugin Global Variables ********************//
		
		//create custom post types and taxonomy
		add_action( 'init', 'nicadv_custom_init' );
		
		// Add custom fields in admin sponser page
		add_action( 'Sponser_add_form_fields', array($this,'nicadv_taxonomy_add_new_meta_field' ), 10, 2 );
		
		//add meta box for custom post type
		add_action( 'add_meta_boxes', array($this,'nicaddv_meta_box_add'));  
		
		//add js and css to admin 
		add_action( 'admin_init', array($this,'nicadv_Add_admin_js_css'));  
		
		//function for handling save/Update event 
		add_action( 'save_post', array($this,'nicadv_save_admin_data'));
		
		// Shortcode 
		add_shortcode("add-advertise", array($this,"nicadv_add_advertise"));
		add_shortcode("view-advertise-listing", array($this,"nicadv_view_advertise_listing"));
		
		/* Filter the single_template with our custom function*/
		add_filter('single_template', array($this,'nic_custom_template'));	
			
		// create custom Ajax call for WordPress
		add_action( 'wp_ajax_nopriv_AddAdvertisement', array($this,'nicadv_Ajax_add_Advertisement') );
		add_action( 'wp_ajax_AddAdvertisement', array($this,'nicadv_Ajax_add_Advertisement') );
		
		//add css js on front side
		add_action("admin_head", array($this,"nicadv_Item_edit_area_selection"));
		
		//Hide image size on admin area while uploading it for Iteam
		add_filter('image_size_names_choose', array($this,'nicadv_remove_wmp_image_sizes'));
		
		// create custom Ajax call for WordPress
		add_action( 'wp_ajax_nopriv_CreateImg', array($this,'nicadv_Ajax_Create_Image') );
		add_action( 'wp_ajax_CreateImg', array($this,'nicadv_Ajax_Create_Image') );		
		
		// create custom Ajax call for WordPress
		add_action( 'wp_ajax_nopriv_AddSponser', array($this,'nicadv_Ajax_Add_Sponser') );
		add_action( 'wp_ajax_AddSponser', array($this,'nicadv_Ajax_Add_Sponser') );	
		
		add_action( 'wp_ajax_nopriv_uploadImg', array($this,'nicadv_Ajax_uploadImg') );
		add_action( 'wp_ajax_uploadImg', array($this,'nicadv_Ajax_uploadImg') );	
		
		add_action( 'wp_ajax_nopriv_AdminChangeImg', array($this,'nicadv_Ajax_Admin_Change_Item_Image') );
		add_action( 'wp_ajax_AdminChangeImg', array($this,'nicadv_Ajax_Admin_Change_Item_Image') );			
		
		add_action( 'wp_ajax_nopriv_AddAdvSponser', array($this,'nicadv_Ajax_Add_Advert_Sponser') );
		add_action( 'wp_ajax_AddAdvSponser', array($this,'nicadv_Ajax_Add_Advert_Sponser') );	
		
		add_action( 'wp_ajax_nopriv_AddSponserAdvPartList', array($this,'nicadv_Ajax_Add_Sponser_Advert_Part_List') );
		add_action( 'wp_ajax_AddSponserAdvPartList', array($this,'nicadv_Ajax_Add_Sponser_Advert_Part_List') );	
		
		//add field to custom post type listing on admin panel
		add_filter('manage_advertiser_posts_columns', array($this,'nicadv_columns_head'));  
		add_action('manage_advertiser_posts_custom_column', array($this,'nicadv_columns_content'), 10, 2); 
		
		add_filter('manage_sponser_posts_columns', array($this,'nicadv_sponser_columns_head'));  
		add_action('manage_sponser_posts_custom_column', array($this,'nicadv_sponser_columns_content'), 10, 2);
		
		add_filter('manage_iteam_posts_columns', array($this,'nicadv_iteam_columns_head'));  
		add_action('manage_iteam_posts_custom_column', array($this,'nicadv_iteam_columns_content'), 10, 2); 
		
		//Handler for trahing custom post type
		add_action('wp_trash_post', array($this,'nicadv_trash_function'));
		
		//Handler for trahing custom post type deletion
		add_action('delete_post', array($this,'nicadv_delete_function'));
		
		/** 	* Hook into admin notices 	*/
		add_action('admin_notices', array($this,'nicadv_show_admin_messages'));
		
		// Add restiction for allow patition to be booked for sponser at sponser update time 
		add_action('publish_sponser',array($this,'nicadv_sponser_book_partition'));
		
		//This is for advertisement filter for sponsers
		add_action('restrict_manage_posts',array($this,'nicadv_restrict_manage_posts'));		
		
		//Handler for advertisement filter for sponsers
		add_action( 'request', array($this,'nicadv_request' ));
		
		// handle pulish event for post pages and custom post types		
		add_action( 'draft_to_publish', array($this,'nicadv_draft_to_publish' ));
		
		// activate textdomain for translations
		load_plugin_textdomain('nicadvtext', false, dirname(plugin_basename( __FILE__ )));
	}		
	
	function nicadv_Ajax_Add_Sponser_Advert_Part_List()
	{
		$AdvertiserId = $_POST['advertiseId'];
		$CurrAdvertise = $_POST['curradvertiseId'];
		$Flag = 0;	
		if($AdvertiserId == $CurrAdvertise)
		{
			$Flag = 1;	
		}
		global $wpdb;		
		$MyPatition = $wpdb->get_results("SELECT DISTINCT p.`post_title`,m.`meta_key`,m.`meta_value` FROM `wp_posts` AS p LEFT JOIN `wp_postmeta` AS m ON p.`ID` = m.`post_id`	WHERE p.`post_type` = 'sponser' AND p.`post_status` = 'publish'	AND p.`post_parent` = '".$AdvertiserId."' AND m.`meta_key` = 'sponser_partition'");		
		if(count($MyPatition) > 0)
		{
			$ShowselectFlag = 0;
			$PartitionArr = array();			
			foreach($MyPatition as $Partition)
			{
				$PartitionArr[] = $Partition->meta_value;
			}
			//echo "<pre>";
			//print_r($PartitionArr);
			$AdvHeightWidth = $wpdb->get_results("SELECT DISTINCT p.`post_title`,m.`meta_key`,m.`meta_value` FROM `wp_posts` AS p LEFT JOIN `wp_postmeta` AS m 
			ON p.`ID` = m.`post_id`	WHERE p.`post_type` = 'advertiser' AND p.`ID` = '".$AdvertiserId."' AND m.`meta_key` = 'advportions'",ARRAY_A);
			$selectHtml = '<select name="admin_sponser_adv_part" id="admin_sponser_adv_part">';
			$AdvRes = $AdvHeightWidth[0]['meta_value'];
			$AdvRes = unserialize(unserialize($AdvRes));
			$TotSquare = $AdvRes['rowcount']*$AdvRes['columncount'];
			for($cnt = 1; $cnt <= $TotSquare; $cnt++)
			{
				if(!in_array($cnt,$PartitionArr) || ($_POST['partition'] == $cnt && $Flag == 1))
				{
					$ShowselectFlag = 1;
					$sel = '';
					if($PartitionId == $cnt)
					{
						$sel = 'selected="selected"';
					}
					$selectHtml .= '<option value="'.$cnt.'" '.$sel.'>'.$cnt.'</option>';
				}
			}
			$selectHtml .= '</select>';	
		}	
		if($ShowselectFlag == 1)
		{
			echo $selectHtml;
		}
		else
		{
			echo '<span style="color:red; font-weight:bold;">'.__('No Available Partition.','nicadvtext').'</span>';
		}		
		exit;	
	}
	
	function nicadv_draft_to_publish()
	{
		global $post;
		//If admin assign advertisement and partition for sponser then and then publish sponser
		if($_POST['admin_sponser_adv'] == '' && $_POST['admin_sponser_adv_part'] == '')
		{
			if ( is_admin() && $post->post_type == 'sponser') 
			{
				if($post->post_parent <= 0)
				{
					wp_update_post(array('ID'=>$post->ID,'post_status' => 'draft'));				
					return;
				}	
			}			
		}
		else
		{
			wp_update_post(array('ID'=>$post->ID,'post_parent' => $_POST['admin_sponser_adv']));
		}
	}
	
	function nicadv_Ajax_Add_Advert_Sponser()
	{
		global $wpdb; 
		
		$AdvMetas = unserialize(get_post_meta($_POST['postId'],'advportions',ARRAY_A));
		$TotalSquare = $AdvMetas['rowcount']*$AdvMetas['columncount'];
		if($_POST['partition'] > $TotalSquare)
		{
			echo __('Invalid Partition.','nicadvtext');
			exit;
		}
		
		$ValidData = $wpdb->get_results("
		SELECT p.`post_title`,p.`post_parent`,p.`ID`
		FROM `wp_posts` AS p
		LEFT JOIN `wp_postmeta` AS m
		ON p.`ID` = m.`post_id`
		WHERE p.`post_type` = 'sponser'
		AND p.`post_parent` = '".$_POST['postId']."'
		AND p.`post_status` = 'publish'
		AND m.`meta_key` = 'sponser_partition'
		AND m.`meta_value` = '".$_POST['partition']."'
		");
		
		if(count($ValidData) > 0)
		{
			echo __('Partition is already assigned.','nicadvtext');
			exit;
		}
		else
		{			
			if(isset($_POST['chk']) && $_POST['chk'] == 1)
			{
				// add post meta for sponser
				wp_update_post(array('ID' => $_POST['sponser'], 'post_parent' => $_POST['postId'], 'post_status' => 'publish' ));
				// update sponser and sassign advertisment for sponser
				update_post_meta($_POST['sponser'], 'sponser_advertiser', $_POST['postId']);
				update_post_meta($_POST['sponser'], 'sponser_partition', $_POST['partition']);	
				exit;				
			}	
			
			if(isset($_POST['chk']) && $_POST['chk'] == 2)
			{
				// Remove sponser from advertisment
				delete_post_meta($_POST['sponser'], 'sponser_advertiser', $_POST['postId']);
				delete_post_meta($_POST['sponser'], 'sponser_partition', $_POST['partition']);	
				// remove advertisment from sponser and change status of sponser to draft
				wp_update_post(array('ID' => $_POST['sponser'], 'post_parent' => 0, 'post_status' => 'draft'));
				exit;
			}
		}
	}
	
	function nicadv_restrict_manage_posts() 
	{
		if($_GET['post_type'] == 'advertiser')
		{
			$GetIteam = '';
			if(isset($_GET['Iteam']) && $_GET['Iteam'] != '')
			{
				$GetIteam	= $_GET['Iteam'];
			}
			$IteamData = get_posts(array('post_type'=>'iteam','post_status'=>'publish')); 
			?>
			<select class="postform" id="Iteam" name="Iteam">
                <option selected="selected" value="0"><?php echo __('Show All Iteams','nicadvtext'); ?></option>
                <?php
                foreach($IteamData as $Iteam)
                {
					$sel = '';
					if($GetIteam == $Iteam->ID)
					{
						$sel = 	'selected="selected"';
					}
                    echo '<option '.$sel.' value="'.$Iteam->ID.'">'.$Iteam->post_title.'</option>';
                }
                ?>
			</select>
			<?php	
		}
		
		if($_GET['post_type'] == 'sponser')
		{
			if(isset($_GET['Sponser']) && $_GET['Sponser'] != '')
			{
				$GetSponser	= $_GET['Sponser'];
			}
			$user_id = get_current_user_id();
			$user_id != 1 ? $SposerData = get_posts(array('post_type'=>'advertiser','post_status'=>'publish','author'=>'$user_id')) : $SposerData = get_posts(array('post_type'=>'advertiser','post_status'=>'publish')) ;
			?>
			<select class="postform" id="Sponser" name="Sponser">
                <option selected="selected" value="0"> <?php echo __('Show All Advertisements','nicadvtext'); ?></option>
                <?php
                foreach($SposerData as $Sponser)
                {
					$sel = '';
					if($GetSponser == $Sponser->ID)
					{
						$sel = 	'selected="selected"';
					}
                    echo '<option '.$sel.' value="'.$Sponser->ID.'">'.$Sponser->post_title.'</option>';
                }
                ?>
			</select>
			<?php	
		}
	}
	
	function nicadv_request($request) 
	{
		global $pagenow;			
		$user_id = get_current_user_id();		
		
		if ($user_id != 1 && $pagenow == 'edit.php' && isset($request['post_type']) && $request['post_type'] == 'sponser')
		{
			$user_id = get_current_user_id();		
			$request['meta_key']   	= $_GET['sponser_advertiser'];
			$request['meta_value'] 	= $_GET['Sponser'];			
			$request['author'] = $user_id;
			return $request;
		}
		
		if ($user_id != 1 && $pagenow == 'edit.php' && isset($request['post_type']) && $request['post_type'] == 'advertiser')
		{
			$user_id = get_current_user_id();		
			$request['meta_key']   = $_GET['adv_iteam'];
			$request['meta_value'] = $_GET['Iteam'];
			$request['author'] = $user_id;
			return $request;
		}
	
		if( is_admin() && $pagenow == 'edit.php' && isset($request['post_type']) && $request['post_type']=='sponser' ) 
		{			
			$request['meta_key']   = $_GET['sponser_advertiser'];
			$request['meta_value'] = $_GET['Sponser'];
		}
		
		if( is_admin() && $pagenow == 'edit.php' && isset($request['post_type']) && $request['post_type']=='advertiser' ) 
		{			
			$request['meta_key']   = $_GET['adv_iteam'];
			$request['meta_value'] = $_GET['Iteam'];
		}
		
		return $request;
	}

	function nicadv_sponser_book_partition()
	{
		global $post;
		$post_id = $post->ID;				
		$post_type = $post->post_type;
		if($post_type = 'sponser')
		{
			$SponserAdvertisement = get_post_meta($post_id,'sponser_advertiser',ARRAY_A);
			$SponserPartition = get_post_meta($post_id,'sponser_partition',ARRAY_A);
			global $wpdb;
			$SponserData = $wpdb->get_results("SELECT p.`post_title`,p.`ID` FROM `".$wpdb->prefix."posts` AS P , `".$wpdb->prefix."postmeta` AS m
			WHERE p.`ID` IN ( SELECT m.`post_id` FROM `wp_posts` AS P JOIN `wp_postmeta` AS m 
			ON p.`ID` = m.`post_id`
			WHERE m.`meta_key` = 'sponser_advertiser' AND m.`meta_value` = '".$SponserAdvertisement."')
			AND p.`ID` = m.`post_id`
			AND m.`meta_key` = 'sponser_partition' AND m.`meta_value` = '".$SponserPartition."'");
			if(count($SponserData) > 0)
			{
				$this->nicadv_add_admin_message('Sponser is aleady assigned for this Advertisement partition. You can not publish it.',true);	
				wp_update_post(array('ID'=>$post_id, 'post_status' => 'draft'));									
				$this->nicadv_show_admin_messages();				
				$link =  wp_get_referer();	
				wp_redirect($link);
				exit;
			}	
		}
	}
	
	/**	* Messages with the default wordpress classes */
	function nicadv_showMessage($message, $errormsg = false)
	{
		if ($errormsg) 
		{
			echo '<div id="message" class="error">';
		}
		else 
		{
			echo '<div id="message" class="updated fade">';
		}	
		echo "<p>$message</p></div>";
		$_COOKIE['wp-admin-messages-error'] = '';
		setcookie('wp-admin-messages-error', null);
		$message = '';
	}
	
	/**	* Display custom messages	*/
	function nicadv_show_admin_messages()
	{
		if(isset($_COOKIE['wp-admin-messages-normal'])) 
		{
			$messages = strtok($_COOKIE['wp-admin-messages-normal'], "@@");		
			while ($messages !== false) 
			{
				$this->nicadv_showMessage($messages, true);
				$messages = strtok("@@");
			}
			setcookie('wp-admin-messages-normal', null);
		}
		
		if(isset($_COOKIE['wp-admin-messages-error'])) 
		{
			$messages = strtok($_COOKIE['wp-admin-messages-error'], "@@");			
			while ($messages !== false) 
			{
				$this->nicadv_showMessage($messages, true);
				$messages = strtok("@@");
			}			
			setcookie('wp-admin-messages-error', null);
		}
	}

	/**	* User Wrapper	*/
	function nicadv_add_admin_message($message, $error = false)
	{
		if(empty($message)) return false;		
		if($error) 
		{
			setcookie('wp-admin-messages-error', $_COOKIE['wp-admin-messages-error'] . '@@' . $message, time()+3);
		} 
		else 
		{
			setcookie('wp-admin-messages-normal', $_COOKIE['wp-admin-messages-normal'] . '@@' . $message, time()+3);
		}
	}  
	
	function nicadv_Ajax_Admin_Change_Item_Image()
	{
		$Partition = get_post_meta($_POST['postId'],'partition',ARRAY_A);
		$IteamImg = get_post_meta($_POST['postId'],'itemimg',ARRAY_A);
		echo $Partition.'|'.$IteamImg;
		exit;
	}

	function nicadv_trash_function($post)
	{	
		$Flag = 0;	
		$MyUri = urldecode($_SERVER['REQUEST_URI']);
		if(isset($_GET['post_type']) && $_GET['post_type'] != '')
		{	
			$MyIteams = $_GET['post'];			
			foreach($MyIteams as $iteam)			
			{
				global $wpdb;
				$AdvData = '';
				
				if($_GET['post_type'] == 'iteam')
				{
				$AdvData = $wpdb->get_results("SELECT DISTINCT p.`post_title` FROM`".$wpdb->prefix."posts`AS p,`".$wpdb->prefix."postmeta` AS m WHERE p.`ID`= m.`post_id` AND m.`meta_key` = 'adv_iteam' AND p.`post_status` = 'publish' AND m.`meta_value` = ".$iteam."",ARRAY_A);	
				}
				else if($_GET['post_type'] == 'advertiser')
				{
					$AdvData = $wpdb->get_results("SELECT p.`post_title` FROM `wp_posts` AS p WHERE p.`post_type` = 'sponser' AND p.`post_title` != '' AND p.`post_status` = 'publish' AND p.`post_parent` = '".$iteam."'",ARRAY_A);	
				}
				
				if(count($AdvData) > 0)
				{
					$Flag = 1;
				}
				else
				{
					wp_update_post( array('ID' => $iteam, 'post_status' => 'trash') );							
				}							
			}	
			
			if($Flag == 1 && count($MyIteams) > 0)			
			{				
				if(!isset($_COOKIE['wp-admin-messages-error']) && $_COOKIE['wp-admin-messages-error'] == '')
				{
					if($_GET['post_type'] == 'iteam')
					{
						$msg = __('Some of Iteams can not be trash. Thiese iteams are assigned to advertisers.','nicadvtext');
						$this->nicadv_add_admin_message($msg,true);
							
					}
					if($_GET['post_type'] == 'advertiser')
					{
						$msg = __('Some of Advertisements can not be trash. These Advertises have Sponsers.','nicadvtext');
						$this->nicadv_add_admin_message($msg,true);	
					}					
					$this->nicadv_show_admin_messages();
				}	
				$link =  wp_get_referer();	
				wp_redirect($link);
				exit;
			}				
			else
			{
				$_COOKIE['wp-admin-messages-error'] = '';
				setcookie('wp-admin-messages-error', null);
				setcookie('wp-admin-messages-error', $_COOKIE['wp-admin-messages-error'] . '@@' . ' ', time()-60000);
			}							
		}
		elseif(isset($_GET['post']) && $_GET['post'] != '')
		{
			$Type = get_post_type($_GET['post']);	
			global $wpdb;
			$iteam = $_GET['post'];			
			if($Type == 'iteam')
			{							
				$AdvData = $wpdb->get_results("SELECT DISTINCT p.`post_title` FROM`".$wpdb->prefix."posts`AS p,`".$wpdb->prefix."postmeta` AS m WHERE p.`ID`= m.`post_id` AND m.`meta_key` = 'adv_iteam' AND p.`post_status` = 'publish' AND m.`meta_value` = ".$iteam."",ARRAY_A);			
			}
			else if($Type == 'advertiser')
			{				
				$AdvData = $wpdb->get_results("SELECT p.`post_title` FROM `wp_posts` AS p WHERE p.`post_type` = 'sponser' AND p.`post_title` != '' AND p.`post_status` = 'publish' AND p.`post_parent` = '".$iteam."'",ARRAY_A);								
			}
			if(count($AdvData) > 0)
			{
				$link =  wp_get_referer();
				$link1 = $link.'&doaction=untrash&ids='.$iteam;
				if(!isset($_COOKIE['wp-admin-messages-error']) && $_COOKIE['wp-admin-messages-error'] == '')
				{
					$msg = __('It can not be trash. This Advertisement have Sponsers.','nicadvtext');
					$this->nicadv_add_admin_message($msg,true);	
					$this->nicadv_show_admin_messages();
				}	
				wp_redirect($link1);
				exit;
			}
			else
			{
				$_COOKIE['wp-admin-messages-error'] = '';
				setcookie('wp-admin-messages-error', null);
				setcookie('wp-admin-messages-error', $_COOKIE['wp-admin-messages-error'] . '@@' . ' ', time()-60000);
			}		
		}
	}	
	
	function nicadv_delete_function()
	{
		/*echo "<pre>";
		print_r($_POST);
		echo "This is in delete function";
		exit;*/
	}
	
	function nicadv_iteam_columns_head($defaults) 
	{  
		$defaults['nicadv_advertiser'] = 'Advertiser';  
        return $defaults;  
    } 
	
	function nicadv_iteam_columns_content($column_name, $post_ID) 
	{
		if ($column_name == 'nicadv_advertiser') 
		{  
			global $wpdb;
			$AdvData = $wpdb->get_results("SELECT DISTINCT p.`post_title` FROM`".$wpdb->prefix."posts`AS p,`".$wpdb->prefix."postmeta` AS m WHERE p.`ID`= m.`post_id` AND m.`meta_key` = 'adv_iteam' AND p.`post_status` = 'publish' AND m.`meta_value` = ".$post_ID."",ARRAY_A);
			foreach($AdvData as $advertiser)
			{
				echo $advertiser['post_title'].'</br>';
			}
		}   
	} 	
	
	// ADD NEW COLUMN  
    function nicadv_sponser_columns_head($defaults)
	{  
		$defaults['nicadv_info'] = __('Booking Infomation','nicadvtext');  
		$defaults['nicadv_advertiser'] = __('Advertisement','nicadvtext');  
        return $defaults;  
    }  
	
	function nicadv_sponser_columns_content($column_name, $post_ID) 
	{  	
        if ($column_name == 'nicadv_info') 
		{  
			$Email = get_post_meta($post_ID,'sponser_email',ARRAY_A);
			$Contact = get_post_meta($post_ID,'sponser_contact',ARRAY_A);
			$Partition = get_post_meta($post_ID,'sponser_partition',ARRAY_A);
			$Banner = get_post_meta($post_ID,'sponser_banner',ARRAY_A);
			
			echo $EmailContent = ( $Email != '' ? __('Email','nicadvtext').' : '.$Email.'</br>' : '');
			echo $ContactContent = ( $Contact != '' ? 'Contact : '.$Contact.'</br>' : '');
			echo $PartitionContent = ( $Partition != '' ? __('Partition','nicadvtext').' : '.$Partition.'</br>' : '');
			echo $BannerContent = ( $Banner != '' ? __('Banner','nicadvtext').' : <img src="'.$Banner.'" width="50" height="50" /></br>' : '');		
        }  
		if ($column_name == 'nicadv_advertiser') 
		{  
			$postcontent = get_post($post_ID);
			$advert = get_post($postcontent->post_parent);
			if(isset($advert) && $advert != '')
			{
				echo $advert->post_title;			
			}
			else
			{
				echo __('No Advertiser Assigned','nicadvtext');	
			}
        }  
	}	
	
	// ADD NEW COLUMN  
    function nicadv_columns_head($defaults) 
	{  
        $defaults['nicadv_sponser'] = __('Sponser','nicadvtext'); 
		$defaults['nicadv_iteam'] = __('Advertisement Iteam','nicadvtext');  
        return $defaults;  
    }  
      
    // SHOW THE FEATURED IMAGE  
    function nicadv_columns_content($column_name, $post_ID) 
	{  
        if ($column_name == 'nicadv_sponser') 
		{  
			$MySponser = get_posts(array('post_type'=> 'sponser','post_parent'=> $post_ID,'post_status'=> 'publish'));
			foreach($MySponser as $Sponser)
			{
				$Sponsers .= $Sponser->post_title.', ';
			}
			echo substr($Sponsers,0,-2);            
        }  
		if ($column_name == 'nicadv_iteam') 
		{  
			$MyIteam = get_post_meta($post_ID,'adv_iteam',ARRAY_A);  
			if(isset($MyIteam) && $MyIteam != '')
			{
				$iteam = get_post_meta($MyIteam,'itemimg',ARRAY_A);
				if(isset($iteam) && $iteam != '')
				{
					echo '<img src="'.$iteam.'" width="100" height="100" />';
				}
			}
        }  
    }  
		
	function nicadv_Ajax_uploadImg()
	{
		$wp_upload_dir = wp_upload_dir();
		$filename = $wp_upload_dir['path'].'/'.$_FILES['banner']['name'];
		$fileguid = $wp_upload_dir['url'].'/'.$_FILES['banner']['name'];		
		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'guid' => $fileguid,
		);	
		$attach_id = wp_insert_attachment( $attachment, $filename, $_POST['sponser'] );
		if (move_uploaded_file($_FILES['banner']['tmp_name'], $filename)) 
		{ 
			//echo "success"; 			
			update_post_meta($_POST['sponser'], 'sponser_banner', $fileguid);
		} 
		else 
		{
			//echo "error";
		}
		exit;
	}
	
	function nicadv_Ajax_Add_Sponser()
	{
		$PageTitle		= sanitize_text_field($_POST['name']);
		$PageContent	= '<p>'.sanitize_text_field($_POST['description']).'</p>';
		
		$AddPage = array(
			'ID'             => '', //Are you updating an existing post?
			'comment_status' => 'open', // 'closed' means no comments.			
			'post_author'    =>  '1', //The user ID number of the author.
			'post_category'  => array('1'), //post_category no longer exists, try wp_set_post_terms() for setting a post's categories
			'post_parent'    => $_POST['postId'],
			'post_content'   => $PageContent, //The full text of the post.
			'post_date'      => date('Y-m-d H:i:s'), //The time post was made.
			'post_date_gmt'  => date('Y-m-d H:i:s'), //The time post was made, in GMT.
			'post_name'      => str_replace(' ','-',$PageTitle), // The name (slug) for your post
			'post_status'    => 'draft', //Set the status of the new post.
			'post_title'     => trim($PageTitle), //The title of your post.
			'post_type'      => 'sponser' //You may want to insert a regular post, page, link, a menu item or some custom 	post type
		);		
		
		$sponserPost = wp_insert_post( $AddPage, true );
		update_post_meta($sponserPost, 'sponser_email', $_POST['email']);
		update_post_meta($sponserPost, 'sponser_contact', $_POST['contact']);
		update_post_meta($sponserPost, 'sponser_advertiser', $_POST['postId']);
		update_post_meta($sponserPost, 'sponser_partition', $_POST['partition']);
		//update_post_meta($sponserPost, 'sponser_banner', $_POST['banner']);
		echo __('Sponser added successfully','nicadvtext').'|'.$sponserPost;
		exit;
	}
	
	function nicadv_getExtension($str) 
	{	
		$i = strrpos($str,".");
		if (!$i) { return ""; } 
		
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}
	
	function nicadv_draw_grid(&$img, $x0, $y0, $width, $height, $cols, $rows, $color) 
	{
		//draw outer border
		imagerectangle($img, $x0, $y0, $x0+$width*$cols, $y0+$height*$rows, $color);
		//first draw horizontal
		$x1 = $x0;
		$x2 = $x0 + $cols*$width;
		for ($n=0; $n<ceil($rows/2); $n++) 
		{
			$y1 = $y0 + 2*$n*$height;
			$y2 = $y0 + (2*$n+1)*$height;
			imagerectangle($img, $x1,$y1,$x2,$y2, $color);
		}
		//then draw vertical
		$y1 = $y0;
		$y2 = $y0 + $rows*$height;
		for ($n=0; $n<ceil($cols/2); $n++) 
		{
			$x1 = $x0 + 2*$n*$width;
			$x2 = $x0 + (2*$n+1)*$width;
			imagerectangle($img, $x1,$y1,$x2,$y2, $color);
		}
	}
 
	function nicadv_Ajax_Create_Image()
	{
		$Partition = unserialize(stripslashes($_POST['partition']));		
		$wp_upload_dir = wp_upload_dir();		
		$IteamBaseDir	= $wp_upload_dir['basedir'].'/iteams/';
		$IteamBaseUrl	= $wp_upload_dir['baseurl'].'/iteams/';
		$image = $_POST['iteamimg'];
		$basename = basename($image);		
		$Destination = $IteamBaseDir.$basename;
		$DestinationUrl = $IteamBaseUrl.$basename;
		copy($image,$Destination);
		
		$extension = $this->nicadv_getExtension($Destination);
		$extension = strtolower($extension);
		
		//strtotime("now")
		$newName = $IteamBaseDir.'post_'.$_POST['postId'].'_item.'.$extension;
		rename($Destination, $newName);
		$Destination = $newName;
		
		$errors=0;
		$image = $image;
		$uploadedfile = $DestinationUrl;	 
	 
		if ($image != '') 
		{	 
			$filename = stripslashes($image);	 			
	 
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
			{
				$msg = __('Unknown Image extension','nicadvtext');
				$swap='<div class="msgdiv">'.$msg.'</div> ';
				$errors=1;
			}
			else
			{
				$size=filesize($Destination);
												 
				if ($size > MAX_SIZE*1024)
				{
					$msg = __('File too big.','nicadvtext');
					$swap='<div class="msgdiv">'.$msg.'</div> ';
					$errors=1;
				}

	 
				if($extension=="jpg" || $extension=="jpeg" )
				{
					$uploadedfile = $Destination;					
					$src = imagecreatefromjpeg($uploadedfile);	
							
				}
				else if($extension=="png")
				{							
					$uploadedfile = $Destination;
					$src = imagecreatefrompng($uploadedfile);							
				}
				else 
				{
					$uploadedfile = $Destination;
					$src = imagecreatefromgif($uploadedfile);					
					$black = imagecolorallocate($src, 0, 0, 0);
					// Make the background transparent
					imagecolortransparent($src, $black);	
				}				
				list($width,$height)=getimagesize($uploadedfile);			
				 
				$newwidth = $width;
				$newheight = $height; 
				//$newheight=($height/$width)*$newwidth;
				$tmp=imagecreatetruecolor($newwidth,$newheight);
				
				imagealphablending($tmp, false);
				$color = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
				imagefill($tmp, 0, 0, $color);
				imagesavealpha($tmp, true);
			
				imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
				
				$Rows = $_POST['rows'];
				$Columns = $_POST['columns'];
				$Width = $Partition['w'];
				$Height = $Partition['h'];
				$X1	= $Partition['x1'];
				$X2	= $Partition['x2'];
				$Y1	= $Partition['y1'];
				$Y2	= $Partition['y2'];	
				
				$sqWidth = $Width/$Rows; //width / columns
				$sqHeight = $Height/$Columns; //height / rows	
				
				$pink = imagecolorallocate($tmp, 0, 0, 0);
				$white = imagecolorallocate($tmp, 255, 255, 255);
				
				// Draw a white rectangle
				//imagerectangle($tmp, 84, 40, 224, 147, $pink);	
				imagefilledrectangle($tmp, $X1, $Y1, $X2, $Y2, $white);												
				$this->nicadv_draw_grid($tmp, $X1, $Y1, $sqWidth, $sqHeight, $Rows, $Columns, $pink);
				
				if($extension=="jpg" || $extension=="jpeg" )
				{
					if(file_exists($Destination))
					{
						unlink($Destination);	
					}
					imagejpeg($tmp,$Destination,100);	
				}
				else if($extension=="png")
				{	
					if(file_exists($Destination))
					{
						unlink($Destination);	
					}
					imagepng($tmp,$Destination);
				}
				else 
				{
					if(file_exists($Destination))
					{
						unlink($Destination);	
					}
					imagegif($tmp,$Destination,100);
				}			
				
				imagedestroy($src);
				imagedestroy($tmp);
				
				if(!empty($Destination) && $Destination != '')
				{
					$IteamUrl	= $wp_upload_dir['baseurl'].'/iteams/'.'post_'.$_POST['postId'].'_item.'.$extension;
					echo $IteamUrl;
					exit;
				}
			}
		}
		exit;	
	}
	
	
	function nicadv_remove_wmp_image_sizes( $sizes) 
	{		
		unset( $sizes['thumbnail']);
		//unset( $sizes['medium']);
		unset( $sizes['large']);     
		unset( $sizes['full']);     
		return $sizes;
	}
	
	function nicadv_Item_edit_area_selection()
	{
		global $post;
		//print_r($post);		
		if($post->post_type == 'iteam')
		{			
			wp_enqueue_script( 'newjs', plugins_url('/js/jquery.Jcrop.js', __FILE__), array('jquery'), '1.0.0' );
			wp_register_style( 'new_style', plugins_url('/css/jquery.Jcrop.css', __FILE__), false, '1.0.0', 'all');
			wp_register_style( 'new_style1', plugins_url('/css/demos.css', __FILE__), false, '1.0.0', 'all');
			wp_register_style( 'new_style2', plugins_url('/css/main.css', __FILE__), false, '1.0.0', 'all');
			wp_enqueue_style( 'new_style' );
			wp_enqueue_style( 'new_style1' );
			wp_enqueue_style( 'new_style2' );	 	
		}		
	}
	
	function nicadv_Ajax_add_Advertisement()
	{
		// Create post object
		$my_post = array(
			'post_title'    => wp_strip_all_tags(esc_attr($_POST['name'])),
			'post_content'  => esc_attr($_POST['description']),
			'post_status'           => 'draft', 
			'post_type'             => 'advertiser',
			'post_author'           => get_current_user_id()
		);
		$AddedPost = wp_insert_post( $my_post );
		
		if($_POST['prices'] != '' && isset($_POST['prices']) && !empty($_POST['prices']))
		{
			$Prices	= serialize($_POST['prices']);
			update_post_meta($AddedPost, 'advprices', $Prices);
		}
		if($_POST['height'] != '' && $_POST['width'] != '' && $_POST['row'] != '' && $_POST['column'] != '')
		{
			$AdverData = serialize(array('heightcount' => $_POST['height'], 'widthcount' => $_POST['width'], 'rowcount' => $_POST['row'], 'columncount' => $_POST['column']));
			update_post_meta($AddedPost, 'advportions', $AdverData);
		}
		if(isset($_POST['form_item']) && $_POST['form_item'] != '')
		{
			update_post_meta($AddedPost, 'adv_iteam', $_POST['form_item']);
		}		
		
		if($AddedPost > 0 && $AddedPost != '')
		{
			$msg = __('Advertisement added successfully.','nicadvtext');
			echo '<center><span style="color:green; width:100%;">'.$msg.'</span></center>';
		}
		else
		{
			$msg = __('Error in adding Advertisement','nicadvtext');
			echo '<center><span style="width:100%; color:red;">'.$msg.'</span></center>';
		}
		exit;
	}
	
	function nicadv_add_js_css()
	{
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'custom', plugins_url('/js/custom.js', __FILE__), array('jquery'), '1.0.0' );
		wp_register_style( 'new_style', plugins_url('/css/jquery-ui-1.10.3.custom.css', __FILE__), false, '1.0.0', 'all');
		wp_enqueue_style( 'new_style' );	 	
	}
	
	function nicadv_add_advertise()
	{
		$this->nicadv_add_js_css();
		?>
         <?php
		if ( is_user_logged_in() ) 
		{
			echo '<input type="button" class="ui-button" value="Add Advertisement" id="AddAdvertise"/>';
		} 
		else 
		{
			echo '<input type="button" class="ui-button" value="Add Advertisement" id="notlogin"/>';
		}
		?>
                  
        <div id="dialog-not-loggin" title="Not Loggedin" style="display:none;">
        <p> <?php echo __('You are not logged in please login to site by click','nicadvtext'); ?>  <a href="<?php echo wp_login_url( $redirect ); ?>"><?php echo __('here','nicadvtext'); ?></a>.</p>
        </div>
         <style>
		#frontadv tr td
		{
			/*padding:5px;
			margin:5px;*/
			border:1px solid #ccc !important;
			padding:5px;
		}
		#feedback { font-size: 1.4em; }
		#frontadv tr td:hover { background: #FECA40; }
		.selected { background: #F39814; color: white; }
		#pricesdiv input { width: 29px; padding:2px; margin: 1px 0;}		
		</style>
         
        <div id="dialog-add-advertise" title="Add Advertisement" style="display:none;">
        <p class="validateTips"><?php echo __('All form fields are required.','nicadvtext'); ?></p>
        	<div style="width:100%" id="resp"></div>
            <div style="width:60%; float:left; margin-top:5px;">
                <form name="addadv" id="addadv">
                    <fieldset>
                    <label for="name"><?php echo __('Title','nicadvtext'); ?> :</label>
                    <input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" /><br />
                    <label for="description"><?php echo __('Description','nicadvtext'); ?> :</label>                    
                    <textarea name="description" id="description" class="textarea ui-widget-content ui-corner-all"></textarea><br />
                    <label for="description"><?php echo __('Item','nicadvtext'); ?> :</label>
                    <select id="form_item" name="form_item" class="text ui-widget-content ui-corner-all" >
                    <option value=""> --- Select --- </option>
                    <?php
					$ItemsData = get_posts(array('post_type'=>'iteam'));				
					foreach($ItemsData as $Item)
					{
						echo '<option value="'.$Item->ID.'">'.$Item->post_title.'</option>';
					}
					?>
                    </select><br />                    
                    <label for="Item"><?php echo __('Item Height X Width','nicadvtext'); ?> :</label><br />
                    <input type="text" name="heightcount" id="heightcount" style="width:100px" class="text ui-widget-content ui-corner-all" />&nbsp;X&nbsp;
                    <input type="text" name="widthcount" id="widthcount" style="width:100px" class="text ui-widget-content ui-corner-all" /><br />
                    <label for="Horizontal Partition"><?php echo __('Horizontal Partition','nicadvtext'); ?> :</label>
                    <input type="text" name="rowcount" id="rowcount" class="text ui-widget-content ui-corner-all" /><br />
                    <label for="Verticle Partition"><?php echo __('Verticle Partition','nicadvtext'); ?> :</label>
                    <input type="text" name="columncount" id="columncount" class="text ui-widget-content ui-corner-all" /><br />
                    <input type="button" class="ui-button" value="Generate" style="float:right;" id="createtable" />                  
                    <input type="hidden" name="site_name" id="site_name" value="<?php echo site_url(); ?>" />
                    </fieldset>
                </form>                
            </div>
            <div style="float:right; width:40%; margin-top:5px;">
            	<span id="box"></span>   
                <table width="150" id="pricesdiv"></table>        
            </div>
        </div>
        <?php		
		return;	
	}	
	
	function nic_custom_template($single) 
	{
		$this->nicadv_add_js_css();
		wp_enqueue_script( 'myjs', plugins_url('/js/jquery.form.js', __FILE__), array('jquery'), '0.0.1' );		
		global $wp_query, $post;

		/* Checks for single template by post type */
		if ($post->post_type == "advertiser")
		{
			$MyFile = plugin_dir_path(__FILE__).'advertisement_single.php';	
			if(file_exists($MyFile))
			{
				return $MyFile;
			}
			else
			{
				echo __("Custom Post type Template Missing", "nicadvtext");				
			}
		}
		return $single;
	}

	function nicadv_view_advertise_listing()
	{	
	?>
    		<?php    
			global $post;    
			$args = array( 'numberposts' => 5, 'post_type' => 'advertiser' );    
			$myposts = get_posts( $args );    
			foreach( $myposts as $post ) : setup_postdata($post); ?>
			<article id="post-<?php the_ID(); ?>" class="post-1 post type-post status-publish format-standard hentry category-uncategorized">

                <header class="entry-header">
                    <h1 class="entry-title">
                        <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentytwelve' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
                            <?php the_title(); ?>
                        </a>
                    </h1>
                    <?php if ( comments_open() ) : ?>
                    <div class="comments-link">
                        <?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'twentytwelve' ) . '</span>', __( '1 Reply', 'twentytwelve' ), __( '% Replies', 'twentytwelve' ) ); ?>
                    </div><!-- .comments-link -->
                    <?php endif; // comments_open() ?>
                </header><!-- .entry-header -->     
                        
                <div class="entry-content">
                    <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?>
                    <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
                </div><!-- .entry-content -->
                
                <footer class="entry-meta">
                    <?php twentytwelve_entry_meta(); ?>
                    <?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
                    <?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries. ?>
                        <div class="author-info">
                            <div class="author-avatar">
                                <?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentytwelve_author_bio_avatar_size', 68 ) ); ?>
                            </div><!-- .author-avatar -->
                            <div class="author-description">
                                <h2><?php printf( __( 'About %s', 'twentytwelve' ), get_the_author() ); ?></h2>
                                <p><?php the_author_meta( 'description' ); ?></p>
                                <div class="author-link">
                                    <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
                                        <?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentytwelve' ), get_the_author() ); ?>
                                    </a>
                                </div><!-- .author-link	-->
                            </div><!-- .author-description -->
                        </div><!-- .author-info -->
                    <?php endif; ?>
                </footer><!-- .entry-meta -->
        
            </article>
			<?php endforeach; ?>     
    <?php
	return;
	}

	function nicadv_save_admin_data()
	{		
		if($_POST)
		{
			global $post;
			$post_id = $post->ID;	
						
			if($_POST['action'] == 'editpost' && $_POST['post_type'] == 'sponser')
			{
				// if user is admin then change sponser status to draft because it's not assigned to any advertisement 
				// At assigning time sponser will be published 	
				
				if($_POST['itemimg'] != '' && isset($_POST['itemimg']))
				{
					update_post_meta($_POST['ID'], 'sponser_banner', $_POST['itemimg']);
				}	
				if($_POST['email'] != '' && isset($_POST['email']))
				{
					update_post_meta($_POST['ID'], 'sponser_email', $_POST['email']);
				}	
				if($_POST['contact'] != '' && isset($_POST['contact']))
				{
					update_post_meta($_POST['ID'], 'sponser_contact', $_POST['contact']);
				}				
				if($_POST['admin_sponser_adv'] != '' && isset($_POST['admin_sponser_adv']))
				{
					update_post_meta($_POST['ID'], 'sponser_advertiser', $_POST['admin_sponser_adv']);
				}
				if($_POST['admin_sponser_adv_part'] != '' && isset($_POST['admin_sponser_adv_part']))
				{
					update_post_meta($_POST['ID'], 'sponser_partition', $_POST['admin_sponser_adv_part']);
				}
			}
			
			if($_POST['action'] == 'editpost' && $_POST['post_type'] == 'advertiser')
			{
							
				if($_POST['price'] != '' && isset($_POST['price']))
				{
					$Prices	= serialize($_POST['price']);
					update_post_meta($post_id, 'advprices', $Prices);
				}
				if($_POST['heightcount'] != '' && $_POST['widthcount'] != '' && $_POST['rowcount'] != '' && $_POST['columncount'] != '')
				{
					$AdverData = serialize(array('heightcount' => $_POST['heightcount'], 'widthcount' => $_POST['widthcount'], 'rowcount' => $_POST['rowcount'], 'columncount' => $_POST['columncount']));
					update_post_meta($post_id, 'advportions', $AdverData);
				}
				if($_POST['iteam'] != '' && isset($_POST['iteam']) )
				{					
					update_post_meta($post_id, 'adv_iteam', $_POST['iteam']);
				}
				if($_POST['post_item_image'] != '' && isset($_POST['post_item_image']))
				{
					update_post_meta($_POST['ID'], 'post_item_image', $_POST['post_item_image']);
				}				
			}
		}	
		if($_POST['action'] == 'editpost' && $_POST['post_type'] == 'iteam')
		{
			
			if($_POST['x1'] != '' && $_POST['y1'] != '' && $_POST['x2'] != '' && $_POST['y2'] != '' && $_POST['w'] != '' && $_POST['h'] != '')
			{
				$partition = serialize(array('x1' => $_POST['x1'], 'y1' => $_POST['y1'], 'x2' => $_POST['x2'], 'y2' => $_POST['y2'], 'w' => $_POST['w'], 'h' => $_POST['h']));
				update_post_meta($_POST['ID'], 'partition', $partition);
			}	
			if($_POST['itemimg'] != '' && isset($_POST['itemimg']))
			{
				update_post_meta($_POST['ID'], 'itemimg', $_POST['itemimg']);
			}
		}
	}
	
	function nicadv_Add_admin_js_css()
	{
		wp_enqueue_script( 'mycustom', plugins_url('/js/custom.js', __FILE__), array('jquery'), '1.0.0' );	
	}
	
	function nicaddv_meta_box_add()
	{
		// meta box for advertiser
		add_meta_box( 'meta-item-advertiser', __('Advertisement Iteam', 'nicadvtext'), array($this,'nicadv_meta_box_item_advertiser'), 'advertiser', 'normal', 'high' ); 
		add_meta_box( 'advertisement-sponser', __('Sponsers', 'nicadvtext'), array($this,'nicadv_advertisment_sponser_meta_box'), 'advertiser', 'side', 'high' ); 		
		add_meta_box( 'meta-price', __('Advertisement Portions', 'nicadvtext'), array($this,'nic_meta_box'), 'advertiser', 'normal', 'high' ); 
		// meta box for iteam
		add_meta_box( 'meta-iteam', __('Advertisement Portions', 'nicadvtext'), array($this,'nicadv_iteam_meta_box'), 'iteam', 'normal', 'high' ); 
		// metea box for sponsers
		add_meta_box( 'meta-sponser', __('Sponser Information', 'nicadvtext'), array($this,'nicadv_sponser_meta_box'), 'sponser', 'normal', 'high' ); 
		add_meta_box( 'meta-sponser-details', __('Advertiser Information', 'nicadvtext'), array($this,'nicadv_sponser_meta_box_details'), 'sponser', 'side', 'high' ); 
	}
	
	function nicadv_sponser_meta_box_details()
	{
		global $post;	
		global $wpdb;
		
		$MyMetas	= get_post_meta($post->ID);
		$AdvertiserId = get_post_meta($post->ID,'sponser_advertiser',ARRAY_A);
		$PartitionId = get_post_meta($post->ID,'sponser_partition',ARRAY_A);	
		
		echo '<input type="hidden" name="curradvertiseId" id="curradvertiseId" value="'.trim($AdvertiserId).'" />';	
		echo '<input type="hidden" name="sponspartition" id="sponspartition" value="'.trim($PartitionId).'" />';	
		echo '<p>'.__('Advertiser','nicadvtext').' : ';
		
		$user_id = get_current_user_id();
		$user_id != 1 ? $AdvData = get_posts(array('post_type'=>'advertiser','post_status'=>'publish','author'=>$user_id)) : $AdvData = get_posts(array('post_type'=>'advertiser','post_status'=>'publish'));
		

		echo '<select id="admin_sponser_adv" name="admin_sponser_adv"><option value=""> ---'.__('Select','nicadvtext').'--- </option>';
		if(count($AdvData) > 0)
		{
			foreach($AdvData as $advert)	
			{
				$sel = '';
				if($advert->ID == $AdvertiserId)
				{
					$sel = 'selected="selected"';	
				}
				echo '<option '.$sel.' value="'.$advert->ID.'">'.$advert->post_title.'</option>';
			}
		}			
		echo '</select>';			
		echo '</p>';		
		
		if($PartitionId > 0)
		{
			echo '<p>'.__('Partition No','nicadvtext').' : <span id="partid"><input type="text" readonly="readonly" name="admin_sponser_adv_part" id="admin_sponser_adv_part" value="'.$PartitionId.'"></span><p>';
		}
		else
		{
			echo '<p>'.__('Partition No','nicadvtext').' : <span id="partid">
			<select name="admin_sponser_adv_part" id="admin_sponser_adv_part">
				<option> ---'.__('Select','nicadvtext').'--- </option>
			</select>
			</span><p>';
		}		
	}
	function nicadv_sponser_meta_box()
	{
		wp_enqueue_script('jquery');  	
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');	
		wp_enqueue_style('thickbox');	
		global $post;	
		
		$Contact	= get_post_meta($post->ID,'sponser_contact',ARRAY_A);
		$Email		= get_post_meta($post->ID,'sponser_email',ARRAY_A);
		$Image		= get_post_meta($post->ID,'sponser_banner',ARRAY_A);
					
		?>
		<script type="text/javascript">
		var j = jQuery.noConflict();	
		j(document).ready(function(){
			j("#nic_meta_cool_image").click(function() 
				{					
					window.send_to_editor = function(html) 
					{
						imgurl = j("img",html).attr("src"); 
						j("#itemimg").val(imgurl);
						j("#myimg").html('<img src="'+imgurl+'" id="target"  />');
						
						//alert(cool_obj("#view_cool_gallery_img").attr("src"));
						j("#target").attr("src", imgurl);
						//j("#view_cool_gallery_img").css("display", "block");
						tb_remove();					
						
					}
					
					//tb_show("", "media-upload.php?post_id=1&type=image&TB_iframe=true");
					tb_show('<?php echo __('Add Iteam Image','nicadvtext'); ?>', 'media-upload.php?type=image&TB_iframe=1');
					return false;
				});
				j("#postdiv, #postdivrich").prependTo("#custom_editor .inside");
		});
		</script>		        
        <table width="600" cellpadding="5" cellspacing="5" >
            <tr>
                <td><?php echo __('Email','nicadvtext'); ?> :</td>
                <td><input type="text" name="email" id="email"  value="<?php echo $Email; ?>" /></td>                    
            </tr>
            <tr>
                <td><?php echo __('Contact No','nicadvtext'); ?> :</td>
                <td><input type="text" name="contact" id="contact" value="<?php echo $Contact; ?>" /></td>                    
            </tr>
            <tr>
                <td valign="top"><?php echo __('Banner','nicadvtext'); ?> :</td>
                <td>				
                <input type="hidden" name="itemimg" class="itemimg" id="itemimg" value="<?php if($Itemimg != ''){ echo $Itemimg; }?>" />
                <input type="hidden" name="sitename" id="sitename" value="<?php echo site_url(); ?>" />
                <input type="button" name="nic_meta_cool_image" id="nic_meta_cool_image" value="Add Media File" title="Click here.." class="button button-primary button-large"/>
                <?php if($Image != ''){ ?>
                	<img src="<?php echo $Image; ?>" id="target" style="float:right;" /> 
				<?php } else { ?> 
                	<img src=""  style="float:right;" id="target" />
				<?php } ?> 
                </td>                    
            </tr>
        </table>
        <?php
	}
	function nicadv_advertisment_sponser_meta_box()
	{
		global $post;
		global $wpdb;		
				
		?>
        <span style="color:red; font-weight:bold;" id="spons_resp"></span>
        <table width="260">
        <caption><b><?php echo __('Booked Partition','nicadvtext'); ?></b></caption>
        	<tr>
            	<th width="230" align="left"><?php echo __('Name','nicadvtext'); ?></th>
                <th width="30"><?php echo __('Partition','nicadvtext'); ?></th>
            </tr>
        <?php
						
		$SponserData = $wpdb->get_results("SELECT p.post_title,p.ID FROM `wp_posts` AS p WHERE p.`post_type` = 'sponser' AND p.`post_parent` = '".$post->ID."' AND p.`post_status` = 'publish'");
		if(count($SponserData) > 0)
		{
			$Sponsers = array();
			foreach($SponserData as $sponser)
			{
				$sponser_part = get_post_meta($sponser->ID,'sponser_partition',ARRAY_A);
				echo '<tr>
					<td width="230" align="left">'.$sponser->post_title.'</td>
					<td width="30" align="center">'.$sponser_part.'</td>
				</tr>';
				$Sponsers[] = 	$sponser->ID;
			}
		}
		else
		{
			echo '<tr>
					<td colspan="2" align="center" style="color:red;">'.__('All Partition Available','nicadvtext').' </td>
				</tr>';
		}
		echo "</table>";			
		?>
        <table width="260">
        <caption><b><?php echo __('Available Sponsers','nicadvtext'); ?><b></caption>
        <tr>
        	<th>&nbsp;</th>
            <th align="left"><?php echo __('Name','nicadvtext'); ?></th>
            <th align="center">	<?php echo __('Partition','nicadvtext'); ?></th>
        </tr>
        <?php
		$AdvData = get_posts(array('numberposts' => -1,'post_type' => 'sponser','post_status' => array('draft'), 'orderby'=>'title' , 'order' => 'ASC'  ));		
		if(count($AdvData) > 0)
		{
			$Scnt=1;
			foreach($AdvData as $Advertisement)
			{
				$check = '';
				if(in_array($Advertisement->ID,$Sponsers))
				{
					$check = 'checked="checked"';
				}
				echo '<tr><td width="5"><input type="checkbox" id="adv_sposer" name="sposer'.$Scnt.'"  lang="str'.$Scnt.'"  '.$check.'  value="'.$Advertisement->ID.'" /></td><td>'.$Advertisement->post_title.'</td><td width="10"><input type="text" name="" id="str'.$Scnt.'" style="width:40px;" maxlength="3" ></td></tr><br />';
				$Scnt++;
			}
		}
		else
		{
			echo '<tr><td colspan="3" align="center" style="color:red;">'.__('No Sponsers In Draft. Please add sponser first.','nicadvtext').'</td></tr>';	
		}
		echo "</table>";	
		?>
        
        <style>
		#basicTable tr td
		{
			border:1px solid #ccc !important;
			padding:3px;
		}
		#feedback { font-size: 1.4em; }
		#basicTable tr td:hover { background: #FECA40; }
		.selected { background: #F39814; color: white; }
		.reserve { background: #1C94C4; color: white;} 
		.err { background:red !important; color:#000 !important; }
		</style>
        <center>
        <br />
        <table cellpadding="5" cellspacing="5" style="margin:0px 0px; float:none;" align="center">
        <caption><b>Partition</b></caption>
            <tr>
                <td valign="middle">&nbsp;<?php echo __('Available','nicadvtext'); ?>&nbsp;  <span style="background:#F8F8F8; height:15px; width:15px; float:left; border:1px solid #ccc;">&nbsp;</span></td>
                <td valign="middle">&nbsp;<?php echo __('Reserved','nicadvtext'); ?>&nbsp;  <span style="background:#1C94C4; height:15px; width:15px; float:left; margin-left:5px; border:1px solid #ccc;">&nbsp;</span></td>
            </tr>        
        </table>
        </center>
		 <table id="basicTable" cellspacing="5" cellpadding="5" width="150" height="150" border="1" align="center" >
                <?php
				$Portiondata = get_post_meta($post->ID,'advportions');
				$Portiondata = unserialize($Portiondata[0]);
				//print_r($Portiondata);	
				//echo "<br>";
				$PortionPrices = get_post_meta($post->ID,'advprices');
				$PortionPrices = unserialize($PortionPrices[0]);
				$args = array(
				'numberposts'     => -1,
				'post_type'       => 'sponser',
				'post_parent'     => $post->ID
				);
				$Advert_array = get_posts( $args );
			
				foreach($Advert_array as $Advertisement)
				{
					$AdvertId = $Advertisement->ID;
					if(isset($AdvertId) && $AdvertId != '')
					{
						$MyMetas = get_post_meta($AdvertId);
						$partition_Arr[] = $MyMetas['sponser_partition'][0];
					}
				}
				
                if(!empty($Portiondata))
                {
                    $cnt = 1;
                    $inc = 0;
                    for ($i = 0; $i < $Portiondata['rowcount']; $i++) 
                    {
                        echo '<tr calss="class1 class2 class3">';
                        for ($j = 0; $j < $Portiondata['columncount']; $j++) 
                        {
							$style = '';							
							$reserve = '';
							if(!empty($partition_Arr) && in_array($cnt, $partition_Arr))
							{
								$style = 'reserve';
							}
                            echo '<td align="center"  class="ui-state-default '.$style.'" lang="'.$cnt.'" style="cursor:pointer;">'.$cnt.'</td>';
                            $cnt++;
                            $inc++;
                        }
                        echo '</tr>';					
                    }
                }
                ?>
            </table>
            <?php		
	}
	
	function nicadv_meta_box_item_advertiser()
	{
		global $post;
		$myIteams = get_posts(array('post_type'=>'iteam'));
		$PostIteam = get_post_meta($post->ID,'adv_iteam',ARRAY_A);		
		?>
			<table>
                <tr>
                	<td colspan="2" align="left" style="color:red; font-weight:bold;" id="itemerr"></td>
                </tr>
            	<tr>
                	<td><?php echo __('Advertisement Iteam','nicadvtext'); ?> : </td>
                    <td>
                    	<select name="iteam" id="iteam">
                        	<option value="">---<?php echo __('Select','nicadvtext'); ?>---</option>
                            <?php
							foreach($myIteams as $Iteam)
							{
								if($PostIteam == $Iteam->ID)
								{
									$sel = 'selected="selected"';	
								}
								else
								{
									$sel = '';	
								}
								echo '<option value="'.$Iteam->ID.'" '.$sel.' >'.$Iteam->post_title.'</option>';
							}
							?>
                        </select>
                    </td>
                </tr>
            </table>
        <?php
	}
	
	function nicadv_iteam_meta_box()
	{	
		global $post;
	
		$Iteampartitions = get_post_meta($post->ID,'partition',ARRAY_A);
		$Iteampartitions = unserialize($Iteampartitions);
		$Itemimg = get_post_meta($post->ID,'itemimg',ARRAY_A);		
		
		wp_enqueue_script('jquery');  	
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');	
		wp_enqueue_style('thickbox');			
	  	
		global $wpdb;
		$AdvData = $wpdb->get_results("SELECT DISTINCT p.`post_title` FROM`".$wpdb->prefix."posts`AS p,`".$wpdb->prefix."postmeta` AS m WHERE p.`ID`= m.`post_id` AND m.`meta_key` = 'adv_iteam' AND p.`post_status` = 'publish' AND m.`meta_value` = ".$post->ID."",ARRAY_A);
			
		?>
		<script type="text/javascript">
		var j = jQuery.noConflict();		
		// Simple event handler, called from onChange and onSelect
		// event handlers, as per the Jcrop invocation above
		
		  j(function(j){

				var jcrop_api;
			
				j('#target').Jcrop({
				  onChange:   showCoords,
				  onSelect:   showCoords,
				  onRelease:  clearCoords
				},function(){
				  jcrop_api = this;
				});
			
			<?php if($Iteampartitions['x1'] != '' ) { ?>
			jcrop_api.animateTo([<?php echo $Iteampartitions['x1']; ?>,<?php echo $Iteampartitions['y1']; ?>,<?php echo $Iteampartitions['x2']; ?>,<?php echo $Iteampartitions['y2']; ?>]);
			<?php } ?>
						
				j('#coords').on('change','input',function(e){
				  var x1 = j('#x1').val(),
					  x2 = j('#x2').val(),
					  y1 = j('#y1').val(),
					  y2 = j('#y2').val();
				  jcrop_api.setSelect([x1,y1,x2,y2]);
				});
				
				<?php
				// disable edit when advertisement have item
				if(count($AdvData) > 0 )
				{
				?>
				jcrop_api.disable();
				<?php } ?>
			
			  });
						
		function showCoords(c)
		{
			j('#x1').val(c.x);
			j('#y1').val(c.y);
			j('#x2').val(c.x2);
			j('#y2').val(c.y2);
			j('#w').val(c.w);
			j('#h').val(c.h);
		};
		
		function clearCoords()
		{
			j('#coords input').val('');
		};
		j(document).ready(function(){
			j("#nic_meta_cool_image").click(function() 
				{					
					window.send_to_editor = function(html) 
					{
						imgurl = j("img",html).attr("src"); 
						j("#itemimg").val(imgurl);
						j("#myimg").html('<img src="'+imgurl+'" id="target"  />');
						
						//alert(cool_obj("#view_cool_gallery_img").attr("src"));
						j("#target").attr("src", imgurl);
						//j("#view_cool_gallery_img").css("display", "block");
						tb_remove();
						
						var jcrop_api;
			
						j('#target').Jcrop({
							onChange:   showCoords,
							onSelect:   showCoords,
							onRelease:  clearCoords
							},function(){
							jcrop_api = this;
							
						});
						 
						jcrop_api.animateTo([<?php echo $Iteampartitions['x1']; ?>,<?php echo $Iteampartitions['y1']; ?>,<?php echo $Iteampartitions['x2']; ?>,<?php echo $Iteampartitions['y2']; ?>]);
						
						j('#coords').on('change','input',function(e){
							var x1 = j('#x1').val(),
							x2 = j('#x2').val(),
							y1 = j('#y1').val(),
							y2 = j('#y2').val();
							jcrop_api.setSelect([x1,y1,x2,y2]);
						});
			
					}
					
					//tb_show("", "media-upload.php?post_id=1&type=image&TB_iframe=true");
					tb_show('Add Iteam Image', 'media-upload.php?type=image&TB_iframe=1');
					return false;
				});
				j("#postdiv, #postdivrich").prependTo("#custom_editor .inside");
				
			});
		</script>
		
        <div class="container">
            <div class="row">
                <div class="span12">
                    <div class="jc-demo-box">
                        <!-- This is the image we're attaching Jcrop to -->
                        <span id="myimg">
						<?php if($Itemimg != ''){ ?><img src="<?php echo $Itemimg; ?>" id="target" /> <?php } else { ?> <img src="" id="target" /><?php } ?> 
                        </span>
                        <!-- This is the form that our event handler fills -->
                        <form id="coords" class="coords" onsubmit="return false;" action="http://example.com/post.php">                
                            <div class="inline-labels">
                                <label><!--X1--> <input type="hidden" size="4" id="x1" name="x1" value="<?php echo $Iteampartitions['x1']; ?>" /></label>
                                <label><!--Y1--> <input type="hidden" size="4" id="y1" name="y1" value="<?php echo $Iteampartitions['y1']; ?>" /></label>
                                <label><!--X2--> <input type="hidden" size="4" id="x2" name="x2" value="<?php echo $Iteampartitions['x2']; ?>" /></label>
                                <label><!--Y2--> <input type="hidden" size="4" id="y2" name="y2" value="<?php echo $Iteampartitions['y2']; ?>" /></label>
                                <label><!--W--> <input type="hidden" size="4" id="w" name="w" value="<?php echo $Iteampartitions['w']; ?>" /></label>
                                <label><!--H--> <input type="hidden" size="4" id="h" name="h" value="<?php echo $Iteampartitions['h']; ?>" /></label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <input type="hidden" name="itemimg" class="itemimg" id="itemimg" value="<?php if($Itemimg != ''){ echo $Itemimg; }?>" />
            <?php
            if(count($AdvData) <= 0)
			{
				?>
            <input type="button" name="nic_meta_cool_image" id="nic_meta_cool_image" value="Add Media File" title="Click here.." class="button button-primary button-large"/>

            <?php
			}
			else
			{
				echo "<span style='color:red;'>Iteam is assigned in Advertisement. Please Detele or trash it first.</span>";
			}
			?>
            <span> <!--(180px X 66px)--></span> 
        </div>   
      <?php
	}
	
	function nic_meta_box()  	
	{  	
		global $post;		
		$Portiondata = get_post_meta($post->ID,'advportions');
		$Portiondata = unserialize($Portiondata[0]);
		$PortionPrices = get_post_meta($post->ID,'advprices');
		$PortionPrices = unserialize($PortionPrices[0]);
		$post_item_image = get_post_meta($post->ID,'post_item_image',ARRAY_A);
		$PostIteam = get_post_meta($post->ID,'adv_iteam',ARRAY_A);
		$ItaemMeta = get_post_meta($PostIteam,'itemimg',ARRAY_A);
		$Meta = get_post_meta($PostIteam,'partition',ARRAY_A);		
		?>
        
        <div style=" width:685px; height:250px;">
        	<span style="float: left; height: 150px; width:400px;">
            <table width="400" >
            <tr>
                <td>
                	<label for="meta_price"><?php echo __('Iteam', 'nicadvtext'); ?><?php echo __('Height', 'nicadvtext'); ?></label>X 
                    <label for="meta_price"><?php echo __('Width', 'nicadvtext'); ?></label>
                </td>
                <td><input type="text" id="heightcount" name="heightcount" style="width:50px;" value="<?php echo $Portiondata['heightcount']; ?>" />" X  <input type="text" id="widthcount" name="widthcount" style="width:50px;" value="<?php echo $Portiondata['widthcount']; ?>" />"</td>
            </tr>	
            <tr>
                <td><label for="meta_price"> <?php echo __('Horizontal Partition', 'nicadvtext'); ?></label>  </td>
                <td><input type="text" id="rowcount" name="rowcount" value="<?php echo $Portiondata['rowcount']; ?>" /></td>
            </tr>
            <tr>
                <td><label for="meta_price"> <?php echo __('Verticle Partition', 'nicadvtext'); ?></label>  </td>
                <td><input type="text" id="columncount" name="columncount" value="<?php echo $Portiondata['columncount']; ?>" />  </td>
            </tr>
            <tr>
            	<td colspan="2">
                    <input type="button" id="createtable" name="createtable" value="Create Table" />                    
                    <input type="hidden" name="iteamimg" id="iteamimg" value="<?php echo $ItaemMeta; ?>" />
                    <input  type="hidden" name="postId" id="postId" value="<?php echo $post->ID; ?>" />
                    <input type="hidden" name="site_name" id="site_name" value="<?php echo site_url(); ?>"  />
                    <input type="hidden" name="partition_meta" id="partition_meta" value="<?php echo htmlspecialchars($Meta); ?>"  />
                    <input type="hidden" name="post_item_image" id="post_item_image" value="<?php echo $post_item_image; ?>" />
                </td>
            </tr>
            </table>
            </span>
<!--            <span id="mainsquare" style="height: 150px; width: 150px; position: absolute; float: right; border:1px solid #000;">            
            </span>-->
            <span id="box" style="height: 150px; width: 150px; position: absolute; float: right; ">
            <table id="basicTable" >
            <?php
            if(!empty($Portiondata))
            {
            	$cnt = 1;
				for ($i = 0; $i < $Portiondata['rowcount']; $i++) 
				{
					echo '<tr calss="class1 class2 class3">';
					for ($j = 0; $j < $Portiondata['columncount']; $j++) 
					{
						echo '<td align="center">&nbsp;'.$cnt.'&nbsp;</td>';
						$cnt++;
					}
					echo '</tr>';					
				}
            }
            ?>
            </table>
            
            </span>
            <span style="float:right; clear:both; margin-right:30px;" id="newimage" ><img src="<?php echo $post_item_image; ?>" /></span>
            <span style="float:right; clear:both; margin-right:30px;" id="newimage1" ></span>
           <!-- <span style="float:left; vertical-align:middle; margin-top:75px;"><b><span id="hoz">4</span>"</b>&nbsp;</span>
            <span style="float:right; vertical-align:middle; clear:both; margin-right:150px;"><b><span id="ver">4</span>"</b>&nbsp;</span>-->
        </div>
       
        <div class="inside">
            <table width="400" id="pricesdiv">
            <?php
            if(!empty($PortionPrices))
			{
				$counter = 1;
				foreach($PortionPrices as $Price) 	
				{
					echo '<tr><td>'.__('Prices for partition','nicadvtext').' '.$counter.'</td><td><input type="text" name="price[]" id="price" value="'.$Price.'"></td></tr>';
					$counter++;
				}
			}
			?>
            </table>
        </div>
       
		<style>
			#basicTable tr td
			{
				border:1px solid #ccc !important;
			}
        </style>
        <script>
		var s = jQuery.noConflict();
		s(document).ready(function() 
		{	
			/*On change of item change item image url and patitions which are already defined in item */
			s('#iteam').change(function()
			{
				var site_name = s('#site_name').val();
				var myurl = site_name+'/wp-admin/admin-ajax.php';
				var postId = s(this).val();
				s.ajax({
					type: 'POST',
					url: myurl,
					async: false,
					data: { action: 'AdminChangeImg', postId : postId },
					success: function(data)
					{
						var res = data.split('|');
						s('#partition_meta').val(res[0]);
						s('#iteamimg').val(res[1]);
						return false;
					}
				});	
			});
			
			/* Create image for advertisement in admin panel*/
			s('#createtable').click(function()
			{
				if(s('#iteam').val().length <= 0)
				{
					s('#itemerr').html('Please select Item first');
					return false;
				}
				else
				{
					s('#itemerr').html('');
				}
				
				var site_name = s('#site_name').val();
				var myurl = site_name+'/wp-admin/admin-ajax.php';
				var postId = s('#postId').val();
				var iteamimg = s('#iteamimg').val();			
				var partition = s('#partition_meta').val();			
				var rows = s('#rowcount').val();			
				var columns = s('#columncount').val();	
				s.ajax({
					type: 'POST',
					url: myurl,
					async: false,
					data: { action: 'CreateImg', postId : postId, iteamimg : iteamimg, partition : partition, rows : rows, columns : columns },
					success: function(data)
					{
						s('#newimage').html('');
						s('#post_item_image').val('');
						s('#newimage').hide('slow', function() {							 
							s('#newimage1').html('<img src="'+data+'" />');
							s('#post_item_image').val(data);
						});
					}
				});	
				
					
				mytable = s('<table id="basicTable" ></table>');
				var myheight =  s('#heightcount').val()*10;
				var mywidth = s('#widthcount').val()*10;
				mytable.css('height',myheight+'px !important');
				mytable.css('width',mywidth+'px !important');							
				var rows = new Number(s("#rowcount").val());
				var cols = new Number(s("#columncount").val());
				var prices = '';
				var tr = [];
				var counter = 1;
				for (var i = 0; i < rows; i++) 
				{
					var row = s('<tr></tr>').attr({ class: ["class1", "class2", "class3"].join(' ') }).appendTo(mytable);
					for (var j = 0; j < cols; j++) 
					{
						s('<td align="center">&nbsp;'+counter+'&nbsp;</td>').appendTo(row);
						prices += '<tr><td><?php echo __('Prices for partition','nicadvtext'); ?> '+counter+'</td><td><input type="text" name="price[]" id="price"></td></tr>';
						counter++;
					}
					
				}
				
				s("#pricesdiv").html(prices);
				console.log("TTTTT:"+mytable.html());
				s("#box").html('');
				mytable.appendTo("#box");					
			});
		});
		</script>     
        <?
	}	
	
	function nicadv_taxonomy_add_new_meta_field() 
	{
		// this will add the custom meta field to the add new term page
		?>
		<div class="form-field">
			<label for="term_meta[custom_term_meta]"><?php _e( 'Email', 'nicadvtext' ); ?></label>
			<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">
			<p class="description"><?php _e( 'Enter a value for this field','nicadvtext' ); ?></p>
		</div>
        <div class="form-field">
			<label for="term_meta[custom_term_meta]"><?php _e( 'Contact No', 'nicadvtext' ); ?></label>
			<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">
			<p class="description"><?php _e( 'Enter a value for this field','nicadvtext' ); ?></p>
		</div>
        <div class="form-field">
			<label for="term_meta[custom_term_meta]"><?php _e( 'Banner', 'nicadvtext' ); ?></label>
			<input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">
			<p class="description"><?php _e( 'Enter a value for this field','nicadvtext' ); ?></p>
		</div>
	<?php
	}	
		
	function nicadv_custom_init() 
	{
		$labels = array(
			'name' => __("Advertisement", "nicadvtext"),
			'singular_name' => __("Advertisement", "nicadvtext"),
			'add_new' => __("Add Advertisement", "nicadvtext"),
			'add_new_item' => __("Add New Advertisement", "nicadvtext"),
			'edit_item' => __("Edit Advertisement", "nicadvtext"),
			'new_item' => __("New Advertisement", "nicadvtext"),
			'all_items' => __("All Advertisement", "nicadvtext"),
			'view_item' => __("View Advertisement", "nicadvtext"),
			'search_items' => __("Search Advertisement", "nicadvtext"),
			'not_found' =>  __("No Advertiser found", "nicadvtext"),
			'not_found_in_trash' => __("No Advertisement found in Trash", "nicadvtext"), 
			'parent_item_colon' => '',
			'menu_name' => __("Advertisement", "nicadvtext")
		);
	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'show_in_admin_bar' => true,
			'rewrite' => array( 'slug' => 'advertiser', 'with_front' => true ),	
			'capability_type' => array("sponser", "sponser"),
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'menu_icon' => plugins_url('/images/icon.png',__FILE__),
			'supports' => array( 'title', 'editor', 'author', 'excerpt', 'comments' ),
			'show_admin_column' => true
		); 
		
		register_post_type( 'advertiser', $args );
		flush_rewrite_rules();

		$txtlabels = array(
			'name' => __("Sponser", "nicadvtext"),
			'singular_name' => __("Sponser", "nicadvtext"),
			'add_new' => __("Add Sponser", "nicadvtext"),
			'add_new_item' => __("Add New Sponser", "nicadvtext"),
			'edit_item' => __("Edit Sponser", "nicadvtext"),
			'new_item' => __("New Sponser", "nicadvtext"),
			'all_items' => __("All Sponser", "nicadvtext"),
			'view_item' => __("View Sponser", "nicadvtext"),
			'search_items' => __("Search Sponser", "nicadvtext"),
			'not_found' =>  __("No Sponser found", "nicadvtext"),
			'not_found_in_trash' => __("No Sponser found in Trash", "nicadvtext"), 
			'parent_item_colon' => '',
			'menu_name' => __("Sponser", "nicadvtext")
		);
		
		$args = array(
			'labels' => $txtlabels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'show_in_admin_bar' => true,
			'rewrite' => array( 'slug' => 'sponser', 'with_front' => true ),	
			'capability_type' => array("sponser", "sponser"),
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'menu_icon' => plugins_url('/images/icon.png',__FILE__),
			'supports' => array( 'title', 'editor'),
			'show_admin_column' => true
		); 
		
		register_post_type( 'sponser', $args ); 
		flush_rewrite_rules();	
		
		$Iteamlabels = array(
			'name' => __("Iteams", "nicadvtext"),
			'singular_name' => __("Iteams", "nicadvtext"),
			'add_new' => __("Add Iteam", "nicadvtext"),
			'add_new_item' => __("Add New Iteam", "nicadvtext"),
			'edit_item' => __("Edit Iteam", "nicadvtext"),
			'new_item' => __("New Iteam", "nicadvtext"),
			'all_items' => __("All Iteam", "nicadvtext"),
			'view_item' => __("View Iteam", "nicadvtext"),
			'search_items' => __("Search Iteam", "nicadvtext"),
			'not_found' =>  __("No Iteam found", "nicadvtext"),
			'not_found_in_trash' => __("No Iteam found in Trash", "nicadvtext"), 
			'parent_item_colon' => '',
			'menu_name' => __("Iteams", "nicadvtext")
		);
		
		$Iteamargs = array(
			'labels' => $Iteamlabels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'show_in_admin_bar' => true,
			'rewrite' => array( 'slug' => 'iteam', 'with_front' => true ),	
			'capability_type' => 'page',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'menu_icon' => plugins_url('/images/icon.png',__FILE__),
			'supports' => array( 'title' ),
			'show_admin_column' => true
		); 
		
		register_post_type( 'iteam', $Iteamargs );
	 	flush_rewrite_rules();
		
		$caps = array(
			'read_sponser',
			'read_private_sponser',
			'edit_sponser',
			'edit_private_sponser',
			'edit_published_sponser',
			'edit_others_sponser',
			'publish_sponser',
			'delete_sponser',
			'delete_private_sponser',
			'delete_published_sponser',
			'delete_others_sponser',
			
			'read_advertiser',
			'read_private_advertiser',
			'edit_advertiser',
			'edit_private_advertiser',
			'edit_published_advertiser',
			'edit_others_advertiser',
			'publish_advertiser',
			'delete_advertiser',
			'delete_private_advertiser',
			'delete_published_advertiser',
			'delete_others_advertiser',
			
		);
		
		$roles = array(
			get_role( 'administrator' ),
			get_role( 'editor' ),
			get_role( 'subscriber' ),
		);
		
		foreach ($roles as $role) 
		{
			foreach ($caps as $cap) 
			{
				$role->add_cap( $cap );
			}
		}
	
	}
	
}
	
add_action("init", "register_nicadv_bag_advertisement_plugin");
function register_nicadv_bag_advertisement_plugin() 
{
	global $nicadv,$post;
	$nicadv = new iNIC_Bag_Adv();
	$nicadv->nicadv_custom_init();
}
