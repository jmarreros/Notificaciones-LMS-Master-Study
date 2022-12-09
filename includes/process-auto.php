<?php

namespace dcms\notifications\includes;

// Automatic Notifications
class ProcessAuto {

	// Main event, called by cron, reminders
	public function process_reminder() {
		$db      = new Database();
		$courses = $db->get_courses_start_in_time(); // Get all courses <= time start 24h

		// Is enable
		$options  = get_option( 'dcms-notif_options' );
		$send_24h = isset( $options['dcms_enable_email_reminder24h'] );
		$send_4h  = isset( $options['dcms_enable_email_reminder4h'] );

		if ( $courses ) {
			foreach ( $courses as $course ) {
				$diff = intval( $course->time_start ) - current_time( 'timestamp', true );

				// 4h Notification
				if ( $diff <= 14400 && $diff > 3600 && $send_4h ) {
					$this->process_notification_user( $course, 4 );
				} // 24h Notification
				else if ( $diff > 14400 && $send_24h ) {
					$this->process_notification_user( $course, 24 );
				}
			}
		}
	}

	private function process_notification_user( $course, $hour ) {
		$course_id    = $course->id ?? 0;
		$course_title = $course->course_name ?? '';

		$meta_key = constant( "DCMS_NOTIF_{$hour}H_COMPLETE" );
		// Validate if sending emails was complete by course
		if ( get_post_meta( $course_id, $meta_key, true ) ) {
			return;
		}

		$db    = new Database();
		$users = $db->get_users_per_course( $course_id );

		if ( ! $users ) {
			add_post_meta( $course_id, $meta_key, 1 );

			return;
		}

		// Process sending
		foreach ( $users as $user ) {
			$sent = $this->send_email( $user->name, $user->email, $course_title, $hour );

			// Save log data
			$data['user_id']   = $user->id;
			$data['course_id'] = $course_id;
			$data['sent']      = $sent;
			$data['hour']      = $hour;

			$db->insert_notifications_user( $data );
		}
	}

	// For alert not start course
	public function alert_not_start_course() {
		$db = new Database;

		// Is enable
		$options  = get_option( 'dcms-notif_options' );
		$send_72h = isset( $options['dcms_enable_email_reminder72h'] );
		if ( ! $send_72h ) {
			return;
		}

		$users = $db->get_students_not_start_course( 3 * DAY_IN_SECONDS );
		$hours = 72; // 3 days in hours

		foreach ( $users as $user ) {

			if ( ! $db->exists_notification_user( $user->id, $user->course_id, $hours ) ) {
				$sent = $this->send_email( $user->name, $user->email, $user->course_title, $hours );

				// Save log data
				$data['user_id']   = $user->id;
				$data['course_id'] = $user->course_id;
				$data['sent']      = $sent;
				$data['hour']      = $hours;

				$db->insert_notifications_user( $data );
			}
		}
	}

	// For sending email
	private function send_email( $name, $email, $course_title, $hour ): bool {
		dcms_sender_configuration();
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		$options = get_option( 'dcms-notif_options' );

		$subject = $options["dcms_subject_email_reminder{$hour}h"];
		$body    = $options["dcms_text_email_reminder{$hour}h"];

		$body = str_replace( '%name%', $name, $body );
		$body = str_replace( '%course_title%', $course_title, $body );

		return wp_mail( $email, $subject, $body, $headers );
	}
}
