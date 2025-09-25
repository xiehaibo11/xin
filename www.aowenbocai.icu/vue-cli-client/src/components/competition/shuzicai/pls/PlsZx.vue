<!--时时彩 三星组三、三星组六、二星组选 复式投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                玩法提示：<span v-if="type==3.1">所选号码与开奖号码相同（顺序不限）</span><span v-if="type==2.1">所选号码与开奖号码一致(顺序不限)，且开奖号码有任意两位相同</span>即中<em class="red">{{gain}}</em>{{lotteryUnit}}。
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
                                <i class="f-mini c-3"  :class="{'red':0}">0</i>
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
                    至少选择{{n}}个号码
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
            name(){
                return this.$route.path.replace('/','')
            },
            //投注总额
            totalMoney(){
                return this.notes * 2
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            //选号篮数量
            badge(){
                return this.$store.state.shuzicai.plan.length
            },
            //选择注数计算
            notes(){
                let count = this.$bet.combination(this.betNum.length,this.n)
                if(this.type == 2.1){ //组三
                    return count * 2
                }else {
                    return count
                }
            },
            //选号容器高度计算
            contentH(){
                return this.$store.state.clientHeight - 152
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
                if(this.betNum.length < this.n){
                    this.$toast('至少选择'+ this.n + '个号码');
                    return
                }
                var betNum =  this.betNum.sort().join(",");

                let data = {}
                data['num'] = betNum
                data['notes'] =this.notes
                data['type_text'] = this.text
                data['type'] =this.type
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
