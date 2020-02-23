<?php
/**
 * Alxarafe. Development of PHP applications in a flash!
 * Copyright (C) 2018 Alxarafe <info@alxarafe.com>
 */

namespace Alxarafe\Base;

use Alxarafe\Helpers\Skin;

/**
 * Class Controller
 *
 * @package Alxarafe\Base
 */
abstract class Controller
{
    /**
     * True to confirm exit before losing changes
     *
     * @var bool
     */
    public $protectedClose;

    /**
     * Contains the action to execute
     *
     * @var string|null
     */
    public $action;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->protectedClose = false;
        $this->action = null;
    }

    public function getResource($string)
    {
        return Skin::getResource($string);
    }

    /**
     * Main is invoked if method is not specified.
     * Check if you have to save changes or just exit
     *
     * @return void
     */
    public function main()
    {
        $this->preAction();
        $this->doAction();
        $this->postAction();
    }

    public function preAction()
    {
        $this->action = filter_input(INPUT_POST, 'action', FILTER_DEFAULT);
    }

    public function doAction()
    {
        if (!isset($this->action)) {
            return;
        }

        switch ($this->action) {
            case 'submit':
            case 'save':
                $this->doSave();
                break;
            case 'exit':
                $this->doExit();
            default:
                trigger_error("The '{$this->action}' action has not been defined!");
        }
    }

    public function postAction()
    {

    }

    public function doSave()
    {
    }

    public function doExit()
    {
        header('Location: ' . BASE_URI);
    }

}
