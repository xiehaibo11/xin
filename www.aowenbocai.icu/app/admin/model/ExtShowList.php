<?php

namespace app\admin\model;

use think\Validate;

class ExtShowList extends BaseModel
{
    protected $updateTime = false;
    protected $createTime = false;
    protected $resultSetType = 'collection';

    public function getList($where = [])
    {
        if (!empty($where)) $this->where($where);
        $res =  $this->order(['sort' => "asc"])->select()->each(function ($item, $key) {
            if ($item['type'] == 1) {
                $name = ltrim($item['name'], '/');
                $item['admin_url'] = $name == 'pk10' ? '/lottery/pk10/index' : '/lottery/lottery_bouns/index/name/'.$name;
            } else {
                $name = explode('game/', $item['name']);
                if(count($name) > 1){
                    $item['admin_url'] =  $name[1] == 'guess' ? 'guess' : '';
                }else{
                    $item['admin_url'] =  '/'.ltrim($item['name'], '/'). '/admin';
                }
            }
        });
        return  $res;
    }

    public function getUseList()
    {
        return $this->field('title,name')->where(['status' => 0])->select();
    }


    public function addList($data)
    {
        $res = $this->where(['name' => $data['name']])->find();
        if ($res) {
            $msg['code'] = 0;
            $msg['msg'] = '该游戏已在列表，请添加其他';
            return $msg;
        }
        $res = $this->save($data);
        $id = $this->id;
        $sort = count($this->select());
        if ($id) {
            $msg['code'] = $this->where(['id' => $id])->setField('sort', $sort);
            $msg['msg'] = '游戏添加成功';
            return $msg;
        }
        return ['code' => 0, 'msg' => '游戏添加失败'];
    }

    public function setIdSort($id, $up)
    {
        $sort = $this->field(['sort'])->find($id)->getAttr('sort');
        $newSort = $up == 1 ? ($sort - 1) : ($sort + 1);
        $res2 = $this->where(['sort' => $newSort])->update(['sort' => $sort]);
        $res1 = $this->where(['id' => $id])->update(['sort' => $newSort]);
        $msg = $res1 && $res2 ? ['code' => 1, 'msg' => '修改成功'] : ['code' => 1, 'msg' => '修改失败，请重试'];
        return $msg;
    }

    // 获取某一个具体信息
    public function getInfo($id)
    {
		
        return $this->where(['id' => $id])->find();
    }

    // 获取某一个具体信息
    public function getInfoByName($name)
    {
        return $this->where(['name' => $name])->find();
    }

    // 修改信息
    public function updateData($data)
    {
        $info = $this->find($data['id']);
        $res = $info->save($data);
        if ($res) {
            (new Ext())->where('name', str_replace('/','',$data['name']))->update(['title' => $info['title']]);
        }
        return $res;
    }

    public function deleteData($id)
    {
        $sort = $this->where('id', $id)->column('sort');
        if($sort){
            $res = $this->where('sort', '>', $sort[0])->setDec('sort', 1);
            if($res){
                return $this->destroy($id);
            }
        }
        return false;
    }
    public function getRecodeExt($type =  0)
    {
        $res = $this->field('name,title,type,id')->where('status', 0)->where('type', $type)->select();
        if(!empty($res)){
            $res = $res->toArray();
            foreach ($res as &$value) {
                $name = explode("/",$value['name']);
                $length = count($name);
                $name = array_filter($name);
                if($value['type'] == 1){
                    $value['name'] = $name[$length - 1];
                    continue;
                }
                $value['name'] = url('/../'.$name[$length - 1].'/admin/record');

            }
        }
        return $res;
    }
}