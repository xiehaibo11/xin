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
                <mt-navbar v-model="status" class="label-large">
                    <mt-tab-item id="">全部类型</mt-tab-item>
                    <mt-tab-item id="1">已中奖</mt-tab-item>
                    <mt-tab-item id="2">未中奖</mt-tab-item>
                    <mt-tab-item id="0">等待开奖</mt-tab-item>
                </mt-navbar>
            </div>
            <div class="contentH" :style="{height:contentH + 'px'}" style="margin-top: 3px">
                <page-item :url="url">
                <template slot-scope="props">
                    <!--@click="toOrders(item.lottery_id,item.id,item.ischase,item.is_join)"-->
                    <a class="mint-cell" v-for="(item,index) in props.data" :key="index">
                        <div class="mint-cell-head"><em class="f-sm c-3">会员：</em>{{item.user_name}}</div>
                        <div class="mint-cell-wrapper">
                            <div class="mint-cell-title">
                                <span class="mint-cell-text">{{item.ext_txt}}</span>
                                <span class="mint-cell-label">{{item.create_time}}</span>
                            </div>
                            <div class="mint-cell-value">
                                <span v-if="item.BuyInfo.statusCode == 0" class="c-1">
                                        <template v-if="item.BuyInfo.is_join">
                                             <em v-if="!item.BuyInfo.finsh">合买中</em>
                                             <em v-else>未出票</em>
                                        </template>
                                        <template v-else>
                                            未出票
                                        </template>
                                    </span>
                                <span v-if="item.BuyInfo.statusCode == 1" class="suc">等待开奖</span>
                                <span v-if="item.BuyInfo.statusCode == 2 && item.bonus > 0" class="red">已中奖 ({{item.bonus}}{{lotteryUnit}})</span>
                                <span v-if="item.BuyInfo.statusCode == 2 && item.bonus == 0" class="c-3">未中奖</span>
                                <span v-if="item.BuyInfo.statusCode == 6" class="c-3">流产撤单</span>
                                <span v-if="item.BuyInfo.statusCode == 7" class="c-3">系统撤单</span>
                                <span v-if="item.BuyInfo.statusCode == 8" class="c-3">用户撤单</span>
                                <span class="mint-cell-label">投注<em class="org">{{item.money}}</em>{{lotteryUnit}}</span>
                            </div>
                            <!--<i class="mint-cell-allow-right"></i>-->
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
        name:'agentBetRecord',
        components: {
            pageItem,
            InputSearch,
            DateActions
        },
        data() {
            return {
                status:'',
                time : 1 , //时间查询值
                value : '', //搜索值
                name:''
            };
        },
        computed:{
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            userid(){
                return this.$route.query.id ? this.$route.query.id : this.$store.state.userinfo.id
            },
            //内容高度
            contentH(){
                return this.$store.state.clientHeight - 148
            },
            //数据地址
            url(){
                let name =  this.name.length ? '&name='+ this.name : ''
                let status = this.status.length ? '&bounsStatus='+ this.status : ''
                return '/web/details/games?userid=' + this.userid + status + name + '&time=' + this.time
            },
            title(){
                let name = this.$route.query.name ? ' ('+this.$route.query.name+')' :  ' (我的会员)'
                return this.$route.meta.title + name
            },
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
