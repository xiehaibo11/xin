<template>
    <div class="form">
        <div class="input-title">基本资料</div>
        <div>
            <mt-field label="用户ID" placeholder="用户ID" :value = "userid" type="text" disabled></mt-field>
            <mt-field label="账号" placeholder="账号" :value = "name" type="text" disabled></mt-field>
            <mt-field label="真实姓名" placeholder="真实姓名" v-model = "real_name" type="text" :disabled="idname.length ? true : false">
                <span class="red f-mini ml-sm" v-if="idname.length">如需修改，请联系客服</span>
            </mt-field>
            <mt-field label="昵称" placeholder="请输入昵称" v-model = "nickname" type="text"></mt-field>
            <a class="mint-cell"  @click="sheetVisible = true">
                <div class="mint-cell-wrapper">
                    <div class="mint-cell-title"><span class="mint-cell-text">性别</span></div>
                    <div class="mint-cell-value is-link">
                        <div class="c-1" v-if="sex == 1">男</div>
                        <div class="c-1" v-if="sex == 2">女</div>
                        <div class="c-1" v-if="sex == 3">保密</div>
                        <div class="c-4" v-if="sex !== 1 && sex !== 2 && sex !== 3">未设置</div>
                    </div>
                    <i class="mint-cell-allow-right"></i>
                </div>
            </a>
            <a class="mint-cell">
                <div class="mint-cell-wrapper">
                    <div class="mint-cell-title"><span class="mint-cell-text">生日</span></div>
                    <div class="mint-cell-value is-link flex">
                        <input type="date" v-model="birth">
                    </div>
                    <i class="mint-cell-allow-right"></i>
                </div>
            </a>
            <mt-field label="QQ号码" placeholder="请输入QQ号码" v-model = "qq" type="text"></mt-field>
            <mt-field label="签名" placeholder="请输入个性签名" v-model = "explan" type="text"></mt-field>
        </div>
        <mt-actionsheet
            :actions="actions"
            v-model="sheetVisible">
        </mt-actionsheet>
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
                    <safe-check url="/index/user/sendSmsInfo" v-show="changeWay == 1" v-model="telYzm" :check-way="1"></safe-check>
                </template>
                <template>
                    <safe-check url="/web/user/sendInfoEmail" v-show="changeWay == 2" v-model="emailYzm" :check-way="2"></safe-check>
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
</template>
<script>
    import SafeCheck from 'components/user/SafeCheck.vue'
    export default{
        data() {
            return {
                loading:false,
                telYzm:'',
                emailYzm:'',
                changeWay:0,
                sheetVisible:false,
                actions: [{
                    name: '男',
                    method: this.choseSex1
                }, {
                    name: '女',
                    method: this.choseSex2
                },{
                    name: '保密',
                    method: this.choseSex3
                    }],
                nickname: this.$store.state.userinfo.nickname || '',
                explan: this.$store.state.userinfo.explan || '',
                sex: this.$store.state.userinfo.sex || '',
                birth: this.$store.state.userinfo.birth || '1900-01-01',
                real_name: this.$store.state.lockInfo.is_name || '',
                qq: this.$store.state.userinfo.qq || '',
            };
        },
        components:{
            SafeCheck
        },
        computed:{
            userid(){
                return this.$store.state.userinfo.id
            },
            name(){
                return this.$store.state.userinfo.username
            },
            userinfo(){
                return this.$store.state.userinfo
            },
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
            isChose(){ //是否需要安全验证
                return this.isYzm || this.isEmail ? true : false
            },
            idname(){ //真实姓名
                return this.$store.state.lockInfo.is_name || ''
            },
            isQq(){
                return Number(this.$store.state.setting.reg_way.common) ? true : false
            }
        },
        watch:{
            userinfo(val){
                if(val){
                    this.$set(this,'nickname',val.nickname)
                    this.$set(this,'explan',val.explan)
                    this.$set(this,'sex',val.sex)
                    this.$set(this,'birth',val.birth)
                }
            }
        },
        methods:{
            choseSex1(){
                this.sex = 1
            },
            choseSex2(){
                this.sex = 2
            },
            choseSex3(){
                this.sex = 3
            },
            checkTel(){ //判断是否绑定手机
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
                if(!this.nickname){
                    this.$messagebox('提示','昵称不能为空')
                    return;
                }
//                if(!this.explan) {
//                    this.$messagebox('提示','个性签名不能为空')
//                    return;
//                }
                if(!this.idname.length && !this.real_name.length) {
                    this.$messagebox('提示','请填写真实姓名')
                    return;
                }
//                if(this.isQq){
//                    if(!this.qq.length){
//                        this.$messagebox('提示','请填写QQ号码')
//                        return
//                    }
//                    if(!this.$base.testQQnum(this.qq)){
//                        this.$messagebox('提示','请填写正确的QQ号码')
//                        return
//                    }
//                }
                if(this.isChose){
                    if(!this.changeWay) {
                        this.$messagebox('提示','请选择验证方式')
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
                }
                this.loading = true
                let yzm = !this.changeWay ? '' : this.changeWay == 1 ? this.telYzm : this.emailYzm
                let data = {
                    changeWay: this.changeWay,
                    yzm : yzm,
                    nickname: this.nickname,
                    explan: this.explan,
                    sex: this.sex,
                    birth: this.birth,
                }
                if(!this.idname.length){
                    data['real_name'] = this.real_name
                }
                if(this.isQq){
                    data['qq'] = this.qq
                }
                this.$axios.post('/web/user/checkInfoChange',data).then(({data}) =>{
                    if(!data.err){
                        this.$store.dispatch('getUserInfo')//更新用户信息
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
        },
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
    .mint-cell-wrapper .mint-cell-title{
        width: 78px;
        flex: inherit;
    }
    input[type="date"]{
        -webkit-appearance:none;outline:none;
        font-size: 16px;
        border:none;
        width: 100%;
        /*&::-webkit-calendar-picker-indicator {*/
            /*display: none;*/
        /*}//用来移除向下箭头*/
        /*::-webkit-clear-button{*/
            /*display:none;*/
        /*}//移除叉叉按钮*/
    }
</style>
