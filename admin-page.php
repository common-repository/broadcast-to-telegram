<?php


require_once( 'notifcaster.class.php' );


function brtg_set_header(){
?>
    <div class="wrap">  
      <table class="title" width="100%">  
        <tbody>  
          <tr>    
            <td  width="200px">      
              <img src="<?php echo plugins_url( '/images/broadcast_telegram.jpg' , __FILE__ ); ?>" title="<?php _e('Notify to Telegram', 'broadcast-telegram'); ?>" alt="<?php _e('Notify to Telegram', 'broadcast-telegram'); ?>">      
    		    </td>    
        		<td>           
              <h1 style="color: #0000ff;"><strong><?php _e('Broadcast to Telegram', 'broadcast-telegram'); ?></strong></h1>
              <h2>            
                <span style="font-size: small;">
                  <?php _e('Post notification system to one or more channels via Telegram Bot', 'broadcast-telegram'); ?>
                  <br />
                  <em><?php _e('by', 'broadcast-telegram'); ?> Enzo Costantini (SoftCos)</em>
                </span>
                <br>      
        		  </h2>    
        		</td>  
          </tr>  
        </tbody>  
      </table>  
      <hr>     
<?php
}

function brtg_update_options_form()
{  
  
  $msg = '';
  
  if ( count($_POST) > 0) {
    
    if (!empty($_POST['post_type_name'])) {
    
			$impVal = implode(',', $_POST['post_type_name']);
			update_option ('brtg_post_type', $impVal, 'no');
			update_option('brtg_bot_token', $_POST['brtg_bot_token'], 'no');
      update_option('brtg_telegram_channels', $_POST['brtg_telegram_channels'], 'no');
      update_option('brtg_msg_web_preview', isset($_POST['brtg_msg_web_preview']), 'no');
      update_option('brtg_msg_tpl', $_POST['brtg_msg_tpl'], 'no');
      update_option('brtg_auto_send', isset($_POST['brtg_auto_send']), 'no');
      update_option('brtg_delete_options', isset($_POST['brtg_delete_options']), 'no');
      
      $msg = '<div id="message" class="updated below-h2"><p>' . __('Settings properly saved', 'broadcast-telegram') . '.</p></div>';
    }
    else {			
      update_option ( 'brtg_post_type', 'post', 'no');
			$msg = '<div id="message" class="error below-h2"><p>' . __('You have to select at least one document type', 'broadcast-telegram') . '!</p></div>';
		}

  } 
  
  brtg_set_header();  
  
?>
    <form method="post" enctype="multipart/form-data" style="min-width: 700px; margin: 10px 10px 2px 0px; padding: 1px 12px;" target="_self">
    <?php echo $msg; ?>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row"><label for="post_type_name"><?php _e('Post type', 'broadcast-telegram'); ?>:</label></th>
            <td>
<?php 
  $args = array(
			'public'   => true,
			'_builtin' => false
		);
	$post_types = get_post_types($args);
	array_push($post_types, 'post');
    foreach($post_types as $type){ 
		$obj = get_post_type_object( $type );
		$post_types_name = $obj->labels->singular_name; 
		
		if(get_option('brtg_post_type') != ''){
			$postType_title = get_option('brtg_post_type');
			$postType_chkd = explode(',',$postType_title);
			$chd = '';
			if(in_array($type, $postType_chkd)){
				 $chd = 'checked';
			}
		}		
?>
              <div class="type_chkbox">
                <input type="checkbox" name="post_type_name[]" value="<?php echo $type; ?>" id="<?php echo $type; ?>" <?php echo $chd; ?> class="chkBox" />
                <label for="<?php echo $type; ?>"><?php echo '  ' . $post_types_name; ?></label> 
              </div>
<?php } ?>
              <hr />
            </td>
			    <tr valign="top">
				    <th scope="row"><label for="brtg_bot_token"><?php _e('Telegram Bot token', 'broadcast-telegram'); ?>:</label></th>
				    <td>
					    <input type="text" style="width: 600px;" name="brtg_bot_token" id="brtg_bot_token" value="<?php echo( get_option('brtg_bot_token') ); ?>" /> 
            </td>
			    </tr>
          <tr valign="top">
					  <th scope="row"><label for="brtg_telegram_channels"><?php _e('Telegram channels list', 'broadcast-telegram'); ?>:</label><p><em><?php _e('format', 'broadcast-telegram'); ?>:</em><br /><?php _e('@idchannel1+@idchannel2+...', 'broadcast-telegram'); ?></p></th>
            <td>
							<textarea name="brtg_telegram_channels" id="brtg_telegram_channels" style="width:600px; height:100px;"><?php echo( get_option('brtg_telegram_channels') ); ?></textarea>
						</td>
          </tr>
          <tr valign="top">                  
						<th scope="row"><label for="brtg_auto_send"><?php _e('Autocheck checkbox to send new posts', 'broadcast-telegram'); ?>:</label></th>
            <td>
              <input type="checkbox" name="brtg_auto_send" value="brtg_auto_send" id="brtg_auto_send" <?php checked(get_option('brtg_auto_send')); ?> class="chkBox" />
            </td>
          </tr>
          <tr valign="top">
						<th scope="row"><label for="brtg_msg_web_preview"><?php _e('Disable link web preview', 'broadcast-telegram'); ?>:</label></th>
            <td>
              <input type="checkbox" name="brtg_msg_web_preview" value="brtg_msg_web_preview" id="brtg_msg_web_preview" <?php checked(get_option('brtg_msg_web_preview')); ?> class="chkBox" />
            </td>
          </tr>
          <tr valign="top">
						<th scope="row"><label for="brtg_msg_tpl"><?php _e('Message template', 'broadcast-telegram'); ?>:</label>
              <p><em><?php _e('placeholders', 'broadcast-telegram'); ?>:</em><br />
              <?php _e(TITLE, 'broadcast-telegram'); ?><br />
              <?php _e(FULLURL, 'broadcast-telegram'); ?><br />
              <?php _e(SHORTURL, 'broadcast-telegram'); ?><br />
              <?php _e(EXCERPT, 'broadcast-telegram'); ?><br />
              <?php _e(TAGS, 'broadcast-telegram'); ?><br />
              <?php _e(CATEGORIES, 'broadcast-telegram'); ?></p>
              <p><em><?php _e('supported markdowns', 'broadcast-telegram'); ?>:</em><br />*<span style="font-weight: bold !important;"><?php _e('bold', 'broadcast-telegram'); ?></span>*<br />_<span style="font-weight: normal; font-style: italic !important; "><?php _e('italic', 'broadcast-telegram'); ?></span>_</p>
            </th>
            <td>
							<textarea name="brtg_msg_tpl" id="brtg_msg_tpl" style="width:600px; height:250px;"><?php echo( get_option('brtg_msg_tpl') ); ?></textarea>
						</td>
          </tr>
          <tr valign="top">
						<th scope="row"><label for="brtg_delete_options"><?php _e('Delete options on uninstall', 'broadcast-telegram'); ?>:</label></th>
            <td>
              <input type="checkbox" name="brtg_delete_options" value="brtg_delete_options" id="brtg_delete_options" <?php checked(get_option('brtg_delete_options')); ?> class="chkBox" />
            </td>
          </tr>
        </tbody>
      </table>
      <p><input class="button-primary" name="Submit" type="submit" value="<?php _e('Save changes', 'broadcast-telegram'); ?>"></p>
    </form>
    </div>
<?php
}

function brtg_add_options_page()
{
  add_options_page( __('Broadcast to Telegram (settings)', 'broadcast-telegram'), __('Broadcast to Telegram', 'broadcast-telegram'), 'administrator', 'brtg_options_page', 'brtg_update_options_form');
}
 
add_action('admin_menu', 'brtg_add_options_page');



?>