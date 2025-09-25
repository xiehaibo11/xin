<!--11选5任选2/3/4/5/6/7/8 /前一/前二组选/前三组选 普通投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                <span v-if="type<8 || type == 13">至少选择{{number}}个号码，与开奖号码任意{{numberAfter}}个数字相同</span>
                <span v-if="type==8">至少选择1个号码，与开奖号码第1个数字相同</span><!--前一-->
                <span v-if="type==9">至少选择2个号码，与开奖号码的前两位号码相同（顺序不限）</span> <!--前二组选-->
                <span v-if="type==11">至少选择3个号码，与开奖号码的前三位号码相同（顺序不限）</span> <!--前三组选-->
                ，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}
            </div>

            <div class="flex-box fast-select-box">
                <div class="fast-title">选号</div>
                <ul class="flex-box fast-list">
                    <li class="flex" @click="choseAll">全</li>
                    <li class="flex" @click="choseBig">大</li>
                    <li class="flex" @click="choseSmall">小</li>
                    <li class="flex" @click="choseJs">奇</li>
                    <li class="flex" @click="choseOs">偶</li>
                    <li class="flex" @click="clear">清</li>
                </ul>
            </div>

            <div class="bet-box flex-box">
                <div class="label">
                    <p>选 号</p>
                    <p>遗 漏</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in ball" :key="index">
                            <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                            <template v-if="miss !== 0">
                                <i class="f-mini c-3" :class="{'red':miss.rx[index+1]>10}" v-if="type < 8 || type == 13">{{miss.rx[index+1]}}</i> <!--任选-->
                                <i class="f-mini c-3" :class="{'red':miss.qy[index+1]>10}" v-if="type == 8">{{miss.qy[index+1]}}</i> <!--前一-->
                                <i class="f-mini c-3" :class="{'red':miss.ze[index+1]>10}" v-if="type == 9">{{miss.ze[index+1]}}</i> <!--前二组选-->
                                <i class="f-mini c-3" :class="{'red':miss.zs[index+1]>10}" v-if="type == 11">{{miss.zs[index+1]}}</i> <!--前三组选-->
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
                如中奖,奖金
                <span class="red"><em v-if="gainMin > 0">{{gainMin}}</em> <em v-if="gainMin > 0" style="color: #333333">～</em>{{countGain}}</span>{{lotteryUnit}}
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
                ball : [ //显示选号 selected 0可选 1选中 -1不可选
                    {num:'01',selected:0},
                    {num:'02',selected:0},
                    {num:'03',selected:0},
                    {num:'04',selected:0},
                    {num:'05',selected:0},
                    {num:'06',selected:0},
                    {num:'07',selected:0},
                    {num:'08',selected:0},
                    {num:'09',selected:0},
                    {num:'10',selected:0},
                    {num:'11',selected:0}], //ball value
                betNum:[],//选号数据
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
            //中奖号码个数 大于5时返回5
            numberAfter(){
                return this.number>5 ? 5 : this.number
            },
            //选择注数计算
            notes(){
                return this.$bet.combination(this.betNum.length,parseInt(this.number))
            },
            //最高中奖注数
            max(){
                var neednum =  parseInt(this.number);  //要求选择个数
                var len = this.betNum.length;  //已选个数
                //任选
                if(this.type < 8 || this.type == 13){
                    if(neednum > 5){
                        return this.$bet.combination(len-5,neednum-5)
                    }else {
                        return this.$bet.combination(5,neednum)
                    }
                }
                //前一、前二组选、前三组选
                if(this.type ==8 || this.type == 9 || this.type == 11){
                    return 1
                }
            },
            //奖金计算
            countGain(){
                if(this.notes > this.max){
                    return this.$bet.accMul(this.$bet.accMul(this.gain ,this.max),this.multiple,5) || 0; //最高奖金
                }else{
                    return this.$bet.accMul(this.$bet.accMul(this.gain ,this.notes),this.multiple,5) || 0;
                }
            },
            //奖金最低值计算
            gainMin(){
                if(this.type < 8 || this.type == 13){  //任选
                    if(this.betNum.length == 11 || this.betNum.length <= this.number || this.number >=5){ //全选时必中最高中奖注数 or 未选够号码 or 所选号码要求5以上 最低值返回0
                        return 0
                    }else {
                        var bz = 5 - (11 - this.betNum.length);
                        if(bz > 0 && bz > this.number){
                            if(this.gain * this.$bet.combination(bz,this.number) == this.countGain){
                                return 0
                            }else {
                                return this.$bet.accMul(this.$bet.accMul(this.gain ,this.$bet.combination(bz,this.number)),this.multiple,5) || 0
                            }
                        } else {
                            return this.$bet.accMul(this.gain,this.multiple,5) || 0
                        }
                    }
                }
                if(this.type ==8 || this.type == 9 || this.type == 11){ //前一、前二组选、前三组选
                    return 0
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
        },
        methods:{
            //投注选号状态
            doBet(num,i){
                var chose = this.betNum.indexOf(num)
                if(chose > -1){
                    this.betNum.splice(chose, 1)
                }else {
                    this.betNum.push(num)
                }
                //改变球的选中状态
                this.ball[i].selected = this.ball[i].selected ? 0 : 1
            },
            //全选
            choseAll(){
                this.betNum = [];
                for(let i in this.ball){
                    this.betNum.push(this.ball[i].num)
                    this.ball[i].selected = 1;
                }
            },
            //大
            choseBig(){
                this.clear();
                for(var i in this.ball){
                    if(i > 4){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1
                    }else {
                        this.ball[i].selected = 0
                    }
                }
            },
            //小
            choseSmall(){
                this.clear();
                for(var i in this.ball){
                    if(i <= 4){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1
                    }else {
                        this.ball[i].selected = 0
                    }
                }
            },
            //奇
            choseJs(){
                this.clear();
                for(var i in this.ball){
                    if(i%2 ==0){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1
                    }else {
                        this.ball[i].selected = 0
                    }
                }
            },
            //偶
            choseOs(){
                this.clear();
                for(var i in this.ball){
                    if(i%2 !==0){
                        this.betNum.push(this.ball[i].num)
                        this.ball[i].selected = 1
                    }else {
                        this.ball[i].selected = 0
                    }
                }
            },
            //清空选号
            clear(){
                this.betNum = [];
                for(let i in this.ball){
                    this.ball[i].selected = 0;
                }
            },
            //添加到号码篮
            addBetBasket(){
                if(this.betNum.length < this.number){
                    this.$toast('至少选择'+ this.number + '个号码');
                    return
                }
                var betNum =  this.betNum.sort().join(",");

                let data = {}
                data['num'] = betNum
                data['notes'] =this.notes
                data['type_text'] = this.text
                data['type'] = this.type + '.1'
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
