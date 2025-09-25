<template>
    <div class="detail">
        <date-query @do-search="doSearch">
            <div slot="info" class="org">
                <em class="c-3 f-sm">账户余额：</em>{{this.$store.state.userinfo.money}}<em class="f-sm c-2">{{lotteryUnit}}</em>
            </div>
        </date-query>
        <div class="contentH" :style="{height: contentH + 'px'}">
        <page-item :url="url">
            <template slot-scope="props">
                <mt-cell :title="item.create_time" :label="item.remark" v-for="(item,index) in props.data" :key="index">
                    <span :class="item.money>0 ? 'suc' : 'red'" v-if="item.money>0">+{{item.money}}</span>
                    <span :class="item.money>0 ? 'suc' : 'red'" v-else>{{item.money}}</span>
                    <em class="f-sm">{{lotteryUnit}}</em>
                </mt-cell>
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
                url:'/web/details/list',
                initUrl:'/web/details/list',
            }
        },
        computed:{
            //列表高度
            contentH(){
                return this.$store.state.clientHeight - 86
            },
            symbol(){
                return this.url.indexOf("?") > -1 ? "&" : '?'
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
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
