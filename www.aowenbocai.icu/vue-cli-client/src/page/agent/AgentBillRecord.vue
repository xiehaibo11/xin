<template>
    <div>
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
            <div slot="right">
                <date-actions @change-date="changeDate"></date-actions>
            </div>
        </mt-header>
        <div>
            <input-search placeholder="账号/昵称查询" v-model="value" @search="doSearch"></input-search>
            <div>
                <mt-navbar v-model="tab" class="label-large">
                    <mt-tab-item id="1">账户明细</mt-tab-item>
                    <mt-tab-item id="2">充值明细</mt-tab-item>
                    <mt-tab-item id="3">兑换明细</mt-tab-item>
                    <mt-tab-item id="4" v-if="gameStatus">转账明细</mt-tab-item>
                </mt-navbar>
            </div>
            <div class="contentH" :style="{height:contentH + 'px'}" style="margin-top: 3px">
                <!--账户明细-->
                <page-item :url="url" v-if="tab == 1" key="tab1">
                    <template slot-scope="props">
                        <a class="mint-cell" v-for="(item,index) in props.data" :key="index">
                            <div class="mint-cell-head"><em class="f-sm c-3">会员账号：</em>{{item.nickname}}</div>
                            <div class="mint-cell-wrapper">
                                <div class="mint-cell-title">
                                    <span class="mint-cell-text">{{item.create_time}}</span>
                                    <span class="mint-cell-label">{{item.remark}}</span>
                                </div>
                                <div class="mint-cell-value">
                                    <span class="mint-cell-label">
                                        <span class="f-large" :class="item.money>0 ? 'suc' : 'red'" v-if="item.money>0">+{{item.money}}</span>
                                        <span class="f-large" :class="item.money>0 ? 'suc' : 'red'" v-else>{{item.money}}</span>
                                        <em class="f-sm">{{lotteryUnit}}</em>
                                    </span>
                                </div>
                                <!--<i class="mint-cell-allow-right"></i>-->
                            </div>
                        </a>
                    </template>
                </page-item>
                <!--充值明细-->
                <page-item :url="url" v-if="tab == 2" key="tab2">
                    <template slot-scope="props">
                        <a class="mint-cell" v-for="(item,index) in props.data" :key="index">
                            <div class="mint-cell-head"><em class="f-sm c-3">会员账号：</em>{{item.nickname}}</div>
                            <div class="mint-cell-wrapper">
                                <div class="mint-cell-title">
                                    <span class="mint-cell-text">{{item.name}}{{item.money}}</span>
                                    <span class="mint-cell-label">{{item.create_time}}</span>
                                </div>
                                <div class="mint-cell-value" style="display: block;text-align: right">
                                    <span>
                                        <template v-if="item.statuss == 0">
                                            <template v-if="item.name == '微信扫码充值'|| item.name == '支付宝扫码充值'">
                                                <em class="org">充值审核中</em>
                                            </template>
                                            <template v-else>
                                                <em class="c-3">等待付款</em>
                                            </template>
                                        </template>
                                        <span class="suc" v-if="item.statuss == 1">充值成功</span>
                                        <span class="red" v-if="item.statuss == 2">充值失败</span>
                                    </span>
                                    <span class="mint-cell-label" v-if="item.remark">
                                        备注：{{item.remark}}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>
                </page-item>
                <!--兑换明细-->
                <page-item :url="url" v-if="tab == 3" key="tab3">
                    <template slot-scope="props">
                        <a class="mint-cell" v-for="(item,index) in props.data" :key="index">
                            <div class="mint-cell-head"><em class="f-sm c-3">会员账号：</em>{{item.nickname}}</div>
                            <div class="mint-cell-wrapper">
                                <div class="mint-cell-title">
                                    <span class="mint-cell-text">{{item.name}}</span>
                                    <span class="mint-cell-label">{{item.create_time}}</span>
                                </div>
                                <div class="mint-cell-value">
                                    <span v-html="item.status"></span>
                                    <span class="mint-cell-label">兑换数量:<em class="org">{{item.num}}</em> 消耗{{lotteryUnit}}:<em class="org">{{item.money}}</em></span>
                                </div>
                            </div>
                        </a>
                    </template>
                </page-item>
                <!--转账明细-->
                <page-item :url="url" v-if="tab == 4" key="tab4">
                    <template slot-scope="props">
                        <a class="mint-cell" v-for="(item,index) in props.data" :key="index">
                            <div class="mint-cell-head"><em class="f-sm c-3">会员账号：</em>{{item.nickname}}</div>
                            <div class="mint-cell-wrapper">
                                <div class="mint-cell-title">
                                    <span class="mint-cell-text">{{item.remark}}</span>
                                    <span class="mint-cell-label">{{item.create_time}}</span>
                                </div>
                                <div class="mint-cell-value">
                                    <span :class="item.money>0 ? 'suc' : 'red'" v-if="item.money>0">+{{item.money}}</span>
                                    <span :class="item.money>0 ? 'suc' : 'red'" v-else>{{item.money}}</span>
                                    <em class="c-3">{{gameUnit}}</em>
                                </div>
                            </div>
                        </a>
                    </template>
                </page-item>
            </div>
        </div>
    </div>
</template>
<script>
    import pageItem from 'components/common/PageItem.vue' //分页
    import InputSearch from 'components/common/InputSearch.vue' //搜索
    import DateActions from 'components/common/DateActions.vue' //按时间查询
    export default{
        name:'agentBillRecord',
        components: {
            pageItem,
            InputSearch,
            DateActions
        },
        data() {
            return {
                tab:'1',
                time : 1 , //时间查询值
                value : '', //搜索值
                name:'',

            };
        },
        computed:{
            title(){
                let name = this.$route.query.name ? ' ('+this.$route.query.name+')' : ' (我的会员)'
                return this.$route.meta.title + name
            },
            id(){
                return this.$route.query.id || this.$store.state.userinfo.id
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            gameStatus(){
                return this.$store.state.setting.game_status
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 148
            },
            //数据地址
            url(){
                let name = this.name ? '&name='+ this.name : ''
                let id = this.id ? '&userid=' + this.id : ''
                let urlObj = {
                    1 : '/web/details/list?time=' + this.time + id +name,
                    2 : '/web/details/rechargelist?time=' + this.time + id+ name,
                    3 : '/web/details/exchangelist?time=' + this.time + id+ name,
                    4 : '/web/details/flowerlist?time=' + this.time + id+ name,
                }
                return urlObj[Number(this.tab)]
            }
        },
        methods:{
            //按照时间查询
            changeDate(emitVal){
                this.time = emitVal
            },
            //搜索
            doSearch(){
                this.name = this.value
            }
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .mint-cell{
        margin-top: 10px;
        padding: 5px 0;
        @include shadow(0px,0px,3px, #ededed);
        .mint-cell-value{
            display: block;
            text-align: right;
        }
        .mint-cell-head{
            padding: 8px 10px 5px;
            font-size: 15px;
        }
    }
</style>
