<!--订单页面 合买设置-->
<template>
    <div>
        <mt-header title="合买详情">
            <mt-button icon="back" slot="left" @click.native="toJoin(false)">取消合买</mt-button>
        </mt-header>
        <div class="bet-join contentH" :style="{height:joinContentH + 'px'}">
            <div class="info flex-box border-bottom-1px">
                <div class="label">方案金额</div>
                <div class="cont">
                    <em class="red">{{totalMoney}}</em> {{lotteryUnit}}
                    <span class="join-tip">
                                {{totalNotes}}注 <em>{{multiple}}倍</em>
                            </span>
                </div>
            </div>
            <div class="info flex-box border-bottom-1px">
                <div class="label">我要分成</div>
                <div class="cont">
                    <input-number v-model="join.total_share" :min="minTotalShare" :max="maxShare" size="small"></input-number> 份
                    <span class="join-tip">每份{{perMoney}}{{lotteryUnit}}</span>
                    <span style="font-size: 10px">(每份至少1{{lotteryUnit}})</span>
                </div>
            </div>
            <div class="info flex-box border-bottom-1px">
                <div class="label">我要认购</div>
                <div class="cont">
                    <input-number v-model="join.buy_share" :min="minJoinMoney" :max="join.total_share" size="small"></input-number> 份
                    <span class="join-tip">至少认购<b class="red">5</b>%</span>
                    <span style="font-size: 10px">(已认购<em class="red">{{isbuy}}</em>%)</span>
                </div>
            </div>
            <div class="info flex-box border-bottom-1px">
                <div class="label">我要保底</div>
                <div class="cont">
                    <input-number v-model="join.bd_share" :min="0" :max="bdMax" size="small"></input-number> 份
                    <span class="join-tip">最多可保底<em class="red">{{bdMax}}</em>份</span>
                    <span style="font-size: 10px">(已保底<em class="red">{{bdPercent}}</em>%)</span>
                </div>
            </div>
            <div v-if="isGain" class="info flex-box border-bottom-1px">
                <div class="label">盈利佣金</div>
                <div class="cont">
                    <span class="select-type border-1px" @click="gainVisible = !gainVisible">{{join.gain}}% <i class="iconfont icon-jiantou c-4 f-mini"></i></span>
                    <span class="join-tip">盈利佣金=奖金*佣金比例</span>
                </div>
            </div>
            <div class="info flex-box border-bottom-1px">
                <div class="label">保密设置</div>
                <div class="cont">
                    <span class="select-type border-1px" @click="openVisible = !openVisible">{{join.infoTitle}} <i class="iconfont icon-jiantou c-4 f-mini"></i></span>
                </div>
            </div>
            <div class="info flex-box border-bottom-1px">
                <div class="label">合买宣言</div>
                <div class="cont">
                    <input class="input" type="text" placeholder="说点什么吧!" v-model="join.declaration">
                </div>
            </div>
        </div>
        <!--底部 start-->
        <div class="bet-foot">
            <div class="flex-box notes-box">
                <div class="flex tl bet-basket">
                    <p>共 {{countShareMoney(join.buy_share+join.bd_share)}} {{lotteryUnit}}</p>
                    <p class="f-mini c-4">认购{{countShareMoney(join.buy_share)}}{{lotteryUnit}}+保底{{countShareMoney(join.bd_share)}}{{lotteryUnit}}</p>
                </div>
                <button class="bet-btn btn-sure" @click="submitOrder">确认合买</button>
            </div>
        </div>
        <!--底部 end-->
        <!--盈利佣金-->
        <mt-popup
            v-model="gainVisible"
            position="bottom"
        >
            <mt-picker :slots="slots1" @change="onGainChange"></mt-picker>
        </mt-popup>
        <!--保密设置-->
        <mt-popup
            v-model="openVisible"
            position="bottom"
        >
            <mt-picker :slots="slots" @change="onValuesChange" valueKey="title"></mt-picker>
        </mt-popup>
    </div>
</template>

<script>
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    export default {
        name: 'orderJoinSet',
        components:{
            inputNumber
        },
        props:{
            visible : { //协议选择状态
                type: Boolean,
                require:true,
                default:false
            },
            multiple:{
                default:1
            },
            totalMoney:{
                default:0
            },
            totalNotes:{
                default:0
            }
        },
        model:{
            prop: 'visible',
            event: 'change'
        },
        data () {
            return {
                joinVisible:this.visible,
                gainVisible: false, //盈利佣金
                openVisible: false, //保密设置
                //合买数据
                join: {
                    total_share:this.totalMoney,//份数
                    buy_share:0,//购买份数
                    bd_share:0,//保底份数
                    infoTitle:'完全公开',
                    infoState: 0, //是否公开
                    gain: '0',
                    declaration: ''
                },
                slots: [
                    {
                        flex: 1,
                        values: [
                            {title:"完全公开",value:0},
                            {title:"截止后公开",value:1},
                            {title:"仅跟单人可见",value:2},
                            {title:"完全保密",value:3}
                        ],
                        className: 'slot1',
                        textAlign: 'center'
                    }
                ],
                slots1: [
                    {
                        flex: 1,
                        values: ['0','1','2','3','4','5','6','7','8','9','10'],
                        className: 'slot1',
                        textAlign: 'center'
                    }
                ]
            }
        },
        watch:{
            joinVisible(val){
                this.$emit('change',val)
            },
            //监听购买总金额
            totalMoney (val) {
                this.join.total_share = val
            },
            //监听总份数
            'join.total_share' (val) {
                this.join.buy_share = Math.ceil(val * 0.05);
                if (this.join.bd_share + this.join.buy_share > val) {
                    this.join.bd_share = this.bdMax;
                }
            },
            //监听认购份数
            'join.buy_share' (val) {
                if (val + this.join.bd_share > this.join.total_share) {
                    this.join.bd_share = this.bdMax;
                }
            },
        },
        computed:{
            //单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //账户余额
            money(){
                return this.$store.state.userinfo.money
            },
            //是否开启佣金
            isGain(){
                return this.$store.state.setting.isGain
            },
            //追号、合买容器高度
            joinContentH(){
                return this.$store.state.clientHeight - 92
            },
            maxShare(){
                return Number(this.totalMoney)
            },
            //最小总份数
            minTotalShare () {
                return this.totalMoney ? 1 : 0
            },
            //每份金额 保留2位去掉
            perMoney(){
                return this.totalMoney ? this.$bet.accDiv(this.totalMoney,this.join.total_share,3) : 0
            },
            //最少购买份数 向上取整
            minJoinMoney() {
                return Number(Math.ceil(this.join.total_share * 0.05));
            },
            //已购买百分比 取整
            isbuy() {
                return !this.totalMoney ? 0 : Math.floor(this.join.buy_share / this.join.total_share * 10000) / 100
            },
            //最多可保底
            bdMax() {
                return this.join.total_share - this.join.buy_share;
            },
            //已保底百分比 取整
            bdPercent() {
                return !this.totalMoney ? 0 :  Math.floor(this.join.bd_share / this.join.total_share * 10000) / 100
            },
        },
        methods:{
            //合买
            toJoin(s){
                if(s){
                    this.joinVisible = true
                    this.join.total_share = this.totalMoney
                }else {
                    this.joinVisible = false
                }
            },
            //合买设置
            //保密设置
            onValuesChange(picker, values) {
                this.join.infoState = values[0].value
                this.join.infoTitle = values[0].title
            },
            //盈利佣金
            onGainChange(picker, values) {
                this.join.gain = values[0]
            },
            submitOrder(){
                this.$emit('submit-order',this.join)
            },
            //计算金额（每份金额*购买份数，保留2位小数）
            countShareMoney (share) {
                return this.$bet.accMul(this.perMoney,share,3)
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
