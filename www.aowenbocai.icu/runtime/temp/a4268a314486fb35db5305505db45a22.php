<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:72:"/www/wwwroot/www.aowenbocai.icu/public/../app/admin/view/user/index.html";i:1758723535;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/head.html";i:1758671433;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/foot.html";i:1758671433;}*/ ?>
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


<style media="screen">
    .add-botton {
        float: right;
        margin-right: 30px;
    }

    .box-padding-bottom {
        padding-bottom: 15px;
    }

    .box-padding {
        padding: 20px;
    }

    .form-group-left {
        margin-left: 15px;
    }
</style>
<div class="box-padding">
    <div class="breadcrumb">
        <ol class="">
            <li class="active"><i class="glyphicon glyphicon-link"></i> 会员管理</li>
        </ol>
        <form class="form-inline" action="">
            <div class="form-group form-group-left">
                <label>时间</label>
                <div class='input-group date' id='begintime' data-date-format="yyyy-mm-dd">
                    <input type='text' class="form-control" name="starttime" value="<?php echo $query['starttime']; ?>" />
                    <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                <label>到</label>
                <div class='input-group date' id='endtime' data-date-format="yyyy-mm-dd">
                    <input type='text' class="form-control" name="endtime" value="<?php echo $query['endtime']; ?>" />
                    <span class="input-group-addon">
          <span class="glyphicon glyphicon-calendar"></span>
        </span>
                </div>
            </div>
            <div class="form-group form-group-left">
                <label>关键字:</label>
                <input type="text" name="words" class="form-control" value="<?php echo $query['words']; ?>" placeholder="关键字/用户名/操作/IP/地址">
            </div>
            <div class="dropdown form-group">
                <label>用户类型:</label>
                <button type="button" class="btn dropdown-toggle" id="dropdownMenu1"
                        data-toggle="dropdown">
              <span class="result">
                  <?php if($query['type'] == ''): ?>
                  全部会员
                  <?php elseif($query['type'] == 0): ?>
                  测试会员
                  <?php elseif($query['type'] == 1): ?>
                  普通会员
                  <?php elseif($query['type'] == 2): ?>
                  代理会员
                   <?php elseif($query['type'] == 6): ?>
                  发单会员
                   <?php elseif($query['type'] == 7): ?>
                  跟单会员
                  <?php endif; ?>
              </span>
                    <span class="caret"></span>
                </button>
                <input type="hidden" name="type" value="<?php echo $query['type']; ?>">
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="text-align:right">
                    <li role="presentation" class="choice">
                        <a role="menuitem" data-value =''>全部会员</a>
                    </li>
                    <li role="presentation" class="choice">
                        <a role="menuitem"  data-value = 0>测试会员</a>
                    </li>
                    <li role="presentation" class="choice">
                        <a role="menuitem" data-value = 1>普通会员</a>
                    </li>
                    <li role="presentation" class="choice">
                        <a role="menuitem" data-value = 2>代理会员</a>
                    </li>
                </ul>
            </div>
            <input type="hidden" id="sort_field" name="sort_field" value="<?php echo $query['sort_field']; ?>">
            <input type="hidden" id="sort_asc" name="sort_asc" value="<?php echo $query['sort_asc']; ?>">
            <button type="submit" class="btn btn-default form-group-left btn-sm">搜索</button>
            <div class="add-botton">
                <a href="<?php echo url('admin/User/add'); ?>" class="btn btn-primary btn-sm" role="button"><img height="16" src="/static/admin/svg/user_online.svg" alt=""> 添加会员</a>
            </div>
        </form>
    </div>
  <div class="table_box">
      <table class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
          <th width="110" class="sort_btn tc" data-field="id">用户ID</th>
          <th width="60" class="tc">头像</th>
          <th width="150" class="tc">昵称</th>
          <th width="100" class="tc">用户名</th>
          <th width="100" class="tc">QQ</th>
          <th width="100" class="tc">上级</th>
          <th width="120" class="sort_btn tc" data-field="money">余额</th>
          <th width="120" class="sort_btn tc" data-field="game_money">游戏币余额</th>
          <th width="180"  class="sort_btn tc" data-field="action_time">最后活动时间</th>
          <th width="80" class="tc">类型</th>
          <th width="80" class="tc">状态</th>
          <th class="tc" width="320">操作</th>
        </tr>
      </thead>
    <?php foreach($list as $item): ?>
    <tr>
      <td class="tc"><?php echo $item['id']; ?></td>
      <td class="tc"><img src="<?php echo $item['photo']; ?>" alt="" width="30" height="30" class="img-circle"> </td>
      <td class="tc"><a title="点击查看资金明细" href="<?php echo url('moneyhistory'); ?>?words=<?php echo $item['nickname']; ?>"><?php echo $item['nickname']; ?></a></td>
      <td class="tc"><?php echo $item['username']; ?></td>
      <td class="tc"><?php echo $item['qq']; ?></td>
      <td class="tc"><?php echo $item['agents_name']; ?></td>
      <td class="tc"><span class="user_money"><?php echo $item['money']; ?></span></td>
      <td class="tc"><span class="user_money"><?php echo $item['game_money']; ?></span></td>
      <td class="tc"><?php echo $item['action_time']; ?></td>
      <td class="tc"><?php echo $item['typename']; ?></td>
      <td class="tc"><?php echo $item['status']; ?></td>
      <td class="tc">
        <div class="user_action">
          <div>
            <a href="<?php echo url('recharge', ['userid' => $item['id']]); ?>" class="btn btn-primary btn-xs"><img height="16" src="/static/admin/svg/send_msg.svg" alt=""> 充值</a>
            <a href="<?php echo url('admin/User/edit'); ?>?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs"><img height="16" src="/static/admin/svg/editor.svg" alt=""> 修改</a>
            <a href="<?php echo url('goto'); ?>?userid=<?php echo $item['id']; ?>" target="_blank" class="btn btn-warning btn-xs"><img height="16" src="/static/admin/svg/look2.svg" alt=""> 登录</a>
            <!--<a href="<?php echo url('send_message', ['userid' => $item['id']]); ?>" class="btn btn-success btn-xs"><img height="16" src="/static/admin/svg/send_msg.svg" alt=""> 消息</a>-->
            <a href="#" class="btn btn-primary btn-xs isdelete" data-url="<?php echo url('delete', ['id' => $item['id']]); ?>"><i class="icon-trash"></i> 删除</a>
          </div>
        </div>
      </td>
    </tr>
    
    <?php endforeach; ?>
  </table>
  </div>

  <!-- <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>注册时间</th>
        <th>注册IP</th>
        <th>余额</th>
        <th>状态</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($list as $item): ?>
      <tr>
        <td><?php echo $item['id']; ?></td>
        <td><?php echo $item['username']; ?></td>
        <td><?php echo $item['create_time']; ?></td>
        <td><?php echo $item['reg_ip']; ?></td>
        <td><?php echo $item['money']; ?></td>
        <td><?php echo $item['status']; ?></td>
        <td>
          <div class="btn-group" role="group" aria-label="...">
            <a href="<?php echo url('admin/User/edit'); ?>?id=<?php echo $item['id']; ?>" class="btn btn-primary btn-xs"><img height="16" src="/static/admin/svg/editor.svg" alt=""> 修改</a>
            <a href="#" class="btn btn-primary btn-xs isdelete" data-url="<?php echo url('admin/User/delete'); ?>?id=<?php echo $item['id']; ?>"><img height="16" src="/static/admin/svg/delete.svg" alt=""> 删除</a>
            <a href="<?php echo url('admin/User/send_message'); ?>?id=<?php echo $item['id']; ?>" class="btn btn-primary btn-xs"><img height="16" src="/static/admin/svg/send_msg.svg" alt=""> 消息</a>
          </div>
        </td>

      </tr>
      <?php endforeach; ?>
    </tbody>
  </table> -->
  <div class="text-right"><?php echo $list->render(); ?></div>
</div>
</div>
<script type='text/javascript' src="/static/admin/list_base.js"></script>
<script>
  $(function () {
    $('.isdelete').click(function () {
      var _url = $(this).data("url");
      $.modal.confirm('<font style="color:red;">删除会员，将会删除包括资金明细在内的全部会员数据</font>，是否确认删除？', function () {
        location.href = _url;
      });
    });
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
  });

</script>
</body>
</html>
