<?php
/**
 * Created by PhpStorm.
 * User: LouisLam
 * Date: 29/1/2017
 * Time: 2:44 下午
 */

namespace LouisLam\CRUD;

class AdminLTESetting {
    public const Blue = "skin-blue";
    public const Black = "skin-black";
    public const Purple = "skin-purple";

    private static $instance = null;

    private $fixedLayout = false;
    private $boxedLayout = false;
    private $skin = AdminLTESetting::Blue;
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new AdminLTESetting();
        }
        return self::$instance;
    }

    /**
     * @return boolean
     */
    public function isFixedLayout() {
        return $this->fixedLayout;
    }

    /**
     * @param boolean $fixedLayout
     */
    public function setFixedLayout($fixedLayout) {
        $this->fixedLayout = $fixedLayout;
    }

    /**
     * @return boolean
     */
    public function isBoxedLayout() {
        return $this->boxedLayout;
    }

    /**
     * @param boolean $boxedLayout
     */
    public function setBoxedLayout($boxedLayout) {
        $this->boxedLayout = $boxedLayout;
    }

    /**
     * @return string
     */
    public function getSkin() {
        return $this->skin;
    }

    /**
     * @param string $skin
     */
    public function setSkin($skin) {
        $this->skin = $skin;
    }
}
