<!--兑换记录-->
<template>
    <div class="detail">
        <date-query @do-search="doSearch">
            <div slot="info" class="c-3">

            </div>
        </date-query>
        <div class="contentH" :style="{height: contentH + 'px'}">
        <page-item :url="url" v-if="isrefresh">
            <template slot-scope="props">
                <a class="mint-cell" v-for="(item,index) in props.data" :key="index">
                    <div class="mint-cell-left"></div>
                    <div class="mint-cell-wrapper">
                        <div class="mint-cell-title">
                            <span class="mint-cell-text">{{item.name}}</span>
                            <span class="mint-cell-label">{{item.create_time}}</span>
                        </div>
                        <div class="mint-cell-value">
                            <span v-html="item.status"></span>
                            <mt-button v-if="!item.status_code" type="danger" size="small" @click.native.self="cancelOrder(item.id)">撤单</mt-button>
                            <span class="mint-cell-label">兑换数量:<em class="org">{{item.num}}</em> 消耗{{lotteryUnit}}:<em class="org">{{item.money}}</em></span>
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
                url:'/web/details/exchangelist',
                initUrl:'/web/details/exchangelist',
                isrefresh: true
            };
        },
        computed:{
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
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
            },
            //撤单
            cancelOrder(id){
                this.$messagebox.confirm(
                    '确定要撤销该订单吗?'
                ).then(()=>{
                    this.$axios.get('/web/user/returnChange',{
                        params:{
                            id : id
                        }
                    }).then(({data})=>{
                        if(!data.err){
                            this.$messagebox.alert(data.msg).then(action => {
                                this.isrefresh = false
                                this.$nextTick(()=>{
                                    this.isrefresh = true
                                })
                            });
                        }else {
                            this.$messagebox('提示',data.msg)
                        }
                    })
                }).catch((err)=>{
                });
                return
            }
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .detail{
        .mint-cell{
            .mint-cell-value{
                display: block;
                text-align: right;
            }
        }
    }
    .mint-button--small {
        display: inline-block;
        padding: 0 5px;
        height: 20px;
        font-size: 12px;
    }
</style>
