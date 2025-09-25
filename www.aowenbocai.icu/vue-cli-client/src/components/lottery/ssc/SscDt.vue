<!--时时彩 三星组三、三星组六 胆拖投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">胆码加拖码不少于<em class="red">{{number + 1}}</em>个,猜对任意{{number}}个号码即中<em class="red">{{gain}}</em>{{lotteryUnit}}</div>
            <div class="bd-v">
                <b class="red">胆码区</b> <span class="f-sm">您认为必出的号码 <em class="red">(选择<em v-if="number>2">1～</em>{{number-1}}个)</em></span>
            </div>
            <div class="bet-box flex-box">
                <div class="label">
                    <p>选 号</p>
                    <p>遗 漏</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in ballDm" :key="index">
                            <a class="balls" :class="[item.selected == 3 ? 'selected' : '',item.selected == 2 ? 'forbidden': '']" @click="doDmBet(item.num,index)">{{item.num}}</a>
                            <template v-if="miss !== 0">
                                <i class="f-mini c-3" v-if="parseInt(type) == 2" :class="{'red':miss.zs_q[index]>10}">{{miss.zs_q[index]}}</i>
                                <i class="f-mini c-3" v-if="parseInt(type) == 3" :class="{'red':miss.zs_z[index]>10}">{{miss.zs_z[index]}}</i>
                                <i class="f-mini c-3" v-if="parseInt(type) == 4" :class="{'red':miss.zs_h[index]>10}">{{miss.zs_h[index]}}</i>
                            </template>
                            <template v-else>
                                <i class="f-mini c-3">--</i>
                            </template>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bd-v">
                <b class="red">拖码区</b> <span class="f-sm">您认为可能的号码 <em class="red">(至少选择2个)</em></span>
            </div>
            <div class="bet-box flex-box">
                <div class="label">
                    <p>选 号</p>
                    <p>遗 漏</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in ballTm" :key="index">
                            <a class="balls" :class="[item.selected == 3 ? 'selected' : '',item.selected == 2 ? 'forbidden': '']" @click="doTmBet(item.num,index)">{{item.num}}</a>
                            <template v-if="miss !== 0">
                                <i class="f-mini c-3" v-if="parseInt(type) == 2" :class="{'red':miss.zs_q[index]>10}">{{miss.zs_q[index]}}</i>
                                <i class="f-mini c-3" v-if="parseInt(type) == 3" :class="{'red':miss.zs_z[index]>10}">{{miss.zs_z[index]}}</i>
                                <i class="f-mini c-3" v-if="parseInt(type) == 4" :class="{'red':miss.zs_h[index]>10}">{{miss.zs_h[index]}}</i>
                            </template>
                            <template v-else>
                                <i class="f-mini c-3">--</i>
                            </template>
                        </li>
                        <mt-button size="small" plain class="chose-all" @click.native="tmAll">全包</mt-button>
                        <mt-button size="small" plain class="chose-all" @click.native="clearTm">清空</mt-button>
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
        props:['type','number','gain','miss','title','text','name','betModel'], //type值,最少选择的号码个数,奖金设置值
        data () {
            return {
                ballDm : [ //显示胆码区选号 selected 1可选 2已被拖码选 3 选中
                    {num:'0',selected:1},
                    {num:'1',selected:1},
                    {num:'2',selected:1},
                    {num:'3',selected:1},
                    {num:'4',selected:1},
                    {num:'5',selected:1},
                    {num:'6',selected:1},
                    {num:'7',selected:1},
                    {num:'8',selected:1},
                    {num:'9',selected:1}], //ball value
                ballTm : [ //显示拖码选号  selected 1可选 2已被胆码选 3 选中
                    {num:'0',selected:1},
                    {num:'1',selected:1},
                    {num:'2',selected:1},
                    {num:'3',selected:1},
                    {num:'4',selected:1},
                    {num:'5',selected:1},
                    {num:'6',selected:1},
                    {num:'7',selected:1},
                    {num:'8',selected:1},
                    {num:'9',selected:1}], //ball value
                betDmNum : [], //胆码投注数据
                betTmNum : [], //拖码投注数据
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
            dmNum(){ // 胆码选择数量
                return this.betDmNum.length
            },
            tmNum(){ // 拖码选择数量
                return this.betTmNum.length
            },
            //选择注数计算
            notes(){
                if(this.dmNum + this.tmNum < parseInt(this.number) + 1 || this.dmNum == 0){
                    return 0
                }else if(this.type == 2.4 || this.type == 3.4 || this.type == 4.4){ //<!--组三-->
                    return this.tmNum * 2
                }else if(this.type == 2.7 || this.type == 3.7 || this.type == 4.7){ //<!--组六-->
                    return this.$bet.combination(this.tmNum,this.number - this.dmNum)
                }
            },
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && this.unitIsOpen || this.rebateIsOpen && this.isRebate ? 89 : 52 //底部高度
                return this.$store.state.clientHeight - 100 - h
            },
        },
        methods:{
            //投注胆码选号状态
            doDmBet(num,index){
                let chose = this.betDmNum.indexOf(num) //-1 存在
                if(chose > -1){
                    this.betDmNum.splice(chose, 1);
                    this.ballTm[index].selected = this.ballTm[index].selected ==3  ? 2 : 1;
                    this.ballDm[index].selected = this.ballDm[index].selected == 1 || this.ballDm[index].selected ==2  ? 3 : 1;
                }else {
                    let max = this.number -1
                    if(this.dmNum >= max){ //胆码数量判断
                        this.$toast('胆码最多只能选择' + max + '个')
                    }else {
                        this.betDmNum.push(num)
                        this.ballTm[index].selected = 2
                        this.ballDm[index].selected = 3
                        if(this.betTmNum.indexOf(num) > -1){
                            this.betTmNum.splice(this.betTmNum.indexOf(num), 1);
                        }
                    }
                }
            },
            //投注拖码选号状态
            doTmBet(num,index){
                var chose = this.betTmNum.indexOf(num) //-1 不存在
                if(chose > -1){
                    this.betTmNum.splice(chose, 1);
                    this.ballDm[index].selected = this.ballDm[index].selected ==3  ? 2 : 1;
                    this.ballTm[index].selected = this.ballTm[index].selected == 1 || this.ballTm[index].selected ==2  ? 3 : 1;
                }else {
                    this.betTmNum.push(num)
                    if(this.betDmNum.indexOf(num) > -1){
                        this.betDmNum.splice(this.betDmNum.indexOf(num), 1);
                    }
                    this.ballDm[index].selected = 2
                    this.ballTm[index].selected = 3
                }
            },
            //拖码全选
            tmAll(){
                this.betTmNum = [];
                for(var i = 0 ; i < 10 ; i ++){
                    var chose = this.betDmNum.indexOf(this.ballTm[i].num); //-1 存在
                    if(chose > -1){
                        this.ballTm[i].selected = 2;
                    }else {
                        this.betTmNum.push(this.ballTm[i].num)
                        this.ballTm[i].selected = 3
                        this.ballDm[i].selected = 2
                    }
                }
            },
            //清空拖码
            clearTm(){
                for(var i = 0 ; i < 10 ; i ++){
                    var chose = this.betDmNum.indexOf(this.ballTm[i].num); //-1 存在
                    if(chose > -1){
                        this.ballTm[i].selected = 2;
                    }else {
                        this.betTmNum.splice(chose,1)
                        this.ballTm[i].selected = 1;
                        this.ballDm[i].selected = 1;
                    }
                }
            },
            //添加到选区后清空
            clear(){
                for(var i = 0 ; i < 10 ; i ++){
                    this.ballTm[i].selected = 1;
                    this.ballDm[i].selected = 1;
                }
                this.betTmNum = []
                this.betDmNum = []
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes<1){
                    this.$toast('胆码+拖码≥'+ (this.number+1) + '个');
                    return
                }
                let type = this.type;
                let type_text =  this.text;
                let dmNum =  this.betDmNum.sort().join(",") + '#';
                let tmNum =  this.betTmNum.sort().join(",");
                let betNum = dmNum + tmNum

                let data = {}
                data['num'] = betNum
                data['notes'] =this.notes
                data['type_text'] =type_text
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
