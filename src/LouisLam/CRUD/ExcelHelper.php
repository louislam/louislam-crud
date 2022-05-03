<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/25/2015
 * Time: 12:08 PM
 */

namespace LouisLam\CRUD;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelHelper {
    private $headerClosure;

    public function __construct() {
        $this->headerClosure= function ($key, $value) {
            header("$key: $value");
        };
    }

    public function genExcel(LouisCRUD $crud, array $list, $filename = null) {
        $excel = new Spreadsheet();
        $sheet = $excel->getActiveSheet();
        $fields = $crud->getShowFields();

        // Header
        $i = 0;
        foreach ($fields as $field) {
            $sheet->setCellValueByColumnAndRow($i, 1, $field->getDisplayName());
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            $i++;
        }

        // Data
        $j = 2;
        foreach ($list as $bean) {
            $i = 0;
            foreach ($fields as $field) {
                $sheet->getCellByColumnAndRow($i, $j)->setValueExplicit(
                    strip_tags($field->cellValue($bean)),
                    DataType::TYPE_STRING
                );
                $i++;
            }
            $j++;
        }

        // Save
        $objWriter = new Xlsx($excel);

        if ($filename == null) {
            $name = $crud->getTableName();
            $date = date("Y-m-d", time());
            $filename = "$name-$date.xlsx";
        }

        $headers = [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => 'attachment;filename="' . $filename . '"',
            "Cache-Control" => "max-age=0"
        ];

        foreach ($headers as $key => $value) {
            $h = $this->headerClosure;
            $h($key, $value);
        }

        $objWriter->save("php://output");
    }

    /**
     * @param \Closure $headerClosure
     */
    public function setHeaderClosure($headerClosure) {
        $this->headerClosure = $headerClosure;
    }
}
