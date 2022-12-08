<?php

namespace dcms\notifications\includes;

class Database {
	private $wpdb;
	private $table_postmeta;
	private $table_posts;
	private $table_user;
	private $table_log_emails_users;

	public function __construct() {
		global $wpdb;

		$this->wpdb                     = $wpdb;
		$this->table_posts              = $this->wpdb->prefix . 'posts';
		$this->table_postmeta           = $this->wpdb->prefix . 'postmeta';
		$this->table_user               = $this->wpdb->prefix . 'users';
		$this->table_notification_users = $this->wpdb->prefix . 'notification_emails_users';
	}

	// Get all data
	public function get_last_course_id( $item_id ) {
		$sql = "SELECT post_id FROM {$this->table_postmeta}
                                WHERE meta_key = 'curriculum' AND
                                        meta_value like '%{$item_id}%'
                                        ORDER BY post_id DESC LIMIT 1";

		return $this->wpdb->get_var( $sql );
	}

	// Get user
	public function get_user_data( $id_user ) {
		$sql = "SELECT display_name, user_email FROM {$this->table_user} WHERE id = {$id_user}";

		return $this->wpdb->get_row( $sql );
	}

	public function is_module_course( $id_course ) {
		$sql = "SELECT post_parent FROM {$this->wpdb->prefix}posts WHERE ID = $id_course";

		return boolval( $this->wpdb->get_var( $sql ) ?? 0 );
	}

	// Create table log sent emails reminders
	public function create_table_notification_user() {
		$sql = "CREATE TABLE IF NOT EXISTS {$this->table_notification_users} (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`user_id` bigint(20) NOT NULL,
				`course_id` mediumint(9) NOT NULL,
				`sent` tinyint(1) DEFAULT NULL,
				`hour` smallint(6) NOT NULL,
				`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
                ) {$this->wpdb->get_charset_collate()};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	// Get start courses <= time, default $time_seconds = 24h = 86400s
	public function get_courses_start_in_time( $time_seconds = 86400 ) {

		$unix_time_zone = dcms_strtotime(null);
		
		$sql = "SELECT p.ID AS id, 
       					p.post_title AS course_name,
       					pm.meta_value AS time_start
						FROM {$this->table_posts} p
						INNER JOIN {$this->table_postmeta} pm ON p.ID = pm.post_id
						WHERE p.post_type = 'stm-courses' 
						AND p.post_parent = 0
						AND pm.meta_key = '" . DCMS_NOTIF_COURSE_TIME . "' 
						AND CAST(pm.meta_value AS SIGNED) - $unix_time_zone <= $time_seconds
						AND CAST(pm.meta_value AS SIGNED) - $unix_time_zone > 0";

		return $this->wpdb->get_results( $sql );
	}

	// Get all user per course id
	public function get_users_per_course( $course_id ) {
		$sql = "SELECT u.ID AS id, 
       				   u.display_name AS name, 
       				   u.user_email AS email
				FROM {$this->wpdb->prefix}stm_lms_user_courses uc
				INNER JOIN {$this->table_user} u ON uc.user_id = u.ID
				WHERE course_id = $course_id
				AND user_id NOT IN (
					SELECT user_id 
					FROM {$this->table_notification_users} 
					WHERE course_id = $course_id
				)";

		return $this->wpdb->get_results( $sql );
	}

	// Insert reminders in table
	public function insert_notifications_user( $data ) {
		return $this->wpdb->insert( $this->table_notification_users, $data, [ '%d', '%d', '%d', '%d' ] );
	}

}
