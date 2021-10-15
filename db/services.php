<?php

/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwstemplate
 * @copyright  2017 Niels Seidel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$functions = array(
        'mod_videofiles_video_pool' => array(
                'classname'   => 'mod_videofiles_external',
                'methodname'  => 'get_video_pool',
                'classpath'   => 'mod/videofiles/classes/external.php',
                'description' => 'Getter pool of uploaded videos from the plugin videofiles',
                'type'        => 'read',
                'ajax'        => true 
        )
);
