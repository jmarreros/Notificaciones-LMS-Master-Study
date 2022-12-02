<?php

namespace dcms\notifications\includes;

// Para las notificaciones automáticas, con base a un evento cron

class ProcessAuto {

	public function __construct() {
	}

	public function process_reminder(){
		$db = new Database();
		$courses = $db->get_courses_start_in_time(); // Get all courses <= time start 24h

		if ( $courses ){
			foreach ( $courses as $course){

				$diff = intval($course->time_start) - current_time('timestamp');
				
				error_log(print_r('Diferencia tiempo',true));
				error_log(print_r($diff,true));

				if ( $diff <= 14400 && $diff > 3600 ){ // 4h Notification
					$meta_notif = get_post_meta($course->id, DCMS_NOTIF_4_COMPLETE, true);
					if ( ! $meta_notif ) {
						$this->process_notification_user($course->id, 4);
						add_post_meta($course->id, DCMS_NOTIF_4_COMPLETE,1 );
					}
				} else if ( $diff > 14400 ){ // 24h Notification
					$meta_notif = get_post_meta($course->id, DCMS_NOTIF_24_COMPLETE, true);
					if ( ! $meta_notif ) {
						$this->process_notification_user($course->id, 24);
						add_post_meta($course->id, DCMS_NOTIF_24_COMPLETE,1 );
					}
				}

			}
		}
	}

	private function process_notification_user($course_id, $hour) {
		$db = new Database();
		$users = $db->get_users_per_course($course_id);
		foreach ( $users as $user ){

			// Save log data
			$data['user_id'] = $user->id;
			$data['course_id'] = $course_id;
			$data['sent'] = 1;
			$data['hour'] = $hour;

			$db->insert_notifications_user($data);
		}
	}

}
