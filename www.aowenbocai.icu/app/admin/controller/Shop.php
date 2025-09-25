<?php
namespace app\admin\controller;

use app\admin\model\Shop as Ashop;
use app\admin\model\ShopNav;
use think\Validate;

class Shop extends Base
{
    public function __construct()
    {
        $this->param        = request()->param();
        $this->post         = request()->post();
        $this->baseModel  = new Ashop();
        $this->id           = isset($this->param['id'])?intval($this->param['id']):'';
        parent::__construct();
    }

    public function index($words = '', $starttime = '', $endtime = '')
    {
        $param  = $this->param;
        if ($words) {
            $this->baseModel->where('name', 'like', "%{$words}%");
        }
        if ($starttime) {
            $this->baseModel->where('create_time', '>=', $starttime);
        }
        if ($endtime) {
            $endtime = date('Y-m-d', strtotime($endtime . ' +1 day'));
            $this->baseModel->where('create_time', '<', $endtime);
        }
        if($param['type']){
            $this->baseModel->where('type', $param['type']);
            $name = (new ShopNav)->where('id', $param['type'])->column('name');
            $param['type'] = empty($name) ? '栏目错误' : $name[0];
        }else{
             $param['type'] = '选择商品类型';
        }
        $list = $this->baseModel->order('create_time desc')->paginate(15,false,['query'=>$param]);
        $this->assign("list",$list);
        $this->assign("query",$param);
        $nav = (new ShopNav)->where('status', 1)->select();
        return $this->fetch('index',['title'=>'商品管理', 'nav' => $nav]);
    }

    public function add()
    {
        if(request()->isPost()){
            $data = input('post.');
            $res =  $this->baseModel->add($data);
            if ($res['code']) {
                $this->success('添加成功',url('index'));
            } else {
                $this->error($res['msg']);
            }
        }
        $nav = (new ShopNav)->where('status', 1)->select();
        return $this->fetch('add', ['title' => '添加商品', 'nav' => $nav]);

    }

    public function edit()
    {
        if(request()->isPost()){
            $data = input('post.');
            $res =  $this->baseModel->add($data,'edit');
            if ($res['code']) {
                $this->success('更新成功',url('index'));
            } else {
                $this->error($res['msg']);
            }
        }
        $info = $this->baseModel->find($this->id);
        $info->statusNum = $info->getData('status');
        $info->typeNum = $info->getData('type');
        $nav = (new ShopNav)->where('status', 1)->select();
        $this->assign('info', $info);
        return $this->fetch('edit', ['title' => '编辑商品', 'nav' => $nav]);

    }

    public function base64Upload($base64_data)
    {
        $path = base64_upload($base64_data,'uploads/image/') . '?t=' . time();
        return ['code' => 1, 'data' => $path];
    }

    public function upload()
    {
        $file = request()->file('file');
        $return_data = [
            'code' => 1,
            'msg' => '上传成功'
        ];

        // 安全验证
        if (!$file) {
            return json(['code' => 0, 'msg' => '没有文件上传']);
        }

        // 验证文件大小（5MB）
        if ($file->getSize() > 5242880) {
            return json(['code' => 0, 'msg' => '文件大小超过限制']);
        }

        // 验证文件扩展名
        $allowedExt = ['gif', 'jpg', 'jpeg', 'png'];
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, $allowedExt)) {
            return json(['code' => 0, 'msg' => '不允许的文件类型']);
        }

        // 验证MIME类型
        $allowedMimes = ['image/gif', 'image/jpeg', 'image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->getPathname());
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            return json(['code' => 0, 'msg' => '文件类型验证失败']);
        }

        // 验证文件内容（检查是否为真实图片）
        $imageInfo = getimagesize($file->getPathname());
        if ($imageInfo === false) {
            return json(['code' => 0, 'msg' => '文件不是有效的图片']);
        }

        // 生成安全的文件名
        $safeName = uniqid() . '.' . $ext;

        $info = $file->move(ROOT_PATH . 'public/static/images/shop/shop_phto', $safeName);
        if($info){
            $return_data['save_type'] = $info->getExtension();
            $return_data['filename'] = $safeName;
        }else{
            $return_data['code'] = 0;
            $return_data['msg'] = $file->getError();
        }
        return json($return_data);
    }

    /**商品栏目 */
    public function shopNav()
    {
        if(request()->isPost()){
            $name = trim(request()->post()['name']);
            if(!$name){
                return json(['err' => 1, 'msg' =>'栏目名错误']);
            }
            if(mb_strlen($name, 'utf-8') > 8){
                return json(['err' => 1, 'msg' =>'栏目名长度大于8位']);
            }
            $shopnav = new ShopNav;
            $res = $shopnav->save(['name' => $name]);
            $id = $shopnav->max('id');
            if(!$res){
                return json(['err' => 2, 'msg' =>'添加失败']);
            }
            return json(['err' => 0, 'data' => ['name' => $name, 'id' => $id]]);
        }
        $nav = (new ShopNav)->order("id desc")->paginate(14);
        return $this->fetch('nav',['title' => '栏目管理', 'list' => $nav]);
    }
}
