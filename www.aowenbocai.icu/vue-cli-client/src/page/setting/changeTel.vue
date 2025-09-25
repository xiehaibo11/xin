<!--修改手机-->
<template>
    <div>
        <div class="steps" v-if="isChose">
            <span :class="{'active':steps >= 1}"></span>
            <span :class="{'active':steps >= 2}"></span>
        </div>
        <!--步骤1 安全验证-->
        <div class="step1" v-if="steps == 1">
            <!--验证手机号-->
            <div v-show="checkWay == 1">
                <div class="input-title">安全验证</div>
                <safe-check url="/index/user/sendSmsEdit" v-model="telYzm" :check-way="1"></safe-check>
            </div>
            <!--验证邮箱-->
            <div v-show="checkWay == 2">
                <div class="input-title">安全验证</div>
                <safe-check url="/web/user/sendTelEmail" v-model="emailYzm" :check-way="2"></safe-check>
            </div>
            <div class="btn-box">
                <mt-button @click.native="toNext" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    下一步
                </mt-button>
            </div>
            <div class="tc">
                <a class="link f-sm" v-if="checkWay == 1 && isEmail" style="text-decoration: underline" @click="checkEmail">使用已绑定邮箱进行验证</a>
                <a class="link f-sm" v-if="checkWay == 2 && isYzm"  style="text-decoration: underline" @click="checkTel">使用已绑定手机进行验证</a>
            </div>
        </div>
        <!--步骤2 修改手机-->
        <div class="step2" v-if="steps == 2">
            <div class="input-title">重置手机</div>
            <get-sms :get-way="1" url="/index/user/sendSmsChange" v-model="options"></get-sms><!--重置手机-->
            <div class="btn-box">
                <mt-button @click.native="sumbit" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    确认重置
                </mt-button>
            </div>
        </div>
    </div>
</template>

<script>
    import SafeCheck from 'components/user/SafeCheck.vue'
    import GetSms from 'components/user/GetSms.vue'
    export default {
        components:{
            SafeCheck,
            GetSms
        },
        data () {
            return {
                checkWay:Number(this.$store.state.setting.tel_checked) ? 1 : 2, //验证方式 默认短信验证
                loading:false,
                steps:Number(this.$store.state.setting.tel_checked) || Number(this.$store.state.setting.email_checked) ? 1 : 2,
                options:{
                    yzm:'',
                    num:''//验证码值
                }, //新手机号
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
                return this.isYzm || this.isEmail ? true :false
            }
        },
        methods:{
            toNext(){ //下一步
                let yzm = this.checkWay == 1 ? this.telYzm : this.emailYzm;
                if(!yzm){
                    this.$toast('验证码不能为空！')
                    return
                }
                this.loading = true
                this.$axios.post('/web/user/checkTelEmail',{
                    changeWay : this.checkWay,
                    yzm : yzm
                }).then(({data}) =>{
                    if(!data.err){
                       this.steps = 2
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
            checkTel(){
                if(this.lockTel){
                    this.checkWay = 1
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
                    this.checkWay = 2
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
            //重置手机
            sumbit(){
                this.loading =true
                let data={
                    tel: this.options.num,
                    yzm: this.options.yzm
                }
                this.$axios.post('/index/user/writePhone',data).then(({data}) =>{
                    if(!data.err){
                        setTimeout(()=>{
                            this.$router.goBack(-1);
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
