<?php
namespace app\admin\controller;

use app\admin\model\Ext;
use core\Curl;
use core\Setting;
use think\Config;
use app\admin\com\Database;
use think\Validate;

class UpdateVersion extends Base
{
    protected $server_url;
    public function __construct()
    {
        parent::__construct();
        $this->server_url = 'http://www.ocrm.cc/';
    }

    /**
     * 升级明细
    */
    public function index()
    {
        $version = Setting::get(['version']);
        return $this->fetch('index',['title' => '升级明细', 'version' =>  $version['version']]);
    }

    /**
     * 获取新闻公告
     */
    public function get_news($id = '')
    {
        $timestamp = time();
        $token = Config::get('baseConfig.authorization_token');
        $signature = md5($token . $timestamp);
        if ($id) {
            return Curl::get($this->server_url.'home/version/get_news_article?signature='.$signature. '&timestamp=' . $timestamp . '&id=' . $id, 'json');
        } else {
            return Curl::get($this->server_url.'home/version/get_news?signature='.$signature. '&timestamp=' . $timestamp . '&limit=9', 'json');
        }
    }

    /**
     * 获取升级明细信息
     */
    public function getVersionArticle($type = 1, $ext_name = '')
    {
        if ($type == 2 && $ext_name) {
            $ext_info = (new Ext())->where('name', $ext_name)->find();
            if (empty($ext_info))  return ['code' => 0, 'msg' => '参数错误'];
            $version = $ext_info['version'];
        } else {
            $version = Setting::get(['version'])['version'];
        }
        $timestamp = time();
        $token = Config::get('baseConfig.authorization_token');
        $signature = md5($token . $timestamp);
        $list = Curl::get($this->server_url.'home/version/get_version_article?version='.$version.'&type='.$type.'&ext_name='.$ext_name.'&signature='.$signature. '&timestamp=' . $timestamp, 'json');
        return $list;
    }

    /**
     * 获取版本信息  -- 获取下一个版本
     *
     */
    public function getVersion($type = 1, $ext_name = '')
    {
        if ($type == 2 && $ext_name) {
            $ext_info = (new Ext())->where('name', $ext_name)->find();
            if (empty($ext_info))  return ['code' => 0, 'msg' => '参数错误'];
            $version = $ext_info['version'];
        } else {
            $version = Setting::get(['version'])['version'];
        }
        $list = Curl::get($this->server_url.'home/version/get_version?version='.$version . '&type=' . $type . '&ext_name=' . $ext_name, 'json');
        if (!$list['code']) return ['code' => 1, 'version' => $version];
        return $list;
    }

    /**
     * 远程下载更新包
     * @param  string $ext_name [description]
     * @return array
     */
    public function download($type = 1, $ext_name = '')
    {
        $has_version = cache('has_update_version');
        if ($has_version and file_exists('../app/version/main.php')) {
            return ['code' => 1, 'msg' => '当前还有未完成的升级'];
        }
        $getVersion = $this->getVersion();
        $version = Setting::get(['version'])['version'];
        if ($getVersion['version'] == $version) return ['code' => 0, 'msg' => '已经是最新版本了'];

        if ($type == 2 && $ext_name) {
            $ext_info = (new Ext())->where('name', $ext_name)->find();
            if (empty($ext_info))  return ['code' => 0, 'msg' => '参数错误'];
            $version = $ext_info['version'];
        } else {
            $version = Setting::get(['version'])['version'];
        }
        $timestamp = time();
        $token = Config::get('baseConfig.authorization_token');
        $signature = md5($token . $timestamp);

        $url = $this->server_url.'home/version/download?version=' . $version . '&type=' . $type . '&ext_name=' . $ext_name . '&signature=' . $signature . '&timestamp=' . $timestamp;
        $file = (new Extend())->copyFile($url, $getVersion['version']);
        if (!(new Extend())->extractTo($file['filepath'], '../app/version')) {
            return ['code' => 0, 'msg' => '下载更新包失败，请检查写入权'];
        }
        cache('has_update_version', 1);
        return ['code' => 1, 'msg' => '下载更新包成功'];

    }

    /**
     * 安装更新包
     * @param  string $main [description]
     * @return array
     */
    public function install()
    {
        $plugin_path = '../app/version/';
        $main = include($plugin_path . 'main.php');

//        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('../'), \RecursiveIteratorIterator::SELF_FIRST);
//        foreach($iterator as $item) {
//            chmod($item, '777');
//        }

        if (isset($main['install']) and $main['install']['end_run'] and cache('has_update_version') == 1) {
            (new \app\version\controller\Install())->index();
            cache('has_update_version', 2);
        }

        if ( cache('has_update_version') == 2) {
            $this->copy_dir($plugin_path . 'main/', '../');//复制主文件
            cache('has_update_version', 3);
        }
        if (isset($main['install']) and $main['install']['sql'] and cache('has_update_version') == 3) {
            $db = new Database([1, $plugin_path . $main['install']['sql'], 1], array('path' => realpath($plugin_path) . DIRECTORY_SEPARATOR));
            $start = $db->import($start);
            cache('has_update_version', 4);
        }
        if (cache('has_update_version') == 4) {
            Setting::set('version', $main['version']);
        }
        deldir('../app/version');
        cache('has_update_version', null);
        return ['code' => 1];
    }

    protected function copy_dir($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->copy_dir($src . '/' . $file,$dst . '/' . $file);
                    continue;
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
        return true;
    }

}
