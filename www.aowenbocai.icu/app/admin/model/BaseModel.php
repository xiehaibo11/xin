<?php

namespace app\admin\model;

use think\Model;

class BaseModel extends Model
{
    protected $type_array;
    protected $status_array;
    protected $award_array;
    protected $baseClassModel;
    protected $resultSetType = 'collection';

    protected function initialize()
    {
        $this->status_array = [0 => '禁用', 1 => '显示'];
        $this->award_array = [0 => '进行中', 1 => '已完成', '2' => '已领取'];
        parent::initialize();
    }

    /**
     * 获取器 - status
     */
    public function getStatusAttr($value,$data)
    {
        $html  = $value == 0 ?  '<font style="color: red;">' .$this->status_array[$value]. '</font>':'<font style="color: #5cb85c;" >' .$this->status_array[$value]. '</font>';
        return $html;
    }

      /**
     * 获取器 - getAward
     */
    public function getGetawardAttr($value,$data)
    {
        $color  = $value == 0 ?  'red' : ($value == 1 ? '#5cb85c' : '#369cc3') ;
        $html  =   '<font style="color: '.$color.';">' .$this->award_array[$value]. '</font>';
        return $html;
    }

    /**
     * 获取器 - 用户信息
     */
    public function getuserInfoAttr($value,$data)
    {
        $user = User::get($data['userid']);
        return ['nickname' => $user['nickname'], 'photo' =>  $user['photo']];
    }

     /**
     * 获取器 - 模块信息
     */
    public function getExtInfoAttr($value,$data)
    {
        if($data['countext'] == 'sign' || $data['countext'] == 'login'){
            $Ext['title'] = $data['countext'] == 'sign' ? '签到' : '登录';
        }else{
            $Ext = ExtShowList::get(['name' =>$data['countext']]);
            if($Ext) $Ext = $Ext ->toarray();
        }
        return ['title' => $Ext['title']];
    }

    /**
     *  status选择项
     */
    public function statusOption($value = "")
    {
        $html = '';
        foreach ($this->status_array as $k => $v) {
            $selected = $k == $value ? 'selected' : '';
            $html .= ' <option ' . $selected . ' value="' . $k . '">' . $v . '</option>';
        }
        return $html;
    }

    /**
     *  type选择项
     * @param  string $value 当前type
     * @return string
     */
    public function typeOption($value = "")
    {
        $html = '';
        foreach ($this->type_array as $k => $v) {
            $selected = $k == $value ? 'selected' : '';
            $html .= ' <option ' . $selected . ' value="' . $k . '">' . $v . '</option>';
        }
        return $html;
    }

    /**
     * 模块选项
     */
    public function extOption($value = "")
    {
        $extshow = new ExtShowList;
        $res = $extshow->getList();
        $html = '';
        foreach ($res as $v) {
            $selected = $v['name'] == $value ? 'selected' : '';
            $html .= ' <option ' . $selected . ' value="' . $v['name'] . '">' . $v['title'] . '</option>';
        }
        return $html;
    }

    /**
     * 选分类
     */
    public function getClass($id = "")
    {
        $class = $this->baseClassModel->order("id asc")->column('name', 'id');
        $html = '';
        foreach ($class as $k => $v) {
            $selected = $k == $id ? 'selected' : '';
            $html .= ' <option ' . $selected . ' value="' . $k . '">' . $v . '</option>';
        }
        return $html;
    }

    /**
     * 选分类  $name 表单名$name
     */
    public function getClassCheck($name = "", $id = "")
    {
        $class = $this->baseClassModel->order("id asc")->column('name', 'id');
        $html = '';
        foreach ($class as $k => $v) {
            $id_array = explode(",", $id);
            $checked = in_array($k, $id_array) ? 'checked' : '';
            $html .= '<input type="checkbox" value="' . $k . '" name="' . $name . '[' . $k . ']" title="' . $v . '" ' . $checked . '>';
        }
        return $html;
    }

}
