<!--时时彩 大小单双 复式投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                玩法提示：每期开出3个号码作为开奖号码，3个号码之和即为特码。
                <template v-if="type == 1">对特码的大（14-27）、小（0-13）、单、双、极大（22-27）、极小（0-5）形态进行投注，任选1个形态组成一注。
                    所选形态与开奖特码的形态相同，即为中奖，最高奖金<em class="red">{{maxGain}}</em>{{lotteryUnit}}，如开出小单（13）、大双（14 ）中奖后保本。
                </template>
                <template v-if="type == 2">对开奖特码的绿波（1、4、7、10、16、19、22、25），蓝波（2、5、8、11、17、20、23、26），红波（3、6、9、12、15、18、21、24）
                    的色波进行投注，任选1个色波组成一注。所选色波与开奖特码色波相同，即为中奖。如开出灰波（0、13、14、27）则投注任何色波均视为不中奖，最高奖金<em class="red">{{maxGain}}</em>{{lotteryUnit}}。
                </template>
                <template v-if="type == 3">
                    投注当期的3个开奖号为豹子号码，3个开奖号码为同一号码，即为中奖，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}。
                </template>
            </div>
            <ul class="balls-box cf" v-if="type == 1">
                <li v-for="(item,index) in ball" :key="index" :class="{'ml-1' : index == 8}">
                    <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index,item.gain)">
                        <p><em class="f-large">{{item.num}}</em></p>
                        <p class="gain f-mini">赔{{handleGain(item.gain)}}</p>
                    </a>
                </li>
            </ul>
            <ul class="balls-box cf" v-if="type == 2">
                <li v-for="(item,index) in ball2" :key="index" :class="{'ml' : index == 0}">
                    <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index,item.gain)">
                        <p><em class="f-large">{{item.num}}</em></p>
                        <p class="gain f-mini">赔{{handleGain(item.gain)}}</p>
                    </a>
                </li>
            </ul>
            <ul class="balls-box cf" v-if="type == 3" style="width: 25%">
                <li v-for="(item,index) in ball3" :key="index" style="width: 100%">
                    <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index,gain)">
                        <p><em class="f-large">{{item.num}}</em></p>
                        <p class="gain f-mini">赔{{gain}}</p>
                    </a>
                </li>
            </ul>
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
        components:{
            SelectUnit,
            RebateRange
        },
        props:['type','gain','title','text','name','betModel','maxGain'], //type值,最少选择的号码个数,奖金设置值
        data () {
            return {
                ball:[{num:'大',selected:false,gain:this.gain.toString().split(',')[0]},{num:'小',selected:false,gain:this.gain.toString().split(',')[1]},
                    {num:'单',selected:false,gain:this.gain.toString().split(',')[2]},{num:'双',selected:false,gain:this.gain.toString().split(',')[3]},
                    {num:'大单',selected:false,gain:this.gain.toString().split(',')[4]},{num:'小单',selected:false,gain:this.gain.toString().split(',')[5]},
                    {num:'大双',selected:false,gain:this.gain.toString().split(',')[6]},{num:'小双',selected:false,gain:this.gain.toString().split(',')[7]},
                    {num:'极大',selected:false,gain:this.gain.toString().split(',')[8]},{num:'极小',selected:false,gain:this.gain.toString().split(',')[9]},
                ],
                ball2:[{num:'红波',selected:false,gain:this.gain.toString().split(',')[0]},{num:'绿波',selected:false,gain:this.gain.toString().split(',')[1]},
                    {num:'蓝波',selected:false,gain:this.gain.toString().split(',')[2]}],
                ball3:[{num:'豹子',selected:false}],
                betNum:[], //投注号码
                betNum2:[], //投注号码
                betNum3:[], //投注号码
                betArr:[],//投注组
                betArr2:[],//投注组
                betArr3:[],//投注组
                gainArr:[],//混合投注奖金数组
                gainArr2:[],//色波投注奖金数组
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
            //注数计算
            notes:function(){
                if(this.type == 1){
                    return this.betNum.length
                }
                if(this.type == 2){
                    return this.betNum2.length
                }
                if(this.type == 3){
                    return this.betNum3.length
                }else {
                    return 0
                }
            },
            divVal:function () {
                return this.betModel == 1 ? 1 : 2//模式2下奖金减半
            },
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && this.unitIsOpen || this.rebateIsOpen && this.isRebate ? 89 : 52 //底部高度
                return this.$store.state.clientHeight - 100 - h
            },
        },
        methods:{
            //奖金计算方法
            handleGain:function (g) {
                return this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accDiv(this.$bet.accMul(Number(g),Number(this.percent)),this.scale),this.divVal,5) : this.$bet.accDiv(Number(g),this.divVal,5)
            },
            addBet(num,gain){
                var arr = []
                if(this.type == 1){
                    arr = this.betArr
                }
                if(this.type == 2){
                    arr = this.betArr2
                }
                if(this.type == 3){
                    arr = this.betArr3
                }
                var list ={}
                list['num'] = num; //投注号码
                if(this.betModel == 2){
                    list['money'] = this.minMoney; //投注号码
                }
                list['gain'] = this.betModel == 2 ? this.$bet.accDiv(gain,this.divVal,5)  : gain //奖金
                arr.push(list);
            },
            //投注选号状态
            doBet(num,s,gain,text){
                if(this.type == 1){
                    var chose = this.betNum.indexOf(num)
                    if(chose > -1){
                        this.betNum.splice(chose, 1)
                        this.betArr.splice(chose, 1)
                        this.gainArr.splice(chose, 1)
                    }else {
                        this.betNum.push(num)
                        this.gainArr.push(gain)
                        this.addBet(num,gain,text)
                    }
                    this.$emit('change-gain',this.gainArr.join(',')) //改变最高奖金显示
                    //改变球的选中状态
                    this.ball[s].selected = !this.ball[s].selected
                }
                if(this.type == 2){
                    var chose = this.betNum2.indexOf(num)
                    if(chose > -1){
                        this.betNum2.splice(chose, 1)
                        this.betArr2.splice(chose, 1)
                        this.gainArr2.splice(chose, 1)
                    }else {
                        this.betNum2.push(num)
                        this.gainArr2.push(gain)
                        this.addBet(num,gain,text)
                    }
                    this.$emit('change-gain',this.gainArr2.join(',')) //改变最高奖金显示
                    //改变球的选中状态
                    this.ball2[s].selected = !this.ball2[s].selected
                }
                if(this.type == 3){
                    var chose = this.betNum3.indexOf(num)
                    if(chose > -1){
                        this.betNum3.splice(chose, 1)
                        this.betArr3.splice(chose, 1)
                    }else {
                        this.betNum3.push(num)
                        this.addBet(num,gain,text)
                    }
                    //改变球的选中状态
                    this.ball3[s].selected = !this.ball3[s].selected
                }
            },
            //清空选号
            clear(){
                if(this.type == 1){
                    this.betNum =[];
                    this.betArr =[];
                    this.gainArr = [];
                    this.$emit('change-gain','') //改变最高奖金显示
                    for(var i in this.ball){
                        this.ball[i].selected = false
                    }
                }
                if(this.type == 2){
                    this.betNum2 =[];
                    this.betArr2 =[];
                    this.gainArr2 = [];
                    this.$emit('change-gain','') //改变最高奖金显示
                    for(var i in this.ball2){
                        this.ball2[i].selected = false
                    }
                }
                if(this.type == 3){
                    this.betNum3 =[];
                    this.betArr3 =[];
                    for(var i in this.ball3){
                        this.ball3[i].selected = false
                    }
                }
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes < 1){
                    this.$toast('每位至少选择1个号码！');
                    return
                }
                var betArr = []
                if(this.type == 1){
                    betArr = this.betArr
                }
                if(this.type == 2){
                    betArr = this.betArr2
                }
                if(this.type == 3){
                    betArr = this.betArr3
                }
                for(let i in betArr){
                    if(this.betModel == 1){ //模式1
                        if(this.unitIsOpen){ //元角分厘开启
                            betArr[i]['money'] = this.$bet.accMul( this.$bet.accDiv(2,this.scale),this.multiple); //投注金额
                            betArr[i]['unit'] = this.label; //投注单位
                            betArr[i]['unit_value'] = this.value; //投注单位
                            betArr[i]['multiple'] = this.multiple; //投注倍数
                            let gain
                            if(this.type == 1 || this.type == 2){
                                if(this.rebateIsOpen){
                                    gain = this.$bet.accDiv(this.$bet.accMul(this.$bet.accMul(betArr[i].gain,this.multiple),this.percent),this.scale,5);
                                }else {
                                    gain = this.$bet.accDiv(this.$bet.accMul(betArr[i].gain,this.multiple),this.scale,5);
                                }
                            }else {
                                gain = this.$bet.accMul(this.gain,this.multiple,5)
                            }
                            betArr[i]['gain']  = gain
                        }else { //默认模式
                            betArr[i]['money'] = 2
                            let gain
                            if(this.type == 1 || this.type == 2){
                                gain = this.rebateIsOpen ? this.$bet.accMul(betArr[i].gain,this.percent,5) : betArr[i].gain
                            }else {
                                gain = betArr[i].gain
                            }
                            betArr[i]['gain'] = gain
                        }
                    }
                    if(this.betModel == 2){ //模式2
                        let gain
                        if(this.type == 1 || this.type == 2){
                            gain = this.rebateIsOpen ? this.$bet.accMul(betArr[i].gain, this.percent, 5) : betArr[i].gain
                        }else {
                            gain = betArr[i].gain
                        }
                        betArr[i]['gain'] = gain
                    }
                    betArr[i]['notes'] = 1; //注数
                    betArr[i]['type_text'] = this.text;
                    betArr[i]['type'] = this.type; //投注type类型
                    if(this.rebateIsOpen && this.isRebate){
                        betArr[i]['rebate'] = this.rebateVal //用户返点
                    }
                }
                this.$store.commit('pushBetArr',betArr)
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
<style scoped type="text/scss" lang="scss">
    .balls-box{
        padding: 10px;
        margin: 0 auto;
        li{
            float: left;
            width: 21%;
            margin: 5px 2%;
            &.ml{
                margin-left: 14%;
            }
            &.ml-1{
                margin-left: 27%;
            }
            a{
                display: inline-block;
                width: 100%;
                border: 1px solid #DCDFE6;
                border-radius: 3px;
                padding: 5px 0;
                text-align: center;
                color: #2d2d2d;
                background-color: #ffffff;
                .gain {
                    color: #9c9c9c;
                }
                &.selected{
                    border-color: $bColor;
                    background-color: $bColor;
                    color: #ffffff;
                    animation: scale .2s;
                    .gain{
                        color: lighten($bColor,48%);
                    }
                }
            }
        }
    }
</style>
