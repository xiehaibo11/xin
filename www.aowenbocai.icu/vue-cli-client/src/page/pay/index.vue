<template>
    <div>
        <div class="card pay-header f-sm">
            <p>
                <em>账号：</em>{{userinfo.nickname}}（{{userinfo.username}}）
            </p>
            <p>
                <em>账户余额：</em>
                <b class="org"> {{userinfo.money}} {{coin}}</b>
            </p>
            <p v-if="gameStatus">
                <em>游戏账户：</em>
                <b class="org"> {{userinfo.game_money}} {{gameUnit}}</b>
            </p>
        </div>
        <div class="card mt">
            <div class="flex-box pay-way border-bottom-1px">
                <div class="flex">充值{{isChange ? gameUnit : coin}}</div>
                <template v-if="gameStatus">
                    <div class="c-2 f-sm change">转换成游戏币</div>
                    <mt-switch v-model="isChange"></mt-switch>
                </template>
            </div>
            <div>
                <div class="pay-title">充值金额</div>
                <ul class="change-list clearfloat">
                    <li v-for="(item,index) in rechargeInfo.rechargeList">
                        <div class="list" :class="{'active':cur == index}"  @click="choseRechage(item.money,index,item.award)">
                            <div class="coin c-2 f-sm">
                                <p v-if="isChange">{{item.coin}}<em class="f-sm c-3">{{gameUnit}}</em></p>
                                <p v-else>{{item.lottery_money}}<em class="f-sm c-3">{{coin}}</em></p>
                            </div>
                            <div><em class="c-2">{{item.money}}元</em></div>
                        </div>
                    </li>
                    <!--三方支付 可输入金额-->
                    <li  :class="[{'active':cur == -1},{'error':state}]" @click="cur = -1">
                        <div class="list"  :class="{'active':cur == -1}" >
                            <div class="coin c-2 f-sm flex-box" style="height: 25px">
                                <input class="input-money" type="tel" v-model="other" placeholder="其他数量" @blur="checkMoney" @focus="otherRecharge">
                            </div>
                            <div><em class="c-2">{{otherMoney}}元</em></div>
                        </div>
                    </li>
                    <!--三方支付 可输入金额 end -->
                </ul>
                <!--赠送显示-->
                <template v-if="cur !== -1 && awards > 0 && rechargeInfo.has_recharge_times > 0 || cur == -1 && otherAward > 0 && rechargeInfo.has_recharge_times > 0">
                    <div class="pd f-mini c-3 mf tips">可额外获得
                        <em class="org" v-if="cur !== -1">{{awards}}</em><em class="org" v-else>{{otherAward}}</em><em class="org">{{coin}}</em>
                        <em class="c-3 f-mini"> （每日充值赠送{{rechargeInfo.recharge_send_times}}次，今日剩余{{rechargeInfo.has_recharge_times}}次）</em>
                    </div>
                </template>
                <!--赠送显示 end-->
            </div>
        </div>
        <div class="pd mt mf">支付方式</div>
        <div class="tabs card">
            <div v-if="rechargeInfo.paysetting.other_alipay || rechargeInfo.paysetting.other_wx || rechargeInfo.select_other_pay !== 0">
                <label class="mint-radiolist-title">三方支付</label>
                <template v-for="(item,index) in options">
                    <a class="mint-cell" @click="payWay = item.id">
                        <div class="mint-cell-left"></div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title"><!---->
                                <label class="mint-radiolist-label">
                                <span class="mint-radio is-right">
                                    <input type="radio" class="mint-radio-input" v-model="payWay" :value="item.id">
                                    <span class="mint-radio-core"></span>
                                </span>
                                    <span class="mint-radio-label">{{item.title}}</span>
                                </label>
                            </div>
                            <div class="mint-cell-value"></div>
                        </div>
                        <div class="mint-cell-right"></div>
                    </a>
                    <!--其他支付下级选项-->
                    <div v-if="item.children" class="third-pay-children">
                        <ul class="cf" v-if="payWay == item.id">
                            <li v-for="(child,i) in item.children" class="fl" :class="{'active': payWayChild == child.id}" @click="payWayChild = child.id">
                                {{child.title}}
                            </li>
                        </ul>
                    </div>
                    <!--其他支付下级选项 end-->
                </template>
            </div>

            <div v-if="rechargeInfo.paysetting.pay_alipay || rechargeInfo.paysetting.pay_wx">
                <mt-radio
                    v-model="payWay"
                    align="right"
                    title="扫码支付"
                    :options="saoOptions">
                </mt-radio>
            </div>

            <div v-if="Number(rechargeInfo.bank_open)">
                <label class="mint-radiolist-title">银行卡转账</label>
                <a class="mint-cell" @click="payWay = 'bankTransfer'">
                    <div class="mint-cell-left"></div>
                    <div class="mint-cell-wrapper">
                        <div class="mint-cell-title"><!---->
                            <label class="mint-radiolist-label">
                                <span class="mint-radio is-right">
                                    <input type="radio" class="mint-radio-input" v-model="payWay" value="bankTransfer">
                                    <span class="mint-radio-core"></span>
                                </span>
                                <span class="mint-radio-label">银行卡转账</span>
                            </label>
                        </div>
                        <div class="mint-cell-value"></div>
                    </div>
                    <div class="mint-cell-right"></div>
                </a>
            </div>
        </div>
        <div class="pay-count pd mt">需支付 <b class="red f-large" v-if="cur == -1">{{otherMoney || 0}}</b><b class="red f-large" v-else>{{money}}</b> 元</div>
        <div class="btn-box">
            <mt-button size="large" type="danger" @click.native="next">下一步</mt-button>
        </div>
        <div class="pd mf">
            <h5 class="c-2 f-sm">温馨提示：</h5>
            <p class="c-3 f-sm">{{recharge_info}}</p>
        </div>
    </div>
</template>
<script>
    import { Indicator } from 'mint-ui';
    export default{
        name:'pay',
        data() {
            return {
                isChange : false , //转换游戏币开关
                cur : 0,
                money : 1,//充值金额
                awards : 0,//额外奖励
                payWay:this.$store.state.rechargeInfo.select_other_pay == 0 ? this.$store.state.rechargeInfo.paysetting.pay_alipay ? 'alipaySao' : this.$store.state.rechargeInfo.paysetting.pay_wx ? 'wxSao' : 'bankTransfer' : this.$store.state.rechargeInfo.select_other_pay, //支付方式
                payWayChild:'',
                weiUrl:'', //微信充值返回页面

                other: '',
                otherAward: 0,
                state: false,
            };
        },
        computed:{
            gameStatus(){
                return this.$store.state.setting.game_status == 1 ? true : false
            },
            recharge_info(){
                return this.$store.state.setting.recharge_info
            },
            userinfo(){
                return this.$store.state.userinfo
            },
            coin(){
                return this.$store.state.setting.lottery_unit
            },
            gameUnit(){
                return this.$store.state.setting.game_unit
            },
            rechargeInfo(){
                return this.$store.state.rechargeInfo
            },
            listData(){
                return this.rechargeInfo.rechargeList
            },
            //三方支付列表
            other_pay(){
                return this.$store.state.rechargeInfo.paysetting.third_pay
            },
           //支付选项
            options(){
                let arr = []
                if(this.rechargeInfo.paysetting.other_alipay){
                    arr.push({title: '支付宝支付',id: 'alipay' })
                }
                if(this.rechargeInfo.paysetting.other_wx){
                    arr.push({title: '微信支付',id: 'wx' })
                }
                if(this.rechargeInfo.select_other_pay !== 0){
                    for(let p in this.other_pay){
                        arr.push(this.other_pay[p])
                    }
                }
                return arr
            },
            saoOptions(){
                let arr = []
                if(this.rechargeInfo.paysetting.pay_alipay){
                    arr.push({label: '支付宝扫码',value: 'alipaySao' })
                }
                if(this.rechargeInfo.paysetting.pay_wx){
                    arr.push({label: '微信扫码',value: 'wxSao' })
                }
                return arr
            },
            //赠送比例
            award(){
                return this.rechargeInfo.award
            },
            //游戏币比例
            scaleCoin(){
                return this.$store.state.setting.recharge_award
            },
            //其他充值金额计算
            otherMoney() {
                var res = this.$base.isValueNumber(this.other);
                if (res) {
                    if (this.isChange) { //充值游戏币
                        return this.other / this.scaleCoin;
                    } else {//余额
                        return this.other;
                    }
                } else {
                    return '';
                }
            }
        },
        watch: {
            //计算额外赠送值
            other(val) {
                if (this.otherMoney < 1) {
                    this.otherAward = 0;
                } else {
                    for (var i in this.listData) {
                        if (parseInt(this.otherMoney) >= this.listData[i].money) {
                            //充值游戏币
                            if (this.isChange) {
                                this.otherAward = Math.floor(this.award[parseInt(this.listData[i].money)] * (val / this.scaleCoin) * 100 / 100);
                            }else {
                                this.otherAward = Math.floor(this.award[parseInt(this.listData[i].money)] * val * 100 / 100);
                            }
                        }
                    }
                }
            },
            isChange() {
                this.checkMoney();
            },
            payWay(val) {
                this.payWayChild = ''
                if(val !== 'alipay' && val !== 'wx' && val !== 'alipaySao' && val !== 'wxSao'){
                    for(let i in this.other_pay){
                        if(val == this.other_pay[i].id && this.other_pay[i].children){
                            this.payWayChild = this.other_pay[i].children[0].id
                        }
                    }
                }
            }
        },
        methods:{
            //获取焦点时输入其他数量
            otherRecharge() {
                this.cur = -1;
            },
            //失去焦点时检查输入数量、赠送值
            checkMoney() {
                var other = parseInt(this.other);
                var scaleCoin = parseInt(this.scaleCoin);
                if (!this.other) {
                    this.state = true;
                    return
                }else {
                    this.state = false;
                    if (this.isChange) { //充值游戏币
                        this.state = false;
                        if (other < scaleCoin) {
                            this.other = this.scaleCoin;
                        } else {
                            this.other = Math.ceil(other / scaleCoin) * scaleCoin;
                        }
                    }else {
                        this.other = parseInt(other);
                    }
                }

            },
            //选择充值金额
            choseRechage(money,index,award){
                this.money = money
                this.cur = index
                this.awards = award
            },
            //进入下一步
            next(){
                let money = this.cur == -1 ? this.otherMoney : this.money
                let val = this.isChange ? 1 : 2
                let type = '&type=' +  val
                if(this.cur == -1){
                    if(!money){
                        this.$messagebox('提示','请输入购买数量')
                        return
                    }
                }
                if(this.payWay == 'alipay'){//支付宝
                    Indicator.open({
                        spinnerType: 'fading-circle'
                    });
                    location.href = '/pay/pay/payToAlipay?total_amount='+ money + type
                }else if(this.payWay == 'wx'){//微信
                    Indicator.open({
                        spinnerType: 'fading-circle'
                    });
                    this.$axios.get('/pay/pay/payToWei',{
                        params:{
                            type:type,
                            total_amount: money
                        }
                    }).then(({data})=>{
                        Indicator.close();
                        this.$router.push({
                            path:'/payWeiRes',
                            query:{
                                res:data
                            }
                        })
                    }).catch(({error})=>{
                        Indicator.close();
                        console.log(error)
                    })
                }else if(this.payWay == 'alipaySao' || this.payWay == 'wxSao'){ //扫码支付
                    let redirect = this.$route.query.redirect
                    let queryData = {
                        money:money,
                        payWay:this.payWay,
                        type:this.isChange ? 1 : 2
                    }
                    if(redirect){
                        queryData['redirect'] = redirect
                    }
                    this.$router.replace({
                        path:'/saoPay',
                        query:queryData
                    })
                    return
                }else if(this.payWay == 'bankTransfer'){
                    let redirect = this.$route.query.redirect
                    let queryData = {
                        money:money,
                        payWay:4, //提交充值方式标识
                        type:this.isChange ? 1 : 2
                    }
                    if(redirect){
                        queryData['redirect'] = redirect
                    }
                    this.$router.replace({
                        path:'/bankPay',
                        query:queryData
                    })
                }else{//其他三方支付
                    Indicator.open({
                        spinnerType: 'fading-circle'
                    });
                    let third_id = '&third_id='+  this.payWay ;
                    var third_type = this.payWayChild ?  '&third_type='+  this.payWayChild : '' ;
                    location.href = '/pay/pay/payToThird?total_amount='+ money + type + third_id + third_type
//                    this.$messagebox('提示','该功能暂未开通，请选择其他支付方式!')
                }
            }
        },
        created(){
            this.money = this.rechargeInfo.rechargeList[0].money
            this.awards = this.rechargeInfo.rechargeList[0].award
            this.$store.commit('setKeepAlivePage','pay')
            this.payWayChild = ''
            if(this.payWay !== 'alipay' && this.payWay !== 'wx' && this.payWay !== 'alipaySao' && this.payWay !== 'wxSao' && this.payWay !== 'bankTransfer'){
                for(let i in this.other_pay){
                    if(this.payWay == this.other_pay[i].id && this.other_pay[i].children){
                        this.payWayChild = this.other_pay[i].children[0].id //三方支付二级选项设置默认值
                    }
                }
            }
        },
        beforeRouteLeave(to, from, next){
            if(to.path =='/payWeiRes' || to.path == '/saoPay'){
                this.$store.commit('setKeepAlivePage','pay')
            }else {
                this.$store.commit('delKeepAlivePage','pay')
            }
            next();
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .pd{
        padding:0 10px;
    }
    .pay-header{
        padding: 10px 5px;
        line-height: 1.8;
    }
    .pay-way{
        margin-left: 10px;
        padding: 10px 10px 10px 0;
        .change{padding-right: 5px}
    }
    .pay-title{
        font-size: 12px;
        color:$color-font-secondary;
        padding: 10px;
    }
    .change-list{
        padding: 0 5px 0;
        li{
            .list{
                line-height: 1.8;
                width: 30%;
                margin:0px 1.5% 10px;
                float: left;
                padding: 5px;
                text-align: center;
                border:0.5px solid $color-border-one;
                border-radius: 3px;
                position: relative;
            }
            .active{
                background-color: $bColor;
                color: #ffffff;
                border:0.5px solid $bColor;
                p{
                    color: #ffffff;
                }
                em{
                    color: #ffffff;
                }
            }
        }
    }
    .input-money{
        width: 100%;
        border: 1px solid #f1f1f1;
        padding:3px 5px;
        @include rounded(2);
    }
    .third-pay-children{
        background-color: #f1f1f1;
        ul{
            padding: 5px 10px;
            li{
                float: left;
                width: 30%;
                margin: 4px 1.5%;
                height: 36px;
                line-height: 36px;
                text-align: center;
                border:1px solid #dddddd;
                background-color: #ffffff;
                font-size: 13px;
                @include rounded(5px);
                &.active{
                    color: $bColor;
                    border-color: $bColor;
                }
            }
        }
    }
</style>
