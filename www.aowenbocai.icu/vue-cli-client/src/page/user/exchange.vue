<template>
    <div>
        <mt-header title="兑换中心">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
            <router-link to="/exchangeRecord" slot="right"><div  class="f-sm">我的兑换</div></router-link>
        </mt-header>
        <div class="mt-sm c-3 f-sm tc">每天兑换时间{{setting.getmoney_startime}}~{{setting.getmoney_endtime}} 今日剩余 <em class="org">{{setting.ure_times}}</em> 次</div>
        <div class="exchange-box mt-sm">
            <div class="border-bottom-1px tel-info">
                <p>{{tel}} <router-link class="link f-common" style="text-decoration: underline" to="/changeTel">修改</router-link></p>
                <p class="f-sm suc mt-sm">账号绑定号码</p>
            </div>
            <div class="title org f-sm" style="height: 60px" v-if="exchangeInfo">兑换说明：{{exchangeInfo}}</div>
            <div class="contentH" :style="{height: contentH + 'px'}">
                <ul class="change-list clearfloat">
                    <li v-for="(item,index) in exchangeList">
                        <div class="list" :class="[{'active':active == index} , {'disabled': item.has_num == 0}]"  @click="choseEx(index)">
                            <p class="value"><b>{{item.name}}</b></p>
                            <p class="money f-mini c-3">售价<em class="org f-sm">{{item.integral}}</em><em class="f-mini c-3">{{coin}}</em></p>
                            <i v-if="active == index" class="iconfont icon-anonymous-iconfont"></i>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="account-box">
            <p>当前账户余额：<em class="f-sm org">{{accountMoney}}</em> {{coin}}</p>
        </div>
        <div class="foot">
            <div class="foot-1 border-top-1px">
                兑换<strong class="red">{{name || '请选择'}}</strong>,
                需消耗<strong class="red">{{money * num}}</strong>{{coin}}
                <em v-if="getmoney_low <= money * num && accountMoney < money * num" class="tips" style="padding: 0 0 0 5px;font-size: 11px">账户余额不足</em>
                <em v-if="getmoney_low > money * num" class="tips" style="font-size: 11px">最低兑换金额{{getmoney_low}}</em>
            </div>
            <div class="foot-2 flex-box">
                <div>
                    <em class="c-4">兑换数量 </em><input-number v-model="num" :min="1"></input-number>
                </div>
                <div class="flex tr">
                    <button class="bet-btn btn-sure" @click="submitOrder">提交订单</button>
                </div>
            </div>
        </div>
        <!--订单信息确认-->
        <mt-popup
            v-model="orderVisible"
            position="bottom">
            <div class="orders-info" v-if="orderVisible">
                <div class="order-title org border-bottom-1px">订单信息确认</div>
                <div class="order-info">
                    <div>
                        <span class="name">兑换内容:</span>
                        <span class="info">{{name}}</span>
                    </div>
                    <div>
                        <span class="name">兑换数量:</span>
                        <span class="info">{{num}}</span>
                    </div>
                    <div>
                        <span class="name">兑换总额:</span>
                        <span class="info">{{money * num}} {{coin}}</span>
                    </div>
                    <div>
                        <span class="name">账户余额:</span>
                        <span class="info"><em>{{accountMoney}}</em> {{coin}}</span>
                        <span class="tips tc" style="font-size: 14px;color: #333" v-if="accountMoney < money * num"><b class="red">（余额不足，请充值!）</b></span>
                    </div>
                </div>
                <!--选择安全验证码方式-->
                <template>
                    <div class="relative">
                        <div class="input-title"><em class="red">*</em> 安全验证</div>
                        <div class="" v-if="isChose && changeWay == 0">
                            <mt-cell title="验证已绑定手机" label="已绑定手机且该手机能接收短信" is-link v-if="isYzm" @click.native="checkTel">
                                <i slot="icon" class="iconfont icon-ai-bind-cel"></i>
                            </mt-cell>
                            <mt-cell title="验证已绑定邮箱" label="已绑定邮箱且该邮箱能接收邮件" is-link v-if="isEmail" @click.native="checkEmail">
                                <i slot="icon" class="iconfont icon-mail"></i>
                            </mt-cell>
                            <mt-cell title="验证安全密码" label="已设置的安全密码" is-link @click.native="checkSafePwd">
                                <i slot="icon" class="iconfont icon-mima1"></i>
                            </mt-cell>
                        </div>
                        <a v-if="isChose && changeWay" style="text-decoration: underline" class="link f-sm change-way-link" @click="changeWay = 0">更换验证方式</a>
                        <template >
                            <safe-check url="/web/user/sendExchangeMsg" v-show="changeWay == 1" v-model="telYzm" :check-way="1"></safe-check>
                        </template>
                        <template>
                            <safe-check url="/web/user/sendExchangeEmail" v-show="changeWay == 2" v-model="emailYzm" :check-way="2"></safe-check>
                        </template>
                        <template v-if="changeWay == 3">
                            <mt-field label="安全密码" placeholder="请输入安全密码" type="password" v-model="safePwd" v-if="lockSafePwd">
                                <div class="btn-yzm f-sm"><router-link to="/changeSafePwd" style="color:#0c6ad4">忘记密码？</router-link></div>
                            </mt-field>
                            <div v-else style="padding: 0 15px" class="red f-sm">
                                您还没有设置安全密码  <router-link to="/setSafePwd"><mt-button size="small">立即去设置</mt-button></router-link>
                            </div>
                        </template>
                    </div>
                </template>
                <div class="order-footer tr">
                    <mt-button class="cancel" @click="orderVisible = false" size="small">取消</mt-button>
                    <mt-button type="primary"  size="small" v-if="accountMoney < money * num" @click="goPay">立即充值</mt-button>
                    <mt-button type="primary" v-else @click="payment" size="small">
                        <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                        确定兑换
                    </mt-button>
                </div>
            </div>
        </mt-popup>
    </div>
</template>
<script>
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    import SafeCheck from 'components/user/SafeCheck.vue'
    export default{
        name:'exchange',
        components:{
            inputNumber,
            SafeCheck
        },
        data() {
            return {
                active: 0,//选中话费,
                num:1,//兑换数量
                orderVisible:false,
                loading: false,

                telYzm:'',
                emailYzm:'',
                safePwd:'',
                changeWay:0
            };
        },
        computed:{
            //兑换说明
            exchangeInfo(){
                return this.$store.state.setting.getmoney_info
            },
            //最低兑换金额
            getmoney_low(){
                return this.$store.state.setting.getmoney_low
            },
            //话费选项高度
            contentH(){
                let h = this.exchangeInfo ? 314 : 254
                return this.$store.state.clientHeight - h
            },
            tel(){
                return this.$store.state.userinfo.tel
            },
//            text(){
//                return this.$base.checkMobile(parseInt(this.tel))
//            },
            exchangeList(){
                return this.$store.state.exchangeList
            },
            setting(){
                return this.$store.state.setting
            },
            coin(){
                return this.$store.state.setting.lottery_unit
            },
            accountMoney(){
                return this.$store.state.userinfo.money
            },
            lockSafePwd(){
                return this.$store.state.lockInfo.safe_pwd !==1 ? true : false //是否设置了安全密码
            },
            lockTel(){
                return this.$store.state.lockInfo.tel !== 1 ? true : false //是否绑定手机号
            },
            lockEmail(){
                return this.$store.state.lockInfo.email !== 1 ? true : false //是否绑定邮箱
            },
            isYzm(){
                return this.$store.state.setting.tel_checked == 1 ? true: false //是否开启短信接口
            },
            isEmail(){
                return this.$store.state.setting.email_checked == 1 ? true: false //是否开启邮箱验证
            },
            isChose(){
                let n = 0
                if(this.isYzm){
                    n += 1
                }
                if(this.isEmail){
                    n += 1
                }
                return n ? true : false
            },
            money(){
                return this.exchangeList.length ? this.exchangeList[this.active].integral : ''
            },//兑换金额
            name(){
                return this.exchangeList.length ?this.exchangeList[this.active].name: ''
            },//兑换内容
            id(){
                return this.exchangeList.length ?this.exchangeList[this.active].id: ''
            },//兑换id
        },
        methods:{
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
            checkSafePwd(){
                if(this.lockSafePwd){
                    this.changeWay = 3
                }else {
                    this.$messagebox.confirm(
                        '您还未设置安全密码，立即去设置?'
                    ).then(()=>{
                        this.$router.push({
                            path:'/setSafePwd'
                        })
                    }).catch((err)=>{

                    });
                }
            },
            //兑换选择
            choseEx(n) {
                this.active = n;
            },
            //充值
            goPay(){
                this.$router.push({
                    path:'/pay',
                    query:{
                        redirect:this.$route.path
                    }
                })
            },
            //订单信息确认
            submitOrder(){
                this.$store.dispatch('getSystemSet') //更新兑换状态
                let totalMoney = this.money * this.num
                if(totalMoney < this.getmoney_low){
                    this.$messagebox('提示','最低兑换金额为'+ this.getmoney_low + this.coin)
                    return
                }
                if(this.setting.ure_times< 1){
                    this.$toast('今日剩余兑换次数已用完！')
                    return
                }
                if(!parseInt(this.setting.isTochange)){
                    this.$toast('兑换时间为 ' + this.setting.getmoney_startime + ' - ' + this.setting.getmoney_endtime,)
                    return
                }
                if(this.active == -1){
                    this.$toast('请选择兑换内容!')
                    return
                }
                this.$store.dispatch("getUserInfo"); //更新用户信息
                this.orderVisible = true
            },
            //兑换成功
            exchangeSuc(){
                this.active = 0
                this.num = 1
                this.orderVisible = false
                this.loading = false
                this.telYzm = ''
                this.emailYzm = ''
                this.safePwd = ''
//                this.changeWay = 0
                this.$store.dispatch('getSystemSet') //更新兑换状态
                this.$store.dispatch('getUserInfo') //更新用户信息
            },
            //确认兑换
            payment(){
                let yzm, changeWay;
//                if (this.isYzm || this.isEmail) {
                    if (this.changeWay == 0) {
                        this.$toast('请选择1种安全验证方式');
                        return;
                    } else if (this.changeWay == 1) {
                        if (!this.telYzm) {
                            this.$toast('请输入验证码');
                            return false;
                        }
                        changeWay = 1; //短信验证
                        yzm = this.telYzm;
                    } else if (this.changeWay == 2) {
                        if (!this.emailYzm) {
                            this.$toast('请输入验证码');
                            return false;
                        }
                        changeWay = 2; //邮箱验证
                        yzm = this.emailYzm;
                    }else if (this.changeWay == 3) {
                        if(this.lockSafePwd){
                            if (!this.safePwd.toString().length) {
                                this.$toast('请输入安全密码');
                                return false;
                            }
                            changeWay = 3; //安全密码验证
                            yzm = this.safePwd;
                        }else {
                            this.$messagebox('提示','请先设置安全密码')
                            return
                        }
                    }
//                }
                this.loading = true
                this.$axios.post('/web/user/exchangeGift',{
                    changeWay: changeWay,
                    yzm: yzm,
                    num: this.num,
                    id: this.id
                }).then(({data}) =>{
                    if(!data.err){
                        this.exchangeSuc();
                    }else {
                    }
                    this.$messagebox('提示',data.msg);
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                })
            }
        },
        created(){
            this.$store.dispatch('getExchangeList')
            this.$store.commit('setKeepAlivePage','exchange')
            if(!this.isYzm && !this.isEmail){
                this.changeWay = 3
            }
        },
        beforeRouteLeave(to, from, next){
            if(to.path =='/exchangeRecord' || to.path == '/pay'){
                this.$store.commit('setKeepAlivePage','exchange')
            }else {
                this.$store.commit('delKeepAlivePage','exchange')
            }
            next();
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .account-box{
        font-size: 12px;
        height: 26px;
        line-height: 26px;
        padding: 0 5px;
    }
    .foot{
        width: 100%;
        .foot-1{
            background-color: #ffffff;
            height: 30px;
            line-height: 30px;
            font-size: 13px;
            padding:0 5px;
        }
        .foot-2{
            padding: 0 5px;
            background-color: #252625;
            text-align: center;
            height: 52px;
        }
    }
    .exchange-box{
       background-color: #ffffff;
        .tel-info{
            padding: 10px;
            font-size: 22px;
            font-weight: 500;
        }
        .title{
            padding:10px;
        }
        .change-list{
            padding: 0 5px 10px;
            li{
                i{
                    position: absolute;
                    right: 1px;
                    bottom: -5px;
                    color: $bColor;
                }
                .list{
                    line-height: 1.8;
                    width: 30%;
                    margin:8px 1.5%;
                    float: left;
                    padding: 5px;
                    text-align: center;
                    border:0.5px solid $color-border-one;
                    border-radius: 3px;
                    position: relative;
                }
                .active{
                    color: $bColor;
                    border:0.5px solid $bColor;
                }
            }
        }
    }
    .orders-info{
        .order-info, .order-footer{
            margin: 0 10px;
            font-size: 16px;
        }
        .order-title{
            padding-bottom: 10px;
            margin: 0 10px 10px;
        }
    }
    .mint-popup{
        padding: 10px 0;
    }
    .relative{
        position: relative;
        margin-bottom: 10px;
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
