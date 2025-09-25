<?php
namespace app\admin\controller;

use app\admin\model\ExtShowList;
use core\Curl;
use think\Config;
use think\Controller;
use think\Db;
use app\admin\model\Ext;
use app\admin\com\Database;

class Extend extends Base
{
    private $base_url = 'http://www.ocrm.cc';
    private $shop_url =  '/home/ext_shop/get_list';

    /**
     * 已安装扩展
     * @return index
     */
    public function index()
    {
        // $ext_list = $this->getExtShop();
        $ext_list = $this->getMyExt()->toArray();
        
        return $this->fetch('index',['title' => '扩展管理', 'ext_list' => $ext_list]);
    }

    /**
     * 未安装扩展-扩展商城
     * @return shop
     */
    public function shop()
    {
        $ext_list = $this->getExtShop();
        if (!$ext_list['code']) {
            $this->error($ext_list['msg']);
        }
        $ext_list = $ext_list['data'];
        $myExt = $this->getMyExt()->toArray();
        foreach($myExt as $item) {
            $k = $this->inObjVal('name', $item['name'], $ext_list);
            if ($k >= 0) {
                array_splice($ext_list, $k, 1);
            }
        }
        foreach ($ext_list as &$v) {
            $v['logo'] = $this->base_url . $v['logo'];
        }
        return $this->fetch('shop', ['title' => '扩展商城', 'ext_list' => $ext_list]);
    }

    /**
     * 远程获取扩展升级信息
     */
    public function getExtUpdate()
    {
        $timestamp = time();
        $token = Config::get('baseConfig.authorization_token');
        $signature = md5($token . $timestamp);
        $ext_list = Curl::get($this->base_url . $this->shop_url . '?signature=' . $signature . '&timestamp=' . $timestamp, 'json');
        return $ext_list;
    }

    /**
     * 远程获取扩展商城数据
     * @return getExtShop
     */
    public function getExtShop()
    {
        $timestamp = time();
        $token = Config::get('baseConfig.authorization_token');
        $signature = md5($token . $timestamp);
        $ext_list =  Curl::get($this->base_url . $this->shop_url . '?signature=' . $signature . '&timestamp=' . $timestamp, 'json');
        return $ext_list;
    }

    /**
     * 拓展遍历判断有无
     * @param  string $name 要遍历的 键
     * @param  string $value 要遍历的 值
     * @param  array $obj 要遍历的数组
     * @return boolean
     */
    private function inObjVal($name, $value, $obj)
    {
        $cache = [];
        foreach($obj as $k =>$item) {
            $cache[$item[$name]] = $k;
        }

        if (isset($cache[$value])) {
            return $cache[$value];
        }

        return -1;
    }

    /**
     * 获取已安装的拓展
     * @return array
     */
    public function getMyExt()
    {
        $Ext = new Ext;
        $myExt = $Ext->getAllExt();
        return $myExt;
    }

    /**
     * 卸载拓展
     * @param  string $ext_name [description]
     * @return disable
     */
    public function uninstall($ext_name)
    {
        $Ext = new Ext;
        $res = $Ext->getInfo($ext_name);
        if(!$res) {
            return ['code' => 0, 'msg' => '卸载失败，扩展不存在'];
        }
        $plugin_path = '../plugin/' . $ext_name . '/';
        $main = include($plugin_path . 'main.php');
        $move_res = $this->moveExtFile($ext_name);
        if (!$move_res['code']) return ['code' => 0, '移除文件失败'];
        $res->delete();
        (new ExtShowList())->where('name', '/' . $ext_name)->delete();
        if (isset($main['uninstall'])) {
			$db = new Database([1, $plugin_path . $main['uninstall']['sql'], 1], array('path' => realpath($plugin_path) . DIRECTORY_SEPARATOR));
			$start = $db->import($start);
            return ['code' => 1];
        }
        //$ext_list = json_decode(file_get_contents($this->shop_url), true);
    }

    /**
     * 移动扩展文件
     * @param  string $ext_name [description]
     * @return disable
     */
    private function moveExtFile($ext_name, $version = '')
    {
        $plugin_path = '../plugin/' . $ext_name . '/';

        $main = include($plugin_path . 'main.php');
        if ($version) $plugin_path .= $version . '/';
        $this->checkDir($plugin_path);
        $to_path = $plugin_path . 'main/';
        $main_path = APP_PATH . $ext_name . '/';
        if (isset($main['main_dir']) and $main['main_dir']) {//主文件目录
            $main_path = APP_PATH . $main['main_dir'] . $ext_name . '/';
        }
         if ($res = $this->moveFile($main_path, $to_path)) {
             return $res;
         }
        if (isset($main['public'])) {
            if ($move_public = $this->moveFile('./static/plugin/' . $ext_name . '/', $plugin_path . $main['public'])) {
                return $move_public; //移动静态资源失败
            }
        }
        return ['code' => 1];

    }

    /**
     * 禁用/启用拓展
     * @param  string $ext_name [description]
     * @return disable
     */
    public function disable($ext_name)
    {
        (new Ext)->disable($ext_name);
        return $this->index();
    }

    /**
     * 远程更新扩展
     * @param  string $ext_name [description]
     * @return download
     */
    public function update_download($ext_name)
    {
        $Ext = new Ext;
        $res = $Ext->getInfo($ext_name);
        $getVersion = (new UpdateVersion())->getVersion(2, $ext_name);
        if ($getVersion['version'] == $res['version']) return ['code' => 0, 'msg' => '已经是最新版本了'];
        $timestamp = time();
        $token = Config::get('baseConfig.authorization_token');
        $signature = md5($token . $timestamp);
        $check_auth =  Curl::get($this->base_url . '/home/ext_shop/checkBuy?version=' . $res['version'] .'&ext_name=' . $ext_name . '&signature=' . $signature . '&timestamp=' . $timestamp, 'json');
        if ($check_auth['code'] <= 0) return $check_auth;
        $url = $this->base_url.'/home/version/download?timestamp=' . $timestamp .'&version=' . $res['version'] . '&type=2&ext_name=' . $ext_name  .'&signature=' . $signature;
        $file = $this->copyFile($url, $ext_name.'-'.$getVersion['version']);
        $plugin_path = '../plugin/' . $ext_name . '/' . $getVersion['version'] . '/';
        if (!$this->extractTo($file['filepath'], '../plugin/' . $ext_name. '/' . $getVersion['version'])) {
            return ['code' => 0, 'msg' => '扩展安装失败，请检查写入权'];
        }
        //移动老版本文件
        $this->moveExtFile($ext_name, $res['version']);
        $main = include($plugin_path . 'update.php');
        return $this->install($main, $getVersion['version']);
    }

    /**
     * 远程下载文件开始
     * @param  string $ext_name [description]
     * @return download
     */
    public function download($ext_name)
    {
        $Ext = new Ext;
        $res = $Ext->getInfo($ext_name);
        if($res) {
            return ['code' => 0, 'msg' => '安装失败，扩展已存在'];
        }
        $timestamp = time();
        $token = Config::get('baseConfig.authorization_token');
        $signature = md5($token . $timestamp);
        $check_auth = Curl::get($this->base_url . '/home/ext_shop/checkBuy?timestamp=' . $timestamp . '&ext_name=' . $ext_name . '&signature=' . $signature);
        if ($check_auth['code'] <= 0) return $check_auth;
        $url = $this->base_url.'/home/ext_shop/download?timestamp=' . $timestamp . '&ext_name=' . $ext_name . '&signature=' . $signature;
        $file = $this->copyFile($url, $ext_name);
        $plugin_path = '../plugin/' . $ext_name . '/';
        if (!$this->extractTo($file['filepath'], '../plugin/' . $ext_name)) {
            return ['code' => 0, 'msg' => '扩展安装失败，请检查写入权'];
        }
        $main = include($plugin_path . 'main.php');
        return $this->install($main);
    }

    /**
     * 安装拓展
     * @param  string $main [description]
     * @return install
     */
    public function install($main, $version = '')
    {
        $ext_name = $main['name'];
        $Ext = new Ext;
        // 扩展相关信息插入数据库
        $plugin_path = '../plugin/' . $ext_name . '/';
        if ($version) $plugin_path .= $version . '/';
        $module_dir = APP_PATH . $ext_name . '/';
        if (isset($main['main_dir']) and $main['main_dir']) {//主文件目录
            $module_dir = APP_PATH . $main['main_dir'] . $ext_name . '/';
        }
        if ($move_main = $this->moveFile($plugin_path . 'main/', $module_dir)) {
            //print_r('移动主程序失败');
            return $move_main; //移动主程序失败
        }

        //前端静态文件处理
        if (isset($main['public'])) {
            if ($move_public = $this->moveFile($plugin_path . $main['public'], './static/plugin/' . $ext_name . '/')) {
                //print_r('移动静态资源失败');
                return $move_public; //移动静态资源失败
            }
        }

        if (isset($main['install'])) {
			$db = new Database([1, $plugin_path . $main['install']['sql'], 1], array('path' => realpath($plugin_path) . DIRECTORY_SEPARATOR));
			$start = $db->import($start);
        }
        if (!$version) {
            $Ext->insert([
                'name' => $main['name'],
                'title' => $main['title'],
                'logo' => '/static/plugin/' . $main['name'] . $main['logo'],
                'version' => $main['version'],
                'remark' => $main['remark']
            ]);
            $ext_show_model = new ExtShowList();
            $has = $ext_show_model->where('name', '/' . $main['name'])->find();
            if (empty($has)) {
                (new ExtShowList())->insert([
                    'name' => '/' . $main['name'],
                    'title' => $main['title'],
                    'type' => $main['type'],
                    'image' => '/static/plugin/' . $main['name'] . $main['logo'],
                    'img' => '/static/plugin/' . $main['name'] . $main['img'],
                    'info' => $main['info'],
                    'remark' => $main['remark']
                ]);
            }
        } else {
            $Ext->where('name', $main['name'])->update([
                'version' => $main['version'],
                'remark' => $main['remark']
            ]);
        }
        return ['code' => 1];

    }

    /**
     * SQL文件格式化
     * @param  string $sql [description]
     * @return string
     */
    public function parseSql($sql)
    {
        $lines=file($sql);
        $sqlstr="";
        foreach($lines as $k => $line){
            $line=trim($line);
                if($line!=""){
                if(!($line{0}=="#" || $line{0}.$line{1}=="--" || $line{0}.$line{1}.$line{2}=="/*!")){
                    $sqlstr.=$line;
                }
            }
        }
        $sqlstr=rtrim($sqlstr,";");
        $sqls=explode(";",$sqlstr);
        return $sqls;
    }

    /**
     * 远程拷贝文件
     * @param  string $url 远程地址
     * @return json
     */
    public function copyFile($url, $version = '')
    {
        set_time_limit (24 * 60 * 60);
        $destination_folder = '../temp/';
        if (!file_exists($destination_folder)){
            mkdir ($destination_folder,0777,true);
        }
        if ($version) {
            $newfname = $destination_folder . $version . '.zip';
        } else {
            $newfname = $destination_folder . basename($url);
        }
        $file = fopen ($url, "rb");
        if ($file) {
            $newf = fopen ($newfname, "wb");
            if ($newf){
                while(!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
                }
                fclose($newf);
            }
            fclose($file);
        }
        return array("err"=>0,"filepath"=>$newfname);
    }

    /**
     * 文件移动
     * @param  string $path   源文件路劲
     * @param  string $toPath 目标文件路劲
     * @return boolean
     */
    public function moveFile($source_name, $target_name)
    {
        //$target_name = $target_path.$target_filename;

        if(file_exists($target_name)||!file_exists($source_name)){
            //print_r('目标文件已经存在或者原始文件不存在。' . $target_name . '+++' . $source_name);
            //return ['code' => 0, 'msg' => '安装失败，扩展已存在'];// "目标文件已经存在或者原始文件不存在。";
        }

        if (!rename($source_name,$target_name)) {
            //print_r('移动失败');
            return ['code' => 0, 'msg' => '安装失败'];// "移动失败";
        }

        //删除文件

        // if(!file_exists($del_name)){
        //     print_r('要删除的文件不存在。');
        //     return;// "要删除的文件不存在。";
        // }

        // if (!unlink($del_name)) {
        //     return;// '删除失败'：
        // }

        return ;
    }

    /**
     * ZIP文件解压到指定目录
     * @param  string $filepath 压缩文件
     * @param  string $topath   解压目录
     * @return boolean
     */
    public function extractTo($filepath = null,$topath = null)
    {
        $zip = new \ZipArchive;
        $res = $zip->open($filepath);
        if ($res === TRUE) {
            $zip->extractTo($topath);
            $zip->close();
            unlink($filepath);
            return 1;
        }else{
            return;
        }
    }
    /**
     * 移动文件
     * @param  string           $path    保存路径
     * @param  string|bool      $savename    保存的文件名 默认自动生成
     * @param  boolean          $replace 同名文件是否覆盖
     * @return false|File false-失败 否则返回File实例
     */
    private function checkDir($path)
    {
        if (is_dir($path)) {
            return true;
        }
        if (mkdir($path, 0777, true)) {
            return true;
        } else {
            return false;
        }
    }
}
