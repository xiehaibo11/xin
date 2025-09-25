<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:71:"/www/wwwroot/www.aowenbocai.icu/public/../app/web/view/index/index.html";i:1758687068;s:69:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/islogin_head.html";i:1758671433;s:61:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/link.html";i:1758671433;s:61:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/head.html";i:1758681975;s:61:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/foot.html";i:1758671433;s:60:"/www/wwwroot/www.aowenbocai.icu/app/web/view/common/end.html";i:1758671433;}*/ ?>
<?php if($company['join_isOpen'] == 1): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php if(isset($title)): ?><?php echo $title; else: ?>首页<?php endif; ?><?php echo isset($site_name) ? '-' . $site_name : ''; ?></title>
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
    <div class="top bg web_">
        <div class="web flex-box">
            <div class="flex">您好，欢迎来到<?php echo $site_name; ?>！</div>
            <!--<div class="tr flex">-->
            <!--<em class="red">适龄提示：本站游戏适合18岁以上玩家</em><em style="color: green;padding-left: 5px">未满18岁的玩家需在家长监督下进行游戏</em>-->
            <!--<a href="http://www.99wan.com/jiazhang/index.htm" class="link" target="_blank" style="padding-right: 25px;">家长监护工程</a>-->
            <!--</div>-->
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
                        <p><?php if(isset($company['company_img_qq'])): ?><img src="<?php echo $company['company_img_qq']; ?>" width="160"><?php endif; ?></p>
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
                                <el-button size="mini"><i class="iconfont icon-anzhuo-copy-copy"></i> 安卓APP</el-button>
                            </div>
                        </div>
                        <?php endif; if(isset($company['ios_pic']) && !empty($company['ios_pic'])): ?>
                        <div class="ma flex-box">
                            <div class="img"><img src="<?php echo $company['ios_pic']; ?>" alt="" width="86"></div>
                            <div>
                                <el-button size="mini"><i class="iconfont icon-ios-copy"></i> 苹果APP</el-button>
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
                curUrl : GetUrlRelativePath(),
                show : false,
                navList:<?php echo $navList; ?>,
                navGame:<?php echo $navGame; ?>,
                cur:-1
            },
            computed:{
                showIcon:function () {
                    return function (type) {
                        console.log(type)
                        return type
                    }
                }
            },
            methods:{
                addName:function(){
                    $('.wap').addClass('hover')
                },
                removeName:function(){
                    $('.wap').removeClass('hover')
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
<?php else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php if(isset($title)): ?><?php echo $title; else: ?>首页<?php endif; ?><?php echo isset($site_name) ? '-' . $site_name : ''; ?></title>
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

<?php endif; ?>
<link rel="stylesheet" href="/static/css/swiper.min.css">
<script src="/static/js/swiper.min.js"></script>
<script src="/static/vipweb/js/scroll.js"></script>
<script src="/static/vipweb/vue-component/module-login.js"></script>
<script src="//at.alicdn.com/t/font_613146_ca4pasyvmtb.js"></script>
<style>
    .icon {
        width: 28px;
        height: 30px;
        vertical-align:middle;
        fill: currentColor;
        overflow: hidden;
    }
</style>
<!--banner start-->
<div class="pic-slide web_">
    <div class="swiper-container">
        <div class="swiper-wrapper" >
            <?php if(is_array($banner) || $banner instanceof \think\Collection || $banner instanceof \think\Paginator): $i = 0; $__LIST__ = $banner;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <div class="swiper-slide">
                <?php if(empty($v['url']) || (($v['url'] instanceof \think\Collection || $v['url'] instanceof \think\Paginator ) && $v['url']->isEmpty())): ?>
                    <a href="javascript:void(0)" target="_blank" style="background-image: url('<?php echo $v['img_url']; ?>')"></a>
                    <?php else: ?>
                    <a href="<?php echo $v['url']; ?>" target="_blank" style="background-image: url('<?php echo $v['img_url']; ?>')"></a>
                <?php endif; ?>
            </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>
<!--banner end-->
<div id="app">
    <!--page2 start-->
    <div class="bg">
        <div class="web">
            <div class="cf">
                <div class="fl row-max" style="width: 910px;">
                    <div class="card" style="padding:15px 0 0 0; ">
                        <div class="card-header flex-box">
                            <span class="title">推荐游戏</span>
                            <span class="more flex tr"><a href="<?php echo url('gamecenter'); ?>">更多游戏>></a></span>
                        </div>
                        <div class="card-cont">
                            <ul class="game-list cf">
                                <?php if(is_array($rec) || $rec instanceof \think\Collection || $rec instanceof \think\Paginator): $i = 0; $__LIST__ = $rec;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                                <li>
                                    <div class="r-cont" style="background-image: url('<?php echo $v['img']; ?>')"></div>
                                    <div class="r-mark" style="margin-top: 0">
                                        <div class="tl title">
                                            <h5><?php echo $v['title']; ?></h5>
                                            <p><?php echo $v['info']; ?></p>
                                            <p><?php echo $v['remark']; ?></p>
                                        </div>
                                        <?php if($v['type'] == 1): ?>
                                        <a @click="link('<?php echo $v['name']; ?>',<?php echo $v['type']; ?>)"><el-button size="mini" type="danger">进入游戏</el-button></a>
                                        <?php else: ?>
                                        <a @click="link('<?php echo $v['link']; ?>',<?php echo $v['type']; ?>)"><el-button size="mini" type="danger">进入游戏</el-button></a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="fr row-small news" style="width: 290px">
                    <el-tabs v-model="activeName" v-cloak>
                        <el-tab-pane label="网站公告" name="first">
                            <ul class="news-list">
                                <?php if(is_array($article['notice']) || $article['notice'] instanceof \think\Collection || $article['notice'] instanceof \think\Paginator): $k = 0; $__LIST__ = $article['notice'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k;?>
                                <li class="flex-box">
                                    <a class="flex" :href="'<?php echo url('./news'); ?>' + '/view/?id=<?php echo $v['id']; ?>&amp;navid=<?php echo $v['nav_id']; ?>'" target="_blank"  title="<?php echo $v['title']; ?>">
                                        <?php if (mb_strlen($v['title']) > 15){
                                        echo mb_substr($v['title'], 0, 15, 'utf-8')."...";
                                        }else{
                                        echo $v['title'];
                                        }
                                        ?>
                                    </a>
                                    <span class="time"><?php echo substr($v['create_time'], 5, 5); ?></span>
                                </li>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </el-tab-pane>
                        <el-tab-pane label="游戏资讯" name="second">
                            <ul class="news-list">
                                <?php if(is_array($article['news']) || $article['news'] instanceof \think\Collection || $article['news'] instanceof \think\Paginator): $i = 0; $__LIST__ = $article['news'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                                <li class="flex-box">
                                    <a class="flex" :href="'<?php echo url('./news'); ?>' + '/view/?id=<?php echo $v['id']; ?>&amp;navid=<?php echo $v['nav_id']; ?>'" target="_blank"  title="<?php echo $v['title']; ?>">
                                        <?php if (mb_strlen($v['title']) > 15){
                                        echo mb_substr($v['title'], 0, 15, 'utf-8')."...";
                                        }else{
                                        echo $v['title'];
                                        }
                                        ?>
                                    </a>
                                    <span class="time"><?php echo substr($v['create_time'], 5, 5); ?></span>
                                </li>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </div>
        </div>
    </div>
    <!--page2 end-->
    <!--ads start-->
    <div class="web">
        <div class="ads-slide">
            <div class="swiper-container1">
                <div class="swiper-wrapper" >
                    <?php if(is_array($bannerA) || $bannerA instanceof \think\Collection || $bannerA instanceof \think\Paginator): $i = 0; $__LIST__ = $bannerA;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                    <div class="swiper-slide">
                        <a href="javascript:void(0)" target="_blank" style="background-image: url('<?php echo $v['img_url']; ?>')"></a>
                    </div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
    <!--ads end-->
    <!--part3 start-->
    <div class="web mt15 flex-box" v-if="Number(<?php echo $company['lottery_status']; ?>)" style="align-items: flex-start;">
        <div class="row-max jion" v-if="Number(<?php echo $company['join_isOpen']; ?>)">
            <div class="el-tabs el-tabs--top el-tabs--border-card" style="border-bottom: none">
                <div class="el-tabs__header is-top">
                    <div class="el-tabs__nav-wrap is-top">
                        <div role="tablist" class="el-tabs__nav" v-cloak>
                            <div class="el-tabs__item" :class="{'is-active': tabs == -1}" @click="tab(-1,'')">合买推荐</div>
                            <div class="el-tabs__item"  v-for="(item,index) in jionNav" :class="{'is-active': tabs == index}" @click="tab(index,item.id)" v-if="index < 6">{{item.title}}</div>
                        </div>
                        <div class="tr flex"><a href="<?php echo url('./join'); ?>" class="link" style="padding-right: 10px;padding-top: 7px;display: inline-block;">进入合买大厅>></a></div>
                    </div>
                </div>
                <!--合买列表 start-->
                <jion-all :url="url" :tabs="tabs" @change-url="changeUrl" :notice-visble="noticeVisble" v-cloak></jion-all>
                <!--合买列表 end-->
            </div>
        </div>
        <div class="row-small" :class="{'web_ flex-box': !Number(<?php echo $company['join_isOpen']; ?>)}"  style="align-items: flex-start;">
            <div class="rolling flex" :class="{'mr10 rolling-m':!Number(<?php echo $company['join_isOpen']; ?>)}">
                <div class="win-news-hd card-title">
                    <span>中奖快讯</span>
                </div>
                <div class="cont scroll-box">
                    <div class="myscroll" :class="{'myscroll-m':!Number(<?php echo $company['join_isOpen']; ?>)}">
                        <ul>
                            <?php if(is_array($awardList) || $awardList instanceof \think\Collection || $awardList instanceof \think\Paginator): $i = 0; $__LIST__ = $awardList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                            <li>
                                <span>【<?php echo $item['lotteryName']; ?>】<?php echo mb_substr($item['nickname'], 0, 1); ?>***</span>
                                <span class="flex tr"><em class="red"><?php echo $item['money']; ?></em> <?php echo $company['lottery_unit']; ?></span>
                            </li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="ranking flex" :class="{'mt15': Number(<?php echo $company['join_isOpen']; ?>)}">
                <div class="win-news-hd card-title">
                    <span>中奖排行</span>
                </div>
                <div class="cont">
                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <th class="bg" width="50">排名</th>
                            <th class="bg">用户名</th>
                            <th class="bg" width="150">中奖金额</th>
                        </tr>
                        <?php if(is_array($top10) || $top10 instanceof \think\Collection || $top10 instanceof \think\Paginator): $i = 0; $__LIST__ = $top10;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                        <tr>
                            <?php if($key > 2): ?>
                            <td><span class="rank-index"><?php echo $key+1; ?></span></td>
                            <?php else: ?>
                            <td valign="middle"><svg class="icon" aria-hidden="true"><use xlink:href="#icon-paiming-<?php echo $key+1; ?>"></use></svg></td>
                            <?php endif; ?>
                            <td><?php echo mb_substr($item['nickname'], 0, 1); ?>***</td>
                            <td style="text-align: right;padding-right: 20px;"><em class="red"><?php echo $item['award']; ?></em> <?php echo $company['lottery_unit']; ?></td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--part3 end-->
    <!--公告弹窗-->
    <!--公告弹窗 end-->
    <div class="notice-popup" v-if="noticeVisble">
        <el-dialog v-cloak
                   @close="closeNotice"
                   title="平台公告"
                   :visible.sync="noticeVisble"
                   :close-on-click-modal = false
                   :close-on-press-escape = false
                   :lock-scroll = false :center="true"
                   top = 0
                   width="980px">
            <div class="notice-cont" :style="{'height':contentH + 'px'}">
                <div class="cf">
                    <div class="cont-left fl" :style="{'height':contentH + 'px'}">
                        <ul class="title-item">
                            <li @click="cur=index" v-for="(item,index) in noticeList" :class="{'active':cur==index}" :title="item.title"><i class="iconfont icon-tubiaozhizuomoban-"></i>  {{item.title}}</li>
                        </ul>
                    </div>
                    <div class="cont-rig fr" :style="{'height':contentH + 'px'}">
                        <template v-for="(item,index) in noticeList">
                            <template v-if="cur==index">
                                <h4 class="content-item-title">
                                    <p>{{item.title}}</p>
                                    <p class="gray tc">{{item.create_time}}</p>
                                </h4>
                                <div class="content-item-detail" v-html="item.content"></div>
                            </template>
                        </template>
                    </div>
                </div>
            </div>
            <span slot="footer" class="dialog-footer tr">
                <el-checkbox v-model="todayIsShow">今日不再显示</el-checkbox>
                <el-button type="primary" @click.native="closeNotice">关闭</el-button>
            </span>
        </el-dialog>
    </div>
</div>
<!--合买推荐-->
<template id="jionAll">
    <div>
        <!--logindialog start-->
        <login-info site-name="<?php echo $site_name; ?>" @login-success="loginSuccess" @login-out="loginOut" msg-num="<?php echo $msgNum; ?>" :money="accountMoney" :nickname="nickname" :login-visible="loginVisible"
                    reg-url="<?php echo url('reg/index'); ?>" my-url="<?php echo url('user/my'); ?>" @open-dialog="loginVisible = true" @close-dialog="loginVisible = false" @refresh="refreshMoney">
        </login-info>
        <!--logindialog end-->
        <div class="options flex-box">
            <div class="mr10">
                <el-select
                        @change="doSearch(search.money)"
                        size="mini"
                        v-model="search.money"
                        placeholder="方案总金额">
                    <el-option
                            v-for="item in moneyOptions"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                    </el-option>
                </el-select>
            </div>
            <div class="mr10" v-if="<?php echo $system['isGain']; ?>">
                <el-select
                        @change="doSearch(search.tc)"
                        size="mini"
                        v-model="search.tc"
                        placeholder="方案佣金">
                    <el-option
                            v-for="item in tcOptions"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                    </el-option>
                </el-select>
            </div>
            <div class="mr5">
                <el-input v-model="user" placeholder="发起人" size="mini" style="display: inline-block;width: 140px" @keyup.enter.native="doSearch"></el-input>
            </div>
            <div class="mr10"><el-button @click.native="doSearch" size="mini">搜索</el-button></div>
            <a class="gray" @click="recover">恢复默认</a>
            <div class="flex tr">
                <span style="cursor: pointer" @click="refresh"><i class="el-icon-refresh"></i> 刷新</span>
            </div>
        </div>
        <el-table :data="jionData" size="small" @sort-change="sortChange">
            <el-table-column property="title" label="游戏"  width="90"></el-table-column>
            <el-table-column property="issue" label="期号"></el-table-column>
            <el-table-column property="nickname" label="发起人(战绩)" width="130">
                <template slot-scope="scope">
                    {{scope.row.nickname}}
                    <span class="join-level">
                        <span v-if="scope.row.queen>0">
                            <i class="hg"><sub :class="'lv'+ scope.row.queen"></sub></i>
                        </span>
                        <span v-if="scope.row.sunNum>0">
                            <i class="ty"><sub :class="'lv'+ scope.row.sunNum"></sub></i>
                        </span>
                        <span v-if="scope.row.MoonNum>0">
                            <i class="yl"><sub :class="'lv'+ scope.row.MoonNum"></sub></i>
                        </span>
                        <span v-if="scope.row.starNum>0">
                            <i class="xx"><sub :class="'lv'+ scope.row.starNum"></sub></i>
                        </span>
                    </span>
                </template>
            </el-table-column>
            <!--sortable="custom"-->
            <el-table-column label="方案金额" property="total_money" width="100">
                <template slot-scope="scope">
                    {{scope.row.total_money}}<?php echo $company['lottery_unit']; ?>
                </template>
            </el-table-column>
            <el-table-column  property="ure" label="剩余份数 | 每份金额" width="150">
                <template slot-scope="scope">
                    <span><em :class="{'red' : scope.row.ure > 0}">{{scope.row.ure}}</em>份 <em class="gray" style="padding-right: 5px;">|</em> <em class="red">{{getPerMoney(scope.row.total_share,scope.row.total_money)}}</em><?php echo $company['lottery_unit']; ?></span>
                </template>
            </el-table-column>
            <!--<el-table-column  property="ure" label="每份金额" width="100">-->
                <!--<template slot-scope="scope">-->
                    <!--<em class="red">{{getPerMoney(scope.row.total_share,scope.row.total_money)}}</em><?php echo $company['lottery_unit']; ?>-->
                <!--</template>-->
            <!--</el-table-column>-->
            <el-table-column label="进度 + 保底" property="buyprecent" width="130">
                <template slot-scope="scope">
                    {{scope.row.buyprecent}}%+{{scope.row.bdprecent}}%  <i class="iconfont icon-bao1" style="color: red"></i>
                </template>
            </el-table-column>
            <el-table-column label="认购份数"  width="150">
                <template slot-scope="scope">
                    <template v-if="scope.row.ure_finsh == 0">
                        <em style="color: #b7b7b7;">已满员</em>
                    </template>
                    <template v-if="scope.row.ure_finsh == -1">
                        <!--合买截止-->
                        <em style="color: #b7b7b7; " v-if="scope.row.status == 0">等待开奖</em>
                        <em class="red" v-if="scope.row.status == 1">已中奖</em>
                        <em style="color: #b7b7b7; " v-if="scope.row.status == 2">未中奖</em>
                        <em style="color: #b7b7b7; " v-if="scope.row.status == 6">流产撤单</em>
                        <em style="color: #b7b7b7; " v-if="scope.row.status == 7">系统撤单</em>
                    </template>
                    <template v-if="scope.row.ure_finsh > 0">
                        <buy-input :item="scope.row" @orders="orderInfo"></buy-input>
                    </template>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="50">
                <template slot-scope="scope">
                    <el-button size="mini" type="text" @click.native="toDetail(scope.row.lottery_id,scope.row.id)">详情</el-button>
                </template>
            </el-table-column>
        </el-table>
        <!--订单信息确认-->
        <div class="orders-info" v-if="orderVisible">
            <el-dialog
                    title="订单信息确认"
                    :visible.sync="orderVisible"
                    top="0"
                    :lock-scroll = "false"
                    width="460px"
            >
                <div class="order-info">
                    <div>
                        <span class="name">投注彩种:</span>
                        <span class="info">{{orders.title}}</span>
                    </div>
                    <div>
                        <span class="name">投注方式:</span>
                        <span class="info">参与合买</span>
                    </div>
                    <div>
                        <span class="name">认购份数:</span>
                        <span class="info"><b class="red">{{money}}</b> 份（共<b class="red">{{buyMoney(orders.total_share,orders.total_money,money)}}</b><?php echo $company['lottery_unit']; ?>)</span>
                    </div>
                    <div>
                        <span class="name">账户余额:</span>
                        <span class="info"><em>{{formatMoney}}</em> <?php echo $company['lottery_unit']; ?></span>
                    </div>
                </div>
                <div class="tips tc" style="font-size: 14px;color: #333" v-if="accountMoney < buyMoney(orders.total_share,orders.total_money,money)">
                    您的<b class="red">账户余额不足</b>，请先充值!
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="orderVisible = false">取消</el-button>
                    <el-button type="primary" v-if="accountMoney < buyMoney(orders.total_share,orders.total_money,money)" @click="goPay">立即充值</el-button>
                    <el-button type="primary" v-else @click="submitOrder">确定购买</el-button>
                </span>
            </el-dialog>
        </div>
        <!--大转盘-->
        <div class="dzp-dialog__wrapper" v-if="dzpVisible && dzpNum"  @click.self="dzpVisible = false">
            <div class="dzp-cont">
                <div class="close tr"><a @click="dzpVisible = false"><img src="/static/vipweb/images/dzp_close.png" alt=""></a></div>
                <div class="num tc">今日剩余抽奖次数：{{dzpNum}}次</div>
                <div class="btn tc"><a href="<?php echo url('web/game/index', 'ext=dzp'); ?>"><img src="/static/vipweb/images/dzp_btn.png" alt=""></a></div>
                <div class="checkbox"><el-checkbox v-model="checkShow" @change="set">今天不再显示</el-checkbox></div>
            </div>
        </div>
        <!--大转盘 end-->

    </div>
</template>
<!--合买推荐 end-->
<!--购买-->
<template id="buyInput">
    <div>
        <el-input size="mini"  placeholder="金额" v-model="money" @blur="checkMoney" class="input-short" style="display: inline-block;width: 50px" :controls="false"></el-input>份
        <el-button type="danger" size="mini" @click="orderInfo">购买</el-button>
    </div>
</template>
<style type="text/css">
    .dzp-dialog__wrapper{position: fixed;top:0;left: 0;display: flex;align-items: center;z-index: 20000;width: 100%;height: 100%;background-color: rgba(0, 0, 0, 0.71);}
    .game-dzp{flex-direction: column;}
    .dzp-cont{width: 540px;height: 703px;background: url("/static/vipweb/images/bg_dzp.png") no-repeat left top;margin: 0 auto}
    .num{padding-top: 362px;font-size: 15px;}
    .btn{padding-top: 15px;}
    .checkbox{margin-left: 55%;}
    .checkbox .el-checkbox{color: #dadada}
    .checkbox .el-checkbox__input.is-checked+.el-checkbox__label{color: #dadada}
    .el-table th>.cell{
        display: flex;
        align-items: center;
    }
</style>
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
<script type="text/javascript">
    "use strict";

    $(function () {
        Vue.component('buy-input', {
            template: '#buyInput',
            props: ['item'],
            data: function() {
                return {
                    money: 1
                };
            },

            methods: {
                //监听购买金额
                checkMoney: function() {
                    if (this.money > parseInt(this.item.ure)) {
                        this.money = parseInt(this.item.ure);
                    } else if (this.money <= 0) {
                        this.money = 1;
                    } else {
                        this.money = parseInt(this.money);
                    }
                },
                //订单信息确认
                orderInfo: function() {
                    this.$emit('orders', [this.item, this.money]);
                }
            }
        });
        Vue.component('jion-all', {
            template: '#jionAll',
            props: ['url', 'tabs','noticeVisble'],
            data: function() {
                return {
                    isGain: parseInt('<?php echo $system['isGain']; ?>'), //提成是否开启
                    jionData: [],
                    proOptions: [//进度
                        { value: '0', label: '全部订单' }, { value: '1', label: '未满员' }, { value: '2', label: '已满员' }],
                    moneyOptions: [//方案金额
                        { value: '0', label: '不限金额' }, { value: '1', label: '100以下' }, { value: '2', label: '101-500' }, { value: '3', label: '500-1000' }, { value: '4', label: '1000以上' }],
                    tcOptions: [//佣金
                        { value: '0', label: '不限佣金' }, { value: '1', label: '=0%' }, { value: '2', label: '<=1%' }, { value: '3', label: '<=2%' }, { value: '4', label: '<=3%' }, { value: '5', label: '<=4%' }, { value: '6', label: '<=5%' }, { value: '7', label: '<=6%' }, { value: '8', label: '<=7%' }, { value: '9', label: '<=8%' }, { value: '10', label: '<=9%' }, { value: '11', label: '<=10%' }],
                    search: { //搜索
                        progress: '',
                        money: '',
                        tc: ''
                    },
                    user: '',
                    sort: '', //排序条件
                    orderVisible: false,
                    loginVisible: false, //登录弹窗
                    nickname: '<?php if(isset($user['sid'])): ?> <?php echo $user['nickname']; endif; ?>', //登录昵称
                    accountMoney: '<?php echo $user['money']; ?>', //账户余额
                    loginType: -1, //判断登录后应执行的操作
                    orders: [], //订单数据
                    money: '', //购买金额

                    dzpNum: 0, //剩余抽奖次数
                    dzpVisible: false, //是否显示
                    checkShow: false //是否显示
                };
            },

            computed: {
                isLogin: function() {
                    if (this.nickname.length) {
                        return true;
                    } else {
                        return false;
                    }
                },
                //账户余额显示格式化
                formatMoney: function (_formatMoney) {
                    function formatMoney() {
                        return _formatMoney.apply(this, arguments);
                    }

                    formatMoney.toString = function () {
                        return _formatMoney.toString();
                    };

                    return formatMoney;
                }(function () {
                    return formatMoney(this.accountMoney);
                }),
                isIndexOf: function() {
                    if (this.url.indexOf("?") != -1) {
                        return "&";
                    } else {
                        return "?";
                    }
                },
                uMoney: function() {
                    if (this.search.money > 0) {
                        return this.isIndexOf + 'money=' + this.search.money;
                    } else {
                        return '';
                    }
                },
                uGain: function() {
                    if (this.search.tc > 0) {
                        return this.isIndexOf + 'gain=' + this.search.tc;
                    } else {
                        return '';
                    }
                },
                uWords: function() {
                    if (this.user.length) {
                        return this.isIndexOf + 'words=' + this.user;
                    } else {
                        return '';
                    }
                },
                uSort: function() {
                    if (this.sort.length) {
                        return this.isIndexOf + this.sort;
                    } else {
                        return '';
                    }
                }
            },
            watch: {
                url: function() {
                    var _this = this;
                    $.get(this.url, function (res) {
                        _this.$set(_this, 'jionData', res.data);
                    });
                },
                sort: function() {
                    this.doSearch();
                },
                noticeVisble:function (val) {
                    if(!val){
                        var _this = this
                        setTimeout(function () {
                            // 判断是否登录
                            if (_this.isLogin && !cookie.getCookie('dzpShow')) {
                                _this.getDzpInfo();
                            }
                        },500)
                    }
                }
            },
            methods: {
                //计算每份金额
                getPerMoney:function (totalShare,totalMoney) {
                      return accDiv(totalMoney,totalShare,3) ? accDiv(totalMoney,totalShare,3) : '1'
                },
                //认购金额计算
                buyMoney:function (totalShare,totalMoney,buyShare) {
                    var perMoney = accDiv(totalMoney,totalShare,3) ? accDiv(totalMoney,totalShare,3) : '1'
                    return accMul(perMoney,buyShare,3)
                },
                //按条件查询
                doSearch: function() {
                    this.$emit('change-url', this.uMoney + this.uGain + this.uWords + this.uSort);
                },
                //恢复默认
                recover: function() {
                    this.search.money = this.moneyOptions[0].value;
                    this.search.tc = this.tcOptions[0].value;
                    this.user = '';
                    this.doSearch();
                },
                //排序
                sortChange: function(column, prop, order) {
                    if (column.prop == null) {
                        this.sort = '';
                    } else {
                        var s;
                        if (column.order == 'descending') {
                            s = 0; //降序
                        } else {
                            s = 1; //升序
                        }
                        this.sort = '&type=' + column.prop + '&sort=' + s;
                    }
                },
                //跳转至详情页
                toDetail: function(orderid, id) {
                    var url = '<?php echo url("./orders"); ?>';
                    window.open(url + '?lottery_id=' + orderid + '&id=' + id);
                },
                //刷新
                refresh: function() {
                    var _this2 = this;

                    $.get(this.url, function (res) {
                        _this2.$set(_this2, 'jionData', res.data);
                    });
                },
                //订单信息确认 判断是否登录
                orderInfo: function(v) {
                    var _this3 = this;

                    this.orders = v[0];
                    this.money = v[1];
                    $.get('<?php echo url("login/checkLogin"); ?>', function (res) {
                        if (!res.err) {
                            _this3.nickname = res.nickname;
                            _this3.accountMoney = res.money;
                            _this3.orderVisible = true;
                        } else {
                            _this3.loginVisible = true;
                            _this3.loginType = 0;
                        }
                    });
                },
                //登录成功
                loginSuccess: function(v) {
                    var _this4 = this;

                    this.nickname = v[0];
                    this.accountMoney = v[1];
                    this.loginVisible = false;
                    if (this.loginType == 0) {
                        setTimeout(function () {
                            _this4.orderVisible = true;
                            _this4.loginType = -1;
                        }, 500);
                    } else {
                        this.getDzpInfo();
                    }
                },
                //退出登录
                loginOut: function() {
                    $.get('<?php echo url("login/logout"); ?>', function () {
                        window.location.href = '/web/login/index';
                    });
                },
                //刷新余额
                refreshMoney:function () {
                    var _this = this;
                    _this.$set(_this,'accountMoney','...')
                    $.get('/index/user/getinfo', function (res) {
                        _this.$set(_this,'accountMoney',res.data.money)
                    });
                },
                //确认购买
                submitOrder: function() {
                    var _this5 = this;
                    $.post('<?php echo url("orders/buyJoin"); ?>', {
                        buy_id: this.orders.id,
                        lottery_id: this.orders.lottery_id,
                        money: this.money
                    }, function (res) {
                        _this5.orderVisible = false;
                        if (!res.err) {
                            setTimeout(function () {
                                _this5.$alert(res.msg, '提示', {
                                    confirmButtonText: '确定',
                                    type: 'success',
                                    center: true,
                                    lockScroll: false
                                });
                                _this5.accountMoney = _this5.accountMoney - _this5.money;
                            }, 300);
                        } else {
                            _this5.$alert(res.msg, '提示', {
                                confirmButtonText: '确定',
                                type: 'warning',
                                center: true,
                                lockScroll: false
                            });
                        }
                        _this5.refresh();
                    });
                },
                //跳转至充值页面
                goPay: function() {
                    location.href = '<?php echo url("./pay"); ?>';
                },
                //今天不再显示设置
                set: function() {
                    if (!cookie.getCookie('dzpShow')) {
                        cookie.setCookie("dzpShow", this.checkShow);
                    } else {
                        cookie.removeCookie("dzpShow");
                    }
                },
                //幸运转盘信息
                getDzpInfo: function() {
                    var _this6 = this;
                    //获取会员转盘剩余抽奖次数
                    //是否弹出大转盘
                    if(Number("<?php echo $company['game_status']; ?>") && <?php echo $dzq_open; ?>){
                        $.get('/dzp/index/getCount', function (res) {
                            _this6.dzpVisible = true
                            _this6.$set(_this6, 'dzpNum', res.data);
                            //获取显示信息
                            if (cookie.getCookie('dzpShow')) {
                                _this6.dzpVisible = false;
                                _this6.checkShow = cookie.getCookie('dzpShow');
                            }
                        });
                    }
                }
            },
            created: function() {
                var _this7 = this;

                this.search.progress = this.proOptions[0].value;
                this.search.money = this.moneyOptions[0].value;
                this.search.tc = this.tcOptions[0].value;
                $.get(this.url, function (res) {
                    if (!res.err) {
                        _this7.$set(_this7, 'jionData', res.data);
                    }
                });
                if(!_this7.noticeVisble){
                    setTimeout(function () {
                        // 判断是否登录
                        if (_this7.isLogin && !cookie.getCookie('dzpShow')) {
                            _this7.getDzpInfo();
                        }
                    },500)
                }
            }
        });
        var wHeight = $(window).height()
        new Vue({
            el: '#app',
            data: function() {
                return {
                    urls: '<?php echo url("game"); ?>', //分彩种合买
                    gameid: '', //彩种id
                    search: '', //搜索条件
                    activeName: 'first',
                    jionNav: <?php echo $gameInfo; ?>,
                    tabs: -1,

                    notice_popup_open: Number('<?php echo $company['notice_popup_open']; ?>'), //弹窗是否开启
                    noticeList: Number('<?php echo $company['notice_popup_open']; ?>') ? <?php echo $noticeList; ?> : [],//公告列表
                    noticeVisble: false,
                    cur:0,
                    contentH:wHeight - 360,
                    todayIsShow: false
                };
            },

            computed: {
                url: function() {
                    var words = this.search.length ? this.search : ''
                    var gameSearch = this.gameid ? '?gameid=' + this.gameid : ''
                    return this.urls + gameSearch + words;
                }
            },
            methods: {
                tab: function(n, action) {
                    this.tabs = n;
                    this.gameid = action;
                },
                changeUrl: function(val) {
                    this.search = val;
                },
                link: function(a, type) {
                    var b = a;
                    if (a.substr(0, 1) == '/') {
                        b = a.substr(1);
                    }
                    if (a.substr(0, 6) == '/game/') {
                        b = a.substr(6);
                    }
                    if (!type) {
                        window.open('<?php echo url("./game"); ?>?ext='+ b); //休闲类游戏跳转路径
                    } else {
                        window.open('<?php echo url("./' + b + '"); ?>'); //开奖类游戏跳转路径
                    }
                },
                closeNotice:function () {
                    this.noticeVisble = false
                    if(this.todayIsShow){
                        cookie.setCookie('notice_show_close',1)
                    }
                }
            },
            created(){
                //获取显示信息
//                this.noticeVisble = true;
                if (!cookie.getCookie('notice_show_close') && this.notice_popup_open) {
                    this.noticeVisble = true;
                }
            }
        });
        //banner
        new Swiper('.pic-slide .swiper-container', {
            loop: true,
            autoplay: 4500,
            pagination: '.swiper-pagination',
            paginationClickable: true,
            autoplayDisableOnInteraction: false
        });
        // 游戏列表
        $("ul.game-list li").mouseover(function () {
            $(this).find('.r-mark').each(function () {
                $(this).stop().animate({ marginTop: '-150px' }, 100);
            });
        });
        $("ul.game-list li").mouseout(function () {
            $(this).find('.r-mark').each(function () {
                $(this).stop().animate({ marginTop: '0px' }, 100);
            });
        });
        //广告
        new Swiper('.ads-slide .swiper-container1', {
            effect: 'fade',
            loop: true,
            autoplay: 4000,
            pagination: '.swiper-pagination',
            paginationClickable: true,
            autoplayDisableOnInteraction: false
        });
        //中奖快讯滚动
        $('.myscroll').myScroll({
            speed: 50, //数值越大，速度越慢
            rowHeight: 32 //li的高度
        });
    });
</script>
