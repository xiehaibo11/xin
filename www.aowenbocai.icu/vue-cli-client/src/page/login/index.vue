<template>
    <div>
        <mt-header :title="title" class="border-bottom-1px">
            <template slot="left" v-if="isShow">
                <i @click="back" class="iconfont icon-zuojiantou-copy c-3" style="font-size: 26px"></i>
                <!--<mt-button @click.native="back" icon="back">取消</mt-button>-->
            </template>
            <div slot="right" class="flex-box" style="justify-content: flex-end;">
                <mt-button @click.native="toReg" v-if="is_reg">注册</mt-button>
                <router-link to="/onLine" tag="div" v-if="qqOnline">
                    <span class="f-sm tr" style="color: #f97622;margin-left: 10px">
                        <b class="iconfont icon-zaixiankefu" style="padding-right: 2px"></b><em>客服</em>
                    </span>
                </router-link>
            </div>
        </mt-header>
        <template v-if="loading">
            <template v-if="way == 1">
                <LoginByName/>
            </template>
            <template v-if="way == 2">
                <LoginByTel/>
            </template>
            <div class="tc chose-way">
                <a class="link" v-if="way == 2 && loginWay.common" @click="way = 1"><i class="iconfont icon-yonghu" slot="icon"></i> 账号登录</a>
                <a class="link" v-if="way == 1 && loginWay.tel" @click="way = 2"><i class="iconfont icon-shouji" slot="icon"></i> 短信登录</a>
            </div>

            <div class="other-login-way border-top-1px" v-if="loginWay.qq">
                <div class="tc">
                    <a :href="'/index/Qqlogin/toLogin?redirect='+ redirect">
                        <p class="login-icon">
                            <i class="iconfont icon-qq-copy" style="color: #03a9f4;font-size: 34px"></i>
                        </p>
                        <p class="f-mini tc c-3">QQ登录</p>
                    </a>
                </div>
            </div>
        </template>
        <div class="tc f-mini c-3 login-foot">
            <div class="card-pd flex-box-c logo tl">
                <img :src="logo" alt="">
            </div>
            <div>
                <p>抵制不良游戏 拒绝盗版游戏 注意自我保护 谨防上当受骗 </p>
                <p>适度游戏益脑 沉迷游戏伤身 合理安排时间 享受健康生活</p>
            </div>
            <div>本平台适合年满18周岁以上的用户使用，为了您的健康，请合理控制游戏时间。</div>
        </div>
    </div>
</template>

<script>
    import { Indicator } from 'mint-ui';
    import LoginByName from 'page/login/login-name.vue'
    import LoginByTel from 'page/login/login-tel.vue'
    export default {
        name: 'login',
        components:{
            LoginByName,
            LoginByTel
        },
        data () {
            return {
                isShows:'',
                way:this.$store.state.setting.login_way.common ? 1 : this.$store.state.setting.login_way.tel ? 2 : -1,
                loading: false
            }
        },
        computed:{
            //在线客服
            qqOnline(){
                return this.$store.state.setting.wap_online_qq
            },
            redirect(){
                return this.$route.query.redirect
            },
            title(){
                return this.$route.meta.title
            },
            setting(){
                return this.$store.state.setting
            },
            is_reg(){
                return this.$store.state.setting.is_reg
            },
            loginWay(){
                return this.$store.state.setting.login_way
            },
            isShow:{
                get(){
                    return this.$base.getCookie('login_in_status')==1 ? false : true
                },
                set(){

                }
            },
            logo(){
                return this.$store.state.setting.logo_url
            }
        },
        methods:{
            back(){
                this.$router.goBack(-1);//返回上一层
//                this.$store.state.bAuth = true
            },
            toReg(){
                this.$router.push({
                    path:'/reg',
                    query:{
                        redirect: this.redirect
                    }
                })
            }
        },
        activated(){
        },
        created(){
            //首次加载登录执行
            if(!this.$base.getCookie('login_first')){
                Indicator.open({
                    text: '加载中...',
                    spinnerType: 'fading-circle'
                });
                this.$axios('/index/moblie/getSetting').then(({data}) => {
                    this.$store.commit('setSystemSet', data.data);
                    this.$base.setCookie('login_first',1)
                    Indicator.close();
                    this.loading = true
                });
            }else {
                this.loading = true
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss" type="text/scss">
    .logo {
        padding: 5px 0;
    }
    .logo img{
        margin-top: 10px;
        max-height:30px;
        opacity: .5;
    }
    a.link{
        text-decoration: none;
    }
    .login-foot{
        position: absolute;
        width: 100%;
        bottom: 5px;
        left: 0;
        line-height: 1.4;
        font-size: 10px;
        color: #b9b9b9;
    }
    .other-login-way{
        margin:20px 15px 15px;
        overflow: hidden;
        a{
            display: inline-block;
            text-align: center;
            .login-icon{
                height: 34px;
                line-height: 34px;
                margin: 10px 0 0;
            }
        }
    }
    .mint-header {
        height: 50px;
        line-height: 50px;
        background-color: #fff;
        color: #333333;
    }
</style>
