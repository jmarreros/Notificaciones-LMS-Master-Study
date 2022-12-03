<?php

namespace dcms\notifications\includes;

/**
 * Class for creating the settings email new users and change seats
 */
class Settings{

    public function __construct(){
        add_action('admin_init', [$this, 'init_configuration']);
    }

    // Register seccions and fields
    public function init_configuration(){
        register_setting('dcms_notif_options_bd', 'dcms-notif_options' );
        $this->fields_email_general();
	    $this->fields_email_section();
        $this->fields_email_module();
        $this->fields_email_course();
		$this->fields_email_reminder4h();
	    $this->fields_email_reminder24h();
    }

    // Fields email general configuration
    private function fields_email_general(){

        add_settings_section('dcms_email_section',
                        __('Configuración general', 'dcms-notifications'),
                                [$this,'dcms_section_cb'],
                                'dcms_notif_sfields' );

        add_settings_field('dcms_sender_email',
                            __('Correo Emisor', 'dcms-notifications'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_notif_sfields',
                            'dcms_email_section',
                            [
                                'dcms_option' => 'dcms-notif_options',
                                'label_for' => 'dcms_sender_email',
                                'required' => true
                            ]
        );

        add_settings_field('dcms_sender_name',
                            __('Nombre emisor', 'dcms-notifications'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_notif_sfields',
                            'dcms_email_section',
                            [
                              'dcms_option' => 'dcms-notif_options',
                              'label_for' => 'dcms_sender_name',
                              'required' => true
                            ]
        );

    }


	private function fields_email_section(){
		add_settings_section('dcms_email_section_section',
			__('Configuración correo fin sección', 'dcms-notifications'),
			[$this,'dcms_section_cb'],
			'dcms_notif_sfields' );


		add_settings_field('dcms_enable_email_section',
			__('Habilitar', 'dcms-notifications'),
			[$this, 'dcms_section_check_cb'],
			'dcms_notif_sfields',
			'dcms_email_section_section',
			[
				'dcms_option' => 'dcms-notif_options',
				'label_for' => 'dcms_enable_email_section',
			]
		);

		add_settings_field('dcms_subject_email_section',
			__('Asunto correo', 'dcms-notifications'),
			[$this, 'dcms_section_input_cb'],
			'dcms_notif_sfields',
			'dcms_email_section_section',
			[
				'dcms_option' => 'dcms-notif_options',
				'label_for' => 'dcms_subject_email_section',
				'required' => true
			]
		);

		add_settings_field('dcms_text_email_section',
			__('Texto correo', 'dcms-notifications'),
			[$this, 'dcms_section_textarea_field'],
			'dcms_notif_sfields',
			'dcms_email_section_section',
			[
				'dcms_option' => 'dcms-notif_options',
				'label_for' => 'dcms_text_email_section',
				'description' => __('Puedes usar las siguientes variables que se pueden reemplazar:
                             %name% (nombre de usuario),
                             %course_title% (título del curso),
                             %section_title% (título de la seccion),
                             ', 'dcms-notifications')
			]
		);
	}


    private function fields_email_module(){

        add_settings_section('dcms_email_section_module',
                                __('Configuración correo fin de módulo', 'dcms-notifications'),
                                [$this,'dcms_section_cb'],
                                'dcms_notif_sfields' );



        add_settings_field('dcms_enable_email_module',
                            __('Habilitar', 'dcms-notifications'),
                            [$this, 'dcms_section_check_cb'],
                            'dcms_notif_sfields',
                            'dcms_email_section_module',
                            [
                                'dcms_option' => 'dcms-notif_options',
                                'label_for' => 'dcms_enable_email_module',
                            ]
        );

        add_settings_field('dcms_subject_email_module',
                            __('Asunto correo', 'dcms-notifications'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_notif_sfields',
                            'dcms_email_section_module',
                            [
                              'dcms_option' => 'dcms-notif_options',
                              'label_for' => 'dcms_subject_email_module',
                              'required' => true
                            ]
        );

        add_settings_field('dcms_text_email_module',
                            __('Texto correo', 'dcms-notifications'),
                            [$this, 'dcms_section_textarea_field'],
                            'dcms_notif_sfields',
                            'dcms_email_section_module',
                            [
                             'dcms_option' => 'dcms-notif_options',
                             'label_for' => 'dcms_text_email_module',
                             'description' => __('Puedes usar las siguientes variables que se pueden reemplazar:
                             %name% (nombre de usuario),
                             %module_title% (título del módulo),
                             ', 'dcms-notifications')
                            ]
        );

    }



    private function fields_email_course(){

        add_settings_section('dcms_email_section_course',
                                __('Configuración correo fin de curso', 'dcms-notifications'),
                                [$this,'dcms_section_cb'],
                                'dcms_notif_sfields' );



        add_settings_field('dcms_enable_email_course',
                            __('Habilitar', 'dcms-notifications'),
                            [$this, 'dcms_section_check_cb'],
                            'dcms_notif_sfields',
                            'dcms_email_section_course',
                            [
                                'dcms_option' => 'dcms-notif_options',
                                'label_for' => 'dcms_enable_email_course',
                            ]
        );

        add_settings_field('dcms_subject_email_course',
                            __('Asunto correo', 'dcms-notifications'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_notif_sfields',
                            'dcms_email_section_course',
                            [
                              'dcms_option' => 'dcms-notif_options',
                              'label_for' => 'dcms_subject_email_course',
                              'required' => true
                            ]
        );

        add_settings_field('dcms_text_email_course',
                            __('Texto correo', 'dcms-notifications'),
                            [$this, 'dcms_section_textarea_field'],
                            'dcms_notif_sfields',
                            'dcms_email_section_course',
                            [
                             'dcms_option' => 'dcms-notif_options',
                             'label_for' => 'dcms_text_email_course',
                             'description' => __('Puedes usar las siguientes variables que se pueden reemplazar:
                             %name% (nombre de usuario),
                             %course_title% (título del curso),
                             ', 'dcms-notifications')
                            ]
        );
    }



	private function fields_email_reminder4h(){

		add_settings_section('dcms_email_section_reminder4h',
			__('Configuración correo recordatorio 4h', 'dcms-notifications'),
			[$this,'dcms_section_cb'],
			'dcms_notif_sfields' );


		add_settings_field('dcms_enable_email_reminder4h',
			__('Habilitar', 'dcms-notifications'),
			[$this, 'dcms_section_check_cb'],
			'dcms_notif_sfields',
			'dcms_email_section_reminder4h',
			[
				'dcms_option' => 'dcms-notif_options',
				'label_for' => 'dcms_enable_email_reminder4h',
			]
		);

		add_settings_field('dcms_subject_email_reminder4h',
			__('Asunto correo', 'dcms-notifications'),
			[$this, 'dcms_section_input_cb'],
			'dcms_notif_sfields',
			'dcms_email_section_reminder4h',
			[
				'dcms_option' => 'dcms-notif_options',
				'label_for' => 'dcms_subject_email_reminder4h',
				'required' => true
			]
		);

		add_settings_field('dcms_text_email_reminder4h',
			__('Texto correo', 'dcms-notifications'),
			[$this, 'dcms_section_textarea_field'],
			'dcms_notif_sfields',
			'dcms_email_section_reminder4h',
			[
				'dcms_option' => 'dcms-notif_options',
				'label_for' => 'dcms_text_email_reminder4h',
				'description' => __('Puedes usar las siguientes variables que se pueden reemplazar:
                             %name% (nombre de usuario),
                             %course_title% (título del curso),
                             ', 'dcms-notifications')
			]
		);
	}


	private function fields_email_reminder24h(){

		add_settings_section('dcms_email_section_reminder24h',
			__('Configuración correo recordatorio 24h', 'dcms-notifications'),
			[$this,'dcms_section_cb'],
			'dcms_notif_sfields' );


		add_settings_field('dcms_enable_email_reminder24h',
			__('Habilitar', 'dcms-notifications'),
			[$this, 'dcms_section_check_cb'],
			'dcms_notif_sfields',
			'dcms_email_section_reminder24h',
			[
				'dcms_option' => 'dcms-notif_options',
				'label_for' => 'dcms_enable_email_reminder24h',
			]
		);

		add_settings_field('dcms_subject_email_reminder24h',
			__('Asunto correo', 'dcms-notifications'),
			[$this, 'dcms_section_input_cb'],
			'dcms_notif_sfields',
			'dcms_email_section_reminder24h',
			[
				'dcms_option' => 'dcms-notif_options',
				'label_for' => 'dcms_subject_email_reminder24h',
				'required' => true
			]
		);

		add_settings_field('dcms_text_email_reminder24h',
			__('Texto correo', 'dcms-notifications'),
			[$this, 'dcms_section_textarea_field'],
			'dcms_notif_sfields',
			'dcms_email_section_reminder24h',
			[
				'dcms_option' => 'dcms-notif_options',
				'label_for' => 'dcms_text_email_reminder24h',
				'description' => __('Puedes usar las siguientes variables que se pueden reemplazar:
                             %name% (nombre de usuario),
                             %course_title% (título del curso),
                             ', 'dcms-notifications')
			]
		);
	}


	// Métodos auxiliares genéricos

    // Callback section
    public function dcms_section_cb(){
		echo '<hr/>';
	}

    // Callback input field callback
    public function dcms_section_input_cb($args){
        $dcms_option = $args['dcms_option'];
        $id = $args['label_for'];
        $req = isset($args['required']) ? 'required' : '';
        $class = isset($args['class']) ? "class='".$args['class']."'" : '';
        $desc = isset($args['description']) ? $args['description'] : '';

        $options = get_option( $dcms_option );
        $val = isset( $options[$id] ) ? $options[$id] : '';

        printf("<input id='%s' name='%s[%s]' class='regular-text' type='text' value='%s' %s %s>",
                $id, $dcms_option, $id, $val, $req, $class);

        if ( $desc ) printf("<p class='description'>%s</p> ", $desc);

    }

    // Callback field textarea
    public function dcms_section_textarea_field( $args ){
        $dcms_option = $args['dcms_option'];
        $id = $args['label_for'];
        $desc = isset($args['description']) ? $args['description'] : '';

        $options = get_option( $dcms_option );
        $val = $options[$id];
        printf("<textarea id='%s' name='%s[%s]' rows='5' cols='80' >%s</textarea><p class='description'>%s</p>", $id, $dcms_option, $id, $val, $desc);
	}

    // Callback field checkbox
    public function dcms_section_check_cb( $args ){
        $dcms_option = $args['dcms_option'];
        $id = $args['label_for'];
        $options = get_option( $dcms_option );
        $val = isset( $options[$id] ) ? 'checked':'';

        printf("<input id='%s' name='%s[%s]' type='checkbox' %s>",
                $id, $dcms_option, $id, $val);
	}

}
