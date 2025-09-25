<!--11选5前二直选、前三直选-->
<template>
    <div>{{text}}
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">每位至少选择1个号码，与开奖号码的前<em v-if="type == 10">两</em><em v-if="type == 12">三</em>位号码相同且顺序一致，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}</div>

            <div class="flex-box fast-select-box">
                <div class="fast-title">第一位</div>
                <ul class="flex-box fast-list">
                    <li class="flex" @click="choseAll(1)">全</li>
                    <li class="flex" @click="choseBig(1)">大</li>
                    <li class="flex" @click="choseSmall(1)">小</li>
                    <li class="flex" @click="choseJs(1)">奇</li>
                    <li class="flex" @click="choseOs(1)">偶</li>
                    <li class="flex" @click="clear(1)">清</li>
                </ul>
            </div>
            <div class="bet-box flex-box">
                <div class="label">
                    <p>选 号</p>
                    <p>遗 漏</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in ballFir" :key="index">
                            <a class="balls" :class="{'selected' : item.selected}" @click="doBet(1,item.num,index)">{{item.num}}</a>
                            <template v-if="miss !== 0">
                                <i class="f-mini c-3" :class="{'red':miss.qy[index+1]>10}">{{miss.qy[index+1]}}</i>
                            </template>
                            <template v-else>
                                <i class="f-mini c-3">--</i>
                            </template>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex-box fast-select-box">
                <div class="fast-title">第二位</div>
                <ul class="flex-box fast-list">
                    <li class="flex" @click="choseAll(2)">全</li>
                    <li class="flex" @click="choseBig(2)">大</li>
                    <li class="flex" @click="choseSmall(2)">小</li>
                    <li class="flex" @click="choseJs(2)">奇</li>
                    <li class="flex" @click="choseOs(2)">偶</li>
                    <li class="flex" @click="clear(2)">清</li>
                </ul>
            </div>
            <div class="bet-box flex-box">
                <div class="label">
                    <p>选 号</p>
                    <p>遗 漏</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in ballSec" :key="index">
                            <a class="balls" :class="{'selected' : item.selected}" @click="doBet(2,item.num,index)">{{item.num}}</a>
                            <template v-if="miss !== 0">
                                <i class="f-mini c-3" :class="{'red':miss.qe[index+1]>10}">{{miss.qe[index+1]}}</i>
                            </template>
                            <template v-else>
                                <i class="f-mini c-3">--</i>
                            </template>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex-box fast-select-box" v-if="type == 12">
                <div class="fast-title">第三位</div>
                <ul class="flex-box fast-list">
                    <li class="flex" @click="choseAll(3)">全</li>
                    <li class="flex" @click="choseBig(3)">大</li>
                    <li class="flex" @click="choseSmall(3)">小</li>
                    <li class="flex" @click="choseJs(3)">奇</li>
                    <li class="flex" @click="choseOs(3)">偶</li>
                    <li class="flex" @click="clear(3)">清</li>
                </ul>
            </div>
            <div class="bet-box flex-box" v-if="type == 12"> <!--前三直选-->
                <div class="label">
                    <p>选 号</p>
                    <p>遗 漏</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in ballThr" :key="index">
                            <a class="balls" :class="{'selected' : item.selected}" @click="doBet(3,item.num,index)">{{item.num}}</a>
                            <template v-if="miss !== 0">
                                <i class="f-mini c-3" :class="{'red':miss.qs[index+1]>10}">{{miss.qs[index+1]}}</i>
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
            <div class="yl-box f-mini border-top-1px tl"  v-if="notes > 0 && betModel == 1">
                如中奖,奖金<span class="red">{{gain}}</span>{{lotteryUnit}}
            </div>
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
        props:['type','number','gain','miss','title','text','name','betModel'], //type值,最少选择的号码个数,奖金设置值
        data () {
            return {
                ballFir : [
                    {num:'01',selected:false},
                    {num:'02',selected:false},
                    {num:'03',selected:false},
                    {num:'04',selected:false},
                    {num:'05',selected:false},
                    {num:'06',selected:false},
                    {num:'07',selected:false},
                    {num:'08',selected:false},
                    {num:'09',selected:false},
                    {num:'10',selected:false},
                    {num:'11',selected:false}], //第一位 ball value
                ballSec : [
                    {num:'01',selected:false},
                    {num:'02',selected:false},
                    {num:'03',selected:false},
                    {num:'04',selected:false},
                    {num:'05',selected:false},
                    {num:'06',selected:false},
                    {num:'07',selected:false},
                    {num:'08',selected:false},
                    {num:'09',selected:false},
                    {num:'10',selected:false},
                    {num:'11',selected:false}], //第二位 ball value
                ballThr : [
                    {num:'01',selected:false},
                    {num:'02',selected:false},
                    {num:'03',selected:false},
                    {num:'04',selected:false},
                    {num:'05',selected:false},
                    {num:'06',selected:false},
                    {num:'07',selected:false},
                    {num:'08',selected:false},
                    {num:'09',selected:false},
                    {num:'10',selected:false},
                    {num:'11',selected:false}], //第三位位 ball value
                betFirNum : [], //第一位数据
                betSecNum : [], //第二位数据
                betThrNum : [], //第二位数据
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
            //注数
            notes:function(){
                if(this.type == 10){ //前二直选
                    return this.$bet.getCount(this.$bet.Zuhe(this.betFirNum,this.betSecNum))
                }
                if(this.type == 12){ //前三直选
                    return this.$bet.getCount(this.$bet.Zuhe(this.betFirNum,this.betSecNum,this.betThrNum))
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
                let a = this.notes > 0 && this.betModel == 1 ? 30 : 0
                return this.$store.state.clientHeight - 100 - h - a
            },

            betNumObj() {
                return {
                    1 : {'betArr': this.betFirNum, 'ball' : this.ballFir},
                    2 : {'betArr': this.betSecNum, 'ball' : this.ballSec},
                    3 : {'betArr': this.betThrNum, 'ball' : this.ballThr}
                }
            }
        },
        methods:{
            //投注选号状态
            doBet:function(w,num,s){
                var betNum = this.betNumObj[w].betArr
                var ball = this.betNumObj[w].ball
                var chose = betNum.indexOf(num)
                if(chose > -1){
                    betNum.splice(chose, 1)
                }else {
                    betNum.push(num)
                }
                //改变球的选中状态
                ball[s].selected = !ball[s].selected
            },
            //全
            choseAll:function(w){
                var ball = this.betNumObj[w].ball
                if(w == 1){
                    this.betFirNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        this.betFirNum.push(ball[i].num)
                        ball[i].selected = true
                    }
                }
                if(w == 2){
                    this.betSecNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        this.betSecNum.push(ball[i].num)
                        ball[i].selected = true
                    }
                }
                if(w == 3){
                    this.betThrNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        this.betThrNum.push(ball[i].num)
                        ball[i].selected = true
                    }
                }
            },
            //大
            choseBig:function(w){
                var ball = this.betNumObj[w].ball
                if(w == 1){
                    this.betFirNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i > 4){
                            this.betFirNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
                if(w == 2){
                    this.betSecNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i > 4){
                            this.betSecNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
                if(w == 3){
                    this.betThrNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i > 4){
                            this.betThrNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
            },
            //小
            choseSmall:function(w){
                var ball = this.betNumObj[w].ball
                if(w == 1){
                    this.betFirNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i <= 4){
                            this.betFirNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
                if(w == 2){
                    this.betSecNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i <= 4){
                            this.betSecNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
                if(w == 3){
                    this.betThrNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i <= 4){
                            this.betThrNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
            },
            //奇数
            choseJs:function(w){
                var ball = this.betNumObj[w].ball
                if(w == 1){
                    this.betFirNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i%2 == 0){
                            this.betFirNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
                if(w == 2){
                    this.betSecNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i%2 == 0){
                            this.betSecNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
                if(w == 3){
                    this.betThrNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i%2 == 0){
                            this.betThrNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
            },
            //偶
            choseOs:function(w){
                var ball = this.betNumObj[w].ball
                if(w == 1){
                    this.betFirNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i%2 !== 0){
                            this.betFirNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
                if(w == 2){
                    this.betSecNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i%2 !== 0){
                            this.betSecNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
                if(w == 3){
                    this.betThrNum = []
                    for(var i = 0 ; i < ball.length;i ++ ){
                        if(i%2 !== 0){
                            this.betThrNum.push(ball[i].num)
                            ball[i].selected = true
                        }else {
                            ball[i].selected = false
                        }
                    }
                }
            },
            //清空
            clear:function(w){
                var ball = this.betNumObj[w].ball
                for(var i in ball){
                    ball[i].selected = false
                }
                if(w == 1){
                    this.betFirNum =[]
                }
                if(w == 2){
                    this.betSecNum =[]
                }
                if(w == 3){
                    this.betThrNum =[]
                }
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes<1){
                    this.$toast('请至少选择1注投注号码');
                    return
                }
                var firNum =  this.betFirNum.sort().join(",");
                var secNum =  this.betSecNum.sort().join(",");
                var thrNum =  this.betThrNum.sort().join(",");
                let betNum
                if(this.type==10){
                    betNum = firNum + '|' + secNum
                }
                if(this.type == 12){
                    betNum = firNum + '|' + secNum + '|' + thrNum
                }

                let data = {}
                data['num'] = betNum
                data['notes'] =this.notes
                data['type_text'] = this.text
                data['type'] = this.type + '.1';
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
                this.clear(1); //清空当前选号
                this.clear(2); //清空当前选号
                this.clear(3); //清空当前选号
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
                        type:this.type + '.1',
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
