<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/excellib.class.php');


class scheduler_export_field {

    public function __construct($name, $labelid, $component='scheduler') {
        $this->parentexport = $parent;
        $this->sitsid=$sitsid;
        $this->weight = $weight;
    }

    public abstract function get_header();

    public abstract function get_value(scheduler_slot $slot, $appointment);

}

class scheduler_slotdate_field extends scheduler_export_field {

    public function __construct($name, $labelid, $component='scheduler') {
        parent::construct($name, $labelid, $component);
    }

}



abstract class scheduler_canvas {

    public function __construct() {
        $this->warnings = array();
    }

    public $format_bold;
    public $format_boldit;
    public $format_boldwrap;

    public abstract function write_string($row, $col, $str, $format);

    public abstract function write_number($row, $col, $num, $format);

    public abstract function write_formula($row, $col, $formula, $format);

    public abstract function merge_cells($row, $fromcol, $tocol);

    protected $warnings;

    public function add_warning($msg) {
        $this->warnings[]=$msg;
    }

    public function get_warnings() {
        return $this->warnings;
    }
    public function has_warnings() {
        return count($this->warnings) > 0;
    }

    public static function xlspos($row, $col, $absrow=false, $abscol=false) {
        // 0 is A, 1 is B and so on
        // This will break with $num < 0 or $num > 25
        $canvasabsrow = $absrow ? '$' : '';
        $canvasabscol = $abscol ? '$' : '';
        $canvascol =chr($col + 65);
        $canvasrow =$row+1;
        return $canvasabscol.$canvascol.$canvasabsrow.$canvasrow;
    }

}



class scheduler_excel_canvas extends scheduler_canvas {

    protected $workbook;
    protected $worksheet;


    public function __construct($filename) {

        parent::__construct();

        $strgrades = 'marksheet';

        /// Creating a workbook
        $this->workbook = new MoodleExcelWorkbook("-");
        /// Sending HTTP headers
        $this->workbook->send($filename);
        /// Adding the worksheet
        $this->worksheet = $this->workbook->add_worksheet($strgrades);

        // TODO fix this
        $this->trailercol=10;

        // Set column widths
        $this->worksheet->set_column(1, $this->trailercol, 8); // setting width of columns
        $this->worksheet->set_column(0, 0, 10); // the first column for the candidate number needs to be wider

        // Set up formats
        $this->format_bold = $this->workbook->add_format();
        $this->format_boldit = $this->workbook->add_format();
        $this->format_boldwrap = $this->workbook->add_format();
        $this->format_bold->set_bold();
        $this->format_boldit->set_bold();
        $this->format_boldit->set_italic();
        $this->format_boldwrap->set_text_wrap();
        $this->format_boldwrap->set_bold();

    }


    public function close() {
        $this->workbook->close();
    }

    public function write_string($row, $col, $str, $format=null) {
        $this->worksheet->write_string($row, $col, $str, $format);
    }

    public function write_number($row, $col, $num, $format=null) {
        $this->worksheet->write_number($row, $col, $num, $format);
    }

    public function write_formula($row, $col, $formula, $format=null) {
        $this->worksheet->write_formula($row, $col, $formula, $format);
    }

    public function merge_cells($row, $fromcol, $tocol) {
        $this->worksheet->merge_cells($row, $fromcol, $row, $tocol);
    }


}


class scheduler_html_canvas extends scheduler_canvas {

    protected $cells;
    protected $formats;
    protected $mergers;

    public function __construct() {

        parent::__construct();

        $this->cells = array();
        $this->formats = array();
        $this->mergers = array();

        $this->format_bold = true;
        $this->format_boldit = true;
        $this->format_boldwrap = true;

    }


    public function write_string($row,$col,$str,$format=null) {
        $this->cells[$row][$col] = $str;
        $this->formats[$row][$col] = $format;
    }

    public function write_number($row,$col,$num,$format=null) {
        $this->write_string($row,$col,$num,$format);
    }

    public function write_formula($row,$col,$formula,$format=null) {
        $this->write_string($row,$col,'(calculated)',$format);
    }

    public function merge_cells($row,$fromcol,$tocol) {
        $this->mergers[$row][$fromcol]=$tocol-$fromcol+1;
    }

    public function as_html($rowcutoff) {

        // find extent of the table
        $maxrow = 0; $maxcol = 0;
        foreach ($this->cells as $rownum=>$row) {
            foreach ($row as $colnum=>$col) {
                if ($rownum > $maxrow) {
                    $maxrow = $rownum;
                }
                if ($colnum > $maxcol) {
                    $maxcol = $colnum;
                }
            }
        }
        if ($maxrow >= $rowcutoff) {
            $maxrow = $rowcutoff-1;
        }

        $table = new html_table();
        for ($row = 0; $row <= $maxrow; $row++) {
            $hrow = new html_table_row();
            $col = 0;
            while ($col <= $maxcol) {
                $span = 1;
                if (isset($this->mergers[$row][$col])) {
                    $mergewidth = (int) $this->mergers[$row][$col];
                    if ($mergewidth >= 1) {
                        $span = $mergewidth;
                    }
                }
                $cell = new html_table_cell('');
                if (isset($this->cells[$row][$col])) {
                    $cell->text = $this->cells[$row][$col];
                }
                if (isset($this->formats[$row][$col])) {
                    $cell->header = $this->formats[$row][$col];
                }
                if ($span>1) {
                    $cell->colspan=$span;
                }
                $hrow->cells[] = $cell;
                $col = $col + $span;
            }
            $table->data[]=$hrow;
        }

        return html_writer::table($table);
    }

}
