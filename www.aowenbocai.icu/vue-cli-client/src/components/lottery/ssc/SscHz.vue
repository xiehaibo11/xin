<!--时时彩 三星直选、三星组三、三星组六、二星直选、二星组选  和值投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                <span v-if="type==2.2 || type==2.5 || type== 2.8">
                    <em v-if="type == 2.5">开奖号码为组三且</em>
                    <em v-if="type== 2.8">开奖号码为组六且</em>万、千、百位号码之和
                </span>
                <span v-if="type==3.2 || type==3.5 || type== 3.8">
                    <em v-if="type == 3.5">开奖号码为组三且</em>
                    <em v-if="type== 3.8">开奖号码为组六且</em>千、百、十位号码之和
                </span>
                <span v-if="type==4.2 || type==4.5 || type== 4.8">
                    <em v-if="type == 4.5">开奖号码为组三且</em>
                    <em v-if="type== 4.8">开奖号码为组六且</em>百、十、个位号码之和
                </span>
                <span v-if="type == 5.2">十、个位号码之和</span>
                <span v-if="type == 5.4">开奖号码十、个位之和与所选和值相同则中奖</span>
                <span v-if="type == 10.2">万、千位号码之和</span>
                <span v-if="type == 10.4">开奖号码万、千位之和与所选和值相同则中奖</span>
                ，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}
            </div>
            <div class="bet-box flex-box">
                <div class="flex">
                    <ul class="balls-box">
                        <template  v-if="type == 2.2 || type == 3.2 || type == 4.2"> <!--前三、中三、后三直选 0-27-->
                            <li v-for="(item,index) in ball" :key="index">
                                <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                            </li>
                        </template>
                        <template v-if="type == 2.5 || type == 3.5 || type == 4.5"><!--前三、中三、后三组三 1-26-->
                            <li v-for="(item,index) in ball" :key="index" v-if="index>0 && index<27">
                                <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                            </li>
                        </template>
                        <template v-if="type == 2.8 || type == 3.8 || type == 4.8"><!--前三、中三、后三组六  3-24-->
                            <li v-for="(item,index) in ball" :key="index" v-if="index>2 && index<25">
                                <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                            </li>
                        </template>
                        <template v-if="type == 5.2 || type == 5.4 || type == 10.2 || type == 10.4"><!--前二、后二组选/直选 0-18-->
                            <li v-for="(item,index) in ball" :key="index" v-if="index<19">
                                <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                            </li>
                        </template>
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
                ball: [{num:0,selected:false},{num:1,selected:false},{num:2,selected:false},{num:3,selected:false},{num:4,selected:false},
                    {num:5,selected:false},{num:6,selected:false},{num:7,selected:false},{num:8,selected:false},{num:9,selected:false},{num:10,selected:false},
                    {num:11,selected:false},{num:12,selected:false},{num:13,selected:false},{num:14,selected:false},{num:15,selected:false},{num:16,selected:false},
                    {num:17,selected:false},{num:18,selected:false},{num:19,selected:false},{num:20,selected:false},{num:21,selected:false},{num:22,selected:false},
                    {num:23,selected:false},{num:24,selected:false},{num:25,selected:false},{num:26,selected:false},{num:27,selected:false}
                    ],
                betNum:[],
                notes:0
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
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && this.unitIsOpen || this.rebateIsOpen && this.isRebate ? 89 : 52 //底部高度
                return this.$store.state.clientHeight - 100 - h
            },
        },
        watch:{
            //注数计算
            betNum(){
                let plusVal = 0
                if(this.type==2.2 || this.type==3.2 || this.type == 4.2){ //前三、中三、后三直选 百十个位号码之和
                    for(var a in this.betNum){
                        for(var i=0 ; i <= 9 ; i++){
                            for(var j=0 ; j <= 9 ; j++){
                                for(var k=0 ; k <= 9 ; k++){
                                    var hz = i + j + k
                                    if(hz == this.betNum[a]){
                                        plusVal +=1;
                                    }
                                }
                            }
                        }
                    }
                    this.notes = plusVal;
                }
                if(this.type == 5.2 || this.type == 10.2){ //二星直选 无序
                    for(var a in this.betNum){
                        for(var i=0 ; i <= 9 ; i++){
                            for(var j=0 ; j <= 9 ; j++){
                                var hz = i + j
                                if(hz == this.betNum[a]){
                                    plusVal +=1;
                                }
                            }
                        }
                    }
                    this.notes = plusVal;
                }
                if(this.type == 5.4 || this.type == 10.4){ //二星组选 有序
                    for(var a in this.betNum){
                        for(var i=0 ; i <= 9 ; i++){
                            for(var j=i ; j <= 9 ; j++){
                                var hz = i + j
                                if(hz == this.betNum[a]){
                                    plusVal +=1;
                                }
                            }
                        }
                    }
                    this.notes = plusVal
                }
                if(this.type == 2.5 || this.type == 3.5 || this.type == 4.5){ //前三、中三、后三组三 2 2 3 有序
                    for(var a in this.betNum){
                        for(var i=0 ; i <= 9 ; i++){
                            for(var j=0 ; j <= 9 ; j++){
                                if(i !== j){
                                    var hz = i + i + j
                                    if(hz == this.betNum[a]){
                                        plusVal +=1;
                                    }
                                }
                            }
                        }
                    }
                    this.notes = plusVal;
                }
                if(this.type == 2.8 || this.type == 3.8 || this.type == 4.8){ //前三、中三、后三组六 2 3 4 有序
                    for(var a in this.betNum){
                        for(var i=0 ; i <= 9 ; i++){
                            for(var j=i ; j <= 9 ; j++){
                                for(var k=j ; k <= 9 ; k++){
                                    if(i != j && j != k && i != k){
                                        var hz = i + j + k
                                        if(hz == this.betNum[a]){
                                            plusVal +=1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    this.notes = plusVal;
                }
            }
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
                    this.$toast('至少选择1个和值号码！');
                    return
                }
                var betNum = this.betNum.sort((a,b)=>{return a-b}).join(',');

                let data = {}
                data['num'] = betNum
                data['notes'] =this.notes
                data['type_text'] =  this.text
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
