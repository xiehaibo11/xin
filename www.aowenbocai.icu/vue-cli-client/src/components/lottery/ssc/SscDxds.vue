<!--时时彩 大小单双 复式投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                分别从个、十位中前一种性质组成一注投注号码，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}
            </div>
            <div class="bet-box flex-box">
                <div class="label">
                    <p>十 位</p>
                    <p>遗 漏</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in balls" :key="index">
                            <a class="balls" :class="{'selected':item.selected}" @click="doBetS(item.num,index)">{{item.num}}</a>
                            <template v-if="miss !== 0">
                                <i class="f-mini c-3"  :class="{'red':miss.dx[0].da>10}" v-if="index==0">{{miss.dx[0].da}}</i>
                                <i class="f-mini c-3" :class="{'red':miss.dx[0].sm>10}" v-if="index==1">{{miss.dx[0].sm}}</i>
                                <i class="f-mini c-3" :class="{'red':miss.dx[0].dan>10}" v-if="index==2">{{miss.dx[0].dan}}</i>
                                <i class="f-mini c-3" :class="{'red':miss.dx[0].shuang>10}" v-if="index==3">{{miss.dx[0].shuang}}</i>
                            </template>
                            <template v-else>
                                <i class="f-mini c-3">--</i>
                            </template>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bet-box flex-box">
                <div class="label">
                    <p>个 位</p>
                    <p>遗 漏</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in ballg" :key="index">
                            <a class="balls" :class="{'selected':item.selected}" @click="doBetG(item.num,index)">{{item.num}}</a>
                            <template v-if="miss !== 0">
                                <i class="f-mini c-3" :class="{'red':miss.dx[1].da>10}" v-if="index==0">{{miss.dx[1].da}}</i>
                                <i class="f-mini c-3" :class="{'red':miss.dx[1].sm>10}" v-if="index==1">{{miss.dx[1].sm}}</i>
                                <i class="f-mini c-3" :class="{'red':miss.dx[1].dan>10}" v-if="index==2">{{miss.dx[1].dan}}</i>
                                <i class="f-mini c-3" :class="{'red':miss.dx[1].shuang>10}" v-if="index==3">{{miss.dx[1].shuang}}</i>
                            </template>
                            <template v-else>
                                <i class="f-mini c-3">--</i>
                            </template>
                        </li>
                    </ul>
                </div>
            </div>
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
    import SelectUnit from 'components/lottery/SelectUnit.vue' //倍数、单位设置组件
    import RebateRange from 'components/lottery/RebateRange.vue' //返点设置
    export default {
        components:{
            SelectUnit,
            RebateRange
        },
        props:['type','gain','miss','title','text','name','betModel'], //type值,最少选择的号码个数,奖金设置值
        data () {
            return {
                balls: [
                    {num : '大',selected : false},
                    {num : '小',selected : false},
                    {num : '单',selected : false},
                    {num : '双',selected : false}
                ],
                ballg: [
                    {num : '大',selected : false},
                    {num : '小',selected : false},
                    {num : '单',selected : false},
                    {num : '双',selected : false}
                ],
                betNumS:[],
                betNumG:[],
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
                return s || 0
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
                return this.betNumS.length * this.betNumG.length
            },
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && this.unitIsOpen || this.rebateIsOpen && this.isRebate ? 89 : 52 //底部高度
                return this.$store.state.clientHeight - 100 - h
            },
        },
        methods:{
            //投注选号状态
            doBetS(num,s){
                var chose = this.betNumS.indexOf(num)
                if(chose > -1){
                    this.betNumS.splice(chose, 1)
                }else {
                    this.betNumS.push(num)
                }
                //改变球的选中状态
                this.balls[s].selected = !this.balls[s].selected
            },
            doBetG(num,s){
                var chose = this.betNumG.indexOf(num)
                if(chose > -1){
                    this.betNumG.splice(chose, 1)
                }else {
                    this.betNumG.push(num)
                }
                //改变球的选中状态
                this.ballg[s].selected = !this.ballg[s].selected
            },
            //清空选号
            clear(){
                this.betNumS =[];
                this.betNumG =[];
                for(var i in this.balls){
                    this.balls[i].selected = false
                }
                for(var i in this.ballg){
                    this.ballg[i].selected = false
                }
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes < 1){
                    this.$toast('每位至少选择1个号码！');
                    return
                }
                var betNum = this.betNumS.join(',') + '|'+ this.betNumG.join(',');

                let data = {}
                data['num'] = betNum
                data['num_text'] = betNum
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
