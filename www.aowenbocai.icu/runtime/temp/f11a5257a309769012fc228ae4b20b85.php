<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:90:"/www/wwwroot/www.aowenbocai.icu/public/../app/admin/view/statistics/today_agent_index.html";i:1758671433;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/head.html";i:1758671433;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/foot.html";i:1758671433;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/static/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="/static/admin/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/static/admin/base.css"/>
    <script src="/static/js/jquery-3.2.1.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.min.js"></script>
    <script src="/static/js/bootstrap-alert.js"></script>
    <script type='text/javascript' src="//cdn.bootcss.com/vue/2.3.4/vue.js"></script>
    <title><?php echo $title; ?></title>
</head>
<body>

<link href="/static/bootstarp-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
<script src="/static/bootstarp-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="/static/bootstarp-datetimepicker/js/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="/static/js/reszieimg.js"></script>
<style>
    .box-padding-bottom{margin-left: 1rem;margin-top:10px}
    .b_bloder td {font-weight: 700}
</style>
<nav class="plugin_nav">
    <a href="<?php echo url('agent_index'); ?>?is_today=0" >代理数据统计</a>
    <a class="nav_active">今日数据统计</a>
</nav>

<div class="box-padding">
    <div class="breadcrumb">
        <ol class="">
            <li class="active"><i class="glyphicon glyphicon-link"></i> 代理数据统计</li>
            <?php if($userid): ?><li><a href="javascript:history.back(-1);" class="btn btn-default form-group-left btn-sm">返回</a></li><?php endif; ?>
        </ol>
        <form class="form-inline" action="">
            <div class="form-group form-group-left">
                <label>用户名:</label>
                <input type="text" name="words" class="form-control" value="<?php echo $query['words']; ?>" placeholder="用户名">
            </div>
            <input type="hidden" name="userid" class="form-control" value="<?php echo $userid; ?>" placeholder="">
            <input type="hidden" name="is_today" class="form-control" value="<?php echo $is_today; ?>" placeholder="">
            <button type="submit" class="btn btn-default form-group-left btn-sm">搜索</button>
        </form>
        <div class="clear"></div>
    </div>
    <div class="content tasklist table_box">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th width="200">用户昵称（用户名）</span></th>
                <th width="100">会员类型</th>
                <th>团队余额</th>
                <th>团队投注</th>
                <th>团队中奖</th>
                <th>游戏币转出</th>
                <th>游戏币转入</th>
                <th>团队充值</th>
                <th>团队兑换</th>
                <th>团队活动</th>
                <th>团队提成</th>
                <th>团队返点</th>
                <th>团队盈利</th>
            </tr>
            </thead>
            <tbody>
                <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                <tr>
                    <td><?php if($userid == $item['id']): ?> <?php echo $item['nickname']; ?>（<?php echo $item['username']; ?>）<?php else: ?><a title="查看下级统计" href="<?php echo url('agent_index'); ?>?userid=<?php echo $item['id']; ?>"><?php echo $item['nickname']; ?>（<?php echo $item['username']; ?>）</a><?php endif; ?></td>
                    <td><?php echo $item['type_name']; ?></td>
                    <td><?php echo isset($item['statistics']['money'])?$item['statistics']['money']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['spend'])?$item['statistics']['spend']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['award'])?$item['statistics']['award']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['game_out'])?$item['statistics']['game_out']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['game_in'])?$item['statistics']['game_in']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['recharge'])?$item['statistics']['recharge']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['change'])?$item['statistics']['change']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['send'])?$item['statistics']['send']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['royalty'])?$item['statistics']['royalty']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['rebate'])?$item['statistics']['rebate']: 0; ?></td>
                    <td><?php echo isset($item['statistics']['gain'])?$item['statistics']['gain']: 0; ?></td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                <tr class="b_bloder">
                  <td colspan="2">当前页总计</td>
                  <td><?php echo isset($sum_page['money'])?$sum_page['money']: 0; ?></td>
                  <td><?php echo isset($sum_page['spend'])?$sum_page['spend']: 0; ?></td>
                  <td><?php echo isset($sum_page['award'])?$sum_page['award']: 0; ?></td>
                    <td><?php echo isset($sum_page['game_out'])?$sum_page['game_out']: 0; ?></td>
                    <td><?php echo isset($sum_page['game_in'])?$sum_page['game_in']: 0; ?></td>
                  <td><?php echo isset($sum_page['recharge'])?$sum_page['recharge']: 0; ?></td>
                  <td><?php echo isset($sum_page['change'])?$sum_page['change']: 0; ?></td>
                  <td><?php echo isset($sum_page['send'])?$sum_page['send']: 0; ?></td>
                  <td><?php echo isset($sum_page['royalty'])?$sum_page['royalty']: 0; ?></td>
                  <td><?php echo isset($sum_page['rebate'])?$sum_page['rebate']: 0; ?></td>
                  <td><?php echo isset($sum_page['gain'])?$sum_page['gain']: 0; ?></td>
               </tr>

            </tbody>
        </table>
    </div>
    <div class="text-right"><?php echo $page_html; ?></div>

</div>

<script>
  $(function () {

    $('#begintime,#endtime').datetimepicker({
      language: 'zh-CN',
      weekStart: 1,
      todayBtn: 1,
      autoclose: 1,
      todayHighlight: 1,
      startView: 2,
			endDate : new Date(),
      minView: 2,
      forceParse: 0
    });
    $('#begintime').on('changeDate',function(e){  
        var startTime = e.date;  
        $('#endtime').datetimepicker('setStartDate',startTime);  
    });
    $('#endtime').on('changeDate',function(e){  
        var startTime = e.date;  
        $('#begintime').datetimepicker('setStartDate',startTime);  
    });

    $(".choice").click(function(){
        var _this = $(this).find('a');
        $(".result").text(_this.text());
        $("input[name=type]").val(_this.data('value'))
    })
    
    $(".choicesort").click(function(){
        var _this = $(this).find('a');
        $(".resultsort").text(_this.text());
        $("input[name=sort]").val(_this.data('value'))
    })
  })
</script>
</body>
</html>
