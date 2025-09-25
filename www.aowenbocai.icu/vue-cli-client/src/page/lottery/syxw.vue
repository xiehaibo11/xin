<template>
    <div class="lottery-bg">
        <div class="loading" v-if="loading">
            <mt-spinner :type="3"></mt-spinner>
        </div>
        <div v-if="!loading" class="lottery-box">
            <!--顶部-->
            <stop-sale-tips :is-stop="isStop"></stop-sale-tips>
            <div class="head">
                <mt-header>
                    <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
                    <mt-button icon="more" slot="right" @click.native = "showMore = !showMore;show = false"></mt-button>
                </mt-header>
                <div class="select flex-box">
                    <span class="f-mini label">玩法</span>
                    <span class="chose" @click="show = !show">{{selected}}-{{topText}}<i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
                </div>
                <transition name="fade">
                <div class="layout z-3" @click.self="showMore = false" v-show="showMore">
                    <div class="head-list border-1px">
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
                        <ul class="clearfloat bet-fir">
                            <template v-for="(item,index) in play">
                                <li @click="chosePlay(item.name,item.type,item.gain)" :class="{'active':type == item.type}">{{item.name}}</li>
                            </template>
                        </ul>
                        <div class="bet-list" v-if="type == 13">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">任选一</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio13 == 1}" @click="radio13 = 1;show = false" v-if="small[13][1] !== 1">复式</li>
                                    <li :class="{'active': radio13 == 3}" @click="radio13 = 3;show = false" v-if="small[13][3] !== 1">单式</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 1">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">任选二</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio1 == 1}" @click="radio1 = 1;show = false" v-if="small[1][1] !== 1">复式</li>
                                    <li :class="{'active': radio1 == 3}" @click="radio1 = 3;show = false" v-if="small[1][3] !== 1">单式</li>
                                    <li :class="{'active': radio1 == 2}" @click="radio1 = 2;show = false" v-if="small[1][2] !== 1">胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 2">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">任选三</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio2 == 1}" @click="radio2 = 1;show = false" v-if="small[2][1] !== 1">复式</li>
                                    <li :class="{'active': radio2 == 3}" @click="radio2 = 3;show = false" v-if="small[2][3] !== 1">单式</li>
                                    <li :class="{'active': radio2 == 2}" @click="radio2 = 2;show = false" v-if="small[2][2] !== 1">胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 3">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">任选四</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio3 == 1}" @click="radio3 = 1;show = false" v-if="small[3][1] !== 1">复式</li>
                                    <li :class="{'active': radio3 == 3}" @click="radio3 = 3;show = false" v-if="small[3][3] !== 1">单式</li>
                                    <li :class="{'active': radio3 == 2}" @click="radio3 = 2;show = false" v-if="small[3][2] !== 1">胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 4">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">任选五</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio4 == 1}" @click="radio4 = 1;show = false" v-if="small[4][1] !== 1">复式</li>
                                    <li :class="{'active': radio4 == 3}" @click="radio4 = 3;show = false" v-if="small[4][3] !== 1">单式</li>
                                    <li :class="{'active': radio4 == 2}" @click="radio4 = 2;show = false" v-if="small[4][2] !== 1">胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 5">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">任选六</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio5 == 1}" @click="radio5 = 1;show = false" v-if="small[5][1] !== 1">复式</li>
                                    <li :class="{'active': radio5 == 3}" @click="radio5 = 3;show = false" v-if="small[5][3] !== 1">单式</li>
                                    <li :class="{'active': radio5 == 2}" @click="radio5 = 2;show = false" v-if="small[5][2] !== 1">胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 6">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">任选七</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio6 == 1}" @click="radio6 = 1;show = false" v-if="small[6][1] !== 1">复式</li>
                                    <li :class="{'active': radio6 == 3}" @click="radio6 = 3;show = false" v-if="small[6][3] !== 1">单式</li>
                                    <li :class="{'active': radio6 == 2}" @click="radio6 = 2;show = false" v-if="small[6][2] !== 1">胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 7">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">任选八</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio7 == 1}" @click="radio7 = 1;show = false" v-if="small[7][1] !== 1">复式</li>
                                    <li :class="{'active': radio7 == 3}" @click="radio7 = 3;show = false" v-if="small[7][3] !== 1">单式</li>
                                    <li :class="{'active': radio7 == 2}" @click="radio7 = 2;show = false" v-if="small[7][2] !== 1">胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 8">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">前一</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio8 == 1}" @click="radio8 = 1;show = false" v-if="small[8][1] !== 1">复式</li>
                                    <li :class="{'active': radio8 == 3}" @click="radio8 = 3;show = false" v-if="small[8][3] !== 1">单式</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 9">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">前二组选</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio9 == 1}" @click="radio9 = 1;show = false" v-if="small[9][1] !== 1">复式</li>
                                    <li :class="{'active': radio9 == 3}" @click="radio9 = 3;show = false" v-if="small[9][3] !== 1">单式</li>
                                    <li :class="{'active': radio9 == 2}" @click="radio9 = 2;show = false" v-if="small[9][2] !== 1">胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 10">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">前二直选</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio10 == 1}" @click="radio10 = 1;show = false" v-if="small[10][1] !== 1">复式</li>
                                    <li :class="{'active': radio10 == 3}" @click="radio10 = 3;show = false" v-if="small[10][3] !== 1">单式</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 11">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">前三组选</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio11 == 1}" @click="radio11 = 1;show = false" v-if="small[11][1] !== 1">复式</li>
                                    <li :class="{'active': radio11 == 3}" @click="radio11 = 3;show = false" v-if="small[11][3] !== 1">单式</li>
                                    <li :class="{'active': radio11 == 2}" @click="radio11 = 2;show = false" v-if="small[11][2] !== 1">胆拖</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 12">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">前三直选</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio12 == 1}" @click="radio12 = 1;show = false" v-if="small[12][1] !== 1">复式</li>
                                    <li :class="{'active': radio12 == 3}" @click="radio12 = 3;show = false" v-if="small[12][3] !== 1">单式</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--顶部 end-->
            <!--倒计时+ 期号-->
            <DownTime/>
            <!--投注操作-->
            <div class="bet-cont">
                <!--任选1-->
                <div v-show="type==13" class="bet-item rx2">
                    <syxw-rx-normal v-show="radio13 == 1" :type="type" :number="1" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-copy-bet v-show="radio13 == 3" :type="type" :n="1" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--任选2-->
                <div v-show="type==1" class="bet-item rx2">
                    <syxw-rx-normal v-show="radio1 == 1" :type="type" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-rx-dt v-show="radio1 == 2" :type="type" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-dt>
                    <syxw-copy-bet v-show="radio1 == 3" :type="type" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--任选3-->
                <div v-show="type==2" class="bet-item rx3">
                    <syxw-rx-normal v-show="radio2 == 1" :type="type" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-rx-dt v-show="radio2 == 2" :type="type" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-dt>
                    <syxw-copy-bet v-show="radio2 == 3" :type="type" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--任选4-->
                <div v-show="type==3" class="bet-item rx4">
                    <syxw-rx-normal v-show="radio3 == 1" :type="type" :number="4" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-rx-dt v-show="radio3 == 2"  :type="type" :number="4" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-dt>
                    <syxw-copy-bet v-show="radio3 == 3" :type="type" :n="4" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--任选5-->
                <div v-show="type==4" class="bet-item rx5">
                    <syxw-rx-normal v-show="radio4 == 1" :type="type" :number="5" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-rx-dt v-show="radio4 == 2" :type="type" :number="5" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-dt>
                    <syxw-copy-bet v-show="radio4 == 3" :type="type" :n="5" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--任选6-->
                <div v-show="type==5" class="bet-item rx6">
                    <syxw-rx-normal v-show="radio5 == 1" :type="type" :number="6" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-rx-dt v-show="radio5 == 2" :type="type" :number="6" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-dt>
                    <syxw-copy-bet v-show="radio5 == 3" :type="type" :n="6" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--任选7-->
                <div v-show="type==6" class="bet-item rx7">
                    <syxw-rx-normal v-show="radio6 == 1" :type="type" :number="7" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-rx-dt v-show="radio6 == 2" :type="type" :number="7" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-dt>
                    <syxw-copy-bet v-show="radio6 == 3" :type="type" :n="7" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--任选8-->
                <div v-show="type==7" class="bet-item rx8">
                    <syxw-rx-normal v-show="radio7 == 1" :type="type" :number="8" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-copy-bet v-show="radio7 == 3" :type="type" :n="8" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                    <syxw-rx-dt v-show="radio7 == 2" :type="type" :number="8" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-dt>
                </div>
                <!--前一组选-->
                <div v-show="type==8" class="bet-item q1">
                    <syxw-rx-normal v-show="radio8 == 1" :type="type" :number="1" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-copy-bet v-show="radio8 == 3" :type="type" :n="1" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--前二组选 -->
                <div v-show="type==9" class="bet-item qzu2">
                    <syxw-rx-normal v-show="radio9 == 1" :type="type" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-rx-dt v-show="radio9 == 2" :type="type" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-dt>
                    <syxw-copy-bet v-show="radio9 == 3" :type="type" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--前二直选-->
                <div v-show="type==10" class="bet-item">
                    <syxw-zx v-show="radio10 == 1" :type="type" :number="2" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-zx>
                    <syxw-copy-bet v-show="radio10 == 3" :type="type" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--前三组选-->
                <div v-show="type==11" class="bet-item qzu2">
                    <syxw-rx-normal v-show="radio11 == 1" :type="type" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-normal>
                    <syxw-rx-dt v-show="radio11 == 2" :type="type" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-rx-dt>
                    <syxw-copy-bet v-show="radio11 == 3" :type="type" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
                <!--前三直选-->
                <div v-show="type==12" class="bet-item">
                    <syxw-zx v-show="radio12 == 1" :type="type" :number="3" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-zx>
                    <syxw-copy-bet v-show="radio12 == 3" :type="type" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel"></syxw-copy-bet>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DownTime from 'components/lottery/DownTime.vue' //顶部期号+ 倒计时组件
    import RecentOpen from 'components/lottery/RecentOpen.vue' //近期开奖
    import SyxwRxNormal from 'components/lottery/syxw/RxNormal.vue' //11选5任选2/3/4/5/6/7/8 /前一/前二组选/前三组选 普通投注组件
    import SyxwRxDt from 'components/lottery/syxw/RxDt.vue' //11选5任选2/3/4/5/6/7/8 /前一/前二组选/前三组选 胆拖投注组件
    import SyxwZx from 'components/lottery/syxw/SyxwZx.vue' //11选5任选前二直选/前三直选 投注组件
    import SyxwCopyBet from 'components/lottery/syxw/CopyBet.vue' //单式
    import StopSaleTips from 'components/lottery/StopSaleTips.vue' //暂停销售
    export default {
        name:'syxw',
        data () {
            return {
                betModel:1,

                show:false, //选择玩法
                showMore:false, //右侧列表
                selected:'',//选中的玩法名
                gain:'',//选中的奖金设置值
                type:'',//选中的玩法type值
                title:'',
                small:'',//玩法开启否
                isStop:false,

                loading: true,
                play:[], //玩法配置内容
                info:{}, //初始数据

                radio1 : 1,
                radio2 : 1,
                radio3 : 1,
                radio4 : 1,
                radio5 : 1,
                radio6 : 1,
                radio7 : 1,
                radio8 : 1,
                radio9 : 1,
                radio10 : 1,
                radio11 : 1,
                radio12 : 1,
                radio13 : 1,
            }
        },
        components:{
            DownTime,
            RecentOpen,
            SyxwRxNormal,
            SyxwRxDt,
            SyxwZx,
            SyxwCopyBet,
            StopSaleTips
        },
        computed:{
            //期号类型
            expectType(){
                return this.$store.state.lottery.expect_type
            },
            miss(){
                return this.$store.state.lottery.miss
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
                let a = this.betModel == 1 ? 1 : 2; //模式2下奖金减半
                let str = this.gain
                let res = this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accMul(Number(str),Number(this.maxRebate)),Number(this.scale)) : str
                return this.$bet.accDiv(res,a,5)  //保留4位小数
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
            //玩法匹配
            nameObj () {
                return {
                    1 :{ name:'任二',radio : this.radio1},
                    2 :{ name:'任三',radio : this.radio2},
                    3 :{ name:'任四',radio : this.radio3},
                    4 :{ name:'任五',radio : this.radio4},
                    5 :{ name:'任六',radio : this.radio5},
                    6 :{ name:'任七',radio : this.radio6},
                    7 :{ name:'任八',radio : this.radio7},
                    8 :{ name:'前一',radio : this.radio8},
                    9 :{ name:'前二组选',radio : this.radio9},
                    10 :{ name:'前二直选',radio : this.radio10},
                    11 :{ name:'前三组选',radio : this.radio11},
                    12 :{ name:'前三直选',radio : this.radio12},
                    13 :{ name:'任一',radio : this.radio13},
                }
            },
            radioObj(){
                return {
                    1 : '复式',
                    2 : '胆拖',
                    3 : '单式'
                }
            },
            //投注文字显示处理
            text() {
                return this.nameObj[this.type].name + this.radioObj[this.nameObj[this.type].radio]
            },
            //顶部玩法显示
            topText(){
                return this.radioObj[this.nameObj[this.type].radio]
            },
            //玩法下拉高度
            selctHeight(){
                return this.$store.state.clientHeight - 40
            },
        },
        methods:{
            //选择玩法
            chosePlay(name,type,gain){
                this.selected = name
                this.type = type
                this.gain = gain
            },
            //中奖说明
            showPlayInfo(){
                this.showMore = false
                this.$router.push({
                    path:'/playInfo',
                    query:{
                        cz:this.cz,
                        name:this.name,
                        model:this.betModel
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
            this.$axios('/index/Syxw/getLotteryInfo/name/' + this.name).then(({data})=>{
                this.$store.commit('setType',data.lottery.expect_type);
                this.$store.commit('setFirstIssue',data.firstIssue);
                this.$set(this,'info',data.info);
                this.$set(this,'betModel',data.mode);
                this.$store.commit('setBetData',data.info);
                this.$store.commit('setMiss',data.miss);
                this.$set(this,'play',data.play);
                this.$store.commit('setPlayInfo',data.play);
                this.$set(this,'title',data.title);
                this.$store.commit('setRecentOpen',data.ten);
                this.$store.commit('isGetNewCode',data.getnewcode);
                this.$set(this,'small',data.small);
                this.$store.commit('setSmallInfo',data.small);
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
            this.$store.commit('setKeepAlivePage','syxw')
        },
        //返回首页时清除倒计时等
        beforeRouteLeave(to, from, next){
            if(to.path =='/betOrder' || to.path == '/playInfo' || to.path == '/historyCode' || to.path == '/news/detail'|| to.path == '/lottery/trade'|| to.path == '/lotteryRecord'){
                this.$store.commit('setKeepAlivePage','syxw')
            }else {
                this.$store.commit('delKeepAlivePage','syxw')
                this.$store.commit('clearDownTime'); //清除倒计时定时器
                this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
                this.$store.commit('clearBetData'); //清除初始数据
                this.$store.commit('clearBetNum'); //清除投注数据
                sessionStorage.removeItem('betinfo');
            }
            this.$store.commit('clearRandomNum')  //清除开奖动画
            next();
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .t-height{
        height: 20px;
    }
    .loading{
        width: 28px;
        height: 28px;
        margin: 30px auto;
    }
</style>
