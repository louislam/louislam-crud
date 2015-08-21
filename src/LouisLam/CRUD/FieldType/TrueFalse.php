<?php
/**
 * Created by PhpStorm.
 * User: Louis Lam
 * Date: 8/13/2015
 * Time: 9:33 PM
 */

namespace LouisLam\CRUD\FieldType;


class TrueFalse extends RadioButton
{

    /**
     * @var string[]
     */
    private $options;

    /**
     * RadioButton constructor.
     * @param string $true
     * @param string $false
     */
    public function __construct($true = "Yes", $false = "No") {
        parent::__construct([
            0 => $false,
            1 => $true
        ]);
    }

}

?>

