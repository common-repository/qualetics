<?php
// Abort if this file is called directly
if ( ! defined( 'RLTQUALETICS_PATH' ) ) {
	die;
}

/**
 * Class QLTS_Admin
 *
 * This class creates QLTS_Admin Settings page
 */
class QLTS_Admin 
{
 
	/**
	* The admin security nonce
	*
	* @var string
	*/
	private $_nonce = 'QLTS_Admin';
 
	/**
	* QLTS_Admin constructor.
	*/
	public function __construct() 
	{
		add_action( 'admin_menu', array( $this, 'QLTSAdminMenu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'QLTSAdminScripts' ) );
	 
	 }
	 /**
	 * Adds rltqualetics to WordPress Admin Sidebar Menu
	 */
	public function QLTSAdminMenu() {
		$icon = RLTQUALETICS_URL . '/assets/images/icon.png';
		add_menu_page(
			__( 'Qualetics', 'rltqualetics' ),
			__( 'Qualetics', 'rltqualetics' ),
			'manage_options',
			'qualetics_setting',
			array( $this, 'adminlayout' ),
			$icon,
			5
		);
	}
	/**
	 * Outputs the Admin Dashboard layout
	 */
	public function adminlayout() {
		$settings_message = '';
		$qualetics_settings = get_option('qualetics_settings', array());
		$token_is = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
		if ( $token_is === "rltqualetics_api_settings" ) {
			if ( wp_verify_nonce( sanitize_text_field($_POST['security']), $this->_nonce ) === false ) {
				$settings_message = __('Security Nonce Expired! Retry.', 'rltqualetics');
			} else {
				$application_id = isset($_POST['application_id']) ? sanitize_text_field($_POST['application_id']) : "";
				$qualetics_settings['APPLICATION_ID'] = $application_id;
				
				$application_secret = isset($_POST['application_secret']) ? sanitize_text_field($_POST['application_secret']) : "";
				$qualetics_settings['APPLICATION_SECRET'] = $application_secret;
				
				$client_id = isset($_POST['client_id']) ? sanitize_text_field($_POST['client_id']) : "";
				$qualetics_settings['CLIENT_ID'] = $client_id;
				
				update_option( 'qualetics_settings', $qualetics_settings );
				$settings_message = __('Settings Saved!', 'rltqualetics');
			}
		}
		if ( $token_is === "rltqualetics_tracking_settings" ) {
			if ( wp_verify_nonce( sanitize_text_field($_POST['security']), $this->_nonce ) === false ) {
				$settings_message = __('Security Nonce Expired! Retry.', 'rltqualetics');
			} else {
				$trackUserGeoLocation = isset($_POST['trackUserGeoLocation']) ? sanitize_text_field($_POST['trackUserGeoLocation']) : "false";
				$qualetics_settings['trackUserGeoLocation'] = $trackUserGeoLocation;
				
				$trackPageVisibilityChanges = isset($_POST['trackPageVisibilityChanges']) ? sanitize_text_field($_POST['trackPageVisibilityChanges']) : "false";
				$qualetics_settings['trackPageVisibilityChanges'] = $trackPageVisibilityChanges;
				
				$appVersion = isset($_POST['appVersion']) ? sanitize_text_field($_POST['appVersion']) : "";
				$qualetics_settings['appVersion'] = $appVersion;
				
				$disableErrorCapturing = isset($_POST['disableErrorCapturing']) ? sanitize_text_field($_POST['disableErrorCapturing']) : "false";
				$qualetics_settings['disableErrorCapturing'] = $disableErrorCapturing;
				
				$captureClicks = isset($_POST['captureClicks']) ? sanitize_text_field($_POST['captureClicks']) : "false";
				$qualetics_settings['captureClicks'] = $captureClicks;
				
				$captureTimings = isset($_POST['captureTimings']) ? sanitize_text_field($_POST['captureTimings']) : "false";
				$qualetics_settings['captureTimings'] = $captureTimings;
				
				update_option( 'qualetics_settings', $qualetics_settings );
				$settings_message = __('Settings Saved!', 'rltqualetics');
			}
		}
		if ( $token_is === "rltqualetics_customtracking_settings" ) {
			if ( wp_verify_nonce( sanitize_text_field($_POST['security']), $this->_nonce ) === false ) {
				$settings_message = __('Security Nonce Expired! Retry.', 'rltqualetics');
			} else {
				$enableCustomEvents = isset($_POST['enableCustomEvents']) ? sanitize_text_field($_POST['enableCustomEvents']) : "false";
				$qualetics_settings['enableCustomEvents'] = $enableCustomEvents;
				
				$trackLogin = isset($_POST['trackLogin']) ? sanitize_text_field($_POST['trackLogin']) : "false";
				$qualetics_settings['trackLogin'] = $trackLogin;
				
				$trackLogout = isset($_POST['trackLogout']) ? sanitize_text_field($_POST['trackLogout']) : "false";
				$qualetics_settings['trackLogout'] = $trackLogout;
				
				$trackRegistration = isset($_POST['trackRegistration']) ? sanitize_text_field($_POST['trackRegistration']) : "false";
				$qualetics_settings['trackRegistration'] = $trackRegistration;
				
				$trackRegistration = isset($_POST['trackRegistration']) ? sanitize_text_field($_POST['trackRegistration']) : "false";
				$qualetics_settings['trackRegistration'] = $trackRegistration;
				
				$trackComment = isset($_POST['trackComment']) ? sanitize_text_field($_POST['trackComment']) : "false";
				$qualetics_settings['trackComment'] = $trackComment;
				
				$trackCart = isset($_POST['trackCart']) ? sanitize_text_field($_POST['trackCart']) : "false";
				$qualetics_settings['trackCart'] = $trackCart;
				
				$trackPurchase = isset($_POST['trackPurchase']) ? sanitize_text_field($_POST['trackPurchase']) : "false";
				$qualetics_settings['trackPurchase'] = $trackPurchase;
				
				$trackReview = isset($_POST['trackReview']) ? sanitize_text_field($_POST['trackReview']) : "false";
				$qualetics_settings['trackReview'] = $trackReview;
				
				$trackSearch = isset($_POST['trackSearch']) ? sanitize_text_field($_POST['trackSearch']) : "false";
				$qualetics_settings['trackSearch'] = $trackSearch;
				
				update_option( 'qualetics_settings', $qualetics_settings );
				$settings_message = __('Settings Saved!', 'rltqualetics');
			}
		}
		$application_id = isset($qualetics_settings['APPLICATION_ID']) ? $qualetics_settings['APPLICATION_ID'] : "";
				
		$application_secret = isset($qualetics_settings['APPLICATION_SECRET']) ? $qualetics_settings['APPLICATION_SECRET'] : "";
				
		$client_id = isset($qualetics_settings['CLIENT_ID']) ? $qualetics_settings['CLIENT_ID'] : "";
		
		$trackUserGeoLocation = isset($qualetics_settings['trackUserGeoLocation']) ? $qualetics_settings['trackUserGeoLocation'] : "false";
				
		$trackPageVisibilityChanges = isset($qualetics_settings['trackPageVisibilityChanges']) ? $qualetics_settings['trackPageVisibilityChanges'] : "false";
				
		$appVersion = isset($qualetics_settings['appVersion']) ? $qualetics_settings['appVersion'] : "";
				
		$disableErrorCapturing = isset($qualetics_settings['disableErrorCapturing']) ? $qualetics_settings['disableErrorCapturing'] : "false";
				
		$captureClicks = isset($qualetics_settings['captureClicks']) ? $qualetics_settings['captureClicks'] : "false";
				
		$captureTimings = isset($qualetics_settings['captureTimings']) ? $qualetics_settings['captureTimings'] : "false";
		
		$enableCustomEvents = isset($qualetics_settings['enableCustomEvents']) ? $qualetics_settings['enableCustomEvents'] : "false";
		
		$trackLogin = isset($qualetics_settings['trackLogin']) ? $qualetics_settings['trackLogin'] : "false";
				
		$trackLogout = isset($qualetics_settings['trackLogout']) ? $qualetics_settings['trackLogout'] : "false";
		
		$trackRegistration = isset($qualetics_settings['trackRegistration']) ? $qualetics_settings['trackRegistration'] : "false";
		
		$trackRegistration = isset($qualetics_settings['trackRegistration']) ? $qualetics_settings['trackRegistration'] : "false";
		
		$trackComment = isset($qualetics_settings['trackComment']) ? $qualetics_settings['trackComment'] : "false";
		
		$trackCart = isset($qualetics_settings['trackCart']) ? $qualetics_settings['trackCart'] : "false";
		
		$trackPurchase = isset($qualetics_settings['trackPurchase']) ? $qualetics_settings['trackPurchase'] : "false";
		
		$trackReview = isset($qualetics_settings['trackReview']) ? $qualetics_settings['trackReview'] : "false";
		
		$trackSearch = isset($qualetics_settings['trackSearch']) ? $qualetics_settings['trackSearch'] : "false";
		
		$icon1 = '<img src="'. RLTQUALETICS_URL. '/assets/images/rltqualetics.png" alt="Qualetics logo">';
		$activetab = isset($_GET['qltab']) && !empty($_GET['qltab']) ? $_GET['qltab'] : '';
		?>
		<style>#wpwrap{background: white;}.rlt-link{box-shadow:none!important;}.rltqualetics-metabox-description{margin-top:0px;}a{list-style-type: disc;}ul strong{text-decoration: underline}</style>
		<div class="wrap">
			<a href="https://docs.qualetics.com/javascript" target="_blank" class="rlt-link"><img src="<?php echo esc_url(RLTQUALETICS_URL); ?>assets/images/QualeticsLogo.png" style=""></a>
			<h2 style="font-weight: 600;font-size: 36px;"><?php _e( 'Qualetics For WordPress', 'rltqualetics'); ?></h2>
			<p style=""><?php _e( 'Power your website with advanced Analytics and AI. Sign up and get your account at <a href="https://www.qualetics.com" target="_blank">www.qualetics.com</a>. If you already have an account, proceed with the setup using the settings below.', 'rltqualetics'); ?></p>
			<h2 class="nav-tab-wrapper">
				<a href="#rltqualetics_api_settings" class="nav-tab" id="rltqualetics_api_settings-tab"><?php _e( 'API Settings', 'rltqualetics'); ?></a>
				<a href="#rltqualetics_tracking_settings" class="nav-tab" id="rltqualetics_tracking_settings-tab"><?php _e( 'Analytics Settings', 'rltqualetics'); ?></a>
				<a href="#rltqualetics_about_us" class="nav-tab" id="rltqualetics_about_us-tab"><?php _e( 'Introduction', 'rltqualetics'); ?></a>
			</h2>
			<?php if ($settings_message) { ?>
			<div class="updated rltqualetics-message" style="display:block;"><?php echo esc_html($settings_message); ?></div>
			<?php } ?>
			<div id="rltqualetics" class="metabox-holder">
				<div id="rltqualetics_api_settings" class="group" style="">
					<div class="inside">
						<div class="wrap rltqualetics-performance">			
							<div class="tabs-holder">
								<div class="content-tab">
									<div class="single-tab" id="app-tab" style="display: block;">
										<div class="row">
											<div class="col-md-12 rltqualetics__section app-section">
												<form method="post" action="<?php echo esc_url(admin_url()); ?>admin.php?page=qualetics_setting">
													<div class="db-row">
														<div class="col-md-4">
															<label><?php _e('APPLICATION ID'); ?></label>
														</div>
														<div class="col-md-8">
															<input class="form-control" id="application_id" name="application_id" placeholder="" value="<?php echo esc_html($application_id); ?>" style="margin-top:2px"/>
															<p class="rltqualetics-metabox-description" style="margin-top: 0px;"><?php _e('Enter APPLICATION_ID'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row">
														<div class="col-md-4">
															<label><?php _e('APPLICATION SECRET'); ?></label>
														</div>
														<div class="col-md-8">
															<input class="form-control" id="application_secret" name="application_secret" placeholder="" value="<?php echo esc_html($application_secret); ?>" style="margin-top:2px"/>
															<p class="rltqualetics-metabox-description" style="margin-top: 0px;"><?php _e('Enter APPLICATION_SECRET'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row">
														<div class="col-md-4">
															<label><?php _e('APPLICATION PREFIX'); ?></label>
														</div>
														<div class="col-md-8">
															<input class="form-control" id="client_id" name="client_id" placeholder="" value="<?php echo esc_html($client_id); ?>" style="margin-top:2px"/>
															<p class="rltqualetics-metabox-description" style="margin-top: 0px;"><?php _e('Enter APPLICATION_PREFIX '); ?></p>
														</div>
													</div>
													<br/>
													<div class="form-group">
														<input type="hidden" name="security" value="<?php echo esc_html( wp_create_nonce( $this->_nonce ) ); ?>" />
														<input type="hidden" name="token" value="rltqualetics_api_settings" />
														<button class="button rltqualetics-btn" id="rltqualetics-api-settings-btn"><?php _e( 'Save Settings', 'rltqualetics' ); ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>	
								</div>									
							</div>
						</div>
					</div>
				</div>
				<div id="rltqualetics_tracking_settings" class="group" style="">
					<div class="inside">
						<div class="wrap rltqualetics-performance">			
							<div class="tabs-holder">
								<div class="tab-nav">
									<input type="hidden" value="" id="tba"></a>
									<ul class="">
										<li class="<?php if ($activetab == 'generalanalytics-tab' || $activetab == '') echo 'active-tab'; ?>" data-tabid="generalanalytics-tab">
											<span><?php _e('General'); ?></span>
											<p class="margin0">
												<medium><?php _e('Main Tracking Settings'); ?></medium>
											</p>
										</li>
										<li class="<?php if ($activetab == 'customanalytics-tab') echo 'active-tab'; ?>" data-tabid="customanalytics-tab">
											<span><?php _e('Custom Events'); ?></span>
											<p class="margin0">
												<medium><?php _e('Custom Events Tracking Settings'); ?></medium>
											</p>
										</li>						                      
									</ul>
								</div>
								<div class="content-tab">
									<div class="single-tab" id="generalanalytics-tab" <?php if ($activetab == 'generalanalytics-tab' || $activetab == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
										<div class="row">
											<div class="col-md-12 rltqualetics__section app-section">
												<form method="post" action="<?php echo esc_url(admin_url()); ?>admin.php?page=qualetics_setting">
													<div class="db-row">
														<div class="col-md-4">
															<label for="trackPageVisibilityChanges"><?php _e('Track Page Visibility Changes'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackPageVisibilityChanges" name="trackPageVisibilityChanges" type="checkbox" value="<?php echo esc_html($trackPageVisibilityChanges); ?>" <?php if ($trackPageVisibilityChanges === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track your website page visibility changes.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row">
														<div class="col-md-4">
															<label for="trackUserGeoLocation"><?php _e('Track User Geo Location'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackUserGeoLocation" name="trackUserGeoLocation" type="checkbox" value="<?php echo esc_html($trackUserGeoLocation); ?>" <?php if ($trackUserGeoLocation === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track your website user geo location.'); ?></p>
														</div>
													</div>
													<br/>										
													<div class="db-row">
														<div class="col-md-4">
															<label><?php _e('App Version'); ?></label>
														</div>
														<div class="col-md-8">
															<input class="form-control" id="appVersion" name="appVersion" placeholder="" value="<?php echo esc_html($appVersion); ?>" style="margin-top:2px"/>
															<p class="rltqualetics-metabox-description"><?php _e('Enter app version.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row">
														<div class="col-md-4">
															<label for="disableErrorCapturing"><?php _e('Enable Error Capturing'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="disableErrorCapturing" name="disableErrorCapturing" type="checkbox" value="<?php echo esc_html($disableErrorCapturing); ?>" <?php if ($disableErrorCapturing === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to capture errors for debugging. By default it\'s set to false!'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row">
														<div class="col-md-4">
															<label for="captureClicks"><?php _e('Capture Clicks'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="captureClicks" name="captureClicks" type="checkbox" value="<?php echo esc_html($captureClicks); ?>" <?php if ($captureClicks === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to capture clicks!'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row">
														<div class="col-md-4">
															<label for="captureTimings"><?php _e('Capture Timings'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="captureTimings" name="captureTimings" type="checkbox" value="<?php echo esc_html($captureTimings); ?>" <?php if ($captureTimings === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to capture timings!'); ?></p>
														</div>
													</div>
													<br/>
													<div class="form-group">
														<input type="hidden" name="security" value="<?php echo esc_html(wp_create_nonce( $this->_nonce )); ?>" />
														<input type="hidden" name="token" value="rltqualetics_tracking_settings" />
														<button class="button rltqualetics-btn" id="rltqualetics-details"><?php _e( 'Save Settings', 'rltqualetics' ); ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>
									<div class="single-tab" id="customanalytics-tab" <?php if ($activetab === 'customanalytics-tab') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
										<div class="row">
											<div class="col-md-12 rltqualetics__section app-section">
												<form method="post" action="<?php echo esc_url(admin_url()); ?>admin.php?page=qualetics_setting">
													<!--
													<div class="db-row">
														<div class="col-md-4">
															<label for="enableCustomEvents"><?php _e('Enable Custom Events Tracking'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="enableCustomEvents" name="enableCustomEvents" type="checkbox" value="<?php echo esc_html($enableCustomEvents); ?>" <?php if ($enableCustomEvents === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to start using custom events tracking on your website.'); ?></p>
														</div>
													</div>
													<br/>
													-->
													<div class="db-row custom-events">
														<div class="col-md-4">
															<label for="trackLogin"><?php _e('Track Login'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackLogin" name="trackLogin" type="checkbox" value="<?php echo esc_html($trackLogin); ?>" <?php if ($trackLogin === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track when users login to your website.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row custom-events">
														<div class="col-md-4">
															<label for="trackLogout"><?php _e('Track Logout'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackLogout" name="trackLogout" type="checkbox" value="<?php echo esc_html($trackLogout); ?>" <?php if ($trackLogout === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track when users logout from your website.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row custom-events">
														<div class="col-md-4">
															<label for="trackRegistration"><?php _e('Track Registration'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackRegistration" name="trackRegistration" type="checkbox" value="<?php echo esc_html($trackRegistration); ?>" <?php if ($trackRegistration === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track when users register on your website.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row custom-events">
														<div class="col-md-4">
															<label for="trackComment"><?php _e('Track Comments'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackComment" name="trackComment" type="checkbox" value="<?php echo esc_html($trackComment); ?>" <?php if ($trackComment === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track when a user adds a comment on your website.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row custom-events">
														<div class="col-md-4">
															<label for="trackCart"><?php _e('Track WC Cart'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackCart" name="trackCart" type="checkbox" value="<?php echo esc_html($trackCart); ?>" <?php if ($trackCart === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track when a user adds or removes products from your WooCommerce store.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row custom-events">
														<div class="col-md-4">
															<label for="trackPurchase"><?php _e('Track WC Purchase'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackPurchase" name="trackPurchase" type="checkbox" value="<?php echo esc_html($trackPurchase); ?>" <?php if ($trackPurchase === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track when someone purchase woocommerce product on your website.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row custom-events">
														<div class="col-md-4">
															<label for="trackReview"><?php _e('Track WC Review'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackReview" name="trackReview" type="checkbox" value="<?php echo esc_html($trackReview); ?>" <?php if ($trackReview === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track when a user adds a review for a product on your WooCommerce store.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="db-row custom-events">
														<div class="col-md-4">
															<label for="trackSearch"><?php _e('Track Search'); ?></label>
														</div>
														<div class="col-md-8">
															<label class="rltqualetics-switch">
																<input id="trackSearch" name="trackSearch" type="checkbox" value="<?php echo esc_html($trackSearch); ?>" <?php if ($trackSearch === "true" ) { echo esc_html("checked"); } ?>>
																<span class="rltqualetics-slider round"></span>
															</label>
															<p class="rltqualetics-metabox-description"><?php _e('Activate this option to track what user is searching on your website.'); ?></p>
														</div>
													</div>
													<br/>
													<div class="form-group">
														<input type="hidden" name="security" value="<?php echo esc_html(wp_create_nonce( $this->_nonce )); ?>" />
														<input type="hidden" name="token" value="rltqualetics_customtracking_settings" />
														<button class="button rltqualetics-btn" id="rltqualetics-details"><?php _e( 'Save Settings', 'rltqualetics' ); ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>	
								</div>									
							</div>
						</div>
					</div>
				</div>
				<div id="rltqualetics_about_us" class="group" style="">
					<div class="inside">
						<div class="wrap rltqualetics-performance">			
							<div class="tabs-holder">
								<div class="">
									<div class="single-tab" id="app-tab" style="display: none;">
										<div class="row">
											<div class="col-12 rltqualetics__section app-section" style="padding-left: 100px;padding-right: 100px;">
												<h2 style="text-align: center;font-size: 2em;">Qualetics: <strong style="color: rgb(153,0,255);">No Code Analytics & AI </strong> for your WordPress Website</h2>

<p style="font-size: 18px;">Qualetics enables your Wordpress site with a proactive Self-learning Intelligence so that your website is continuously reporting insights to you that can help you improve your customer outreach and engagement. </p>
<h3 style="text-align: center;margin-bottom: 0px;">Analytics & AI</h3>
<ul class="a"><li><p style="font-size: 18px;margin-top: 0px;"><strong>Software quality</strong><b> → </b> Let your Wordpress site report to you how well the site is running, are there any issues while users are browsing your site, where are they experiencing the issues, when and what path are they taking to cause those issues. All this without writing a single line of code! Qualetics helps you to understand the quality, performance and stability of your wordpress website so that you can proactively get ahead of users facing problems.</p></li>

<li><p style="font-size: 18px;margin-top: 0px;"><strong>Product Analytics</strong><b> → </b>Qualetics helps you unearth insights into how your users interact with your website in this case. You will get to know what actions visitors take on your website so you can enhance the experience of the visitors across your website in entirety. </p></li>

<li><p style="font-size: 18px;margin-top: 0px;"><strong>User analytics</strong><b> → </b>Qualetics helps you unearth insights about your visitors, how much time they spend on a specific page, how many times they visit per day, per week, per month, etc. to name a few. </p></li>

<li><p style="font-size: 18px;margin-top: 0px;"><strong>Performance analytics</strong><b> → </b>This is where it gets interesting for you as a wordpress website owner. Qualetics lets you know about the overall quality of your software powering your wordpress website so you can get a fair idea of why or why not visitor traffic is increasing or decreasing. </p></li>

<li><p style="font-size: 18px;margin-top: 0px;"><strong>Geo location insights</strong><b> → </b>Qualetics helps you understand from what all global locations are visitors visiting your wordpress site. You may want to, in the future, maybe create some content in a local language from the most visited country to have a personalized experience for visitors from such locations.</p></li>

<li><p style="font-size: 18px;margin-top: 0px;"><strong>User behavior flow</strong><b> → </b>Wouldn’t it be great if you know exactly where in the entire website visitor experience the visitors faced issues, exceptions, errors, bugs, or friction points? Qualetics helps you to understand the entire user behavior flow on your wordpress website to let you know exactly what derailed your website visitor experience.</p></li></ul>

<h3 style="text-align: center;margin-bottom: 0px;">Data Ownership</h3>
<ul class="a"><li><p style="font-size: 18px;margin-top: 0px;"><strong>Your Website, Your Data</strong><b> → </b>With Qualetics, you are the owner of your data, not Google, not Wordpress. Your account will be set up with a dedicated database and not mixed with any other customer account. You can download and have access to the entire data at any given time. </p></li></ul>

<h3 style="text-align: center;margin-bottom: 0px;">Data Management</h3>
<ul class="a"><li><p style="font-size: 18px;margin-top: 0px;"><strong>Manage multiple sites in one place</strong><b> → </b>If you are an Agency managing multiple customers, track the performance of all your applications in one place. Qualetics multi-tenant platform enables you to manage your entire consortium of wordpress sites as a single window platform. </p></li>

<li><p style="font-size: 18px;margin-top: 0px;"><strong>Provision multiple users including your clients</strong><b> → </b>Do you need to submit performance reports to your customers? How about giving them access to their site reports from one central location? Qualetics enables you to manage multiple accounts, create user groups and create user access to specific accounts for greater privacy and ease of management </p></li></ul>

<h3 style="text-align: center;margin-bottom: 0px;">Data Access</h3>
<ul class="a"><li><p style="font-size: 18px;margin-top: 0px;"><strong>Easily export data into multiple formats</strong><b> → </b>Qualetics enables you to export your analytics and insights through multiple formats such as an Excel spreadsheet, PDF document, etc. so that you have the ability to review those at a later time or the ability to share such insights with a single user or user groups.</p></li></ul> 
<h3 style="text-align: center;margin-bottom: 0px;">Data Integration</h3>
<ul class="a"><li><p style="font-size: 18px;margin-top: 0px;"><strong>Use REST API to embed insights into other apps</strong><b> → </b>Want access to the user activity data to import it into your own analytics dashboard? Use Qualetics Rest-API to get programmatic access to more than 50 different insights to help you stay informed and make sure you make the most accurate data driven decisions.</p></li></ul>

<h3 style="text-align: center;margin-bottom: 0px;">Notification & Alerts</h3>
<ul class="a"><li><p style="font-size: 18px;margin-top: 0px;"><strong>Get proactive alerts based on conditions you set</strong><b> → </b>What if you are keen to check for specific filtered conditions applicable to your wordpress website visitors, under normal circumstances, you will have to visit your website analytics dashboard time and again. With Qualetics, you can set a filter for specific conditions to be met and save that view as a favorite. The system will prompt you as and when such conditions are met. </p></li></ul>


<p style="font-size: 18px;">Now, these are some of the easily transferable benefits of using Qualetics for your wordpress website.</p>

<p style="font-size: 18px;">For more information, please visit <a href="https://wordpress.qualetics.com" target="_blank">https://wordpress.qualetics.com</a></p>

<p style="font-size: 18px;">For additional documentation, please visit <a href="https://docs.qualetics.com" target="_blank">https://docs.qualetics.com</a></p>

<p style="font-size: 18px;">To submit any feedback, please visit <a href="https://feedback.qualetics.com" target="_blank">https://feedback.qualetics.com</a></p>

<p style="font-size: 18px;">To submit a support request, please visit <a href="https://qualetics.freshdesk.com" target="_blank">https://qualetics.freshdesk.com</a></p>

<p style="font-size: 18px;">We look forward to having you on board with us soon.
</p>
											</div>
										</div>
									</div>	
								</div>									
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<script>
					jQuery(document).ready(function($) {
						var _msg = $('.rltqualetics-message');
						if (_msg.length > 0){
							_msg.delay(1500).fadeOut("fast");    
						}
						$('.rltqualetics-switch input').change(function(e){
							if ($(this).is(':checked')) {
								$(this).val("true");
							} else {
								$(this).val("off");
							}
						});
						$('.group').hide();
						var activetab = '';
						if (typeof(localStorage) != 'undefined' ) {
							activetab = localStorage.getItem("activetab");
						}

						//if url has section id as hash then set it as active or override the current local storage value
						if(window.location.hash){
							activetab = window.location.hash;
							if (typeof(localStorage) != 'undefined' ) {
								localStorage.setItem("activetab", activetab);
							}
						}

						if (activetab != '' && $(activetab).length ) {
							$(activetab).fadeIn();
						} else {
							$('.group:first').fadeIn();
						}
						$('.single-tab').show();
						$('.group .collapsed').each(function(){
							$(this).find('input:checked').parent().parent().parent().nextAll().each(
							function(){
								if ($(this).hasClass('last')) {
									$(this).removeClass('hidden');
									return false;
								}
								$(this).filter('.hidden').removeClass('hidden');
							});
						});

						if (activetab != '' && $(activetab + '-tab').length ) {
							$(activetab + '-tab').addClass('nav-tab-active');
						}
						else {
							$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
						}
						$('.nav-tab-wrapper a').click(function(evt) {
							$('.nav-tab-wrapper a').removeClass('nav-tab-active');
							$(this).addClass('nav-tab-active').blur();
							var clicked_group = $(this).attr('href');
							if (typeof(localStorage) != 'undefined' ) {
								localStorage.setItem("activetab", $(this).attr('href'));
							}
							$('.group').hide();
							$(clicked_group).fadeIn();
							evt.preventDefault();
						});
						//dbranklocal tabs toggle
						var QualeticsTabs = '.tab-nav ul li';
						if( $(QualeticsTabs).length > 0 ){
							$('#customanalytics-tab').hide();
							$(QualeticsTabs).on('click', function () {
								var tabsHolder = $(this).closest('.tabs-holder');
								$(QualeticsTabs, tabsHolder).removeClass('active-tab');
								var tabId = $(this).data('tabid');
								$('#tba').val(tabId);
								$(this).addClass('active-tab');
								$('.content-tab .single-tab', tabsHolder).hide();
								$( '#' + tabId ).fadeIn('slow');		
							});		
						}
						/*
						if ($('#enableCustomEvents').is(':checked')) {
							$('.custom-events').show();
						} else {
							$('.custom-events').hide();
						}
						$('#enableCustomEvents').on('change', function () {
							if ($(this).is(':checked')) {
								$('.custom-events').show();
							} else {
								$('.custom-events').hide();
							}
						});
						*/
					});
			</script>
	<?php
	}
	/**
	 * Adds Admin Scripts
	 */
	public function QLTSAdminScripts() {
		$page = isset($_GET['page']);
		if ($page == 'qualetics_setting') {
			wp_register_style( 'QLTS_Admin_css', RLTQUALETICS_URL. '/assets/css/rltqualetics-admin.css' );
			wp_enqueue_style( 'QLTS_Admin_css' );
		}

	}

}
 
/*
 * Starts our admin class!
 */
new QLTS_Admin();