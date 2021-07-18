<?php

namespace dcms\notifications\includes;

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

        // Ubicar si la lección es la última de un módulo
        // Ubicar si la lección es la última de un curso

        $result = $this->is_finish_course($lesson_id, $course_id);

        error_log('Lección ' . $lesson_id . ' completada por usuario: ' . $user_id. ' Curso:'.$course_id);
        error_log('Resultado: ' . $result );
    }

    // Para las preguntas
    public function quiz_passed($user_id, $quiz_id, $progress){
        if($progress == 100) {

            error_log('Quizz ' . $quiz_id . ' completada por usuario: ' . $user_id);
        }
    }

    // Para las asignaciones
    public function save_post_assigment_meta($meta_id, $object_id, $meta_key, $meta_value){
        if ( $meta_key == 'status' ){
            error_log('Meta value: '. $meta_value);
            error_log(print_r($object_id,true));
        }
    }

    // Función que dada una funcióin encuentra:
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


    // Si es el final del curso el que se acaba de completar
    // private function is_finish_course($item_id, $course_id = 0){
    //     if ( $course_id ){

    //         $curriculum = get_post_meta($course_id, 'curriculum', true);
    //         $arr = explode(',', $curriculum);

    //         if ( $count($arr) ){
    //             if ( $item_id == (int)$arr[$count($arr) - 1]){
    //                 return true;
    //             }
    //         }
    //     }
    //     return false;
    // }

