<?php

namespace app\admin\model;

use think\Model;
use think\Validate;

class Address extends Model
{
    protected $createTime = false;
    protected $updateTime = false;
    protected $resultSetType = 'collection';
}