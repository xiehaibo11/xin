<?php
namespace app\hook\controller;

use app\admin\model\User;
use think\Controller;
use app\common\Hook;
use think\Hook AS thinkHook;

class Main extends Controller
{
    public $path = APP_PATH . 'hook';
    public $hook = [];

    public function __construct()
    {
        // $module = request()->module();
        // $controller = request()->controller();
        // $action = request()->action();
        // $this->path = [$module, $controller, $action];
        // echo implode('_', $this->path);
    }

    public function index()
    {
        Hook::call('Category' , 'index');
    }

    public function on($name = 'index_user_index.login', $func = '')
    {
        if ($func) {
            $this->hook[$name][] = $func;
        } else {
            foreach ($this->hook[$name] as $key => $value) {
                $value();
            }
        }

        // $path_action = explode('.', $name);
        // $path = $path_action[0];
        // $action = $path_action[1];
        // $hook_path = APP_PATH . 'hook/plugin/' . $path . '/' . $action;
        // //print_r($hook_path);
        // if ($func) {
        //     if (!is_dir($hook_path)) {
        //         mkdir($hook_path,0777,true);
        //     }
        //     $dir = scandir($hook_path);
        //     $hook_path_file = $hook_path . '/' . (count($dir) - 2) . '.php';
        //     $fp=fopen($hook_path_file, "w+"); //打开文件指针，创建文件
        //     if (!is_writable($hook_path_file)){
        //         die("文件:" .$hook_path_file. "不可写，请检查！");
        //     }
        //     fwrite($fp, var_export($func, true));
        //     fclose($fp);  //关闭指针
        // }
        // $fp=fopen($filename, "w+"); //打开文件指针，创建文件
        // if ( !is_writable($filename) ){
        //       die("文件:" .$filename. "不可写，请检查！");
        // }
        // fwrite($fp, $encode);
        // fclose($fp);  //关闭指针
        //
        // if (!is_dir($hook_path)) {
        //     print_r('wu');
        //     mkdir($hook_path,0777,true);
        // } else {
        //     print_r('you');
        // }
    }
}
