<template>
    <div class="lottery-ks">
        <div class="loading" v-if="loading">
            <mt-spinner :type="3"></mt-spinner>
        </div>
        <div v-if="!loading" class="lottery-box">
            <stop-sale-tips :is-stop="isStop"></stop-sale-tips>
            <!--顶部-->
            <div class="head head-ks">
                <mt-header>
                    <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
                    <mt-button icon="more" slot="right" @click.native = "showTopList"></mt-button>
                </mt-header>
                <div class="select flex-box">
                    <span class="f-mini label">玩法</span>
                    <span class="chose" @click="showPlay">{{selected}}<i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
                </div>
                <transition name="fade">
                <div class="layout z-3" @click.self="showMore = false" v-show="showMore">
                    <div class="head-list" style="box-shadow: none">
                        <ul >
                            <li class="border-bottom-1px" @click="showPlayInfo">中奖说明</li>
                            <li class="border-bottom-1px" @click="toPlayNews">玩法介绍</li>
                            <li class="border-bottom-1px" @click="toHistoryCode">历史开奖</li>
                            <li class="border-bottom-1px" @click="toTrade">走势图</li>
                            <li class="border-bottom-1px" @click="toRecord">投注记录</li>
                        </ul>
                    </div>
                </div>
                </transition>
                <div class="layout z-2 contentH" @click.self="show = false" v-show="show" :style="{height:selctHeight + 'px'}">
                    <div class="select-grounp">
                        <ul class="clearfloat">
                            <template v-for="(item,index) in play">
                                <li @click="chosePlay(item.name,item.type,item.gain)">
                                    <a :class="{'active':type == item.type}">
                                        <i v-if="type == item.type" class="iconfont icon-anonymous-iconfont gou"></i>
                                        <p class="play-name">{{item.name}}</p>
                                        <p class="f-mini play-gain">
                                            <em v-if="item.type == 1">最高赔{{handleGain(hhMaxGain)}}倍</em>
                                            <em v-else>赔率{{handleGain(item.gain)}}</em>
                                        </p>
                                        <p v-if="item.type == 1">
                                            <span class="open-dice dice1 p1"></span>
                                            <span class="open-dice dice2 p1"></span>
                                            <span class="open-dice dice3"></span>
                                        </p>
                                        <p v-if="item.type == 5">
                                            <span class="open-dice dice1"></span>
                                            <span class="open-dice dice2"></span>
                                            <span class="open-dice dice3"></span>
                                        </p>
                                        <p v-if="item.type == 2 || item.type == 3">
                                            <span class="open-dice dice1"></span>
                                            <span class="open-dice dice1"></span>
                                            <span class="open-dice dice1"></span>
                                        </p>
                                        <p v-if="item.type == 4">
                                            <span class="open-dice dice2"></span>
                                            <span class="open-dice dice3"></span>
                                            <span class="open-dice dice5"></span>
                                        </p>
                                        <p v-if="item.type == 6 || item.type == 7">
                                            <span class="open-dice dice1"></span>
                                            <span class="open-dice dice1"></span>
                                            <span class="open-dice dice3"></span>
                                        </p>
                                        <p v-if="item.type == 8">
                                            <span class="open-dice dice1"></span>
                                            <span class="open-dice dice4"></span>
                                            <span class="open-dice dice4"></span>
                                        </p>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
            <!--顶部 end-->
            <!--倒计时+ 期号-->
            <KsTopInfo/>
            <!--倒计时+ 期号 end-->
            <!--投注操作-->
            <div class="bet-cont">
                <!--和值 投注-->
                <div class="bet-item" v-show="type == 1">
                    <!--<div class="bet-item-tips tc f-sm c-light">猜3个开奖号相加的和，3-10为小，11-18为大。</div>-->
                    <ks-hz :type="type" :gain="play[0].gain" :title="title" :text="text" :name="name"
                           :bet-model="betModel" @change-gain="getHhGain" :max-gain="maxGain"></ks-hz>
                </div>
                <!--三同号通选 投注-->
                <div class="bet-item" v-show="type == 2">
                    <!--<div class="bet-item-tips tl f-sm c-light">对所有相同的3个号码（111、222、333、444、555、666）进行投注，任意号码开出即中奖，赔率{{maxGain}}。</div>-->
                    <ks-san :type="type" :gain="maxGain" :title="title" :text="text" :name="name"
                            :bet-model="betModel"></ks-san>
                </div>
                <!--三同号单选-->
                <div class="bet-item" v-show="type == 3">
                    <!--<div class="bet-item-tips tl f-sm c-light">对相同的3个号码（111、222、333、444、555、666）中的任意一个进行投注，所选号码开出即中奖，赔率{{maxGain}}。</div>-->
                    <ks-san :type="type" :gain="maxGain" :title="title" :text="text" :name="name"
                            :bet-model="betModel"></ks-san>
                </div>
                <!--三连号通选 投注-->
                <div class="bet-item" v-show="type == 5">
                    <!--<div class="bet-item-tips tl f-sm c-light">对所有相同的3个号码（123、234、345、456）进行投注，任意号码开出即中奖，赔率{{maxGain}}。</div>-->
                    <ks-san :type="type" :gain="maxGain" :title="title" :text="text" :name="name"
                            :bet-model="betModel"></ks-san>
                </div>
                <!--二同号复选 投注-->
                <div class="bet-item" v-show="type == 6">
                    <!--<div class="bet-item-tips tl f-sm c-light">开奖号码的任意2位，与您投注的二同号一致即中奖，赔率{{maxGain}}。</div>-->
                    <ks-san :type="type" :gain="maxGain" :title="title" :text="text" :name="name"
                            :bet-model="betModel"></ks-san>
                </div>
                <!--三不同号 投注-->
                <div class="bet-item" v-show="type == 4">
                    <!--<div class="bet-item-tips tl f-sm c-light">至少选择3个不同号码投注，所选号码与开奖号码一致即中奖，赔率{{maxGain}}。</div>-->
                    <ks-rx :type="type" :gain="maxGain" :title="title" :text="text" :name="name"
                           :bet-model="betModel" :number="3"></ks-rx>
                </div>
                <!--二不同号 投注-->
                <div class="bet-item" v-show="type == 8">
                    <!--<div class="bet-item-tips tl f-sm c-light">至少选择2个号码投注，与开奖号码的任意2位一致即中奖，赔率{{maxGain}}。</div>-->
                    <ks-rx :type="type" :gain="maxGain" :title="title" :text="text" :name="name"
                           :bet-model="betModel" :number="2"></ks-rx>
                </div>
                <!--二同号单选 投注-->
                <div class="bet-item" v-show="type == 7">
                    <!--<div class="bet-item-tips tl f-sm c-light">选择1个相同号码和1个不同号码投注，选号与开奖号码一致即中奖，赔率{{maxGain}}。</div>-->
                    <ks-dx :type="type" :gain="maxGain" :title="title" :text="text" :name="name"
                           :bet-model="betModel"></ks-dx>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import KsHz from 'components/lottery/Ks/KsHz.vue' //快3和值投注
    import KsSan from 'components/lottery/Ks/KsSan.vue' //快3 三同号通选、三同号单选、三连号通选、二同号复选投注
    import KsRx from 'components/lottery/Ks/KsRx.vue' //快3 三不同号、二不同号投注
    import KsDx from 'components/lottery/Ks/KsDx.vue' //快3 二同号单选投注
    import StopSaleTips from 'components/lottery/StopSaleTips.vue' //暂停销售
    import KsTopInfo from 'components/lottery/Ks/KsTopInfo.vue' //顶部期号 + 倒计时
    export default {
        name:'ks',
        data () {
            return {
                recentShow:false,//近期开奖
                show:false, //选择玩法
                showMore:false, //右侧列表
                selected:'',//选中的玩法名
                gain:'',//选中的奖金设置值
                type:'',//选中的玩法type值
                title:'',
                isStop:false,
                betModel:'' || 1,

                loading: true,
                play:[], //玩法配置内容
                info:{}, //初始数据
                open:[],//近期开奖

                hhGain:'',//混合奖金字符串
            }
        },
        components:{
            KsHz,
            KsSan,
            KsRx,
            KsDx,
            KsTopInfo,
            StopSaleTips
        },
        computed:{
            //期号类型
            expectType(){
                return this.$store.state.lottery.expect_type
            },
            //返点设置是否开启
            rebateIsOpen(){
                return this.$store.state.setting.rebate_isOpen == 1 ? true : false
            },
            //比例
            scale(){
                return this.$store.getters.getScale
            },
            //最高奖金计算百分比
            maxRebate:function () {
                return this.$store.getters.maxRebate
            },
            //返点后最高奖金
            maxGain:function () {
                let arr = this.type == 1 ? this.hhGain ? this.hhGain.split(",") : this.gain.split(",") : this.gain.split(",")
                let a = this.betModel == 1 ? 1 : 2; //模式2下奖金减半
                let s = this.$bet.getMaxMin(arr,'max')
                let res = this.rebateIsOpen ?  this.$bet.accDiv(this.$bet.accDiv(this.$bet.accMul(Number(s),Number(this.maxRebate)),Number(this.scale)),a,5) : this.$bet.accDiv(s,a,5) //保留4位小数
                return res//<!--混合投注type == 1时取最大值-->
            },
            //投注单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            cz(){
                return this.$route.path.replace('/','')
            },
            name(){ //游戏名eg:gd11
                return this.$route.query.name
            },
            //投注文字显示处理
            text() {
                const typeObj = {
                    1 : '和值',
                    2 : '三同号通选',
                    3 : '三同号单选',
                    4 : '三不同号',
                    5 : '三连号通选',
                    6 : '二同号复选',
                    7 : '二同号单选',
                    8 : '二不同号',
                }
                return typeObj[this.type]
            },
            //混合投注最高奖金
            hhMaxGain(){
                for(let i in this.play){
                    if(this.play[i].type == 1){
                        return this.$bet.getMaxMin(this.play[i].gain.split(","),'max')
                    }else {
                        return 0
                    }
                }
            },
            //玩法下拉高度
            selctHeight(){
                return this.$store.state.clientHeight - 40
            }
        },
        filters:{
            strSplit1(value){ //奖金显示处理 取第一个值
                var arr = value.split(',')
                return arr[0]
            },
            lastExpect(value){
                return value.substring(value.length-2);
            },
            subStr(val){
                return val.toString().slice(4);
            }
        },
        methods:{
            //奖金计算方法
            handleGain(g) {
                let a = this.betModel == 1 ? 1 : 2; //模式2下奖金减半
                return this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accDiv(this.$bet.accMul(Number(g),this.maxRebate),this.scale),a,5) : this.$bet.accDiv(Number(g),a,5)
            },
            //混合奖金字符串
            getHhGain (emitVal) {
                this.hhGain = emitVal
            },
            //选择玩法
            chosePlay(name,type,gain){
                this.selected = name
                this.type = type
                this.gain = gain
                this.show = false
            },
            //中奖说明
            showPlayInfo(){
                this.showMore = false
                this.$router.push({
                    path:'/playInfo',
                    query:{
                        cz:this.cz,
                        name:this.name
                    }
                })
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
            //右上角显示
            showTopList(){
                this.show = false
                this.showMore = !this.showMore
            },
            //玩法下拉框
            showPlay(){
                this.showMore = false
                this.show = !this.show
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
            },
            //进入投注记录
            toRecord(){
                this.showMore = false
                this.$router.push({
                    path:'/lotteryRecord',
                    query:{
                        name : this.name,
                        cz:this.cz
                    }
                })
            }
        },
        created(){
            this.loading = true
            this.$axios('/index/Ks/getLotteryInfo/name/' + this.name).then(({data})=>{
                this.$store.commit('setType',data.lottery.expect_type);
                this.$store.commit('setFirstIssue',data.firstIssue);
                this.$set(this,'info',data.info);
                this.$set(this,'betModel',data.mode);
                this.$store.commit('setBetData',data.info);
                this.$set(this,'play',data.play);
                this.$store.commit('setPlayInfo',data.play);
                this.$set(this,'title',data.title);
                this.$store.commit('setRecentOpen',data.ten);
                this.$store.commit('isGetNewCode',data.getnewcode);
                for(let i in data.play){
                    if( data.play[i].type == data.info.type){
                        this.$set(this,'selected',data.play[i].name);
                    }
                }
                this.$set(this,'type',data.info.type);
                this.$set(this,'gain',data.info.gain);
                this.$store.commit('setAwardNumber',data.info.awardNumber.code);
                this.$store.commit('setRebateInfo',data);
                this.isStop = data.lottery.pause
                this.loading = false
            })
            this.$store.commit('setKeepAlivePage','ks')
        },
        //返回首页时清除倒计时等
        beforeRouteLeave(to, from, next){
            if(to.path =='/betOrder' || to.path == '/playInfo' || to.path == '/historyCode' || to.path == '/news/detail'|| to.path == '/lottery/trade'|| to.path == '/lotteryRecord'){
                this.$store.commit('setKeepAlivePage','ks')
            }else {
                this.$store.commit('delKeepAlivePage','ks')
                this.$store.commit('clearDownTime'); //清除倒计时定时器
                this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
                this.$store.commit('clearBetData'); //清除初始数据
                this.$store.commit('clearBetNum'); //清除投注数据
                sessionStorage.removeItem('betinfo');
            }
            next();
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .t-height{
        height: 100px;
    }
    .loading{
        width: 28px;
        height: 28px;
        margin: 30px auto;
    }
    $ks-color-base:#3f7555;
    $ks-color-pull:#36674b;
    $ks-color-dark: #184612;
    $ks-color-black: #252625;
    $ks-border-color:#74a78d;
    $ks-color-cont:#549263;
    $ks-color-yellow:#eaca52;
    .c-light{
        color: #caebda;
    }
    .c-light-2{
        color: #9bdbc2;
    }
    .lottery-ks{
        color: #ffffff;
        background-color:$ks-color-base;
        .icon-jiantou{
            color: #f8d447;
        }
        .mint-header{
            background-color: $ks-color-black;
        }
        .head-ks{
            .select-grounp{
                background-color: $ks-color-pull;
                border-bottom: 2px solid $ks-color-dark;
                padding: 5px;
                li{
                    width: 33.3333%;
                    float:left;
                    margin: 5px 0;
                    text-align: center;
                    a{
                        display: inline-block;
                        width: 87%;
                        background-color: #457a53;
                        border:2px solid $ks-border-color;
                        color: #ffffff;
                        height: 75px;
                        @include rounded(8px);
                        line-height: 20px;
                        position: relative;
                        &.active{
                            background-color: #305d3f;
                            border-color: $ks-color-yellow;
                            .gou{
                                position: absolute;
                                color: $ks-color-yellow;
                                font-size: 18px;
                                bottom: 0;
                                right: 1px;
                            }
                        }
                        .play-name{
                            font-size: 15px;
                            padding-top: 5px;
                        }
                        .play-gain{
                            color: #add3b9;
                        }
                        .p1{
                            position: relative;
                        }
                        .p1:after{
                            content: "+";
                            position: absolute;
                            font-size: 13px;
                            top: 2px;
                            right: -5px;
                        }
                    }
                }
            }
        }
        /*倒计时+近期开奖*/
        .ks-time{
            width: 100%;
            top: 40px;
            background-color: #36674b;
            border-bottom: 1px solid $ks-color-dark;
            .ks-kjCode{
                border-right: 1px solid $ks-color-dark;
                height: 60px;
                .dice-last{
                    position: relative;
                    i{
                        position: absolute;
                        right: -25px;
                        top: 3px;
                        font-size: 16px;
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
                .open-dice{
                    width: 28px;
                    height: 28px;
                }
            }
            .ks-downTime{
                height: 60px;
                border-left: 1px solid #42795a;
            }
            .kj-title{
                padding: 4px 0 2px;
                color: #cae2d0;
                font-size: 15px;
            }
            .kj-time{
                font-size:26px;
                color: #ffffff;
                padding-top: 5px;
                letter-spacing: 3px;
                font-weight: 600;
            }
        }
    }
    .open-dice{
        display: inline-block;
        width: 26px;
        height: 26px;
        text-align: center;
        background: url(~assets/images/m_open_num.png) no-repeat top left;
        background-size: 200% 600%;
        margin-top: 2px;
    }
    $height:-26px;
    .dice1{
        background-position:0 0;
    }
    .dice2{
        background-position:0 $height;
    }
    .dice3{
        background-position:0 $height * 2;
    }
    .dice4{
        background-position:0 $height * 3;
    }
    .dice5{
        background-position:0 $height * 4;
    }
    .dice6{
        background-position:0 $height * 5;
    }
    .dice-lg-1{
        background-position:0 0;
    }
    $height-lg:-28px;
    .dice-lg-2{
        background-position:0  $height-lg;
    }
    .dice-lg-3{
        background-position:0  $height-lg * 2;
    }
    .dice-lg-4{
        background-position:0 $height-lg * 3;
    }
    .dice-lg-5{
        background-position:0 $height-lg * 4;
    }
    .dice-lg-6{
        background-position:0 $height-lg * 5;
    }
    .rDice{
        background-position:100% 0;
    }

    .bet-item-tips{
        padding: 0 15px;
    }
    .bet-item{
        /*padding: 20px 0 0;*/
    }
    //table 列表样式
    .ks-recent-open-box{
        top: 100px;
        .layout{
            position: fixed;
            top:100px;
            width: 100%;
            height: 100%;
            z-index: 50;
            left: 0;
        }
    }
    .ks-table-list{
        width: 100%;
        tr{
            td,th{
                font-size: 12px;
                text-align: center;
                padding: 3px 0;
                border:solid #396d4f;
                border-width: 0 .5px .5px 0;
                color: #ffffff;
                &:last-child{
                    border-width: 0 0 .5px 0;
                }
            }
            td{
                background-color: #2a523b;
            }
            th{
                padding: 5px 0;
                background-color:#2a523b;
            }
            &:nth-child(even) td{
                background-color: #2f5d42;
            }
        }
        em{
            display: inline-block;
            width: 22px;
            height: 22px;
            line-height: 22px;
            border-radius: 3px;
            background-color: #2f5d42;
            margin: 0 3px;
            &.xiao , &.shuang{
                background-color: #FF9800;
            }
            &.da , &.dan{
                background-color: #2196F3;
            }
        }
    }
</style>
