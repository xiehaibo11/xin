<!--时时彩 三星直选、三星组三、三星组六、二星直选、二星组选  和值投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                玩法提示：所选的和值与开奖号码和值相同即中，奖金<em class="red">{{gain}}</em>{{lotteryUnit}}。
            </div>
            <div class="bet-box flex-box">
                <div class="flex">
                    <ul class="balls-box">
                        <template v-if="type == 1.3"> <!--直选和值 0-27-->
                            <li v-for="(item,index) in ball" :key="index">
                                <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                            </li>
                        </template>
                        <template v-if="type == 2.3"><!--组三和值 1-26-->
                            <li v-for="(item,index) in ball" :key="index" v-if="index>0 && index<27">
                                <a class="balls" :class="{'selected':item.selected}" @click="doBet(item.num,index)">{{item.num}}</a>
                            </li>
                        </template>
                        <template v-if="type == 3.3"><!--组六和值  3-24-->
                            <li v-for="(item,index) in ball" :key="index" v-if="index>2 && index<25">
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
            <div class="yl-box f-mini border-top-1px tc c-3">
                <template v-if="!notes">
                    至少选择1个号码
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
        props:['type','gain','miss','title','text','expect','endTime'], //type值,最少选择的号码个数,奖金设置值
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
            //选号容器高度计算
            contentH(){
                return this.$store.state.clientHeight - 152
            },
        },
        watch:{
            //注数计算
            betNum(){
                let plusVal = 0
                if(this.type==1.3){ //直选 百十个位号码之和
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
                if(this.type == 2.3){ //组三 2 2 3 有序
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
                if(this.type == 3.3){ //组六 2 3 4 有序
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
                data['notes'] = this.notes
                data['type_text'] =  this.text
                data['type'] = this.type
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
<style scoped>
</style>
