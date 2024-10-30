<?php
/*
Plugin Name: Broadcast to Telegram
Description: Post notification system on one or more channels via Telegram Bot. The plugin allows to select the document type and Telegram channels.
Author: Enzo Costantini (SoftCos)
Version: 1.2.0
Author URI: www.softcos.eu
*/

if ( !function_exists( 'add_action' ) ) {
	echo( 'Error! This is a WordPress plugin...');
	exit;
}

load_plugin_textdomain( 'broadcast-telegram', false, basename( dirname( __FILE__ ) ) . '/lang/' );

define("TITLE",      '{' . __('TITLE', 'broadcast-telegram') . '}');
define("FULLURL",    '{' . __('FULLURL', 'broadcast-telegram') . '}');
define("SHORTURL",   '{' . __('SHORTURL', 'broadcast-telegram') . '}');
define("EXCERPT",    '{' . __('EXCERPT', 'broadcast-telegram') . '}');
define("TAGS",       '{' . __('TAGS', 'broadcast-telegram') . '}');
define("CATEGORIES", '{' . __('CATEGORIES', 'broadcast-telegram') . '}');
define("MSG_TPL",  TITLE . "\n \n" . FULLURL);


require_once( 'notifcaster.class.php' );

function brtg_isActive() {
	if ( '' == get_option('brtg_bot_token') ) {
		return( '1' );
	}
	else if ( '' != get_option('brtg_bot_token') ) {
		$nt = new Notifcaster_Class();
		$nt->_telegram( get_option('brtg_bot_token') );
		$result = $nt->get_bot();
		if ( '1' != $result['ok'] ) {
			return( '2' ); 
		}
	}
	return( '0' );
}

if ( is_admin() ) {
	include_once( 'admin-page.php' );
}

function brtg_activate(){

  update_option('brtg_author', 'Enzo Costantini (SoftCos)', 'no');
  if ( null == get_option('brtg_delete_options') ) {
    update_option('brtg_delete_options', 0, 'no');
  }
  if ( null == get_option('auto_send') ) {
    update_option('auto_send', 0, 'no');
  }
  if ( null == get_option('brtg_post_type') ) {
    update_option('brtg_post_type', '', 'no');
  }
  if ( null == get_option('brtg_bot_token') ) {
    update_option('brtg_bot_token', '', 'no');
  }
  if ( null == get_option('brtg_telegram_channels') ) {
    update_option('brtg_telegram_channels', '', 'no');
  }
  if ( null == get_option('brtg_msg_web_preview') ) {
    update_option('brtg_msg_web_preview', 0, 'no');
  }
  if ( null == get_option('brtg_msg_tpl') ) {
    update_option('brtg_msg_tpl', MSG_TPL, 'no');
  }
}
register_activation_hook( __FILE__, 'brtg_activate');

function brtg_uninstall(){
  if(get_option('brtg_delete_options')){
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE 'brtg_%'" );
    delete_post_meta_by_key( '_brtg_targetSent' );
  }  
}
register_uninstall_hook( __FILE__, 'brtg_uninstall' );

function brtg_add_metabox_callback() {   
  global $post;
  
  $wasSent = get_post_meta( $post->ID, '_brtg_targetSent', true );
    
  wp_nonce_field( plugin_basename(__FILE__), '_brtg_toTelegram_nonce' );
?>
<input type="hidden" name="brtg_wasSent" id="brtg_wasSent" value="<?php echo base64_encode($wasSent); ?>">
<table width="100%">
  <tr>
    <td>
       <img src="<?php echo plugins_url( '/images/telegram-icon.png' , __FILE__ ); ?>" title="<?php _e('Notify to Telegram', 'broadcast-telegram'); ?>" alt="<?php _e('Notify to Telegram', 'broadcast-telegram'); ?>">      
    </td>
    <td>
<?php
   if( !empty($wasSent) ){echo '<span style="font-weight: bold; color: #0000ee;">' . __('Post already notified', 'broadcast-telegram') . '</span>';}
?>
  </td>
  </tr>
</table>
<table class="form-table">
   <tr>
     <td style="margin: 0; padding: 0;">
       <label><?php _e('Available channels to send:', 'broadcast-telegram'); ?></label>
       <div style="min-height: 40px; max-height: 130px; overflow: auto; padding: 0.25em 0.75em; border: 1px solid #ddd; background-color: #fdfdfd;">   
       <ul>
<?php       
  $brtg_channels = get_option('brtg_telegram_channels');
  $brtg_channels_arr = explode('+', $brtg_channels);
  foreach($brtg_channels_arr as $brtg_channel){ 
  $nt = new Notifcaster_Class();
	$nt->_telegram( get_option('brtg_bot_token') );
	$result = $nt->get_chat($brtg_channel);
  $channel = '';
  $members = 0; 
  if(1 == $result['ok']){
    $channel = $result['result']['title'];
        
    $membersRes = $nt->get_members_count($brtg_channel);
    $members = $membersRes['result']; 
     
    if( !empty($wasSent) ){
        $colored = !(false === strpos($wasSent, $brtg_channel));
        $checked = 0;
    } else {
        $colored = false;
        $checked = get_option('brtg_auto_send');
    }
?>       
       
       <li><input name="brtg_channels[]" id="<?php echo $brtg_channel; ?>" value="<?php echo $brtg_channel; ?>" <?php checked($checked); ?> type="checkbox">
<?php 
  $item = $channel . '<small><em> (' . $members . ' m)</em></small>';
  if ( $colored ){ $item = '<span style="color: #0000ee; font-weight: bold;">' . $item . '</span>';}
  echo $item; 
  
?>
  </li>
<?php } 

      } ?>
       </ul>
       </div>  
    </td>
   </tr>

</table>
<?php
}


function brtg_add_metabox($post_type) {
  $brtg_post_type = get_option('brtg_post_type');
  if($brtg_post_type <> ''){
	  $post_type_arr = explode(',',$brtg_post_type);
    if(in_array($post_type, $post_type_arr)){
	    add_meta_box('brtg_metabox_id',
                    __('Broadcast to Telegram', 'broadcast-telegram'),
                   'brtg_add_metabox_callback',
                    $post_type, 
                   'side', 
                   'high' );
    }
  } 
}

if ('0' == brtg_isActive()){ 
  add_action('add_meta_boxes', 'brtg_add_metabox'); 
}


function brtg_publish_post( $post_id ) {   

	if ( is_admin() ) {
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return( false );
	    if ( empty( $post_id ) ) return( false );
	    if ( false !== wp_is_post_revision( $post_id ) ) return( false );
	    if ( !wp_verify_nonce( $_POST['_brtg_toTelegram_nonce'], plugin_basename(__FILE__) ) ) return( $post_id );
	    if ( !isset( $_POST['brtg_channels'] ) ) return( $post_id );
 	    if ( '0' != brtg_isActive() ) return( false );
	}
    
    $disableWebPreview = get_option('brtg_msg_web_preview');
        
    $theMessage = get_option('brtg_msg_tpl');
    if ( '' == $theMessage ) {
    	$theMessage = MSG_TPL;
    }
    
    $theMessage = str_replace( TITLE, get_the_title( $post_id ), $theMessage );
    $theMessage = str_replace( FULLURL, get_permalink( $post_id ), $theMessage );
    $theMessage = str_replace( SHORTURL, wp_get_shortlink( $post_id ), $theMessage );
    $theMessage = str_replace( EXCERPT, wp_trim_words( get_post_field( 'post_content', $post_id ), 100, '...' ), $theMessage );
    
    if ( strpos( $theMessage, TAGS ) !== false ) {
    	$postTags = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );
    	foreach ($postTags as $tag) {
    		$tagList .= ' #' . str_replace( ' ', '', $tag );
    	}
    	$theMessage = str_replace( TAGS, substr( $tagList, 1 ), $theMessage );
    }
    
    if ( strpos( $theMessage, CATEGORIES ) !== false ) {
    	$postCategories = wp_get_post_categories( $post_id, array( 'fields' => 'names' ) );
    	foreach ($postCategories as $category) {
    		$categoriesList .= $category . ', ';
    	}
    	$theMessage = str_replace( CATEGORIES, substr( $categoriesList, 0, -2 ), $theMessage );
    }
    
    $theMessage = html_entity_decode($theMessage);
       
    $botToken = get_option('brtg_bot_token');
    $channelsName = $_POST['brtg_channels'];
    
    $wasSent = base64_decode($_POST['brtg_wasSent']);
    $wasSentArr = json_decode($wasSent, true); 
    
    $nt = new Notifcaster_Class();
    $nt->_telegram( $botToken, 'markdown', $disableWebPreview );
	  
    $result = array();
    foreach($channelsName as $channelName){
      $msg_id = $wasSentArr[$channelName];
      if( $msg_id ){
         $sentResult = $nt->edit_channel_text( $channelName, $msg_id, '*(M)* ' . $theMessage);
      } else {
         $sentResult = $nt->channel_text( $channelName, $theMessage);
      }
      if( true ==  $sentResult["ok"]){
        $result[trim($channelName)] = $sentResult['result']['message_id'];
      }    
	  }
    if ( !empty($result) ) {
    update_post_meta( $post_id, '_brtg_targetSent', json_encode($result));
	}
}
add_action( 'publish_post', 'brtg_publish_post', 10, 2 );


?>