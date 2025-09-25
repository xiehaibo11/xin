<!--重置安全密码-->
<template>
    <div>
        <div class="steps">
            <span :class="{'active':steps >= 1}"></span>
            <span :class="{'active':steps >= 2}"></span>
            <span :class="{'active':steps >= 3}"></span>
        </div>
        <!--步骤1 设置新安全密码-->
        <div class="step1" v-if="steps == 1">
            <div class="input-title">设置新安全密码</div>
            <div>
                <mt-field label="新密码" placeholder="请输入新安全密码(至少6位)"  v-model="password1" type="password"></mt-field>
                <mt-field label="重复密码" placeholder="请再次输入新安全密码(至少6位)"  v-model="password2" type="password"></mt-field>
            </div>
            <div class="btn-box">
                <mt-button @click.native="toNext" type="danger" size="large">
                    下一步
                </mt-button>
            </div>
        </div>
        <!--步骤2 选择验证方式-->
        <div class="step2" v-if="steps == 2">
            <div class="input-title">选择安全验证方式</div>
            <div class="chose detail">
                <mt-cell title="验证原安全密码" label="能记住原安全密码可选此方式" is-link @click.native="choseWay(1)">
                    <i slot="icon" class="iconfont icon-mima2"></i>
                </mt-cell>
                <mt-cell title="验证已绑定手机" label="已绑定手机且该手机能接收短信" is-link v-if="isYzm" @click.native="choseWay(2)">
                    <i slot="icon" class="iconfont icon-ai-bind-cel"></i>
                </mt-cell>
                <mt-cell title="验证已绑定邮箱" label="已绑定邮箱且该邮箱能接收邮件" is-link v-if="isEmail" @click.native="choseWay(3)">
                    <i slot="icon" class="iconfont icon-mail"></i>
                </mt-cell>
            </div>
        </div>
        <!--步骤3 安全验证-->
        <div class="step3" v-if="steps == 3">
            <!--验证原安全密码-->
            <div v-if="checkWay == 1">
                <div class="input-title">安全验证</div>
                <div>
                    <mt-field label="原密码" placeholder="请输入原安全密码" type="password"  v-model="oldPwd"></mt-field>
                </div>
            </div>
            <!--验证手机号-->
            <div v-if="checkWay == 2">
                <div class="input-title">安全验证</div>
                <safe-check url="/web/user/sendSafePwdMsg" v-model="telYzm" :check-way="1"></safe-check>
            </div>
            <!--验证邮箱-->
            <div v-if="checkWay == 3">
                <div class="input-title">安全验证</div>
                <safe-check url="/web/user/sendSafePwdEmail" v-model="emailYzm" :check-way="2"></safe-check>
            </div>

            <div class="btn-box" v-if="steps == 3">
                <mt-button @click.native="sumbit" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    确认重置
                </mt-button>
            </div>
            <div class="tc" v-if="steps == 3"><a class="link f-sm" style="text-decoration: underline" @click="steps = 2">更换验证方式</a></div>
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
                checkWay: 0, //验证方式
                loading:false,
                steps:1,

                password1:'',
                password2:'',

                oldPwd:'',
                telYzm:'',
                emailYzm:'',
            }
        },
        computed:{
            lockTel(){
                return this.$store.state.lockInfo.tel !== 1 ? true : false //是否绑定手机号
            },
            lockEmail(){
                return this.$store.state.lockInfo.email !== 1 ? true : false //是否绑定邮箱
            },
            isYzm(){
                return Number(this.$store.state.setting.tel_checked) ? true: false //是否开启短信接口
            },
            isEmail(){
                return Number(this.$store.state.setting.email_checked) ? true: false //是否开启邮箱验证
            },
            isChose(){
                return this.isYzm || this.isEmail ? true : false
            }
        },
        methods:{
            toNext(){ //设置新安全密码下一步
                if(!this.password1 || !this.password2){
                    this.$toast('安全密码不能为空！')
                    return
                }
                if(this.password1.length < 6 || this.password2.length < 6){
                    this.$toast('安全密码长度要求为（至少6位）！')
                    return
                }
                if(this.password1 !== this.password2){
                    this.$toast('两次输入的安全密码不相同！')
                    return
                }
                this.steps = 2
            },
            checkTel(){
                if(this.lockTel){
                    this.checkWay = 2
                    this.steps = 3
                }else {
                    this.$messagebox.confirm(
                        '您还未绑定手机，立即去绑定?'
                    ).then(()=>{
                        this.$router.push({
                            path:'/setTel'
                        })
                    }).catch((err)=>{

                    });
                }
            },
            checkEmail(){
                if(this.lockEmail){
                    this.checkWay = 3
                    this.steps = 3
                }else {
                    this.$messagebox.confirm(
                        '您还未绑定邮箱，立即去绑定?'
                    ).then(()=>{
                        this.$router.push({
                            path:'/setEmail'
                        })
                    }).catch((err)=>{

                    });
                }
            },
            choseWay(n){
                if(n == 1){
                    this.checkWay = 1;
                    this.steps = 3
                }
              if(n == 2){
                  this.checkTel();
              }
              if(n == 3){
                  this.checkEmail();
              }
            },
            sumbit(){
                this.loading =true
                let postUrl,data;
                if(this.checkWay == 1){
                    postUrl = '/web/user/changeSafePwdByPwd'
                    data={
                        oldpwd: this.oldPwd,
                        password: this.password1,
                        password2: this.password2,
                    }
                }else if(this.checkWay == 2){
                    postUrl = '/web/user/changeSafePwdByTel'
                    data={
                        yzm: this.telYzm,
                        password: this.password1,
                        password2: this.password2,
                    }
                }else if(this.checkWay == 3){
                    postUrl = '/web/user/changeSafePwdByEmail'
                    data ={
                        yzm: this.emailYzm,
                        password: this.password1,
                        password2: this.password2,
                    }
                }
                this.$axios.post(postUrl,data).then(({data}) =>{
                    if(!data.err){
                        setTimeout(()=>{
                            this.$router.goBack(-1);
                        },1000)
                    }else if(data.err == 1) {
                        setTimeout(()=>{
                            this.steps = 1
                        },1000)
                    }
                    this.$toast({
                        message: data.msg,
                        duration: 1000
                    });
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
</style>
