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

        // General introduction.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Select data to export.
        $mform->addElement('header', 'general', get_string('dataselect', 'scheduler'));

        // Output format
        $mform->addElement('header', 'general', get_string('outputformat', 'scheduler'));

        $mform->addElement('radio', 'outputformat', get_string('csvformat', 'scheduler'), '', 'csv');
        $mform->addElement('radio', 'outputformat', get_string('excelformat', 'scheduler'), '', 'xls');
        $mform->addElement('radio', 'outputformat', get_string('odsformat', 'scheduler'), '', 'ods');
        $mform->addElement('radio', 'outputformat', get_string('pdfformat', 'scheduler'), '', 'pdf');

        // Start date/time of the slot
        $mform->addElement('date_time_selector', 'starttime', get_string('date', 'scheduler'));
        $mform->setDefault('starttime', time());
        $mform->addHelpButton('starttime', 'choosingslotstart', 'scheduler');


        $this->add_action_buttons();

    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }

}
