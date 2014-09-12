<?php

/**
 * Structure step to restore one attendanceslip activity
 */
class restore_attendanceslip_activity_structure_step extends restore_activity_structure_step {
 
    protected function define_structure() {
 
        $paths = array();

        $paths[] = new restore_path_element('attendanceslip', '/activity/attendanceslip');
        $paths[] = new restore_path_element('attendanceslip_entries', '/activity/attendanceslip/entries');
 
        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }
 
    protected function process_attendanceslip($data) {
        global $DB;
  
        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
 
        $data->introformat = $this->apply_date_offset($data->introformat);
        $data->days = $this->apply_date_offset($data->days);
        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
 
        // insert the attendanceslip record
        $newitemid = $DB->insert_record('attendanceslip', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }
 
    protected function process_attendanceslip_entries($data) {
        global $DB;
 
        $data = (object)$data;
        $oldid = $data->id;
 
        $data->attendanceslip = $this->get_new_parentid('attendanceslip');
        
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->modified = $this->apply_date_offset($data->modified);
 
        $newitemid = $DB->insert_record('attendanceslip_entries', $data);
        $this->set_mapping('attendanceslip_entries', $oldid, $newitemid);
    }
    

    protected function after_execute() {
        // Add attendanceslip related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_attendanceslip', 'intro', null);
    }
}