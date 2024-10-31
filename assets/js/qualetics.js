var _trackPage_VisibilityChanges = (qualetics_setup.trackPageVisibilityChanges === "true");
var _disable_ErrorCapturing = (qualetics_setup.disableErrorCapturing === "true");
var _trackUser_GeoLocation = (qualetics_setup.trackUserGeoLocation === "true");
var _capture_Clicks = (qualetics_setup.captureClicks === "true");
var _capture_Timings = (qualetics_setup.captureTimings === "true");

var qualetics = new Qualetics.service(qualetics_setup.app_id, qualetics_setup.app_secret, qualetics_setup.app_prefix, _trackPage_VisibilityChanges, {host: "wss://api.qualetics.com", port: 443, defaultActor: qualetics_setup.defaultActor, appVersion:qualetics_setup.appV, disableErrorCapturing: _disable_ErrorCapturing, trackUserGeoLocation: _trackUser_GeoLocation,captureClicks: _capture_Clicks, captureTimings: _capture_Timings});
qualetics.init();

jQuery( document ).ready(function() {
	if (login_trackingObj) {
		qualetics.send(login_trackingObj);
	}
	if (registration_trackingObj) {
		qualetics.send(registration_trackingObj);
	}
	if (addCartObj) {
		setTimeout(function(){
			qualetics.send(addCartObj);
		}, 1500);
	}
	if (comment_trackingObj) {
		qualetics.send(comment_trackingObj);
	}
	if (login_trackingObj) {
		qualetics.send(login_trackingObj);
	}
	jQuery( 'body' ).on( 'added_to_cart', function( e,h, w, button ) {
		setTimeout(function(){
			if(added_to_cartObj){
				qualetics.send(added_to_cartObj);
			}
		}, 1000);
	});
	if(jQuery(".single_add_to_cart_button").length){
		jQuery(document).on("click", ".single_add_to_cart_button", function(){
			setTimeout(function(){
				if(single_add_to_cartObj){
					qualetics.send(single_add_to_cartObj);
				}
			}, 1000);
		});
	}
	setTimeout(function(){
		if(purchaseObj){
			qualetics.send(purchaseObj);
		}
	}, 1000);
	if (searchObj) {
		qualetics.send(searchObj);
	}
	if (logout_trackingObj) {
		qualetics.send(logout_trackingObj);
	}
	jQuery('body').on('updated_wc_div',function() {
		jQuery.ajax( {
			url: qualetics_setup.ajax_url,
			type: 'post',
			data: {
				action: 'qlts_get_removed_from_cart',
				security: qualetics_setup._nonce,
				},
				beforeSend: function(){
				},
				complete: function(){
				},
				success: function ( response ) {
					if (response.data.html) {
						jQuery("body").append(response.data.html);
					}
			}
		} );
	});
});