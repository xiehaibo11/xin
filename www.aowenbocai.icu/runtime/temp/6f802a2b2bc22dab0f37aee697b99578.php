<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:71:"/www/wwwroot/www.aowenbocai.icu/public/../app/web/view/login/index.html";i:1758671433;s:67:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/login_head.html";i:1758671433;s:61:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/link.html";i:1758671433;s:68:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/simple_foot.html";i:1758671433;s:60:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/end.html";i:1758671433;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php if(isset($title)): ?><?php echo $title; else: ?>用户登录<?php endif; ?> <?php echo isset($site_name) ? '-' . $site_name : ''; ?></title>
    <meta http-equiv="X-UA-Compatible" content.top="ie=edge">
<meta name="renderer" content.top="webkit">
<script src="/static/js/jquery-3.2.1.min.js"></script>
<!--<script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/vue/2.6.9/vue.js"></script>-->
<script src="/static/vipweb/js/vue-2.6.9.js"></script>
<!-- 引入element-ui样式 -->
<!--<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">-->
<link rel="stylesheet" type="text/css"  href="/static/vipweb/theme/element/index.css">
<!-- 引入element-ui组件库 -->
<!--<script src="https://unpkg.com/element-ui@2.3.8/lib/index.js"></script>-->
<script src="/static/vipweb/js/element-ui-2.3.8.min.js"></script>

<link rel="stylesheet" type="text/css"  href="//at.alicdn.com/t/font_613146_sk4gdb38bq.css">
<!--<link rel="stylesheet" type="text/css"  href="/static/vipweb/fonts/iconfont.css">-->
<link rel="stylesheet" type="text/css"  href="/static/vipweb/css/reset.css">
<link rel="stylesheet" type="text/css"  href="/static/vipweb/css/style.css">
<script src="/static/vipweb/js/init.js"></script>
<script src="/static/vipweb/js/lottery.js"></script>
<!--[if lt IE 10]>
<div style="position:absolute;left:0;top:0;width:100%;height:50px;background:rgb(255,255,233);color:rgb(30,84,148);border-bottom:1px solid rgb(230,230,198);text-align:center;line-height:50px;fonts-size:12px;z-index:99999;">您使用的浏览器版本过低，可能会影响到您浏览本网页，建议您升级至ie10及以上版本或更换浏览器!</div>
<![endif]-->

</head>
<body>

<div id="top">
    <div class="header flex-box web">
        <div class="logo"><a href="<?php echo url('./'); ?>"><img src="<?php echo $company['logo_url']; ?>" alt=""></a></div>
        <div style="font-size: 25px"><?php echo $title; ?></div>
        <div class="info tr flex">
            <?php if(!empty($company['company_img_qq']) || !empty($company['company_qq'])): ?>
            <div class="reference">
                <el-popover
                        placement="bottom"
                        width="200"
                        trigger="hover">
                    <div v-cloak  class="tc">
                        <p><?php if(isset($company['company_img_qq'])): ?><img src="<?php echo $company['company_img_qq']; ?>" alt="" width="160"><?php endif; ?></p>
                        <p><?php if(isset($company['company_qq'])): ?><?php echo $company['company_qq']; endif; ?></p>
                    </div>
                    <span slot="reference">
                         <a href="tencent://message/?uin=<?php echo $qq_num; ?>&amp;Site=<?php echo $company['site_name']; ?>&amp;Menu=yes">
                            <i class="iconfont icon-qq-copy"></i>
                         </a>
                    </span>
                </el-popover>
            </div>
            <?php endif; if(!empty($company['company_img']) || !empty($company['company_wx'])): ?>
            <div class="reference tc">
                <el-popover
                        placement="bottom"
                        width="200"
                        trigger="hover">
                    <div v-cloak class="tc">
                        <p><?php if(isset($company['company_img'])): ?><img src="<?php echo $company['company_img']; ?>" alt="" width="160"><?php endif; ?></p>
                        <p><?php if(isset($company['company_wx'])): ?><?php echo $company['company_wx']; endif; ?></p>
                    </div>
                    <span slot="reference"><i class="iconfont icon-big-WeChat"></i></span>
                </el-popover>
            </div>
            <?php endif; if(!(empty($company['company_tel']) || (($company['company_tel'] instanceof \think\Collection || $company['company_tel'] instanceof \think\Paginator ) && $company['company_tel']->isEmpty()))): ?>
            <div class="reference">
                <el-popover
                        placement="bottom"
                        width="200"
                        trigger="hover">
                    <div v-cloak class="tc">
                        <?php echo $company['company_tel']; ?>
                    </div>
                    <span slot="reference"><i class="iconfont icon-phone"></i></span>
                </el-popover>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    $(function() {
        var vm = new Vue({
            el: '#top'
        });
    })
</script>

<link rel="stylesheet" href="/static/vipweb/css/login.css">
<script language=javascript src='/static/vipweb/vue-component/login-tel.js'></script>
<script language=javascript src='/static/vipweb/vue-component/login-name.js'></script>
<style>
    .jz{
        margin: 0 auto;
    }
</style>
<div id="app" class="page-login" style="min-height: 500px" :style="{'height':wHeight + 'px','paddingTop':pdTop + 'px'}">
    <div style="max-width: 1200px;margin: 0 auto;" class="cf">
        <div class="login-box" style="padding-right: 0" :class="wWidth > 1024 ? 'fr' : 'jz'">
            <div class="login-way bg flex-box" v-cloak>
                <h4 class="flex tc" v-if="isCommon"  @click="show=1" :class="{'red':show==1}">
                    用户名登录
                </h4>
                <h4 class="flex tc" v-if="isTel" @click="show=2" :class="{'red':show==2}">
                    手机验证码登录
                </h4>
            </div>
            <div class="login-form" v-cloak>
                <web-login v-if="isCommon && show == 1" @success="loginSuc"></web-login>
                <tel-login v-if="isTel && show == 2" @success="loginSuc"></tel-login>
                <!--其他方式登录-->
                <div class="other flex-box">
                    <i style="color: #50b674" class="iconfont icon-weixin" v-if="isWei"></i>
                    <a href="<?php echo url('weilogin'); ?>"  target="_blank" v-if="isWei">微信登录</a>
                    <i style="color: #1296db" class="iconfont icon-qq" v-if="isQq"></i>
                    <a href="/web/Login/qq" target="_blank" v-if="isQq">QQ登录</a>
                </div>
                <div class="tr link">
                    <a class="link" href="<?php echo url('./findpwd'); ?>" style="padding-right: 8px">忘记密码</a>
                    <a href="<?php echo url('reg/index'); ?>" v-if="is_reg" class="link">注册账号</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="foot mt15 bg" style="padding-bottom: 0"  id="foot">
    <div class="web">
        <ul class="note flex-box">
            <li>
                <i class="iconfont icon-zhanghuanquan"></i>
                <span>账户安全</span>
            </li>
            <li>
                <i class="iconfont icon-fuwu"></i>
                <span>金牌服务</span>
            </li>
            <li>
                <i class="iconfont icon-youxi1"></i>
                <span>游戏丰富</span>
            </li>
            <li>
                <i class="iconfont icon-xinyu"></i>
                <span>品牌信誉</span>
            </li>
        </ul>
        <div class="tips tc" style="padding-bottom: 0;margin-bottom: 0;">
            <p class="tc">
                <em class="red">适龄提示：本站游戏适合18岁以上玩家</em><em style="color: green;padding-left: 5px">未满18岁的玩家需在家长监督下进行游戏</em>
                <a href="<?php echo url('index/custody'); ?>" class="link" target="_blank" style="padding-right: 25px;">家长监护工程</a>
            </p>
            <p>
                健康游戏公告：拒绝盗版游戏 抵制不良游戏 注意自我保护 谨防受骗上当 适度游戏益脑 沉迷游戏伤身 合理安排时间 享受健康生活
            </p>
            <div><?php echo $company['web_bottom']; ?></div>
        </div>
    </div>
</div>
<?php if(isset($company['web_online_qq'])): ?>
<?php echo $company['web_online_qq']; endif; ?>
</body>
</html>
<script type="text/javascript">
    $(function () {
        new Vue({
            el: '#foot',
            data: {
                regVisible:false,
                ysVisible:false,
                gameVisible:false
            }
        });
    });
</script>
<style type="text/css">
    div{
        background-repeat: no-repeat;
    }
</style>
<script type="text/javascript">
    'use strict';

    $(function () {
        //背景图随机
        var bg = new Array();
        bg = [
            { img: '/static/vipweb/images/login/login_bg_sr.png', color: '#dfd5d4', position: '22% center' },
            { img: '/static/vipweb/images/login/login_bg_dj.png', color: '#a0e5d8', position: '22% center'},
            { img: '/static/vipweb/images/login/login_bg_cs.png', color: '#ff6455', position: '22% 0'},
            { img: '/static/vipweb/images/login/login_bg_jcll.png', color: '#00245d', position: '22% center'}
            ];
        var bgIdx = Math.floor(Math.random() * bg.length);
        $('.page-login').css({ 'background-image': 'url(' + bg[bgIdx].img + ')', 'background-repeat': 'no-repeat', 'background-color': bg[bgIdx].color, 'background-position': bg[bgIdx].position });

        var loginWay = '<?php echo $company['login_way']; ?>'.split(',');
        var wHeight = $(window).height()
        var wWidth = $(window).width()
        var tHeight = $('#top').height()
        var fHeight = $('#foot').height()//减去的高度
        var totalHeight = tHeight + fHeight + 35
        new Vue({
            el: '#app',
            data: {
                is_reg: parseInt('<?php echo $company['is_reg']; ?>'), //是否开启注册
                isTel: parseInt('<?php echo $data['tel']; ?>'),
                isCommon: parseInt('<?php echo $data['common']; ?>'),
                isWei: parseInt('<?php echo $data['wxsao']; ?>') ? parseInt('<?php echo $data['wx']; ?>') : 0,
                isQq: parseInt('<?php echo $data['qq']; ?>'),
                show: 1 ,//显示内容

                wHeight:accSub(wHeight,totalHeight,3),//内容高度
                wWidth: wWidth //内容宽度
            },
            computed:{
                pdTop:function () {
                    return accDiv(this.wHeight - 388,2,3)
                }
            },
            methods: {
                loginSuc: function (v) {
                    var _this = this;
                    this.$message({
                        message: '登录成功',
                        type: 'success'
                    });
                    setTimeout(function () {
                        window.location.href = v[2];
                        _this.loading = false;
                    }, 1000);
                }
            },
            created: function () {
                if (!this.isCommon && this.isTel) {
                    this.show = 2;
                }
            }
        });
    });
</script>
</body>
</html>