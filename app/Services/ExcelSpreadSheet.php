<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelSpreadSheet
{
    public $data;
    public $spreadsheet;
    public $sheet;
    public $row_limit;
    public $column_limit;
    public $row_range;
    public $startcount;

    public function __construct($data)
    {
        $this->spreadsheet = IOFactory::load($data->getRealPath());
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->row_limit = $this->sheet->getHighestDataRow();
        $this->column_limit = $this->sheet->getHighestDataColumn();
        $this->row_range = range(2, $this->row_limit);
        $this->startcount = 2;

        // $column_range = range('F', $column_limit);
        // $data = [];
    }
}
