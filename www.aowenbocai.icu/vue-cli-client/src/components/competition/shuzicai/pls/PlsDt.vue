<!--排列3组三、组六 胆拖投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">玩法提示：{{n+1}}≤胆码+拖码≤9,<em v-if="type == 1.4">不含组3和豹子</em><em v-else>不含豹子</em>,<em v-if="type==1.4">按位数全部相同</em><em v-else>猜对任意{{n}}个号码</em>即中<em class="red">{{gain}}</em>{{lotteryUnit}}。</div>
            <div class="bd-v">
                <b class="red">胆码区</b> <span class="f-sm">您认为必出的号码 <em class="red" v-if="type == 3.4 || type == 1.4">(选择1~2个)</em><em class="red" v-if="type == 2.4">(选择1个)</em></span>
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
                                <i class="f-mini c-3" :class="{'red':0}">0</i>
                            </template>
                            <template v-else>
                                <i class="f-mini c-3">--</i>
                            </template>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bd-v">
                <b class="red">拖码区</b> <span class="f-sm">您认为可能的号码 <em class="red">(2≤拖码个数≤9)</em></span>
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
                                <i class="f-mini c-3" :class="{'red':0}">0</i>
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
            <div class="yl-box f-mini border-top-1px tc c-3">
                <template v-if="!notes">
                    胆码+拖码不少于<em class="red">{{n + 1}}</em>个
                </template>
                <template v-else>
                    已选<em class="red">{{notes}}</em>注，共<em class="red">{{notes * 2}}</em>{{lotteryUnit}}
                </template>
            </div>
            <div class="flex-box notes-box">
                <div class="flex bet-detail">
                    <div class="c-4" @click="clear">
                        <i class="iconfont icon-shanchu btn-icon-clear"></i>清空
                    </div>
                </div>
                <div class="tc bet-basket" style="margin: 0 15px 0 5px">
                    <button class="bet-btn btn-basket" @click="addBetBasket">+ 号码篮 <mt-badge class="badge" type="error" size="small">{{badge}}</mt-badge></button>
                </div>
                <button class="bet-btn btn-sure" @click="submitOrder">选好了</button>
            </div>
        </div>
        <!--底部 end-->
    </div>
</template>

<script>
    export default {
        props:['type','gain','miss','title','text','expect','endTime','n'], //type值,最少选择的号码个数,奖金设置值
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
            name(){
                return this.$route.path.replace('/','')
            },
            //投注总额
            totalMoney:function(){
                return this.notes * 2
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            //选号篮数量
            badge(){
                return this.$store.state.shuzicai.plan.length
            },
            dmNum(){ // 胆码选择数量
                return this.betDmNum.length
            },
            tmNum(){ // 拖码选择数量
                return this.betTmNum.length
            },
            //选择注数计算
            notes(){
                if(this.dmNum + this.tmNum < parseInt(this.n) + 1 || this.dmNum == 0){
                    return 0
                }else if(this.type == 1.4){ //<!--直选-->
                    return this.$bet.combination(this.tmNum,this.n - this.dmNum) * 6
                }else if(this.type == 2.4){ //<!--组三-->
                    return this.tmNum * 2
                }else if(this.type == 3.4){ //<!--组六-->
                    return this.$bet.combination(this.tmNum,this.n - this.dmNum)
                }
            },
            //选号容器高度计算
            contentH(){
                return this.$store.state.clientHeight - 152
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
                    let max = this.n -1
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
                    this.$toast('胆码+拖码≥'+ (this.n+1) + '个');
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

                this.$store.commit('pushSzcBetNum',data)
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
                    path:'/shuzicai/bet',
                    query:{
                        type:this.type,
                        name:this.name,
                        title:this.title,
                        expect:this.expect,
                        end_time : this.endTime
                    }
                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .bet-box ul.balls-box{
        padding: 0 10px;
    }
    .bet-box ul.balls-box li{
        width: 20%;
    }
</style>
