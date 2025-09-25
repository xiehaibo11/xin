<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:76:"/www/wwwroot/www.aowenbocai.icu/public/../app/admin/view/recharge/index.html";i:1758671433;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/head.html";i:1758671433;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/foot.html";i:1758671433;}*/ ?>
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
    .redSuccess{color:#409478}
    .greenError{color:#f17b7b}
</style>
<div id="app" class="box-padding">
    <div class="form-group form-group-left" style="margin:-10px 0 0 -10px;display:inline-block">
        <div class="btn-group" data-toggle="buttons" style="margin:1rem">
        <label class="btn btn-primary isdelete" data-url="<?php echo url('deleteId', ['day' =>'1']); ?>">
            <input type="checkbox"> 数据全部清除
        </label>
        <label class="btn btn-primary isdelete" data-url="<?php echo url('deleteId', ['day' =>'30']); ?>">
            <input type="checkbox"> 清除30天前数据
        </label>
        <label class="btn btn-primary isdelete" data-url="<?php echo url('deleteId', ['day' =>'15']); ?>">
            <input type="checkbox"> 清除15天前数据
        </label>
        <label class="btn btn-primary isdelete" data-url="<?php echo url('deleteId', ['day' =>'7']); ?>">
            <input type="checkbox"> 清除7天前数据
        </label>
        <label class="btn btn-primary isdelete" data-url="<?php echo url('deleteId', ['day' =>'3']); ?>">
            <input type="checkbox"> 清除3天前数据
        </label>
        </div>
    </div>
    <div class="breadcrumb">
        <ol class="">
            <li class="active"><i class="glyphicon glyphicon-link"></i> 充值记录</li>
        </ol>
        <form class="form-inline" action="" style="display:inline-block">
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
                <input type="text" name="words" class="form-control" value="<?php echo $query['words']; ?>" placeholder="关键字/用户名">
            </div>
            <div class="dropdown form-group">
                <label>充值类型:</label>
                <button type="button" class="btn dropdown-toggle" id="dropdownMenu1"
                        data-toggle="dropdown">
              <span class="result">
                  <?php if($query['retype'] == ''): ?>
                  所有类型
                  <?php elseif($query['retype'] == 1): ?>
                  支付宝充值
                  <?php elseif($query['retype'] == 2): ?>
                  微信扫码充值
                  <?php elseif($query['retype'] == 3): ?>
                  支付宝扫码充值
                  <?php elseif($query['retype'] == 4): ?>
                  其他三方充值
                  <?php elseif($query['retype'] == 5): ?>
                  银行卡转账充值
                  <?php else: ?>
                  微信充值
                  <?php endif; ?>
              </span>
                    <span class="caret"></span>
                </button>
                <input type="hidden" name="retype" value="<?php echo $query['retype']; ?>">
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="text-align:right">
                    <li role="presentation" class="choice">
                        <a role="menuitem" data-value =''>所有类型</a>
                    </li>
                    <li role="presentation" class="choice">
                        <a role="menuitem"  data-value = 0>微信充值</a>
                    </li>
                    <li role="presentation" class="choice">
                        <a role="menuitem" data-value = 1>支付宝充值</a>
                    </li>
                    <li role="presentation" class="choice">
                        <a role="menuitem" data-value = 2>微信扫码充值</a>
                    </li>
                    <li role="presentation" class="choice">
                        <a role="menuitem" data-value = 3>支付宝扫码充值</a>
                    </li>
                    <li role="presentation" class="choice">
                        <a role="menuitem" data-value = 4>其他三方充值</a>
                    </li>
                    <li role="presentation" class="choice">
                        <a role="menuitem" data-value = 5>银行卡转账充值</a>
                    </li>
                </ul>
            </div>

            <div class="dropdown form-group">
                <label>充值结果:</label>
                <button type="button" class="btn dropdown-toggle" id="dropdownMenu1"
                        data-toggle="dropdown">
              <span class="reresult">
                  <?php if($query['reresult'] == ''): ?>
                  所有充值
                  <?php elseif($query['reresult'] == 1): ?>
                  充值成功
                  <?php else: ?>
                  充值失败
                  <?php endif; ?>
              </span>
                    <span class="caret"></span>
                </button>
                <input type="hidden" name="reresult" value="<?php echo $query['reresult']; ?>">
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" style="text-align:right">
                    <li role="presentation" class="rechoice">
                        <a role="menuitem" data-value =''>所有充值</a>
                    </li>
                    <li role="presentation" class="rechoice">
                        <a role="menuitem"  data-value = 1>充值成功</a>
                    </li>
                    <li role="presentation" class="rechoice">
                        <a role="menuitem" data-value = 0>充值失败</a>
                    </li>
                </ul>
            </div>
            <input type="hidden" id="sort_field" name="sort_field" value="<?php echo $query['sort_field']; ?>">
            <input type="hidden" id="sort_asc" name="sort_asc" value="<?php echo $query['sort_asc']; ?>">
            <button type="submit" class="btn btn-default form-group-left btn-sm">搜索</button>
        </form>
        <div class="clear"></div>
    </div>

    <div class="table_box">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>用户昵称（用户名）</th>
                <th class="sort_btn" data-field="money">充值金额</th>
                <th>充值方式</th>
                <th width="180" class="sort_btn" data-field="create_time">充值时间</th>
                <th>备注</th>
                <th width="120">完成状态</th>
                <th class="tc" width="200">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($list as $item): ?>
            <tr>
                <td><?php echo $item['userinfo']['nickname']; ?> （<a><?php echo $item['userinfo']['username']; ?></a>）</td>
                <td><?php echo $item['money']; ?></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['create_time']; ?></td>
                <td><?php if(!$item['statuss'] and ($item['name']=='微信扫码充值' || $item['name']=='支付宝扫码充值' || $item['name']=='银行卡转账充值')): ?> <?php echo $item['info']; endif; ?></td>
                <td><?php echo $item['userinfo']['status']; ?></td>
                <td class="tc">
                    <?php if(!$item['statuss'] and ($item['name']=='微信扫码充值' || $item['name']=='支付宝扫码充值' || $item['name']=='银行卡转账充值')): ?>
                    <a href="#" class="btn btn-danger btn-sm isDo" data-url="<?php echo url('rechargeDo', ['id' => $item['id']]); ?>">确认充值</a>
                    <a href="<?php echo url('refuse', ['id' => $item['id']]); ?>" class="btn btn-primary btn-sm">拒绝充值</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td> <b>统计</b> </td>
                <td colspan="3"><b>充值成功:<?php echo $statis['1']; ?></b></td>
                <td colspan="3"><b>未完成充值:<?php echo $statis['0']; ?></b></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="text-right"><?php echo $page; ?></div>
</div>
</div>
<script type='text/javascript' src="/static/admin/list_base.js"></script>
<script>
    $(function () {
        $('.isDo').click(function () {
            var _url = $(this).data("url");
            $.modal.confirm('你是否确认为该用户充值', function () {
                location.href = _url;
            });
        });
        $('.isdelete').click(function () {
            var _url = $(this).data("url");
            $.modal.confirm('你是否确定删除', function () {
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
            $("input[name=retype]").val(_this.data('value'))
        })
        $(".rechoice").click(function(){
            var _this = $(this).find('a');
            $(".reresult").text(_this.text());
            $("input[name=reresult]").val(_this.data('value'))
        })
    });

</script>
</body>
</html>
