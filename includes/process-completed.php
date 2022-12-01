<?php

namespace dcms\notifications\includes;

// Para las notificaciones de secciones, módulos y cursos al completarse

class ProcessCompleted {

	public function __construct() {
		// Para las lecciones
		add_action( 'dcms_complete_lesson', [ $this, 'lesson_passed' ], 10, 3 );
		// Para las preguntas
		add_action( 'stm_lms_quiz_passed', [ $this, 'quiz_passed' ], 10, 3 );
		// Para las asignaciones
		add_action( 'updated_post_meta', [ $this, 'save_post_assigment_meta' ], 20, 4 );
	}

	// Para las lecciones
	public function lesson_passed( $user_id, $lesson_id, $course_id ) {
		$this->process_email_notification( $lesson_id, $course_id, $user_id );
	}

	// Para los cuestionarios
	public function quiz_passed( $user_id, $quiz_id, $progress ) {
		if ( $progress == 100 ) {
			$db        = new Database();
			$course_id = $db->get_last_course_id( $quiz_id );
			$this->process_email_notification( $quiz_id, $course_id, $user_id );
		}
	}

	// Para las asignaciones
	public function save_post_assigment_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( $meta_key == 'status' && $meta_value == 'passed' ) {
			$user_id      = get_post_meta( $object_id, 'student_id', true );
			$course_id    = get_post_meta( $object_id, 'course_id', true );
			$assigment_id = get_post_meta( $object_id, 'assignment_id', true );

			$this->process_email_notification( $assigment_id, $course_id, $user_id );
		}
	}


	// Métodos complementarios
	// --------------------------

	// Send email notifications
	public function process_email_notification( $lesson_id, $course_id, $user_id ) {

		$result  = $this->is_finish_course( $lesson_id, $course_id );
		$options = get_option( 'dcms-notif_options' );

		$send_section = isset( $options['dcms_enable_email_section'] );
		$send_module  = isset( $options['dcms_enable_email_module'] );
		$send_course  = isset( $options['dcms_enable_email_course'] );

		if ( ! $send_section && ! $send_module && ! $send_course ) {
			return;
		}

		// User data
		$db    = new Database;
		$user  = $db->get_user_data( $user_id );
		$name  = $user->display_name;
		$email = $user->user_email;

		// Course data
		$course_title  = get_the_title( $course_id );
		$section_title = $result;

		if ( $send_section && gettype( $result ) == 'string' ) {
			$this->send_email( $name, $email, $section_title, '', $course_title );
		}

		if ( $result === true ) {
			$is_module = $db->is_module_course( $course_id );

			if ( $send_course && ! $is_module ) {
				$this->send_email( $name, $email, '', '', $course_title );
			}
			if ( $send_module && $is_module ) {
				$this->send_email( $name, $email, '', $course_title, '' );
			}
		}
	}

	// Función para enviar el correo
	private function send_email( $name, $email, $section_title = '', $module_title = '', $course_title = '' ) {
		$this->sender_configuration();

		$options = get_option( 'dcms-notif_options' );

		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		if ( ! empty( $section_title ) ) { // correo para fin de seccion
			$subject = $options['dcms_subject_email_section'];
			$body    = $options['dcms_text_email_section'];
		} elseif ( ! empty( $module_title ) ) { // correo para fin de módulo
			$subject = $options['dcms_subject_email_module'];
			$body    = $options['dcms_text_email_module'];
		} else { // correo para fin de curso
			$subject = $options['dcms_subject_email_course'];
			$body    = $options['dcms_text_email_course'];
		}

		$body = str_replace( '%name%', $name, $body );
		$body = str_replace( '%course_title%', $course_title, $body );
		$body = str_replace( '%section_title%', $section_title, $body );
		$body = str_replace( '%module_title%', $module_title, $body );

		return wp_mail( $email, $subject, $body, $headers );
	}

	// Si es el final de una sección devuelte el nombre de la sección
	// Si es el final del curso devuelve true, caso contrario false
	private function is_finish_course( $item_id, $course_id = 0 ) {
		if ( $course_id ) {
			$curriculum = get_post_meta( $course_id, 'curriculum', true );
			$arr        = explode( ',', $curriculum );

			$key = array_search( $item_id, $arr );

			if ( ! $key ) {
				return false;
			}

			if ( isset( $arr[ $key + 1 ] ) ) {
				$result = '';
				if ( ! is_numeric( $arr[ $key + 1 ] ) ) {
					for ( $i = $key; $i >= 0; $i -- ) {
						if ( ! is_numeric( $arr[ $i ] ) ) {
							$result = $arr[ $i ];
							break;
						}
					}

					return $result;
				}
			} else if ( $key == count( $arr ) - 1 ) {
				return true;
			}
		}

		return false;
	}

	// Sender configuration
	private function sender_configuration() {
		add_filter( 'wp_mail_from', function () {
			$options = get_option( 'dcms-notif_options' );

			return $options['dcms_sender_email'];
		} );
		add_filter( 'wp_mail_from_name', function () {
			$options = get_option( 'dcms-notif_options' );

			return $options['dcms_sender_name'];
		} );
	}

}
