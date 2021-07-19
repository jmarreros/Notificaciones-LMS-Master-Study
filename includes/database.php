<?php

namespace dcms\notifications\includes;

class Database{
    private $wpdb;
    private $table_postmeta;

    public function __construct(){
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_postmeta = $this->wpdb->prefix.'postmeta';
    }

    // Get all data
    public function get_last_course_id($item_id){
        $sql= "SELECT post_id FROM {$this->table_postmeta}
                                WHERE meta_key = 'curriculum' AND
                                        meta_value like '%{$item_id}%'
                                        ORDER BY post_id DESC LIMIT 1";

        return $this->wpdb->get_var($sql);
    }

}
