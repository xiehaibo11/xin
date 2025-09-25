<template>
    <div>
        <header class="mint-header">
            <div class="mint-header-button is-left"></div>
            <h1 class="mint-header-title">
                <ul class="top-tab cf">
                    <li :class="{'active' : active == 1}" @click="active=1">足球</li>
                    <li :class="{'active' : active == 2}" @click="active=2">篮球</li>
                </ul>
            </h1>
            <div class="mint-header-button is-right">
                <!--<span class="flex-box search-ls">-->
                     <!--<i class="mintui mintui-search"></i>-->
                    <!--<em class="f-sm">联赛</em>-->
                <!--</span>-->
            </div>
        </header>
        <!--<div class="bf-nav" style="margin-top: 2px;">-->
            <!--<ly-tab v-model="selected"-->
                    <!--:items="tabList"-->
                    <!--active-color="green"></ly-tab>-->
        <!--</div>-->
        <div class="filter-cont cf">
            <div class="filter-time fl flex-box">
                <div class="icon-back prev-day" @click="changeDate('prev')" v-if="curr_expect !== expect_list[expect_list.length-1]"><i class="mintui mintui-back"></i></div>
                <div class="icon-back prev-day no-click" v-else><i class="mintui mintui-back"></i></div>
                <div class="today flex tc c-3"><i class="iconfont icon-rili"></i>{{getDay}}</div>
                <div class="icon-back next-day" @click="changeDate('next')" v-if="curr_expect !== expect_list[0]"><i class="mintui mintui-back rotate180"></i></div>
                <div class="icon-back next-day no-click" v-else><i class="mintui mintui-back rotate180"></i></div>
           </div>
            <div class="filter-league-wrap fr">
                <div class="filter-league tc c-3" @click="filterVisible=true"><i class="iconfont icon-shaixuan" :class="{'red' : matchesData.length !== totalMatch && totalMatch !== 0}"></i> 筛选</div>
            </div>
        </div>
        <!--筛选-->
        <mt-popup
            class="filter-popup-box" style="z-index:201"
            v-model="filterVisible"
            position="right">
            <div class="filter-head flex-box">
                <span class="flex f-large c-2" v-if="active == 1">足球</span>
                <span class="flex f-large c-2" v-else>篮球</span>
                <span>
                    共{{matchesData.length}}场比赛,已选{{totalMatch}}场
                </span>
            </div>
            <div class="filter-sel contentH">
                <ul class="cf">
                    <li v-for="(item,index) in filterList" class="sel-item" :class="{'active' : item.select}" @click="handleMatchFilter(index)">
                        {{item.simpleleague}}
                    </li>
                </ul>
            </div>
            <div class="filter-sel-btn cf">
                <span class="btn" @click="filterChose(1)" :class="{'selected':filterSelected == 1}">全选</span>
                <span class="btn" @click="filterChose(2)" :class="{'selected':filterSelected == 2}">反选</span>
                <span class="btn" @click="filterChose(3)" :class="{'selected':filterSelected == 3}">5大联赛</span>
            </div>
            <div class="filter-btn flex-box">
                <div class="btn btn-l flex border-right-1px" @click="filterCancel">取消</div>
                <div class="btn btn-r flex" @click="filterSure">筛好了</div>
            </div>
        </mt-popup>
        <!--筛选 end-->
        <div class="bf-list contentH" :style="{'height':contentH + 'px'}" ref="wrapper">
            <ul>
                <li class="flex-box" v-for="(item,index) in filterData(matchesData)">
                    <template v-if="active == 1"><!--足球（主、客）-->
                        <div class="bf-list-left flex">
                            <div class="game-info-l">
                                {{item.order}}<span class="two_blank"></span>{{item.simpleleague}}
                            </div>
                            <div class="game-detail-l flex-box">
                                <div class="flex">
                                    <div class="game-name"><img :src="item.homelogo" :alt="item.homesxname" width="20">{{item.homesxname}}<em v-if="item.homestanding>0">[{{item.homestanding}}]</em></div>
                                    <div class="game-name"><img :src="item.awaylogo" :alt="item.homesxname" width="20">{{item.awaysxname}}<em v-if="item.awaystanding>0">[{{item.awaystanding}}]</em></div>
                                </div>
                                <!--完场-->
                                <template v-if="item.status == 4">
                                    <div class="bf-info">
                                        <div><em class="f-mini">{{item.homehalfscore}}</em><span class="ten_blank"></span><em class="f-sm c-1">{{item.homescore}}</em></div>
                                        <div><em class="f-mini">{{item.awayhalfscore}}</em><span class="ten_blank"></span><em class="f-sm c-1">{{item.awayscore}}</em></div>
                                    </div>
                                </template>
                                <!--未开始-->
                                <template v-if="item.status == 0">
                                    <div class="bf-info" v-if="status == 1">
                                        <div>{{item.extra_info.homerecord}}</div>
                                        <div>{{item.extra_info.awayrecord}}</div>
                                    </div>
                                    <div class="bf-info-pl" v-if="status == 2">
                                        <div>{{item.extra_info.currodds.split('/')[0]}}</div>
                                        <div>{{item.extra_info.currodds.split('/')[1]}}</div>
                                        <div>{{item.extra_info.currodds.split('/')[2]}}</div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template v-if="active == 2"><!--篮球（客、主）-->
                        <div class="bf-list-left flex">
                            <div class="game-info-l">
                                {{item.order}}<span class="two_blank"></span>{{item.simpleleague}}<span class="two_blank"></span><em v-if="item.status == 4">总分{{item.total}}</em>
                            </div>
                            <div class="game-detail-l flex-box">
                                <div class="flex">
                                    <div class="game-name"><img :src="item.awaylogo" :alt="item.homesxname" width="20">{{item.awaysxname}}<em v-if="item.awaystanding>0">[{{item.awaystanding}}]</em></div>
                                    <div class="game-name"><img :src="item.homelogo" :alt="item.homesxname" width="20">{{item.homesxname}}<em v-if="item.homestanding>0">[{{item.homestanding}}]</em></div>
                                </div>
                                <!--完场-->
                                <template v-if="item.status == 4">
                                    <div class="bf-info">
                                        <div><em class="f-mini">{{item.awayhalfscore}}</em><span class="ten_blank"></span><em class="f-sm c-1">{{item.awayscore}}</em></div>
                                        <div><em class="f-mini">{{item.homehalfscore}}</em><span class="ten_blank"></span><em class="f-sm c-1">{{item.homescore}}</em></div>
                                    </div>
                                </template>
                                <!--未开始-->
                                <template v-if="item.status == 0">
                                    <div class="bf-info" v-if="status == 1">
                                        <div>{{item.extra_info.awayrecord}}</div>
                                        <div>{{item.extra_info.homerecord}}</div>
                                    </div>
                                    <div class="bf-info-pl" v-if="status == 2">
                                        <div>{{item.extra_info.currodds.split('/')[0]}}</div>
                                        <div>{{item.extra_info.currodds.split('/')[1]}}</div>
                                        <div>{{item.extra_info.currodds.split('/')[2]}}</div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <div class="bf-list-rig">
                        <div>{{item.matchtime | timeStr}}</div>
                        <div class="status">{{item.status_desc}}</div>
                    </div>
                </li>
            </ul>
            <!--右下角按钮-->
            <div style="height: 100px"></div>
            <div class="foot-fixed">
                <div class="switch-box bf-btn-box" v-if="isToday">
                    <em class="switch-cont switch-pl" @click="status = 1" v-if="status == 0"><i class="iconfont icon-shuangjiantou2"></i></em>
                    <em class="switch-cont switch-pl" @click="status = 2" v-if="status == 1">状态</em>
                    <em class="switch-cont switch-pl" @click="status = 0" v-if="status == 2">赔率</em>
                </div>
                <div style="height: 40px" v-else></div>
                <div class="refresh-box bf-btn-box" @click="refreshData" style="padding-top: 1px"><em class="iconfont icon-huanyipi" :class="{'rotate360' : refreshing}" style="font-size: 24px;"></em></div>
            </div>
            <!--右下角按钮 end-->
        </div>
    </div>
</template>

<script>
    import BaseModal from 'components/common/BaseModal.vue' //弹窗
    export default {
        name: 'bf',
        components:{
            BaseModal
        },
        data () {
            return {
                active : 1 , // 顶部选中 1 足球 2 篮球
                selected:0,
                tabList:[{label:'竞彩',tab:0},
                    {label:'足彩',tab:1},
                    {label:'有料',tab:2},
                    {label:'全部',tab:3},
                    {label:'指数',tab:4},
                    {label:'热门',tab:5},
                    {label:'单场',tab:6},
                    {label:'中超',tab:7},
                    {label:'关注',tab:8}],
                matchesData:{}, //赛事列表
                curr_expect:'', //选中日期
                expect_list:[],//日期组
                status:0 , //列表显示状态

                filterVisible: false , //筛选选项
                filterArr:[],
                filterList:[],
                tempfilterList:[],
                filterSelected: '',
                refreshing:false
            }
        },
        watch:{
            active(val){
                this.$refs.wrapper.scrollTop = 0
                this.getData();
            }
        },
        computed:{
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - 155
            },
            getDay(){
                let weekDay = ["周日", "周一", "周二", "周三", "周四", "周五", "周六"];
                let myDate = new Date(Date.parse(this.curr_expect.toString().replace(/-/g, "/")));
                let today = this.$bet.formatTime('Y-m-d', new Date().getTime()/1000)
                let day = today == this.curr_expect ? '今日' : this.curr_expect.slice(5,16)
                return day + ' ' + weekDay[myDate.getDay()]
            },
            //判断选择的日期是否为今日
            isToday(){
                let today = this.$bet.formatTime('Y-m-d', new Date().getTime()/1000)
                return today == this.curr_expect ? true : false
            },
            //筛选--赛事总场数
            totalMatch(){
                let num = 0
                for(let i in this.filterList){
                    if(this.filterList[i].select){
                        for(let j in this.matchesData){
                            if(this.matchesData[j].simpleleague == this.filterList[i].simpleleague){
                                num +=1
                            }
                        }
                    }
                }
                return num
            },
        },
        filters:{
            //截止时间截取
            timeStr(val){
                return val.slice(5,16)
            }
        },
        methods:{
            //切换日期
            changeDate(handle){
                for(let i in this.expect_list){
                    if(this.expect_list[i] == this.curr_expect){
                        if(handle == 'prev'){
                            this.curr_expect = this.expect_list[Number(i) + 1]
                            return
                        }
                        if(handle == 'next'){
                            this.curr_expect = this.expect_list[Number(i) - 1]
                            return
                        }
                    }
                }
            },
            //获取数据
            getData(){
                this.$store.commit('setLoadStatus',true)
                let data = {
                    "err": "0",
                    "msg": "OK",
                    "data": {
                        "expect_list": ["2019-02-15", "2019-02-14", "2019-02-13", "2019-02-12"],
                        "curr_expect": "2019-02-14",
                        "servertime": 1549963560,
                        "subscribe_list": [],
                        "matches":
                            [
                                {
                                    "status": "4",
                                    "wid": "129217",
                                    "awayhalfscore": "0",
                                    "away_red_counts": "0",
                                    "extra_statusid": "0",
                                    "spot_kick_score": "0:0",
                                    "awayid": "496",
                                    "status_desc": "完场",
                                    "extra_time_home_score": "0",
                                    "homehalfscore": "0",
                                    "homelogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_797.png",
                                    "home_red_counts": "0",
                                    "extra_time_score": "0:0",
                                    "iscrazybet": "0",
                                    "homesxname": "费内巴",
                                    "homeid": "797",
                                    "isright": "1",
                                    "extra_time_away_score": "0",
                                    "isrecommend": "1",
                                    "extra_info": {"homerecord": "负胜胜负平负", "currodds": "2.15/3.25/3.90", "iscrazybet": "0", "ishasvideo": "1", "isrecommend": "1", "awayrecord": "胜负负负负负"},
                                    "seasonid": "4837",
                                    "zlc": "0",
                                    "matchdate": "2019-02-13",
                                    "league_id": "63",
                                    "awayscore": "1",
                                    "awaystanding": "0",
                                    "awaysxname": "泽尼特",
                                    "extra_exist": "0",
                                    "match_at": "",
                                    "homescore": "0",
                                    "matchtime": "2019-02-13 01:55:00",
                                    "rangqiu": "-1",
                                    "simpleleague": "欧罗巴",
                                    "fid": "768781",
                                    "awaylogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_496.png",
                                    "homestanding": "0",
                                    "spot_kick_home_score": "0",
                                    "matchround": "0",
                                    "order": "周二001",
                                    "spot_kick_away_score": "0"
                                },
                                {"status": "0",
                                    "wid": "129222",
                                    "awayhalfscore": "",
                                    "away_red_counts": "0",
                                    "extra_statusid": "0",
                                    "spot_kick_score": "0:0",
                                    "awayid": "704",
                                    "status_desc": "未开始",
                                    "extra_time_home_score": "0",
                                    "homehalfscore": "",
                                    "homelogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_701.png",
                                    "home_red_counts": "0",
                                    "extra_time_score": "0:0",
                                    "iscrazybet": "0",
                                    "homesxname": "伯明翰",
                                    "homeid": "701",
                                    "isright": "1",
                                    "extra_time_away_score": "0",
                                    "isrecommend": "1",
                                    "extra_info": {"homerecord": "胜胜平负负负", "currodds": "1.50/4.20/8.00", "iscrazybet": "0", "ishasvideo": "0", "isrecommend": "1", "awayrecord": "负负平负负负"},
                                    "seasonid": "4843",
                                    "zlc": "0",
                                    "matchdate": "2019-02-13",
                                    "league_id": "62",
                                    "awayscore": "",
                                    "awaystanding": "23",
                                    "awaysxname": "博尔顿",
                                    "extra_exist": "0",
                                    "match_at": "",
                                    "homescore": "",
                                    "matchtime": "2019-02-13 03:45:00",
                                    "rangqiu": "-1",
                                    "simpleleague": "英冠",
                                    "fid": "735100",
                                    "awaylogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_704.png",
                                    "homestanding": "8",
                                    "spot_kick_home_score": "0",
                                    "matchround": "第32",
                                    "order": "周二002",
                                    "spot_kick_away_score": "0"
                                },
                                {
                                    "status": "0",
                                    "wid": "129223",
                                    "awayhalfscore": "",
                                    "away_red_counts": "0",
                                    "extra_statusid": "0",
                                    "spot_kick_score": "0:0",
                                    "awayid": "1037",
                                    "status_desc": "未开始",
                                    "extra_time_home_score": "0",
                                    "homehalfscore": "",
                                    "homelogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_872.png",
                                    "home_red_counts": "0",
                                    "extra_time_score": "0:0",
                                    "iscrazybet": "0",
                                    "homesxname": "赫尔城",
                                    "homeid": "872",
                                    "isright": "1",
                                    "extra_time_away_score": "0",
                                    "isrecommend": "1",
                                    "extra_info": {"homerecord": "负胜负平胜负", "currodds": "1.75/3.75/5.25", "iscrazybet": "0", "ishasvideo": "0", "isrecommend": "1", "awayrecord": "平平负负负负"},
                                    "seasonid": "4843",
                                    "zlc": "0",
                                    "matchdate": "2019-02-13",
                                    "league_id": "62",
                                    "awayscore": "",
                                    "awaystanding": "21",
                                    "awaysxname": "罗瑟汉",
                                    "extra_exist": "0",
                                    "match_at": "",
                                    "homescore": "",
                                    "matchtime": "2019-02-13 03:45:00",
                                    "rangqiu": "-1",
                                    "simpleleague": "英冠",
                                    "fid": "735101",
                                    "awaylogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_1037.png",
                                    "homestanding": "12",
                                    "spot_kick_home_score": "0",
                                    "matchround": "第32",
                                    "order": "周二003",
                                    "spot_kick_away_score": "0"},
                                {
                                    "status": "0",
                                    "wid": "129224",
                                    "awayhalfscore": "",
                                    "away_red_counts": "0",
                                    "extra_statusid": "0",
                                    "spot_kick_score": "0:0",
                                    "awayid": "1300",
                                    "status_desc": "未开始",
                                    "extra_time_home_score": "0",
                                    "homehalfscore": "",
                                    "homelogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_1097.png",
                                    "home_red_counts": "0",
                                    "extra_time_score": "0:0",
                                    "iscrazybet": "0",
                                    "homesxname": "米尔沃",
                                    "homeid": "1097",
                                    "isright": "1",
                                    "extra_time_away_score": "0",
                                    "isrecommend": "1",
                                    "extra_info": {"homerecord": "负平胜平负胜", "currodds": "2.20/3.30/3.75", "iscrazybet": "0", "ishasvideo": "0", "isrecommend": "1", "awayrecord": "平胜负胜胜负"},
                                    "seasonid": "4843",
                                    "zlc": "0", "matchdate": "2019-02-13", "league_id": "62", "awayscore": "", "awaystanding": "16", "awaysxname": "谢周三", "extra_exist": "0", "match_at": "", "homescore": "", "matchtime": "2019-02-13 03:45:00", "rangqiu": "-1", "simpleleague": "英冠", "fid": "735108", "awaylogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_1300.png", "homestanding": "20", "spot_kick_home_score": "0", "matchround": "第32", "order": "周二004", "spot_kick_away_score": "0"},
                                {
                                    "status": "0", "wid": "129225", "awayhalfscore": "", "away_red_counts": "0", "extra_statusid": "0", "spot_kick_score": "0:0", "awayid": "954", "status_desc": "未开始",
                                    "extra_time_home_score": "0", "homehalfscore": "", "homelogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_728.png", "home_red_counts": "0",
                                    "extra_time_score": "0:0", "iscrazybet": "0", "homesxname": "布里斯", "homeid": "728", "isright": "1", "extra_time_away_score": "0", "isrecommend": "1",
                                    "extra_info": {"homerecord": "胜胜胜胜胜胜", "currodds": "1.85/3.60/4.75", "iscrazybet": "0", "ishasvideo": "0", "isrecommend": "1", "awayrecord": "负胜负平负负"},
                                    "seasonid": "4843", "zlc": "0", "matchdate": "2019-02-13", "league_id": "62", "awayscore": "", "awaystanding": "15", "awaysxname": "巡游者", "extra_exist": "0", "match_at": "",
                                    "homescore": "", "matchtime": "2019-02-13 03:45:00", "rangqiu": "-1", "simpleleague": "英冠", "fid": "735106",
                                    "awaylogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_954.png", "homestanding": "6", "spot_kick_home_score": "0", "matchround": "第32", "order": "周二005",
                                    "spot_kick_away_score": "0"
                                },
                                {
                                    "status": "0", "wid": "129218", "awayhalfscore": "", "away_red_counts": "0", "extra_statusid": "0", "spot_kick_score": "0:0", "awayid": "694", "status_desc": "未开始",
                                    "extra_time_home_score": "0", "homehalfscore": "", "homelogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_1032.png", "home_red_counts": "0",
                                    "extra_time_score": "0:0", "iscrazybet": "0", "homesxname": "罗  马", "homeid": "1032", "isright": "1", "extra_time_away_score": "0", "isrecommend": "1",
                                    "extra_info": {"homerecord": "胜平负平胜胜", "currodds": "2.10/3.50/3.75", "iscrazybet": "0", "ishasvideo": "1", "isrecommend": "1", "awayrecord": "平平胜平胜胜"},
                                    "seasonid": "4838", "zlc": "0", "matchdate": "2019-02-13", "league_id": "101", "awayscore": "", "awaystanding": "0", "awaysxname": "波尔图", "extra_exist": "0", "match_at": "",
                                    "homescore": "", "matchtime": "2019-02-13 04:00:00", "rangqiu": "-1", "simpleleague": "欧冠", "fid": "768757",
                                    "awaylogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_694.png", "homestanding": "0", "spot_kick_home_score": "0", "matchround": "0", "order": "周二006",
                                    "spot_kick_away_score": "0"
                                },
                                {
                                    "status": "0", "wid": "129219", "awayhalfscore": "", "away_red_counts": "0", "extra_statusid": "0", "spot_kick_score": "0:0", "awayid": "647", "status_desc": "未开始",
                                    "extra_time_home_score": "0", "homehalfscore": "", "homelogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_1075.png", "home_red_counts": "0",
                                    "extra_time_score": "0:0", "iscrazybet": "0", "homesxname": "曼  联", "homeid": "1075", "isright": "1", "extra_time_away_score": "0", "isrecommend": "1",
                                    "extra_info": {"homerecord": "胜胜平胜胜胜", "currodds": "2.40/3.50/3.10", "iscrazybet": "0", "ishasvideo": "1", "isrecommend": "1", "awayrecord": "胜平负胜胜胜"},
                                    "seasonid": "4838", "zlc": "0", "matchdate": "2019-02-13", "league_id": "101", "awayscore": "", "awaystanding": "0", "awaysxname": "日尔曼", "extra_exist": "0", "match_at": "",
                                    "homescore": "", "matchtime": "2019-02-13 04:00:00", "rangqiu": "-1", "simpleleague": "欧冠", "fid": "768754",
                                    "awaylogo": "https://odds.500.com/static/soccerdata/images/TeamPic/teamsignnew_647.png", "homestanding": "0", "spot_kick_home_score": "0", "matchround": "0", "order": "周二007",
                                    "spot_kick_away_score": "0"
                                }
                            ]
                    }
                }
                this.$set(this,'matchesData',data.data.matches)
                this.$set(this,'curr_expect',data.data.curr_expect)
                this.$set(this,'expect_list',data.data.expect_list)
                //初始化赛事筛选数据数组
                let filterArr = []
                for(let m in data.data.matches){
                    let simpleleague=  data.data.matches[m].simpleleague
                    if(!this.$base.isExit(filterArr,simpleleague)){
                        filterArr.push(simpleleague)
                    }
                }
                this.$set(this,'filterArr',filterArr)

                let resArr = []
                for(let i in filterArr){
                    let list = {}
                    list['select'] = false //添加选择状态
                    list['simpleleague'] = filterArr[i] //添加比赛名称
                    resArr.push(list)
                }
                this.$set(this,'filterList',resArr)
                this.$set(this,'tempfilterList',JSON.parse(JSON.stringify(resArr))) //临时数组，用于筛选取消后恢复

                setTimeout(()=>{
                    this.$store.commit('setLoadStatus',false)
                },200)
            },
            //<!--赛事筛选****************-->
            //赛事列表过滤
            filterData(list){
                return list.filter((item)=>{
                    return this.filterArr.indexOf(item.simpleleague) > -1
                })
            },
            //选择
            handleMatchFilter(index){
                this.filterList[index].select = !this.filterList[index].select
                this.filterSelected = ''
            },
            //取消筛选
            filterCancel(){
                this.$set(this,'filterList',JSON.parse(JSON.stringify(this.tempfilterList))) //还原筛选选项
                this.filterVisible = false
            },
            //确认筛选
            filterSure(){
                if(this.totalMatch < 1){
                    this.$toast('筛选结果为0条数据，请重新设定筛选条件')
                    return
                }
                //更新筛选数组
                this.filterArr = []
                for(let i in this.filterList){
                    if(this.filterList[i].select){
                        this.filterArr.push(this.filterList[i].simpleleague)
                    }
                }
                //更新临时列表
                this.$set(this,'tempfilterList',JSON.parse(JSON.stringify(this.filterList)))
                this.filterVisible = false
            },
            //快捷筛选
            filterChose(val){
                this.filterSelected = val
                if(val == 1){ //全选
                    for(let i in this.filterList){
                        this.filterList[i].select = true
                    }
                }
                if(val == 2){ //反选
                    for(let i in this.filterList){
                        this.filterList[i].select = !this.filterList[i].select
                    }
                }
                if(val == 3){ //五大联赛
                    for(let i in this.filterList){
                        if(this.filterList[i].simpleleague == '英超' || this.filterList[i].simpleleague == '意甲' || this.filterList[i].simpleleague == '德甲' ||
                            this.filterList[i].simpleleague == '西甲' || this.filterList[i].simpleleague == '法甲'){
                            this.filterList[i].select = true
                        }else {
                            this.filterList[i].select = false
                        }
                    }
                }
            },
            //<!--赛事筛选 end****************-->
            //刷新数据
            refreshData(){
                if(!this.refreshing){
                    this.refreshing = true
                    this.getData();
                    setTimeout(()=>{
                        this.refreshing = false
                    },1000)
                }
            },
        },
        created(){
           this.getData();
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    span.two_blank{
        margin-right: 5px;
    }
    span.ten_blank{
        margin-right: 25px;
    }
    .bf-list{
        ul>li{
            padding: 10px;
            border-bottom: 0.5px solid #eaeaea;
            zoom: 1;
            background-color: #ffffff;
            font-size: 11px;
            color: #aab5bd;
            align-items: flex-start;
        }
    }
    .bf-list-left{
        margin-right: 15px;
        .game-detail-l{
            margin-top: 5px;
            border-right:0.5px solid #eeeeee;
            padding-right: 12px;
            line-height: 2;
            .game-name{
                font-size: 14px;
                color: #333333;
                img{
                    margin-right: 5px;
                }
                em{
                    font-size: 11px;
                    color: #aab5bd;
                    margin-left: 5px;
                }
            }
            .bf-info{
                line-height: 28px;
            }
            .bf-info-pl{
                line-height: 1.7;
            }
        }
    }
    .bf-list-rig{
        .status{
            font-size: 13px;
            line-height: 56px;
            text-align: center;
        }
    }
    .foot-fixed{
        position: fixed;
        width: 40px;
        height: 90px;
        right: 10px;
        bottom: 65px;
        .bf-btn-box{
            height: 40px;
            text-align: center;
            @include rounded(50%);
            background-color: #d55c45;
            color: #ffffff;
            line-height: 40px;
            font-size: 12px;
        }
        .refresh-box{
            margin-top: 10px;
            position: relative;
            em{
                width: 40px;
                height: 40px;
                display: inline-block;
                position: absolute;
                right: 0;
                top: 0px;
            }
            .rotate360{
               @include dz();
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    }
    //日期、赛事筛选
    .filter-cont{
        background-color: #ffffff;
        padding: 15px 10px 5px 10px;
        .filter-time,.filter-league-wrap{
            height: 40px;
            line-height: 40px;
            border: 1px solid #eaeaea;
            @include shadow(0,0,5px, rgba(22, 34, 29, 0.12));
            @include rounded(3px);
            font-size: 14px;
            color: #666666;
        }
        .filter-time{
            width: 65%;
        }
        .filter-league-wrap{
            width: 32%;
        }
        .icon-back{
            width: 30px;
            height: 40px;
            text-align: center;
            i{
                font-size: 13px;
                color: #b7b7b7;
            }
        }
        .next-day{
            position: relative;
            i.rotate180{
                -webkit-animation: all .2s linear;
                animation: all .2s linear;
                -webkit-transform: rotate(180deg);
                -ms-transform: rotate(180deg);
                transform: rotate(180deg);
                position: absolute;
                right: 50%;
                top: 0px;
                margin-right: -8px;
            }
        }
        .no-click{
            i{
                color: #ececec;
            }
        }
        .today{
            i{
                margin-right: 10px;
            }
        }
    }
    .filter-popup-box{
        padding: 15px 0 0;
        width: 90%;
        right: 5%;
        @include rounded(6px);
        .filter-head{
            padding: 0 15px;
            font-size: 13px;
            color: #666666;
        }
        .filter-sel{
            padding: 15px 5px;
            height: 215px;
            ul > li{
                width: 28%;
                height: 30px;
                line-height: 30px;
                text-align: center;
                margin:4px 2.5%;
                float: left;
                font-size: 14px;
                @include rounded(4px);
                background: #ebf1f5;
                color: #242c35;
                &.active{
                    background: #5c788f;
                    color: #fff;
                }
            }
        }
        .filter-sel-btn{
            padding: 10px 10px 14px 10px;
            font-size: 12px;
            span.btn{
                padding: 2px 8px;
                border: 1px solid #eaeaea;
                @include rounded(13px);
                color: #aab5bd;
                float: left;
                margin-right: 7px;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }
            span.selected{
                background: #ebf1f5;
                color: #242c35;
            }
        }
        .filter-btn{
            border-top:1px solid #ececec;
            .btn{
                height: 45px;
                line-height: 45px;
                text-align: center;
                font-size: 14px;
            }
        }
    }
</style>
