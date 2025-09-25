<template>
    <div>
        <header class="mint-header">
            <div class="mint-header-button is-left"></div>
            <h1 class="mint-header-title">
                <ul class="top-tab cf">
                    <li :class="{'active' : selected == 1}" @click="selected=1">足球</li>
                    <li :class="{'active' : selected == 2}" @click="selected=2">篮球</li>
                </ul>
            </h1>
            <div class="mint-header-button is-right">
                <!--<span class="flex-box search-ls">-->
                <!--<i class="mintui mintui-search"></i>-->
                <!--<em class="f-sm">联赛</em>-->
                <!--</span>-->
            </div>
        </header>
        <header class="mint-header tab-header">
            <div class="mint-header-button is-left tab-header-left">
                <span @click="choseTab('/jc/gd')">跟单</span>
                <span class="active" @click="choseTab('/jc/join')">合买</span>
            </div>
            <div class="mint-header-button is-right">
                <!--<router-link to="/jc/search?type=join"><i class="iconfont icon-sousuo"></i></router-link>-->
                <!--<i style="padding-left: 10px" class="iconfont icon-jingshi"></i>-->
            </div>
        </header>
        <div class="content" :style="{height: contentH + 'px'}" ref="wrapper" @scroll.native="scrollEvent">
            <mt-loadmore :top-method="loadTop" :bottom-method="loadBottom" :bottom-all-loaded="allLoaded" :max-distance="100"
                         @top-status-change="handleTopChange" ref="loadmore" :auto-fill="false" :bottom-distance="10">

                <div slot="top" class="mint-loadmore-top">
                    <span v-show="topStatus === 'pull'" :class="{ 'rotate': topStatus === 'drop' }">↓</span>
                    <span v-show="topStatus === 'loading'"><mt-spinner type="triple-bounce" color="#e50e03"></mt-spinner></span>
                    <span v-show="topStatus === 'drop'">↑</span>
                </div>

                <div class="scroll-wrapper">
                    <div class="card">
                        <div v-for="(item,index) in pageData" :key="index" class="join-list border-bottom-1px">
                            <div class="mint-cell-allow-right join-link"  @click="toOrders(item.lottery_id,item.id,index)">
                                <div class="title flex-box" :class="{'c-3': readArr.indexOf(item.id) > -1}">
                                <span>
                                     {{item.title}}
                                    <em class="f-mini c-3">第{{item.issue}}期</em>
                                </span>
                                    <span class="flex tr">
                                    <em class="f-mini c-4">状态: </em>
                                    <template v-if="item.ure_finsh == 0">
                                        <em  class="num org">已满员</em>
                                    </template>
                                    <template v-else>
                                        <em class="f-sm c-3" v-if="item.status == 0">等待开奖</em>
                                        <em class="f-sm suc" v-if="item.status == 1">已中奖</em>
                                        <em class="f-sm c-3" v-if="item.status == 2">未中奖</em>
                                        <em class="f-sm c-3" v-if="item.status == 6">流产撤单</em>
                                        <em class="f-sm c-3" v-if="item.status == 7">系统撤单</em>
                                    </template>
                                </span>
                                </div>
                                <div class="flex-box flex-start">
                                    <div class="progress"><progress-circle :progress="item.buyprecent" :bd-progress="item.bdprecent"></progress-circle></div>
                                    <div class="flex info">
                                        <div class="nickname"> <em class="f-mini c-4">发起人: </em>{{item.nickname}}
                                            <span class="join-level">
                                            <span v-if="item.queen > 0">
                                                <i class="hg"><sub :class="'lv'+ item.queen"></sub></i>
                                            </span>
                                            <span v-if="item.sunNum > 0">
                                                <i class="ty"><sub :class="'lv'+ item.sunNum"></sub></i>
                                            </span>
                                            <span v-if="item.MoonNum > 0">
                                                <i class="yl"><sub :class="'lv'+ item.MoonNum"></sub></i>
                                            </span>
                                            <span v-if="item.starNum > 0">
                                                <i class="xx"><sub :class="'lv'+ item.starNum"></sub></i>
                                            </span>
                                        </span>
                                        </div>
                                        <div class="flex-box det">
                                            <div class="flex tl">
                                                <p class="num org">{{item.total_money}}<em class="f-mini c-3">{{unit}}</em></p>
                                                <p class="f-mini c-3">方案金额</p>
                                            </div>
                                            <div class="flex tl">
                                                <p  class="num org">{{item.ure}}<em class="f-mini c-3">{{unit}}</em></p>
                                                <p class="f-mini c-3">剩余金额</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="height: 8px" v-if="item.ure_finsh < 0"></div>
                            </div>
                            <template v-if="item.ure_finsh > 0">
                                <div class="join-buy-box">
                                    <join-buy :max="item.ure" :order-info="item" @update-join-list='updateJoinList(index+1)'></join-buy><!--购买组件-->
                                </div>
                            </template>
                        </div>
                    </div>
                    <div v-if="textShow" class="tc flex-box f-sm loading c-3">{{loadText}}</div>
                </div>
            </mt-loadmore>
        </div>
    </div>
</template>

<script>
    import BackLayout from 'components/layouts/BackLayout.vue'
    import progressCircle from 'components/common/progressCircle.vue'
    import joinBuy from 'components/orders/JoinBuy.vue'
    let isFromC;
    export default {
        name : 'join',
        data() {
            return {
                selected:'2',
                readArr:[], //已读数组

                cur :0 ,//默认选中第一项
                show:false,
                buy:1,//购买金额

                perPage:14 , //每页数量
                currentPage: 1, //当前页
                lastPage: '', //最后一页
                loadText:'上拉加载更多',
                textShow:true,
                contentH:0,
                allLoaded: false,
                topStatus: '',
            };
        },
        components: {
            progressCircle,
            joinBuy,
            BackLayout
        },
        watch:{
            url(){
                this.loadTop();
            }
        },
        computed:{
            symbol(){
                return this.url.indexOf("?") > -1 ? "&" : '?'
            },
            loadState(){
                return this.$store.state.page.loadState
            },
            unit(){
                return this.$store.state.setting.lottery_unit
            },
            url(){
                return '/index/lottery/game';
            },
            //彩种导航
            options(){
                return this.$store.state.lotteryNav
            },
            pageData(){
                return this.$store.state.joinList
            }
        },
        methods:{
            //顶部tab切换
            choseTab(n){
                this.$router.push({
                    path:n
                })
            },
            toOrders(lottery_id,id,index){ //进入订单详情页
                if(this.readArr.indexOf(id) == -1){
                    this.readArr.push(id)
                }
                this.$router.push({
                    path:'/jc/join/detail',
                    query:{
                        lottery_id:lottery_id,
                        id:id,
                        i:index
                    }
                })
            },
            //购买成功后更新数据
            updateJoinList(index){
                let page =index == 0 ? 1 : Math.ceil(index/this.perPage)
                this.$axios(this.url + this.symbol + 'page='+ page).then(({data})=>{
                    this.$store.commit('updataJoinList',data)
                })
            },
            //获取首页数据
            getData(page){
                this.$axios(this.url + this.symbol + 'page='+ page).then(({data})=>{
                    this.$store.commit('setJoinList',data.data)
                    this.$set(this,'lastPage',data.last_page)
                    this.$set(this,'perPage',data.per_page)
                    this.textShow = true;
                    if(!data.data.length){
                        this.loadText = '暂无合买方案'
                    }else {
                        if(this.currentPage == this.lastPage){
                            this.allLoaded = true
                            this.loadText = '没有更多了'
                        }else{
                            this.allLoaded = false
                            this.loadText = '上拉加载更多'
                        }
                    }
                })
            },
            loadTop() {  // 刷新数据的操作
                this.currentPage =1
                this.getData(1);
                setTimeout(()=> {
                    this.$refs.loadmore.onTopLoaded();
                }, 1000);
            },
            loadBottom () { // 加载更多数据的操作
                this.textShow = false
                this.currentPage++;
                console.log(this.currentPage)
                this.$axios(this.url + this.symbol + 'page='+ this.currentPage).then(({data})=>{
                    this.$store.commit('loadMoreJoinList',data.data)
                    this.textShow = true
                    this.$set(this,'lastPage',data.last_page)
                    this.$set(this,'currentPage',data.current_page)
                    this.$refs.loadmore.onBottomLoaded();
                    if(this.lastPage == this.currentPage){
                        this.allLoaded = true
                        this.loadText = '没有更多了'
                    }else {
                        this.allLoaded = false
                        this.loadText = '上拉加载更多'
                    }
                })

            },
            handleTopChange(status) {
                this.topStatus = status;
            }
        },
        mounted() {
            // mint-ui loadmore组件需要包裹，且内容高度要高去包裹才可加载更多，所以给它一个 指定的高度
            this.contentH = document.documentElement.clientHeight - 135;
        },
        created(){

        },
        beforeRouteEnter(to,from,next){
            if(from.path == '/jc/join/detail' || from.path == '/jc/search'){
                isFromC = true;
            }else {
                isFromC = false
            }
            next();
        },
        beforeRouteLeave(to, from, next){
            if(to.path == '/jc/join/detail' || to.path == '/jc/userRecord' ||  to.path == '/jc/search'){
                this.$store.commit('setPageYOffset', this.$refs.wrapper.scrollTop)
                this.$store.commit('setKeepAlivePage','jcGendan')
            }else {
                this.$store.commit('delKeepAlivePage','jcGendan')
            }
            next();
        },
        activated(){
            if(isFromC){
                this.$refs.wrapper.scrollTop = this.$store.state.pageYOffset;
            }else {
                this.currentPage =1
                this.getData(1);
            }
            if(this.$store.state.updateIndex > -1){
                let page =this.$store.state.updateIndex == 0 ? 1 : Math.ceil(this.$store.state.updateIndex/this.perPage)
                this.updateJoinList(page);
            }
        }
    };
</script>

<style lang="scss" scoped type="text/scss">
    .tab-header{
        .tab-header-left{
            span{
                display: inline-block;
                height: 38px;
                line-height: 38px;
                padding: 0 10px;
                &.active{
                    border-bottom:2px solid #ffffff;
                    color: #fdd9d9;
                }
            }
        }
    }
    .loading{
        justify-content: center;
        height:40px;
        span{
            padding-right: 8px;
        }
    }
    .join-list{
        position: relative;
        padding: 15px 0 0 0;
        // &:active{
        //     background-color: $color-bg;
        //  }
        .join-link{
            padding: 0px 30px 0px 10px;
        }
        .title{
            padding-bottom: 10px;
        }
        .progress{
            padding-right: 8px;
        }
        .info{
            .nickname{
                padding-bottom: 10px;
            }
            .det{
                line-height: 2;
            }
        }
        .join-buy-box{
            padding: 5px 10px;
            justify-content: flex-end;
            background-color: $color-bg;
        }
    }
    .select{
        background-color: $bColor;
        width: 100%;
        height: 40px;
        line-height: 40px;
        justify-content: center;
        color: #ffffff;
        overflow: hidden;
        z-index: 101;
        .label{
            display: inline-block;
            width: 12px;
            line-height: 1.2;
            margin-right: 5px;
        }
        span.chose{
            padding: 2px 8px;
            border:.5px solid#ffffff;
            border-radius: 3px;
            height: 20px;
            display: inline-block;
            line-height: 20px;
            i{
                -webkit-transition: all 0.25s ease-in-out;
                -moz-transition: all 0.25s ease-in-out;
                -o-transition: all 0.25s ease-in-out;
                transition: all 0.25s ease-in-out;
                display: inline-block;
                &.is-active{
                    -webkit-transform: rotate(180deg);
                    -moz-transform: rotate(180deg);
                    -ms-transform:rotate(180deg);
                    -o-transform:rotate(180deg);
                    transform: rotate(180deg);
                }
                &.no-active{
                    transform: rotate(0deg);
                }
            }
        }
    }
    .layout{
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 50;
        top: 40px;
        left: 0;
    }
    .select-grounp{
        padding: 10px;
        background-color: $color-bg;
        border-bottom: 2px solid $bColor;
        li{
            width: 33.3%;
            float: left;
            a{
                display: block;
                width: 80%;
                background-color: #ffffff;
                margin: 5px auto;
                text-align: center;
                height: 28px;
                line-height: 28px;
                border:0.5px solid $color-border-two;
                color: $color-font-primary;
                font-size: $font-mini + 1;
                border-radius: 3px;
                &.active{
                    border:0.5px solid $bColor;
                    color: $bColor;
                    position: relative;
                    .gou{
                        height: 23px;
                        position: absolute;
                        right: 0;bottom: 0;
                    }
                }
            }
        }
    }
    .content {
        overflow-y: scroll;
        -webkit-overflow-scrolling: touch;
    }
</style>
