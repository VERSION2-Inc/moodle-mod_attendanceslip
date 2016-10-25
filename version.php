<?PHP // $Id: version.php,v 1.0 2006/10/06 12:30:00 moodler Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of NEWMODULE
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

// Original version: 2012030700
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'mod_attendanceslip'; // Full name of the plugin (used for diagnostics).
$plugin->version  = 2014091200;  // The current module version (Date: YYYYMMDDXX)
$plugin->cron     = 0;           // Period for cron to check this module (secs)
