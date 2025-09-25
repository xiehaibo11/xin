<!--时时彩 5星通/5星直/3星直/二星直/一星/定位胆 复式投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                <template v-if="type==1.1">
                    <em>每行至少选1号码，三个奖级通吃，五次中奖机会，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}</em>
                </template>
                <template v-else>
                    <em v-if="type==1.2|| type==2.1 || type==3.1 || type==4.1 || type==5.1 || type==10.1">每行至少选1个号码</em>
                    <em v-if="type==6.1">从0~9任意选择1个号码为一个投注号</em>
                    <em v-if="type==8.1">在万位、千位、百位、十位、个位任意位置上任意选择1个或1个以上号码</em>
                    ，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}
                </template>
            </div>
            <bet-balls v-if="type == 1.1 || type == 1.2 || type == 8.1 || type == 2.1 || type == 10.1" :wz="5" :miss="miss" v-model="betNumW"></bet-balls>
            <bet-balls v-if="type == 1.1 || type == 1.2 || type == 8.1 || type == 2.1 || type == 3.1 || type == 10.1" :wz="4" :miss="miss" v-model="betNumQ"></bet-balls>
            <bet-balls v-if="type == 1.1 || type == 1.2 || type == 8.1 || type == 2.1 || type == 3.1 || type == 4.1" :wz="3" :miss="miss" v-model="betNumB"></bet-balls>
            <bet-balls v-if="type == 1.1 || type == 1.2 || type == 8.1 || type == 3.1 || type == 4.1 || type == 5.1" :wz="2" :miss="miss" v-model="betNumS"></bet-balls>
            <bet-balls v-if="type == 1.1 || type == 1.2 || type == 8.1 || type == 4.1 || type == 5.1 || type == 6.1" :wz="1" :miss="miss" v-model="betNumG"></bet-balls>
        </div>
        <!--选号 end-->
        <!--底部 start-->
        <div class="bet-foot">
            <div class="gain-box f-sm border-top-1px">
                <div class="flex-box ks-unit">
                    <div class="flex">
                        <select-unit v-if="betModel == 1 && unitIsOpen"></select-unit><!--模式1 元角分厘模式开启时显示-->
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
    import betBalls from 'components/lottery/ssc/BetBalls.vue' //选号组件
    import SelectUnit from 'components/lottery/SelectUnit.vue' //倍数、单位设置组件
    import RebateRange from 'components/lottery/RebateRange.vue' //返点设置
    export default {
        components:{
            betBalls,
            SelectUnit,
            RebateRange
        },
        props:['type','gain','miss','title','text','name','betModel'], //type值,最少选择的号码个数,奖金设置值
        data () {
            return {
                betNumW : [], //万位投注数据
                betNumQ : [], //千位投注数据
                betNumB : [], //百位投注数据
                betNumS : [], //十位投注数据
                betNumG : [], //个位投注数据
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

            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            //选号篮数量
            badge(){
                return this.$store.state.lottery.betArr.length
            },
            //选择注数计算
            notes(){
                if(parseInt(this.type) == 1){ // 五星通选1.1/五星直选1.2 （万位、千位、百位、十位、个位）
                    return this.betNumG.length * this.betNumS.length * this.betNumB.length * this.betNumQ.length * this.betNumW.length
                }
                if(this.type == 2.1){  // 前三-直选（万位、千位、百位）
                    return this.betNumW.length * this.betNumQ.length * this.betNumB.length
                }
                if(this.type == 3.1){  // 中三-直选（千位、百位、十位）
                    return this.betNumQ.length * this.betNumB.length * this.betNumS.length
                }
                if(this.type == 4.1){  // 后三-直选（百位、十位、个位）
                    return this.betNumB.length * this.betNumS.length * this.betNumG.length
                }
                if(this.type == 10.1) {  // 前二直选（万位、千位）
                    return this.betNumW.length * this.betNumQ.length
                }
                if(this.type == 5.1) {  // 二星直选（十位、个位）
                    return this.betNumG.length * this.betNumS.length
                }
                if(this.type == 6.1){  // 一星（个位）
                    return this.betNumG.length
                }
                if(this.type == 8.1){ //定位胆
                    return this.betNumG.length + this.betNumS.length + this.betNumB.length + this.betNumQ.length + this.betNumW.length
                }
            },
            //投注总额
            totalMoney(){
                var count = this.$bet.accMul(2 , this.$bet.accMul(Number(this.notes),this.multiple))
                return this.$bet.accDiv(count,this.scale)
            },
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && this.unitIsOpen || this.rebateIsOpen && this.isRebate ? 89 : 52 //底部高度
                return this.$store.state.clientHeight - 100 - h
            },
        },
        methods:{
            //添加到选区后清空
            clear(){
                this.$set(this,'betNumG',[])
                this.$set(this,'betNumS',[])
                this.$set(this,'betNumB',[])
                this.$set(this,'betNumQ',[])
                this.$set(this,'betNumW',[])
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes<1){
                    this.$toast('每位至少选择1个号码！');
                    return
                }
                let type = this.type;
                let type_text =  this.text;
                let betNum, gw,sw,bw,qw,ww,betting;
                ww = this.betNumW.length ? this.betNumW.sort().join(",") + '|' : '';
                qw = this.betNumQ.length ? this.betNumQ.sort().join(",") + '|' : '';
                bw = this.betNumB.length ? this.betNumB.sort().join(",") + '|' : '';
                sw = this.betNumS.length ? this.betNumS.sort().join(",") + '|' : '';
                gw = this.betNumG.length ? this.betNumG.sort().join(",") : '';
                betNum = ww + qw + bw + sw + gw;
                if(this.type == 2.1 || this.type == 3.1 || this.type == 10.1){
                    betting =  betNum.substr(0, betNum.length - 1)
                }else if(this.type == 8.1){
                    ww = this.betNumW.length ? this.betNumW.sort().join(",") :  '-'
                    qw = this.betNumQ.length ? this.betNumQ.sort().join(",") :  '-'
                    bw = this.betNumB.length ? this.betNumB.sort().join(",") :  '-'
                    sw = this.betNumS.length ? this.betNumS.sort().join(",") :  '-'
                    gw = this.betNumG.length ? this.betNumG.sort().join(",") :  '-'
                    betting = ww + '|' + qw + '|' + bw + '|' + sw + '|' + gw
                }else {
                    betting =  betNum
                }

                let data = {}
                data['num'] = betting
                data['notes'] =this.notes
                data['type_text'] =this.text
                data['type'] =type
                if(this.rebateIsOpen && this.isRebate){
                    data['rebate'] =this.rebateVal
                }
                if(this.betModel == 1){ //模式1
                    if(this.unitIsOpen){ //元角分模式
                        data['multiple'] = this.multiple//投注倍数
                        data['unit'] = this.label//投注单位
                        data['unit_value'] = this.value //投注单位值
                        data['money'] = this.$bet.accMul(this.$bet.accDiv(2,this.scale),this.multiple)  * this.notes //投注金额,
                    }else { //默认模式
                        data['multiple'] = this.multiple //投注倍数
                        data['money'] = 2  * this.notes //投注金额
                    }
                }
                if(this.betModel == 2){ //模式2
                    data['money'] = this.minMoney //投注金额
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
