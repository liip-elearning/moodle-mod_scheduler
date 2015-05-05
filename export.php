<?php

require_once(dirname(__FILE__).'/exportform.php');

/**
 * Export scheduler data to a file.
 *
 * @package    mod
 * @subpackage scheduler
 * @copyright  2011 Henning Bostelmann and others (see README.txt)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


$actionurl = new moodle_url('/mod/scheduler/view.php', array('what' => 'export', 'id' => $scheduler->cmid));
$returnurl = new moodle_url('/mod/scheduler/view.php', array('what' => 'view', 'id' => $scheduler->cmid));
$mform = new scheduler_export_form($actionurl, $scheduler);

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if (!$data= $mform->get_data(false)) {
    // Print top tabs.

    $taburl = new moodle_url('/mod/scheduler/view.php', array('id' => $scheduler->cmid, 'what' => 'export'));
    echo $output->teacherview_tabs($scheduler, $taburl, 'downloads');

    echo $output->heading('export', 2);

    $mform->display();

    echo $output->footer();
    exit();
}

debugging('now exporting');
print_object($data);
