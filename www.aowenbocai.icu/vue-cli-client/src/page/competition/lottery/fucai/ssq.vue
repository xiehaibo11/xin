<template>
    <div>
        <div class="loading" v-if="loading">
            <mt-spinner :type="3"></mt-spinner>
        </div>
        <div class="dlt-wrapper">
            <!--顶部导航-->
            <div class="head">
                <mt-header>
                    <mt-button icon="back" slot="left" @click.native="goHome"></mt-button>
                    <mt-button icon="more" slot="right" @click.native = "showMore = !showMore;show = false;recentOpen=false"></mt-button>
                </mt-header>
                <div class="select flex-box">
                    <span class="f-mini label">玩法</span>
                    <span class="chose" @click="show = !show;recentOpen=false">{{selected}}<i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
                </div>
                <transition name="fade">
                    <div class="layout z-3" @click.self="showMore = false" v-show="showMore">
                        <div class="head-list border-1px">
                            <ul >
                                <!--<li class="border-bottom-1px" @click="showPlayInfo">中奖说明</li>-->
                                <li class="border-bottom-1px" @click="toPlayNews">玩法介绍</li>
                                <li class="border-bottom-1px" @click="toHistoryCode">历史开奖</li>
                                <li class="border-bottom-1px" @click="toTrade">走势图</li>
                            </ul>
                        </div>
                    </div>
                </transition>
                <div class="layout z-2 contentH" @click.self="show = false;" v-show="show" :style="{height:selctHeight + 'px'}">
                    <div class="select-grounp">
                        <ul class="clearfloat bet-fir" style="margin-bottom: 0">
                            <template v-for="(item,index) in play">
                                <li :class="{'active':type == item.type}" @click="type=item.type;show=false;selected=item.name">{{item.name}}</li>
                            </template>
                        </ul>
                    </div>
                </div>
                <!--近期开奖-->
                <div class="layout z-2 contentH" v-show="recentOpen" @click="recentOpen = !recentOpen;" style="padding-top: 0px;top: 70px;" :style="{height:recentHeight + 'px'}">
                    <div class="recent-open-box">
                        <table cellpadding="0" cellspacing="0" class="table-list">
                            <tr v-for="(item,index) in recentOpenCodeArr" :key="index" v-if="index < 10">
                                <td width="23%">{{item.expect}}期</td>
                                <td width="10%" v-for="num in item.foreCode">
                                    <em class="red f-sm">{{num}}</em>
                                </td>
                                <td width="11%" v-for="num in item.backCode">
                                    <em class="abled f-sm">{{num}}</em>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!--近期开奖end-->
            </div>
            <!--顶部导航 end-->
            <!--滚动期号信息-->
            <div class="flex-box card border-bottom-1px" @click="recentOpen = !recentOpen" >
                <div style="width: 70px;padding-top: 4px;" v-if="initData.todyIsKj"><i class="iconfont icon-jinrikaijiang org" style="font-size: 28px"></i></div>
                <div class="marquee_box">
                    <ul class="marquee_list" :style="{ top: -num + 'px'}" :class="{marquee_top:num}">
                        <!-- 当显示最后一条的时候（num=0转换布尔类型为false）去掉过渡效果-->
                        <li>距{{initData.expect}}期投注截止还有 <b class="red">{{timer}}</b></li>
                        <li>{{initData.expect}}期 开奖时间: <em>{{initData.kjDate}}</em> <em>{{initData.kjWeek}}</em> <em class="red">{{initData.kjTime}}</em></li>
                        <li>{{initData.expect}}期 奖池滚存 <em class="red">{{initData.prizepool}}</em>元</li>
                    </ul>
                </div>
                <div style="width: 50px;text-align: center">
                    <i class="iconfont icon-xialajiantou rotateIcon" :class="recentOpen ? 'is-active' : 'no-active'"></i>
                </div>
            </div>
            <!--滚动期号信息 end-->
            <!--标准选号-->
            <ssq-base-bet v-show="type==1" :type="type" :text="selected" :miss="miss" :expect="initData.expect" :end-time="initData.end_time" title="双色球" name="ssq"></ssq-base-bet>
            <!--单式投注-->
            <ssq-copy-bet v-show="type==2" :type="type" :text="selected" :expect="initData.expect" :end-time="initData.end_time" title="双色球" name="ssq"></ssq-copy-bet>
            <!--胆拖选号-->
            <ssq-dt-bet v-show="type==3" :type="type" :text="selected" :miss="miss" :expect="initData.expect" :end-time="initData.end_time" title="双色球" name="ssq"></ssq-dt-bet>
            <!--定胆杀号-->
            <ssq-dd-sh-bet v-show="type==4" :type="type" :text="selected" :miss="miss" :expect="initData.expect" :end-time="initData.end_time" title="双色球" name="ssq"></ssq-dd-sh-bet>
        </div>
    </div>
</template>

<script>
    import SsqBaseBet from 'components/competition/shuzicai/ssq/SsqBaseBet.vue' //双色球标准投注
    import SsqCopyBet from 'components/competition/shuzicai/ssq/SsqCopyBet.vue' //双色球单式投注
    import SsqDtBet from 'components/competition/shuzicai/ssq/SsqDtBet.vue' //双色球胆拖投注
    import SsqDdShBet from 'components/competition/shuzicai/ssq/SsqDdShBet.vue' //双色球定胆杀号
    export default {
        name: 'ssq',
        components:{
            SsqBaseBet,
            SsqCopyBet,
            SsqDtBet,
            SsqDdShBet
        },
        data () {
            return {
                num:0,//滚动距离
                intervalTime:null, //倒计时定时器

                show:false, //选择玩法
                showMore:false, //右侧列表
                selected:'标准选号',//选中的玩法名
                type:1, //选中的玩法标识

                loading : false,
                play:[{'type': 1 ,'name' : '标准选号'},
                    {'type': 2 ,'name' : '单式投注'},
                    {'type': 3 ,'name' : '胆拖投注'},
                    {'type': 4 ,'name' : '定胆杀号'}],
                name: 'ssq',
                initData:{}, //初始数据
                miss:{},//遗漏
                recentOpen: false, //近期开奖
                recentOpenCodeArr:[
                    {expect:'19022',foreCode:['02','06','13','16','19','29'],backCode:['07']},
                    {expect:'19023',foreCode:['04','06','13','16','19','29'],backCode:['05']},
                    {expect:'19024',foreCode:['05','06','13','16','19','29'],backCode:['07']},
                    {expect:'19025',foreCode:['04','06','13','16','19','29'],backCode:['08']},
                    {expect:'19026',foreCode:['03','06','13','16','19','29'],backCode:['09']},
                    ]
            }
        },
        computed:{
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //选号区高度
            betHeight(){
                return this.$store.state.clientHeight - 152
            },
            //玩法下拉高度
            selctHeight(){
                return this.$store.state.clientHeight - 40
            },
            recentHeight(){
                return this.$store.state.clientHeight - 70
            },
            //倒计时格式化
            timer(){
                return this.$bet.formatDownTime(this.initData.down_time)
            },
            //选号篮数量
            badge(){
                return this.$store.state.shuzicai.plan.length
            },
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
                    if(num>=3 ){
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
                    'down_time': 6000,
                    "prizepool" : "72.81亿",
                    'end_time':"2019-03-04 20:00:00",
                    'todyIsKj':1
                },
                miss:{
                    "fore" : [ 1, 6, 3, 2, 4, 20, 0, 1, 9, 6, 2, 7, 0, 7, 8, 1, 0, 3, 2, 7, 16, 4, 20, 0, 2, 3, 1, 5, 14, 6, 7, 2, 0],
                    "back" : [ 0, 2, 1, 4, 14, 3, 9, 8, 7, 0, 7, 1,0, 7, 1,5]
                }
            }
            this.$set(this,'initData',data.info)
            this.$set(this,'miss',data.miss)
            this.showMarquee(this.num)
            this.timeInterval();
            this.$store.commit('setKeepAlivePage','ssq')
        },
        //离开当前投注页面
        beforeRouteLeave(to, from, next){
            if(to.path =='/shuzicai/bet'){
                //缓存页面
                this.$store.commit('setKeepAlivePage','ssq')
            }else {
                this.$store.commit('delKeepAlivePage','ssq') //清除投注页面缓存
                this.$store.commit('delKeepAlivePage','shuzicaiBet') //清除订单页面缓存
                this.$store.commit('clearSzcBetNum') //清空数字彩投注数据
            }
            next();
        }
    }
</script>
