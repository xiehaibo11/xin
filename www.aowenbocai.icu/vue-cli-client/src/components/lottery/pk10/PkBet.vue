<!--pk10 投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                <span v-if="type==1">选号与开奖号码中第一位一致即中奖，奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==2">猜每期开奖号码前二名。按位置命中1-2位即中奖。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==3">猜每期开奖号码前三名。按位置命中1-3位即中奖。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==4">猜每期开奖号码前四名，按位置命中1-4位即中奖。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==5">猜每期开奖号码前五名，按位置命中1-5位即中奖。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==6">猜每期开奖号码前六名，按位置命中1-6位即中奖。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==7">猜每期开奖号码前七名，按位置命中1-7位即中奖。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==8">猜每期开奖号码前八名，按位置命中1-8位即中奖。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==9">猜每期开奖号码前九名，按位置命中1-9位即中奖。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==10">猜每期开奖号码前十名，按位置命中1-10位即中奖。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==11">从冠、亚、季、四、五、六、七、八、九、十任意位置上任意选择一个或一个以上号码。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==2.2">猜每期开奖的前二名，按位置全部命中。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==3.2">猜每期开奖的前三名，按位置全部命中。奖金<em class="red">{{gain}}</em></span>
                <span v-if="type==4.2">猜每期开奖的前四名，按位置全部命中。奖金<em class="red">{{gain}}</em></span>
                {{lotteryUnit}}
            </div>
            <bet-balls :type="parseInt(type)" :wz="1" :miss="miss" v-model="betNum1"></bet-balls><!--第一位-->
            <bet-balls :type="parseInt(type)"  :wz="2" :miss="miss" v-model="betNum2" v-if="parseInt(type) > 1"></bet-balls><!--第二位-->
            <bet-balls :type="parseInt(type)" :wz="3" :miss="miss" v-model="betNum3" v-if="parseInt(type) > 2"></bet-balls><!--第三位-->
            <bet-balls :type="parseInt(type)" :wz="4" :miss="miss" v-model="betNum4" v-if="parseInt(type) > 3"></bet-balls><!--第四位-->
            <bet-balls :type="parseInt(type)" :wz="5" :miss="miss" v-model="betNum5" v-if="parseInt(type) > 4"></bet-balls><!--第五位-->
            <bet-balls :type="parseInt(type)" :wz="6" :miss="miss" v-model="betNum6" v-if="parseInt(type) > 5"></bet-balls><!--第六位-->
            <bet-balls :type="parseInt(type)" :wz="7" :miss="miss" v-model="betNum7" v-if="parseInt(type) > 6"></bet-balls><!--第七位-->
            <bet-balls :type="parseInt(type)" :wz="8" :miss="miss" v-model="betNum8" v-if="parseInt(type) > 7"></bet-balls><!--第八位-->
            <bet-balls :type="parseInt(type)" :wz="9" :miss="miss" v-model="betNum9" v-if="parseInt(type) > 8"></bet-balls><!--第九位-->
            <bet-balls :type="parseInt(type)" :wz="10" :miss="miss" v-model="betNum10" v-if="parseInt(type) > 9"></bet-balls><!--第十位-->
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
    import betBalls from 'components/lottery/pk10/BetBalls.vue' //选号组件
    import SelectUnit from 'components/lottery/SelectUnit.vue' //倍数、单位设置组件
    import RebateRange from 'components/lottery/RebateRange.vue' //返点设置
    export default {
        components:{
            betBalls,
            SelectUnit,
            RebateRange
        },
        props:['type','gain','miss','title','text','name','small','betModel','sign'], //type值,最少选择的号码个数,奖金设置值
        data () {
            return {
                betNum1 : [],
                betNum2 : [],
                betNum3 : [],
                betNum4 : [],
                betNum5 : [],
                betNum6 : [],
                betNum7 : [],
                betNum8 : [],
                betNum9 : [],
                betNum10 : [],
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
            num1(){
                return this.betNum1.length
            },
            num2(){
                return this.betNum2.length
            },
            num3(){
                return this.betNum3.length
            },
            num4(){
                return this.betNum4.length
            },
            num5(){
                return this.betNum5.length
            },
            num6(){
                return this.betNum6.length
            },
            num7(){
                return this.betNum7.length
            },
            num8(){
                return this.betNum8.length
            },
            num9(){
                return this.betNum9.length
            },
            num10(){
                return this.betNum10.length
            },
            //选择注数计算
            notes(){
                if(this.type == 1){
                    return this.$bet.getCount(this.$bet.Zuhe(this.betNum1))
                }
                if(this.type == 2 || this.type == 2.2){
                    if(!this.num1 || !this.num2){
                        return 0
                    }else {
                        return this.$bet.getCount(this.$bet.Zuhe(this.betNum1,this.betNum2))
                    }
                }
                if(this.type == 3 || this.type == 3.2){
                    if(!this.num1 || !this.num2 || !this.num3){
                        return 0
                    }else {
                        return this.$bet.getCount(this.$bet.Zuhe(this.betNum1,this.betNum2,this.betNum3))
                    }
                }
                if(this.type == 4 || this.type == 4.2){
                    if(!this.num1 || !this.num2 || !this.num3 || !this.num4){
                        return 0
                    }else {
                        return this.$bet.getCount(this.$bet.Zuhe(this.betNum1,this.betNum2,this.betNum3,this.betNum4))
                    }
                }
                if(this.type == 5){
                    if(!this.num1 || !this.num2 || !this.num3 || !this.num4 || !this.num5){
                        return 0
                    }else {
                        return this.$bet.getCount(this.$bet.Zuhe(this.betNum1,this.betNum2,this.betNum3,this.betNum4,this.betNum5))
                    }
                }
                if(this.type == 6){
                    if(!this.num1 || !this.num2 || !this.num3 || !this.num4|| !this.num5 || !this.num6){
                        return 0
                    }else {
                        return this.$bet.getCount(this.$bet.Zuhe(this.betNum1,this.betNum2,this.betNum3,this.betNum4,this.betNum5,this.betNum6))
                    }
                }
                if(this.type == 7){
                    if(!this.num1 || !this.num2 || !this.num3 || !this.num4|| !this.num5 || !this.num6 || !this.num7){
                        return 0
                    }else {
                        return this.$bet.getCount(this.$bet.Zuhe(this.betNum1,this.betNum2,this.betNum3,this.betNum4,this.betNum5,this.betNum6,this.betNum7))
                    }
                }
                if(this.type == 8){
                    if(!this.num1 || !this.num2 || !this.num3 || !this.num4|| !this.num5 || !this.num6 || !this.num7 || !this.num8){
                        return 0
                    }else {
                        return this.$bet.getCount(this.$bet.Zuhe(this.betNum1,this.betNum2,this.betNum3,this.betNum4,this.betNum5,this.betNum6,this.betNum7,this.betNum8))
                    }
                }
                if(this.type == 9){
                    if(!this.num1 || !this.num2 || !this.num3 || !this.num4|| !this.num5 || !this.num6 || !this.num7 || !this.num8 || !this.num9){
                        return 0
                    }else {
                        return this.$bet.getCount(this.$bet.Zuhe(this.betNum1,this.betNum2,this.betNum3,this.betNum4,this.betNum5,this.betNum6,this.betNum7,this.betNum8,this.betNum9))
                    }
                }
                if(this.type == 10){
                    if(!this.num1 || !this.num2 || !this.num3 || !this.num4|| !this.num5 || !this.num6 || !this.num7 || !this.num8 || !this.num9 || !this.num10){
                        return 0
                    }else {
                        return this.$bet.getCount(this.$bet.Zuhe(this.betNum1,this.betNum2,this.betNum3,this.betNum4,this.betNum5,this.betNum6,this.betNum7,this.betNum8,this.betNum9,this.betNum10))
                    }
                }
                if(this.type == 11){
                    return this.num1 + this.num2 + this.num3 + this.num4 + this.num5 + this.num6 + this.num7 + this.num8 + this.num9 + this.num10
                }
            },
            //投注总额
            totalMoney(){
                var count = 2 * this.$bet.accMul(Number(this.notes),Number(this.multiple))
                var s  = this.$bet.accDiv(count,Number(this.scale))
                return s || 0
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
                this.$set(this,'betNum1',[])
                this.$set(this,'betNum2',[])
                this.$set(this,'betNum3',[])
                this.$set(this,'betNum4',[])
                this.$set(this,'betNum5',[])
                this.$set(this,'betNum6',[])
                this.$set(this,'betNum7',[])
                this.$set(this,'betNum8',[])
                this.$set(this,'betNum9',[])
                this.$set(this,'betNum10',[])
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes<1){
                    this.$toast('每位至少选择1个号码！');
                    return
                }
                let type_text =  this.text;
                let betNum,d1,d2,d3,d4,d5,d6,d7,d8,d9,d10;
                if(this.num1){
                    d1 =  this.betNum1.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d1 = this.type == 11 ? '-|' : ''
                }
                if(this.num2){
                    d2 = this.betNum2.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d2 = this.type == 11 ? '-|' : ''
                }
                if(this.num3){
                    d3 = this.betNum3.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d3 = this.type == 11 ? '-|' : ''
                }
                if(this.num4){
                    d4 = this.betNum4.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d4 = this.type == 11 ? '-|' : ''
                }
                if(this.num5){
                    d5 = this.betNum5.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d5 = this.type == 11 ? '-|' : ''
                }
                if(this.num6){
                    d6 = this.betNum6.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d6 = this.type == 11 ? '-|' : ''
                }
                if(this.num7){
                    d7 = this.betNum7.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d7 = this.type == 11 ? '-|' : ''
                }
                if(this.num8){
                    d8 = this.betNum8.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d8 = this.type == 11 ? '-|' : ''
                }
                if(this.num9){
                    d9 = this.betNum9.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d9 = this.type == 11 ? '-|' : ''
                }
                if(this.num10){
                    d10 = this.betNum10.sort(function(a,b){return a-b}).join(',') + '|';
                }else {
                    d10 = this.type == 11 ? '-|' : ''
                }
                var plans = d1 + d2 + d3 + d4 + d5 + d6 + d7 + d8 + d9 + d10
                betNum = plans.substring(0,plans.length-1)

                let data = {}
                data['num'] = betNum
                data['notes'] =this.notes
                data['type_text'] = type_text
                data['type'] = this.sign
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
                        type:parseInt(this.type),
                        name:this.name,
                        sign:this.sign,
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
