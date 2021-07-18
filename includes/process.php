<?php

namespace dcms\notifications\includes;

class Process{

    public function __construct(){
        error_log(print_r('Process',true));

        // Para las lecciones
        add_action('stm_lms_lesson_passed', [$this, 'lesson_passed'], 10, 2);
        // Para las preguntas
        add_action('stm_lms_quiz_passed', [$this, 'quiz_passed'], 10, 3);
        // Para las asignaciones
        add_action('updated_post_meta', [$this, 'save_post_assigment_meta'], 20, 4);
    }


    // Para las lecciones
    public function lesson_passed($user_id, $lesson_id){

        // Ubicar si la lección es la última de un módulo
        // Ubicar si la lección es la última de un curso

        error_log('Lección ' . $lesson_id . ' completada por usuario: ' . $user_id);
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

}
