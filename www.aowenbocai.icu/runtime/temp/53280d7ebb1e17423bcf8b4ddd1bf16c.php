<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:73:"/www/wwwroot/www.aowenbocai.icu/public/../app/admin/view/index/index.html";i:1758671433;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/head.html";i:1758671433;s:63:"/www/wwwroot/www.aowenbocai.icu/app/admin/view/common/foot.html";i:1758671433;}*/ ?>
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

<link rel="stylesheet" href="//at.alicdn.com/t/font_613146_hxet5uekdak.css">
<style>
    body {
        padding:0;
    }
    .link_item i {
        opacity: 0.4;
        padding-right: 5px;
    }
</style>
<audio id="audio">
    <source src="/static/admin/msg.ogg" type="audio/ogg">
    <source src="/static/admin/msg.mp3" type="audio/mpeg">
    您的浏览器不支持 audio 标签
</audio>
<nav class="top_nav navbar navbar-static-top flex-box">
    <div class="p_left">
        <button type="button" class="navbar-toggle show_nav" data-toggle="collapse" data-target="#left-nav"></button>
        <a href="<?php echo url('index'); ?>">后台管理系统</a>
    </div>
    <div class="menu flex">
        <div class="flex-box">
            <div class="flex">
                <?php foreach($menu_tree as $key => $item): ?>
                <div class="menu-item <?php if($key == 0): ?> active<?php endif; ?>" data-index="<?php echo $key; ?>"><?php echo $item['title']; ?></div>
                <?php endforeach; ?>
            </div>
            <div class="flex-box" style="align-items: center">
                <div class="tooltip-top icon-cj" data-toggle="tooltip" title="采集已开启" data-placement="bottom" style="padding-right: 5px">
                    <a class="cj"><i class="iconfont icon-caijirenwu" style="font-size: 21px"></i></a>
                </div>
                <div class="tooltip-top icon-order" data-toggle="tooltip" title="有?条待处理兑换订单" data-placement="bottom">
                    <a class="badge-change">
                        <i class="glyphicon glyphicon-gift"></i>
                        <sup class="badge-dot" style="display: none"></sup>
                    </a>
                </div>
                <div class="tooltip-top icon-recharge" data-toggle="tooltip" title="有?条待处理充值订单" data-placement="bottom">
                    <a class="badge-change">
                        <i class="glyphicon glyphicon-usd"></i>
                        <sup class="badge-dot" style="display: none"></sup>
                    </a>
                </div>
                <div class="tooltip-top" data-toggle="tooltip" title="网站首页" data-placement="bottom">
                    <a href="<?php echo url('/../index'); ?>" target="_blank"><i class="glyphicon glyphicon-home"></i></a>
                </div>
                <div class="flex-box" style='align-items: center;padding-right: 30px;height: 30px;line-height: 30px;padding-left: 10px'>
                    <div class="dropdown">
                        <span class="admin_info"  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <?php echo $admin['username']; ?>
                            <span class="caret"></span>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-top" aria-labelledby="dropdownMenu1" style="min-width: 90px;">
                            <li><a href="<?php echo url('admin/Login/logout'); ?>">退出登录</a></li>
                        </ul>
                    </div>
                    <span><i class="icon_photo"></i></span>
                </div>
            </div>
        </div>
    </div>
</nav>
<style>
    a.cj:hover{
        text-decoration: none;
    }
</style>
<div class="main">
    <div class="collapse navbar-collapse" id="left-nav">
        <?php foreach($menu_tree as $key => $item): ?>
        <div class="menu-nav-group <?php echo $key>0?'' : 'menu-nav-show'; ?>" data-index="<?php echo $key; ?>" >
            <?php foreach($item['nav'] as $nav): ?>
            <div data-url="<?php echo url($nav['url']); ?>" class="link_item"><i class="<?php echo $nav['icon']; ?>"></i> <?php echo $nav['title']; ?></div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="iframe_main">
        <iframe src="<?php echo url('main'); ?>" frameborder="0" id="iframe"></iframe>
    </div>
</div>

<script>
    $(function(){
        $("[data-toggle='tooltip']").tooltip();

        var isCj = <?php echo $isCj; ?>;
        var cjClass = isCj ? 'open' : '';
        var cjName = isCj ? '采集已开启' : '采集已关闭';
        $(".icon-cj a i").addClass(cjClass);
        $(".icon-cj").attr("title",cjName).tooltip('fixTitle');
        $(".icon-cj a").click(function(){
            var newCj = isCj ? 0 : 1;
            $.post('<?php echo url("SystemSet/set"); ?>', {
                data: JSON.stringify({
                    openCj : newCj,
                })
            }, function() {
                if(newCj){
                    isCj = 1
                    $(".icon-cj a i").addClass('open');
                    $(".icon-cj").attr("title",'采集已开启').tooltip('fixTitle');
                    $.modal.alert('采集已开启');
                }else {
                    isCj = 0
                    $(".icon-cj a i").removeClass('open');
                    $(".icon-cj").attr("title",'采集已关闭').tooltip('fixTitle');
                    $.modal.alert('采集已关闭');
                }
            });
        })

        //查看兑换订单
        $(".icon-order").click(function () {
            var newSrc = '<?php echo url("shop_order/index"); ?>';
            $('#iframe').attr('src',newSrc)
        })

        //查看充值订单
        $(".icon-recharge").click(function () {
            var newSrc = '<?php echo url("recharge/index"); ?>';
            $('#iframe').attr('src',newSrc)
        })

        var $navbarToggle = $('.navbar-toggle');
        $(".link_item").on('click',function(){
            var $this = $(this);
            if (!$this.hasClass('link_active')) {
                var $active = $('.link_item.link_active').removeClass('link_active');
                $this.addClass('link_active');
            }
            if ($navbarToggle.css('display') != 'none') $navbarToggle.click();


            var url = $(this).data("url");
            if(url){
                $("#iframe").attr({'src':url});
            }
        });

        $('.menu-item').click(function() {
            $('.menu-item.active').removeClass('active');
            $(this).addClass('active');
            var index = $(this).data('index');
            var $showNav = $('.menu-nav-group.menu-nav-show');
            if ($showNav.data('index') != index) {
                $showNav.removeClass('menu-nav-show');
                $('.menu-nav-group[data-index=' + index + ']').addClass('menu-nav-show');
            }
        });
        //初始化订单信息
        var newOrder = "<?php echo $shopNum; ?>";
        $(".icon-order").attr("title",'有'+ newOrder + '条待处理兑换订单').tooltip('fixTitle');
        $('.icon-order .badge-dot').css('display',newOrder > 0 ? 'block' : 'none');
        var newRechargeOrder = "<?php echo $rechargeNum; ?>";
        $(".icon-recharge").attr("title",'有'+ newRechargeOrder + '条待处理充值订单').tooltip('fixTitle');
        $('.icon-recharge .badge-dot').css('display',newRechargeOrder > 0 ? 'block' : 'none');
        if(newOrder>0 || newRechargeOrder > 0){
            if(newOrder > 0 ){
                $('.icon-order .badge-dot').css('display', newOrder > 0 ? 'block' : 'none');
                $(".icon-order").attr("title",'有'+ newOrder + '条待处理兑换订单').tooltip('fixTitle').tooltip('show');
            }
            if(newRechargeOrder > 0 ){
                $('.icon-recharge .badge-dot').css('display', newRechargeOrder > 0 ? 'block' : 'none');
                $(".icon-recharge").attr("title",'有'+ newRechargeOrder + '条待处理充值订单').tooltip('fixTitle').tooltip('show');
            }
            $('#audio')[0].play()
        }
        //获取新订单
        var interval = setInterval(function () {
            $.get('<?php echo url("index/getOrderChange"); ?>',function (res) {
                newOrder = res.shop_order.num
                $('.icon-order .badge-dot').css('display', newOrder > 0 ? 'block' : 'none');
                if (newOrder > 0) {
                    $(".icon-order").attr("title",'有'+ newOrder + '条待处理兑换订单').tooltip('fixTitle').tooltip('show');
                } else {
                    $(".icon-order").attr("title",'有0条待处理兑换订单').tooltip('fixTitle').tooltip('hide');
                }
                newRechargeOrder = res.recharge_order.num
                $('.icon-recharge .badge-dot').css('display', newRechargeOrder > 0 ? 'block' : 'none');
                if (newRechargeOrder > 0) {
                    $(".icon-recharge").attr("title",'有'+ newRechargeOrder + '条待处理充值订单').tooltip('fixTitle').tooltip('show');
                } else {
                    $(".icon-recharge").attr("title",'有0条待处理充值订单').tooltip('fixTitle').tooltip('hide');
                }
                if (newRechargeOrder > 0 || newOrder > 0)  $('#audio')[0].play();
            })
        },60000)
    });
</script>
</body>
</html>

