<?PHP  // $Id: view.php,v 1.0 2006/02/13 18:30:00 moodler Exp $

/// This page prints a particular instance of attendanceslip
/// (Replace attendanceslip with the name of your module)

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT);    // Course Module ID
    $a  = optional_param('a', NULL, PARAM_TEXT);     // attendanceslip ID

    if ($id) {
        if (! $cm = $DB->get_record("course_modules", array("id" => $id))) {
            error("Course Module ID was incorrect");
        }

        if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
            error("Course is misconfigured");
        }

        if (! $attendanceslip = $DB->get_record("attendanceslip", array("id" => $cm->instance))) {
            error("Course module is incorrect");
        }

    } else {
        if (! $attendanceslip = $DB->get_record("attendanceslip", array("id" => $a))) {
            error("Course module is incorrect");
        }
        if (! $course = $DB->get_record("course", array("id" => $attendanceslip->course))) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("attendanceslip", $attendanceslip->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);

    $context = context_module::instance($cm->id);

/// Print the page header

    if (isset($USER->id) && isset($attendanceslip->id) && $current_attendanceslip_entries = $DB->get_record('attendanceslip_entries', array('attendanceslip' => $attendanceslip->id,'userid' => $USER->id))) {
        $alreadyregistered = true;
    } else {
        $alreadyregistered = false;
    }

    $strattendanceslips = get_string("modulenameplural", "attendanceslip");
    $strattendanceslip  = get_string("modulename", "attendanceslip");


    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    } else {
        $navigation = '';
    }

    $event = \mod_attendanceslip\event\course_module_viewed::create(array(
        'context' => $context,
        'objectid' => $attendanceslip->id
    ));
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('attendanceslip', $attendanceslip);
    $event->trigger();

// Initialize $PAGE, compute blocks
    $PAGE->set_url('/mod/attendanceslip/view.php', array('id' => $id));

    $title = $course->shortname . ': ' . format_string($attendanceslip->name);
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

/// Print the main part of the page

    if (has_capability('mod/attendanceslip:teacher', $context)) {
        if ( $attendanceslip->passwords ) {
            echo "<div align=right><a href=\"attendanceslip.php?id=$cm->id\">".get_string("viewattendanceslip","attendanceslip")."</a></div>";
        }
        echo "<div align=right><a href=\"aslist.php?id=$cm->id\">".get_string("viewaslist","attendanceslip")."</a></div>";
    }

    echo $OUTPUT->box_start('generalbox');

    echo "<div align=center>".$attendanceslip->intro."</div>";

    echo $OUTPUT->box_end();

    echo $OUTPUT->box_start('generalbox');
    echo "<div align=center>";

    if (has_capability('mod/attendanceslip:teacher', $context)) {
        $pws = $attendanceslip->passwords;
        $pwsarray = explode("\n",$pws);
        $pwsnum = (sizeof($pwsarray) - 1);

        if ($data = $DB->get_records("attendanceslip_entries", array("attendanceslip" => $attendanceslip->id)))
          $ansnum = count($data);
        else
          $ansnum = 0;

        echo "attendanceslip: ".$ansnum." / ".$pwsnum;
    } else {
        $nowTime = time();
        if ( $attendanceslip->timeopen > $nowTime ) {
            //not yet
            echo "<br>".get_string("beforetimeopen","attendanceslip")."<br>";
        } else if ( $attendanceslip->timeclose < $nowTime ) {
            //finished
            echo "<br>".get_string("aftertimeclose","attendanceslip")."<br>";
        } else {
            echo "<form action=\"check.php\" method=\"post\">";

            echo "<div style='border:2px solid #3333DD;width:300px;height:50px;padding:10px;'>";
            if ( $attendanceslip->passwords ) {
                if ( $alreadyregistered ) {
                    echo get_string("youalreadyregistered","attendanceslip")."<br>";
                    echo "( ".date("Y/m/d H:i:s",$current_attendanceslip_entries->modified)." )";
                } else {
                    echo print_string("password","attendanceslip").": <input type=\"text\" name=\"password\" value=\""/*.$entry->password*/."\"><br><br>";
                    echo "<input type=\"submit\" value=\"".get_string("enter","attendanceslip")."\">";
                }
            }
            echo "</div>";
            echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">";
            echo "</form>";
        }
    }
    echo "</div>";

    echo $OUTPUT->box_end();

/// Finish the page
    echo $OUTPUT->footer();

