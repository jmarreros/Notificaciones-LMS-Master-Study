<?php

namespace dcms\notifications\includes;

class Database {
	private $wpdb;
	private $table_postmeta;
	private $table_posts;
	private $table_user;

	public function __construct() {
		global $wpdb;

		$this->wpdb           = $wpdb;
		$this->table_posts    = $this->wpdb->prefix . 'posts';
		$this->table_postmeta = $this->wpdb->prefix . 'postmeta';
		$this->table_user     = $this->wpdb->prefix . 'users';
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

	// Get start courses <= time, default $time_seconds = 24h = 86400s
	public function get_courses_start_in_time($time_seconds = 86400) {
		$sql          = "SELECT p.post_id 
						FROM {$this->table_posts} p
						INNER JOIN {$this->table_postmeta} pm ON p.ID = pm.post_id
						WHERE pm.meta_key = '" . DCMS_NOTIF_COURSE_TIME . "' 
						AND CAST(pm.meta_value AS UNSIGNED) - UNIX_TIMESTAMP() <= $time_seconds";

		return $this->wpdb->get_col( $sql );
	}

}
