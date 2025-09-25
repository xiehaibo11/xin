<template>
    <div>
        <div class="loading" v-if="loading">
            <mt-spinner :type="3"></mt-spinner>
        </div>
        <div v-if="!loading" class="contentH" :style="{height: contentH + 'px'}">
            <div class="order-header pd-sm border-bottom-1px">
                <span><img :src="orders.image" alt="" width="35"></span>
                <b>{{orders.title}}</b>
                <span class="c-3 f-mini">
                    <template v-if="orders.ischase">追号合买 共追号{{chaseIssue}}期</template>
                </span>
                <div class="f-sm mt-sm c-2">
                    <div>
                        <span>发起人： <em class="f-mini">{{orders.nickname}}</em></span>
                    </div>
                    <div>
                        <span>合买宣言： <em class="f-mini">{{orders.declaration}}</em></span>
                    </div>
                </div>
            </div>
            <div class="join-cont flex-box border-bottom-1px">
                <div class="progress tc border-right-1px">
                    <progress-circle :progress="orders.progress.progress" :bd-progress="orders.progress.assure_progress"></progress-circle>
                    <div class="f-mini c-3 tc">合买进度</div>
                </div>
                <div class="progress-info">
                    <p>
                        <span class="label">方案金额：</span>
                        <span class="red f-large">{{orders.total_money}} <em class="f-mini c-3">{{lotteryUnit}}</em></span>
                    </p>
                    <p>
                        <span class="label">总份数：</span>
                        <span class="red f-large">{{orders.total_share}} <em class="f-mini c-3">份</em></span>
                    </p>
                    <p>
                        <span class="label">剩余份数：</span>
                        <span class="f-large" :class="{'red': orders.progress.lost_num > 0}">{{orders.progress.lost_num}} <em class="f-mini c-3">份</em></span>
                    </p>
                    <p>
                        <span class="label">每份金额：</span>
                        <span class="red f-large">{{getPerMoney}} <em class="f-mini c-3">{{lotteryUnit}}</em></span>
                    </p>
                    <p v-if="isGain">
                        <span class="label">盈利佣金：</span>
                        <span>{{orders.gain}} <em class="f-mini c-3">%</em></span>
                    </p>
                    <p v-if="isGain">
                        <span class="label">承诺保底：</span>
                        <span>{{orders.assure_money}} <em class="f-mini c-3">份 (占{{orders.progress.assure_progress}}%)</em></span>
                    </p>
                    <p>
                        <span  class="label">截止时间：</span>
                        <span class="label">{{orders.end_time}}</span>
                    </p>
                </div>
            </div>
            <div class="pd card border-bottom-1px">
                <span class="join-title">方案状态</span><span v-html="orders.status_txt"></span>
                <!--<span v-if="orders.expect[0].status == 0" class="c-2">未出票</span>-->
                <!--<span v-if="orders.expect[0].status == 1" class="red">等待开奖</span>-->
                <!--<span v-if="orders.expect[0].status == 2 && orders.expect[0].bonus > 0" class="red">已中奖 奖金{{orders.expect[0].bonus}} {{lotteryUnit}}</span>-->
                <!--<span v-if="orders.expect[0].status == 2 && orders.expect[0].bonus == 0" class="c-3">未中奖</span>-->
                <!--<span v-if="orders.expect[0].status == 6" class="c-3">流产撤单</span>-->
                <!--<span v-if="orders.expect[0].status == 7" class="c-3">系统撤单</span>-->
                <!--<span v-if="orders.expect[0].status == 8" class="c-3">用户撤单</span>-->
            </div>
            <div class="pd order-cont card border-bottom-1px" v-if="!orders.ischase">
                <span class="join-title">开奖号码</span>
                <span v-if="!orders.expect[0].code.length">--</span>
                <span v-else v-for="item in orders.expect[0].code" class="red code"><b>{{item}}</b></span>
            </div>
            <div class="order-detail card order-cont border-bottom-1px">
                <div class="title flex-box" @click="isShow=!isShow"  :class="{'border-bottom-1px':isShow}">
                    <span class="join-title">方案内容</span><em v-if="orders.show == 0 || orders.isShow || orders.fristBuy">共{{orders.betting.length}}条</em>
                    <span class="flex tr" v-if="orders.show == 0 || orders.isShow || orders.fristBuy"><i class="iconfont icon-xiajiantou c-3" :class="{'hide':!isShow}"></i></span>
                    <!--截止后公开-->
                    <template v-if="orders.show == 1 && !orders.isShow && !orders.fristBuy">
                        <span class="flex tl">截止后公开</span>
                    </template>
                    <!--完全保密-->
                    <template v-if="orders.show == 2 && !orders.isShow && !orders.fristBuy">
                        <span class="flex tl">仅跟单人可见</span>
                    </template>
                    <!--仅跟单人可见-->
                    <template v-if="orders.show == 3 && !orders.isShow && !orders.fristBuy">
                        <span class="flex tl">完全保密</span>
                    </template>
                </div>
                <!--完全公开-->
                <template v-if="orders.show == 0 || orders.isShow || orders.fristBuy">
                    <ul class="chose-code c-3" v-show="isShow">
                        <li v-for="item in orders.betting">
                            <span v-html="item"></span>
                        </li>
                    </ul>
                </template>
            </div>
            <div class="order-detail card border-bottom-1px">
                <div class="title flex-box" @click="chaseShow=!chaseShow"  :class="{'border-bottom-1px':chaseShow}">
                    <span class="join-title">期号详情 </span><em>共{{chaseIssue}}期</em>
                    <span class="flex tr"><i class="iconfont icon-xiajiantou c-3" :class="{'hide':!chaseShow}"></i></span>
                </div>
                <div v-show="chaseShow" class="c-3 chase-detail">
                    <table border='0' cellspacing="0" cellpadding="0">
                        <tr>
                            <th width="22%" style="text-align: left;padding-left: 2px">期号</th>
                            <th width="20%" style="text-align: left;padding-left: 2px">开奖号码</th>
                            <th width="10%">倍数</th>
                            <th width="15%">金额</th>
                            <!--<th width="20%">状态</th>-->
                            <th width="15%">奖金/状态</th>
                            <th width="13%"  v-if="orders.fristBuy">操作</th>
                        </tr>
                        <tr v-for="(item,index) in orders.expect" :key="index">
                            <td width="22%" style="text-align: left;padding-left: 2px">{{item.expect}}</td>
                            <td width="20%" style="text-align: left;padding-left: 2px;word-wrap: break-word; width: 85px;display: block;height:auto;padding: 5px 0;">
                                <template v-if="item.code.length">
                                    <em v-for="(co,p) in item.code" class="red">{{co}}<i v-if="p!==(item.code.length-1)">,</i></em>
                                </template>
                                <template v-else>--</template>
                            </td>
                            <td width="10%">{{item.multiple}}</td>
                            <td width="15%">{{orders.total_money/mulTotal*item.multiple}}{{lotteryUnit}}</td>
                            <!--<td width="20%">-->
                            <!--<span v-if="item.newStatus==0">未出票</span>-->
                            <!--<span v-if="item.newStatus==1">已出票</span>-->
                            <!--<span v-if="item.newStatus==2">已完成</span>-->
                            <!--<span v-if="item.newStatus==3">中奖停止</span>-->
                            <!--<span v-if="item.newStatus==4">进行中</span>-->
                            <!--<span v-if="item.newStatus==5">停止追号</span>-->
                            <!--<span v-if="item.newStatus==6">流产撤单</span>-->
                            <!--<span v-if="item.newStatus==7">系统撤单</span>-->
                            <!--<span v-if="item.newStatus==8">用户撤单</span>-->
                            <!--</td>-->
                            <td width="15%">
                                <span v-if="item.newStatus==0">未出票</span>
                                <span v-if="item.newStatus==1">已出票</span>
                                <span v-if="item.newStatus==2">
                                   <b v-if="item.bonus>0" :class="{'red':item.bonus>0}">{{item.bonus}}</b>
                                   <em v-else>未中奖</em>
                               </span>
                                <span v-if="item.newStatus==3">中奖停止</span>
                                <span v-if="item.newStatus==4">进行中</span>
                                <span v-if="item.newStatus==5">停止追号</span>
                                <span v-if="item.newStatus==6">流产撤单</span>
                                <span v-if="item.newStatus==7">系统撤单</span>
                                <span v-if="item.newStatus==8">用户撤单</span>
                                <!--<span v-if="item.status==2"><b :class="{'red':item.bonus>0}">{{item.bonus}}</b></span>-->
                                <!--<span v-else>&#45;&#45;</span>-->
                            </td>
                            <td width="13%" v-if="orders.fristBuy">
                                <template v-if="item.newStatus==0">
                                    <stop-chase :lottery-id="orders.lottery_id" :order-id="orders.id" :id="item.id" @reload-expect="updateExpect"></stop-chase>
                                </template>
                                <template v-else>--</template>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="order-detail card border-bottom-1px">
                <div class="title flex-box" @click="getJoinList" :class="{'border-bottom-1px':joinShow}">
                    <span class="join-title">参与用户</span>
                    <em>已有<b class="red">{{orders.joinNum}}</b>人参与，共认购<b class="red">{{orders.progress.buy_num}}</b>份</em>
                    <span class="flex tr"><i class="iconfont icon-xiajiantou c-3" :class="{'hide':!joinShow}"></i></span>
                </div>
                <div v-show="joinShow" class="c-3 chase-detail">
                    <table border='0' cellspacing="0" cellpadding="0">
                        <tr>
                            <th width="15%" style="text-align: left;padding-left: 2px">用户名</th>
                            <th>认购份数 | 总金额</th>
                            <th width="20%">奖金</th>
                            <th width="35%">认购时间</th>
                        </tr>
                        <tr v-for="(item,index) in joinList" :key="index">
                            <td width="15%" style="text-align: left;padding-left: 2px">
                                <em v-if="orders.fristBuy">{{item.username}}</em>
                                <em v-else>{{item.username.substring(0,1)}}***</em>
                            </td>
                            <td><em class="red">{{item.money}}</em>份 | <em class="red">{{item.user_buy}}</em>{{lotteryUnit}}</td>
                            <td width="20%">{{item.bonus}}</td>
                            <td width="35%">{{item.create_time}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="order-foot">
                <p>发起时间：{{orders.create_time}}</p>
                <p v-if="orders.ischase">追号设置：
                    <em v-if="orders.is_stop">中奖后停止追号</em>
                    <em v-else>中奖后继续追号</em>
                </p>
                <p>订单编号：{{orders.lottery_id}}{{isGain}}</p>
            </div>
        </div>
        <!--购买-->
        <template v-if="!loading">
            <div class="buy-grounp flex-box">
                <template v-if="orders.joinStatus && parseInt(orders.progress.progress) !== 100">
                    <div class="f-sm c-4">剩余份数：<em class="f-large yellow">{{orders.progress.lost_num}}</em> <em class="c-3">份</em></div>
                    <div class="join-buy flex-box" style="justify-content: flex-end;">
                        <input-number v-model='value' :max='orders.progress.lost_num' :min='1' :step='1'></input-number> <em class="f-mini c-3" style="margin-left: 5px">份</em>
                        <mt-button class="btn-buy" type="danger" size="small" @click.native="submitOrder">购买</mt-button>
                        <!--订单信息确认-->
                        <mt-popup
                            v-model="orderVisible"
                            position="bottom">
                            <div class="orders-info" v-if="orderVisible">
                                <div class="order-title org border-bottom-1px">订单信息确认</div>
                                <div class="order-info">
                                    <div>
                                        <span class="name">投注彩种:</span>
                                        <span class="info">{{orders.title}}</span>
                                    </div>
                                    <div>
                                        <span class="name">投注方式:</span>
                                        <span class="info">参与合买</span>
                                    </div>
                                    <div>
                                        <span class="name">认购份数:</span>
                                        <span class="info"><b class="red f-large">{{value}}</b>份 （共<em class="red">{{buyMoney(value)}}</em>{{lotteryUnit}}）</span>
                                    </div>
                                    <div>
                                        <span class="name">账户余额:</span>
                                        <span class="info"><em>{{money}}</em> {{lotteryUnit}}</span>
                                        <span class="tips tc" style="font-size: 14px;color: #333" v-if="money < buyMoney(value)"><b class="red">（余额不足，请充值!）</b></span>
                                    </div>
                                </div>
                                <div class="order-footer tr">
                                    <mt-button class="cancel" @click="orderVisible = false" size="small">取消</mt-button>
                                    <mt-button type="primary"  size="small" v-if="money < buyMoney(value)" @click="goPay">立即充值</mt-button>
                                    <mt-button type="primary" v-else @click="payment" size="small">
                                        <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="orderLoading"></mt-spinner>
                                        确定购买
                                    </mt-button>
                                </div>
                            </div>
                        </mt-popup>
                    </div>
                </template>
                <template v-if="orders.joinStatus && parseInt(orders.progress.progress) == 100">
                    <mt-button class="disabled-btn" size="small" disabled>方案已满员</mt-button>
                </template>
                <template v-if="orders.status_num !== 7 && !orders.joinStatus">
                    <mt-button class="disabled-btn" size="small" disabled>合买已截止</mt-button>
                </template>
                <template v-if="orders.status_num == 7 && !orders.joinStatus">
                    <mt-button class="disabled-btn" size="small" disabled>系统撤单</mt-button>
                </template>
            </div>
        </template>
    </div>
</template>
<script>
    import stopChase from 'components/orders/StopChase.vue'
    import progressCircle from 'components/common/progressCircle.vue'
//    import joinBuy from  'components/orders/JoinBuy.vue'
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    export default{
        name:'joinDetail',
        components: {
            stopChase,
            progressCircle,
            inputNumber
        },
        data() {
            return {
                loading:true,
                orders:{},
                isShow:false, //方案内容
                chaseShow:false, //期号详情
                joinShow:false,//参与用户
                joinList:[], //参与列表

                orderLoading: false,
                value:1,
                orderVisible:false,
            };
        },
        computed:{
            money(){ //账户余额
                return this.$store.state.userinfo.money
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 93
            },
            lottery_id(){
                return this.$route.query.lottery_id;
            },
            id(){
                return this.$route.query.id;
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            isGain(){ //是否开启提成
                return this.$store.state.setting.isGain
            },
            //追号总期数
            chaseIssue() {
                return this.orders.expect.length;
            },

            //追号总倍数
            mulTotal() {
                var mul = 0;
                for (var i in this.orders.expect) {
                    mul = mul + this.orders.expect[i].multiple;
                }
                return mul;
            },
            //计算每份金额
            getPerMoney(){
                let perMoney = this.$bet.accDiv(Number(this.orders.total_money),Number(this.orders.total_share),3)
                return perMoney ? perMoney : 1
            }
        },
        methods:{
            //计算购买金额
            buyMoney(buy){
                return this.$bet.accMul(this.getPerMoney,Number(buy),3)
            },
            //获取合买人列表
            getListData(){
                this.$axios.get('/index/orders/joinlist',{
                    params:{
                        name:this.orders.name,
                        buyid:this.orders.id
                    }
                }).then(({data}) => {
                    this.$set(this,'joinList',data.data)
                });
            },
            //获取订单信息
            getOrderInfo(){
                this.$axios.get('/web/orders/getDetails',{
                    params:{
                        id:this.id,
                        lottery_id:this.lottery_id
                    }
                }).then(({data}) => {
                    this.$set(this,'orders',data)
                });
            },
            //获取参与用户
            getJoinList(){
                if(this.joinShow){
                    this.joinShow = false
                }else {
                    this.joinShow = true
                    if(!this.joinList.length){
                       this.getListData();
                    }
                }
            },
            //更新期号信息
            updateExpect(e){
                this.$set(this.orders,'expect',e)
            },
            //购买成功后更新订单信息
            updateJoinList(){
                this.$store.commit('setJoinIndex',this.$route.query.i)
                this.getListData();
                this.getOrderInfo();
            },
            //订单信息确认
            submitOrder(){
                this.$store.commit('setLoadStatus',true)
                this.$axios('/index/moblie/checkLogin').then(({data}) => {
                    this.$store.commit('setBauth', data.status);
                    this.$store.commit('setLoadStatus',false)
                    if(!data.status){
                        this.$router.replace({
                            path:'/login',
                            query:{
                                redirect:this.$router.currentRoute.fullPath
                            }
                        })
                    }else {
                        this.$store.dispatch("getUserInfo"); //更新用户信息
                        this.orderVisible = true
                    }
                });
            },
            //充值
            goPay(){
                this.$router.replace({
                    path:'/pay',
                    query:{
                        redirect:this.$router.currentRoute.fullPath
                    }
                })
            },
            //付款
            payment(){
                this.orderLoading = true
                this.$axios.post('/web/orders/buyJoin',{
                    buy_id: this.orders.id,
                    lottery_id: this.orders.lottery_id,
                    money: this.value
                }).then(({data}) =>{
                    if(!data.err){
                        this.value = 1 ;
                        this.$store.dispatch('getUserInfo') //更新用户信息
                    }
                    this.orderVisible = false
                    this.orderLoading = false
                    setTimeout(()=>{
                        this.$messagebox({
                            title: '提示',
                            message: data.msg
                        });
                    },200)
                    this.updateJoinList();
                }).catch(function (error) {
                    console.log(error);
                })
            }
        },
        created(){
            this.$store.commit('setJoinIndex',-1)
            this.orders = {}
            this.joinShow = false
            this.joinList = []
            this.isShow = false
            this.chaseShow = false
            this.loading = true
            this.$axios.get('/index/orders/getDetails',{
                params:{
                    id:this.id,
                    lottery_id:this.lottery_id
                }
            }).then(({data}) => {
                this.$set(this,'orders',data)
                this.loading = false
            });
            this.$store.commit('setKeepAlivePage','joinDetail')
            if(this.$route.query.buy){
                this.value = this.$route.query.buy
                this.orderVisible = true
            }
        },
        beforeRouteLeave(to, from, next){
            if(to.path =='/login' || to.path == '/pay'){
                this.$store.commit('setKeepAlivePage','joinDetail')
            }else {
                this.$store.commit('delKeepAlivePage','joinDetail')
            }
            next();
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .buy-grounp{
        height: 53px;
        width: 100%;
        padding: 10px;
        background-color: #333333;
        .disabled-btn{
            margin: 0 auto;
        }
    }
    .mint-button.is-disabled{
        opacity: 0.9;
    }
    .pd{
        padding: 0px 10px;
        height: 42px;
        line-height: 42px;
        font-size: 15px;
    }
    .join-title{
        color: $color-font-secondary;
        font-size: 13px;
        padding-right: 15px;
    }
    .loading{
        width: 28px;
        height: 28px;
        margin: 30px auto;
    }
    .order-header{
        background-color: #ffffff;
        .win-info{
            margin-top: 5px;
            div{
                margin: 0 8px;
            }
        }
    }
    .order-cont{
        padding: 10px;
        font-size: 15px;
        line-height: 1.8;
        .code{
            b{
                padding: 0 2px;
            }
        }
    }
    .join-cont{
        padding: 10px;
        align-items: flex-start;
        .progress{
            padding-right: 10px;
        }
        .progress-info{
            padding-left: 10px;
            line-height: 1.4;
            span.label{
                font-size: 12px;
                color: $color-font-secondary;
            }
        }
    }
    .order-detail{
        padding:0 10px;
        font-size: 15px;
        line-height: 1.8;
        .title{
            padding: 5px 0;
            i{
                display: inline-block;
                transform: rotate(180deg);
                font-size: 18px;
                &.hide{
                    transform: rotate(0deg);
                }
            }
        }
        .chose-code{
            padding-bottom: 5px;
            li{
                line-height: 2;
                font-size: 13px;
            }
        }
        .chase-detail{
            padding: 5px 0;
            table{
                width: 100%;
            }
            table td,th{
                font-size: 12px;
                line-height: 1.2;
                text-align: center;
                height: 26px;
            }
            table th{
                background-color: #fffbe1;
                font-weight: normal;
                color: #333333;
            }
        }
    }
    .order-foot{
        padding: 10px;
        font-size: 13px;
        line-height: 2;
        color: #9c3a01;
    }

</style>
