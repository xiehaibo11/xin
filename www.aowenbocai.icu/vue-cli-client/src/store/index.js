import Vue from 'vue';
import Vuex from 'vuex';
import actions from './actions.js';
import mutations from './mutations.js';
import getters from './getters.js';
Vue.use(Vuex);
import base from 'assets/js/base.js'  //公用js

let now = new Date().getTime();
export default new Vuex.Store({
    state: {
        clientHeight:0,
        clientWidth:0,

        transitionName:'fade', //页面动画
        isformRules:true, //控制刷新页面时默认动画为‘fade’

        // bAuth :base.getCookie('bAuth') || false,//是否登录
        bAuth :JSON.parse(localStorage.getItem("loginToken")) || base.getCookie('bAuth') ? true : false,//是否登录
        splash:base.getCookie('splash') || false,//是否显示启动画面
        needKeepAlive:['game','discover','kaijiang','join','user'], //需要被缓存的页面

        setting:JSON.parse(localStorage.getItem("setting")) || {
            login_way:{common:1,tel:0,qq:0}
        },//系统设置信息
        selected:'大厅',//路由title
        userinfo:{
            rebate:{},
            user_grade:{}
        },//用户信息
        grade:[],//用户等级信息
        lockInfo:[],//用户绑定信息
        trueInfo:'',//实名制信息
        trueState:'',//实名制认证状态
        loadStatus: false,
        loadTime: 0,

        gameList: JSON.parse(localStorage.getItem("gameList")) || [], //休闲游戏列表
        lotteryList:JSON.parse(localStorage.getItem("lotteryList")) ||[],//彩票游戏列表
        lotteryShow:JSON.parse(localStorage.getItem("navList")) || {},  //彩票分组列表
        newNavClassId:JSON.parse(localStorage.getItem("news_nav_class_id")) || {},  //新闻栏目id
        newsPrize : [], //中奖快讯
        banner:[],//首页banner图片

        kaijiang:[],//开奖公告列表
        historyIsLink:true, //底部是否显示去投注
        lotteryNav:JSON.parse(localStorage.getItem("lotteryNav")) || [],//彩种导航列表
        gameNav:JSON.parse(localStorage.getItem("gameNav")) || [],//游戏导航列表

        //个人中心
        bag:[],//游戏道具
        exchangeList:[], //兑换商品列表
        systemMsg:[],//系统消息

        static:'',//消费统计数据

        extList:[], //最近玩过的游戏列表

        lockBank:{ //绑定账号信息
            banks:[], //银行卡
            alipay:[], //支付宝账号
            wei:[], //微信账号
        },

        page:{
            loadState:true, //触发加载更多状态
            loadState2:true //触发加载更多状态2
        },

        intervalTime:'', //倒计时
        newCodeFun:'', //获取开奖号码
        rNumTimer:'', //开奖动画
        lottery:{ //投注相关
            info:{//页面初始数据
                name: '',
                time : '0' , //剩余时间
                expect : '--', //当前期号
                sort_expect:'--', //处理后的期号
                lastIssue:'--', //上一期期号
                todayTime:'', //日期
                totalIssue:'',//每天总期数
                timelong:'',//几分钟一期
                awardNumber:'',// 最新开奖
                firstIssue:'2019020101'//当天第一期
            },
            miss:{}, //遗漏
            small:{},//小玩法
            play:[], //玩法配置信息
            isGetCode : false , //是否获取开奖号码
            recent_open:[],//近期开奖
            betArr: JSON.parse(sessionStorage.getItem("betinfo")) || [], //投注内容

            pk10Bouns:JSON.parse(localStorage.getItem("pk10Bouns")) || {},//pk10玩法介绍

            label:'元', //单位
            value:1, //单位值
            multiple:1,//倍数

            sliderValue:0, //range值
            upUserRebate:0, //上级
            userRebate:0, //自己
            bonus_base:0, //网站
            expect_type:0,//期号类型
        },

        joinList:JSON.parse(localStorage.getItem("joinList")) || [],//合买列表数据
        pageYOffset: 0, //记录页面滚动条位置
        pageYOffset2: 0, //2次记录滚动条位置
        updateIndex: -1, //记录合买详情页所修改的index

        rechargeInfo:JSON.parse(localStorage.getItem("paySetting")) || [], //充值配置信息

        signInfo:JSON.parse(sessionStorage.getItem("signInfo")) || {}, //签到信息

        helpData:[],//帮助中心

        //用户对应返点
        userRebate:{},
        //代理相关
        agent:{
            explain:{},//代理说明
            static:{},//代理报表数据
        },
        winPrizeList:[],//中奖快讯
        rankingList:[],//奖金排行榜
        //竞彩足球
        jczq:{
            plan: JSON.parse(sessionStorage.getItem("m_jczq_plan")) || {}, //所选的投注数据
            tempMatchData:JSON.parse(sessionStorage.getItem("m_match_data")) || {}, //所选投注赛事相关数据
            playMap:{
                'ht' : '混合投注',
                'spf' : "让球胜平负",
                'nspf' : "胜平负",
                'bf' : "比分",
                'jqs' : "进球数",
                'bqc' : "半全场",
                'gy' : "冠军/冠亚军",
                'dg' : "单关",
            }
        },
        //北京单场
        bjdc:{
            plan: JSON.parse(sessionStorage.getItem("m_bjdc_plan")) || {}, //所选的投注数据
            tempMatchData:JSON.parse(sessionStorage.getItem("m_bjdc_match_data")) || {}, //所选投注赛事相关数据
            playMap:{
                'spf' : '让球胜平负',
                'sf' : "胜负过关",
                'jqs' : "总进球数",
                'sxds' : "上下单双",
                'bf' : "比分",
                'bqc' : "半全场"
            }
        },
        //竞彩篮球
        jclq:{
            plan: JSON.parse(sessionStorage.getItem("m_jclq_plan")) || {}, //所选的投注数据
            tempMatchData:JSON.parse(sessionStorage.getItem("m_jclq_match_data")) || {}, //所选投注赛事相关数据
            playMap:{
                'ht' : '混合投注',
                'rfsf' : "让分胜负",
                'sf' : "胜负",
                'sfc' : "胜分差",
                'dxf' : "大小分",
                'dg' : "单关",
            }
        },
        //足彩（胜负彩 任选9）
        zucai:{
            plan: JSON.parse(sessionStorage.getItem("m_zucai_plan")) || {}, //所选的投注数据
            tempMatchData:JSON.parse(sessionStorage.getItem("m_zucai_match_data")) || {}, //所选投注赛事相关数据
            playMap:{
                'sfc' : '胜负彩',
                'rx9' : "任选9"
            }
        },
        //数字彩(大乐透、排列3、排列5、七星彩)
        shuzicai:{
            plan:JSON.parse(sessionStorage.getItem("m_szc_plan")) || [], //数字彩投注内容
        }
    },
    actions,
    mutations,
    getters
})
