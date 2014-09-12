<?PHP // $Id: aslist.php,v 1.18 2006/09/26 18:30:00 moodler Exp $

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

    //if (! $attendancesliplist = get_records("attendanceslip", "course", $cm->course, "id")) {
    $attendancesliplist = $DB->get_records_sql("SELECT a.id, a.name
                                            FROM {attendanceslip} a,
                                                 {course_modules} cm,
                                                 {modules} m
                                           WHERE cm.instance = a.id
                                             AND cm.course   = ?
                                             AND cm.module   = m.id
                                             AND cm.visible  = 1
                                             AND m.name      = 'attendanceslip'", array($cm->course));

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    } else {
        $navigation = '';
    }


    //add_to_log($course->id, "attendanceslip aslist", "view", "aslist.php?id=$id", $cm->instance);

// Initialize $PAGE, compute blocks
    $PAGE->set_url('/mod/attendanceslip/aslist.php', array('id' => $id));

    $title = $course->shortname . ': ' . format_string($attendanceslip->name);
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

    $table = new html_table();

    echo $OUTPUT->box_start('generalbox');

    echo "<a href=\"view.php?id=".$id."\"><< back</a>";

    $table->head = array ("User", "Last Name", "First Name");
    $table->align = array ("center", "left", "left");
    $table->width = "95%";

        $cnt = 0;
        foreach ($attendancesliplist as $attendanceslipitem) {
            $cnt++;
            $table->head[] = $attendanceslipitem->name;
        }
    $table->head[] = "Rate(%)";

    $c = 0;


    $stu = get_enrolled_users(context_course::instance($course->id));

    if ($stu) {

        foreach ($stu as $eachstu) {
            $c ++;
            $table->data[$c] = array ($eachstu->username, $eachstu->lastname, $eachstu->firstname);
            $pcnt = 0;
            foreach ($attendancesliplist as $attendanceslipitem) {
                if ( $attendanceslip_entries = $DB->get_record("attendanceslip_entries", array("attendanceslip" => $attendanceslipitem->id, "userid" => $eachstu->id)) ) {
                    $pcnt++;
                    $table->data[$c][] = '<table width="100%"><tr><td bgcolor="#CCFFFF">A</td></tr></table>';
                } else {
                    $table->data[$c][] = '<table width="100%"><tr><td bgcolor="#CCFFFF">&nbsp;</td></tr></table>';
                }
            }

            $table->data[$c][] = round($pcnt/$cnt*100,1);
        }

    }

    if (!empty($table)) {
        echo html_writer::table($table);
    }


    echo $OUTPUT->box_end();

/// Finish the page
    echo $OUTPUT->footer();



