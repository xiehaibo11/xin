<template>
    <div class="lottery-bg">
        <div class="loading" v-if="loading">
            <mt-spinner :type="3"></mt-spinner>
        </div>
        <div v-if="!loading" class="lottery-box">
            <stop-sale-tips :is-stop="isStop"></stop-sale-tips>
            <!--顶部-->
            <div class="head">
                <mt-header>
                    <mt-button icon="back" slot="left" @click.native="$router.goBack(-1)"></mt-button>
                    <mt-button icon="more" slot="right" @click.native = "showMore = !showMore"></mt-button>
                </mt-header>
                <div class="select flex-box">
                    <span class="f-mini label">玩法</span>
                    <span class="chose" @click="show = !show">{{selected}}-{{topText}} <i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
                </div>
                <transition name="fade">
                    <div class="layout z-3" @click.self="showMore = false" v-show="showMore">
                        <div class="head-list border-1px">
                            <ul >
                                <li class="border-bottom-1px" @click="showPlayInfo">中奖说明</li>
                                <li class="border-bottom-1px" @click="toPlayNews">玩法介绍</li>
                                <li class="border-bottom-1px" @click="toHistoryCode">历史开奖</li>
                                <li class="border-bottom-1px" @click="toRecord">投注记录</li>
                            </ul>
                        </div>
                    </div>
                </transition>
                <div class="layout z-2 contentH" @click.self="show = false" v-show="show" :style="{height:selctHeight + 'px'}">
                    <div class="select-grounp">
                        <ul class="clearfloat bet-fir">
                            <template v-for="(item,index) in play">
                                <li @click="chosePlay(item.name,item.type,item.gain,item.sign)" :class="{'active':type == item.type}">{{item.name}}</li>
                            </template>
                        </ul>
                        <div class="bet-list" v-if="type == 1">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜冠军</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio1 == 1}" @click="radio1 = 1;show = false">普通</li>
                                    <li :class="{'active': radio1 == 3}" @click="radio1 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 2">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜亚军</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio2 == 2}" @click="radio2 = 2;show = false">精准</li>
                                    <li :class="{'active': radio2 == 1}" @click="radio2 = 1;show = false">普通</li>
                                    <li :class="{'active': radio2 == 3}" @click="radio2 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 3">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜季军</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio3 == 2}" @click="radio3 = 2;show = false">精准</li>
                                    <li :class="{'active': radio3 == 1}" @click="radio3 = 1;show = false">普通</li>
                                    <li :class="{'active': radio3 == 3}" @click="radio3 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 4">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜前四</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio4 == 2}" @click="radio4 = 2;show = false">精准</li>
                                    <li :class="{'active': radio4 == 1}" @click="radio4 = 1;show = false">普通</li>
                                    <li :class="{'active': radio4 == 3}" @click="radio4 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 5">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜前五</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio5 == 1}" @click="radio5 = 1;show = false">普通</li>
                                    <li :class="{'active': radio5 == 3}" @click="radio5 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 6">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜前六</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio6 == 1}" @click="radio6 = 1;show = false">普通</li>
                                    <li :class="{'active': radio6 == 3}" @click="radio6 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 7">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜前七</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio7 == 1}" @click="radio7 = 1;show = false">普通</li>
                                    <li :class="{'active': radio7 == 3}" @click="radio7 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 8">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜前八</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio8 == 1}" @click="radio8 = 1;show = false">普通</li>
                                    <li :class="{'active': radio8 == 3}" @click="radio8 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 9">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜前九</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio9 == 1}" @click="radio9 = 1;show = false">普通</li>
                                    <li :class="{'active': radio9 == 3}" @click="radio9 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 10">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">猜前十</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio10 == 1}" @click="radio10 = 1;show = false">普通</li>
                                    <li :class="{'active': radio10 == 3}" @click="radio10 = 3;show = false">粘贴</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 11">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">定位胆</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio11 == 1}" @click="radio10 = 1;show = false">普通</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 12">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">大小单双</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio12 == 1}" @click="radio12 = 1;show = false">前五大小单双</li>
                                    <li :class="{'active': radio12 == 2}" @click="radio12 = 2;show = false">后五大小单双</li>
                                    <li :class="{'active': radio12 == 3}" @click="radio12 = 3;show = false">冠亚和大小单双</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bet-list" v-if="type == 13">
                            <div class="flex-box flex-start chose-l">
                                <span class="l-tit">龙虎斗</span>
                                <ul class="l-chose flex clearfloat">
                                    <li :class="{'active': radio13 == 1}" @click="radio13 = 1;show = false">普通</li>
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
                <!--猜冠军-->
                <div class="bet-item" v-show="type == 1">
                    <pk-bet v-show="radio1 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-copy-bet v-show="radio1 == 3" :type="type" :n="1" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--猜亚军-->
                <div class="bet-item" v-show="type == 2">
                    <pk-bet v-show="radio2 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-bet v-show="radio2 == 2" :type="type + '.2'" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--精准-->
                    <pk-copy-bet v-show="radio2 == 3" :type="type" :n="2" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--猜季军-->
                <div class="bet-item" v-show="type == 3">
                    <pk-bet v-show="radio3 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-bet v-show="radio3 == 2" :type="type + '.2'" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--精准-->
                    <pk-copy-bet v-show="radio3 == 3" :type="type" :n="3" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--猜前四-->
                <div class="bet-item" v-show="type == 4">
                    <pk-bet v-show="radio4 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-bet v-show="radio4 == 2" :type="type + '.2'" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--精准-->
                    <pk-copy-bet v-show="radio4 == 3" :type="type" :n="4" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--猜前五-->
                <div class="bet-item" v-show="type == 5">
                    <pk-bet v-show="radio5 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-copy-bet v-show="radio5 == 3" :type="type" :n="5" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--猜前六-->
                <div class="bet-item" v-show="type == 6">
                    <pk-bet v-show="radio6 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-copy-bet v-show="radio6 == 3" :type="type" :n="6" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--猜前七-->
                <div class="bet-item" v-show="type == 7">
                    <pk-bet v-show="radio7 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-copy-bet v-show="radio7 == 3" :type="type" :n="7" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--猜前八-->
                <div class="bet-item" v-show="type == 8">
                    <pk-bet v-show="radio8 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-copy-bet v-show="radio8 == 3" :type="type" :n="8" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--猜前九-->
                <div class="bet-item" v-show="type == 9">
                    <pk-bet v-show="radio9 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-copy-bet v-show="radio9 == 3" :type="type" :n="9" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--猜前十-->
                <div class="bet-item" v-show="type == 10">
                    <pk-bet v-show="radio10 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                    <pk-copy-bet v-show="radio10 == 3" :type="type" :n="10" :gain="maxGain" :title="title" :text="text" :name="name" :bet-model="betModel" :sign="signVal"></pk-copy-bet><!--粘贴-->
                </div>
                <!--定位胆-->
                <div class="bet-item" v-show="type == 11">
                    <pk-bet v-show="radio11 == 1" :type="type" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-bet><!--普通-->
                </div>
                <!--大小单双-->
                <div class="bet-item" v-show="type == 12">
                    <pk-dxds v-show="radio12 == 1" :type="type + '.1'" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-dxds> <!--前五大小单双-->
                    <pk-dxds v-show="radio12 == 2" :type="type + '.2'" :gain="maxGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-dxds> <!--后五大小单双-->
                    <pk-dxds v-show="radio12 == 3" :type="type + '.3'" :gain="maxGain" :gyh-gain="gyhGain" :miss="miss" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-dxds> <!--冠亚和大小单双-->
                </div>
                <!--龙虎-->
                <div class="bet-item" v-show="type == 13">
                    <pk-lh v-show="radio13 == 1" :type="type" :gain="maxGain" :title="title" :text="text" :name="name" :small="small" :bet-model="betModel" :sign="signVal"></pk-lh><!--龙虎-->
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import DownTime from 'components/lottery/DownTime.vue' //顶部期号+ 倒计时组件
    import RecentOpen from 'components/lottery/RecentOpen.vue' //近期开奖
    import PkBet from 'components/lottery/pk10/PkBet.vue' //投注
    import PkDxds from 'components/lottery/pk10/PkDxds.vue' //大小单双投注
    import PkLh from 'components/lottery/pk10/PkLh.vue' //龙虎斗投注
    import PkCopyBet from 'components/lottery/pk10/PkCopyBet.vue' //单式
    import StopSaleTips from 'components/lottery/StopSaleTips.vue' //暂停销售
    export default {
        name:'pk10',
        data () {
            return {
                betModel:1,

                show:false, //选择玩法
                showMore:false, //右侧列表
                selected:'',//选中的玩法名
                gain:'',//选中的奖金设置值
                type:'',//选中的玩法type值
                sign:'',//标识
                title:'',
                miss:'' ,//遗漏
                small:'',//玩法开启否
                isStop:false,

                loading: true,
                play:[], //玩法配置内容
                info:{}, //初始数据

                radio1: '1',
                radio2: '2',
                radio3: '2',
                radio4: '2',
                radio5: '1',
                radio6: '1',
                radio7: '1',
                radio8: '1',
                radio9: '1',
                radio10: '1',
                radio11: '1',
                radio12: '1',
                radio13: '1',

                gyhGain:[] , //冠亚和奖金
            }
        },
        components:{
            DownTime,
            RecentOpen,
            PkBet,
            PkDxds,
            PkLh,
            PkCopyBet,
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
            maxRebate () {
                return this.$store.getters.maxRebate
            },
            //返点后最高奖金
            maxGain () {
                let a = this.betModel == 1 ? 1 : 2; //模式2下奖金减半
                //冠亚和大小单双
                if(this.type == 12){
                    var arr = []
                    if(this.radio12 == 1){
                        arr = this.gain.split(',').slice(0,1)
                    }
                    if(this.radio12 == 2){
                        arr = this.gain.split(',').slice(1,2)
                    }
                    if(this.radio12 == 3){
                        arr = this.gain.split(',').slice(2,6)
                    }
                    for(let i in arr){
                        arr[i] = this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accDiv(this.$bet.accMul(Number(arr[i]),Number(this.maxRebate)),Number(this.scale)),a,5)
                            : this.$bet.accDiv(arr[i],a,5)
                    }
                    if(this.radio12 == 3){
                        this.$set(this,'gyhGain',arr)
                        var newArr = JSON.parse(JSON.stringify(arr))
                        var min = this.$bet.getMaxMin(newArr,'min')
                        var max = this.$bet.getMaxMin(newArr,'max')
                        return min == max  ? min : min + '~' + max
                    }else {
                        return arr.join(',')
                    }
                }else {
                    let gain = this.gain
                    if(this.signVal == 'YJ.2'){
                        gain = this.small[0].gain
                    }
                    if(this.signVal == 'JJ.2'){
                        gain = this.small[1].gain
                    }
                    if(this.signVal == 'QS.2'){
                        gain = this.small[2].gain
                    }
                    let isExit = gain.toString().indexOf('-');
                    let arr = isExit > -1 ? gain.toString().split("-") : gain.toString().split(',')
                    for(let i in arr){
                        arr[i] = this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accDiv(this.$bet.accMul(Number(arr[i]),Number(this.maxRebate)),Number(this.scale)),a,5)
                            : this.$bet.accDiv(arr[i],a,5)
                    }
                    //保留4位小数
                    return arr.length > 1 ? arr[0]+ '~'+ arr[1] : arr.join(',')
                }
            },
            //投注单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
            name(){ //游戏名eg:gd11
                return this.$route.query.name
            },
            cz(){
                return this.$route.path.replace('/','')
            },
            nameObj(){
                return{
                    1 : {name:'猜冠军',radio : this.radio1},
                    2 : {name:'猜亚军',radio : this.radio2},
                    3 : {name:'猜季军',radio : this.radio3},
                    4 : {name:'猜前四',radio : this.radio4},
                    5 : {name:'猜前五',radio : this.radio5},
                    6 : {name:'猜前六',radio : this.radio6},
                    7 : {name:'猜前七',radio : this.radio7},
                    8 : {name:'猜前八',radio : this.radio8},
                    9 : {name:'猜前九',radio : this.radio9},
                    10 : {name:'猜前十',radio : this.radio10},
                    11 : {name:'定位胆',radio : this.radio11},
                    12 : {name:'',radio : this.radio12},
                    13 : {name:'龙虎斗',radio : this.radio13},
                }
            },
            //投注文字显示处理
            text() {
                let playObj = {}
                if (this.type == 12) {
                    playObj = {
                        1 : '前五大小单双',
                        2 : '后五大小单双',
                        3 : '冠亚和大小单双'
                    }
                }else{
                    playObj = {
                        1 : this.type == 13 ? '' : '普通',
                        2 : '精准',
                        3 : '普通'
                    }
                }
                return this.nameObj[this.type].name + playObj[this.nameObj[this.type].radio]
            },
            //顶部玩法显示
            topText(){
                let playObj = {}
                if (this.type == 12) {
                    playObj = {
                        1 : '前五',
                        2 : '后五',
                        3 : '冠亚和'
                    }
                }else{
                    playObj = {
                        1 : '普通',
                        2 : '精准',
                        3 : '粘贴'
                    }
                }
                return playObj[this.nameObj[this.type].radio]
            },
            //投注标识type
            signVal(){
                let playObj = {
                    1 : '',
                    2 : '.2',
                    3 : ''
                }
                return this.sign + playObj[this.nameObj[this.type].radio]
            },
            //玩法下拉高度
            selctHeight(){
                return this.$store.state.clientHeight - 40
            },
        },
        methods:{
            //选择玩法
            chosePlay(name,type,gain,sign){
                this.selected = name
                this.type = type
                this.gain = gain
                this.sign = sign
            },
            //中奖说明
            showPlayInfo(){
                this.showMore = false
                this.$router.push({
                    path:'/playInfo/pk',
                    query:{
                        cz:this.cz,
                        name:this.name,
                        model:this.model
                    }
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
            this.$axios(' /index/pk10/getLotteryInfo/name/' + this.name).then(({data})=>{
                this.$store.commit('setType',data.lottery.expect_type);
                this.$store.commit('setFirstIssue',data.firstIssue);
                this.$set(this,'info',data.info);
                this.$set(this,'betModel',data.mode);
                this.$store.commit('setBetData',data.info);
                this.$set(this,'miss',data.miss);
                this.$set(this,'play',data.play);
                this.$store.commit('setPlayInfo',data.play);
                this.$set(this,'title',data.title);
                this.$store.commit('setRecentOpen',data.ten);
                this.$store.commit('isGetNewCode',data.getnewcode);
                this.$set(this,'small',data.small);
                this.$set(this,'selected',data.play[0].name);
                this.$set(this,'type',data.play[0].type);
                this.$set(this,'gain',data.play[0].gain);
                this.$set(this,'sign',data.play[0].sign);
                this.$store.commit('setPlayBouns',data.bouns)
                this.$store.commit('setAwardNumber',data.info.awardNumber.code);
                this.$store.commit('setRebateInfo',data);
                this.isStop = data.lottery.pause
                this.loading = false
            })
            this.$store.commit('setKeepAlivePage','pk10')
        },
        //返回首页时清除倒计时等
        beforeRouteLeave(to, from, next){
            if(to.path =='/betOrder' || to.path == '/playInfo/pk' || to.path == '/historyCode' || to.path == '/news/detail'|| to.path == '/lotteryRecord'){
                this.$store.commit('setKeepAlivePage','pk10')
            }else {
                this.$store.commit('delKeepAlivePage','pk10')
                this.$store.commit('clearDownTime'); //清除倒计时定时器
                this.$store.commit('clearBetData'); //清除初始数据
                this.$store.commit('clearBetNum'); //清除投注数据
                sessionStorage.removeItem('betinfo');
            }
            this.$store.commit('clearNewCode')  //清除获取开奖号码定时器
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
