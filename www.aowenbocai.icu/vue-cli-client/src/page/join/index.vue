<template>
    <div>
        <div class="select flex-box mf">
            <span class="f-mini label">彩种</span>
            <span class="chose" @click="show = !show">{{selected}} <i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
        </div>
        <div class="layout contentH" @click.self="show = false" v-show="show" :style="{height: pullHeight + 'px'}">
            <ul class="select-grounp clearfloat"  v-show="show">
                <li v-for="(item,index) in options">
                     <a @click="toLottery('/join',item.id,item.label,index)" :class="{'active':cur == item.id}">
                    <i v-if="cur == item.id" class="iconfont icon-anonymous-iconfont gou"></i>{{item.label}}</a>
                </li>
            </ul>
        </div>
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
                                <span class="flex tr" v-if="item.isFish || item.ure_finsh == 0">
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
                                            <p  class="num org">{{item.ure}}<em class="f-mini c-3">份</em></p>
                                            <p class="f-mini c-3">剩余份数</p>
                                        </div>
                                        <div class="flex tl">
                                            <p class="num org">{{getPerMoney(item.total_money,item.total_share)}}<em class="f-mini c-3">{{unit}}</em></p>
                                            <p class="f-mini c-3">每份金额</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="height: 8px" v-if="item.ure_finsh < 0"></div>
                        </div>
                        <template v-if="item.ure_finsh > 0">
                            <div class="join-buy-box">
                                <join-buy-simple :max="item.ure" :order-info="item" @do-buy='toOrders2' :index="index"></join-buy-simple><!--购买组件-->
                            </div>
                        </template>
                    </div>
                </div>
                <div v-if="textShow" class="tc flex-box f-sm loading c-3">{{loadText}}</div>
            </div>
        </mt-loadmore>
        </div>
        <!--<div class="b-height"></div>-->
    </div>
</template>

<script>
    import progressCircle from 'components/common/progressCircle.vue'
    import joinBuySimple from 'components/orders/JoinBuySimple.vue'
    let isFromC;
    export default {
        name : 'join',
        data() {
            return {
                readArr:[], //已读数组

                gameId:this.$route.query.gameid || '',//彩种id
                selected:'所有彩种',
                cur :0 ,//默认选中第一项
                show:false,
                buy:1,//购买金额

                perPage:14 , //每页数量
                currentPage: 1, //当前页
                lastPage: '', //最后一页
                loadText:'上拉加载更多',
                textShow:true,
                contentH:0,
                allLoaded: true,
                topStatus: '',
            };
        },
        components: {
            progressCircle,
            joinBuySimple
        },
        watch:{
            url(){
                this.loadTop();
            }
        },
        computed:{
            pullHeight(){
                return this.$store.state.clientHeight - 100
            },
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
                let id = this.gameId ? '/gameid/'+ this.gameId : '';
                return '/index/lottery/game' + id;
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
            //计算每份金额
            getPerMoney(totalMoney,totalShare){
                let perMoney = this.$bet.accDiv(Number(totalMoney),Number(totalShare),3)
                return perMoney ? perMoney : 1
            },
            //计算购买金额
            buyMoney(totalMoney,totalShare,buy){
                let perMoney = this.$bet.accDiv(Number(totalMoney),Number(totalShare),3)
                let res = perMoney ? perMoney : 1
                return this.$bet.accMul(res,Number(buy),3)
            },
            //具体彩种
            toLottery(path,gameid,select){
                this.gameId = gameid
                this.selected = select
                this.cur = gameid
                this.$router.push({
                    path: path,
                    query:{
                        gameid:gameid,
                    }
                });
                setTimeout(()=>{
                    this.show = false
                },200)
            },
            toOrders(lottery_id,id,index){ //进入订单详情页
                this.$store.commit('setPageYOffset', this.$refs.wrapper.scrollTop)
                if(this.readArr.indexOf(id) == -1){
                    this.readArr.push(id)
                }
                this.$router.push({
                    path:'/joinDetail',
                    query:{
                        lottery_id:lottery_id,
                        id:id,
                        i:index
                    }
                })
            },
            toOrders2(emitVal){ //进入订单详情页
                this.$store.commit('setPageYOffset', this.$refs.wrapper.scrollTop)
                if(this.readArr.indexOf(emitVal.orderInfo.id) == -1){
                    this.readArr.push(emitVal.orderInfo.id)
                }
                this.$router.push({
                    path:'/joinDetail',
                    query:{
                        lottery_id:emitVal.orderInfo.lottery_id,
                        id:emitVal.orderInfo.id,
                        i:emitVal.index,
                        buy:emitVal.buy
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
            this.contentH = document.documentElement.clientHeight - this.$refs.wrapper.getBoundingClientRect().top - this.$refs.wrapper.getBoundingClientRect().bottom;
        },
        created(){
            //匹配所选彩种
            for(let p in this.options){
                if(this.options[p].id == this.gameId){
                    this.cur = this.options[p].id
                    this.selected = this.options[p].label
                }
            }
        },
        beforeRouteEnter(to,from,next){
            if(from.path == '/joinDetail'){
                isFromC = true;
            }else {
                isFromC = false
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
