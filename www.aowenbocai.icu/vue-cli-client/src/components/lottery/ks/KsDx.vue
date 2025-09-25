<!--快3 二同号单选 投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="ks-ball contentH" :style="{height:contentH + 'px'}">
            <div class="g-bet-item-tips tl f-sm g-c-light" v-if="type == 7">选择1个相同号码和1个不同号码投注，选号与开奖号码一致即中奖，赔率<em class="yellow">{{gain}}</em>。</div>
            <ul class="balls-box cf">
                <li v-for="(item,index) in ball1" :key="index" style="width: 14.5%;margin: 10px 1%">
                    <a href="javascript:;" :class="[item.selected == 3 ? 'selected' : '',item.selected == 2 ? 'forbidden': '']" @click="doBet1(item.num,index)">{{item.num}}</a>
                </li>
            </ul>
            <ul class="balls-box cf">
                <li v-for="(item,index) in ball2" :key="index" style="width: 14.5%;margin: 10px 1%">
                    <a href="javascript:;" :class="[item.selected == 3 ? 'selected' : '',item.selected == 2 ? 'forbidden': '']" @click="doBet2(item.num,index)">{{item.num}}</a>
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
        props:['type','gain','title','text','name','betModel'],
        components:{
            SelectUnit,
            RebateRange
        },
        data () {
            return {
                ball1: [{num:'11',selected:1},{num:'22',selected:1},{num:'33',selected:1},{num:'44',selected:1},{num:'55',selected:1},{num:'66',selected:1}],
                ball2: [{num:'1',selected:1},{num:'2',selected:1},{num:'3',selected:1},{num:'4',selected:1},{num:'5',selected:1},{num:'6',selected:1}],
                betNum1:[], //同号投注
                betNum2:[], //不同号投注
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
            totalMoney:function(){
                var count = 2 * this.$bet.accMul(this.notes,this.multiple)
                var s  = this.$bet.accDiv(count,this.scale)
                return s
            },
            //注数计算
            notes:function(){
                return this.betNum1.length && this.betNum2.length ? this.betNum1.length * this.betNum2.length : 0
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
            //同号选号状态
            doBet1(num,index){
                var chose = this.betNum1.indexOf(num) //-1 存在
                if(chose > -1){
                    this.betNum1.splice(chose, 1);
                    this.ball2[index].selected = this.ball2[index].selected ==3  ? 2 : 1;
                    this.ball1[index].selected = this.ball1[index].selected == 1 || this.ball1[index].selected ==2  ? 3 : 1;
                }else {
                    this.betNum1.push(num)
                    this.ball2[index].selected = 2
                    this.ball1[index].selected = 3
                    if(this.betNum2.indexOf(this.ball2[index].num) > -1){
                        this.betNum2.splice(this.betNum2.indexOf(this.ball2[index].num), 1);
                    }
                }
            },
            //不同号选号状态
            doBet2(num,index){
                var chose = this.betNum2.indexOf(num) //-1 不存在
                if(chose > -1){
                    this.betNum2.splice(chose, 1);
                    this.ball1[index].selected = this.ball1[index].selected ==3  ? 2 : 1;
                    this.ball2[index].selected = this.ball2[index].selected == 1 || this.ball2[index].selected ==2  ? 3 : 1;
                }else {
                    this.betNum2.push(num)
                    this.ball1[index].selected = 2
                    this.ball2[index].selected = 3
                    if(this.betNum1.indexOf(this.ball1[index].num) > -1){
                        this.betNum1.splice(this.betNum1.indexOf(this.ball1[index].num), 1);
                    }
                }
            },
            //清空
            clear(){
                this.betNum1 =[];
                this.betNum2 =[];
                for(var i in this.ball1){
                    this.ball1[i].selected = 1
                }
                for(var i in this.ball2){
                    this.ball2[i].selected = 1
                }
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes < 1){
                    this.$toast('请至少选择1注投注号码！');
                    return
                }
                let betNum,n1,n2
                n1 = this.betNum1.sort(function(a,b){return a-b}).join(',')
                n2 = this.betNum2.sort(function(a,b){return a-b}).join(',')
                betNum = n1 + '|' + n2

                let data = {}
                data['num'] = betNum
                data['notes'] = this.notes
                data['type_text'] = this.text
                data['type'] = this.type
                if(this.rebateIsOpen && this.isRebate){
                    data['rebate'] = this.rebateVal
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
