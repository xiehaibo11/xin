<template>
    <div>
        <div class="form">
            <div class="tc"><span class="way-icon"><i class="iconfont icon-yonghu" slot="icon"></i></span></div>
            <div>
                <mt-field label="用户名" placeholder="请输入用户名/手机号/邮箱" v-model="username" @keyup.enter.native="sumbit"></mt-field>
                <mt-field label="密码" placeholder="请输入密码" v-model="password" type="password" @keyup.enter.native="sumbit"></mt-field>
            </div>
            <div class="btn-box">
                <mt-button @click.native="sumbit" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    登 录
                </mt-button>
                <div class="flex-box" style="margin-top: 5px">
                    <div class="f-sm flex-box c-2" @click="remember = !remember"><span class="input-checkbox" :class="{'checkbox-checked': remember}"></span>记住密码,一周内免登陆</div>
                    <p class="tr f-sm"><router-link to="/findpwd">忘记密码?</router-link></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                username:'',
                password:'',
                loading:false,
                remember: false
            }
        },
        computed:{
            title(){
                return this.$route.meta.title
            },
            loginWay(){
                return this.$store.state.setting.login_way
            },
            logo(){
                return this.$store.state.setting.logo_url
            },
            is_reg(){
                return this.$store.state.setting.is_reg
            },
        },
        methods:{
            back(){
                this.$router.isBack = true
                this.$router.replace({
                    path:'/login',
                    query: {
                        redirect: this.$route.query.redirect
                    }
                })
            },
            toReg(){
                this.$router.push({
                    path:'/reg',
                    query:{
                        redirect: this.$route.query.redirect
                    }
                })
            },
            //提交
            sumbit(){
                if(!this.username){
                    this.$toast('用户名不能为空');
                    return;
                }
                if(!this.password) {
                    this.$toast('密码不能为空');
                    return;
                }
                this.loading = true
                this.$axios.post('/index/login/loginbyname',{
                    username: this.username,
                    password: this.password,
                    remember : this.remember ? 1 : 0
                }).then(({data}) =>{
                    if(!data.err){
//                        this.$store.state.bAuth = 1;
                        this.$store.dispatch('getUserInfo')
                        this.$store.dispatch('getLockInfo'); //获取账号绑定信息
                        this.$store.dispatch('getTrueInfo'); //获取实名制信息
                        this.$store.dispatch('getSignInfo'); //获取签到信息
                        this.$store.dispatch('getSystemMsg');
                        this.$store.dispatch('getRechargeList'); //获取充值配置
                        if(this.remember){//写入本地存储
                            let cookieVal = this.$base.getCookie('has_login')
                            let nowtamp = new Date().getTime();
                            let loginToken = {
                                timeTemp: nowtamp, //写入时间
                                token : cookieVal
                            }
                            localStorage.setItem('loginToken',JSON.stringify(loginToken))
                        }
                        setTimeout(()=>{
                            var redirect = this.$route.query.redirect
                            if(redirect){
                                this.$router.replace({
                                    path:redirect
                                })
                            }else {
                                this.$router.replace({
                                    path:'/'
                                })
                            }
                        },1000)
                        this.$toast({
                            message: '登录成功',
                            iconClass: 'iconfont icon-gou-copy',
                            duration: 1000
                        });
                    }else {
                        this.$toast({
                            message: data.msg,
                            duration: 1000
                        });
                    }
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                })
            }
        },
        created(){
//            this.$store.state.bAuth = false
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss" type="text/scss">
    .btn-box{
        padding: 20px 15px;
    }
    .way-icon{
        display: inline-block;
        width: 50px;
        height: 50px;
        line-height: 50px;
        background-color:#ef4f4f;
        border-radius: 50%;
        margin: 15px 0;
        i{
            font-size: 20px;
            color: #ffffff;
        }
    }
</style>
