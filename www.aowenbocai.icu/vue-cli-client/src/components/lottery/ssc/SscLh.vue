<!--时时彩 龙虎投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                玩法提示：从
                <em v-if="type == 9.1">万位、千位</em>
                <em v-if="type == 9.2">万位、百位</em>
                <em v-if="type == 9.3">万位、十位</em>
                <em v-if="type == 9.4">万位、个位</em>
                <em v-if="type == 9.5">千位、百位</em>
                <em v-if="type == 9.6">千位、十位</em>
                <em v-if="type == 9.7">千位、个位</em>
                <em v-if="type == 9.8">百位、十位</em>
                <em v-if="type == 9.9">百位、个位</em>
                <em v-if="type == 9.11">十位、个位</em>
                “龙、虎、和”或“大、小、单、双”中任意选择一个
            </div>
            <div class="bet-box flex-box">
                <div class="label">
                    <p>龙 虎</p>
                    <p>赔 率</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in ball" :key="index" v-if="index < 3">
                            <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,item.gain,index)">{{item.num}}</a>
                            <i class="f-mini c-1">{{handleGain(item.gain)}}</i>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bet-box flex-box">
                <div class="label">
                    <p>和 值</p>
                    <p>赔 率</p>
                </div>
                <div class="flex">
                    <ul class="balls-box">
                        <li v-for="(item,index) in ball" :key="index" v-if="index >= 3">
                            <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,item.gain,index)">{{item.num}}</a>
                            <i class="f-mini c-1">{{handleGain(item.gain)}}</i>
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
            return{
                ball:[
                    {num : '龙',selected : false,gain:this.gain.split(',')[0]},
                    {num : '虎',selected : false,gain:this.gain.split(',')[1]},
                    {num : '和',selected : false,gain:this.gain.split(',')[2]},
                    {num : '大',selected : false,gain:this.gain.split(',')[3]},
                    {num : '小',selected : false,gain:this.gain.split(',')[4]},
                    {num : '单',selected : false,gain:this.gain.split(',')[5]},
                    {num : '双',selected : false,gain:this.gain.split(',')[6]}
                ],
                betNum:[], //投注号码
                gainArr:[],//龙虎投注奖金数组
            }
        },
        computed:{
            balls:{
                get:function(){
                    if(parseInt(this.type) == 9){
                        return [
                            {num : '龙',selected : false,gain:this.gain.split(',')[0]},
                            {num : '虎',selected : false,gain:this.gain.split(',')[1]},
                            {num : '和',selected : false,gain:this.gain.split(',')[2]},
                            {num : '大',selected : false,gain:this.gain.split(',')[3]},
                            {num : '小',selected : false,gain:this.gain.split(',')[4]},
                            {num : '单',selected : false,gain:this.gain.split(',')[5]},
                            {num : '双',selected : false,gain:this.gain.split(',')[6]}
                        ]
                    }
                },
                set:function(val){
//                    this.balls = val
                }
            },
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
            //当前用户最高奖金返点百分比
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
            //选择注数计算
            notes(){
                return this.betNum.length
            },
            //投注总额
            totalMoney(){
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
            divVal () {
                return this.betModel == 1 ? 1 : 2//模式2下奖金减半
            }
        },
        filters:{
            strSplit1(value){ //奖金显示处理 取第一个值
                var arr = value.split(',')
                return arr[0]
            },
            strSplit2(value){ //奖金显示处理 取第二个值
                var arr = value.split(',')
                return arr[1]
            },
            strSplit3(value){ //奖金显示处理 取第三个值
                var arr = value.split(',')
                return arr[2]
            }
        },
        methods:{
            //奖金计算方法
            handleGain:function (g) {
                return this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accDiv(this.$bet.accMul(Number(g),this.percent),this.scale),this.divVal,5) : this.$bet.accDiv(Number(g),this.divVal,5)
            },
            //投注选号状态
            doBet(num,gain,s){
                var chose = this.betNum.indexOf(num)
                if(chose > -1){
                    this.betNum.splice(chose, 1)
                    this.gainArr.splice(chose, 1)
                }else {
                    this.betNum.push(num)
                    this.gainArr.push(gain)
                }
                this.$emit('change-gain',this.gainArr.join(',')) //改变最高奖金显示
                //改变球的选中状态
                this.ball[s].selected = !this.ball[s].selected
            },
            //清空选号
            clear(){
                this.betNum =[];
                this.gainArr =[];
                for(var i in this.ball){
                    this.ball[i].selected = false
                }
                this.$emit('change-gain','') //改变最高奖金显示
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes < 1){
                    this.$toast('每位至少选择1个号码！');
                    return
                }
                var betNum = this.betNum.join(',');

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
    .bet-box ul.balls-box li{
        width: 65px;
    }
</style>
