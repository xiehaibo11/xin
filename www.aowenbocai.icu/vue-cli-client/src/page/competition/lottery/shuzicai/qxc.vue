<template>
    <div>
        <div class="loading" v-if="loading">
            <mt-spinner :type="3"></mt-spinner>
        </div>
        <div class="dlt-wrapper">
            <!--顶部导航-->
            <div class="head">
                <!--<mt-header title="标准选号">-->
                    <!--<mt-button icon="back" slot="left" @click.native="goHome"></mt-button>-->
                    <!--<mt-button icon="more" slot="right" @click.native = "showMore = !showMore;show = false"></mt-button>-->
                <!--</mt-header>-->
                <header class="mint-header">
                    <div class="mint-header-button is-left">
                        <button class="mint-button mint-button--default mint-button--normal" @click="goHome">
                            <span class="mint-button-icon"><i class="mintui mintui-back"></i></span>
                        </button>
                    </div>
                    <h1 class="mint-header-title">
                        <ul class="top-tab cf">
                            <li :class="{'active' : type == 1}" @click="type=1">标准选号</li>
                            <li :class="{'active' : type == 2}" @click="type=2">单式投注</li>
                        </ul>
                    </h1>
                    <div class="mint-header-button is-right">
                        <button class="mint-button mint-button--default mint-button--normal" @click = "showMore = !showMore;show = false">
                            <span class="mint-button-icon"><i class="mintui mintui-more"></i></span>
                        </button>
                    </div>
                </header>
                <transition name="fade">
                    <div class="layout z-3" @click.self="showMore = false" v-show="showMore">
                        <div class="head-list border-1px">
                            <ul >
                                <li class="border-bottom-1px" @click="toPlayNews">玩法介绍</li>
                                <li class="border-bottom-1px" @click="toHistoryCode">历史开奖</li>
                                <li class="border-bottom-1px" @click="toTrade">走势图</li>
                            </ul>
                        </div>
                    </div>
                </transition>
                <!--近期开奖-->
                <div class="layout z-2 contentH" v-show="recentOpen" @click="recentOpen = !recentOpen;" style="padding-top: 0px;top: 70px;" :style="{height:recentHeight + 'px'}">
                    <div class="recent-open-box">
                        <table cellpadding="0" cellspacing="0" class="table-list">
                            <tr>
                                <th width="25%">期号</th>
                                <th width="35%">开奖号码</th>
                                <th width="20%">和值</th>
                                <th width="20%">形态</th>
                            </tr>
                            <tr v-for="(item,index) in recentOpenCodeArr" :key="index" v-if="index < 10">
                                <td>{{item.expect}}期</td>
                                <td>
                                    <span v-for="num in item.code" class="ball-sm">{{num}}</span>
                                </td>
                                <td>{{item.he}}</td>
                                <td>{{item.xt}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!--近期开奖end-->
            </div>
            <!--顶部导航 end-->
            <!--滚动期号信息-->
            <div class="flex-box card border-bottom-1px" @click="recentOpen = !recentOpen" >
                <div style="width: 70px;padding-top: 4px;"><i class="iconfont icon-jinrikaijiang org" style="font-size: 28px"></i></div>
                <div class="marquee_box">
                    <ul class="marquee_list" :style="{ top: -num + 'px'}" :class="{marquee_top:num}">
                        <!-- 当显示最后一条的时候（num=0转换布尔类型为false）去掉过渡效果-->
                        <li>距{{initData.expect}}期投注截止还有 <b class="red">{{timer}}</b></li>
                        <!--<li>{{initData.expect}}期 开奖时间: <em>{{initData.kjDate}}</em> <em>{{initData.kjWeek}}</em> <em class="red">{{initData.kjTime}}</em></li>-->
                        <li>开奖时间: 每天<em class="red">20:30</em>开奖</li>
                    </ul>
                </div>
                <div style="width: 50px;text-align: center">
                    <i class="iconfont icon-xialajiantou rotateIcon" :class="recentOpen ? 'is-active' : 'no-active'"></i>
                </div>
            </div>
            <!--滚动期号信息 end-->

            <!--标准选号-->
            <qxc-zhix-fs v-if="type==1" text="标准选号" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time"></qxc-zhix-fs>
            <!--单式投注-->
            <qxc-copy-bet v-if="type==2" :type="type" text="单式投注" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time" :n="7"></qxc-copy-bet>
        </div>
    </div>
</template>

<script>
    import QxcZhixFs from 'components/competition/shuzicai/PlZhixFs.vue'
    import QxcCopyBet from 'components/competition/shuzicai/pls/PlsCopyBet.vue' //排列3-单式
    export default {
        name: 'qxc',
        components:{
            QxcZhixFs,
            QxcCopyBet
        },
        data () {
            return {
                num:0,//滚动距离
                intervalTime:null, //倒计时定时器

                showMore:false, //右侧列表
                gain:'500万',
                type:1,

                loading : false,
                name:'qxc',
                initData:{}, //初始数据
                miss:{},//遗漏

                recentOpen: false, //近期开奖
                recentOpenCodeArr:[
                    {expect:'19022',code:['1','2','3'],he:6,xt:'组六'},
                    {expect:'19023',code:['2','3','5'],he:6,xt:'组六'},
                    {expect:'19023',code:['1','1','3'],he:6,xt:'组三'},
                    {expect:'19023',code:['4','6','9'],he:6,xt:'组六'},
                    {expect:'19023',code:['1','1','1'],he:6,xt:'豹子'},
                    {expect:'19023',code:['4','4','9'],he:6,xt:'组三'},
                    {expect:'19023',code:['4','6','8'],he:6,xt:'组六'},
                ]
            }
        },
        computed:{
            title(){
                return this.$route.meta.title
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //选号区高度
            betHeight(){
                return this.$store.state.clientHeight - 152
            },
            recentHeight(){
                return this.$store.state.clientHeight - 70
            },
            //玩法下拉高度
            selctHeight(){
                return this.$store.state.clientHeight - 40
            },
            //倒计时格式化
            timer(){
                return this.$bet.formatDownTime(this.initData.down_time)
            },
            //选号篮数量
            badge(){
                return this.$store.state.shuzicai.plan.length
            }
        },
        methods:{
            //返回大厅
            goHome(){
                if(this.badge > 0){
                    this.$messagebox.confirm(
                        '退出投注页面，您的投注号码将不会保存'
                    ).then(()=>{
                        this.$router.isBack = true
                        this.$router.push({
                            path:'/jc'
                        })
                    }).catch((err)=>{

                    });
                }else {
                    this.$router.isBack = true
                    this.$router.push({
                        path:'/jc'
                    })
                }
            },
            //倒计时
            timeInterval(){
                this.intervalTime = setInterval(()=>{
                    this.initData.down_time--;
                },1000)
            },
            //顶部期号信息滚动显示
            showMarquee(num) {
                let marqueetimer =  setInterval(()=>{
                    num++;
                    if(num>=2 ){
                        num=0;
                    }
                    this.num=num*30;
                }, 5000);
            },
            //玩法介绍
            toPlayNews(){
                this.$store.commit('setLoadStatus',true)
                this.$axios.get('/index/news/getAppBetNew',{
                    params:{
                        name:this.name
                    }
                }).then(({data})=>{
                    this.$store.commit('setLoadStatus',false)
                    this.showMore = false
                    if(!data.err){
                        this.$router.push({
                            path:'/news/detail',
                            query:{
                                cz:this.cz,
                                id:data.data.id,
                                navid:data.data.nav_id
                            }
                        })
                    }else {
                        this.$toast('暂无相关玩法介绍!')
                    }
                }).catch(({error})=>{
                    console.log(error)
                })
            },
            //查看历史详情
            toHistoryCode(){
                this.showMore = false
                this.$router.push({
                    path:'/historyCode',
                    query:{
                        name:this.name,
                        cz:this.cz,
                        title:this.title,
                        expectType:this.expectType
                    }
                })
            },
            //进入走势图
            toTrade(){
                this.showMore = false
                this.$router.push({
                    path:'/lottery/trade',
                    query:{
                        name : this.name,
                        title : this.title,
                        cz:this.cz,
                        expectType:this.expectType
                    }
                })
            }
        },
        created(){
            let data = {
                info :{
                    "expect" : "19022",
                    "allowbuy" : 1,
                    "kjDate" : "02-27",
                    "kjWeek" : "周三",
                    "kjTime" : "20:30",
                    'down_time': 600,
                    'end_time':"2019-03-04 20:00:00"
                },
                miss:0
            }
            this.$set(this,'initData',data.info)
            this.$set(this,'miss',data.miss)
            this.showMarquee(this.num)
            this.timeInterval();
            this.$store.commit('setKeepAlivePage','qxc')
        },
        //离开当前投注页面
        beforeRouteLeave(to, from, next){
            if(to.path =='/shuzicai/bet'){
                //缓存页面
                this.$store.commit('setKeepAlivePage','qxc')
            }else {
                this.$store.commit('delKeepAlivePage','qxc') //清除投注页面缓存
                this.$store.commit('delKeepAlivePage','shuzicaiBet') //清除订单页面缓存
                this.$store.commit('clearSzcBetNum') //清空数字彩投注数据
            }
            next();
        }
    }
</script>
<style scoped type="text/scss" lang="scss">
    .head .select-grounp .bet-fir li{
        width: 28%;
        height: 35px;
        line-height: 35px;
        margin: 4px 2.5%;
    }
</style>
