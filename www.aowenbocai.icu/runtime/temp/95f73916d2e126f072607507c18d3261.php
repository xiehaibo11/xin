<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:69:"/www/wwwroot/www.aowenbocai.icu/public/../app/web/view/pay/index.html";i:1758671433;s:61:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/head.html";i:1758681975;s:61:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/link.html";i:1758671433;s:61:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/foot.html";i:1758671433;s:60:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/end.html";i:1758671433;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php if(isset($title)): ?><?php echo $title; else: ?>充值<?php endif; ?><?php echo isset($site_name) ? '-' . $site_name : ''; ?></title>
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
<script src="/static/vipweb/vue-component/module-login.js"></script>

<div id="top">
    <div class="top bg web_">
        <div class="web flex-box">
            <div class="flex">您好，欢迎来到<?php echo $site_name; ?>！</div>
            <div>
                <!--logindialog start-->
                <login-info site-name="<?php echo $site_name; ?>" @login-success="loginSuccess" @login-out="loginOut" @refresh="refreshMoney" msg-num="<?php echo $msgNum; ?>" :nickname="nickname" :login-visible="loginVisible"
                            :money="accountMoney"
                            reg-url="<?php echo url('reg/index'); ?>" my-url="<?php echo url('user/my'); ?>" @open-dialog="loginVisible = true" @close-dialog="loginVisible = false">
                </login-info>
                <!--logindialog end-->
            </div>
        </div>
    </div>
    <div class="header flex-box web">
        <div class="logo"><a href="<?php echo url('./'); ?>"><img src="<?php echo $company['logo_url']; ?>" alt=""></a></div>
        <?php if(isset($topImg)): if(is_array($topImg) || $topImg instanceof \think\Collection || $topImg instanceof \think\Paginator): $i = 0; $__LIST__ = $topImg;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <div><a href="<?php echo $v['url']; ?>" target="_blank"><img src="<?php echo $v['img_url']; ?>" alt=""></a></div>
            <?php endforeach; endif; else: echo "" ;endif; endif; ?>
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
                         <a href="tencent://message/?uin=<?php echo $qq_num; ?>&amp;Site=<?php echo $site_name; ?>&amp;Menu=yes">
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
    <div class="nav">
        <div class="web flex-box">
            <!--@mouseenter="show = true" @mouseleave="show=false" @touch="show = !show"-->
            <div class="nav-game" @mouseenter="show = true" @mouseleave="show=false" @touch="show = !show">
                <div class="flex-box">
                    <div class="flex">全部游戏</div>
                    <i class="el-icon-arrow-down" :class="show ? 'is-active' : 'no-active'"></i>
                </div>
                <div class="nav-game-list" v-show="show" style="display: none">
                    <div class="hot-game-list cf">
                        <div class="fl hot-title"><em>热门推荐</em></div>
                        <div class="fl" style="margin-right: 25px;line-height: 1">
                            <img src="/static/vipweb/images/hot_new.gif" alt="">
                        </div>
                        <ul class="links fl">
                            <?php if(is_array($recs) || $recs instanceof \think\Collection || $recs instanceof \think\Paginator): $i = 0; $__LIST__ = $recs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                            <li class="dib">
                                <a @click="link('<?php echo $v['name']; ?>',<?php echo $v['type']; ?>)"><?php echo $v['title']; ?></a>
                            </li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                    <ul>
                        <template v-if="<?php echo $company['lottery_status']; ?>">
                            <li class="class-A cf" v-for="(item,index) in navList"  @mouseenter="cur = index" @mouseleave="cur = -1" :class="{'active' : cur == index}">
                                <span class="fl class-A-icon">
                                    <em v-if="item.type == 'syxw'" class="cz-icon iconfont icon-xuan" style="color: #6c609e;;"></em>
                                    <em v-if="item.type == 'pk10'" class="cz-icon iconfont icon-icon-test" style="color: #fab62f"></em>
                                    <em v-if="item.type == 'pc28'" class="cz-icon iconfont icon-xingyun1" style="color: #a666b1"></em>
                                    <em v-if="item.type == 'ks'" class="cz-icon iconfont icon-kuai1" style="color: #759e60;"></em>
                                    <em  v-if="item.type == 'ssc'"class="cz-icon iconfont icon-shishicai1" style="color: #e36a6f;"></em>
                                </span>
                                <ul class="fl class-A-list">
                                    <li class="class-A-title">{{item.label}}</li>
                                    <li class="class-A-grounp" v-for="(nav,i) in item.data" v-if="i < 4">
                                        <a @click="link(nav.name,nav.type)">{{nav.label}}</a></a>
                                    </li>
                                </ul>
                                <ul class="lottery-item cf" v-show="cur == index">
                                    <li v-for="(nav,i) in item.data" class="class-B"  @click="link(nav.name,nav.type)" >
                                        <i class="iconfont icon-yitingshou" v-if="nav.pause" style="font-size: 46px"></i>
                                        <span><img :src="nav.image" alt=""></span>
                                        <p>{{nav.label}}</p>
                                    </li>
                                </ul>
                            </li>
                        </template>
                        <template v-if="<?php echo $company['game_status']; ?>">
                            <li class="class-A cf" @mouseenter="cur = 101" @mouseleave="cur = -1" :class="{'active' : cur == 101}">
                                <span class="fl class-A-icon">
                                      <em class="cz-icon iconfont icon-youxi1"></em>
                                </span>
                                <ul class="fl class-A-list">
                                    <li class="class-A-title">休闲游戏</li>
                                    <li class="class-A-grounp" v-for="(nav,i) in navGame" v-if="i < 4">
                                        <a @click="link(nav.name,nav.type)">{{nav.title}}</a></a>
                                    </li>
                                </ul>
                                <!--<i class="el-icon-arrow-right fl"></i>-->
                                <ul class="lottery-item cf" v-show="cur == 101">
                                    <li v-for="(nav,i) in navGame" class="class-B"  @click="link(nav.name,nav.type)" >
                                        <span><img :src="nav.image" alt=""></span>
                                        <p>{{nav.title}}</p>
                                    </li>
                                </ul>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
            <div class="nav-list">
                <ul>
                    <li><a :class="{'cur': curUrl == '/web/' || curUrl == '/web' || curUrl == '/web/index'}" href="<?php echo url('./'); ?>">网站首页</a></li>
                    <li><a :class="{'cur': curUrl == '<?php echo url('index/gameCenter'); ?>'}" href="<?php echo url('index/gameCenter'); ?>">游戏大厅</a></li>
                    <?php if($company['lottery_status'] == 1): if($company['join_isOpen'] == 1): ?>
                    <li><a :class="{'cur': curUrl == '/web/join'}" href="<?php echo url('./join'); ?>">合买大厅</a></li>
                    <?php endif; ?>
                    <li><a :class="{'cur': curUrl == '<?php echo url('index/kaijiang'); ?>'}" href="<?php echo url('index/kaijiang'); ?>">开奖公告</a></li>
                    <?php endif; ?>
                    <li><a :class="{'cur': curUrl == '/web/news/game/navid/<?php echo $newsId; ?>'}" href="/web/news/game/navid/<?php echo $newsId; ?>">游戏资讯</a></li>
                    <li><a :class="{'cur': curUrl == '/web/news/game/navid/<?php echo $noticeId; ?>'}" href="/web/news/game/navid/<?php echo $noticeId; ?>">网站公告</a></li>
                    <li style="position: relative">
                        <a :class="{'cur': curUrl == '/web/news/activity/navid/<?php echo $activityId; ?>'}" href="/web/news/activity/navid/<?php echo $activityId; ?>">优惠活动</a>
                        <img src="/static/vipweb/images/hot_new.gif" alt="" style="position: absolute;right: 0;top: 0px;z-index: 10">
                    </li>
                </ul>
            </div>
            <?php if($company['moblie_status'] == 1): ?>
            <div class="flex tc nav-wap">
                <el-popover
                        placement="bottom" @show="addName" @hide="removeName"
                        width="250"
                        trigger="hover">
                    <div class="down" v-cloak>
                        <?php if(isset($company['android_pic']) && !empty($company['android_pic'])): ?>
                        <div class="ma flex-box">
                            <div class="img"><img src="<?php echo $company['android_pic']; ?>" alt="" width="86"></div>
                            <div>
                                <el-button size="mini"><i class="iconfont icon-anzhuo-copy-copy"></i> 安卓版APP</el-button>
                            </div>
                        </div>
                        <?php endif; if(isset($company['ios_pic']) && !empty($company['ios_pic'])): ?>
                        <div class="ma flex-box">
                            <div class="img"><img src="<?php echo $company['ios_pic']; ?>" alt="" width="86"></div>
                            <div>
                                <el-button size="mini"><i class="iconfont icon-ios-copy"></i> 苹果版APP</el-button>
                            </div>
                        </div>
                        <?php endif; if(isset($company['wap_pic']) && !empty($company['wap_pic'])): ?>
                        <div class="ma flex-box">
                            <div class="img"><img src="<?php echo $company['wap_pic']; ?>" alt="" width="86"></div>
                            <div>
                                <el-button size="mini"><i class="iconfont icon-shouji"></i> 手机WAP</el-button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <a slot="reference" class="flex-box wap"><i class="iconfont icon-shouji" style="font-size: 18px;padding-right: 3px"></i>手机网站</a>
                </el-popover>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(function(){
        var vm = new Vue({
            el:'#top',
            data:{
                loginVisible:false,
                nickname:'<?php if(isset($user['sid'])): ?><?php echo $user['nickname']; endif; ?>',
                accountMoney : '<?php echo $user['money']; ?>', //账户余额
                curUrl : GetUrlRelativePath(),
                show : false,
                navList:<?php echo $navList; ?>,
                navGame:<?php echo $navGame; ?>,
                cur:-1
            },
            methods:{
                addName:function(){
                    $('.wap').addClass('hover')
                },
                removeName:function(){
                    $('.wap').removeClass('hover')
                },
                loginSuccess:function(v){  //登录成功执行
                    this.nickname = v[0];
                    this.accountMoney = v[1];
                    this.loginVisible = false ;
                },
                loginOut:function(){
                    $.get('<?php echo url("login/logout"); ?>',function(){
                        window.location.href = '/web/login/index';
                    })
                },
                //刷新余额
                refreshMoney:function () {
                    var _this = this;
                    _this.$set(_this,'accountMoney','...')
                    $.get('/index/user/getinfo', function (res) {
                        _this.$set(_this,'accountMoney',res.data.money)
                    });
                },
                link:function(a,type){
                    if(!type){
                        window.location = '<?php echo url("./game"); ?>?ext='+ a//休闲类游戏跳转路径
                    }else {
                        window.location = '<?php echo url("./'+ a + '"); ?>'//开奖类游戏跳转路径
                    }
                }
            }
        })
    })

</script>

<link rel="stylesheet" type="text/css"  href="/static/vipweb/css/pay.css">
<script src="/static/vipweb/js/clipboard.min.js"></script>
<style>
    .el-switch__core:after {
        content: "";
        position: absolute;
        top: 1px;
        left: 1px;
        border-radius: 100%;
        transition: all .3s;
        width: 16px;
        height: 16px;
        background-color: #fff;
    }
    .el-switch.is-checked .el-switch__core:after{
        left: 100%;
        margin-left: -17px;
    }
    .el-slider__button{
        user-select: none;
        border-color: rgb(255, 255, 255);
        background-color: rgb(255, 255, 255);
        box-shadow: rgba(143, 143, 143, 0.68) 0px 1px 4px 0px;
    }
</style>
<div class="page-title">
    <div class="web"><span>充值中心</span></div>
</div>
<div class="web pay mt15" id="app">
    <div class="pay-info">
        <span>用户名：<?php echo $user['nickname']; ?></span>
        <span>账户余额：<em class="red"><?php echo $user['money']; ?></em> <?php echo $company['lottery_unit']; ?></span>
        <span v-if="<?php echo $company['game_status']; ?>">游戏账户：<em class="red"><?php echo $user['game_money']; ?></em> <?php echo $company['game_unit']; ?></span>
    </div>
    <div class="pay-cont-tabs border">
        <div class="head">
            <ul class="flex-box">
                <?php if($paysetting['other_alipay'] == 1 OR $paysetting['other_wx'] == 1  OR $select_other_pay != 0): ?>
                <li :class="{'active': activeTab == 0}" @click="tabs(0)">三方在线充值</li>
                <?php endif; if($paysetting['pay_alipay'] == 1): ?>
                <li :class="{'active': activeTab == 1}" @click="tabs(1)">支付宝扫码支付</li>
                <?php endif; if($paysetting['pay_wx'] == 1): ?>
                <li :class="{'active': activeTab == 2}" @click="tabs(2)">微信扫码支付</li>
                <?php endif; if($paysetting['bank_open'] == 1): ?>
                <li :class="{'active': activeTab == 3}" @click="tabs(3)">银行卡转账</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="cont" v-cloak>
            <div style="padding: 5px;background-color: #fffaec;font-size: 16px;font-weight: bold;">
                <span>充值{{isChange ? '<?php echo $setting['game_unit']; ?>' : '<?php echo $setting['lottery_unit']; ?>'}}</span>
                <span class="red" style="font-size:14px; font-weight: normal; margin-left:20px;">每日充值赠送<?php echo $recharge_send_times; ?>次，今日剩余<?php echo $has_recharge_times; ?>次</span>
                <span v-if="<?php echo $company['game_status']; ?>" class="recharge-checkbox fr">转换成游戏币<el-switch v-model="isChange"></el-switch></span>
            </div>
            <div class="row flex-box">
                <div>
                    <label>充值金额:</label>
                </div>
                <div class="rechage-list flex">
                    <ul>
                        <li v-for="(item,index) in listData" :class="{'active':cur == index}" @click="choseRechage(item.money,index)">
                            <div class="coin">
                                <p v-if="isChange">{{item.coin}}<em class="gray"><?php echo $company['game_unit']; ?></em></p>
                                <p v-else>{{item.lottery_money}}<em class="gray"><?php echo $company['lottery_unit']; ?></em></p>
                                <p class="award" v-if="item.award >=1 && <?php echo $has_recharge_times; ?> > 0">额外赠送{{item.award}}<?php echo $company['lottery_unit']; ?></p>
                            </div>
                            <div><b style="color: #000000">{{item.money}}元</b></div>
                            <i class="icon-gou" v-if="index == cur"></i>
                        </li>
                        <!--三方支付 可输入金额 v-if="activeTab == 0"-->
                        <li  :class="[{'active':cur == -1},{'error':state}]" @click="otherRecharge">
                            <div class="coin">
                                <p style="height: 35px">
                                    <el-input class="other-rechage" size="small" placeholder="其他数量" v-model="other" @focus="otherRecharge" @blur="checkMoney"></el-input><em class="gray">{{isChange ? '<?php echo $setting['game_unit']; ?>' : '<?php echo $setting['lottery_unit']; ?>'}}</em>
                                </p>
                                <p class="award" style="height: 18px" v-if="otherAward > 0 && <?php echo $has_recharge_times; ?> > 0"><em>额外赠送{{otherAward}}<?php echo $company['lottery_unit']; ?></em></p>
                                <p class="red recharge-tip" v-if="state">请输入充值数量!</p>
                            </div>
                            <div><b style="color: #000000">{{otherMoney}}元</b></div>
                            <i class="icon-gou" v-if="cur == -1"></i>
                        </li>
                        <!--三方支付 可输入金额 end-->
                    </ul>
                </div>
            </div><!--充值金额列表 end-->
            <!--在线充值-->
            <div v-if="activeTab == 0">
                <div class="row flex-box">
                    <label>支付方式:</label>
                    <div class="flex">
                        <div>
                            <el-radio v-model="wayRadio" label="alipay" border  v-if="otherAlipay">支付宝支付</el-radio>
                            <el-radio v-model="wayRadio" label="wx" border v-if="otherWei">微信支付</el-radio>
                            <template v-for="(item,index) in third_pay">
                                <el-radio v-model="wayRadio" :label="item.id" border>{{item.title}}</el-radio>
                            </template>
                        </div>
                        <!--其他支付下级选项-->
                        <template v-for="(item,index) in third_pay">
                            <div v-if="item.children">
                                <ul class="cf childer-list" v-if="wayRadio == item.id" style="background-color: #f9f9f9;border: 1px solid #f1f1f1;padding: 10px;margin-top: 10px">
                                    <el-radio-group v-model="wayRadiochildren">
                                        <li v-for="(child,i) in item.children" class="fl" style="margin: 0 10px 10px 0">
                                            <el-radio :label="child.id">{{child.title}}</el-radio>
                                        </li>
                                    </el-radio-group>
                                </ul>
                            </div>
                        </template>
                        <!--其他支付下级选项 end-->
                    </div>
                </div>
                <div>需支付 <b class="red" style="font-size: 22px"><em v-if="cur >= 0">{{money}}</em><em v-else>{{otherMoney || 0}}</em></b> 元</div>
                <div class="tc submit">
                    <el-button type="primary" @click="submitOrder">下一步</el-button>
                </div>
            </div>
            <!--在线充值 end-->
            <!--支付宝扫码充值-->
            <div v-if="activeTab == 1">
                <div class="row flex-box">
                    <label>支付方式:</label>
                    <div class="flex">
                        <div class="pay-sm">
                            <div><img :src="imgAlipayData" alt=""></div>
                            <div class="rig-info">
                                <div class="money"><i class="iconfont icon-zhifubaozhifu" style="font-size: 20px;color: #00aaee"></i> 支付宝扫码，支付
                                    <b class="money-val red" v-if="cur==-1">{{otherMoney || 0}}</b>
                                    <b class="money-val red" v-else>{{money}}</b> 元
                                </div>
                                <div class="remark">
                                    支付成功后请填写备注信息
                                    <el-tooltip placement="bottom">
                                        <div slot="content">
                                            可填写交易订单号后6位或<br>
                                            转账时的备注等相关信息
                                        </div>
                                        <i class="el-icon-question"></i>
                                    </el-tooltip>
                                </div>
                                <div class="input"><el-input size="small" v-model="orderAlipay" placeholder="请填写备注信息"></el-input></div>
                                <div class="btn mt15"><el-button type="primary" size="small" @click="submitNum">提交</el-button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--支付宝扫码充值 end-->
            <!--微信扫码充值-->
            <div v-if="activeTab == 2">
                <div class="row flex-box">
                    <label>支付方式:</label>
                    <div class="flex">
                        <div class="pay-sm">
                            <div><img :src="imgWeiData" alt=""></div>
                            <div class="rig-info">
                                <div class="money"><i class="iconfont icon-weixinzhifu" style="font-size: 20px;color: #41b035"></i> 微信扫码，支付
                                    <b class="money-val red" v-if="cur==-1">{{otherMoney || 0}}</b>
                                    <b class="money-val red" v-else>{{money}}</b> 元
                                </div>
                                <div class="remark">
                                    支付成功后请填写备注信息
                                    <el-tooltip placement="bottom">
                                        <div slot="content">
                                            可填写交易订单号后6位或<br>
                                            转账时的备注等相关信息
                                        </div>
                                        <i class="el-icon-question"></i>
                                    </el-tooltip>
                                </div>
                                <div class="input"><el-input size="small" v-model="orderWei" placeholder="请填写备注信息"></el-input></div>
                                <div class="btn mt15"><el-button type="primary" size="small" @click="submitNum">提交</el-button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--微信扫码充值 end-->
            <!--银行卡转账充值-->
            <div v-if="activeTab == 3">
                <div class="row">
                    <div class="bank-pay-title"><em class="tip-num">1</em><label>入款银行选择:</label></div>
                    <div>
                        <ul class="bank-list cf">
                            <li v-for="(item,index) in bankList" :class="{'active': bankCur == index}" @click="bankCur=index">
                                <div><span class="label">开户银行：</span>
                                    <input id="bank_name" type="text" v-model="item.bank_name" type="text" autocomplete="off" readonly></div>
                                <div>
                                    <span class="label">开户网点：</span>
                                    <input id="bank_area" type="text" v-model="item.bank_area" type="text" autocomplete="off" readonly>
                                    <a class="copy-btn link" data-clipboard-action="copy" data-clipboard-target="#bank_area">复制</a>
                                </div>
                                <div>
                                    <span class="label">收款账号：</span>
                                    <input id="bank_num" type="text" v-model="item.bank_num" type="text" autocomplete="off" readonly>
                                    <a class="copy-btn link" data-clipboard-action="copy" data-clipboard-target="#bank_num">复制</a>
                                </div>
                                <div>
                                    <span class="label">收款人：</span>
                                    <input id="name" type="text" v-model="item.name" type="text" autocomplete="off" readonly>
                                    <a class="copy-btn link" data-clipboard-action="copy" data-clipboard-target="#name">复制</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="mt15">* 您目前选择的是<b class="red">{{curSelectBank}}</b></div>
                    <div>
                        <p class="money">需转账金额
                            <b class="money-val red" v-if="cur==-1">{{otherMoney || 0}}</b>
                            <b class="money-val red" v-else>{{money}}</b> 元
                            <em class="red f12"> (<i class="iconfont icon-jinggao"></i>仅限于使用银行卡转账，您实际支付金额必须与此处的金额一致，若不一致，将无法到账。)</em>
                        </p>
                    </div>
                    <div class="bank-pay-title"><em class="tip-num">2</em><label>填写转账资料：</label></div>
                    <div class="input mt15" style="width: 550px">
                        <el-form :model="ruleForm" :rules="rules" size="small" ref="ruleForm" label-width="120px" class="demo-ruleForm">
                            <el-form-item label="付款人姓名" prop="name">
                                <el-input v-model="ruleForm.name"></el-input>
                            </el-form-item>
                            <el-form-item label="付款银行名称" prop="bank_name">
                                <el-input v-model="ruleForm.bank_name"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click="submitForm('ruleForm')">提交</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                    <div class="acc-txt tips">
                        <p class="pay-ico"><i class="iconfont icon-jinggao red"></i> 转账须知：</p>
                        <p>* 转账方式仅限于您使用<em style="color: #333333">银行卡</em>进行转账，请勿使用其他方式。</p>
                        <p>* 所填写转账资料必须与您实际支付信息一致，若不一致，将无法到账。</p>
                        <p>* 以上银行账号仅限本次使用，账号不定期更换！请您根据本页面所提供的收款账号进行转账，若存款至过期账号，本网站恕不负责！</p>
                    </div>
                </div>
            </div>
            <!--银行卡转账充值 end-->
            <div class="tip">
                <h6>充值说明:</h6>
                <p><?php echo $paysetting['recharge_info']; ?></p>
            </div>
        </div>
    </div>
    <el-dialog
            title="提示"
            :visible.sync="payDialogVisible"
            width="20%"
            top="0"
            center>
        <span>请在新开页面完成付款!</span>
        <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click.native="finishPay">已完成付款</el-button>
        </span>
    </el-dialog>
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
        <div class="flex-box help">
            <div class="qrcode">
                <p class="tc" style="font-size: 13px;font-weight: normal"><?php echo $company['company_wx']; ?></p>
                <img src="<?php echo $company['company_img']; ?>" alt="" width="110">
            </div>
            <dl>
                <dt>新手教程</dt>
                <dd>
                    <?php if(is_array($footArticle['course']) || $footArticle['course'] instanceof \think\Collection || $footArticle['course'] instanceof \think\Paginator): $i = 0; $__LIST__ = $footArticle['course'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <a href="<?php echo url('news/help'); ?>/id/<?php echo $vo['id']; ?>/navid/<?php echo $vo['nav_id']; ?>" target="_blank"><?php echo $vo['title']; ?></a>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </dd>
            </dl>
            <dl>
                <dt>彩票投注</dt>
                <dd>
                    <?php if(is_array($footArticle['betting']) || $footArticle['betting'] instanceof \think\Collection || $footArticle['betting'] instanceof \think\Paginator): $i = 0; $__LIST__ = $footArticle['betting'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <a href="<?php echo url('news/help'); ?>/id/<?php echo $vo['id']; ?>/navid/<?php echo $vo['nav_id']; ?>" target="_blank"><?php echo $vo['title']; ?></a>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </dd>
            </dl>
            <dl>
                <dt>充值兑换</dt>
                <dd>
                    <?php if(is_array($footArticle['recharge']) || $footArticle['recharge'] instanceof \think\Collection || $footArticle['recharge'] instanceof \think\Paginator): $i = 0; $__LIST__ = $footArticle['recharge'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <a href="<?php echo url('news/help'); ?>/id/<?php echo $vo['id']; ?>/navid/<?php echo $vo['nav_id']; ?>" target="_blank"><?php echo $vo['title']; ?></a>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </dd>
            </dl>
            <dl>
                <dt>玩法介绍</dt>
                <dd>
                    <?php if(is_array($footArticle['playinfo']) || $footArticle['playinfo'] instanceof \think\Collection || $footArticle['playinfo'] instanceof \think\Paginator): $i = 0; $__LIST__ = $footArticle['playinfo'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <a href="<?php echo url('news/help'); ?>/id/<?php echo $vo['id']; ?>/navid/<?php echo $vo['nav_id']; ?>" target="_blank"><?php echo $vo['title']; ?></a>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </dd>
            </dl>
            <dl class="last">
                <dt>服务条款</dt>
                <dd>
                    <a @click="ysVisible=true">隐私保护协议</a>
                    <a @click="gameVisible=true">游戏服务协议</a>
                    <a @click="regVisible=true">用户注册协议</a>
                </dd>
            </dl>
        </div>

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
    <!--注册协议-->
    <el-dialog v-cloak
            title="用户注册协议"
            :visible.sync="regVisible"
            :lock-scroll = false :center="true"
            top = 0
            width="60%">
        <div class="service-cont"><?php echo $company['user_web']; ?></div>
        <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click="regVisible = false">我知道了</el-button>
         </span>
    </el-dialog>
    <!--隐私保护协议-->
    <el-dialog v-cloak
            title="隐私保护协议"
            :visible.sync="ysVisible"
            :lock-scroll = false :center="true"
            top = 0
            width="60%">
        <div class="service-cont"><?php echo $company['user_service']; ?></div>
        <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click="ysVisible = false">我知道了</el-button>
         </span>
    </el-dialog>
    <!--游戏服务协议-->
    <el-dialog v-cloak
            title="游戏服务协议"
            :visible.sync="gameVisible"
            :lock-scroll = false :center="true"
            top = 0
            width="60%">
        <div class="service-cont"><?php echo $company['web_service']; ?></div>
        <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click="gameVisible = false">我知道了</el-button>
         </span>
    </el-dialog>
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
<script>
    'use strict';

    $(function () {
        new Vue({
            el: "#app",
            data: {
                third_pay:<?php echo $third_pay; ?>, //三方支付列表
                listData: <?php echo $rechargeList; ?>, //充值金额列表
                activeTab: '0',
                cur: '0',
                money: '',
                other: '',
                state: false,
                scaleCoin: parseInt('<?php echo $paysetting['recharge_award']; ?>'), //游戏币比值
                isChange: false, //转换成游戏币
                imgAlipayData: '', //支付宝充值二维码图片
                imgWeiData: '', //微信充值二维码图片
                orderAlipay: '', //支付宝充值订单号
                orderWei: '', //微信充值订单号
                wayRadio: '<?php echo $select_other_pay; ?>', //默认三方支付方式 alipay 支付宝 wx 微信 + 其他三方支付
                wayRadiochildren: '',
                payWay: '',
                payDialogVisible: false,

                otherAlipay: parseInt('<?php echo $paysetting['other_alipay']; ?>'), //三方支付-支付宝
                otherWei: parseInt('<?php echo $paysetting['other_wx']; ?>'), //三方支付-微信
                otherPay: '<?php echo $select_other_pay; ?>', //三方支付-新增
                payAlipay: parseInt('<?php echo $paysetting['pay_alipay']; ?>'), //扫码支付-支付宝
                payWei: parseInt('<?php echo $paysetting['pay_wx']; ?>'), //扫码支付-微信

                payAlipayUrl: <?php echo $payAlipayUrl; ?>, //支付宝扫码图片
                payWeiUrl: <?php echo $payWeiUrl; ?>, //微信扫码图片
                award: <?php echo $award; ?>, //赠送比例

                otherAward: 0,

                bankList:<?php echo $bank_config; ?>,
                bankCur:0,
                ruleForm: {
                    name: '',
                    bank_name: ''
                },
                rules: {
                    name: [
                        { required: true, message: '请输入付款人姓名', trigger: 'blur' }
                    ],
                    bank_name: [
                        { required: true, message: '请输入付款银行名称', trigger: 'blur' }
                    ]
                }
            },
            computed: {
                //其他充值金额计算
                otherMoney: function() {
                    var reg = /^[0-9]+.?[0-9]*$/;
                    var res = reg.test(this.other);
                    if(res) {
                        return this.isChange ? this.other / this.scaleCoin : this.other
                    } else {
                        return '';
                    }
                },
                curSelectBank:function () {
                    return this.bankList.length ? this.bankList[this.bankCur].bank_name : ''
                }
            },
            watch: {
                other: function(val) {
                    this.state = false
                    //计算额外赠送值
                    if (this.otherMoney < 1) {
                        this.otherAward = 0;
                    } else {
                        for (var i in this.listData) {
                            if (parseInt(this.otherMoney) >= this.listData[i].money) {
                                //充值游戏币
                                if (this.isChange) {
                                    this.otherAward = Math.floor(this.award[parseInt(this.listData[i].money)] * (val / this.scaleCoin) * 100 / 100);
                                }else {
                                    this.otherAward = Math.floor(this.award[parseInt(this.listData[i].money)] * val * 100 / 100);
                                }
                            }
                        }
                    }
                },
                isChange: function(val) {
                    this.checkMoney();
                },
                wayRadio:function (val) {
                    this.wayRadiochildren = ''
                    if(val !== 'alipay' && val !== 'wx'){
                        for(var i = 0 ;i < this.third_pay.length ; i ++){
                            if(val == this.third_pay[i].id && this.third_pay[i].children){
                                this.wayRadiochildren = this.third_pay[i].children[0].id
                            }
                        }
                    }
                }
            },
            methods: {
                finishPay:function () {
                    location.reload();
                },
                tabs: function(n) {
                    this.activeTab = n;
                    if (this.cur > -1) {
                        this.money = this.listData[parseInt(this.cur)].money;
                        this.imgAlipayData = this.payAlipayUrl[parseInt(this.listData[parseInt(this.cur)].money)];
                        this.imgWeiData = this.payWeiUrl[parseInt(this.listData[parseInt(this.cur)].money)];
                    } else {
                        this.imgAlipayData = this.payAlipayUrl.other;
                        this.imgWeiData = this.payWeiUrl.other;
                    }
                },
                //获取焦点时输入其他数量
                otherRecharge: function() {
                    this.cur = -1;
                    if (this.cur == -1) {
                        this.imgAlipayData = this.payAlipayUrl.other;
                        this.imgWeiData = this.payWeiUrl.other;
                    }
                },
                //失去焦点时检查输入数量、赠送值
                checkMoney: function() {
                    var other = parseInt(this.other);
                    var scaleCoin = parseInt(this.scaleCoin);
                    if (!this.other) {
                        this.state = true;
                        return
                    }else {
                        this.state = false;
                        if (this.isChange) { //充值游戏币
                            this.state = false;
                            if (other < scaleCoin) {
                                this.other = this.scaleCoin;
                            } else {
                                this.other = Math.ceil(other / scaleCoin * 100 / 100) * scaleCoin;
                            }
                        }else {
                            this.other = parseInt(other);
                        }
                    }
                },
                //选择金额
                choseRechage: function(m, index) {
                    this.money = m;
                    this.state = false;
                    this.cur = index;
                    if (this.activeTab == 1) {
                        this.imgAlipayData = this.payAlipayUrl[parseInt(m)];
                    }
                    if (this.activeTab == 2) {
                        this.imgWeiData = this.payWeiUrl[parseInt(m)];
                    }
                },

                //在线充值提交订单
                submitOrder: function() {
                    var money, type;
                    if (this.cur >= 0) {
                        money = this.money;
                    } else {
                        money = this.otherMoney;
                    }
                    var val = this.isChange ? 1 : 2
                    type = '&type=' +  val
                    if (money < 1) {
                        this.$alert('请输入充值数量!', '提示', {
                            confirmButtonText: '确定',
                            type: 'warning',
                            center: true,
                            lockScroll: false,
                            showClose: false
                        });
                        return;
                    };
                    this.payDialogVisible = true;
                    //支付宝充值跳转
                    if (this.wayRadio == 'alipay') {
                        window.open('/pay/pay/pcAlipay?total_amount=' + money + type);
                    }else if(this.wayRadio == 'wx') { //微信充值跳转
                        window.open('/pay/pay/wxqrcodepay?total_amount=' + money + type);
                    }else{
                        var third_id = '&third_id='+  this.wayRadio ;
                        var third_type = this.wayRadiochildren ?  '&third_type='+  this.wayRadiochildren : '' ;
                        window.open('/pay/pay/payToThird?total_amount=' + money + type + third_id + third_type);
                    }
                },
                //扫码支付--提交订单
                submitNum: function() {
                    var _this = this;
                    var type, num, way, total_amount;
                    type = _this.isChange ? 1 : 2 //1转换为游戏币 2充值彩金
                    if (_this.activeTab == 1) {
                        way = 3;
                        num = _this.orderAlipay;
                    }
                    if (_this.activeTab == 2) {
                        way = 2;
                        num = _this.orderWei;
                    }
                    if (!num.length) {
                        _this.$alert('请输入备注信息!', '提示', {
                            confirmButtonText: '确定',
                            type: 'error',
                            center: true,
                            lockScroll: false,
                            showClose: false
                        });
                        return;
                    }
                    if (_this.cur == -1) {
                        total_amount = _this.otherMoney;
                    } else {
                        total_amount = _this.money;
                    }
                    $.get('/pay/pay/saoToPay', {
                        way: way,
                        total_amount: total_amount,
                        type: type,
                        info: num
                    }, function (res) {
                        if (!res.err) {
                            _this.$alert('提交成功，请等待管理员审核!', '提示', {
                                confirmButtonText: '确定',
                                type: 'success',
                                center: true,
                                lockScroll: false,
                                showClose: false
                            });
                            _this.orderAlipay = '';
                            _this.orderWei = '';
                        } else {
                            _this.$alert(res.msg, '提示', {
                                confirmButtonText: '确定',
                                type: 'error',
                                center: true,
                                lockScroll: false,
                                showClose: false
                            });
                        }
                    });
                },
                //银行卡转账提交订单
                submitForm(formName) {
                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            var _this = this;
                            var type, total_amount;
                            type = _this.isChange ? 1 : 2 //1转换为游戏币 2充值彩金
                            if (_this.cur == -1) {
                                total_amount = _this.otherMoney;
                            } else {
                                total_amount = _this.money;
                            }
                            $.get('/pay/pay/saoToPay', {
                                way: 4,
                                total_amount: total_amount,
                                type: type,
                                re_bank_suffix : _this.bankCur,
                                name: _this.ruleForm.name,
                                bank_name : _this.ruleForm.bank_name
                            }, function (res) {
                                if (!res.err) {
                                    _this.$alert('提交成功，请等待管理员审核!', '提示', {
                                        confirmButtonText: '确定',
                                        type: 'success',
                                        center: true,
                                        lockScroll: false,
                                        showClose: false
                                    });
                                    _this.ruleForm.name = ''
                                    _this.ruleForm.bank_name = ''
                                } else {
                                    _this.$alert(res.msg, '提示', {
                                        confirmButtonText: '确定',
                                        type: 'error',
                                        center: true,
                                        lockScroll: false,
                                        showClose: false
                                    });
                                }
                            });
                        } else {
                            return false;
                        }
                    });
                }
            },
            created: function() {
                if (!this.otherAlipay && !this.otherWei && this.otherPay == 0) {
                    //三方支付未开启
                    this.activeTab = this.payAlipay ? '1' : this.payWei ? '2' : '<?php echo $paysetting['bank_open']; ?>' == 1 ? '3' : '';
                    this.imgAlipayData = this.payAlipayUrl[parseInt(this.listData[0].money)];
                    this.imgWeiData = this.payWeiUrl[parseInt(this.listData[0].money)];
                }
                this.money = this.listData[0].money;
                this.wayRadiochildren = ''
                if(this.wayRadio !== 'alipay' && this.wayRadio !== 'wx'){
                    for(var i = 0 ;i < this.third_pay.length ; i ++){
                        if(this.wayRadio == this.third_pay[i].id && this.third_pay[i].children){
                            this.wayRadiochildren = this.third_pay[i].children[0].id
                        }
                    }
                }

                var _this = this
                var clipboard = new ClipboardJS('.copy-btn');
                clipboard.on('success', function(e) {
                    _this.$alert( '复制成功', '提示', {
                        confirmButtonText: '确定',
                        type: 'success',
                        center: true
                    });
                });
                clipboard.on('error', function(e) {
                    alert('请选择“拷贝”进行复制!')
                });
            }
        });
    });
</script>