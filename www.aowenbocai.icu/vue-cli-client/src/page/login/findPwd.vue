<!--找回密码-->
<template>
    <div>
        <div class="steps">
            <span :class="{'active':steps >= 1}"></span>
            <span :class="{'active':steps >= 2}"></span>
            <span :class="{'active':steps >= 3}"></span>
        </div>
        <!--步骤1 验证账号-->
        <div class="step1" v-if="steps == 1">
            <div class="input-title">输入您需要找回的账号</div>
            <mt-field  placeholder="请输入用户名/手机号/邮箱"  v-model="username"></mt-field>
            <div class="btn-box">
                <mt-button @click.native="checkUsername" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    下一步
                </mt-button>
            </div>
        </div>
        <!--步骤2 安全验证-->
        <div class="step2 relative" v-if="steps == 2">
            <!--选择安全验证码方式-->
            <div class="input-title">安全验证</div>
            <div v-if="doChose == -1" class="tc mt c-2" style="padding: 0 20px;line-height: 1.8">该账号未绑定手机或邮箱，无法找回密码，请联系客服电话：{{company_tel}}</div>
            <div  v-if="doChose == 0 && changeWay == 0">
                <mt-cell title="验证已绑定手机" label="已绑定手机且该手机能接收短信" is-link @click.native="changeWay = 1">
                    <i slot="icon" class="iconfont icon-ai-bind-cel"></i>
                </mt-cell>
                <mt-cell title="验证已绑定邮箱" label="已绑定邮箱且该邮箱能接收邮件" is-link @click.native="changeWay = 2">
                    <i slot="icon" class="iconfont icon-mail"></i>
                </mt-cell>
            </div>
            <a v-if="doChose == 0" style="text-decoration: underline" class="link f-sm change-way-link" @click="changeWay = 0">更换验证方式</a>
            <template v-if="doChose == 1 || changeWay == 1">
                <safe-check url="/index/findpwd/sendTelMsg" v-model="telYzm" :check-way="1"></safe-check>
            </template>
            <template v-if="doChose == 2 || changeWay == 2">
                <safe-check url="/index/findpwd/sendEmailMsg" v-model="emailYzm" :check-way="2"></safe-check>
            </template>
            <div class="btn-box" v-if="doChose !== -1">
                <mt-button @click.native="checkYzm" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    下一步
                </mt-button>
            </div>
        </div>
        <!--步骤3 设置新密码-->
        <div class="step3" v-if="steps == 3">
            <div class="input-title">重置登录密码</div>
            <mt-field label="新密码" placeholder="请输入新密码" type="password" v-model="pwd"></mt-field>
            <mt-field label="确认密码" placeholder="请再次输入新密码" type="password" v-model="pwd2"></mt-field>
            <div class="btn-box">
                <mt-button @click.native="sumbit" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    保存
                </mt-button>
            </div>
        </div>
    </div>
</template>

<script>
    import SafeCheck from 'components/user/SafeCheck.vue'
    export default {
        components:{
            SafeCheck
        },
        data () {
            return {
                username : '',

                loading:false,
                steps:1,

                doChose: 0, //验证选项是否显示
                tel:'', //已绑定的手机号
                email:'', //已绑定的邮箱
                changeWay:0,

                telYzm:'',
                emailYzm:'',

                pwd : '',
                pwd2 : '',
            }
        },
        computed:{
            company_tel(){
                return this.$store.state.setting.company_tel
            }
        },
        methods:{
            //验证账号信息
            checkUsername(){
                if(!this.username.length){
                    this.$toast('请输入您要找回的账号！')
                    return
                }
                this.loading = true;
                this.$axios.post('/index/findpwd/checkName',{
                    username: this.username
                }).then(({data}) =>{
                    if(!data.err){
                        this.steps = 2
                        this.tel = data.data.tel ? data.data.tel : '';
                        this.email = data.data.email ? data.data.email : '';
                        if (data.data.tel && data.data.email) {
                            this.doChose = 0; //手机、邮箱都可验证
                        } else if (data.data.tel && !data.data.email) {
                            this.doChose = 1; //手机验证
                            this.changeWay =1
                        } else if (!data.data.tel && data.data.email) {
                            this.doChose = 2; //邮箱验证
                            this.changeWay = 2
                        } else {
                            this.doChose = -1; //无法验证
                        }
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
            },
            //验证安全码
            checkYzm(){
                let yzm = this.changeWay == 1 ? this.telYzm : this.emailYzm;
                if(!yzm){
                    this.$toast('验证码不能为空！')
                    return
                }
                let data
                if(this.changeWay == 1){
                    data = {
                        changeWay:this.changeWay,
                        yzm:this.telYzm
                    }
                }
                if(this.changeWay == 2){
                    data = {
                        changeWay:this.changeWay,
                        yzm:this.emailYzm
                    }
                }
                this.loading = true
                this.$axios.post('/index/findpwd/checkYzm',data).then(({data}) =>{
                    if(!data.err){
                       this.steps = 3
                    }else{
                        this.$toast({
                            message: data.msg,
                            duration: 1000
                        });
                    }
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                })
            },
            //重置密码
            sumbit(){
                if(!this.pwd.length){
                    this.$toast('请输入新密码！')
                    return
                }
                if(!this.pwd2.length){
                    this.$toast('请确认新密码！')
                    return
                }
                if(this.pwd !== this.pwd2){
                    this.$toast('两次输入的密码不相同！')
                    return
                }
                this.loading =true
                this.$axios.post('/index/findpwd/resetPwd',{
                    pwd:this.pwd,
                    pwd2:this.pwd2
                }).then(({data}) =>{
                    if(!data.err){
                        this.$messagebox({
                            title: '提示',
                            message: data.msg,
                            confirmButtonText:'立即去登录？'
                        }).then(()=>{
                            this.$router.push({
                                path:'/login'
                            })
                        }).catch((err)=>{

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
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .relative{
        position: relative;
        .change-way-link{
            position: absolute;
            right: 10px;
            top: 0px;
            display: inline-block;
            height: 30px;
            line-height: 30px;
        }
    }
</style>
