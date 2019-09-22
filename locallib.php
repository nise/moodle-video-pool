<?php
/**
 * This class provides functionality for the videofile module.
 *
 * @package   mod_videofile
 * @copyright 2013 Jonas Nockert <jonasnockert@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Standard base class for mod_videofile.
 */
class videofile {
    /** @var stdClass The videofile record that contains the
     *                global settings for this videofile instance.
     */
    private $instance;

    /** @var context The context of the course module for this videofile instance
     *               (or just the course if we are creating a new one).
     */
    private $context;

    /** @var stdClass The course this videofile instance belongs to */
    private $course;

    /** @var videofile_renderer The custom renderer for this module */
    private $output;

    /** @var stdClass The course module for this videofile instance */
    private $coursemodule;

    /** @var string modulename Prevents excessive calls to get_string */
    private static $modulename = null;

    /** @var string modulenameplural Prevents excessive calls to get_string */
    private static $modulenameplural = null;

    /**
     * Constructor for the base videofile class.
     *
     * @param mixed $coursemodulecontext context|null The course module context
     *                                   (or the course context if the coursemodule
     *                                   has not been created yet).
     * @param mixed $coursemodule The current course module if it was already loaded,
     *                            otherwise this class will load one from the context
     *                            as required.
     * @param mixed $course The current course if it was already loaded,
     *                      otherwise this class will load one from the context as
     *                      required.
     */
    public function __construct($coursemodulecontext, $coursemodule, $course) {
        global $PAGE;
        global $CFG;
        $this->context = $coursemodulecontext;
        $this->coursemodule = $coursemodule;
        $this->course = $course;
        $this->TMP_DIR = $CFG->dataroot;
    }

    /**
     * Set the course data.
     *
     * @param stdClass $course The course data
     */
    public function set_course(stdClass $course) {
        $this->course = $course;
    }

    /**
     * Add this instance to the database.
     *
     * @param stdClass $formdata The data submitted from the form
     * @return mixed False if an error occurs or the int id of the new instance
     */
    public function add_instance(stdClass $formdata) {
        global $DB;

        file_put_contents('php://stderr', 'rrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr__');
        file_put_contents('php://stderr', $formdata->posters);
        file_put_contents('php://stderr', '__rrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr');
       
        // Add the database record.
        $add = new stdClass();
        $add->name = $formdata->name;
        $add->timemodified = time();
        $add->timecreated = time();
        $add->course = $formdata->course;
        $add->courseid = $formdata->course;
        $add->intro = $formdata->intro;
        $add->introformat = $formdata->introformat;
        $add->width = $formdata->width;
        $add->height = $formdata->height;
        $add->responsive = $formdata->responsive;

        $add->title = $formdata->name;
        $add->creator = $formdata->creator;
        $add->subject = $formdata->subject;
        $add->description = $add->intro;
        $add->publisher = $formdata->publisher;
        $add->contributor = $formdata->contributor;
        $add->date = $formdata->date;
        
        $add->format = 'video';//$formdata->format;
        $add->source = $formdata->source;
        $add->language = $formdata->language;
        $add->relation = $formdata->relation;
        $add->coverage = $formdata->coverage;
        $add->rights = $formdata->rights;
        
        $add->license = $formdata->license;
        $add->institution = $formdata->institution;
        $comma = array(", ", ", ");
        $cleantags = str_replace($comma, ",", $formdata->videotags);
        $add->tags = $cleantags;
        $add->sports = $formdata->sports;
        $add->actors = $formdata->actors;
        $add->movements = $formdata->movements;
        $add->location = $formdata->location;
        
        //$add->compentencies = implode(",",$formdata->compentencies);
        if(isset($formdata->compentencies) && sizeof($formdata->compentencies) > 0){
            $add->compentencies = implode(",",$formdata->compentencies);
        }else{
            $add->compentencies = '';
        }
        //$add->courselevel = implode(",",$formdata->courselevel);
        if(isset($formdata->courselevel) && sizeof($formdata->courselevel) > 0){
            $add->courselevel = implode(",",$formdata->courselevel);
        }else{
            $add->courselevel = '';
        }
        //$add->activities = implode(",",$formdata->activities);
         if(isset($formdata->activities) && sizeof($formdata->activities) > 0){
            $add->activities = implode(",",$formdata->activities);
        }else{
            $add->activities = '';
        }
        //$add->perspectives = implode(",",$formdata->perspectives);
         if(isset($formdata->perspectives) && sizeof($formdata->perspectives) > 0){
            $add->perspectives = implode(",",$formdata->perspectives);
        }else{
            $add->perspectives = '';
        }
        
        // save the file
        file_save_draft_area_files(
            $formdata->videos,
            $this->context->id,
            'mod_videofile',
            'videos',
            0
        );

        $fs = get_file_storage();    
        $videos = $fs->get_area_files($this->context->id,
                                   'mod_videofile',
                                   'videos',
                                   false,
                                   'itemid, filepath, filename',
                                   false);

        foreach ($videos as $file) {
            if ($mimetype = $file->get_mimetype()) {
                $videourl = $this->util_get_file_url($file);
                $fileinfo = array(
                    'component' => $file->get_component(),     // usually = table name
                    'filearea' => $file->get_filearea(),     // usually = table name
                    'itemid' => $file->get_itemid(),               // usually = ID of row in table
                    'contextid' => $file->get_contextid(), // ID of context
                    'filepath' => $file->get_filepath(),           // any path beginning and ending in /
                    'filename' => $file->get_filename() // any filename
                    );
                $videourl = $this->util_get_file_url($file);
                $add->url = '/moodle/pluginfile.php' . $this->accessProtected($videourl, 'slashargument');
                $add->filename = $add->url;
                $add->type = 'video';
                $add->mimetype = $mimetype;  
            }
        } 
        
        // Get file
        $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                            $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
        
        // Read contents
        if ($file) {   
            $target_path =  $this->TMP_DIR . '/'.$file->get_filename();
            $file->copy_content_to($target_path);
            $filesize = filesize($target_path);
            $add->size = $filesize;
            $add->length = $this->getVideoDuration($target_path);
            //$add->posters = $this->extractImages( $target_path, $add->length, 3, str_replace('.mp4', '', $file->get_filename()) );
        } else {
            // file doesn't exist - do something
        }
        
        $returnid = $DB->insert_record('videofile', $add);
        $this->instance = $DB->get_record('videofile',
                                          array('id' => $returnid),
                                          '*',
                                          MUST_EXIST);
        
        // store poster.
        $draftitemid = 0;//$formdata->posters;
        if ($draftitemid) {
            file_save_draft_area_files(
                $draftitemid,
                $this->context->id,
                'mod_videofile',
                'posters',
                0
            );
        }

        // Cache the course record.
        $this->course = $DB->get_record('course',
                                        array('id' => $formdata->course),
                                        '*',
                                        MUST_EXIST);

        return $returnid;
    }

     
    
    /**
     * Utility function for getting a file URL
     *
     * @param stored_file $file
     * @return string file url
     */
    private function util_get_file_url($file) {
        return moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename(),
            false);
    }


    /**
     * Determines the video duration
     */
    function getVideoDuration($filename){
        require_once 'vendor/autoload.php'; 
        $ffprobe = FFMpeg\FFProbe::create(); 
        return round( $ffprobe->format( $filename )->get('duration') );
    }

    /**
     * Extracts a given number of still images from a video
     */
    function extractImages( $videofile, $duration, $n, $name ){
        require_once 'vendor/autoload.php'; 
        $ffmpeg = FFMpeg\FFMpeg::create(array(
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',//$this->config['ffmpeg'],
            'ffprobe.binaries' => '/usr/bin/ffprobe',//$this->config['ffprobe'],
            'timeout'          => 360000, // The timeout for the underlying process
            'ffmpeg.threads'   => 16   // The number of threads that FFMpeg should use
        ));  

        $video = $ffmpeg->open( $videofile );
        
        if(is_dir($this->TMP_DIR) && is_writable($this->TMP_DIR)){
            // generate a thumbnail image
            $video
                ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(round($duration/2)))
                ->save( $this->TMP_DIR . '/still-' . $name . '.jpg');
            // move thumbnail to moodle
            $fs = get_file_storage();
            $file_record = array(
                'contextid'=>$this->context->id, 
                'component'=>'mod_videofile', 
                'filearea'=>'video',
                'itemid'=>0, 
                'filepath'=>'/', 
                'filename'=> 'still-' . $name . '.jpg',
                'timecreated'=>time(), 
                'timemodified'=>time()
            );
            $fs->create_file_from_pathname($file_record, $this->TMP_DIR . '/still-' . $name . '.jpg');
            $posters = $fs->get_area_files($this->context->id,
                                   'mod_videofile',
                                   'poster',
                                   false,
                                   'itemid, filepath, filename',
                                   false);

            foreach ($posters as $file) {
                $poster_url = $this->util_get_file_url($file);
            }
            
            return poster_url;
            
            /*    
            // generate gif animation
            $video 
                ->gif(FFMpeg\Coordinate\TimeCode::fromSeconds(0), new FFMpeg\Coordinate\Dimension(320, 240), 10)
                ->save( $this->TMP_DIR . '/still-' . $name . '_comp.gif');
            $this->send_message('img-ani', 'animation', 100);
    
            // generate preview images per minute
            for($i=0; $i < $duration; $i++){
                $video
                    ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($i))
                    ->save( $this->TMP_DIR . '/preview-' . $name . '-' . $i . '.jpg' );
                 $this->send_message('img', 'preview', round( ($i / $duration) * 100));    
            }
            $this->send_message('img', 'preview', 100);    
            */
        }else{
            return $this->TMP_DIR . ' does not exist or is not writable';
        }
    }

    /**
     * 
     */
    private function accessProtected($obj, $prop) {
        $reflection = new ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }


    /**
     * Delete this instance from the database.
     *
     * @return bool False if an error occurs
     */
    public function delete_instance() {
        global $DB;
        $result = true;

        // Delete files associated with this videofile.
        $fs = get_file_storage();
        if (! $fs->delete_area_files($this->context->id) ) {
            $result = false;
        }

        // Delete the instance.
        // Note: all context files are deleted automatically.
        $DB->delete_records('videofile', array('id' => $this->get_instance()->id));

        return $result;
    }

    /**
     * Update this instance in the database.
     *
     * @param stdClass $formdata The data submitted from the form
     * @return bool False if an error occurs
     */
    public function update_instance($formdata) {
        global $DB;

        $update = new stdClass();
        $update->id = $formdata->instance;
        $update->name = $formdata->name;
        $update->timemodified = time();
        $update->course = $formdata->course;
        $update->intro = $formdata->intro;
        $update->introformat = $formdata->introformat;
        $update->width = $formdata->width;
        $update->height = $formdata->height;
        $update->responsive = $formdata->responsive;

        $update->title = $formdata->name;
        $update->creator = $formdata->creator;
        $update->subject = $formdata->subject;
        $update->description = $update->intro;
        $update->publisher = $formdata->publisher;
        $update->contributor = $formdata->contributor;
        $update->date = $formdata->date;
        $update->type = $formdata->type;
        $update->mimetype = $formdata->mimetype;
        $update->format = $formdata->format;
        $update->source = $formdata->source;
        $update->language = $formdata->language;
        $update->relation = $formdata->relation;
        $update->coverage = $formdata->coverage;
        $update->rights = $formdata->rights;
        
        //$update->filename = $url;
        $update->length = $formdata->length;
        $update->size = $formdata->size;
        $update->license = $formdata->license;
        $update->poster = $formdata->poster;
        $update->institution = $formdata->institution;
        $comma = array(", ", ", ");
        $cleantags = str_replace($comma, ",", $formdata->videotags);
        $update->tags = $cleantags;
        $update->actors = $formdata->actors;
        $update->sports = $formdata->sports;
        $update->movements = $formdata->movements;
        $update->location = $formdata->location;

        //$add->compentencies = implode(",",$formdata->compentencies);
        if(isset($formdata->compentencies) && sizeof($formdata->compentencies) > 0){
            $update->compentencies = implode(",",$formdata->compentencies);
        }else{
            $update->compentencies = '';
        }
        //$add->courselevel = implode(",",$formdata->courselevel);
        if(isset($formdata->courselevel) && sizeof($formdata->courselevel) > 0){
            $update->courselevel = implode(",",$formdata->courselevel);
        }else{
            $update->courselevel = '';
        }
        //$add->activities = implode(",",$formdata->activities);
         if(isset($formdata->activities) && sizeof($formdata->activities) > 0){
            $update->activities = implode(",",$formdata->activities);
        }else{
            $update->activities = '';
        }
        //$add->perspectives = implode(",",$formdata->perspectives);
         if(isset($formdata->perspectives) && sizeof($formdata->perspectives) > 0){
            $update->perspectives = implode(",",$formdata->perspectives);
        }else{
            $update->perspectives = '';
        }

        $result = $DB->update_record('videofile', $update);
        $this->instance = $DB->get_record('videofile',
                                          array('id' => $update->id),
                                          '*',
                                          MUST_EXIST);
        $this->save_files($formdata);

        return $result;
    }

    /**
     * Get the name of the current module.
     *
     * @return string The module name (Videofile)
     */
    protected function get_module_name() {
        if (isset(self::$modulename)) {
            return self::$modulename;
        }
        self::$modulename = get_string('modulename', 'videofile');
        return self::$modulename;
    }

    /**
     * Get the plural name of the current module.
     *
     * @return string The module name plural (Videofiles)
     */
    protected function get_module_name_plural() {
        if (isset(self::$modulenameplural)) {
            return self::$modulenameplural;
        }
        self::$modulenameplural = get_string('modulenameplural', 'videofile');
        return self::$modulenameplural;
    }

    /**
     * Has this videofile been constructed from an instance?
     *
     * @return bool
     */
    public function has_instance() {
        return $this->instance || $this->get_course_module();
    }

    /**
     * Get the settings for the current instance of this videofile.
     *
     * @return stdClass The settings
     */
    public function get_instance() {
        global $DB;
        if ($this->instance) {
            return $this->instance;
        }
        if ($this->get_course_module()) {
            $params = array('id' => $this->get_course_module()->instance);
            $this->instance = $DB->get_record('videofile', $params, '*', MUST_EXIST);
        }
        if (!$this->instance) {
            throw new coding_exception('Improper use of the videofile class. ' .
                                       'Cannot load the videofile record.');
        }
        return $this->instance;
    }

    /**
     * Get the context of the current course.
     *
     * @return mixed context|null The course context
     */
    public function get_course_context() {
        if (!$this->context && !$this->course) {
            throw new coding_exception('Improper use of the videofile class. ' .
                                       'Cannot load the course context.');
        }
        if ($this->context) {
            return $this->context->get_course_context();
        } else {
            return context_course::instance($this->course->id);
        }
    }

    /**
     * Get the current course module.
     *
     * @return mixed stdClass|null The course module
     */
    public function get_course_module() {
        if ($this->coursemodule) {
            return $this->coursemodule;
        }
        if (!$this->context) {
            return null;
        }

        if ($this->context->contextlevel == CONTEXT_MODULE) {
            $this->coursemodule = get_coursemodule_from_id('videofile',
                                                           $this->context->instanceid,
                                                           0,
                                                           false,
                                                           MUST_EXIST);
            return $this->coursemodule;
        }
        return null;
    }

    /**
     * Get context module.
     *
     * @return context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Get the current course.
     *
     * @return mixed stdClass|null The course
     */
    public function get_course() {
        global $DB;

        if ($this->course) {
            return $this->course;
        }

        if (!$this->context) {
            return null;
        }
        $params = array('id' => $this->get_course_context()->instanceid);
        $this->course = $DB->get_record('course', $params, '*', MUST_EXIST);

        return $this->course;
    }

    /**
     * Util function to add a message to the log.
     *
     * @param string $action The current action
     * @param string $info A detailed description of the change.
     *                     But no more than 255 characters.
     * @param string $url The url to the videofile module instance.
     * @return void
     */
    public function add_to_log($action = '', $info = '', $url='') {
        global $USER;

        $fullurl = 'view.php?id=' . $this->get_course_module()->id;
        if ($url != '') {
            $fullurl .= '&' . $url;
        }

        add_to_log($this->get_course()->id,
                   'videofile',
                   $action,
                   $fullurl,
                   $info,
                   $this->get_course_module()->id,
                   $USER->id);
    }

    /**
     * Lazy load the page renderer and expose the renderer to plugins.
     *
     * @return videofile_renderer
     */
    public function get_renderer() {
        global $PAGE;

        if ($this->output) {
            return $this->output;
        }
        $this->output = $PAGE->get_renderer('mod_videofile');
        return $this->output;
    }

    /**
     * Save draft files.
     *
     * @param stdClass $formdata
     * @return void
     */
    protected function save_files($formdata) {
        global $DB;

        // Storage of files from the filemanager (videos).
        $draftitemid = $formdata->videos;
        if ($draftitemid) {
            file_save_draft_area_files(
                $draftitemid,
                $this->context->id,
                'mod_videofile',
                'videos',
                0
            );
        }

        // Storage of files from the filemanager (captions).
        $draftitemid = $formdata->captions;
        if ($draftitemid) {
            file_save_draft_area_files(
                $draftitemid,
                $this->context->id,
                'mod_videofile',
                'captions',
                0
            );
        }

        // Storage of files from the filemanager (posters).
        $draftitemid = $formdata->posters;
        if ($draftitemid) {
            file_save_draft_area_files(
                $draftitemid,
                $this->context->id,
                'mod_videofile',
                'posters',
                0
            );
        }
    }
}
