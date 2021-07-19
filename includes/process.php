<?php

namespace dcms\notifications\includes;

use dcms\notifications\includes\Database;

class Process{

    public function __construct(){
        error_log(print_r('Process',true));

        $this->process_email_notification(5926, 5917, 1);

        // Para las lecciones
        add_action('dcms_complete_lesson', [$this, 'lesson_passed'], 10, 3);
        // Para las preguntas
        add_action('stm_lms_quiz_passed', [$this, 'quiz_passed'], 10, 3);
        // Para las asignaciones
        add_action('updated_post_meta', [$this, 'save_post_assigment_meta'], 20, 4);
    }


    // Para las lecciones
    public function lesson_passed($user_id, $lesson_id, $course_id){
        $this->process_email_notification($lesson_id, $course_id, $user_id);
    }

    // Para los cuestionarios
    public function quiz_passed($user_id, $quiz_id, $progress){
        if($progress == 100) {
            $db = new Database();
            $course_id = $db->get_lat_course_id($quiz_id);
            $this->process_email_notification($quiz_id, $course_id, $user_id);
        }
    }

    // Para las asignaciones
    public function save_post_assigment_meta($meta_id, $object_id, $meta_key, $meta_value){
        if ( $meta_key == 'status' && $meta_value = 'passed'){
            $user_id    = get_post_meta($object_id, 'student_id', true);
            $course_id  = get_post_meta($object_id, 'course_id', true);
            $assigment_id = get_post_meta($object_id, 'assignment_id', true);

            $this->process_email_notification($assigment_id, $course_id, $user_id);
        }
    }


    // Métodos complementarios
    // --------------------------

    // Send email notifications
    public function process_email_notification($lesson_id, $course_id, $user_id){

        $result = $this->is_finish_course($lesson_id, $course_id);
        $options = get_option( 'dcms-notif_options' );

        $enviar_modulo = isset($options['dcms_enable_email_module']);
        $enviar_curso = isset($options['dcms_enable_email_course']);

        if ( ! $enviar_modulo && ! $enviar_curso ) return;

        // include_once(DCMS_NOTIF_PATH.'../../../wp-includes/pluggable.php');

        // User data
        $db = new Database;
        $user = $db->get_user_data($user_id);

        error_log(print_r($user,true));

        $name = $user->display_name;
        $email = $user->user_email;
        // Course data
        $course_title = get_the_title($course_id);

        if ( $enviar_modulo && gettype($result) == 'string') {
            $module_title = $result;
            $this->send_email($name,$email,$course_title,$module_title);
        }

        if ( $enviar_curso &&  $result === true ){
            $this->send_email($name,$email,$course_title);
        }

    }


    private function send_email( $name, $email, $course_title, $module_title = ''){
        error_log('Email enviado 🚀🚀');
        error_log($name.'-'.$email.'-'.$course_title.'-'.$module_title);

        // $options = get_option( 'dcms_events_options' );

        // add_filter( 'wp_mail_from', function(){
        //     $options = get_option( 'dcms_events_options' );
        //     return $options['dcms_sender_email'];
        // });
        // add_filter( 'wp_mail_from_name', function(){
        //     $options = get_option( 'dcms_events_options' );
        //     return $options['dcms_sender_name'];
        // });

        // $headers = ['Content-Type: text/html; charset=UTF-8'];
        // $subject = $options['dcms_subject_email'];
        // $body    = $options['dcms_text_email'];
        // $body = str_replace( '%name%', $name, $body );
        // $body = str_replace( '%event_title%', $event_title, $body );
        // $body = str_replace( '%event_extracto%', $event_excerpt, $body );

        // $str = '';
        // if ($convivientes){
        //     $str = "Convivientes: <br>";
        //     $str .= "<ul>";
        //     foreach ($convivientes as $key => $value){
        //         $str .= "<li> ID: " . $key . " - " . $value . "</li>";
        //     }
        //     $str .= "</ul>";
        // }
        // $body = str_replace( '%convivientes%', $str, $body );


        // return wp_mail( $email, $subject, $body, $headers );
    }





    // Función que dada una función encuentra:
    // Si es el final de una sección devuelte el nombre de la sección
    // Si es el final del curso devuelve true, caso contrario false
    private function is_finish_course($item_id, $course_id = 0){
        if ( $course_id ){
            $curriculum = get_post_meta($course_id, 'curriculum', true);
            $arr = explode(',', $curriculum);

            $key = array_search($item_id, $arr);

            if ( ! $key ) return false;

            if ( isset($arr[$key + 1])){
                $result = '';
                if ( ! is_numeric($arr[$key+1]) ) {
                    for ($i=$key ; $i>=0; $i--) {
                        if ( ! is_numeric($arr[$i]) ){
                            $result = $arr[$i];
                            break;
                        }
                    }
                    return $result;
                }
            } else if ( $key == count($arr) - 1){
                return true;
            }
        }
        return false;
    }

}
