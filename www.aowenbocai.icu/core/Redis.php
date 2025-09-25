<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace core;

class Redis extends \think\cache\driver\Redis
{
    /**
     * 发布
     * @access public
     * @return bool
     */
    public function pub($channel, $msg)
    {
        return $this->handler->publish($channel, $msg);
    }

    /**
     * 订阅
     * @access public
     * @return bool
     */
    public function sub($channel, $callback)
    {
        return $this->handler->subscribe($channel, $callback);
    }

}
