<?php
namespace app\common\controller;

use think\Controller;
use think\Db;

class Count extends Controller
{
    // 统计配置
    private $counConfig = [
            
        /**
        * 统计在线人数
        */
        'online' => [
            'type' => 0,
            'name' => 'action_time',
            'diff' => 10
        ],
        
        /**
        * 统计注册人数
        */
        'reg' => [
            'type' => 1,
            'name' => 'create_time',
            'diff' => 15
        ]
    ];

    public static function init()
    {
        $self = new self;
        $self->run();
    }

    public function run()
    {
        foreach($this->counConfig as $info) {
            $this->setCount($info);
        }
    }



    /**
     * 记录统计人数
     */
    private function setCount($info)
    {
        $tp = $info['type'];
        $name = $info['name'];

        $sum = 30 * 60;
        $sum_aft = 10 * 60;
        $last_data = Db::name('chart_data')->where('type', $tp)->order('id desc')->find();
        if (!$last_data) {
            $last_time = strtotime(date('Y-m-d 00:00:00'));
        } else {
            $last_time = strtotime($last_data['create_time']);
        }
        $now = time();
        $diff = $now - $last_time;

        if ($diff < $sum + $sum_aft) {
            return;
        }

        $insert_data = [];
        $length = floor(($diff - $sum_aft) / $sum);
        for($i = 1; $i <= $length; $i ++) {
            $count_end_time = date('Y-m-d H:i:s', $last_time + $i * $sum);
            $count = $this->byTimeCount($name, $count_end_time, $info['diff']);
            $insert_data[] = [
                'create_time' => $count_end_time,
                'type' => $tp,
                'value' => $count
            ];
        }
        Db::name('chart_data')->insertAll($insert_data);
    }

    /**
     * 根据时间统计人数
     */
    private function byTimeCount($name, $end_time, $diff = 10)
    {
        return Db::name('user')->
        where($name, '>=', date('Y-m-d H:i:s', strtotime($end_time . ' -' . $diff . ' minute')))->
        where($name, '<', date('Y-m-d H:i:s', strtotime($end_time . ' +' . $diff . ' minute')))->
        count();
    }

    public function isAjax()
    {
        $get_page = request()::instance()->header()['get-page'];
        if (!$get_page && request()->isAjax()) {
            return true;
        }
        return false;
    }
}
