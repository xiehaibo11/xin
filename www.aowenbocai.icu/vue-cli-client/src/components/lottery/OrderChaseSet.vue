<!--订单页面 追号设置-->
<template>
    <div>
        <mt-header title="追号详情">
            <mt-button icon="back" slot="left" @click.native="chaseVisible = false">取消追号</mt-button>
        </mt-header>
        <div class="bet-join contentH" :style="{height:joinContentH + 'px'}">
            <div class="info flex-box border-bottom-1px">
                <div class="label">追号期数</div>
                <div class="cont">
                    <input-number v-model="chase.chaseNum" :min="2" :max="100" size="small"></input-number> 期
                    <span class="join-tip">最多可追100期</span>
                </div>
            </div>
            <div class="border-bottom-1px" style="background-color: #ffffff;">
                <div class="label f-small c-2 mt-sm mf-sm tl">
                    <div class="tc" style="padding-top: 10px">
                        期号列表
                    </div>
                </div>
                <div class="cont">
                    <table cellpadding="0" cellspacing="0" class="table-list">
                        <tr>
                            <th width="10%">序号</th>
                            <th width="27%">期号</th>
                            <th width="38%">倍数<em class="c-3" style="font-weight: normal"></em></th>
                            <th width="25%">金额</th>
                        </tr>
                        <tr v-for="(item,index) in chase.chaseData" :key="index">
                            <td>{{index + 1}}</td>
                            <td>{{item.expect}}</td>
                            <td><input-number v-model="item.multiple" :min="1" size="small"></input-number> 倍</td>
                            <td>{{getGain(oneMoney,item.multiple)}}{{oneMoney * 1000000 * item.multiple / 1000000}} {{lotteryUnit}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!--底部 start-->
        <div class="bet-foot">
            <div class="multiple f-small">
                <div class="tr f-sm c-2 chase-box">
                    <div class="flex-box border-top-1px" style="padding:0 10px;height: 38px">
                        <div>
                            <em>投</em> <input-number v-model="multiple" :min="1" :step="1" size="small"></input-number> <em>倍</em>
                        </div>
                        <div class="flex-box" style="justify-content: flex-end;">
                            <label class="mint-radiolist-label" style="padding-right: 5px">
                                        <span class="mint-radio">
                                            <input type="checkbox" class="mint-radio-input" v-model="chase.isStop">
                                            <span class="mint-radio-core"></span>
                                        </span>
                                <em class="mint-radio-label" style="margin-left: 0">中奖后停止追号</em>
                            </label>
                            <i @click="showStop" class="iconfont icon-yiwen1 f-sm c-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-box notes-box">
                <div class="flex tl bet-basket">
                    <p>共 {{chaseTotalMoney}} {{lotteryUnit}}</p>
                    <p class="f-mini c-4">追{{chase.chaseNum}}期</p>
                </div>
                <button class="bet-btn btn-sure" @click="submitOrder">追号提交</button>
            </div>
        </div>
        <!--底部 end-->
    </div>
</template>

<script>
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    export default {
        name: 'orderChaseSet',
        components:{
            inputNumber
        },
        props:{
            visible : { //协议选择状态
                type: Boolean,
                require:true,
                default:false
            },
            oneMoney:{
                default: 0
            },
            expect:{
                default: 0
            },
            initMultiple:{
                default: 1
            }
        },
        model:{
            prop: 'visible',
            event: 'change'
        },
        data () {
            return {
                chaseVisible:this.visible,
                //追号数据
                chase:{
                    chaseNum: 4, //默认追号期数
                    chaseMul: 1, //追号倍数设置
                    isStop: false, //中奖是否停止追号
                    chaseData: [], //追号数据内容
                },
                multiple:this.initMultiple
            }
        },
        watch:{
            chaseVisible(val){
                this.$emit('change',val)
            },
            //监听追号期数
            'chase.chaseNum'(val){
                this.doChase();
            },
            multiple(val){
                this.chase.chaseMul = val;
                this.doChase();
            }
        },
        computed:{
            getGain(){
                return (m,n)=>{
                    return this.$bet.accMul(m,n,5)
                }
            },
            //单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //追号、合买容器高度
            joinContentH(){
                return this.$store.state.clientHeight - 130
            },
            //总注数
            totalNotes(){
                return this.$store.getters.getSzcTotalNotes
            },
            //追号总金额
            chaseTotalMoney(){
                let m =0
                if(this.chase.chaseData.length){
                    for(let i in this.chase.chaseData){
                        m = this.$bet.accAdd(m,this.$bet.accMul(this.chase.chaseData[i].multiple ,this.oneMoney))
                    }
                }
                return m
            },
//            maxChase(){
//                let total = parseInt(this.totalIssue); //每天售卖期数
//                let max_chase = this.$store.state.setting.max_chase //最大追号期数
//                return max_chase ? max_chase > total ? total : max_chase : total //控制最多只能追第二天的期号
//            }
        },
        methods:{
            //追号
            doChase() {
                var arr = [];
                var curExpect = parseInt(this.expect);

                for (let i = 0; i < this.chase.chaseNum; i++) {
                    var chaseList = {};
                    chaseList['expect'] = curExpect + i;
                    chaseList['multiple'] = parseInt(this.chase.chaseMul);
                    arr.push(chaseList);
                }
                this.$set(this.chase, 'chaseData', arr);
            },
            submitOrder(){
                this.$emit('submit-order',this.chase)
            },
            //中奖停止解释
            showStop(){
                this.$messagebox({
                    title: '提示',
                    message: '勾选后，您的追号方案中的某一期中奖后，后续的追号订单将被撤销，资金返还您的账户中。如不勾选，系统一直帮您购买所有的追号投注任务。',
                    confirmButtonText:'我知道了'
                });
            }
        },
        created(){
            this.chase.chaseMul = this.initMultiple
            this.doChase();
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
