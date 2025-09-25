<?php
namespace app\home\controller;

use app\common\controller\LotteryCommon;
use think\Controller;
use app\news\News;
use app\index\model\ExtShowList;
use \app\home\common\Common;

class Index extends Common
{
    public function index()
    {
        $article = ['news' => News::getNewList('','5','新闻资讯'), 'notice'=>News::getNewList('','5','网站公告')];
        $this->assign(['article' => $article]);

        $extShow = new ExtShowList;
        $rec = $extShow->where('reco', 1)->where('status',0)->limit(0,8)->order('sort ASC')->select();
        $rec = $rec->toArray();
        foreach ($rec as &$value) {
            $value['name'] = LotteryCommon::setUrl($value['name'])[0];
        }
        $this->assign('rec', $rec);

        $lotterryGame =  $extShow->field('id,name,title,img,image,remark,info')->where('type', 1)->order('sort ASC')->select();
        $smallGame =  $extShow->field('id,name,title,img,image,remark,info')->where('type', 0)->order('sort ASC')->select();
        $this->assign(['lotterryGame' => $lotterryGame,'smallGame' => $smallGame]);

        return $this->fetch();
    }

    public function about()
    {
        return $this->fetch('');
    }

    public function contact()
    {
        return $this->fetch('');
    }

    public function custody()
    {
        return $this->fetch('');
    }

    public function honor()
    {
        return $this->fetch('');
    }

    public function product()
    {
        $extShow = new ExtShowList;
        $rec = $extShow->where('reco', 1)->where('status',0)->limit(0,6)->order('sort ASC')->select();
        $rec = $rec->toArray();
        foreach ($rec as &$value) {
            $value['name'] = LotteryCommon::setUrl($value['name'])[0];
        }
        $this->assign('rec', $rec);

        $lotterryGame =  $extShow->field('id,name,title,img,image,remark,info')->where('type', 1)->order('sort ASC')->select();
        $smallGame =  $extShow->field('id,name,title,img,image,remark,info')->where('type', 0)->order('sort ASC')->select();
        $this->assign(['lotterryGame' => $lotterryGame,'smallGame' => $smallGame]);
        return $this->fetch('');
    }

     public function buy()
     {
         return $this->fetch('');
     }

      public function help()
      {
          return $this->fetch('');
      }
}




