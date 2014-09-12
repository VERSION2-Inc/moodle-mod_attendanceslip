<?php

/**
 * Define the complete attendanceslip structure for backup, with file and id annotations
 */     
class backup_attendanceslip_activity_structure_step extends backup_activity_structure_step {
 
    protected function define_structure() {
 
        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');
 
        // Define each element separated
        $attendanceslip = new backup_nested_element('attendanceslip', array('id'), array(
            'course', 'name', 'intro', 'introformat',
            'timeopen', 'timeclose', 'passwords', 'timemodified'));
 
        $entries = new backup_nested_element('entries', array('id'), array(
            'attendanceslip', 'userid', 'modified', 'password'));
 
        // Build the tree
        $attendanceslip->add_child($entries);
        
        
        // Define sources
        
        $attendanceslip->set_source_table('attendanceslip', array('id' => backup::VAR_ACTIVITYID));
 
        if ($userinfo) {
            $entries->set_source_table('attendanceslip_entries', array('attendanceslip' => backup::VAR_PARENTID));
        }
        // Define id annotations
        $entries->annotate_ids('user', 'userid');

        // Define file annotations
        $entries->annotate_files('mod_attendanceslip', 'intro', null);
        
        // Return the root element (attendanceslip), wrapped into standard activity structure
        
        return $this->prepare_activity_structure($attendanceslip);
 
    }
}