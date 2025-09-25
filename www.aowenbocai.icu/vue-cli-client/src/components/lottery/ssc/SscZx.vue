<!--时时彩 三星组三、三星组六、二星组选 复式投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                <span v-if="type== 2.3 || type == 3.3 || type == 4.3">从0~9这10个号码里任选2个或以上的号码</span><!--三星组三-->
                <span v-if="type== 2.6 || type == 3.6 || type == 4.6">从0~9这10个号码里任选3个或以上的号码</span><!--三星组六-->
                <!--<span v-if="type == 5.3">从0~9任意任选M（2≤M≤7）个号码，</span> &lt;!&ndash;二星组选&ndash;&gt;-->
                <span v-if="type == 5.3 || type == 10.3">从0~9任意任选2个或以上的号码</span> <!--二星组选-->
                ，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}
            </div>
            <div class="flex-box fast-select-box">
                <div class="fast-title">选号</div>
                <ul class="flex-box fast-list">
                    <li class="flex" @click="choseAll">全</li> <!-- v-if="type != 5.3"-->
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
                                <i class="f-mini c-3"  :class="{'red':miss.zs_q[index]>10}" v-if="parseInt(type) == 2">{{miss.zs_q[index]}}</i>
                                <i class="f-mini c-3"  :class="{'red':miss.zs_z[index]>10}" v-if="parseInt(type) == 3">{{miss.zs_z[index]}}</i>
                                <i class="f-mini c-3"  :class="{'red':miss.zs_h[index]>10}" v-if="parseInt(type) == 4">{{miss.zs_h[index]}}</i>
                                <i class="f-mini c-3"  :class="{'red':miss.ze_h[index]>10}" v-if="parseInt(type) == 5">{{miss.ze_h[index]}}</i>
                                <i class="f-mini c-3"  :class="{'red':miss.ze_q[index]>10}" v-if="parseInt(type) == 10">{{miss.ze_q[index]}}</i>
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
        props:['type','number','gain','miss','title','text','name','betModel'], //type值,最少选择的号码个数,奖金设置值
        data () {
            return {
                ball : [ //显示选号 selected 0可选 1选中 -1不可选
                    {num:'0',selected:0},
                    {num:'1',selected:0},
                    {num:'2',selected:0},
                    {num:'3',selected:0},
                    {num:'4',selected:0},
                    {num:'5',selected:0},
                    {num:'6',selected:0},
                    {num:'7',selected:0},
                    {num:'8',selected:0},
                    {num:'9',selected:0}], //ball value
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
                let count = this.$bet.combination(this.betNum.length,this.number)
                if(this.type== 2.3 || this.type == 3.3 || this.type == 4.3){ //前三、中三、后三组三
                    return count * 2
                }else {
                    return count
                }
            },
            //选号容器高度计算
            contentH(){
                let h = this.betModel == 1 && this.unitIsOpen || this.rebateIsOpen && this.isRebate ? 89 : 52 //底部高度
                return this.$store.state.clientHeight - 100 - h
            },
        },
        methods:{
            //投注选号状态
            doBet(num,i){
                var chose = this.betNum.indexOf(num)
                if(chose > -1){
                    this.betNum.splice(chose, 1)
                }else {
//                    if(this.type == 5.3){
//                        if(this.betNum.length >= 7){
//                            this.$toast('二星组选复式只能选择2到7个号码!');
//                            return
//                        }
//                    }
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
                    if(i%2 !==0){
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
                    if(i%2 ==0){
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
