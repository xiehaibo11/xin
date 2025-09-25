<template>
    <div>
        <div class="loading" v-if="loading">
            <mt-spinner :type="3"></mt-spinner>
        </div>
        <div v-if="!loading && isrefresh" class="contentH" :style="{height: contentH + 'px'}">
            <div class="order-header pd-sm">
                <span><img :src="orders.image" alt="" width="35"></span>
                <b>{{orders.title}}</b>
                <span class="c-3 f-mini">
                    <template v-if="orders.ischase">自购追号 共追号{{chaseIssue}}期</template>
                </span>
                <span class="fr" v-if="orders.status < 1"><mt-button type="danger" size="small" @click.native.self.stop="cancelOrder(orders.lottery_id)">撤单</mt-button></span>
                <div class="flex-box f-sm c-3 win-info">
                    <div class="tc">
                        <p class="c-1">{{orders.total_money}} <em class="f-mini">{{lotteryUnit}}</em></p>
                        <p class="f-mini">订单金额</p>
                    </div>
                    <!--<div class="tc">-->
                        <!--<p v-if="this.orders.status == 0">&#45;&#45;</p>-->
                        <!--<p :class="{'red':this.orders.status == 1}" v-else>{{orders.expect[0].bonus}} <em class="f-mini">{{lotteryUnit}}</em></p>-->
                        <!--<p class="f-mini">中奖金额</p>-->
                    <!--</div>-->
                </div>
            </div>
            <div class="order-cont border-bottom-1px">
                <div>
                    <span class="title">订单状态：</span>
                    <span><em v-html="orders.status_txt"></em><em class="f-mini c-2">（详情查看追号详情）</em></span>
                </div>
            </div>
            <div class="order-detail card border-bottom-1px">
                <div class="title flex-box" @click="isShow=!isShow"  :class="{'border-bottom-1px':isShow}">
                    <span class="join-title">方案内容</span>
                    <span> 共{{orders.betting.length}}条</span>
                    <span class="flex tr"><i class="iconfont icon-xiajiantou c-3" :class="{'hide':!isShow}"></i></span>
                </div>
                <ul class="chose-code c-3" v-show="isShow">
                    <li v-for="item in orders.betting">
                        <span v-html="item"></span>
                    </li>
                </ul>
            </div>
            <div class="order-detail card border-bottom-1px" v-if="orders.ischase">
                <div class="title flex-box" @click="chaseShow=!chaseShow"  :class="{'border-bottom-1px':chaseShow}">
                    <span class="join-title">追号详情</span>
                    <span>共追号{{chaseIssue}}期</span>
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
            <div class="order-foot">
                <p>投注时间：{{orders.create_time}}</p>
                <p v-if="orders.ischase">追号设置：
                    <em v-if="orders.is_stop">中奖后停止追号</em>
                    <em v-else>中奖后继续追号</em>
                </p>
                <p>订单编号：{{orders.lottery_id}}</p>
            </div>
        </div>
    </div>
</template>
<script>
    import stopChase from 'components/orders/StopChase.vue'
    import joinBuy from  'components/orders/JoinBuy.vue'
    export default{
        components: {
            stopChase,
            joinBuy
        },
        data() {
            return {
                loading:true,
                orders:{},
                isShow:true,
                chaseShow:true,
                isrefresh : true
            };
        },
        computed:{
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 40
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
        },
        methods:{
            updateExpect(e){
                this.$set(this.orders,'expect',e)
            },
            //撤单
            cancelOrder(id){
                this.$messagebox.confirm(
                    '确定要撤销该订单吗?'
                ).then(()=>{
                    this.$axios.get('/web/orders/returnTicket',{
                        params:{
                            lottery_id : id
                        }
                    }).then(({data})=>{
                        if(!data.err){
                            this.$messagebox.alert(data.msg).then(action => {
                                this.orders.status = 8
                            });
                        }else {
                            this.$messagebox('提示',data.msg)
                        }
                    })
                }).catch((err)=>{
                });
                return
            }
        },
        created(){
            this.orders = {}
            this.loading = true
            this.$axios.get('/web/orders/getDetails',{
                params:{
                    id:this.id,
                    lottery_id:this.lottery_id
                }
            }).then(({data}) => {
                this.$set(this,'orders',data)
                this.loading = false
            });
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
