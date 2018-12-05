<?php

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');

/**
 * Very inefficient class. Needs to be refactored as an object of parameters and return objects.
 */
class mod_videofiles_external extends external_api {
    
     public static function name_parameters() {
        //  VALUE_REQUIRED, VALUE_OPTIONAL, or VALUE_DEFAULT. If not mentioned, a value is VALUE_REQUIRED 
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'id of course', VALUE_OPTIONAL))
        );
    }
    
    public static function name_is_allowed_from_ajax() { return true; }

    public static function name_returns() {
        return new external_single_structure(
                array(
                    'data' => new external_value(PARAM_TEXT, 'username')
                )
        );
    }
    public static function name($data) {
        return array(
            'data' => 'video db'
        );
    }

/**
 * Get the metadata of videos that are related to a course
 */    
     public static function get_video_pool_parameters() {
        //  VALUE_REQUIRED, VALUE_OPTIONAL, or VALUE_DEFAULT. If not mentioned, a value is VALUE_REQUIRED 
        return new external_function_parameters(
            array(
                'data' => new external_single_structure(
                    array(
                        'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_OPTIONAL),
                    )
                )
            )
        );
    }
    public static function get_video_pool_returns() {
        return new external_single_structure(
                array(
                    'data' => new external_value(PARAM_TEXT, 'data')
                )
        );
    }
    public static function get_video_pool($data) {
        /*global $CFG, $DB, $USER;
        
        $transaction = $DB->start_delegated_transaction(); 
        $table = "videofiles_videofile"; 
        $res = $DB->get_records($table); 
        $transaction->allow_commit();*/
        return array(
            'data' => 'hello'//json_encode($res)
        );
    }
    //public static function get_video_pool_is_allowed_from_ajax() { return true; }
  

 

}


?>