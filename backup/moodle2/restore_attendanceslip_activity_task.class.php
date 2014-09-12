<?php

/**
 * attendanceslip restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
 
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/attendanceslip/backup/moodle2/restore_attendanceslip_stepslib.php'); // Because it exists (must)
 
class restore_attendanceslip_activity_task extends restore_activity_task {
 
    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }
 
    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // attendanceslip only has one structure step
        $this->add_step(new restore_attendanceslip_activity_structure_step('attendanceslip_structure', 'attendanceslip.xml'));
    }
 
    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();
 
        $contents[] = new restore_decode_content('attendanceslip', array('intro'), 'attendanceslip');
 
        return $contents;
    }
 
    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();
 /*
        $rules[] = new restore_decode_rule('attendanceslipVIEWBYID', '/mod/attendanceslip/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('attendanceslipREPORTBYID', '/mod/attendanceslip/report.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('attendanceslipEXTRACTBYID', '/mod/attendanceslip/extract.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('attendanceslipINDEX', '/mod/attendanceslip/index.php?id=$1', 'course');
 */
        return $rules;
 
    }
 
    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * attendanceslip logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();
 /*
        $rules[] = new restore_log_rule('attendanceslip', 'update feedback', 'report.php?id={course_module}', '{attendanceslip}');
        $rules[] = new restore_log_rule('attendanceslip', 'view', 'view.php?id={course_module}', '{attendanceslip}');
        $rules[] = new restore_log_rule('attendanceslip', 'update entry', 'view.php?id={course_module}', '{attendanceslip}');
 */
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
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();
 /*
        // Fix old wrong uses (missing extension)
        $rules[] = new restore_log_rule('attendanceslip', 'view all', 'index.php?id={course}', null);
 */
        return $rules;
    }
 
}