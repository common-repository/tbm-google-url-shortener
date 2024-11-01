<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;
$table_name = $wpdb->prefix . 'tbmurlshortener';
$mylink = $wpdb->get_results( "SELECT * FROM $table_name WHERE id=1" );
foreach( $mylink as $results ) {
        $googlekeyvalue = $results->googlekey;
}
?>

<h1>TBM Google URL Shortener Settings</h1>
<hr />
<h2>How to use this plugin</h2>
TBM Google URL Shortener is easy to use plugin. Please use the shortcode <strong>[tbm_google_url_shortener]</strong> to your page or widget to display the form.
<br /><br />
<hr />
<h2>Google API Key</h2>
To run this plugin, you need the Google API Key. Get your Google API Key at <a href="https://developers.google.com/url-shortener/v1/getting_started#APIKey" target="_blank">https://developers.google.com/url-shortener/v1/getting_started#APIKey</a>.
<br />
Learn more about Google API Key at <a href="https://developers.google.com/url-shortener/v1/getting_started#auth" target="_blank">https://developers.google.com/url-shortener/v1/getting_started#auth</a>.
<br /><br />
<hr />
<h2>Enter your Google API Key</h2>
<form method="post">
Your Google API Key: <input type="text" value="<?php echo $googlekeyvalue; ?>" name="yourgoogleapikey" />
<br />
<input type="submit" value="Submit" name="newkeybtn" />
<?php wp_nonce_field( 'tbm-url-short-admin'); ?>
</form>
<?php
if(isset($_POST['newkeybtn'])){ 
	$check = check_admin_referer("tbm-url-short-admin");
	if($check==1){
		$newgooglekey = sanitize_text_field($_POST['yourgoogleapikey']); 
		$updatequery = $wpdb->update( $table_name, array('id' => "1", 'googlekey' => $newgooglekey), array( 'id' => 1 ));
		if($updatequery){
			echo "Your new Google API Key is ".$newgooglekey;
		}
	}
}
?>
<br /><br />
<hr />
Visit the developer website: <a href="http://www.thebestmakers.com" target="_blank">The Best Makers</a>.