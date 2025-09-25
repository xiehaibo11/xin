<template>
    <div class="form">
        <div style="margin-top: 15px;margin-bottom: 8px">
            <template v-if="codeStatus">
                <template v-if="this.$route.query.code">
                    <mt-field label="邀请码" placeholder="请输入邀请码" v-model="code" disabled> </mt-field>
                </template>
                <template v-else>
                    <mt-field label="邀请码" placeholder="请输入邀请码" v-model="code"> </mt-field>
                </template>
            </template>
            <mt-field label="用户名" placeholder="请输入3-25个字母或数字" v-model="username"> </mt-field>
            <mt-field label="用户昵称" placeholder="请输入2-10个不为纯数字的用户昵称" v-model="nickname"> </mt-field>
            <mt-field label="登录密码" placeholder="请输入密码（长度6-25）" v-model="pwd" type="password"></mt-field>
            <mt-field label="确认密码" placeholder="请再次输入密码（长度6-25）" v-model="pwd2" type="password"></mt-field>
            <mt-field label="QQ号码" placeholder="请输入QQ号码"  v-model="qq" v-if="regWay.common"></mt-field>
            <mt-field label="手机号" placeholder="请输入手机号"  v-model="tel" v-if="regWay.tel"></mt-field>
            <mt-field label="验证码" placeholder="请输入验证码" v-model="yzm" type="tel" v-if="regWay.tel && isYzm">
                <div @click="sendSms" class="btn-yzm" :class="yzmState ? 'abled': 'disabled'">{{text}}</div>
            </mt-field>
        </div>
        <div class="tc mt f-sm c-3">
            <input type="checkbox" v-model="check" checked> 我已阅读并同意<a class="link" @click="agreeVisible=true">《注册协议》</a>
        </div>
        <div class="btn-box">
            <mt-button @click.native="submit" type="danger" size="large">
                <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                立即注册
            </mt-button>
            <div class="mt-sm">
                <p class="tr flex f-sm">已有账号？<router-link to="/login">去登陆</router-link></p>
            </div>
        </div>
        <!--注册协议-->
        <mt-popup
            class="agree-poput"
            v-model="agreeVisible"
            position="right">
            <div>
                <mt-header title="注册协议">
                    <mt-button icon="back" slot="left" @click.native="agreeVisible = false">关闭</mt-button>
                </mt-header>
                <div v-html="user_web" class="service-cont"></div>
            </div>
        </mt-popup>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                agreeVisible:false,
                loading:false,
                text:'获取验证码',
                countDown:120,
                yzmState:false,

                tel: '',
                yzm: '',
                username: '',
                nickname: '',
                pwd: '',
                pwd2: '',
                check:true,
                code: this.$route.query.code || '',
                qq:''
            }
        },
        watch:{
            tel(val){
                if(val){
                    this.yzmState = true
                }
            }
        },
        computed:{
            isYzm(){
                return this.$store.state.setting.tel_checked==1 ? true: false //是否开启短信接口
            },
            regWay(){
                return this.$store.state.setting.reg_way
            },
            //注册协议
            user_web(){
                return this.$store.state.setting.user_web
            },
            //邀请码开启状态
            codeStatus(){
                return this.$store.state.setting.is_reg_code == 1 ? true : false
            }
        },
        methods:{
            //发送验证码
            sendSms(){
                let data;
                if(this.countDown != 120) return false;
                if(!this.$base.testTel(this.tel)){
                    this.$toast({
                        message: '请输入正确的手机号',
                        duration: 1500
                    });
                    return false;
                }
                this.yzmState = false;
                this.text = '发送中...';
                this.$axios.get('/index/reg/sendsms',{params:{
                    tel:this.tel
                }}).then(({data}) =>{
                    if(!data.err){
                        this.text = "重新发送("+this.countDown+"s)";
                        var interval = setInterval(()=>{
                            this.countDown = this.countDown - 1;
                            if(this.countDown == 0){
                                this.text = '获取验证码';
                                this.yzmState = true
                                this.countDown = 120;
                                clearInterval(interval);
                                return;
                            }
                            this.text = "重新发送("+this.countDown+"s)";
                        },1000);
                        this.$toast({
                            message: '发送成功',
                            duration: 1500
                        });
                    }else {
                        this.$toast({
                            message: data.msg,
                            duration: 1500
                        });
                        this.text = '获取验证码';
                        this.yzmState = true
                    }
                }).catch(function (error) {
                    console.log(error);
                })
            },
            //注册
            submit(){
                let regObj = {
                    1:{pattern:/^[a-zA-Z0-9]{3,25}$/ , message : '用户名只能由3-25位字母或数字组成'},
                    2:{pattern:/^[0-9]{2,10}$/ , message : '用户昵称必须由2-10个不为纯数字的组成'},
                    3:{pattern:/^\d+$/ , message : '请输入正确的验证码'},
                    4:{pattern:/^[1-9][0-9]{4,14}$/ , message : '请输入正确的QQ号码'},
                }
                if(this.codeStatus && !this.code.length){
                    this.$messagebox('提示','请输入邀请码!')
                    return
                }
                if(!this.username.length){
                    this.$messagebox('提示','请输入用户名!')
                    return
                }
                if(!regObj[1].pattern.test(this.username)){
                    this.$messagebox('提示',regObj[1].message)
                    return
                }
                if(!this.nickname.length){
                    this.$messagebox('提示','请输入用户昵称!')
                    return
                }
                if(regObj[2].pattern.test(this.nickname)){
                    this.$messagebox('提示',regObj[2].message)
                    return
                }
                if(!this.pwd.length){
                    this.$messagebox('提示','请输入登录密码!')
                    return
                }
                if(!this.pwd2.length){
                    this.$messagebox('提示','请确认登录密码!')
                    return
                }
                if(this.pwd !== this.pwd2){
                    this.$messagebox('提示','两次输入的密码不相同!')
                    return
                }
                if(this.regWay.common && !regObj[4].pattern.test(this.qq)){
                    this.$messagebox('提示',regObj[4].message)
                    return
                }
                if(this.regWay.tel && !this.$base.testTel(this.tel)){
                    this.$messagebox('提示','请输入正确的手机号!')
                    return
                }
                if(this.regWay.tel && this.isYzm && !this.yzm.length){
                    this.$messagebox('提示','请输入验证码!')
                    return
                }
                let data = {
                    username: this.username,
                    nickname: this.nickname,
                    password: this.pwd,
                    password2: this.pwd2
                };
                if(this.regWay.common){
                    data['qq'] = this.qq
                }
                if(this.regWay.tel){
                    data['tel'] = this.tel
                }
                if(this.regWay.tel && this.isYzm){
                    data['yzm'] = this.yzm
                }
                if(this.codeStatus){
                    data['code'] = this.code
                }
                if(!this.check){
                    this.$toast('请阅读并同意相关协议!')
                    return
                }
                this.loading = true
                this.$axios.post('/web/reg/reg',data).then(({data})=>{
                    if(!data.err){
                        this.$store.dispatch('getUserInfo')
                        this.$base.setCookie('bAuth',true)
                        this.$store.dispatch('getLockInfo'); //获取账号绑定信息
                        this.$store.dispatch('getTrueInfo'); //获取实名制信息
                        this.$store.state.bAuth = true;
                        setTimeout(()=>{
                            var redirect = this.$route.query.redirect
                            if(redirect){
                                this.$router.push({
                                    path:redirect
                                })
                            }else {
                                this.$router.push({
                                    path:'/'
                                })
                            }
                        },1000)
                        this.$toast({
                            message: '注册成功',
                            iconClass: 'iconfont icon-gou-copy',
                            duration: 1000
                        });
                    }else {
                        this.$messagebox('提示',data.msg)
                    }
                    this.loading = false
                }).catch((err)=>{

                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    input{
        outline: thin;
        -webkit-appearance: checkbox;
    }
</style>
