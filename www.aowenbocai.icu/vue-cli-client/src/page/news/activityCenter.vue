<!--优惠活动-->
<template>
    <div>
        <mt-header title="优惠活动" v-if="this.$route.path == '/activity'"></mt-header>
        <div class="activity-list contentH" ref="wrapper" :style="{height: contentH + 'px'}">
            <page-item :url="url">
                <template slot-scope="props">
                    <template v-for="(item,index) in props.data">
                        <a class="mint-cell" @click="toDetail(item.id,item.nav_id,index)" :class="{'readed': readArr.indexOf(index) > -1}">
                            <div class="mint-cell-wrapper">
                                <div class="article-img" v-if="item.pic" :style="{backgroundImage:'url(' + item.pic + ')'}"></div>
                                <div class="mint-cell-title">
                                    <span class="mint-cell-text">{{item.title}}</span>
                                    <span class="mint-cell-label">{{item.content_index | slice}}</span>
                                    <!--<span class="mint-cell-label f-mini c-3">-->
                                    <!--<i class="iconfont icon-dengdai f-mini"></i> {{item.create_time}}-->
                                    <!--</span>-->
                                </div>
                                <i class="mint-cell-allow-right"></i>
                            </div>
                        </a>
                    </template>
                </template>
            </page-item>
        </div>
    </div>
</template>

<script>
    import pageItem from 'components/common/PageItem.vue'
    export default {
        name: 'news',
        components: {
            pageItem,
        },
        data () {
            return {
                readArr:[] //已读数组
            }
        },
        computed:{
            url(){
                return "/index/news/getGameNews/navid/" + this.$route.query.navid
            },
            //屏幕高度
            contentH(){
                let h = this.$route.path == '/activity' ? 95 : 40
                return this.$store.state.clientHeight - h
            }
        },
        filters: {
            slice (value) {
                if(value && value.length > 30) {
                    value= value.substring(0,30)+ '...';
                }
                return value;
            }
        },
        methods:{
            back(){
                this.$router.goBack(-1);//返回上一层
            },
            toDetail(id,navid,i){ //进入文章详情
                this.$store.commit('setPageYOffset', this.$refs.wrapper.scrollTop)
                if(this.readArr.indexOf(i) == -1){
                    this.readArr.push(i)
                }
                let path = this.$route.path == '/activity' ? '/activity/detail' : '/news/detail'
                this.$router.push({
                    path: path,
                    query:{
                        id:id,
                        navid:navid,
                    }
                })
            }
        },
        created(){
            this.$store.commit('setKeepAlivePage','news')
        },
        //进入详情页后禁止滚动加载事件
        beforeRouteLeave(to, from, next){
            if(to.path =='/news/detail'||to.path =='/activity/detail'){
                this.$store.commit('setKeepAlivePage','news')
            }else {
                this.$store.commit('delKeepAlivePage','news')
            }
            next();
        },
        activated(){
            this.$refs.wrapper.scrollTop = this.$store.state.pageYOffset;
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .activity-list{
        .article-img{
            width: 180px;
            height: 65px;
            margin-right: 5px;
            background-size: cover;
            background-repeat: no-repeat;
        }
        .mint-cell{
            min-height:90px;
            &.readed{
                color: #999999;
            }
        }
        .mint-cell-wrapper{
            padding-right: 25px;
        }
        .mint-cell-text{
            font-weight: 500;
        }
        .mint-cell-label{
            line-height: 1.3;
        }
    }
</style>
