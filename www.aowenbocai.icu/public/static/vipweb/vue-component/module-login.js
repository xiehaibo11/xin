
document.write("<script language=javascript src='/static/vipweb/vue-component/login-tel.js'><\/script>");
document.write("<script language=javascript src='/static/vipweb/vue-component/login-name.js'><\/script>");
$(function () {
    //登录方式
    Vue.component('module-login', {
        props: ['loginVisible', 'siteName', 'isLogin'],
        template:'<!--login start-->\
                 <div v-if="loginVisible">\
                     <el-dialog :title="siteName + \'账号登录\'" :visible.sync="loginVisible" :lock-scroll="false" :before-close="close" width="400px" top="0" class="login" >\
                        <div class="login-way">\
                            <div>\
                                <h4 v-if="login_tel">手机登录</h4>\
                                <h4 v-if="login_name">用户名登录</h4>\
                            </div>\
                            <div class="flex tr">\
                                <template v-if="tel_open && login_name">\
                                    <div class="login-tip" style="right: 48px">\
                                        <div class="poptip">\
                                            <div class="poptip-arrow">\
                                                <em></em>\
                                                <span></span>\
                                            </div>\
                                            <div class="poptip-content">\
                                               手机登录在这里\
                                            </div>\
                                        </div>\
                                    </div>\
                                    <span @click="changeWay(1)" class="login-icon"><i class="iconfont icon-bossdenglu-shoujidenglu"></i></span>\
                                </template>\
                                <template v-if="name_open && login_tel">\
                                    <div class="login-tip">\
                                        <div class="poptip">\
                                            <div class="poptip-arrow">\
                                                <em></em>\
                                                <span></span>\
                                            </div>\
                                            <div class="poptip-content">\
                                                用户名登录在这里\
                                            </div>\
                                        </div>\
                                    </div>\
                                    <span @click="changeWay(0)" class="login-icon"><i class="iconfont icon-diannaodenglu"></i></span>\
                                </template>\
                            </div>\
                        </div>\
                        <tel-login v-if="login_tel && tel_open" @success="closeDialog"></tel-login>\
                        <web-login v-if="login_name && name_open" @success="closeDialog"></web-login>\
                        <!--其他方式登录-->\
                        <div class="other flex-box">\
                            <i style="color: #50b674" class="iconfont icon-weixin"  v-if="wei_open"></i>\
                            <a href="/web/login/weilogin"  target="_blank"  v-if="wei_open">微信登录</a>\
                            <i style="color: #1296db" class="iconfont icon-qq" v-if="qq_open"></i>\
                           <a href="/web/Login/qq/" target="_blank" v-if="qq_open">QQ登录</a>\
                        </div>\
                        <div class="tr link">\
                            <a href="/web/findpwd" style="padding-right: 7px">忘记密码</a>\
                            <a href="/web/reg/index" v-if="is_reg">注册账号</a>\
                        </div>\
                    </el-dialog>\
                </div>\
                <!--login end-->',
        data: function data() {
            return {
                tel_open: 0, //手机登录是否开启
                name_open: 0, //普通登录是否开启
                is_reg: 0, //注册是否开启
                qq_open: 0, //QQ是否开启
                wei_open: 0, //微信是否开启

                login_tel: 0,
                login_name: 0
            };
        },

        methods: {
            changeWay: function changeWay(n) {
                //登录方式切换
                if (n) {
                    this.login_tel = 1;
                    this.login_name = 0;
                    return;
                }
                if (!n) {
                    this.login_tel = 0;
                    this.login_name = 1;
                }
            },
            closeDialog: function closeDialog(v) {
                //登录成功执行
                this.$emit('login-success', v);
            },
            close: function close() {
                this.$emit('close');
            }
        },
        created: function created() {
            var _this = this;
            if (!this.isLogin) {
                $.get('/index/login/getWays', function (res) {
                    if (res.data.common) {
                        _this.name_open = 1;
                        _this.login_name = 1;
                    } else {
                        _this.name_open = 0;
                        _this.login_name = 0;
                    }
                    if (res.data.tel) {
                        _this.tel_open = 1;
                        _this.login_tel = 1;
                    } else {
                        _this.tel_open = 0;
                        _this.login_tel = 0;
                    }
                    if (res.data.tel && res.data.common) {
                        _this.login_name = 1;
                        _this.login_tel = 0;
                    }
                    _this.qq_open = res.data.qq;
                    _this.wei_open = res.data.wxsao ? res.data.wx : 0;
                    _this.is_reg = res.is_reg;
                });
            }
        }
    });
    //顶部登录信息
    Vue.component('login-info', {
        props: ['siteName', 'nickname', 'msgNum', 'loginVisible', 'regUrl', 'myUrl','money'],
        template:'<div class="login-info">\
                     <module-login :login-visible="loginVisible" :site-name="siteName" :is-login="isLogin" @login-success="loginSuc" @close="closeDialog"></module-login>\
                     <template v-if="!isLogin">\
                            <a class="flex-box" @click="openDialog"><i class="iconfont icon-denglu"></i>登录</a>\
                            <em> | </em>\
                            <a  class="flex-box"  :href="regUrl"><i class="iconfont icon-zhuce"></i> 免费注册</a>\
                            <em> | </em>\
                            <a @click="openDialog">个人中心</a>\
                        </template>\
                   <template v-else>\
                        <span class="name"><a :href="myUrl">欢迎：<font class="red">{{nickname}}</font></a></span>\
                        <span class="money"><a :href="myUrl">余额：<font class="red">{{money}}</font></a> \
                        <i class="refresh el-icon-refresh" @click="refreshMoney"></i></span>\
                        <a @click="loginOut" style="padding-left: 5px">[退出]</a>\
                        <em> | </em>\
                        <a :href="myUrl">个人中心</a>\
                        <em> | </em>\
                        <a href="/web/user/my?ac=0-5">系统消息<el-badge v-if="msgNum > 0" :value="msgNum"></el-badge></a>\
                        <em> | </em>\
                        <a class="recharge" href="/web/pay">充值</a>\
                    </template>\
                </div>',
        data: function() {
            return {};
        },
        computed: {
            isLogin: function() {
                return this.nickname.length;
            }
        },
        methods: {
            loginSuc: function(v) {
                //登录成功执行
                this.$emit('login-success', v);
            },
            openDialog: function() {
                //打开登录弹窗
                this.$emit('open-dialog');
            },
            closeDialog: function() {
                //关闭登录弹窗
                this.$emit('close-dialog');
            },
            refreshMoney: function() {
                //刷新账号余额
                this.$emit('refresh');
            },
            loginOut: function() {
                //退出登录
                this.$emit('login-out');
            }
        }
    });
});
