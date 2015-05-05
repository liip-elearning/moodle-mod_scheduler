<?php

/**
 * Export settings form
 * (using Moodle formslib)
 *
 * @package    mod
 * @subpackage scheduler
 * @copyright  2015 Henning Bostelmann and others (see README.txt)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class scheduler_export_form extends moodleform {

    protected $scheduler;
    protected $context;
    protected $has_duration = false;

    public function __construct($action, scheduler_instance $scheduler, $customdata=null) {
        $this->scheduler = $scheduler;
        parent::__construct($action, $customdata);
    }

    protected function definition() {

        $mform = $this->_form;

        // Select data to export.
        $mform->addElement('header', 'general', get_string('dataselect', 'scheduler'));

		$mform->addElement('advcheckbox', 'field-date', 'slot date', null, array('group' => 1));
		$mform->addElement('advcheckbox', 'field-starttime', 'start time', null, array('group' => 1));
		$mform->addElement('advcheckbox', 'field-endtime', 'end time', null, array('group' => 1));
		$mform->addElement('advcheckbox', 'field-studentfirstname', 'student first name', null, array('group' => 1));
		$mform->addElement('advcheckbox', 'field-studentlastname',  'student last name', null, array('group' => 1));
		$mform->addElement('advcheckbox', 'field-studentfullname',  'student full name', null, array('group' => 1));
		$mform->addElement('advcheckbox', 'field-studentidnumber',  'student id number', null, array('group' => 1));
		$mform->addElement('advcheckbox', 'field-studentemail',     'student e-mail', null, array('group' => 1));
		$mform->addElement('advcheckbox', 'field-slotcomments',     'slot comments', null, array('group' => 1));
		$mform->addElement('advcheckbox', 'field-appointmentcomments', 'appointment comments', null, array('group' => 1));
		$this->add_checkbox_controller(1);

        // Output format
        $mform->addElement('header', 'general', get_string('outputformat', 'scheduler'));

        $mform->addElement('radio', 'outputformat', get_string('csvformat', 'scheduler'), '', 'csv');
        $sepoptions = array('tab' => get_string('septab', 'grades'),
            			   'comma' => get_string('sepcomma', 'grades'),
            			   'colon' => get_string('sepcolon', 'grades'),
            			   'semicolon' => get_string('sepsemicolon', 'grades')
           				   );
        $mform->addElement('select', 'csvseparator', get_string('separator', 'scheduler'), $sepoptions);
		$mform->disabledIf('csvseparator', 'outputformat', 'neq', 'csv');

        $mform->addElement('radio', 'outputformat', get_string('excelformat', 'scheduler'), '', 'xls');
        $mform->addElement('radio', 'outputformat', get_string('odsformat', 'scheduler'), '', 'ods');
        $mform->addElement('radio', 'outputformat', get_string('pdfformat', 'scheduler'), '', 'pdf');


        $this->add_action_buttons();

    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }

}
