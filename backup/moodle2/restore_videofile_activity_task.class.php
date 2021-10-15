<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Define all the backup steps that will be used by the backup_videofile_activity_task
 *
 * @package   mod_videofile
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//require_once($CFG->dirroot . '/mod/videofile/backup/moodle2/restore_videofile_stepslib.php');

/**
 * videofile restore task that provides all the settings and steps to perform one complete restore of the activity
 *
 * @package   mod_videofile
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_videofile_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have.
     */
    protected function define_my_steps() {
        // Assignment only has one structure step.
        //$this->add_step(new restore_videofile_activity_structure_step('videofile_structure', 'videofile.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder.
     *
     * @return array
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('videofile', array('intro'), 'videofile');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder.
     *
     * @return array of restore_decode_rule
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('VIDEOFILEVIEWBYID',
                                           '/mod/videofile/view.php?id=$1',
                                           'course_module');
        $rules[] = new restore_decode_rule('VIDEOFILEINDEX',
                                           '/mod/videofile/index.php?id=$1',
                                           'course_module');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * videofile logs. It must return one array
     * of {@link restore_log_rule} objects.
     *
     * @return array of restore_log_rule
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('videofile', 'add', 'view.php?id={course_module}', '{videofile}');
        $rules[] = new restore_log_rule('videofile', 'update', 'view.php?id={course_module}', '{videofile}');
        $rules[] = new restore_log_rule('videofile', 'view', 'view.php?id={course_module}', '{videofile}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     *
     * @return array
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        return $rules;
    }

    /**
     * Given a comment area, return the itemname that contains the itemid mappings.
     *
     * @param string $commentarea
     * @return string
     */
    public function get_comment_mapping_itemname($commentarea) {
        switch ($commentarea) {
            case 'submission_comments':
                $itemname = 'submission';
                break;
            default:
                $itemname = parent::get_comment_mapping_itemname($commentarea);
                break;
        }

        return $itemname;
    }
}
