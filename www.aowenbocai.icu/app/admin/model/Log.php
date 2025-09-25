<?php
namespace app\admin\model;


use Jenssegers\Agent\Agent;

class Log extends BaseModel
{
    protected $updateTime = false;
    protected $resultSetType = 'collection';

    /**
     * 获取器 - username
     * @return json
     */
    public function getUsernameAttr($value,$data)
    {
       $user=User::get($data['userid']);
        return ['username'=>$user['username'], 'nickname' => $user['nickname']];
    }

    /**
     * 创建行为日志
     * @param  string       $remark    日志备注
     * @param  string       $type      日志类型  默认为1  登录日志  2 修改密码
     * @return array
     */
    public function  createLog($type="1",$remark){
        $userid = session('uid');
        if(request()->module() == 'admin' || request()->controller() == 'admin'){
            $userid = session('admin_uid');
        }
        $agent = (new Agent());
        $sb_name = '';
        if ($agent->isMobile()) {
            $sb_name = '手机端';
        }
        if ($agent->isTablet()) {
            $sb_name = '平板端';
        }
        if ($agent->isDesktop()) {
            $sb_name = '桌面端';
        }
        $browser = $agent->browser();
        $data   = [
            'create_ip'  => request()->ip(),
            'city' =>  getIPCity( request()->ip()),
            'terminal' => '[' . $sb_name . '] 系统：' . $agent->platform() . ' 浏览器：' . $browser,
            'url'        => request()->pathinfo(),
            'remark'     => $remark,
            'userid'     => $userid,
            'type'       => $type
        ];
        $this->save($data);//存入数据库
    }

    static public function write($remark)
    {
        $data   = [
            'create_ip'  => request()->ip(),
            'url'        => request()->pathinfo(),
            'remark'     => $remark,
            'userid'     => session('uid'),
            'type'       => 1
        ];
        (new self)->save($data);//存入数据库
    }

}
