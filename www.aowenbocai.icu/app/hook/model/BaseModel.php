<?php
namespace app\admin\model;

use think\Model;

class BaseModel extends Model
{
    protected $type_array;
    protected $status_array;
    protected $baseClassModel;

    protected function initialize()
    {
        $this->status_array = [0=>'禁用',1=>'显示'];
        parent::initialize();
    }

    /**
     * 获取器 - status
     */
    public function getStatusAttr($value)
    {
        $status = [0=>'禁用',1=>'正常'];
        return $status[$value];
    }

    /**
     *  status选择项
     */
    public function status_option($value="")
    {
        $html = '';
        foreach($this->status_array as $k=>$v){
            $selected = $k==$value?'selected':'';
            $html   .= ' <option '.$selected.' value="'.$k.'">'.$v.'</option>';
        }
        return $html;
    }

    /**
     *  type选择项
     * @param  string $value 当前type
     * @return json
     */
    public function type_option($value="")
    {
        $html = '';
        foreach($this->type_array as $k=>$v){
            $selected = $k==$value?'selected':'';
            $html   .= ' <option '.$selected.' value="'.$k.'">'.$v.'</option>';
        }
        return $html;
    }

    /**
     * 选分类
     */
    public function get_class($id="")
    {
        $class = $this->baseClassModel->order("id asc")->column('name','id');
        $html = '';
        foreach($class as $k=>$v){
            $selected = $k==$id?'selected':'';
            $html   .= ' <option '.$selected.' value="'.$k.'">'.$v.'</option>';
        }
        return $html;
    }

    /**
     * 选分类  $name 表单名$name
     */
    public function get_class_check($name="",$id="")
    {
        $class = $this->baseClassModel->order("id asc")->column('name','id');
        $html = '';
        foreach($class as $k=>$v){
            $id_array=explode(",",$id);
            $checked= in_array($k,$id_array)?'checked':'';
            $html   .= '<input type="checkbox" value="'.$k.'" name="'.$name.'['.$k.']" title="'.$v.'" '.$checked.'>';
        }
        return $html;
    }


}
