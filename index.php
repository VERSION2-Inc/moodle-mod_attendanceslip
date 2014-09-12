<?PHP // $Id: index.php,v 1.0 2006/02/13 18:30:00 moodler Exp $

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT);    // Course Module ID

    if (! $course = $DB->get_record('course', array('id' => $id))) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    $event = \mod_attendanceslip\event\course_module_instance_list_viewed::create(array(
        'context' => context_course::instance($course->id)
    ));
    $event->trigger();

/// Get all required strings

    $strattendanceslips = get_string("modulenameplural", "attendanceslip");
    $strattendanceslip  = get_string("modulename", "attendanceslip");

    $PAGE->set_url('/mod/attendanceslip.php', array('id' => $id));
    $PAGE->set_title("$course->shortname: $strattendanceslips");
    $PAGE->set_heading($course->fullname);
    $PAGE->navbar->add($strattendanceslips);

/// Print the header

    echo $OUTPUT->header();

/// Get all the appropriate data

    if (! $attendanceslips = get_all_instances_in_course("attendanceslip", $course)) {
        notice("There are no attendanceslips", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    $table = new html_table;

    $course->format = course_get_format($course);
    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("CENTER", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname);
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

    foreach ($attendanceslips as $attendanceslip) {
        if (!$attendanceslip->visible) {
            //Show dimmed if the mod is hidden
            $link = "<A class=\"dimmed\" HREF=\"view.php?id=$attendanceslip->coursemodule\">$attendanceslip->name</A>";
        } else {
            //Show normal if the mod is visible
            $link = "<A HREF=\"view.php?id=$attendanceslip->coursemodule\">$attendanceslip->name</A>";
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($attendanceslip->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<BR>";

    echo html_writer::table($table);

/// Finish the page

    echo $OUTPUT->footer();
