<template>
    <div>
        <div v-if="trueState">
            <div class="input-title">您的实名认证信息如下<em class="f-mini org">（如需重新认证，请联系客服）</em></div>
            <div>
                <mt-field label="真实姓名" placeholder="须与身份证保持一致" v-model= "isName" type="text" disabled></mt-field>
                <mt-field label="身份证号" placeholder="仅支持大陆第二代身份证" v-model = "isNum" type="text" disabled></mt-field>
            </div>
        </div>
        <div class="form" v-else>
            <div class="tips f-mini" style="font-size: 12px">温馨提示：实名信息是领取大奖的重要核对依据，请填写真实信息，填写后不可修改！</div>
            <div class="input-title">填写真实信息</div>
            <div>
                <mt-field label="真实姓名" placeholder="须与身份证保持一致" v-model = "name" type="text" v-if="isName==0"></mt-field>
                <mt-field label="真实姓名" placeholder="须与身份证保持一致" v-model = "isName" type="text" v-else disabled></mt-field>
                <mt-field label="身份证号" placeholder="仅支持大陆第二代身份证" v-model = "num" type="text"></mt-field>
            </div>
            <!--选择安全验证码方式-->
            <template>
                <div class="relative">
                    <div class="input-title" v-if="isChose">安全验证</div>
                    <div class="" v-if="isChose && changeWay == 0">
                        <mt-cell title="验证已绑定手机" label="已绑定手机且该手机能接收短信" is-link v-if="isYzm" @click.native="checkTel">
                            <i slot="icon" class="iconfont icon-ai-bind-cel"></i>
                        </mt-cell>
                        <mt-cell title="验证已绑定邮箱" label="已绑定邮箱且该邮箱能接收邮件" is-link v-if="isEmail" @click.native="checkEmail">
                            <i slot="icon" class="iconfont icon-mail"></i>
                        </mt-cell>
                    </div>
                    <a v-if="changeWay" style="text-decoration: underline" class="link f-sm change-way-link" @click="changeWay = 0">更换验证方式</a>
                    <template >
                        <safe-check url="/web/user/sendTrueMsg" v-show="changeWay == 1" v-model="telYzm" :check-way="1"></safe-check>
                    </template>
                    <template>
                        <safe-check url="/web/user/sendIdCardEmail" v-show="changeWay == 2" v-model="emailYzm" :check-way="2"></safe-check>
                    </template>
                </div>
            </template>
            <div class="btn-box">
                <mt-button @click.native="sumbit" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    保 存
                </mt-button>
            </div>
        </div>
    </div>
</template>
<script>
    import SafeCheck from 'components/user/SafeCheck.vue'
    export default{
        data() {
            return {
                loading:false,
                name:'',
                num:'',
                telYzm:'',
                emailYzm:'',
                changeWay:0
            };
        },
        components:{
            SafeCheck
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
            },
            trueState(){ //实名认证状态
                return this.$store.state.lockInfo.is_num == 0 ? false : true
            },
            trueInfo(){ //实名认证信息
                return this.$store.state.lockInfo
            },
            //实名认证姓名
            isName(){
                return this.$store.state.lockInfo.is_name
            },
            //实名认证号码
            isNum(){
                return this.$store.state.lockInfo.is_num
            }
        },
        methods:{
            checkTel(){ //判断是否绑定手机
                if(!this.name && this.isName == 0){
                    this.$toast('姓名不能为空');
                    return;
                }
                if(!this.num) {
                    this.$toast('身份证号码不能为空');
                    return;
                }
                if(!this.$base.testIdNum(this.num)){
                    this.$toast('请输入正确的身份证号');
                    return;
                }
                if(this.lockTel){
                    this.changeWay = 1
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
                if(!this.name && this.isName == 0){
                    this.$toast('姓名不能为空');
                    return;
                }
                if(!this.num) {
                    this.$toast('身份证号码不能为空');
                    return;
                }
                if(!this.$base.testIdNum(this.num)){
                    this.$toast('请输入正确的身份证号');
                    return;
                }
                if(this.lockEmail){
                    this.changeWay = 2
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
            //提交
            sumbit(){
                if(!this.name && this.isName == 0){
                    this.$toast('姓名不能为空');
                    return;
                }
                if(!this.num) {
                    this.$toast('身份证号码不能为空');
                    return;
                }
                if(!this.$base.testIdNum(this.num)){
                    this.$toast('请输入正确的身份证号');
                    return;
                }
                if(this.isChose && !this.changeWay) {
                    this.$toast('请选择验证方式');
                    return;
                }else {
                    if(this.changeWay == 1){
                        if(!this.telYzm){
                            this.$toast('验证码不能为空');
                            return;
                        }
                    }
                    if(this.changeWay == 2){
                        if(!this.emailYzm){
                            this.$toast('验证码不能为空');
                            return;
                        }
                    }
                }
                this.loading = true
                let yzm
                if(!this.changeWay){
                    yzm = ''
                }else {
                    yzm = this.changeWay == 1 ? this.telYzm : this.emailYzm
                }
                let data = {
                    changeWay: this.changeWay,
                    yzm : yzm,
                    idnum: this.num
                }
                if(this.isName == 0){
                    data['idname'] = this.name
                }
                this.$axios.post('/web/user/trueName',data).then(({data}) =>{
                    if(!data.err){
//                        this.$store.dispatch('getTrueInfo')//更新用户实名认证信息
                        this.$store.dispatch('getLockInfo')//更新用户绑定信息
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
