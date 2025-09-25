<?php
namespace app\admin\controller;

use think\Validate;

class Restart extends Base
{
    protected $type_array;
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->type_array  = [ 1 => '打地鼠', 2 => '欢乐捕鱼', 3 => '动物园', 4 => '系统开奖/零点统计'];
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index()
    {
        $this->assign('select_option', $this->DataCheck('type', $this->type_array));
        return $this->fetch('index',['title'=>'重启管理']);
    }

    public function doStop()
    {
        $game_stop_node = [
            1 => 'forever stop dishu',
            2 => 'forever stop fish',
            3 => 'forever stop animal',
            4 => 'forever stop zero',
        ];
        if(request()->isPost()){
            $data = input('post.');
            $res = false;
            $msg = '';
            if (!$data['type']) return $this->error('请选择游戏',url('index'));
            foreach ($data['type'] as $v) {
                $res = exec($game_stop_node[$v]);
                $msg .= $this->type_array[$v];
                if (!$res) {
                    $msg .='停止失败; ';
                } else {
                    $msg .='停止成功; ';
                }
            }
            return $this->success($msg,url('index'));
        }
    }

    public function doRestart()
    {
        $game_restart_node = [
            1 => 'forever restart dishu',
            2 => 'forever restart fish',
            3 => 'forever restart animal',
            4 => 'forever restart zero',
        ];
        if(request()->isPost()){
            $data = input('post.');
            $res = false;
            $msg = '';
            if (!$data['type']) return $this->error('请选择游戏',url('index'));
            foreach ($data['type'] as $v) {
                $res = exec($game_restart_node[$v]);
                $msg .= $this->type_array[$v];
                if (!$res) {
                    $res = $this->startGame($v);
                    if (!$res)
                        $msg .='重启失败; ';
                    else
                        $msg .='启动成功: ';
                } else {
                    $msg .='重启成功; ';
                }
            }
            return $this->success($msg,url('index'));
        }
    }

    /**
     * 启动游戏
    */
    public function startGame($type)
    {
        $res = false;
        $node_dir = $_SERVER['DOCUMENT_ROOT'];
        if (substr($node_dir, -1) == '/') {
            $node_dir = substr($node_dir, 0, -7);
        } else {
            $node_dir = substr($node_dir, 0, -6);
        }
        $node_dir .= 'node_server/';
        $game_start_node = [
            1 => 'forever start --uid "dishu" -a ' . $node_dir . 'dishu/src/index.js',
            2 => 'forever start --uid "fish" -a ' . $node_dir . 'fish/src/index.js',
            3 => 'forever start --uid "animal" -a ' . $node_dir . 'animal/src/index.js',
            4 => 'forever start --uid "zero" -a ' . $node_dir . 'zero_task/index.js',
        ];
        switch ($type) {
            case 0:
                foreach ($game_start_node as $v) {
                    $res = exec($v);
                }
                break;
            case 1:
                $res = exec($game_start_node[1]);
                break;
            case 2:
                $res = exec($game_start_node[2]);
                break;
            case 3:
                $res = exec($game_start_node[3]);
                break;
            case 4:
                $res = exec($game_start_node[4]);
                break;
        }
        return $res;
    }

    /**
     *  type选择项
     * @param  string $value 当前type
     * @return string
     */
    private function DataCheck($field, $data = [], $id = "")
    {
        if (empty($data)) return '';
        $html = '';
        foreach ($data as $k => $v) {
            if (!is_array($id)) {
                $id_array = explode(",", $id);
            } else {
                $id_array = $id;
            }
            $checked = in_array($k, $id_array) ? 'checked' : '';
            $html .= '<input type="checkbox" value="' . $k . '" name="' . $field . '[' . $k . ']" title="' . $v . '" ' . $checked . '><label style="margin: 0 15px 0 5px;">' . $v . '</label>';
        }
        return $html;
    }

}
