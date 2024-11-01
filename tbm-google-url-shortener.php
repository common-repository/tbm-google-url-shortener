<?php
/**
 * Plugin Name: TBM Google URL Shortener
 * Plugin URI: http://www.thebestmakers.com/tbm-google-url-shortener
 * Description: TBM Google URL Shortner will put a form on your website from where your website visitor can make the long URL short. This is a easy url shortner plugin for WordPress. Add <strong>[tbm_google_url_shortener]</strong> to your page or widget to display the form.
 * Version: 1.0.0
 * Author: TheBestMakers
 * Author URI: http://www.thebestmakers.com
 * License: GPLv2 or later
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $tbm_google_url_shortener_db_version;
$tbm_google_url_shortener_db_version = '1.0';

function tbm_google_url_shortener_install() {
	global $wpdb;
	global $tbm_google_url_shortener_db_version;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'tbmurlshortener';
	$sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  googlekey text NOT NULL,
	  PRIMARY KEY  (id)
	) $charset_collate;";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	$googlekey = "GoogleKey";
	$table_name = $wpdb->prefix . 'tbmurlshortener';
	$wpdb->insert( 
		$table_name, 
		array( 
			'googlekey' => $googlekey, 
		) 
	);
}
register_activation_hook( __FILE__, 'tbm_google_url_shortener_install' );
// main function
function url_shortener(){
class GoogleUrlApi {
	function GoogleURLAPI($key,$apiURL = 'https://www.googleapis.com/urlshortener/v1/url') {
		$this->apiURL = $apiURL.'?key='.$key;
	}
	// URL shortener
	function shorten($url) { 
		$response = $this->send($url);
		return isset($response['id']) ? $response['id'] : false;
	}
	// Expand URL
	function expand($url) {
		$response = $this->send($url,false);
		return isset($response['longUrl']) ? $response['longUrl'] : false;
	}
	function send($url,$shorten = true) {
		
		if($shorten) {
			
			$result = wp_remote_post($this->apiURL, array(
				'method' => 'POST',
				'sslverify' => false,
				'httpversion' => '1.0',
				'headers' =>  array( 'content-type' => 'application/json' ),
				'body' =>  json_encode(array( "longUrl" => $url ))
				)
			); 
			
		}
		else { 
			$result = wp_remote_post($this->apiURL.'&shortUrl='.$url, array(
				'method' => 'GET',
				'sslverify' => false,
				'httpversion' => '1.0',
				'headers' =>  array( 'content-type' => 'application/json' )
				)
			);
		}
		//print_r($result);
		return json_decode($result["body"],true);
	}		
}
?>
<!-- TBM URL Shortener Form -->
<form method="post">
<input type="text" name="urltoshort" />
<br />
<input type="submit" name="urltoshortbtn" value="Get the link" />
<?php wp_nonce_field( 'tbm-url-short'); ?>
</form>
<?php   

if(isset($_POST["urltoshortbtn"])){

	$thislink = sanitize_text_field($_POST['urltoshort']); 
	$check = check_admin_referer("tbm-url-short");
	// get api key from db
	global $wpdb;
	$table_name = $wpdb->prefix . 'tbmurlshortener'; 
	$mylink = $wpdb->get_results( "SELECT * FROM $table_name WHERE id=1" );
	foreach( $mylink as $results ) {
			$googlekeyvalue = $results->googlekey;
	}
	// Create instance with key
	$key = $googlekeyvalue;
	$googer = new GoogleURLAPI($key);
	
		if($thislink != "" && $check==1){
		$shorURL = $googer->shorten($thislink);
		echo "Your short url: ".$shorURL;
		$longURL = $googer->expand($shorURL);
		echo "<br />The original url: ".$longURL;
		}
	}
}
add_shortcode('tbm_google_url_shortener', 'url_shortener');
// option page
add_action('admin_menu', 'tbm_google_url_shortener_options');
function tbm_google_url_shortener_options() {
	add_menu_page( 'TBM Google URL Shortener Settings', 'TBM URL Short', 'manage_options', 'tbm-google-url-shortener/options.php', '', plugins_url( 'tbm-google-url-shortener/icon.png' )  );
}