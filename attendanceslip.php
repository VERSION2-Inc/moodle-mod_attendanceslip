<?PHP // $Id: attendanceslip.php,v 1.18 2006/02/13 18:30:00 moodler Exp $

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT);    // Course Module ID

    if (! $cm = $DB->get_record("course_modules", array("id" => $id))) {
        error("Course Module ID was incorrect");
    }

    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        error("Course module is misconfigured");
    }

    require_login($course->id);

    $context = context_module::instance($cm->id);
    $contextcourse = context_course::instance($course->id);

    if (!has_capability('mod/attendanceslip:teacher', $context)) {
        error("Only teachers can look at this page");
    }

    if (! $attendanceslip = $DB->get_record("attendanceslip", array("id" => $cm->instance))) {
        error("Course module is incorrect");
    }

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    } else {
        $navigation = '';
    }


    $event = \mod_attendanceslip\event\attendanceslip_viewed::create(array(
        'context' => $context,
        'objectid' => $attendanceslip->id
    ));
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('attendanceslip', $attendanceslip);
    $event->trigger();

// Initialize $PAGE, compute blocks
    $PAGE->set_url('/mod/attendanceslip/attendanceslip.php', array('id' => $id));

    $title = $course->shortname . ': ' . format_string($attendanceslip->name);
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

    $table = new html_table();

    echo $OUTPUT->box_start('generalbox');

    echo "<a href=\"view.php?id=".$id."\"><< back</a>";

    $selStr = "";

    $pwsArray = explode("\n",$attendanceslip->passwords);
    for( $ii=0; $ii<sizeof($pwsArray); $ii++ ) {
        $pwsArray[$ii] = trim($pwsArray[$ii]);                    //20040720�ǉ�
        if ( $pwsArray[$ii] ) {
            if ($eee = $DB->get_record("attendanceslip_entries", array("password" => trim($pwsArray[$ii])))) {
                $user = $DB->get_record("user", array("id" => $eee->userid));
                $codeArray[]  = $user->username;
                $name1Array[] = $user->lastname;
                $name2Array[] = $user->firstname;
                $timeArray[]  = date("Y/m/d H:i:s",$eee->modified);
            } else {
                $codeArray[]  = "zzzzzzzzzzzzzzzzzzzz".$pwsArray[$ii];
                $name1Array[] = "&nbsp";
                $name2Array[] = "&nbsp";
                $timeArray[]  = "&nbsp";
            }
            $atteArray[] = $pwsArray[$ii];
        }
    }
    asort($codeArray);
    reset($codeArray);

    $table->head = array ("User", "Last Name", "First Name", "Presented", "Attendanceslip Code");
    $table->align = array ("center", "left", "left", "left", "center");
    $table->width = "95%";

    while (list ($key, $val) = each ($codeArray)) {
        $code = $val;
        if ( substr($code,0,20) == "zzzzzzzzzzzzzzzzzzzz" ) {
            $code = "&nbsp;";
        }
        $table->data[] = array ($code, $name1Array[$key], $name2Array[$key], $timeArray[$key], $atteArray[$key]);
    }


    if (!empty($table)) {
        echo html_writer::table($table);
    }


    echo $OUTPUT->box_end();

/// Finish the page
    echo $OUTPUT->footer();

