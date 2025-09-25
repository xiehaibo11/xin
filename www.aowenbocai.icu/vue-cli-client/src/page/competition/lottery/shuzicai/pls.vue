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
                    <mt-button icon="more" slot="right" @click.native = "showMore = !showMore;show = false"></mt-button>
                </mt-header>
                <div class="select flex-box">
                    <span class="f-mini label">玩法</span>
                    <span class="chose" @click="show = !show">排列3-{{selected}}{{topText}}<i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
                </div>
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
                <div class="layout z-2 contentH" @click.self="show = false" v-show="show" :style="{height:selctHeight + 'px'}">
                    <div class="select-grounp">
                        <ul class="clearfloat bet-fir">
                            <template v-for="(item,index) in play">
                                <li @click="chosePlay(item.name,item.type,item.gain)" :class="{'active':type == item.type}">{{item.name}}</li>
                            </template>
                        </ul>
                        <div class="bet-list" v-if="type == 1">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">直选</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio1 == 1}" @click="radio1 = 1;show = false" >复式</li>
                                    <li :class="{'active': radio1 == 2}" @click="radio1 = 2;show = false" >单式</li>
                                    <li :class="{'active': radio1 == 3}" @click="radio1 = 3;show = false" >和值</li>
                                    <li :class="{'active': radio1 == 4}" @click="radio1 = 4;show = false" >胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 2">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">组三</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio2 == 1}" @click="radio2 = 1;show = false" >复式</li>
                                    <li :class="{'active': radio2 == 2}" @click="radio2 = 2;show = false" >单式</li>
                                    <li :class="{'active': radio2 == 3}" @click="radio2 = 3;show = false" >和值</li>
                                    <li :class="{'active': radio2 == 4}" @click="radio2 = 4;show = false" >胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 3">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">组六</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio3 == 1}" @click="radio3 = 1;show = false" >复式</li>
                                    <li :class="{'active': radio3 == 2}" @click="radio3 = 2;show = false" >单式</li>
                                    <li :class="{'active': radio3 == 3}" @click="radio3 = 3;show = false" >和值</li>
                                    <li :class="{'active': radio3 == 4}" @click="radio3 = 4;show = false" >胆拖</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <li>{{initData.expect}}期 开奖时间: <em>{{initData.kjDate}}</em> <em>{{initData.kjWeek}}</em> <em class="red">{{initData.kjTime}}</em></li>
                        <li>{{initData.expect}}期 奖池滚存 <em class="red">{{initData.prizepool}}</em>元</li>
                    </ul>
                </div>
                <div style="width: 50px;text-align: center">
                    <i class="iconfont icon-xialajiantou rotateIcon" :class="recentOpen ? 'is-active' : 'no-active'"></i>
                </div>
            </div>
            <!--滚动期号信息 end-->

            <!--投注区-->
            <div class="bet-cont">
                <!--直选-->
                <div v-show="type==1" class="bet-item zx">
                    <!--复式-->
                    <pls-zhix-fs v-if="radio1==1" :type="sign" :text="text" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time"></pls-zhix-fs>
                    <!--单式-->
                    <pls-copy-bet v-if="radio1==2" :type="sign" :text="text" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time" :n="3"></pls-copy-bet>
                    <!--和值-->
                    <pls-hz v-if="radio1==3" :type="sign" :text="text" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time"></pls-hz>
                    <!--胆拖-->
                    <pls-dt v-if="radio1==4" :type="sign" :text="text" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time" :n="3"></pls-dt>
                </div>
                <!--组三-->
                <div v-show="type==2" class="bet-item zs">
                    <!--复式-->
                    <pls-zx v-if="radio2==1" :type="sign" :text="text" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time" :n="2"></pls-zx>
                    <!--单式-->
                    <pls-copy-bet v-if="radio2==2" :type="sign" :text="text" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time" :n="2"></pls-copy-bet>
                    <!--和值-->
                    <pls-hz v-if="radio2==3" :type="sign" :text="text" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time"></pls-hz>
                    <!--胆拖-->
                    <pls-dt v-if="radio2==4" :type="sign" :text="text" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time" :n="2"></pls-dt>
                </div>
                <!--组六-->
                <div v-show="type==3" class="bet-item zl">
                    <!--复式-->
                    <pls-zx v-if="radio3==1" :type="sign" :text="text" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time" :n="3"></pls-zx>
                    <!--单式-->
                    <pls-copy-bet v-if="radio3==2" :type="sign" :text="text" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time" :n="3"></pls-copy-bet>
                    <!--和值-->
                    <pls-hz v-if="radio3==3" :type="sign" :text="text" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time"></pls-hz>
                    <!--胆拖-->
                    <pls-dt v-if="radio3==4" :type="sign" :text="text" :miss="miss" :expect="initData.expect" :gain="gain" :title="title" :end-time="initData.end_time" :n="3"></pls-dt>
                </div>
            </div>
            <!--投注区 end-->
        </div>
    </div>
</template>

<script>
    import PlsZhixFs from 'components/competition/shuzicai/PlZhixFs.vue' //排列3-直选复式
    import PlsCopyBet from 'components/competition/shuzicai/pls/PlsCopyBet.vue' //排列3-单式
    import PlsHz from 'components/competition/shuzicai/pls/PlsHz.vue' //排列3-和值
    import PlsDt from 'components/competition/shuzicai/pls/PlsDt.vue' //排列3-胆拖
    import PlsZx from 'components/competition/shuzicai/pls/PlsZx.vue' //排列3-组三、组六复式
    export default {
        name: 'pls',
        components:{
            PlsZhixFs,
            PlsCopyBet,
            PlsHz,
            PlsDt,
            PlsZx,
        },
        data () {
            return {
                num:0,//滚动距离
                intervalTime:null, //倒计时定时器

                show:false, //选择玩法
                showMore:false, //右侧列表
                selected:'直选',//选中的玩法名
                type:1, //选中的玩法标识
                gain:'1040',

                loading : false,
                play:[{'type': 1 ,'name' : '直选','gain':'1040'},
                    {'type': 2 ,'name' : '组三','gain':'346'},
                    {'type': 3 ,'name' : '组六','gain':'173'}],

                initData:{}, //初始数据
                miss:{},//遗漏
                name:'pls',
                radio1 : 1,//直选
                radio2 : 1,//组三
                radio3 : 1,//组六

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
            },
            //玩法匹配
            nameObj () {
                return {
                    1 :{ name:'直选',radio : this.radio1},
                    2 :{ name:'组三',radio : this.radio2},
                    3 :{ name:'组六',radio : this.radio3}
                }
            },
            radioObj(){
                return {
                    1 : '复式',
                    2 : '单式',
                    3 : '和值',
                    4 : '胆拖'
                }
            },
            //投注标识
            sign(){
                return this.type + '.' + this.nameObj[this.type].radio
            },
            //投注文字显示处理
            text() {
                return this.nameObj[this.type].name + this.radioObj[this.nameObj[this.type].radio]
            },
            //顶部玩法显示
            topText(){
                return this.radioObj[this.nameObj[this.type].radio]
            },
        },
        methods:{
            chosePlay(name,type,gain){
                this.selected = name
                this.type = type
                this.gain = gain
            },
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
                    'down_time': 6000,
                    'end_time':"2019-03-04 20:00:00"
                },
                miss:0
            }
            this.$set(this,'initData',data.info)
            this.$set(this,'miss',data.miss)
            this.showMarquee(this.num)
            this.timeInterval();
            this.$store.commit('setKeepAlivePage','pls')
        },
        //离开当前投注页面
        beforeRouteLeave(to, from, next){
            if(to.path =='/shuzicai/bet'){
                //缓存页面
                this.$store.commit('setKeepAlivePage','pls')
            }else {
                this.$store.commit('delKeepAlivePage','pls') //清除投注页面缓存
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
