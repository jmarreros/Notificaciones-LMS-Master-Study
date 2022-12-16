<?php

namespace dcms\notifications\includes;

class ProcessOrder {
	public function __construct() {
		add_action( 'woocommerce_order_status_changed', [ $this, 'order_status_change' ], 20, 3 );
	}

	public function order_status_change( $order_id, $old_status, $new_status ) {


		if ( $new_status === 'failed' || $new_status === 'cancelled' ) {
			$order = wc_get_order( $order_id );
			$user  = $order->get_user();
			$name  = $user->display_name;
			$email = $user->user_email;
			$this->send_email( $name, $email, $order_id );
		}
	}

	// For sending email
	private function send_email( $name, $email, $order_id ): void {
		dcms_sender_configuration();
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		$options = get_option( 'dcms-notif_options' );

		$enable = isset( $options['dcms_enable_email_failed_order'] );
		if ( ! $enable ) {
			return;
		}

		$subject = $options["dcms_subject_email_failed_order"];
		$body    = $options["dcms_text_email_failed_order"];

		$body = str_replace( '%name%', $name, $body );
		$body = str_replace( '%order_id%', $order_id, $body );

		wp_mail( $email, $subject, $body, $headers );
	}
}
