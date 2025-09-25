<?php
namespace app\attack\controller;

use app\index\controller\Base as ABase;

class Base extends ABase
{
    protected $param;
    protected $post;
    protected $id;
    public function __construct()
    {
        parent::__construct();
    }
}
