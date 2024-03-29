<?php
// Sender configuration
function dcms_sender_configuration() {
	add_filter( 'wp_mail_from', function () {
		$options = get_option( 'dcms-notif_options' );

		return $options['dcms_sender_email'];
	} );
	add_filter( 'wp_mail_from_name', function () {
		$options = get_option( 'dcms-notif_options' );

		return $options['dcms_sender_name'];
	} );
}

//Return unix time according timezone
function dcms_strtotime( $str ) {
	try {
		$datetime = new \DateTime( $str, wp_timezone() );
	} catch ( \Exception $e ) {
		return false;
	}

	return $datetime->format( 'U' );
}