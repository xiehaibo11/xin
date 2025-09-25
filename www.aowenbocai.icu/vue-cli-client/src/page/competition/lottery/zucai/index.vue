<template>
    <div>
        <header class="mint-header">
            <div class="mint-header-button is-left"  @click="goBack">
                <button class="mint-button mint-button--default mint-button--normal">
                    <span class="mint-button-icon"><i class="mintui mintui-back"></i></span>
                </button>
            </div>
            <h1 class="mint-header-title">
                <ul class="top-tab cf">
                    <router-link tag="li" to="/zucai?name=sfc" :class="{'active' : selected == 1}">胜负彩</router-link>
                    <router-link tag="li" to="/zucai?name=rx9" :class="{'active' : selected == 2}">任选9</router-link>
                </ul>
            </h1>
            <div class="mint-header-button is-right">
                <!--<button class="mint-button mint-button&#45;&#45;default mint-button&#45;&#45;normal">-->
                    <!--<span class="mint-button-icon"><i class="mintui mintui-more"></i></span>-->
                <!--</button>-->
            </div>
        </header>
        <div class="zucai-header head flex-box f-sm border-bottom-1px">
            <div class="date" @click="show = !show">{{cur==0 ? '当前' : '预售'}}第{{curExpect}}期 <i class="iconfont icon-xialajiantou rotateIcon" :class="show ? 'is-active' : 'no-active'"></i></div>
            <div class="end-time flex tr f-mini c-2">{{curTime}}截止</div>
            <!--期号列表-->
            <div class="layout z-2 zucai-expect contentH top-1-select" @click.self="show = false" v-show="show" :style="{height:selctHeight + 'px'}">
                <div class="select-grounp">
                    <ul>
                        <template v-for="(item,index) in expectInfo">
                            <li :class="{'active':cur == index}" class="f-mini" @click="changeExpect(index,item.expect,item.fsendtime)">
                                {{index==0 ? '当前' : '预售'}}第{{item.expect}}期 <i class="iconfont icon-gou-copy" v-if="cur == index"></i>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
            <!--期号列表 end-->
        </div>
        <div class="zucai-cont jc-bet-cont contentH" :style="{height:contentH + 'px'}" ref="wrapper">
            <div class="border-bottom-1px zucai-list" v-for="(match,index) in matchInfo">
                <div class="flex-box jc-item">
                    <div class="jc-item-left" @click="infoSlide(match.mdata.fid)" style="padding-top: 0;line-height: 1.2">
                        <p class="f-sm">{{match.mdata.simpleleague}}</p>
                        <p>{{index + 1}}</p>
                        <p>{{match.mdata.resultscore}}</p>
                        <p class="c-3">分析<i class="iconfont icon-xialajiantou rotateIcon" :class="infoState(match.mdata.fid) ? 'is-active' : 'no-active'"></i></p>
                    </div>
                    <div class="jc-item-right flex">
                        <div class="cf zucai-bet-item">
                            <div class="fl bet-win item" :class="{'active' : curOpt(match.mdata.fid,'3')}" @click="selectDone(match.mdata.fid,'3',match.pdata.win)">
                                <p><i class="c-4 f-mini" v-if="Number(match.mdata.homestanding)>0">[{{match.mdata.homestanding}}]</i>{{match.mdata.homename}}</p>
                                <p><em class="pl">主胜{{match.pdata.win}}</em></p>
                            </div>
                            <div class="fl bet-draw item" :class="{'active' : curOpt(match.mdata.fid,'0')}" @click="selectDone(match.mdata.fid,'0',match.pdata.draw)">
                                <p>vs</p>
                                <p><em class="pl">平{{match.pdata.draw}}</em></p>
                            </div>
                            <div class="fl bet-lost item" :class="{'active' : curOpt(match.mdata.fid,'1')}" @click="selectDone(match.mdata.fid,'1',match.pdata.lost)">
                                <p>{{match.mdata.awayname}}<i class="c-4 f-mini" v-if="Number(match.mdata.awaystanding)>0">[{{match.mdata.awaystanding}}]</i></p>
                                <p><em class="pl">客胜{{match.pdata.lost}}</em></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--分析-->
                <div class="fxinfo-box"  :class="infoState(match.mdata.fid) ? 'fxinfo-box-on' : ''">
                    <div class="jc-info-arrow"><i class="ico-arrow2"></i></div>
                    <div class="jc-item-fxinfo">
                        <ul>
                            <li>历史交锋：{{match.cdata.count_against || '-'}}</li>
                            <li>近战战绩：<span>主队 {{match.cdata.count_home || '-'}}</span>; <span>客队 {{match.cdata.count_away ||'-'}}</span></li>
                            <li>平均赔率：<span class="mr-sm">主胜{{match.cdata.win}}</span><span class="mr-sm">平{{match.cdata.draw}}</span><span class="mr-sm">主负{{match.cdata.lost}}</span></li>
                        </ul>
                        <div class="more tc">
                            <a>
                                详细赛事分析 >>
                            </a>
                        </div>
                    </div>
                </div>
                <!--分析 end-->
            </div>
        </div>
        <!--底部选号区-->
        <div class="jc-bet-foot flex-box border-top-1px">
            <div class="bet-del tc border-right-1px" @click="clear">
                <p><i class="iconfont icon-shanchu btn-icon-clear"></i></p>
                <p class="f-mini">清空</p>
            </div>
            <div class="bet-plan flex tc">
                <p>已选择<em class="red"> {{games}} </em>场</p>
                <p class="f-mini">至少选择{{name == 'sfc' ? 14 : 9}}场比赛</p>
            </div>
            <button class="bet-btn btn-sure" @click="submitOrder">选好了</button>
        </div>
        <!--底部选号区 end-->
    </div>
</template>

<script>
    export default {
        name: 'zucai',
        data () {
            return {
                show: false,

                matchInfo:[], //列表
                expectInfo:[], //期号组
                cur : 0,
                curExpect:'',
                curTime:'',

                infoArr:[],

                plan: this.$store.state.zucai.plan || {}, //投注方案内容
            }
        },
        computed:{
            name(){
                return this.$route.query.name
            },
            selected(){
                return this.name == 'sfc' ? 1 : 2
            },
            contentH(){
                return this.$store.state.clientHeight - 122
            },
            //玩法下拉高度
            selctHeight(){
                return this.$store.state.clientHeight - 70
            },

            //当前选中的显示状态
            curOpt(){
                return (id,sign)=>{
                    return this.checkStatus(this.plan,id,sign)
                }
            },
            //已选场数
            games(){
                return this.$_.size(this.plan)
            },
        },
        methods:{
            //返回
            goBack(){
                if(this.games > 0){
                    this.$messagebox.confirm(
                        '返回大厅将清空所有已选的号码'
                    ).then(()=>{
                        this.$store.commit('delKeepAlivePage','zucai') //清除投注页面缓存
                        this.$store.commit('delKeepAlivePage','zucaiOrder') //清除订单页面缓存
                        this.$store.commit('clearZucaiBetData')
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
            getData(){
                this.$store.commit('setLoadStatus',true)
                let data = {
                    "err":"0",
                    "msg":"OK",
                    "expectInfo": [{"expect":"19022","fsendtime" : "2019-02-19 22:57"},
                        {"expect":"19023","fsendtime" : "2019-02-22 22:57"},
                        {"expect":"19024","fsendtime" : "2019-02-23 19:57"}
                        ],
                    "data":[
                    {"mdata":{"resultscore":"02-23 23:00","hometeamid":"667","fsendtime":"2019-02-23 21:27","ordernum":"1","expect":["19022","19023","19024"],"homename":"伯恩茅",
                        "guestteamid":"978","current_expect":"19024","isvalid":"1","awaystanding":"11","awayname":"狼队","bgcolor":"#FF1717","simpleleague":"英超","fid":"731167",
                        "homestanding":"12"},"pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},
                        "cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"3胜1平6负","win":"-","count_away":"6胜2平2负","count_against":"3|2|0|1"}
                     },
                    {"mdata":{"resultscore":"02-23 23:00","hometeamid":"973","fsendtime":"2019-02-23 21:27","ordernum":"2",
                        "expect":["19022","19023","19024"],"homename":"莱切城",
                        "guestteamid":"516","current_expect":"19024","isvalid":"1","awaystanding":"14","awayname":"水晶宫","bgcolor":"#FF1717","simpleleague":"英超","fid":"731172",
                        "homestanding":"8"},"pdata":{"win":"2.30","draw":"3.82","lost":"5.23"},
                        "cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"3胜1平6负","win":"-","count_away":"5胜2平3负","count_against":"10|3|2|5"}},
                    {"mdata":{"resultscore":"02-23 23:00","hometeamid":"1137","fsendtime":"2019-02-23 21:27","ordernum":"3","expect":["19022","19023","19024"],"homename":"纽卡斯",
                        "guestteamid":"847","current_expect":"19024","isvalid":"1","awaystanding":"20","awayname":"哈德斯","bgcolor":"#FF1717","simpleleague":"英超","fid":"731174",
                        "homestanding":"18"},"pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},
                        "cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"2胜4平4负","win":"-","count_away":"0胜1平9负","count_against":"8|5|1|2"}},
                    {"mdata":{"resultscore":"02-24 01:30","hometeamid":"565","fsendtime":"2019-02-23 21:27","ordernum":"4","expect":["19022","19023","19024"],"homename":"埃弗顿",
                        "guestteamid":"1072","current_expect":"19024","isvalid":"1","awaystanding":"2","awayname":"曼城","bgcolor":"#FF1717","simpleleague":"英超","fid":"731171",
                        "homestanding":"10"},
                        "pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"4胜0平6负","win":"-","count_away":"9胜0平1负",
                        "count_against":"10|2|4|4"}},
                    {"mdata":{"resultscore":"02-23 22:30","hometeamid":"1088","fsendtime":"2019-02-23 21:27","ordernum":"5","expect":["19022","19023","19024"],"homename":"美因茨",
                        "guestteamid":"478","current_expect":"19024","isvalid":"1","awaystanding":"13","awayname":"沙尔克","bgcolor":"#990099","simpleleague":"德甲","fid":"737755",
                        "homestanding":"12"},"pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"3胜3平4负","win":"-",
                        "count_away":"3胜4平3负","count_against":"10|2|1|7"}},
                    {"mdata":{"resultscore":"02-23 22:30","hometeamid":"1089","fsendtime":"2019-02-23 21:27","ordernum":"6","expect":["19022","19023","19024"],"homename":"门兴",
                        "guestteamid":"1269","current_expect":"19024","isvalid":"1","awaystanding":"5","awayname":"沃尔夫","bgcolor":"#990099","simpleleague":"德甲","fid":"737749",
                        "homestanding":"3"},"pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"5胜1平4负","win":"-","count_away":"6胜1平3负",
                        "count_against":"10|3|2|5"}},
                    {"mdata":{"resultscore":"02-23 22:30","hometeamid":"664","fsendtime":"2019-02-23 21:27","ordernum":"7","expect":["19022","19023","19024"],"homename":"拜仁",
                        "guestteamid":"659","current_expect":"19024","isvalid":"1","awaystanding":"8","awayname":"赫塔","bgcolor":"#990099","simpleleague":"德甲","fid":"737750",
                        "homestanding":"2"},"pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"7胜2平1负","win":"-","count_away":"3胜4平3负",
                        "count_against":"10|5|4|1"}},
                    {"mdata":{"resultscore":"02-23 22:30","hometeamid":"804","fsendtime":"2019-02-23 21:27","ordernum":"8","expect":["19022","19023","19024"],"homename":"弗赖堡",
                        "guestteamid":"1923","current_expect":"19024","isvalid":"1","awaystanding":"15","awayname":"奥格斯","bgcolor":"#990099","simpleleague":"德甲","fid":"737753",
                        "homestanding":"11"},"pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"2胜5平3负","win":"-","count_away":"3胜2平5负",
                        "count_against":"10|3|2|5"}},
                    {"mdata":{"resultscore":"02-23 22:30","hometeamid":"781","fsendtime":"2019-02-23 21:27","ordernum":"9","expect":["19022","19023","19024"],"homename":"杜塞尔",
                        "guestteamid":"1138","current_expect":"19024","isvalid":"1","awaystanding":"18","awayname":"纽伦堡","bgcolor":"#990099","simpleleague":"德甲","fid":"737752","homestanding":"14"},
                        "pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"4胜2平4负","win":"-","count_away":"0胜2平8负","count_against":"10|3|1|6"}},
                    {"mdata":{"resultscore":"02-23 22:00","hometeamid":"776","fsendtime":"2019-02-23 21:27","ordernum":"10","expect":["19022","19023","19024"],"homename":"都灵","guestteamid":"1308",
                        "current_expect":"19024","isvalid":"1","awaystanding":"8","awayname":"亚特兰","bgcolor":"#0066FF","simpleleague":"意甲","fid":"750033","homestanding":"9"},
                        "pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"3胜4平3负","win":"-","count_away":"6胜2平2负","count_against":"10|4|4|2"}},
                    {"mdata":{"resultscore":"02-24 01:00","hometeamid":"707","fsendtime":"2019-02-23 21:27","ordernum":"11","expect":["19022","19023","19024"],"homename":"博洛尼","guestteamid":"1330",
                        "current_expect":"19024","isvalid":"1","awaystanding":"1","awayname":"尤文","bgcolor":"#0066FF","simpleleague":"意甲","fid":"750036","homestanding":"18"},
                        "pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"1胜4平5负","win":"-","count_away":"7胜2平1负","count_against":"10|0|1|9"}},
                    {"mdata":{"resultscore":"02-24 03:30","hometeamid":"2000","fsendtime":"2019-02-23 21:27","ordernum":"12","expect":["19022","19023","19024"],"homename":"弗洛西","guestteamid":"1032",
                        "current_expect":"19024","isvalid":"1","awaystanding":"6","awayname":"罗马","bgcolor":"#0066FF","simpleleague":"意甲","fid":"750035","homestanding":"19"},
                        "pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"3胜2平5负","win":"-","count_away":"6胜2平2负","count_against":"4|0|0|4"}},
                    {"mdata":{"resultscore":"02-23 22:00","hometeamid":"647","fsendtime":"2019-02-23 21:27","ordernum":"13","expect":["19022","19023","19024"],"homename":"日尔曼","guestteamid":"1132",
                        "current_expect":"19024","isvalid":"1","awaystanding":"11","awayname":"尼姆","bgcolor":"#663333","simpleleague":"法甲","fid":"729458","homestanding":"1"},
                        "pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"7胜1平2负","win":"-","count_away":"4胜1平5负","count_against":"2|2|0|0"}},
                    {"mdata":{"resultscore":"02-23 22:00","hometeamid":"1194","fsendtime":"2019-02-23 21:27","ordernum":"14","expect":["19022","19023","19024"],"homename":"斯特堡","guestteamid":"998",
                        "current_expect":"19024","isvalid":"1","awaystanding":"2","awayname":"里尔","bgcolor":"#663333","simpleleague":"法甲","fid":"729460","homestanding":"7"},
                        "pdata":{"win":"2.29","draw":"3.48","lost":"2.75"},"cdata":{"draw":"-","lost":"-","count_tzbl":"","count_home":"5胜2平3负","win":"-","count_away":"7胜1平2负","count_against":"10|4|2|4"}}
                        ]
                }
                this.$set(this,'matchInfo',data.data)
                this.$set(this,'expectInfo',data.expectInfo)
                this.$set(this,'curExpect',data.expectInfo[0].expect)
                this.$set(this,'curTime',data.expectInfo[0].fsendtime)
                this.$store.commit('setLoadStatus',false)
            },
            //切换期号
            changeExpect(i,expect,endtime){
                this.cur = i
                this.curExpect = expect
                this.curTime = endtime
                this.show = false
                this.$refs.wrapper.scrollTop = 0
            },
            //赛事分析展开or收起状态
            infoState(val){
                return this.infoArr.indexOf(val) > - 1 ? true : false
            },
            //分析展开or收起
            infoSlide(val){
                let a = this.infoArr.indexOf(val)
                if(a > -1){
                    this.infoArr.splice(a,1)
                }else {
                    this.infoArr.push(val)
                }
            },
            //检测选中状态
            checkStatus(obj,id,sign) {
                let res = false
                if(obj.hasOwnProperty(id)){
                    if(obj[id].hasOwnProperty(sign)){
                        res = true
                    }else {
                        res = false
                    }
                }else {
                    res = false
                }
                return res
            },
            //选择方案增减
            addPlan(obj,id,sign,pl){
                if(obj.hasOwnProperty(id)){
                    if(obj[id].hasOwnProperty(sign)){
                        this.$delete(obj[id],sign)
                        if(JSON.stringify(obj[id]) == '{}'){
                            this.$delete(obj,[id])
                        }
                    }else {
                        this.$set(obj[id],[sign], pl)
                    }
                }else{
                    this.$set(obj,[id], {})
                    this.$set(obj[id],[sign], pl)
                }
            },
            //方案选择(场次id,sign选号标识3主胜、1平、0主负,pl赔率)
            selectDone(id,sign,pl){
                this.addPlan(this.plan,id,sign,pl);
                console.log(JSON.stringify(this.plan))
            },
            //清空
            clear(){
                this.$set(this,'plan',{})
                this.$store.commit('clearZucaiBetData')
            },
            //提交
            submitOrder(){
                let needGames = this.name == 'sfc' ? 14 : 9
                if(this.games < needGames){
                    this.$messagebox('提示','至少选择' + needGames + '场比赛')
                    return
                }
                let tempMatchData = {}
                //提取已选赛事相关数据
                for(let i in this.matchInfo){
                    if(this.plan.hasOwnProperty(this.matchInfo[i].mdata.fid)){
                        tempMatchData[this.matchInfo[i].mdata.fid] = this.matchInfo[i]
                    }
                }
                this.$store.commit('setZucaiPlan',this.plan)
                this.$store.commit('setZucaiTempMatchData',tempMatchData)
                this.$router.push({
                    path : '/zucai/bet',
                    query:{
                        name : this.name,
                        expect : this.curExpect,
                        endtime : this.curTime
                    }
                })
            }
        },
        created(){
            this.getData();
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    $t-height: 30px;
    .head .zucai-expect{
        top:$t-height;
        .select-grounp{
            width: 140px;
            margin-top: 1px;
            ul{
                padding: 0 10px;
                li{
                    float: none;
                    width: 100%;
                    height: 32px;
                    line-height: 32px;
                    border-bottom: 1px solid #eeeeee;
                    text-align: left;
                    padding-left: 5px;
                    &.active{
                        color: $bColor;
                    }
                    i{
                        font-size: 14px;
                        margin-left: 5px;
                    }
                }
            }
        }
    }
    .zucai-header{
        height: $t-height;
        line-height: $t-height;
        padding: 0 10px;
        background-color: #ffffff;
    }
</style>
