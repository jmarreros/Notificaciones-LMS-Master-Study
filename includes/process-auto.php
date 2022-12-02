<?php

namespace dcms\notifications\includes;

// Para las notificaciones automáticas, con base a un evento cron

class ProcessAuto {

	public function __construct() {
	}

	public function process_reminder() {
		$db      = new Database();
		$courses = $db->get_courses_start_in_time(); // Get all courses <= time start 24h

		if ( $courses ) {
			foreach ( $courses as $course ) {

				$diff = intval( $course->time_start ) - current_time( 'timestamp' );

				// 4h Notification
				if ( $diff <= 14400 && $diff > 3600 ) {
					$this->process_notification_user( $course->id, 4 );
				}
				// 24h Notification
				else if ( $diff > 14400 ) {
					$this->process_notification_user( $course->id, 24 );
				}

			}
		}
	}

	private function process_notification_user( $course_id, $hour ) {
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

}
