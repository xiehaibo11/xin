<!--快3和值投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="ks-ball contentH" :style="{height:contentH + 'px'}">
            <div class="g-bet-item-tips tc f-sm g-c-light">猜3个开奖号相加的和，3-10为小，11-18为大。</div>
            <ul class="balls-box cf">
                <li v-for="(item,index) in ball" :key="index">
                    <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index,item.gain)">
                        <p><b class="f-large">{{item.num}}</b></p>
                        <p class="gain f-mini">赔{{handleGain(item.gain)}}</p>
                    </a>
                </li>
            </ul>
        </div>
        <!--选号 end-->
        <!--底部 start-->
        <div class="bet-foot">
            <div class="gain-box f-sm ks-notes">
                <div class="flex-box ks-unit">
                    <div class="flex">
                        <select-unit v-if="betModel == 1 && unitIsOpen"></select-unit><!--模式1 元角分厘模式开启时显示-->
                    </div>
                    <div class="flex" v-if="rebateIsOpen && isRebate">
                        <rebate-range :gain="maxGain"></rebate-range>
                    </div>
                </div>
            </div>
            <div class="flex-box notes-box">
                <div class="flex bet-detail">
                    <!--<button class="bet-btn btn-clear"></button>-->
                    <div class="cf">
                        <i class="iconfont icon-shanchu btn-icon-clear fl"  @click="clear"></i>
                        <span class="fl f-sm">
                            <em class="total-money">{{notes}}</em>注
                            <template v-if="betModel == 1">
                                <span><em class="total-money">{{totalMoney}}</em>{{lotteryUnit}}</span>
                            </template>
                        </span>
                    </div>
                </div>
                <div class="tc bet-basket" style="margin: 0 15px 0 5px">
                    <button class="bet-btn btn-basket" @click="addBetBasket">+ 号码篮 <mt-badge class="badge" type="error" size="small">{{badge}}</mt-badge></button>
                </div>
                <button class="bet-btn btn-sure" @click="submitOrder">立即投注</button>
            </div>
        </div>
        <!--底部 end-->
    </div>
</template>

<script>
    import SelectUnit from 'components/lottery/SelectUnit.vue' //倍数、单位设置组件
    import RebateRange from 'components/lottery/RebateRange.vue' //返点设置
    export default {
        props:['type','gain','title','text','name','betModel','maxGain'],
        components:{
            SelectUnit,
            RebateRange
        },
        data () {
            return {
                ball: [{num:3,selected:false,gain:this.gain.split(',')[0]},{num:4,selected:false,gain:this.gain.split(',')[1]},
                    {num:5,selected:false,gain:this.gain.split(',')[2]},{num:6,selected:false,gain:this.gain.split(',')[3]},
                    {num:7,selected:false,gain:this.gain.split(',')[4]},{num:8,selected:false,gain:this.gain.split(',')[5]},
                    {num:9,selected:false,gain:this.gain.split(',')[6]},{num:10,selected:false,gain:this.gain.split(',')[7]},
                    {num:11,selected:false,gain:this.gain.split(',')[8]},{num:12,selected:false,gain:this.gain.split(',')[9]},
                    {num:13,selected:false,gain:this.gain.split(',')[10]},{num:14,selected:false,gain:this.gain.split(',')[11]},
                    {num:15,selected:false,gain:this.gain.split(',')[12]},{num:16,selected:false,gain:this.gain.split(',')[13]},
                    {num:17,selected:false,gain:this.gain.split(',')[14]},{num:18,selected:false,gain:this.gain.split(',')[15]},
                    {num:'大',selected:false,gain:this.gain.split(',')[16]},{num:'小',selected:false,gain:this.gain.split(',')[17]},
                    {num:'单',selected:false,gain:this.gain.split(',')[18]},{num:'双',selected:false,gain:this.gain.split(',')[19]}
                ],
                betNum:[],
                betArr:[],//投注组
                gainArr:[],//混合投注奖金数组
            }
        },
        computed:{
            //返点设置是否开启
            rebateIsOpen(){
                return this.$store.state.setting.rebate_isOpen == 1 ? true : false
            },
            //用户返点为零时不显示
            isRebate(){
                return this.$store.state.lottery.userRebate == 0 ? false : true
            },
            //是否开启合买
            joinOpen(){
                return this.$store.state.setting.join_isOpen == 1 ? true : false
            },
            //模式1下 元角分模式是否开启
            unitIsOpen(){
                return this.$store.state.setting.unit_isOpen == 1 ? true : false
            },
            //模式2下每注的最低金额
            minMoney(){
                return this.$store.state.setting.mode2_min_money
            },
            //返点值
            rebateVal(){
                return this.$store.getters.rebateVal
            },
            //奖金百分比
            percent(){
                return this.$store.getters.maxRebate
            },
            //单位
            label(){
                return this.$store.state.lottery.label
            },
            //单位值
            value(){
                return this.$store.state.lottery.value
            },
            //比例
            scale(){
                return this.$store.getters.getScale
            },
            //倍数
            multiple(){
                return this.$store.state.lottery.multiple
            },
            //投注总额
            totalMoney(){
                var count = 2 * this.$bet.accMul(this.notes,this.multiple)
                var s  = this.$bet.accDiv(count,this.scale)
                return s
            },
            //注数计算
            notes(){
                return this.betNum.length
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            //选号篮数量
            badge(){
                return this.$store.state.lottery.betArr.length
            },
            divVal:function () {
                return this.betModel == 1 ? 1 : 2//模式2下奖金减半
            },
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && this.unitIsOpen || this.rebateIsOpen && this.isRebate ? 89 : 52 //底部高度
                return this.$store.state.clientHeight - 102 - h
            },
        },
        methods:{
            //奖金计算方法
            handleGain:function (g) {
                return this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accDiv(this.$bet.accMul(Number(g),this.percent),this.scale),this.divVal,5) : this.$bet.accDiv(Number(g),this.divVal,5)
            },
            addBet(num,gain){
                var list ={}
                list['num'] = num; //投注号码
                if(this.betModel == 2){
                    list['money'] = this.minMoney; //投注号码
                }
                list['gain'] = this.betModel == 2 ? this.$bet.accDiv(gain,this.divVal,5)  : gain //奖金
                this.betArr.push(list);
            },
            //投注选号状态
            doBet(num,s,gain){
                var chose = this.betNum.indexOf(num)
                if(chose > -1){
                    this.betNum.splice(chose, 1)
                    this.betArr.splice(chose, 1)
                    this.gainArr.splice(chose, 1)
                }else {
                    this.betNum.push(num)
                    this.gainArr.push(gain)
                    this.addBet(num,gain)
                }
                this.$emit('change-gain',this.gainArr.join(',')) //改变最高奖金显示
                //改变球的选中状态
                this.ball[s].selected = !this.ball[s].selected
            },
            //清空
            clear(){
                this.betNum =[];
                this.betArr =[];
                this.gainArr =[];
                this.$emit('change-gain','') //改变最高奖金显示
                for(var i in this.ball){
                    this.ball[i].selected = false
                }
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes < 1){
                    this.$toast('请至少选择1注投注号码！');
                    return
                }
                for(let i in this.betArr){
                    if(this.betModel == 1){ //模式1
                        if(this.unitIsOpen){ //元角分厘开启
                            this.betArr[i]['money'] = this.$bet.accMul( this.$bet.accDiv(2,this.scale),this.multiple); //投注金额
                            this.betArr[i]['unit'] = this.label; //投注单位
                            this.betArr[i]['unit_value'] = this.value; //投注单位
                            this.betArr[i]['multiple'] = this.multiple; //投注倍数
                            let gain
                            if(this.rebateIsOpen){
                                gain = this.$bet.accDiv(this.$bet.accMul(this.$bet.accMul(this.betArr[i].gain,this.multiple),this.percent),this.scale,5);
                            }else {
                                gain = this.$bet.accDiv(this.$bet.accMul(this.betArr[i].gain,this.multiple),this.scale,5);
                            }
                            this.betArr[i]['gain']  = gain
                        }else { //默认模式
                            this.betArr[i]['money'] = 2
                            let gain
                            if(this.rebateIsOpen){
                                gain = this.$bet.accMul(this.betArr[i].gain,this.percent,5)
                            }else {
                                gain = this.betArr[i].gain
                            }
                            this.betArr[i]['gain'] = gain
                        }
                    }
                    if(this.betModel == 2){ //模式2
                        let gain
                        if(this.rebateIsOpen){
                            gain = this.$bet.accMul(this.betArr[i].gain, this.percent, 5)
                        }else {
                            gain = this.betArr[i].gain
                        }
                        this.betArr[i]['gain'] = gain
                    }
                    this.betArr[i]['notes'] = 1; //注数
                    this.betArr[i]['type_text'] = '和值';
                    this.betArr[i]['type'] = this.type; //投注type类型
                    if(this.rebateIsOpen && this.isRebate){
                        this.betArr[i]['rebate'] = this.rebateVal //用户返点
                    }
                }
                this.$store.commit('pushBetArr',this.betArr)
                this.clear(); //清空当前选号
            },
            // 立即投注
            submitOrder(){
                if(this.badge < 1 && this.notes < 1){
                    this.$messagebox({
                        title: '提示',
                        message: '至少选择1注投注号码！',
                        confirmButtonText:'我知道了'
                    });
                    return
                }
                if(this.notes > 0){
                    this.addBetBasket();
                }
                this.$router.push({
                    path:'/betOrder',
                    query:{
                        cz:(this.$route.path).replace("/",""),
                        type:this.type,
                        name:this.name,
                        title:this.title,
                        betModel:this.betModel
                    }
                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
