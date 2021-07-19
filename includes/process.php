<?php

namespace dcms\notifications\includes;

use dcms\notifications\includes\Database;

class Process{

    public function __construct(){
        error_log(print_r('Process',true));

        // Para las lecciones
        add_action('dcms_complete_lesson', [$this, 'lesson_passed'], 10, 3);
        // Para las preguntas
        add_action('stm_lms_quiz_passed', [$this, 'quiz_passed'], 10, 3);
        // Para las asignaciones
        add_action('updated_post_meta', [$this, 'save_post_assigment_meta'], 20, 4);
    }


    // Para las lecciones
    public function lesson_passed($user_id, $lesson_id, $course_id){
        $this->send_email_notification($lesson_id, $course_id, $user_id);
    }

    // Para los cuestionarios
    public function quiz_passed($user_id, $quiz_id, $progress){
        if($progress == 100) {
            $db = new Database();
            $course_id = $db->get_last_course_id($quiz_id);
            $this->send_email_notification($quiz_id, $course_id, $user_id);
        }
    }

    // Para las asignaciones
    public function save_post_assigment_meta($meta_id, $object_id, $meta_key, $meta_value){
        if ( $meta_key == 'status' && $meta_value = 'passed'){
            $user_id    = get_post_meta($object_id, 'student_id', true);
            $course_id  = get_post_meta($object_id, 'course_id', true);
            $assigment_id = get_post_meta($object_id, 'assignment_id', true);

            $this->send_email_notification($assigment_id, $course_id, $user_id);
        }
    }


    // Métodos complementarios
    // --------------------------

    // Send email notifications
    private function send_email_notification($lesson_id, $course_id, $user_id){

        $result = $this->is_finish_course($lesson_id, $course_id);

        if ( gettype($result) == 'string') {
            error_log('Fin del módulo: ' . $result );
            error_log('Lección ' . $lesson_id . ' completada por usuario: ' . $user_id. ' Curso:'.$course_id);
        } else {
            if ($result) {
                error_log('Fin del curso');
                error_log('Lección ' . $lesson_id . ' completada por usuario: ' . $user_id. ' Curso:'.$course_id);
            }
        }
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
