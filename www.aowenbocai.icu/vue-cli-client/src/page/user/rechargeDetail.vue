<!--充值明细-->
<template>
    <div class="detail">
        <date-query @do-search="doSearch">
            <div slot="info" class="c-3">
            </div>
        </date-query>
        <div class="contentH" :style="{height: contentH + 'px'}">
        <page-item :url="url">
            <template slot-scope="props">
                <a class="mint-cell" v-for="(item,index) in props.data" :key="index">
                    <div class="mint-cell-left"></div>
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
                    <div class="mint-cell-right"></div>
                </a>
            </template>
        </page-item>
        </div>
    </div>
</template>
<script>
    import pageItem from 'components/common/PageItem.vue'
    import DateQuery from 'components/user/DateQuery.vue'
    export default{
        components: {
            pageItem,
            DateQuery
        },
        data() {
            return {
                url:'/web/details/rechargelist',
                initUrl:'/web/details/rechargelist'
            };
        },
        computed:{
            //列表高度
            contentH(){
                return this.$store.state.clientHeight - 86
            },
            symbol(){
                return this.url.indexOf("?") > -1 ? "&" : '?'
            }
        },
        methods:{
            //搜索
            doSearch(emitVal){
                this.url = this.initUrl + this.symbol + emitVal
            }
        }
    }
</script>

<style scoped type="text/scss" lang="scss">

</style>
