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
                    第{{orders.expect[0].expect}}期 自购
                </span>
                <span class="fr" v-if="orders.status < 1"><mt-button type="danger" size="small" @click.native.self.stop="cancelOrder(orders.lottery_id)">撤单</mt-button></span>
                <div class="flex-box f-sm c-3 win-info">
                    <div class="tc">
                        <p>{{orders.total_money}} <em class="f-mini">{{lotteryUnit}}</em></p>
                        <p class="f-mini">订单金额</p>
                    </div>
                    <div class="tc" v-if="orders.show_expect">
                        <p>{{orders.multiple}}</p>
                        <p class="f-mini">倍数</p>
                    </div>
                    <div class="tc">
                        <p v-if="orders.status == 0">--</p>
                        <p :class="{'red':this.orders.status == 1}" v-else>{{orders.expect[0].bonus}} <em class="f-mini">{{lotteryUnit}}</em></p>
                        <p class="f-mini">中奖金额</p>
                    </div>
                </div>
            </div>
            <div class="order-cont border-bottom-1px">
                <div>
                    <span class="title">订单状态：</span>
                    <span v-html="orders.status_txt"></span>
                </div>
                <div>
                    <span class="title">开奖号码：</span>
                    <span v-if="!orders.expect[0].code.length">--</span>
                    <span v-else v-for="item in orders.expect[0].code" class="red code"><b>{{item}}</b></span>
                </div>
            </div>
            <div class="order-detail card border-bottom-1px">
                <div class="title flex-box" @click="isShow=!isShow"  :class="{'border-bottom-1px':isShow}">
                    <span class="join-title">方案内容</span>
                    <span>共{{orders.betting.length}}条</span>
                    <span class="flex tr"><i class="iconfont icon-xiajiantou c-3" :class="{'hide':!isShow}"></i></span>
                </div>
                <ul class="chose-code c-3" v-show="isShow">
                    <li v-for="item in orders.betting" v-html="item" ref="codeItem"></li>
                </ul>
            </div>
            <div class="order-foot">
                <p>投注时间：{{orders.create_time}}</p>
                <p>订单编号：{{orders.lottery_id}}</p>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        data() {
            return {
                loading:true,
                orders:{},
                isShow:true,
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
            }
        },
        methods:{
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
        created() {
            this.orders = {}
            this.loading = true
            this.$axios.get('/web/orders/getDetails', {
                params: {
                    id: this.id,
                    lottery_id: this.lottery_id
                }
            }).then(({data}) => {
                this.$set(this, 'orders', data)
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
            li.code-item{
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
