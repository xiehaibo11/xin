<!--文章详情页-->
<template>
    <div>
        <div class="article-detail">
            <div class="loading" v-if="loading">
                <mt-spinner :type="3"></mt-spinner>
            </div>
            <div v-else>
                <h1 class="mf-sm">{{articleInfo.title}}</h1>
                <div class="info c-3 f-mini mf">发布时间：{{articleInfo.create_time}}</div>
                <div class="d-content" v-html="articleInfo.content"></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                loading:true,
                articleInfo:{}
            }
        },
        methods:{
        },
        created(){
            this.articleInfo = {}
            this.loading = true
            this.$axios('/index/news/getView/id/'+this.$route.query.id + '/navid/'+this.$route.query.navid).then(({data})=>{
                this.$set(this,'articleInfo',data)
                this.loading = false
            })
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .loading{
        width: 28px;
        height: 28px;
        margin: 30px auto;
    }
    .article-detail{
        padding: 20px 10px;
        .d-content{
            line-height: 1.8;
        }
    }
</style>
