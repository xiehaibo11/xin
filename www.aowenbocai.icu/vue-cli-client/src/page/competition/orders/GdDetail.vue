<template>
    <div>
        <!--<div class="page-loading" v-if="loading">-->
            <!--<mt-spinner :type="3"></mt-spinner>-->
        <!--</div>-->
        <div v-if="!loading" class="contentH" :style="{height: contentH + 'px'}">
            <div class="gd-header pd-sm card">
                <div class="border-bottom-1px" style="padding-bottom: 10px;">
                    <div>
                        <span><img :src="orders.image" alt="" width="35"></span>
                        <b>{{orders.title}}</b>
                        <span class="c-3 f-mini">第{{orders.expect[0].expect}}期</span>
                    </div>
                    <div class="gd-xy f-sm">跟着我一起中大奖！！！</div>
                </div>
                <div class="flex-box win-info mt-sm">
                    <div class="tc flex">
                        <p>{{orders.total_money}} <em class="f-mini">{{lotteryUnit}}</em></p>
                        <p class="f-mini c-3">自投金额</p>
                    </div>
                    <div class="tc flex">
                        <p>{{orders.total_money}} <em class="f-mini">{{lotteryUnit}}</em></p>
                        <p class="f-mini c-3">起跟金额</p>
                    </div>
                    <div class="tc flex">
                        <p v-if="this.orders.status_num == 0">--</p>
                        <p :class="{'red':this.orders.status_num == 1}" v-else>{{orders.expect[0].bonus}} <em class="f-mini">{{lotteryUnit}}</em></p>
                        <p class="f-mini c-3">奖金</p>
                    </div>
                </div>
            </div>
            <div class="gd-detail card mt-sm">
                <div class="title flex-box border-bottom-1px" @click="isShow=!isShow">
                    <em class="iconfont icon-pingjia c-3"></em>
                    <span class="f-large" style="padding-left: 5px">方案信息</span>
                    <span class="flex tr">
                         <i class="iconfont icon-xialajiantou animate-jt" :class="isShow ? 'is-active' : 'no-active'"></i>
                    </span>
                </div>
                <ul class="gd-order-info" v-show="isShow">
                    <li>
                        <span class="info-label">发起人：</span>
                        <span class="info-cont">张三</span>
                    </li>
                    <li>
                        <span class="info-label">金额：</span>
                        <span class="info-cont">4000{{lotteryUnit}}</span>
                    </li>
                    <li>
                        <span class="info-label">跟单信息：</span>
                        <span class="info-cont">
                            <mt-button size="small" type="danger" @click.native="popupVisible = true">查看跟单列表<i class="iconfont"></i></mt-button>
                        </span>
                    </li>
                    <li>
                        <span class="info-label">佣金：</span>
                        <span class="info-cont">10%</span>
                    </li>
                    <li>
                        <span class="info-label">过关方式：</span>
                        <span class="info-cont">2串1</span>
                    </li>
                    <li>
                        <span class="info-label">投注信息：</span>
                        <span class="info-cont red">开赛后可查看投注详情</span>
                    </li>
                </ul>
            </div>
            <div class="gd-detail card mt-sm">
                <div class="title flex-box border-bottom-1px" @click="showDetail=!showDetail">
                    <em class="iconfont icon-touzhujilu f-large c-3"></em>
                    <span class="f-large" style="padding-left: 5px">投注内容</span>
                    <span class="flex tr">
                         <i class="iconfont icon-xialajiantou animate-jt" :class="showDetail ? 'is-active' : 'no-active'"></i>
                    </span>
                </div>
                <div class="card-pd" v-if="showDetail">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="bet-detail">
                        <tr>
                            <th width="20%">场次</th>
                            <th width="25%">主队VS客队</th>
                            <th width="20%">玩法</th>
                            <th width="20%">投注</th>
                            <th width="15%">彩果</th>
                        </tr>
                        <tr>
                            <td>周二<br>001</td>
                            <td>
                                <p>葡萄牙</p>
                                <p>1:1</p>
                                <p>波兰</p>
                            </td>
                            <td>胜平负</td>
                            <td>胜<br>1.44</td>
                            <td>平</td>
                        </tr>
                        <tr>
                            <td>周二<br>001</td>
                            <td>
                                <p>葡萄牙</p>
                                <p>1:1</p>
                                <p>波兰</p>
                            </td>
                            <td>胜平负</td>
                            <td>胜<br>1.44</td>
                            <td>平</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="gd-foot">
                <p>投注时间：{{orders.create_time}}</p>
                <p>订单编号：{{orders.lottery_id}}</p>
            </div>
        </div>
        <!--底部-->
        <div class="gd-buy flex-box" v-show="!loading">
            <em class="f-sm c-4" style="margin-right: 2px">购买</em>
            <input-number v-model='multiple' :min='1' :step='1' size="small"></input-number>
            <em class="f-sm c-4" style="margin-left: 2px">倍</em>
            <span class="c-4 f-sm" style="padding-left: 10px"><em class="yellow f-large">{{2*multiple}}</em> {{lotteryUnit}}</span>
            <div class="flex tr">
                <mt-button class="btn-buy" type="danger" size="small" @click.native="submitOrder">立即跟单</mt-button>
            </div>
        </div>
        <!--底部 end-->
        <!--跟单列表-->
        <mt-popup class="gendan-person-list"
            v-model="popupVisible"
            position="right">
            <div>
                <mt-header title="跟单列表">
                    <mt-button icon="back" slot="left" @click.native="popupVisible = false"></mt-button>
                </mt-header>
                <div class="contentH" :style="{height: gdH + 'px'}">
                    <div class="gd-person-head flex-box">
                        <span class="p-name">跟单用户</span>
                        <span class="p-mul">倍数</span>
                        <span class="p-money">金额({{lotteryUnit}})</span>
                        <span class="p-time">时间</span>
                    </div>
                    <div class="gd-person-cont">
                        <div class="list-item flex-box border-bottom-1px">
                            <span class="p-name">张三</span>
                            <span class="p-mul">50</span>
                            <span class="p-money org">100</span>
                            <span class="p-time">2018/05/04</span>
                        </div>
                        <div class="list-item flex-box border-bottom-1px">
                            <span class="p-name">李四</span>
                            <span class="p-mul">50</span>
                            <span class="p-money org">50</span>
                            <span class="p-time">2018/05/04</span>
                        </div>
                        <div class="list-item flex-box border-bottom-1px">
                            <span class="p-name">王五</span>
                            <span class="p-mul">50</span>
                            <span class="p-money org">200</span>
                            <span class="p-time">2018/05/04</span>
                        </div>
                    </div>
                </div>
            </div>
        </mt-popup>
        <!--跟单列表 end-->
    </div>
</template>
<script>
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    export default{
        name: 'gdDetail',
        components: {
            inputNumber
        },
        data() {
            return {
                loading:true,
                orders:{},
                isShow:true,
                showDetail:true,
                popupVisible:false,
                multiple:'1',
            };
        },
        computed:{
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 93
            },
            //内容高度
            gdH(){
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
            }
        },
        methods:{
            submitOrder(){
                this.$axios.post('/index/orders/getDetails',{
                    gd_multiple: this.multiple
                }).then(({data})=>{
                    this.$messagebox('提示','跟单成功！')
                })
            }
        },
        created() {
            this.orders = {}
            this.loading = true
            this.$store.commit('setLoadStatus',true)
            this.$axios.get('/index/orders/getDetails?lottery_id=PK10|115523618731801&id=44').then(({data}) => {
                this.$set(this, 'orders', data)
                this.$store.commit('setLoadStatus',false)
                this.loading = false
            });
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .page-loading{
        width: 28px;
        height: 28px;
        margin: 15px auto;
    }
    .gd-xy{
        margin-top: 5px;
        line-height: 1.4;
        color: #666666;
    }
    .gd-detail{
       .title{
           padding: 0 10px;
           height: 40px;
           line-height: 40px;
       }
       .gd-order-info{
           li{
               font-size: 14px;
               height: 42px;
               line-height: 42px;
               border-bottom: 1px solid #f5f5f5;
               margin: 0 15px;
               display: flex;
               .info-label{
                   color: #888888;
                   padding-left: 16px;
               }
               .info-cont{
                   flex: 1;
                   text-align: right;
               }
           }
       }
       .bet-detail{
           table-layout: fixed;
           th,td{
               border:1px solid #dcdcdc;
               text-align: center;
               font-size: 12px;
               line-height: 1.2;
               padding: 3px 0;
           }
           th{
               background-color: #fff6f5;
               padding: 7px 0;
               font-weight: normal;
           }
       }
   }
    .gd-foot{
        padding: 10px;
        font-size: 13px;
        line-height: 2;
        color: #9c3a01;
    }

    .gendan-person-list{
        padding: 0;
        .p-name{width: 25%;}
        .p-mul{width: 20%;}
        .p-money{width: 25%;}
        .p-time{width: 30%;}
        $p-height:45px;
        .gd-person-head{
            color: #888888;
            span{
                display: inline-block;
                text-align: center;
                height: $p-height;
                line-height: $p-height;
                font-size: 14px;
                background-color: #ececec;
                &.p-name{padding-left: 10px}
                &.p-time{padding-right: 10px}
            }
        }
        .gd-person-cont{
            padding: 0 10px;
            .list-item{
                height: $p-height;
                line-height: $p-height;
                color: #888888;
                span{
                    display: inline-block;
                    text-align: center;
                    font-size: 14px;
                }
            }
        }
    }

    .gd-buy{
        padding: 0 10px;
        height: 53px;
        background-color: #333333;
    }
</style>
