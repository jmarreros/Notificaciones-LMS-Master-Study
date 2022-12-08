<?php
/*
Plugin Name: Notificaciones de correo para LMS MasterStudy
Plugin URI: https://decodecms.com
Description: Plugin for sending notifications emails for MasterStudy LMS Plugin, requiere do_action('dcms_complete_lesson') como parte del plugin LMS
Version: 1.0
Author: Jhon Marreros Guzmán
Author URI: https://decodecms.com
Text Domain: dcms-notifications
Domain Path: languages
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

namespace dcms\notifications;

use dcms\notifications\includes\Plugin;
use dcms\notifications\includes\Submenu;
use dcms\notifications\includes\Database;
use dcms\notifications\includes\Settings;
use dcms\notifications\includes\MetaboxTime;
use dcms\notifications\includes\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin class to handle settings constants and loading files
**/
final class Loader{

	// Define all the constants we need
	public function define_constants(){
		define ('DCMS_NOTIF_VERSION', '1.0');
		define ('DCMS_NOTIF_PATH', plugin_dir_path( __FILE__ ));
		define ('DCMS_NOTIF_URL', plugin_dir_url( __FILE__ ));
		define ('DCMS_NOTIF_BASE_NAME', plugin_basename( __FILE__ ));
		define ('DCMS_NOTIF_SUBMENU', 'options-general.php');
		define ('DCMS_NOTIF_COURSE_TIME', 'dcms_course_time');
		define ('DCMS_NOTIF_24H_COMPLETE', 'dcms_notif_24_complete');
		define ('DCMS_NOTIF_4H_COMPLETE', 'dcms_notif_4_complete');
//		define ('DCMS_NOTIF_72H_NOT_START', 'dcms_alert_72h_not_start');
	}

	// Load all the files we need
	public function load_includes(){
		include_once ( DCMS_NOTIF_PATH . '/helpers/functions.php');
		include_once ( DCMS_NOTIF_PATH . '/includes/plugin.php');
		include_once ( DCMS_NOTIF_PATH . '/includes/submenu.php');
		include_once ( DCMS_NOTIF_PATH . '/includes/process-completed.php');
		include_once ( DCMS_NOTIF_PATH . '/includes/process-auto.php');
		include_once ( DCMS_NOTIF_PATH . '/includes/database.php');
		include_once ( DCMS_NOTIF_PATH . '/includes/settings.php');
		include_once ( DCMS_NOTIF_PATH . '/includes/metabox-time.php');
		include_once ( DCMS_NOTIF_PATH . '/includes/cron.php');
	}

	// Load tex domain
	public function load_domain(){
		add_action('plugins_loaded', function(){
			$path_languages = dirname(DCMS_NOTIF_BASE_NAME).'/languages/';
			load_plugin_textdomain('dcms-notifications', false, $path_languages );
		});
	}

	// Add link to plugin list
	public function add_link_plugin(){
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ){
			return array_merge( array(
				'<a href="' . esc_url( admin_url( DCMS_NOTIF_SUBMENU . '?page=notifications' ) ) . '">' . __( 'Configuración', 'dcms-notifications' ) . '</a>'
			), $links );
		} );
	}

	// Initialize all
	public function init(){
		$this->define_constants();
		$this->load_includes();
		$this->load_domain();
		$this->add_link_plugin();
		new Plugin();
		new Cron();
		new SubMenu();
		new Database();
		new Settings();
		new MetaboxTime();
	}

}

$dcms_notifications_process = new Loader();
$dcms_notifications_process->init();


