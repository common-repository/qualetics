<?php
// Abort if this file is called directly
if ( ! defined( 'RLTQUALETICS_PATH' ) ) {
	die;
}
/**
 * Class QLTS_Public
 *
 */
class QLTS_Public{

    /**
     * QLTS_Public constructor.
     */
    public function __construct()
    {
		// Frontend Hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'qlts_front_script' ), 999 ); 
		add_action( 'wp_login',  array( $this, 'qlts_track_login' ), 10, 2 ); 
		add_action( 'wp_logout',  array( $this, 'qlts_track_logout' ), 10, 1 );
		add_action( 'user_register',  array( $this, 'qlts_track_registration' ), 10, 1 );
		add_action( 'comment_post',  array( $this, 'qlts_track_comment' ), 10, 3 );
		add_action( 'wp_footer', array($this, 'qlts_addtocart_shop'), 10);
		add_action( 'woocommerce_add_to_cart', array($this, 'qlts_add_to_cart'), 10, 6);
		add_action( 'wp_footer', array($this, 'qlts_track_search'), 30);
		add_action( 'woocommerce_thankyou', array($this, 'qlts_track_purchase'), 10, 1 );
		/* Remove From cart Tracking */
		add_action( 'woocommerce_cart_item_removed', array($this, 'qlts_track_cartitemremoved'), 10, 2 );
		add_action( 'wp_footer', array($this, 'qlts_track_item_removed'), 25);
		add_action( 'wp_ajax_qlts_get_removed_from_cart', array( $this, 'qlts_get_removed_from_cart' ) );
		add_action( 'wp_ajax_nopriv_qlts_get_removed_from_cart', array( $this, 'qlts_get_removed_from_cart' ) );
		/* Remove From cart Tracking Done */
		add_filter( 'script_loader_tag', array( $this, 'qlts_defer_js'), 10, 1 );
    }
	
	function qlts_defer_js( $url ) {
		if ( strpos( $url, 'qualetics.js' ) || strpos( $url, 'qualetics-js-sdk-v3.js' ) ) {
			return str_replace( ' src', ' defer src', $url );
		}
		return $url;
	}
	
	public function qlts_front_script() {
		
		$qualetics_settings = get_option('qualetics_settings', array());
		
		$application_id = isset($qualetics_settings['APPLICATION_ID']) ? $qualetics_settings['APPLICATION_ID'] : "";
				
		$application_secret = isset($qualetics_settings['APPLICATION_SECRET']) ? $qualetics_settings['APPLICATION_SECRET'] : "";
				
		$client_id = isset($qualetics_settings['CLIENT_ID']) ? $qualetics_settings['CLIENT_ID'] : "";
		
		$trackUserGeoLocation = isset($qualetics_settings['trackUserGeoLocation']) ? $qualetics_settings['trackUserGeoLocation'] : "false";
				
		$trackPageVisibilityChanges = isset($qualetics_settings['trackPageVisibilityChanges']) ? $qualetics_settings['trackPageVisibilityChanges'] : "false";
				
		$appVersion = isset($qualetics_settings['appVersion']) ? $qualetics_settings['appVersion'] : "";
				
		$disableErrorCapturing = isset($qualetics_settings['disableErrorCapturing']) ? $qualetics_settings['disableErrorCapturing'] : "false";
				
		$captureClicks = isset($qualetics_settings['captureClicks']) ? $qualetics_settings['captureClicks'] : "false";
				
		$captureTimings = isset($qualetics_settings['captureTimings']) ? $qualetics_settings['captureTimings'] : "false";
		if ( empty( $application_id ) || empty( $application_secret ) || empty( $client_id ) ) {
			return;
		}
		$defaultActor = '';
		$login_tracking = '';
		$logout_tracking = '';
		$registration_tracking = '';
		$comment_tracking = '';
		$add_to_cart_tracking = '';
		$current_page_id = get_the_ID();
		$perma = get_permalink($current_page_id);
		$current_page_title = get_the_title($current_page_id);
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$current_user = wp_get_current_user();
			$user_name = $current_user->display_name;
			$user_email = $current_user->user_email;
			$user_roles = $current_user->roles;
			$user_role = $user_roles[0];
			$defaultactor = '{"type":"'.$user_role.'","id":"'.$user_id.'","attributes":{"name":"'.$user_name.'"}},';
			$track_user_login = get_user_meta($user_id, 'qualetics_track_login', true);
			if ($track_user_login === "1" && $this->is_trackable('trackLogin')) {
				$login_tracking = 'var login_trackingObj = {"action": {"type": "Login"},"context": {"type": "Login","name": "Login","attributes":{"redirectUrl":"'.$perma.'","user_name":"'.$user_name.'","user_email":"'.$user_email.'"}}};
				';
				delete_user_meta($user_id, 'qualetics_track_login');
			} else {
				$login_tracking = 'var login_trackingObj = "";';
			}
			$track_user_registration = get_user_meta($user_id, 'qualetics_track_registration', true);
			if ($track_user_registration === "1" && $this->is_trackable('trackRegistration')) {
				$registration_tracking = 'var registration_trackingObj = {"action": {"type": "Registration"},"context": {"type": "Registration","name": "Registration","attributes":{"user_name":"'.$user_name.'","user_email":"'.$user_email.'"}}};
				';
				delete_user_meta($user_id, 'qualetics_track_registration');
			} else {
				$registration_tracking = 'var registration_trackingObj = "";';
			}
		} else {
			$defaultactor = '{"type":"System","id":"System","attributes":{"name":"System"}},';
		}
		if (isset($_COOKIE['qualetics_track_addtocart'])) {
			$cart_key = sanitize_text_field($_COOKIE['qualetics_track_addtocart']);
			if ($cart_key) {
				$track_user_addtocart = get_option('qualetics_track_addtocart_'.$cart_key, '');
				if (is_array($track_user_addtocart)) {
				 $variation_id = isset($track_user_addtocart["variation_id"]) ? $track_user_addtocart["variation_id"] : '';
					$product_id = isset($track_user_addtocart["product_id"]) ? $track_user_addtocart["product_id"] : '';
					$quantity = isset($track_user_addtocart["quantity"]) ? $track_user_addtocart["quantity"] : 1;
					if ($product_id) {
						$term_list = wp_get_post_terms($product_id,'product_cat',array('fields'=>'ids'));
						$cat_id = (int)$term_list[0];
						$category = get_term ($cat_id, 'product_cat');
						$product_category = $category->name;
						if($variation_id){
							$product_id = $variation_id;
						}
						$_product = wc_get_product($product_id);
						$product_type = $_product->get_type();
						$product_price = $_product->get_price();
						$product_currency = get_woocommerce_currency();
						$product_title = get_the_title($product_id);
						$perma = get_permalink($product_id);
						$pd = wp_strip_all_tags($_product->post->post_content);
						$decription = preg_replace( "/\r|\n/", "",$pd);
						$decription = str_ireplace('"', "'", $decription);
						$add_to_cart_tracking = 'var addCartObj = {
										 "action": {"type": "Add To Cart"},
										 "context": {"type": "Shopping Cart","name": "Shopping Cart"},
										 "object": {"type": "Item", "name": "'.$product_title.'",
										"attributes":{"url":"'.$perma.'","Product Type":"'.$product_type.'","Product Description":"'.$decription.'","Product Quantity":"'.$quantity.'", "Price":"'.$product_price.'"}}
							};';
						delete_option('qualetics_track_addtocart_'.$cart_key);
						unset($_COOKIE['qualetics_track_addtocart']); 
						setcookie('qualetics_track_addtocart', null, -1, '/');
					}
				}
			}
		}
		if (isset($_COOKIE['qualetics_track_comment'])) {
			$comment_id = (int)sanitize_text_field($_COOKIE['qualetics_track_comment']);
			if ($comment_id > 0) {
				$comment_data = get_option('qualetics_track_comment_'.$comment_id, '');
				if (is_array($comment_data)) {
					$comment_text = $comment_data["comment_content"];
					$comment_post = (int)$comment_data["comment_post_ID"];
					$post_type = get_post_type($comment_post);
					$user_name = $comment_data["comment_author_email"];
					if ($post_type == 'product') {
						if($this->is_trackable('trackReview')){
							$_product = wc_get_product($comment_post);
							$product_title = get_the_title($comment_post);
							$product_type = $_product->get_type();
							$pd = wp_strip_all_tags($_product->post->post_content);
							$decription = preg_replace( "/\r|\n/", "",$pd);
							$decription = str_ireplace('"', "'", $decription);
							$comment_tracking = 'var comment_trackingObj = {"action": {"type":"Engagement", "name":"Product Review"},"context": {"type": "Page","name":"'.$current_page_title.'","attributes":{"url":"'.$perma.'", "Product Review":"'.$comment_text.'"}},
								 "object": {"type": "Item", "name":"'.$product_title.'", "attributes":{"Product Type":"'.$product_type.'","Product Description":"'.$decription.'"}}
								 };
								';
							}
					} else {
						if($this->is_trackable('trackComment')){
								$comment_tracking = 'var comment_trackingObj = {"action": {"type": "Engagement","name": "Add Comment"},"context": {"type": "Page","name": "'.$current_page_title.'","attributes":{"url":"'.$perma.'","user_name":"'.$user_name.'","comment":"'.$comment_text.'"}}};
								';
							}
						} 
				}
				delete_option('qualetics_track_comment_'.$comment_id);
				unset($_COOKIE['qualetics_track_comment']); 
				setcookie('qualetics_track_comment', null, -1, '/'); 
			}
		}
		
		wp_enqueue_script( 'qualetics', 'https://sdk.qualetics.com/qualetics-js-sdk-v3.js', array(), '' );
		
		$custom_scripts = '';
		if (empty($login_tracking)){
			$login_tracking = 'var login_trackingObj = "";';
		}
		$custom_scripts .= $login_tracking;
		$custom_scripts .= $logout_tracking;
		if (empty($registration_tracking)){
			$registration_tracking = 'var registration_trackingObj = "";';
		}
		$custom_scripts .= $registration_tracking;
		if (empty($comment_tracking)){
			$comment_tracking = 'var comment_trackingObj = "";';
		}
		$custom_scripts .= $comment_tracking;
		if (empty($add_to_cart_tracking)) {
			$add_to_cart_tracking = 'var addCartObj = "";';
		}
		$custom_scripts .= $add_to_cart_tracking;
		if ( class_exists( 'WooCommerce' ) && is_checkout() && !empty( is_wc_endpoint_url('order-received') ) ) {
			$custom_scripts .= '';
		} else {
			$custom_scripts .= 'var purchaseObj = "";';
		}
		
		wp_add_inline_script('qualetics', $custom_scripts);
		
		wp_enqueue_script( 'qualetics-setup', RLTQUALETICS_URL. 'assets/js/qualetics.js', array(), RLTQUALETICS_VERSION );
		
		$qualetics_options = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'_nonce'   => wp_create_nonce("qlts_get_removed_from_cart"),
			'app_id'   => $application_id,
			'app_secret'   => $application_secret,
			'app_prefix'   => $client_id,
			'trackPageVisibilityChanges'   => $trackPageVisibilityChanges,
			'defaultactor'   => $defaultactor,
			'appV'   => $appVersion,
			'disableErrorCapturing'   => $disableErrorCapturing,
			'trackUserGeoLocation'   => $trackUserGeoLocation,
			'captureClicks'   => $captureClicks,
			'captureTimings'   => $captureTimings,
		);
		wp_localize_script( 'qualetics-setup', 'qualetics_setup', $qualetics_options );
	}
	public function qlts_track_login($user_login, WP_User $user) {
		if($this->is_trackable('trackLogin')){
			update_user_meta($user->ID, 'qualetics_track_login', '1');
		}
	}
	public function qlts_track_logout($user_id) {
		if($this->is_trackable('trackLogout')){
			setcookie('qualetics_track_logout', $user_id, time() + (86400 * 30), "/");
			wp_safe_redirect( home_url() );
			exit();
		}
	}
	public function qlts_track_registration($user_id) {
		if($this->is_trackable('trackRegistration')){
			update_user_meta($user_id, 'qualetics_track_registration', '1');
		}
	}
	public function qlts_track_comment($comment_ID, $comment_approved, $commentdata ) {
		if ($this->is_trackable('trackComment') || $this->is_trackable('trackReview')){
			update_option('qualetics_track_comment_' . $comment_ID , $commentdata);
			setcookie('qualetics_track_comment', $comment_ID, time() + (86400 * 30), "/");
		}
	}
	public function qlts_add_to_cart ($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data){
		if ($this->is_trackable('trackCart')){
			$data = array("variation_id" => $variation_id, "product_id" => $product_id, "quantity" => $quantity);
			update_option('qualetics_track_addtocart_' . $cart_item_key , $data);
			setcookie('qualetics_track_addtocart', $cart_item_key, time() + (86400 * 30), "/");
		}
	}
	
	public function qlts_addtocart_shop(){
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if($this->is_trackable('trackCart') && !isset($_GET['wc-ajax']) ) {
			global $post;
			$product_currency = get_woocommerce_currency();
			$product_price = "";
			$product_id = $post->ID;
			$post_type = get_post_type($product_id);
			if ($post_type == 'product') {
				$_product = wc_get_product($product_id);
				if($post && $_product){
					$product_price = $_product->get_price();
					$product_type = $_product->get_type();
					$product_title = get_the_title($product_id);
					$perma = get_permalink($product_id);
					$pd = wp_strip_all_tags($_product->post->post_content);
					$decription = preg_replace( "/\r|\n/", "",$pd);
					$decription = str_ireplace('"', "'", $decription); 
				?>
				<script>
					var added_to_cartObj = {
							 "action": {"type": "Add To Cart"},
							 "context": {"type": "Shopping Cart","name": "Shopping Cart"},
							 "object": {"type": "Item", "name": "<?php echo $product_title;?>",
							"attributes":{"url":"<?php echo $perma; ?>","Product Type":"<?php echo $product_type; ?>","Product Description":"<?php echo $decription; ?>", "Price":"<?php echo $product_price;?>"}}
					};
					if(jQuery(".single_add_to_cart_button").length){
						 var single_add_to_cartObj = {
								 "action": {"type": "Add To Cart"},
								 "context": {"type": "Shopping Cart","name": "Shopping Cart"}, "object": {"type": "Item", "name": "<?php echo $product_title;?>",
								"attributes":{"url":"<?php echo $perma; ?>","Product Type":"<?php echo $product_type; ?>","Product Description":"<?php echo $decription; ?>", "Price":"<?php echo $product_price;?>"}}
						};
					}
				</script>
				<?php
				} else { 
				?>
				<script>
					var added_to_cartObj = "";
				</script>	
				<?php 
				}
			}
		}
	}
	public function qlts_track_cartitemremoved($item_key, $cart){
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if( $this->is_trackable('trackCart') ){
			$line_item = $cart->removed_cart_contents[ $item_key ];
			$product_id = $line_item[ 'product_id' ];
			$quantity = $line_item['quantity'];
			WC()->session->set( 'qlts_removed_item_qty' , $product_id . "_" . $quantity );
		}
	}
	public function qlts_track_item_removed(){
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if( $this->is_trackable('trackCart') ){
			?>
			<script>
				var remove_from_cart = true;
			</script>
			<?php
		} else {
			?>
			<script>
				var remove_from_cart = false;
			</script>
			<?php
		} 
	}
	public function qlts_get_removed_from_cart(){
		if ( wp_verify_nonce( sanitize_text_field($_POST['security']), "qlts_get_removed_from_cart" ) === false ) {
			wp_send_json_error( __( 'Invalid request, you are not allowed to do that action.', 'qualetics' ) );
		}
		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_send_json_error( __( 'Invalid request, you are not allowed to do that action without WC.', 'qualetics' ) );
		}
		if( $this->is_trackable('trackCart') ) {
			global $woocommerce;
			$cart_item = WC()->session->get( 'qlts_removed_item_qty' );
			$cart_array = explode("_", $cart_item);
			$product_id = $cart_array[0];
			$quantity = $cart_array[1];
			if ((int)$product_id > 0) {
				$_product = wc_get_product($product_id);
				$product_price = $_product->get_price();
				$product_type = $_product->get_type();
				$product_title = get_the_title($product_id);
				$perma = get_permalink($product_id);
				$pd = wp_strip_all_tags($_product->post->post_content);
				$decription = preg_replace( "/\r|\n/", "",$pd);
				$decription = str_ireplace('"', "'", $decription);
				ob_start(); 
				?>
				<script>
					setTimeout(function() {
						var removedObj = {
							"action": {"type": "Remove From Cart"}, 
							"context": {"type": "Shopping Cart","name": "Shopping Cart"},
							"object": {"type": "Item", "name": "<?php echo $product_title; ?>",
							"attributes":{"url":"<?php echo $perma; ?>","Product Type":"<?php echo $product_type; ?>","Product Description":"<?php echo $decription; ?>", "Price":"<?php echo $product_price; ?>", "Product Quantity":"<?php echo $quantity; ?>"}}
							};
						qualetics.send(removedObj);
					}, 1000);
				</script>
				<?php
				$html = ob_get_clean();
				WC()->session->set( 'qlts_removed_item' , '' );
				$data = array(
					'type'     => 'success',
					'text'     => __( 'Good.', 'kineticpay-forminator' ),
					'html' => $html,
				);
				wp_send_json_success( $data );
			} else {
				wp_send_json_error( __( 'Invalid request, your session is expired.', 'qualetics' ) );
			}
		} else {
			wp_send_json_error( __( 'Invalid request, you are not allowed to do that action without trackCart enabled.', 'qualetics' ) );
		}
	}
	public function qlts_track_purchase($order_id){
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if( $this->is_trackable('trackPurchase') ){
			global $woocommerce;
			$order = new WC_Order( $order_id );
			$order_total = $order->get_total();
			$_order_items = $order->get_items();
			$num_items =  $order->get_item_count();
			$product_currency = get_woocommerce_currency();
			?>
			<script>
				var purchaseObj = {
					"action": {"type": "Checkout"},
					"context": {
						"type":"Order Transaction",
						"name":"Checkout"
					},
					"object": {
						"type": "Order Transaction",
						"name":"<?php if($order_id){echo $order_id;}?>",
						"attributes":{
							"Order Total":"<?php echo $order_total; ?>",
							"Number of Items":"<?php echo $num_items; ?>",
							"Products":[<?php
							foreach($_order_items as $item_id => $item ) {
								$product        = $item->get_product();
								$product_id = $product->get_id();
								$active_price   = $product->get_price();
								$item_quantity  = $item->get_quantity();
								$pd = wp_strip_all_tags($product->post->post_content);
								$decription = preg_replace( "/\r|\n/", "",$pd);
								$decription = str_ireplace('"', "'", $decription);
								$product_type = $product->get_type();
								$product_title = get_the_title($product_id);
								print_r('{
									"Product Type":"'.$active_price.'",
									"Product Description":"'.$decription.'",
									"Product Quantity":"'.$item_quantity.'",
									"Price":"'.$active_price.'"
								},');
							}
						?>]}
					},
				};
			</script>
			<?php
		} else {
		?>
			<script>
				var purchaseObj = "";
			</script>
		<?php
		}
	}	
	public function qlts_track_search(){
		if( $this->is_trackable('trackSearch') ){
			$searched = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : "";
			if ($searched) {
			?>
			<script>
				var searchObj = {"action":{"type":"Search"},"context":{"type":"Search","name":"Keyword Search", "attributes":{"searchTerm":"<?php echo $searched ; ?>"}}};
			</script>
			<?php
			} else {
				?>
				<script>
					var searchObj = "";
				</script>
				<?php
			}
		} else {
				?>
				<script>
					var searchObj = "";
				</script>
				<?php
		}
		if (isset($_COOKIE['qualetics_track_logout']) && $this->is_trackable('trackLogout')) {
			$user_id = sanitize_text_field($_COOKIE['qualetics_track_logout']);
			$current_user = get_user_by('id', (int)$user_id);
			$user_name = $current_user->display_name;
			$user_email = $current_user->user_email;
			$current_page_id = get_the_ID();
			$perma = get_permalink($current_page_id);
			?>
			<script>
			var logout_trackingObj = {"action": {"type": "Logout"},"context": {"type": "Logout","name": "Logout","attributes":{"redirectUrl":"<?php echo $perma; ?>","user_name":"<?php echo $user_name; ?>","user_email":"<?php echo $user_email; ?>"}}};
			</script>
			<?php
			unset($_COOKIE['qualetics_track_logout']); 
			setcookie('qualetics_track_logout', null, -1, '/'); 
		} else {
		?>
		<script>
			var logout_trackingObj = "";
		</script>
		<?php
		}
	}
	public function is_trackable($key = ''){
		$qualetics_settings = get_option('qualetics_settings', array());
		
		$application_id = isset($qualetics_settings['APPLICATION_ID']) ? $qualetics_settings['APPLICATION_ID'] : "";
				
		$application_secret = isset($qualetics_settings['APPLICATION_SECRET']) ? $qualetics_settings['APPLICATION_SECRET'] : "";
				
		$client_id = isset($qualetics_settings['CLIENT_ID']) ? $qualetics_settings['CLIENT_ID'] : "";
		
		if ( empty( $application_id ) || empty( $application_secret ) || empty( $client_id ) ) {
			return false;
		}
		if ($key) {
			$custom_key = isset($qualetics_settings[$key]) ? $qualetics_settings[$key] : "";
			if ($custom_key) {
				return true;
			} else {
				return false;
			}
		}
		return true;
	}
	
	public function qlts_get_orderid( $key = false){
		global $wpdb;

		$query = "SELECT post_id FROM " .$wpdb->prefix. "postmeta WHERE
			meta_key='_order_key' and
			meta_value='".$key."' ORDER BY meta_id DESC Limit 1";

		$row = $wpdb->get_row($query);
		$order_id = $row->post_id;

		return $order_id;
	}

}
/*
 * Starts our plugins!
 */
 new QLTS_Public();