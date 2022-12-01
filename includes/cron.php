<?php

namespace dcms\notifications\includes;

// Class for manage Cron Job
class Cron{

	public function __construct(){
		add_filter( 'cron_schedules', [ $this, 'dcms_custom_schedule' ]);
		add_action( 'dcms_caes_notifications_hook', [ $this, 'dcms_cron_notif_process' ] );
	}

	// Add new schedule
	public function dcms_custom_schedule( $schedules ) {

		$schedules['dcms_caes_interval'] = array(
//			'interval' => 1800,
//			'display' => '30 minutes'
			'interval' => 120,
			'display' => '2 minutes'
		);

		return $schedules;
	}

	// Cron process
	public function dcms_cron_notif_process() {
		error_log(print_r("Se ejecutó el evento cron " . date_i18n("Y-m-d H:i:s"),true) );
	}

}