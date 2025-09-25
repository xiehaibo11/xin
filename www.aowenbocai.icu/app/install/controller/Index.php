<?php
namespace app\install\controller;

use app\admin\com\Database;
use app\index\controller\Base;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Env;
use think\Validate;

class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $lock = Env::get('install.lock') ? Env::get('install.lock') : false;
        if ($lock) {
            return $this->error('您已经安装过了，不能重复安装!', url('index/index/index'));
        }
         return $this->fetch('index',['title' => '安装系统 - 许可协议', 'progress' => 1]);
    }

    /**
     * 环境监测
    */
    public function environment() {
        if (request()->isPost()) {
            $data = request()->post();
            if (isset($data['protocol']) and $data['protocol']) {
                //服务器信息
                $ret['server']['os']['value'] = php_uname();//服务器操作系统
                $ret['server']['os']['class'] = '';
                $ret['server']['os']['remark'] = '';
                if(PHP_SHLIB_SUFFIX == 'dll') {
                    $ret['server']['os']['remark'] = '建议使用 Linux 系统以提升程序性能';
                    $ret['server']['os']['class'] = 'warning';
                }
                $ret['server']['sapi']['value'] = $_SERVER['SERVER_SOFTWARE'];//Web服务器环境
                $ret['server']['sapi']['class'] = '';
                $ret['server']['sapi']['remark'] = '';
                if(PHP_SAPI == 'isapi') {
                    $ret['server']['sapi']['remark'] = '建议使用 Apache 或 Nginx 以提升程序性能';
                    $ret['server']['sapi']['class'] = 'warning';
                }
                $ret['server']['php']['value'] = PHP_VERSION;//PHP版本
                $ret['server']['dir']['value'] = str_replace("\\",'/', dirname(__FILE__));//程序安装目录
                if(function_exists('disk_free_space')) {
                    $ret['server']['disk']['value'] = floor(disk_free_space($ret['server']['dir']['value']) / (1024*1024)).'M';//磁盘空间
                } else {
                    $ret['server']['disk']['value'] = 'unknow';
                }
                $ret['server']['upload']['value'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';//上传大小
                //服务器信息  end

                //php信息
                $ret['php']['version']['value'] = PHP_VERSION;
                $ret['php']['version']['class'] = 'success';
                $ret['php']['version']['failed'] = false;
                $ret['php']['version']['remark'] = '';
                if(version_compare(PHP_VERSION, '5.5.0') == -1) {
                    $ret['php']['version']['class'] = 'danger';
                    $ret['php']['version']['failed'] = true;
                    $ret['php']['version']['remark'] = 'PHP版本必须为 5.5.0 以上. ';
                }
                $ret['php']['pdo']['ok'] = extension_loaded('pdo') && extension_loaded('pdo_mysql');
                if($ret['php']['pdo']['ok']) {
                    $ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
                    $ret['php']['pdo']['class'] = 'success';
                    $ret['php']['pdo']['remark'] = '';
                    $ret['php']['pdo']['failed'] = false;
                } else {
                    $ret['php']['pdo']['failed'] = true;
                    $ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-remove text-warning"></span>';
                    $ret['php']['pdo']['class'] = 'warning';
                    $ret['php']['pdo']['remark'] = '您的PHP环境不支持PDO, 请开启此扩展.';
                }

                $ret['php']['fopen']['ok'] = @ini_get('allow_url_fopen') && function_exists('fsockopen');
                if($ret['php']['fopen']['ok']) {
                    $ret['php']['fopen']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
                } else {
                    $ret['php']['fopen']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                }

                $ret['php']['curl']['ok'] = extension_loaded('curl') && function_exists('curl_init');
                if($ret['php']['curl']['ok']) {
                    $ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
                    $ret['php']['curl']['class'] = 'success';
                    $ret['php']['curl']['remark'] = '';
                    $ret['php']['curl']['failed'] = false;
                } else {
                    $ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                    $ret['php']['curl']['class'] = 'danger';
                    $ret['php']['curl']['remark'] = '您的PHP环境不支持cURL, 也不支持 allow_url_fopen, 系统无法正常运行. ';
                    $ret['php']['curl']['failed'] = true;
                }

                $ret['php']['ssl']['ok'] = extension_loaded('openssl');
                if($ret['php']['ssl']['ok']) {
                    $ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
                    $ret['php']['ssl']['class'] = 'success';
                    $ret['php']['ssl']['remark'] = '';
                    $ret['php']['ssl']['failed'] = false;
                } else {
                    $ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                    $ret['php']['ssl']['class'] = 'danger';
                    $ret['php']['ssl']['failed'] = true;
                    $ret['php']['ssl']['remark'] = '没有启用OpenSSL, 将无法访问公众平台的接口, 系统无法正常运行.';
                }

                $ret['php']['gd']['ok'] = extension_loaded('gd');
                if($ret['php']['gd']['ok']) {
                    $ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
                    $ret['php']['gd']['class'] = 'success';
                    $ret['php']['gd']['remark'] = '';
                    $ret['php']['gd']['failed'] = false;
                } else {
                    $ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                    $ret['php']['gd']['class'] = 'danger';
                    $ret['php']['gd']['failed'] = true;
                    $ret['php']['gd']['remark'] = '没有启用GD, 将无法正常上传和压缩图片, 系统无法正常运行.';
                }

                $ret['php']['dom']['ok'] = class_exists('DOMDocument');
                if($ret['php']['dom']['ok']) {
                    $ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
                    $ret['php']['dom']['class'] = 'success';
                    $ret['php']['dom']['remark'] = '';
                    $ret['php']['dom']['failed'] = false;
                } else {
                    $ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                    $ret['php']['dom']['class'] = 'danger';
                    $ret['php']['dom']['failed'] = true;
                    $ret['php']['dom']['remark'] = '没有启用DOMDocument, 将无法正常安装使用模块, 系统无法正常运行. ';
                }

                $ret['php']['session']['ok'] = ini_get('session.auto_start');
                if($ret['php']['session']['ok'] == 0 || strtolower($ret['php']['session']['ok']) == 'off') {
                    $ret['php']['session']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
                    $ret['php']['session']['class'] = 'success';
                    $ret['php']['session']['remark'] = '';
                    $ret['php']['session']['failed'] = false;
                } else {
                    $ret['php']['session']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                    $ret['php']['session']['class'] = 'danger';
                    $ret['php']['session']['failed'] = true;
                    $ret['php']['session']['remark'] = '系统session.auto_start开启, 将无法正常注册会员, 系统无法正常运行. ';
                }

                $ret['php']['asp_tags']['ok'] = ini_get('asp_tags');
                if(empty($ret['php']['asp_tags']['ok']) || strtolower($ret['php']['asp_tags']['ok']) == 'off') {
                    $ret['php']['asp_tags']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
                    $ret['php']['asp_tags']['class'] = 'success';
                    $ret['php']['asp_tags']['remark'] = '';
                    $ret['php']['asp_tags']['failed'] = false;
                } else {
                    $ret['php']['asp_tags']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                    $ret['php']['asp_tags']['class'] = 'danger';
                    $ret['php']['asp_tags']['failed'] = true;
                    $ret['php']['asp_tags']['remark'] = '请禁用可以使用ASP 风格的标志，配置php.ini中asp_tags = Off';
                }

                $ret['write']['root']['ok'] = $this->local_writeable(str_replace("\\",'/', dirname(__FILE__)) . '/');
                if($ret['write']['root']['ok']) {
                    $ret['write']['root']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
                    $ret['write']['root']['class'] = 'success';
                    $ret['write']['root']['failed'] = false;
                    $ret['write']['root']['remark'] = '';
                } else {
                    $ret['write']['root']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                    $ret['write']['root']['class'] = 'danger';
                    $ret['write']['root']['failed'] = true;
                    $ret['write']['root']['remark'] = '本地目录无法写入, 将无法使用自动更新功能, 系统无法正常运行';
                }
                //php信息 - end

                $ret['continue'] = true;
                foreach($ret['php'] as $opt) {
                    if(isset($opt['failed']) and $opt['failed']) {
                        $ret['continue'] = false;
                        break;
                    }
                }
                if( $ret['write']['root']['failed']) {
                    $ret['continue'] = false;
                }

                return $this->fetch('environment',['title' => '安装系统 - 环境监测', 'progress' => 2, 'ret' => $ret]);
            } else {
                $this->error('请选择是否同意许可协议',url('index'));
            }
        }
    }

    /**
     * 参数配置
     */
    public function doForm() {
        if (request()->isPost()) {
            $data = request()->post();
            if ($data['do']) {
                return $this->fetch('do_form',['title' => '安装系统 - 参数配置', 'progress' => 3]);
            }
        } else {
            $this->error('请选择是否同意许可协议',url('index'));
        }
    }

    /**
     * 安装判断
     */
    public function installData() {
        if (request()->isPost()) {
            $db = request()->post();
            //判断数据库连接是否成功

            try {
                $pieces = explode(':', $db['server']);
                $db['server'] = $pieces[0];
                $db['port'] = !empty($pieces[1]) ? $pieces[1] : '3306';
                $link = new \PDO("mysql:host={$db['server']};port={$db['port']}", $db['database_username'], $db['database_password']); 	// dns可以没有dbname
                $link->exec("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
                $link->exec("SET sql_mode=''");
                $error = ['err' => 0];
                if ($link->errorCode() != '00000') {
                    $errorInfo = $link->errorInfo();
                    $error = ['err' => 1, 'msg' => $errorInfo[2]];
                } else {
                    $statement = $link->query("SHOW DATABASES LIKE '{$db['database_name']}';");
                    $fetch = $statement->fetch();
                    if (empty($fetch)){
                        if (substr($link->getAttribute(\PDO::ATTR_SERVER_VERSION), 0, 3) > '4.1') {
                            $link->query("CREATE DATABASE IF NOT EXISTS `{$db['database_name']}` DEFAULT CHARACTER SET utf8");
                        } else {
                            $link->query("CREATE DATABASE IF NOT EXISTS `{$db['database_name']}`");
                        }
                    }
                    $statement = $link->query("SHOW DATABASES LIKE '{$db['database_name']}';");
                    $fetch = $statement->fetch();
                    if (empty($fetch)) {
                        $error = ['err' => 3, 'msg' => '数据库不存在且创建数据库失败'];
                    }
                    if ($link->errorCode() != '00000') {
                        $errorInfo = $link->errorInfo();
                        $error = ['err' => 1, 'msg' => $errorInfo[2]];
                    }
                }
            } catch (\PDOException $e) {
                $error = $e->getMessage();
                if (strpos($error, 'Access denied for user') !== false) {
                    $error = ['err' => 2, 'msg' => '您的数据库访问用户名或是密码错误'];
                } else {
                    $error = ['err' => 1, 'msg' => $error];
                }
            }
            if(!$error['err']) {
                $link->exec("USE {$db['database_name']}");
                $statement = $link->query("SHOW TABLES LIKE 'kr_%';");
                if ($statement->fetch()) {
                    $error = ['err' => 3, 'msg' => '您的数据库不为空!'];
                }
            }
            if(!$error['err']) {
                if (!is_file('admin.php')) {
                    return $error = ['err' => 4, 'msg' => '后台文件admin.php不存在'];
                }
            }
            if ($error['err']) {
                return $this->fetch('err_form',['title' => '安装系统 - 参数配置', 'progress' => 3, 'error' => $error, 'data' => $db]);
            } else {

                $string ="hostname = ".$db['server']."\ndatabase_database = '".$db['database_name']."'\ndatabase_username =  '".$db['database_username']."'\ndatabase_password =  '".$db['database_password']."'\nauthorization_token =  '".$db['authorization_token']."'";
                file_put_contents("../.env", $string );

                return $this->fetch('install_sql',['title' => '安装系统 - 安装数据库', 'progress' => 4, 'user' => [
                    'user_username' => $db['user_username'],
                    'user_password' => $db['user_password'],
                    'admin_file' => $db['admin_file'],
                ]]);
            }

        } else {
            $this->error('请选择是否同意许可协议',url('index'));
        }
    }

    /**
     * 执行数据库操作
     */
    public function overSql() {
        $lock = Env::get('install.lock') ? Env::get('install.lock') : false;
        if ($lock) {
            return $this->error('您已经安装过了，不能重复安装!', url('index/index/index'));
        }

        $db = request()->post();
        $id = Db::name('user')->insertGetId([
            'username' => $db['user_username'],
            'password' => md5($db['user_password']),
            'nickname' => '管理员',
            'create_time' => date('Y-m-d H:i:s'),
            'reg_ip' => request()->ip(),
            'sid' => $this->getRandChar(32),
        ]);
        Db::name('admin')->insert([
            'status' => 1,
            'userid' => $id
        ]);
        //后台文件改名
        if (is_file('admin.php')) {
            rename('admin.php', $db['admin_file']);
        } else {
            return $this->error('后台文件admin.php不存在');
        }
        $handle=fopen('../.env',"a+");
        fwrite($handle,"\ninstall_lock=true");
        fclose($handle);

        return ['code' => 1];
    }

    /**
     * 获取随机字符串
     * @return string
     */
    private function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

    /**
     * 安装完成
     */
    public function complete($admin_file) {

        return $this->fetch('complete',['title' => '安装系统 - 安装完成', 'progress' => 5, 'admin_file' => $admin_file]);

    }

    /**
     * 还原数据库
     * @author 艺品网络  <twothink.cn>
     */
    public function import( $part = null, $start = null) {
        if (is_null($part) && is_null($start)) {
            //初始化
            //获取备份文件信息
            $name  = 'install*.sql*';
            $path  = '../data/' . $name;
            $files = glob($path);
            $list  = array();
            foreach ($files as $name) {
                $basename        = basename($name);
                $match           = sscanf($basename, 'install');
                $gz              = preg_match('/^\install.sql.gz$/', $basename);
                $list[1] = array(1, $name, $gz);
            }

            ksort($list);
            //检测文件正确性
            $last = end($list);
            if (count($list) === $last[0]) {
                session('backup_list', $list); //缓存备份列表
                return $this->success('初始化完成！', '', array('part' => 1, 'start' => 0));
            } else {
                return $this->error('备份文件可能已经损坏，请检查！');
            }
        } elseif (is_numeric($part) && is_numeric($start)) {
            $list = session('backup_list');
            // foreach($list as $part => $value) {
            // 	$db = new \app\admin\com\Database($list[$part], array('path' => realpath(config('data_backup_path')) . DIRECTORY_SEPARATOR, 'compress' => $list[$part][2]));
            // 	$start = $db->import($start);
            // }
            //print_r($list[$part]);
            $db = new \app\admin\com\Database($list[$part], array('path' => realpath(config('data_backup_path')) . DIRECTORY_SEPARATOR, 'compress' => $list[$part][2]));

            $start = $db->import($start);

            if (false === $start) {
                return $this->error('还原数据出错！');
            } elseif (0 === $start) {
                //下一卷
                if (isset($list[++$part])) {
                    $data = array('part' => $part, 'start' => 0);
                    return $this->success("正在安装...#{$part}", '', $data);
                } else {
                    session('backup_list', null);
                    return $this->success('安装完成！');
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if ($start[1]) {
                    $rate = floor(100 * ($start[0] / $start[1]));
                    return $this->success("正在安装...#{$part} ({$rate}%)", '', $data);
                } else {
                    $data['gz'] = 1;
                    return $this->success("正在安装...#{$part}", '', $data);
                }
            }
        } else {
            return $this->error('参数错误！');
        }
    }

    private  function local_writeable($dir) {
        $writeable = 0;
        if(!is_dir($dir)) {
            @mkdir($dir, 0777);
        }
        if(is_dir($dir)) {
            if($fp = fopen("$dir/test.txt", 'w')) {
                fclose($fp);
                unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }
        return $writeable;
    }


}
