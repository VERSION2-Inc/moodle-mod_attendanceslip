<?PHP  // $Id: lib.php,v 1.0 2006/02/13 18:30:00 gustav_delius Exp $


function attendanceslip_add_instance($attendanceslip) {
    global $CFG, $USER, $DB;

    $attendanceslip->timemodified = time();

    return $DB->insert_record("attendanceslip", $attendanceslip);
}


function attendanceslip_update_instance($attendanceslip) {
    global $CFG, $USER, $DB;

    $attendanceslip->timemodified = time();
    $attendanceslip->id = $attendanceslip->instance;

    return $DB->update_record("attendanceslip", $attendanceslip);
}


function attendanceslip_delete_instance($id) {
    global $CFG, $USER, $DB;

    if (! $attendanceslip = $DB->get_record("attendanceslip", array("id" => $id))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("attendanceslip", array("id" => $attendanceslip->id))) {
        $result = false;
    }

    return $result;
}

function attendanceslip_user_outline($course, $user, $mod, $attendanceslip) {
    return $return;
}

function attendanceslip_user_complete($course, $user, $mod, $attendanceslip) {
    return true;
}

function attendanceslip_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    return false;  //  True if anything was printed, otherwise false
}

function attendanceslip_cron () {
    global $CFG;

    return true;
}

function attendanceslip_grades($attendanceslipid) {
   return NULL;
}

function attendanceslip_get_participants($attendanceslipid) {
    return false;
}

function attendanceslip_scale_used ($attendanceslipid,$scaleid) {
    $return = false;

    return $return;
}

/**
 *
 * @param int $scaleid
 * @return boolean
 */
function attendanceslip_scale_used_anywhere($scaleid) {
	return false;
}

function attendanceslip_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}


