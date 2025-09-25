<!--选择安全验证码方式-->
<template>
    <div class="relative">
        <div class="input-title" v-if="isChose">安全验证</div>
        <div class="cell" v-if="isChose && changeWay == 0">
            <mt-cell title="验证已绑定手机" is-link v-if="isYzm" @click.native="checkTel">
                <i slot="icon" class="iconfont icon-shouji"></i>
            </mt-cell>
            <mt-cell title="验证已绑定邮箱" is-link v-if="isEmail" @click.native="checkEmail">
                <i slot="icon" class="iconfont icon-mail"></i>
            </mt-cell>
        </div>
        <a v-if="changeWay" style="text-decoration: underline" class="link f-sm change-way-link" @click="changeWay = 0">更换验证方式</a>
        <template >
            <safe-check :url="sendSmsUrl" v-show="changeWay == 1" :value="telYzm" :check-way="1" @change="change"></safe-check>
        </template>
        <template>
            <safe-check :url="sendEmailUrl" v-show="changeWay == 2" :value="emailYzm" :check-way="2" @change="changes"></safe-check>
        </template>
    </div>
</template>

<script>
    import SafeCheck from 'components/user/SafeCheck.vue'
    export default {
        name: 'choseCheckWay',
        components:{
            SafeCheck
        },
        props:{
            sendSmsUrl : { //发送短信地址
                type: String,
            },
            sendEmailUrl : { //发送邮件地址
                type: String,
            },
            telYzm:{},
            emailYzm:{},
            changeWay:{
                defalut:0
            },
        },
        data () {
            return {
//                telYzm : '',
//                emailYzm : '',
//                changeWay: 0, //验证方式选择 1 手机 2邮箱
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
                return this.$store.state.setting.tel_checked ? true: false //是否开启短信接口
            },
            isEmail(){
                return this.$store.state.setting.email_checked ? true: false //是否开启邮箱验证
            },
            isChose(){
                return this.isYzm || this.isEmail ? true : false
            }
        },
        watch:{
//            telYzm(val){
//                this.$emit('tel-yzm',val)
//            },
//            emailYzm(val){
//                this.$emit('email-yzm',val)
//            },
////            changeWay(val){
////                this.$emit('change-way',val)
////            }
        },
        methods:{
            change(emitVal){
                this.$emit('tel-yzm',emitVal)
            },
            changes(emitVal){
                this.$emit('email-yzm',emitVal)
            },
            checkTel(){ //判断是否绑定手机
                if(this.lockTel){
                    this.$emit('change-way',1)
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
            checkEmail(){ //判断是否绑定了邮箱
                if(this.lockEmail){
                    this.$emit('change-way',2)
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
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
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
