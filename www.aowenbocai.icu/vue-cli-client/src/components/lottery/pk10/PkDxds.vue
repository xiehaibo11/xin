<!--pk10大小单双 投注-->
<template>
    <div>
        <!--选号 start-->
        <div class="bet-ball contentH" :style="{height:contentH + 'px'}">
            <div class="bet-tips f-mini c-3 bd-v">
                <template v-if="type == 12.1">
                    <span>前五位选择至少一个，对开奖号码的"大（6～10）、小（1～5）、单（01 03 05 07 09）、 双（02 04 06 08 10）"形态进行购买，所选号码的形态与对应位置的开奖号码的形态相同。奖金<em class="red">{{gain}}</em> {{lotteryUnit}}</span>
                </template>
                <template v-if="type == 12.2">
                    <span>后五位选择至少一个，对开奖号码的"大（6～10）、小（1～5）、单（01 03 05 07 09）、 双（02 04 06 08 10）"形态进行购买，所选号码的形态与对应位置的开奖号码的形态相同。奖金<em class="red">{{gain}}</em> {{lotteryUnit}}</span>
                </template>
                <template v-if="type == 12.3">
                    <template v-if="isSame">
                        <span>对冠亚军和值的"大（12～19）、小（3～10）、单（03 05 07 09 13 15 17 19）、 双（04 06 08 10 12 14 16 18）"形态进行购买，所选号码的形态与开奖号码的形态相同，即为中奖。奖金<em class="red">{{gain}}</em> {{lotteryUnit}}，
                         等于11的注单视为打和。</span>
                    </template>
                    <template v-else>
                        <span>对冠亚军和值的"大（12～19）、小（3～11）、单（03 05 07 09 11 13 15 17 19）、 双（04 06 08 10 12 14 16 18）"形态进行购买，所选号码的形态与开奖号码的形态相同，即为中奖。奖金<em class="red">{{gain}}</em> {{lotteryUnit}}</span>
                    </template>
                </template>
            </div>
            <!--前五-->
            <template v-if="type == 12.1">
                <dxds-balls :wz="1" :miss="miss" :bet-num="betNum1"></dxds-balls><!--第一位-->
                <dxds-balls :wz="2" :miss="miss" :bet-num="betNum2"></dxds-balls><!--第二位-->
                <dxds-balls :wz="3" :miss="miss" :bet-num="betNum3"></dxds-balls><!--第三位-->
                <dxds-balls :wz="4" :miss="miss" :bet-num="betNum4"></dxds-balls><!--第四位-->
                <dxds-balls :wz="5" :miss="miss" :bet-num="betNum5"></dxds-balls><!--第五位-->
            </template>
            <!--后五-->
            <template v-if="type == 12.2">
                <dxds-balls :wz="6" :miss="miss" :bet-num="betNum6"></dxds-balls><!--第六位-->
                <dxds-balls :wz="7" :miss="miss" :bet-num="betNum7"></dxds-balls><!--第七位-->
                <dxds-balls :wz="8" :miss="miss" :bet-num="betNum8"></dxds-balls><!--第八位-->
                <dxds-balls :wz="9" :miss="miss" :bet-num="betNum9"></dxds-balls><!--第九位-->
                <dxds-balls :wz="10" :miss="miss" :bet-num="betNum10"></dxds-balls><!--第十位-->
            </template>
            <template v-if="type == 12.3">
                <dxds-balls :wz="11" :miss="miss" :gain="gain" :gyh-gain="gyhGain" :bet-num="betNumGy"></dxds-balls><!--冠亚和-->
            </template>
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
    import dxdsBalls from 'components/lottery/pk10/DxdsBalls.vue' //选号组件
    import SelectUnit from 'components/lottery/SelectUnit.vue' //倍数、单位设置组件
    import RebateRange from 'components/lottery/RebateRange.vue' //返点设置
    export default {
        components:{
            dxdsBalls,
            SelectUnit,
            RebateRange
        },
        props:['type','gain','miss','title','text','name','small','betModel','sign','gyhGain'], //type值,最少选择的号码个数,奖金设置值
        data () {
            return {
                betNum1 : [],
                betNum2 : [],
                betNum3 : [],
                betNum4 : [],
                betNum5 : [],
                betNum6 : [],
                betNum7 : [],
                betNum8 : [],
                betNum9 : [],
                betNum10 : [],
                betNumGy: [],
            }
        },
        computed:{
            //冠亚和大小单双赔率是否一样
            isSame(){
                return this.gain.toString().indexOf("~") > -1 ? false : true
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
            num1(){
                return this.betNum1.length
            },
            num2(){
                return this.betNum2.length
            },
            num3(){
                return this.betNum3.length
            },
            num4(){
                return this.betNum4.length
            },
            num5(){
                return this.betNum5.length
            },
            num6(){
                return this.betNum6.length
            },
            num7(){
                return this.betNum7.length
            },
            num8(){
                return this.betNum8.length
            },
            num9(){
                return this.betNum9.length
            },
            num10(){
                return this.betNum10.length
            },
            numGy(){
                return this.betNumGy.length
            },
            //选择注数计算
            notes(){
                if(this.type == 12.1){
                    return this.num1 + this.num2 + this.num3 + this.num4 + this.num5
                }
                if(this.type == 12.2){
                    return  this.num6 + this.num7 + this.num8 + this.num9 + this.num10
                }
               if(this.type == 12.3){
                    return this.numGy
               }else {
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
                return this.$store.state.clientHeight - 100 - h
            }
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
                this.$set(this,'betNum8',[])
                this.$set(this,'betNum9',[])
                this.$set(this,'betNum10',[])
                this.$set(this,'betNumGy',[])
            },
            //添加到号码篮
            addBetBasket(){
                if(this.notes<1){
                    this.$toast('每位至少选择1个号码！');
                    return
                }
                let d1,d2,d3,d4,d5,d6,d7,d8,d9,d10,dgy,betNum;
                d1 = this.num1 ? this.betNum1.join(',') : '-'
                d2 = this.num2 ? this.betNum2.join(',') : '-'
                d3 = this.num3 ? this.betNum3.join(',') : '-'
                d4 = this.num4 ? this.betNum4.join(',') : '-'
                d5 = this.num5 ? this.betNum5.join(',') : '-'
                d6 = this.num6 ? this.betNum6.join(',') : '-'
                d7 = this.num7 ? this.betNum7.join(',') : '-'
                d8 = this.num8 ? this.betNum8.join(',') : '-'
                d9 = this.num9 ? this.betNum9.join(',') : '-'
                d10 = this.num10 ? this.betNum10.join(',') : '-'
                dgy = this.betNumGy.join(',')
                if(this.type == 12.1){
                    betNum = d1 + '|'+ d2 + '|'+ d3 + '|'+ d4 + '|'+ d5
                }
                if(this.type == 12.2){
                    betNum = d6 + '|'+ d7 + '|'+ d8 + '|'+ d9 + '|'+ d10
                }
                if(this.type == 12.3){
                    betNum = dgy
                }

                let data = {}
                data['num'] = betNum
                data['num_text'] = betNum
                data['notes'] =this.notes
                data['type_text'] = this.text
                data['type'] = this.sign
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
                        sign:this.sign,
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
