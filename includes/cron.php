<?php

namespace dcms\notifications\includes;

// Class for manage Cron Job
class Cron{

	public function __construct(){
		add_filter( 'cron_schedules', [ $this, 'dcms_custom_schedule' ]);
		add_action( 'dcms_caes_notifications_hook', [ $this, 'dcms_cron_notif_process' ] ); // For remainder
		add_action( 'dcms_caes_alert_hook', [ $this, 'dcms_cron_alert_process' ] ); // For alerts
	}

	// Add new schedule
	public function dcms_custom_schedule( $schedules ) {

		$schedules['dcms_caes_interval'] = array(
			'interval' => 1800,
			'display' => '30 minutes'
		);

		return $schedules;
	}

	// Cron process, every dcms_caes_interval (30 min), for remainders
	public function dcms_cron_notif_process() {
		$process = new ProcessAuto();
		$process->process_reminder();
	}

	// Cron process, every 24 hours, for alerts
	public function dcms_cron_alert_process(){
		$process = new ProcessAuto();
		$process->alert_not_start_course();
	}

}