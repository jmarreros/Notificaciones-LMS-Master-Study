<?php

namespace dcms\notifications\includes;

/**
 * Class for creating a dashboard submenu
 */
class Submenu{
    // Constructor
    public function __construct(){
        add_action('admin_menu', [$this, 'register_submenu']);
    }

    // Register submenu
    public function register_submenu(){
        add_submenu_page(
            DCMS_SUBMENU,
            __('Notificaciones LMS','dcms-notifications'),
            __('Notificaciones LMS','dcms-notifications'),
            'manage_options',
            'notifications',
            [$this, 'submenu_page_callback'],
        );
    }

    // Callback, show view
    public function submenu_page_callback(){
        include_once (DCMS_NOTIF_PATH. '/views/main-screen.php');
    }
}