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
     * @var bool
     */
    public $protectedClose;

    /**
     * @var mixed
     */
    public $action;

    /**
     * Main is invoked if method is not specified.
     * Check if you have to save changes or just exit
     *
     * @return void
     */
    // abstract public function main();

    /**
     * Controller constructor.
     */
    public function __construct()
    {
    }

    public function main()
    {
        $this->pre_load();
        $this->do_action();
    }

    public function pre_load()
    {
        $this->protectedClose = false;
        $this->action = filter_input(INPUT_POST, 'action', FILTER_DEFAULT);
    }

    public function do_action()
    {
        if (!isset($this->action)) {
            return;
        }

        switch ($this->action) {
            case 'save':
                $this->do_save();
                break;
            case 'exit':
                $this->do_exit();
            default:
                trigger_error("The '{$this->action}' action has not been defined!");
        }
    }

    public function do_save()
    {
    }

    public function do_exit()
    {
        header('Location: ' . BASE_URI);
    }

    public function getResource($string)
    {
        return Skin::getResource($string);
    }

}
