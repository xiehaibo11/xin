<!--快3 三不同号4、二不同号8 组件投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="ks-ball contentH" :style="{height:contentH + 'px'}">
            <div class="g-bet-item-tips tl f-sm g-c-light" v-if="type == 4">至少选择3个不同号码投注，所选号码与开奖号码一致即中奖，赔率<em class="yellow">{{gain}}</em>。</div>
            <div class="g-bet-item-tips tl f-sm g-c-light" v-if="type == 8">至少选择2个号码投注，与开奖号码的任意2位一致即中奖，赔率<em class="yellow">{{gain}}</em>。</div>
            <ul class="balls-box cf">
                <li v-for="(item,index) in ball" :key="index" style="width: 14.5%;margin: 10px 1%">
                    <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index)">
                        <p><b class="f-large">{{item.num}}</b></p>
                    </a>
                </li>
            </ul>
        </div>
        <!--选号 end-->
        <!--底部 start-->
        <div class="bet-foot" ref="footBox">
            <div class="gain-box f-sm ks-notes">
                <div class="flex-box ks-unit">
                    <div class="flex">
                        <select-unit v-if="betModel == 1 && unitIsOpen == 1"></select-unit><!--模式1 元角分厘模式开启时显示-->
                    </div>
                    <div class="flex" v-if="rebateIsOpen && isRebate">
                        <rebate-range :gain="gain"></rebate-range>
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
        props:['type','gain','title','text','name','betModel','number'],
        components:{
            SelectUnit,
            RebateRange
        },
        data () {
            return {
                ball: [{num:'1',selected:false},{num:'2',selected:false},{num:'3',selected:false},{num:'4',selected:false},{num:'5',selected:false},{num:'6',selected:false}],
                betNum:[] //投注号码
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
                return s || 0
            },
            //注数计算
            notes(){
                return this.$bet.combination(this.betNum.length,parseInt(this.number))
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            //选号篮数量
            badge(){
                return this.$store.state.lottery.betArr.length
            },
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && this.unitIsOpen || this.rebateIsOpen && this.isRebate ? 89 : 52 //底部高度
                return this.$store.state.clientHeight - 102 - h
            },
        },
        methods:{
            //投注选号状态
            doBet(num,s){
                var chose = this.betNum.indexOf(num)
                if(chose > -1){
                    this.betNum.splice(chose, 1)
                }else {
                    this.betNum.push(num)
                }
                //改变球的选中状态
                this.ball[s].selected = !this.ball[s].selected
            },
            //清空
            clear(){
                this.betNum =[];
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
                let betNum = this.betNum.sort(function(a,b){return a-b}).join(',')

                let data = {}
                data['num'] = betNum
                data['notes'] =this.notes
                data['type_text'] =this.text
                data['type'] =this.type
                if(this.rebateIsOpen && this.isRebate){
                    data['rebate'] =this.rebateVal
                }
                if(this.betModel == 1){ //模式1
                    if(this.unitIsOpen){ //元角分模式
                        data['multiple'] = this.multiple//投注倍数
                        data['unit'] = this.label//投注单位
                        data['unit_value'] = this.value //投注单位值
                        data['money'] = this.$bet.accMul(this.$bet.accDiv(2,this.scale),this.multiple)  * this.notes //投注金额,
                        data['gain'] = this.$bet.accMul(this.multiple,this.gain) //可中金额
                    }else { //默认模式
                        data['multiple'] = this.multiple //投注倍数
                        data['money'] = 2  * this.notes //投注金额,
                        data['gain'] = this.gain //可中金额
                    }
                }
                if(this.betModel == 2){ //模式2
                    data['money'] = this.minMoney //投注金额,
                    data['gain'] = this.gain //可中金额
                }
                this.$store.commit('pushBetNum',data)
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
