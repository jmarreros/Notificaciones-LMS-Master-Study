<?php

namespace dcms\notifications\includes;

class Plugin{

    public function __construct(){
        register_activation_hook( DCMS_NOTIF_BASE_NAME, [ $this, 'dcms_activation_plugin'] );
        register_deactivation_hook( DCMS_NOTIF_BASE_NAME, [ $this, 'dcms_deactivation_plugin'] );
    }

    // Activate plugin - create options and database table
    public function dcms_activation_plugin(){
		$db = new Database();
		$db->create_table_notification_user();

	    // Create cron
	    if( ! wp_next_scheduled( 'dcms_caes_notifications_hook' ) ) {
		    wp_schedule_event( current_time( 'timestamp' ), 'dcms_caes_interval', 'dcms_caes_notifications_hook' );
	    }
    }

    // Deactivate plugin
    public function dcms_deactivation_plugin(){
	    wp_clear_scheduled_hook( 'dcms_caes_notifications_hook' );
    }

}
