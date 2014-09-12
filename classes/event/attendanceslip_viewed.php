<?php

namespace mod_attendanceslip\event;
defined('MOODLE_INTERNAL') || die();

class attendanceslip_viewed extends \core\event\course_module_viewed {
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'attendanceslip';
    }

    public function get_url() {
        return new \moodle_url('/mod/attendanceslip/attendanceslip.php', array('id' => $this->contextinstanceid));
    }

    protected function get_legacy_logdata() {
        return array($this->courseid, 'attendanceslip', 'view',
            "attendanceslip.php?id={$this->contextinstanceid}",
            $this->objectid, $this->contextinstanceid);
    }

    protected function validate_data() {
        parent::validate_data();
        if ($this->contextlevel !== CONTEXT_MODULE)
            throw new \coding_exception('Context level must be CONTEXT_MODULE.');
    }
}
