<?php //$Id,v 1.0 2012/03/07 12:00:00 Serafim Panov Exp $

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_attendanceslip_mod_form extends moodleform_mod {
    function definition() {
        global $COURSE, $CFG, $_GET, $DB, $PAGE;

        //$attendanceslipcfg = get_config('attendanceslip');


        $mform    =& $this->_form;

        $context = context_course::instance($COURSE->id);

//-------------------------------------------------------------------------------
    /// Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));
    /// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('attendanceslipname', 'attendanceslip'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
    /// Adding the optional "intro" and "introformat" pair of fields
        $this->add_intro_editor(true, get_string('attendanceslipquestion', 'attendanceslip'));


        $mform->addElement('date_time_selector', 'timeopen', get_string('timeopen', 'attendanceslip'),
                array('optional' => false, 'step' => 1));

        $mform->addElement('date_time_selector', 'timeclose', get_string('timeclose', 'attendanceslip'),
                array('optional' => false, 'step' => 1));

        $mform->addElement('textarea', 'passwords', get_string('passwords', 'attendanceslip'), 'rows="10" cols="30"');

        $mform->addElement('html', '<div id="fitem_id_codes" class="fitem required fitem_ftext"><div class="fitemtitle"><label for="id_codes">&nbsp;</label></div><div class="felement ftext"><input type="button" value="'.get_string("makecodes","attendanceslip").'" onClick="makePw();"><br> <input type="button" value="'.get_string("printcodes","attendanceslip").'" onClick="printPw();"></div></div>');

        $mform->addElement('html', '      <script language="JavaScript" type="text/javascript">

function chgCheck(s1,s2) {
    if ( document.getElementById(s1).checked ) {
        document.getElementById(s2).value = 1;
    } else {
        document.getElementById(s2).value = 0;
    }
}

function printPw() {
    var w1=window.open(\'\',\'print\',\'top=50,left=100,width=800,height=500,menubar=yes,scrollbars=yes,status=yes,resizable=yes,toolbar=yes\');
    var str = document.getElementById(\'id_passwords\').value;
    var strarray = str.split(\'\n\');
    st  = \'<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\'
        + \'<html>\n\'
        + \'<head>\n\'
        + \'<title>Lecturefeedback Password</title>\n\'
        + \'<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Shift_JIS\">\n\'
        + \'</head>\n\'
        + \'<body>\n\'
        + \'<div align=center>\n\';
    w1.document.write(st);
    w1.document.write(\'<font size=5>\' + document.getElementById(\'id_name\').value + \'</font><br><br>\n\');
    w1.document.write(\'<table border=1>\n\');
    for ( i=0; i<strarray.length; i++ ) {
        if ( strarray[i] != \'\' ) {
            w1.document.write(\'<tr height=50 valign=middle>\n\');
            w1.document.write(\'<td width=200 align=center><pre>\' + strarray[i] + \'</pre></td>\');
            w1.document.write(\'<td width=200 align=center><pre>\' + strarray[i] + \'</pre></td>\');
            w1.document.write(\'</tr>\n\');
        }
    }
    w1.document.write(\'</table>\n</div>\n</body>\n</html>\n\');
}

function genPassword() {
    var seedDate = new Date();
    var seedTime = seedDate.getTime();
    seed = ((seedTime*9301+49297) % 233280)/(233280.0);

    //random dummy loop
    for( i=0; i<Math.ceil(seed*100); i++ ) {
        rnd = Math.random();
    }

    var randomchars = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
    var str = "";
    for( i=0; i<5; i++ ) {
        rnd = Math.random();
        str += randomchars.charAt(Math.floor( rnd * (randomchars.length+1) ));
    }
    return str;
}

function makePw() {
    var num;
    var salt;
    num = prompt(\'How many codes do you want to make?\',\'30\');
    if ( num == \'\' || num == null ) {
        alert(\'Please input number.\');
        return;
    }
    if ( num <=0 ) {
        alert(\'Please input number.\');
        return;
    }
    salt = prompt(\'What is a suffix for codes?\',\'\');
    if ( salt == \'\' || salt == null ) {
        alert(\'Please input a suffix for codes.\');
        return;
    }
    var i,j;
    var rn;
    var flg;
    var st = new Array(num);
    for ( i=0; i<num; i++ ) {
        rn = genPassword();
        flg = true;
        for ( j=0; j<i; j++ ) {
            if ( st[j] == rn ) flg = false;
        }
        if ( flg ) {
            st[i] = rn;
        } else {
            alert(\'Couldn`t make codes. Please try this again.\');
            return;
        }
    }
    var str = \'\';
    for ( i=1; i<=num; i++ ) {
        //str += salt + addZero(i,3) + st[i-1] + \'<br>\';
        str += salt + addZero(i,3) + st[i-1] + \'\n\';
    }
    document.getElementById(\'id_passwords\').value = str;
}
function addZero(s,n) {
    str = \'00000000000000000000\' + s;
    return str.substring(str.length-n);
}
</script>');

        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }
}


