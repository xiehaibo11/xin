<template>
    <div
        v-infinite-scroll="loadMore"
        infinite-scroll-disabled="loadState"
        infinite-scroll-distance="60">
        <div><slot :data="pageData"></slot></div>
        <div class="tc f-sm flex-box loading c-3">
            <template v-if="loadText !== '暂无相关数据'">
                <mt-spinner type="fading-circle" :size="22" v-if="loading"></mt-spinner>
                <span>{{loadText}}</span>
            </template>
            <template v-else>
                <div class="no-data">
                    <div class="tc mf-sm"><i class="iconfont icon-icon"></i></div>
                    <div>{{loadText}}</div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        name:'page',
        props:['url'],
        data() {
            return {
                pageData:[],
                loading : true,
                loadText: '加载更多',
//                this.$store.state.page.loadState: true , //数据是否加载完成

                currentPage: 1, //当前页
                lastPage: '', //最后一页
                count : 0 //记录是否为第一次加载
            };
        },
        computed:{
            symbol(){
                return this.url.indexOf("?") > -1 ? "&" : '?'
            },
            loadState(){
                return this.$store.state.page.loadState2
            }
        },
        watch:{
            url(){
                this.pageData = []
                this.currentPage = 1
                this.lastPage = ''
                this.getPageList();
            }
        },
        methods:{
            getPageList (flag) {
                this.loading = true
                this.loadText = '加载中..'
                this.$axios(this.url + this.symbol + 'page='+ this.currentPage).then(({data}) => {
                    if(!data.total){
                        this.loadText = '暂无相关数据'
                    }else {
                        if (flag) {
                            for(let p in data.data){
                                this.pageData.push(data.data[p]);
                            }
                        } else {
                            this.$set(this,'pageData',data.data)
                        }
                        if(data.current_page == data.last_page){
                            this.$store.commit('setPageLoad2',true)
                            this.loadText = '没有更多了'
                        }else {
                            this.$store.commit('setPageLoad2',false)
                            this.loadText = '加载更多'
                        }
                        this.currentPage = data.current_page
                        this.lastPage = data.last_page
                    }
                    this.loading = false
                })
            },
            //加载更多
            loadMore() {
                this.$store.commit('setPageLoad2',true)
                this.currentPage++;
                this.getPageList(true);
            }
        },
        activated(){
            var status = this.currentPage == this.lastPage ? true : false
            this.$store.commit('setPageLoad2',status)
        },
        created(){
            this.$store.commit('setPageLoad2',true)
            this.getPageList();
        },
    };
</script>

<style lang="scss" scoped type="text/scss">
    .loading{
        justify-content: center;
        min-height: 40px;
        span{
            padding-right: 8px;
        }
    }
</style>
