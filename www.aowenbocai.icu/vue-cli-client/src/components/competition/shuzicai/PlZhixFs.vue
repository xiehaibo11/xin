<!--排列3、排列5、七星彩 复式投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                <em v-if="name == 'pls'">玩法提示：所选号码与开奖号码按位数全部相同即中<em class="red">{{gain}}</em>{{lotteryUnit}}。</em>
                <em v-if="name == 'plw'">玩法提示：所选号码与开奖号码全部相同且顺序一致，即中<em class="red">{{gain}}</em>{{lotteryUnit}}。</em>
                <em v-if="name == 'qxc'">玩法提示：所选号码与开奖号码全部相同且顺序一致，最高可中<em class="red">{{gain}}</em>{{lotteryUnit}}。</em>
            </div>
            <bet-balls wz-text="第一位" :miss="miss" v-model="betNum1"></bet-balls>
            <bet-balls wz-text="第二位" :miss="miss" v-model="betNum2"></bet-balls>
            <bet-balls wz-text="第三位" :miss="miss" v-model="betNum3"></bet-balls>
            <template v-if="name=='plw'|| name == 'qxc'"><!--排列5、七星彩 -->
                <bet-balls wz-text="第四位" :miss="miss" v-model="betNum4"></bet-balls>
                <bet-balls wz-text="第五位" :miss="miss" v-model="betNum5"></bet-balls>
            </template>
            <template v-if="name == 'qxc'"><!--七星彩 -->
                <bet-balls wz-text="第六位" :miss="miss" v-model="betNum6"></bet-balls>
                <bet-balls wz-text="第七位" :miss="miss" v-model="betNum7"></bet-balls>
            </template>
        </div>
        <!--选号 end-->
        <!--底部 start-->
        <div class="bet-foot">
            <div class="yl-box f-mini border-top-1px tc c-3">
                <template v-if="!notes">
                    每位至少选择1个号码
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
    import betBalls from 'components/competition/shuzicai/PlBetBalls.vue' //选号组件
    export default {
        components:{
            betBalls
        },
        props:['type','gain','miss','title','text','expect','endTime'], //type值,最少选择的号码个数,奖金设置值
        data () {
            return {
                betNum1 : [], //1位投注数据
                betNum2 : [], //2位投注数据
                betNum3 : [], //3位投注数据
                betNum4 : [], //4位投注数据
                betNum5 : [], //5位投注数据
                betNum6 : [], //6位投注数据
                betNum7 : [], //7位投注数据
            }
        },
        computed:{
            name(){
                return this.$route.path.replace('/','')
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
                let note = 0
                //排列3
                if(this.name == 'pls'){
                    note = this.betNum1.length * this.betNum2.length * this.betNum3.length
                }
                //排列5
                if(this.name == 'plw'){
                    note = this.betNum1.length * this.betNum2.length * this.betNum3.length * this.betNum4.length * this.betNum5.length
                }
                //七星彩
                if(this.name == 'qxc'){
                    note = this.betNum1.length * this.betNum2.length * this.betNum3.length * this.betNum4.length * this.betNum5.length * this.betNum6.length * this.betNum7.length
                }
                return note
            },
            //投注总额
            totalMoney(){
                return this.notes * 2
            },
            //选号容器高度计算
            contentH(){
                return this.$store.state.clientHeight - 152
            },
        },
        methods:{
            //添加到选区后清空
            clear(){
                this.$set(this,'betNum1',[])
                this.$set(this,'betNum2',[])
                this.$set(this,'betNum3',[])
                this.$set(this,'betNum4',[])
                this.$set(this,'betNum5',[])
                this.$set(this,'betNum6',[])
                this.$set(this,'betNum7',[])
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes<1){
                    this.$toast('每位至少选择1个号码！');
                    return
                }
                let type = this.type;
                let type_text =  this.text;
                let betNum,d1,d4,d3,d2,d5,d6,d7,betting;
                d1 = this.betNum1.length ? this.betNum1.sort().join(",") + '|' : '';
                d2 = this.betNum2.length ? this.betNum2.sort().join(",") + '|' : '';
                d3 = this.betNum3.length ? this.betNum3.sort().join(",") + '|' : '';
                d4 = this.betNum4.length ? this.betNum4.sort().join(",") + '|' : '';
                d5 = this.betNum5.length ? this.betNum5.sort().join(",") + '|' : '';
                d6 = this.betNum6.length ? this.betNum6.sort().join(",") + '|' : '';
                d7 = this.betNum7.length ? this.betNum7.sort().join(",") : '';
                betNum = d1 + d2 + d3 + d4 + d5 + d6 + d7;
                if(this.name == 'pls' || this.name == 'plw'){
                    betting = betNum.substr(0, betNum.length - 1)
                }else {
                    betting = betNum
                }
                let data = {}
                data['num'] = betting
                data['notes'] = this.notes
                data['type_text'] = type_text
                data['type'] = type
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

</style>
