<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/25/2015
 * Time: 12:08 PM
 */

namespace LouisLam\CRUD;


use PHPExcel;
use PHPExcel_Writer_Excel2007;

class ExcelHelper
{

    private $headerClosure;

    public function __construct() {
        $this->headerClosure= function ($key, $value) {
            header("$key: $value");
        };
    }

    public function genExcel(LouisCRUD $crud, array $list, $filename = null) {
        $excel = new PHPExcel();

        $excel->createSheet();

        $objWriter = new PHPExcel_Writer_Excel2007($excel);
        //$rand = dechex(rand(0, 99999999));

        if ($filename == null) {
            $name = $crud->getTableName();
            $date = date("Y-m-d", time());
            $filename = "$name-$date.xlsx";
        }

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0'
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
    public function setHeaderClosure($headerClosure)
    {
        $this->headerClosure = $headerClosure;
    }


}