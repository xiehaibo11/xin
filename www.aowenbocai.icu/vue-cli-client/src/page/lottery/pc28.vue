<template>
    <div class="lottery-bg">
        <div class="lottery-loading" v-if="loading">
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
                    <span class="chose" @click="show = !show">{{selected}} <i class="iconfont icon-jiantou yellow" :class="show ? 'is-active':'no-active'"></i></span>
                </div>
                <transition name="fade">
                    <div class="layout" @click.self="showMore = false" v-show="showMore">
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
                <div class="layout z-2 contentH top-1-select" @click.self="show = false" v-show="show" :style="{height:selctHeight + 'px'}">
                    <div class="select-grounp">
                        <ul class="clearfloat">
                            <template v-for="(item,index) in play">
                                <li @click="chosePlay(item.name,item.type,item.gain)">
                                    <a :class="{'active':type == item.type}">{{item.name}}</a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
            <!--顶部 end-->
            <!--倒计时+ 期号-->
            <DownTime/>
            <!--投注操作-->
            <div class="bet-cont">
                <!--混合-->
                <div class="bet-item" v-show="type == 1">
                    <pc-bet :type="type" :gain="play[0].gain" :title="title" :text="text" :name="name"
                            @change-gain="getHhGain" :max-gain="maxGain" :bet-model="betModel"></pc-bet>
                </div>
                <!--色波-->
                <div class="bet-item" v-show="type == 2">
                    <pc-bet :type="type" :gain="play[1].gain" :title="title" :text="text" :name="name"
                            @change-gain="getHhGain" :max-gain="maxGain" :bet-model="betModel"></pc-bet>
                </div>
                <!--豹子-->
                <div class="bet-item" v-show="type == 3">
                    <pc-bet :type="type" :gain="maxGain" :title="title" :text="text" :name="name"
                            @change-gain="getHhGain" :max-gain="maxGain" :bet-model="betModel"></pc-bet>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DownTime from 'components/lottery/DownTime.vue' //顶部期号+ 倒计时组件
    import PcBet from 'components/lottery/pc28/PcBet.vue' //投注
    import StopSaleTips from 'components/lottery/StopSaleTips.vue' //暂停销售
    export default {
        name:'pc28',
        data () {
            return {
                show:false, //选择玩法
                showMore:false, //右侧列表
                selected:'',//选中的玩法名
                gain:'',//选中的奖金设置值
                type:'',//选中的玩法type值
                title:'',
                isStop:false,
                betModel:1 ,

                loading: true,
                play:[], //玩法配置内容
                info:{}, //初始数据

                hhGain:'',
                sbGain:''
            }
        },
        components:{
            DownTime,
            PcBet,
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
            maxRebate() {
                return this.$store.getters.maxRebate
            },
            //返点后最高奖金
            maxGain () {
                var arr = []
                if(this.type == 1){
                    arr = this.hhGain ? this.hhGain.split(",") : this.gain.split(",")
                }else if(this.type == 2){
                    console.log(this.sbGain)
                    arr = this.sbGain ? this.sbGain.split(",") : this.gain.split(",")
                }else {
                    arr = this.gain.split(",")
                }
                var a = this.betModel == 1 ? 1 : 2; //模式2下奖金减半
                var s = this.$bet.getMaxMin(arr,'max') //<!--混合投注type == 1时取最大值-->
                var res = this.rebateIsOpen ? this.$bet.accDiv(this.$bet.accDiv(this.$bet.accMul(Number(s),Number(this.maxRebate)),Number(this.scale)),a,5) : this.$bet.accDiv(s,a,5) //保留4位小数
                return res
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
            //投注文字显示处理
            text() {
                let typeObj = {
                    1 : '混合',
                    2 : '色波',
                    3 : '豹子'
                }
                return typeObj[this.type]
            },
            //玩法下拉高度
            selctHeight(){
                return this.$store.state.clientHeight - 40
            }
        },
        methods:{
            //混合奖金字符串
            getHhGain (emitVal) {
                if(this.type == 1){
                    this.hhGain = emitVal
                }
                if(this.type == 2){
                    this.sbGain = emitVal
                }
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
            this.$axios('/index/Pc28/getLotteryInfo/name/' + this.name).then(({data})=>{
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
            this.$store.commit('setKeepAlivePage','pc28')
        },
        //返回首页时清除倒计时等
        beforeRouteLeave(to, from, next){
            if(to.path =='/betOrder' || to.path == '/playInfo' || to.path == '/historyCode' || to.path == '/news/detail'|| to.path == '/lottery/trade'|| to.path == '/lotteryRecord'){
                this.$store.commit('setKeepAlivePage','pc28')
            }else {
                this.$store.commit('delKeepAlivePage','pc28')
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

</style>
