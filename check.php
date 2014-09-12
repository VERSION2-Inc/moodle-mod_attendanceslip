<?PHP // $Id: check.php,v 1.0 2006/02/13 18:30:00 moodler Exp $

    require_once("../../config.php");

    $id = optional_param('id', 0, PARAM_INT);    // Course Module ID

    if (! $cm = $DB->get_record("course_modules", array("id" => $id))) {
        error("Course Module ID was incorrect");
    }

    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    $PAGE->set_url('/mod/attendanceslip/attendanceslip.php', array('id' => $id));

    $context = context_module::instance($cm->id);

    if (!has_capability('mod/attendanceslip:teacher', $context) && !has_capability('mod/attendanceslip:student', $context)) {
        error("Guests are not allowed to edit attendanceslips", $_SERVER["HTTP_REFERER"]);
    }

    if (! $attendanceslip = $DB->get_record("attendanceslip", array("id" => $cm->instance))) {
        error("Course module is incorrect");
    }

    $PAGE->set_title($course->shortname . ': ' . format_string($attendanceslip->name));
    $PAGE->set_heading($course->fullname);

    $entry = $DB->get_record("attendanceslip_entries", array("userid" => $USER->id, "attendanceslip" => $attendanceslip->id));


/// If data submitted, then process and store.

    if ($form = data_submitted()) {
		//submitted
		$pwsArray = explode("\n",$attendanceslip->passwords);
		$flg = true;
		for( $ii=0; $ii<sizeof($pwsArray); $ii++ ) {
			if ( $pwsArray[$ii] ) {
				//print trim($pwsArray[$ii]).":".$form->password."<br>";
				if ( trim($pwsArray[$ii]) == $form->password ) {
					$flg = false;
					break;  //ok
				}
			}
		}
		if ( $flg || !($form->password) ) {
			//passwrod error(not exist on the list / password == "")
			echo "<div onClick=\"history.back();\" align=\"center\" style=\"cursor:hand;\"><font color=blue><b><u>".get_string("return", "attendanceslip")."</u></b></font></div>";
			error(get_string("notexistpassword", "attendanceslip"));
		}

		$attendanceslip_entries = $DB->get_record("attendanceslip_entries", array("password" => $form->password));
		if ( $attendanceslip_entries ) {
			//password exist
			if ( $attendanceslip_entries->id != $entry->id ) {
				//other's password!!
				error(get_string("alreadyexistpassword", "attendanceslip"));
			}
			//OK
		}

		if ( !empty($entry->password) && ($entry->password != $form->password) ) {
			//attendanceslip existed
			echo "<div onClick=\"history.back();\" align=\"center\" style=\"cursor:hand;\"><font color=blue><b><u>".get_string("return", "attendanceslip")."</u></b></font></div>";
			error(get_string("notexistpassword", "attendanceslip"));
		}

		$timenow = time();

		$newentry = new StdClass;

		if ($entry) {
			$newentry = clone $entry;
			$newentry->password = $form->password;
			$newentry->modified = $timenow;
			if (! $DB->update_record("attendanceslip_entries", $newentry)) {
				error("Could not update your attendanceslip");
			}
            $event = \mod_attendanceslip\event\entry_updated::create(array(
                'context' => $context,
                'objectid' => $newentry->id
            ));
            $event->add_record_snapshot('attendanceslip_entries', $newentry);
            $event->trigger();
		} else {
			$newentry->userid = $USER->id;
			$newentry->attendanceslip = $attendanceslip->id;
			$newentry->password = $form->password;
			$newentry->modified = $timenow;
			if (! $newentry->id = $DB->insert_record("attendanceslip_entries", $newentry)) {
				error("Could not insert a new attendanceslip entry");
			}
            $event = \mod_attendanceslip\event\entry_created::create(array(
                'context' => $context,
                'objectid' => $newentry->id
            ));
            $event->add_record_snapshot('attendanceslip_entries', $newentry);
            $event->trigger();
		}

		if ( !($form->password) ) {
			error(get_string("abnormalpassword", "attendanceslip"));
		}
		//print_header(get_string("accepted", "attendanceslip"));
		notice(get_string("accepted", "attendanceslip"),"view.php?id=$cm->id");
    }

/// Otherwise fill and print the form.

    $strattendanceslip = get_string("modulename", "attendanceslip");
    $strattendanceslips = get_string("modulenameplural", "attendanceslip");
    $stredit = get_string("edit");

// Initialize $PAGE, compute blocks
    $PAGE->set_url('/mod/attendanceslip/view.php', array('id' => $id));

    $title = $course->shortname . ': ' . format_string($attendanceslip->name);
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

    echo $OUTPUT->box_start('generalbox');

    echo "<div align=center>".$attendanceslip->intro."</div>";

    echo $OUTPUT->box_end();


/// Finish the page
    echo $OUTPUT->footer();

