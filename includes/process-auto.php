<?php

namespace dcms\notifications\includes;

// Automatic Notifications
class ProcessAuto {


	// Main event, called by cron
	public function process_reminder() {
		$db      = new Database();
		$courses = $db->get_courses_start_in_time(); // Get all courses <= time start 24h

		if ( $courses ) {
			foreach ( $courses as $course ) {

				$diff = intval( $course->time_start ) - current_time( 'timestamp', true);

				error_log(print_r('Diferencia segundos',true));
				error_log( current_time( 'timestamp', true ) );
				error_log(print_r($diff,true));

				// 4h Notification
				if ( $diff <= 14400 && $diff > 3600 ) {
					$this->process_notification_user( $course, 4 );
				}
				// 24h Notification
				else if ( $diff > 14400 ) {
					$this->process_notification_user( $course, 24 );
				}

			}
		}
	}

	private function process_notification_user( $course, $hour ) {
		$course_id = $course->id??0;
		$course_name = $course->name??'';

		$meta_key = constant( "DCMS_NOTIF_{$hour}_COMPLETE" );
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



			// Save log data
			$data['user_id']   = $user->id;
			$data['course_id'] = $course_id;
			$data['sent']      = 1;
			$data['hour']      = $hour;

			$db->insert_notifications_user( $data );
		}
	}



	private function send_email( $name, $email, $course_title ) {
		dcms_sender_configuration();

		$options = get_option( 'dcms-notif_options' );

		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		$subject = $options['dcms_subject_email_course'];
		$body    = $options['dcms_text_email_course'];

		$body = str_replace( '%name%', $name, $body );
		$body = str_replace( '%course_title%', $course_title, $body );

		return wp_mail( $email, $subject, $body, $headers );
	}


}
