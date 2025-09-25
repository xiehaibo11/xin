import Vue from 'vue'
import Router from 'vue-router'
import BackLayout from 'components/layouts/BackLayout.vue' //高频版导航
import Header from 'components/common/header.vue' //通用头部
import JcLayout from 'components/competition/layouts/JcLayout.vue' //竞彩版导航
import store from '../store'
import base from 'assets/js/base.js'  //公用js

// 异步加载组件，优化首次打开速度，第二次加载直接去的缓存
const Game = resolve => require(['page/game'], resolve) //游戏大厅
const LotteryMore = resolve => require(['page/game/LotteryMore'], resolve) //全部彩票游戏
const GameMore = resolve => require(['page/game/GameMore'], resolve) //全部休闲游戏
const Kaijiang = resolve => require(['page/kaijiang'], resolve) //开奖
const HistoryCode = resolve => require(['page/kaijiang/historyCode'], resolve) //历史开奖
const Join = resolve => require(['page/join'], resolve) //合买
const User = resolve => require(['page/user'], resolve) //个人中心
const Discover = resolve => require(['page/discover'], resolve) //发现
const Login = resolve => require(['page/login'], resolve) //登录
const LoginByName = resolve => require(['page/login/login-name'], resolve) //账号登录
const LoginByTel = resolve => require(['page/login/login-tel'], resolve) //手机短信登录
const Reg = resolve => require(['page/reg'], resolve) //注册
const FindPwd = resolve => require(['page/login/findPwd'], resolve) //找回密码

const Ranking = resolve => require(['page/discover/ranking'], resolve) //累计中奖排行榜
const WinPrize = resolve => require(['page/discover/winPrize'], resolve) //中奖快讯

const Setting = resolve => require(['page/setting'], resolve) //设置
const SetInfo = resolve => require(['page/setting/setInfo'], resolve) //个人资料
const SetPhoto = resolve => require(['page/setting/setPhoto'], resolve) //修改头像
const SetPwd = resolve => require(['page/setting/setPwd'], resolve) //设置密码
const ChangePwd = resolve => require(['page/setting/changePwd'], resolve) //重置密码
const SetSafePwd = resolve => require(['page/setting/setSafePwd'], resolve) //设置密码
const ChangeSafePwd = resolve => require(['page/setting/changeSafePwd'], resolve) //重置密码
const SetTel = resolve => require(['page/setting/setTel'], resolve) //手机管理
const ChangeTel = resolve => require(['page/setting/changeTel'], resolve) //修改手机
const SetEmail = resolve => require(['page/setting/setEmail'], resolve) //设置邮箱
const ChangeEmail = resolve => require(['page/setting/changeEmail'], resolve) //设置邮箱
const RealName = resolve => require(['page/setting/realName'], resolve) //实名认证
const LockCard = resolve => require(['page/setting/lockCard'], resolve) //绑定账号
const BankList = resolve => require(['page/setting/bankList'], resolve) //我的银行卡列表
const LockBank = resolve => require(['page/setting/lockBank'], resolve) //绑定银行卡
const LockAlipay = resolve => require(['page/setting/lockAlipay'], resolve) //绑定支付宝
const LockWei = resolve => require(['page/setting/lockWei'], resolve) //绑定微信
const PersonalLevel = resolve => require(['page/setting/personalLevel'], resolve) //等级头衔

const Detail = resolve => require(['page/user/detail'], resolve) //账户明细
const CoinDetail = resolve => require(['page/user/coinDetail'], resolve) //彩金明细
const TransformDetail = resolve => require(['page/user/transformDetail'], resolve) //转账明细
const RechargeDetail = resolve => require(['page/user/rechargeDetail'], resolve) //充值明细
const GameRecord = resolve => require(['page/user/gameRecord'], resolve) //游戏记录
const LotteryRecord = resolve => require(['page/user/lotteryRecord'], resolve) //投注记录
const ExchangeRecord = resolve => require(['page/user/exchangeRecord'], resolve) //兑换记录
const Statis = resolve => require(['page/user/statis'], resolve) //消费统计
const SystemMsg = resolve => require(['page/user/systemMsg'], resolve) //消息中心
const Bag = resolve => require(['page/user/bag'], resolve) //我的背包
const RecPlay = resolve => require(['page/user/recPlay'], resolve) //最近玩过
const Contact = resolve => require(['page/user/contact'], resolve) //联系我们

const Pay = resolve => require(['page/pay'], resolve) //充值
const SaoPay = resolve => require(['page/pay/saoPay'], resolve) //扫码支付
const BankPay = resolve => require(['page/pay/bankPay'], resolve) //银行卡转账
const PayWeiRes = resolve => require(['page/pay/payWeiRes'], resolve) //微信支付返回页面
const Exchange = resolve => require(['page/user/exchange'], resolve) //兑换
const Transform = resolve => require(['page/user/transform'], resolve) //转账

const Orders = resolve => require(['page/orders'], resolve) //普通投注详情页
const Chase = resolve => require(['page/orders/chase'], resolve) //追号投注详情页
const JoinDetail = resolve => require(['page/orders/join'], resolve) //追号投注详情页

const Syxw = resolve => require(['page/lottery/syxw'], resolve) //11选5投注页面
const Ssc = resolve => require(['page/lottery/ssc'], resolve) //ssc投注页面
const Pk10 = resolve => require(['page/lottery/pk10'], resolve) //pk10投注页面
const Ks = resolve => require(['page/lottery/ks'], resolve) //快3投注页面
const Pc28 = resolve => require(['page/lottery/pc28'], resolve) //pc28投注页面
const BetOrder = resolve => require(['page/lottery/betOrder'], resolve) //选号列表
const PlayInfo = resolve => require(['page/lottery/playInfo'], resolve) //中奖说明
const PkPlayInfo = resolve => require(['page/lottery/PkPlayInfo'], resolve) //pk10横屏中奖说明
const LotteryTrendChart = resolve => require(['page/lottery/TrendChart'], resolve) //基本走势

const SignIn = resolve => require(['page/activity/signIn'], resolve) //签到
const GameNews = resolve => require(['page/news'], resolve) //游戏资讯2 网站公告1
const NewsDetail = resolve => require(['page/news/newsDetail'], resolve) //文章详情
const Help = resolve => require(['page/news/help'], resolve) //帮助中心
const ActivityCenter = resolve => require(['page/news/activityCenter'], resolve) //优惠活动

const Iframe = resolve => require(['page/iframe'], resolve) //休闲游戏嵌套页面
const onLineKfIframe = resolve => require(['page/iframe/onLineKf'], resolve) //在线客服嵌套页面

const Agent = resolve => require(['page/agent'], resolve) //代理中心
const AgentExplain = resolve => require(['page/agent/AgentExplain'], resolve) //代理说明
const AgentReport = resolve => require(['page/agent/AgentReport'], resolve) //代理报表
const SubAgentReport = resolve => require(['page/agent/SubAgentReport'], resolve) //下级报表
const AgentInvite = resolve => require(['page/agent/AgentInvite'], resolve) //下级开户
const AgentInviteCode = resolve => require(['page/agent/AgentInviteCode'], resolve) //邀请码管理
const AgentMember = resolve => require(['page/agent/AgentMember'], resolve) //会员管理
const AgentBetRecord = resolve => require(['page/agent/AgentBetRecord'], resolve) //投注明细
const AgentBillRecord = resolve => require(['page/agent/AgentBillRecord'], resolve) //资金明细
const AgentRebateOdds = resolve => require(['page/agent/AgentRebateOdds'], resolve) //返点赔率表

const JcIndex = resolve => require(['page/competition/index'], resolve) //竞彩版首页
const JcBf = resolve => require(['page/competition/bf'], resolve) //比分（篮球/足球）
const JcGendan = resolve => require(['page/competition/ball-secret/gendan'], resolve) //跟单
const JcJoin = resolve => require(['page/competition/ball-secret/join'], resolve) //合买
const JcHotPersonSearch = resolve => require(['page/competition/ball-secret/search'], resolve) //红人搜索
const JcUserRecord = resolve => require(['page/competition/user-record'], resolve) //用户战绩中心
const JcGdDetail = resolve => require(['page/competition/orders/GdDetail'], resolve) //跟单详情

const JcZq = resolve => require(['page/competition/lottery/jczq'], resolve) //竞彩足球
const JcZqBetSet = resolve => require(['page/competition/lottery/jczq/JczqBetSet'], resolve) //竞彩足球-我的订单
const JcZqOptimalBonus = resolve => require(['page/competition/lottery/jczq/JczqOptimalBonus'], resolve) //奖金优化

const Bjdc = resolve => require(['page/competition/lottery/bjdc'], resolve) //北京单场
const BjdcBetSet = resolve => require(['page/competition/lottery/bjdc/BjdcBetSet'], resolve) //背景单场-我的订单

const JcLq = resolve => require(['page/competition/lottery/jclq'], resolve) //竞彩篮球
const JcLqBetSet = resolve => require(['page/competition/lottery/jclq/JclqBetSet'], resolve) //竞彩篮球-我的订单

const ZuCai = resolve => require(['page/competition/lottery/zucai'], resolve) //足彩
const ZucaiBetSet = resolve => require(['page/competition/lottery/zucai/ZucaiBetSet'], resolve) //足彩（胜负彩，任选9）-我的订单

const SzcPls = resolve => require(['page/competition/lottery/shuzicai/pls'], resolve) //数字彩--排列3
const SzcPlw = resolve => require(['page/competition/lottery/shuzicai/plw'], resolve) //数字彩--排列5
const SzcQxc = resolve => require(['page/competition/lottery/shuzicai/qxc'], resolve) //数字彩--七星彩
const SzcDlt = resolve => require(['page/competition/lottery/shuzicai/dlt'], resolve) //数字彩--大乐透
const ShuziCaiBetSet = resolve => require(['page/competition/lottery/shuzicai/betOrder'], resolve) //数字彩--订单页面

const FcSsq = resolve => require(['page/competition/lottery/fucai/ssq'], resolve) //福彩--双色球

const Error = resolve => require(['components/common/ErrorPage'], resolve) //404页面


Router.prototype.goBack = function (n) {
    this.isBack = true
    window.history.go(n)
}
Vue.use(Router)

const requireState = base.getCookie('login_in_status')==1 ? true : false //是否需要登录才能访问

const router = new Router({
    // mode:'history',
    routes: [
        {
            path: '/',
            name: 'Index',
            redirect: '/game',
            meta: {
                title: '首页',
                requireAuth: requireState,  // 是否需要登录
            },
        },
        {
            component: BackLayout,
            path: '/BackLayout',
            children:[
                {
                    path: '/game',
                    component: Game,
                    meta: {
                        title: '大厅',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/discover',
                    component: Discover,
                    meta: {
                        title: '发现',
                        requireAuth: requireState,
                        keepAlive:true
                    }
                },
                {
                    path: '/join',
                    component: Join,
                    meta: {
                        title: '合买',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/user',
                    component: User,
                    meta: {
                        title: '我的',
                        requireAuth: true
                    }
                },
                {
                    path: '/kaijiang',
                    component: Kaijiang,
                    meta: {
                        title: '开奖',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/activity',
                    component: ActivityCenter,
                    meta: {
                        title: '活动',
                        requireAuth: requireState
                    }
                },
                {
                    component: Header,
                    path: '/Header',
                    children:[
                        {
                            path: '/joinDetail',
                            name:'joinDetail',
                            component: JoinDetail,
                            meta: {
                                title: '合买详情',
                                requireAuth : requireState
                            }
                        },
                        {
                            path: '/activity/detail',
                            component: NewsDetail,
                            meta: {
                                title: '文章详情',
                                requireAuth: requireState
                            }
                        },
                    ]
                }
            ],
        },
        {
            component: Header,
            path: '/Header',
            children:[
                {
                    path: '/news/notice',
                    component: GameNews,
                    meta: {
                        title: '网站公告',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/news/news',
                    component: GameNews,
                    meta: {
                        title: '游戏资讯',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/news/activity',
                    component: ActivityCenter,
                    meta: {
                        title: '优惠活动',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/news/detail',
                    component: NewsDetail,
                    meta: {
                        title: '文章详情',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/help',
                    component: Help,
                    meta: {
                        title: '帮助中心',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/reg',
                    component: Reg,
                    meta: {
                        title: '欢迎注册',
                        requireAuth: false
                    }
                },
                {
                    path: '/findPwd',
                    component: FindPwd,
                    meta: {
                        title: '找回密码',
                        requireAuth: false
                    }
                },
                {
                    path: '/playInfo',
                    component: PlayInfo,
                    meta: {
                        title: '中奖说明',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/historyCode',
                    component: HistoryCode,
                    meta: {
                        title: '历史开奖',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/viewCode',
                    component: HistoryCode,
                    meta: {
                        title: '历史开奖',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/setting',
                    component: Setting,
                    meta: {
                        title: '设置',
                        requireAuth: true
                    }
                },
                {
                    path: '/setPhoto',
                    component: SetPhoto,
                    meta: {
                        title: '头像',
                        requireAuth: true
                    }
                },
                {
                    path: '/setInfo',
                    component: SetInfo,
                    meta: {
                        title: '个人资料',
                        requireAuth: true
                    }
                },
                {
                    path: '/setPwd',
                    component: SetPwd,
                    meta: {
                        title: '设置密码',
                        requireAuth: true
                    }
                },
                {
                    path: '/changePwd',
                    component: ChangePwd,
                    meta: {
                        title: '重置密码',
                        requireAuth: true
                    }
                },
                {
                    path: '/setSafePwd',
                    component: SetSafePwd,
                    meta: {
                        title: '设置安全密码',
                        requireAuth: true
                    }
                },
                {
                    path: '/changeSafePwd',
                    component: ChangeSafePwd,
                    meta: {
                        title: '重置安全密码',
                        requireAuth: true
                    }
                },
                {
                    path: '/setTel',
                    component: SetTel,
                    meta: {
                        title: '绑定手机',
                        requireAuth: true
                    }
                },
                {
                    path: '/changeTel',
                    component: ChangeTel,
                    meta: {
                        title: '修改手机',
                        requireAuth: true
                    }
                },
                {
                    path: '/setEmail',
                    component: SetEmail,
                    meta: {
                        title: '绑定邮箱',
                        requireAuth: true
                    }
                },
                {
                    path: '/changeEmail',
                    component: ChangeEmail,
                    meta: {
                        title: '修改邮箱',
                        requireAuth: true
                    }
                },
                {
                    path: '/realName',
                    component: RealName,
                    meta: {
                        title: '实名认证',
                        requireAuth: true
                    }
                },
                {
                    path: '/lockCard',
                    component: LockCard,
                    meta: {
                        title: '绑定账号',
                        requireAuth: true
                    }
                },
                {
                    path: '/bankList',
                    component: BankList,
                    meta: {
                        title: '我的银行卡',
                        requireAuth: true
                    }
                },
                {
                    path: '/lockBank',
                    component: LockBank,
                    meta: {
                        title: '绑定银行卡',
                        requireAuth: true
                    }
                },
                {
                    path: '/lockAlipay',
                    component: LockAlipay,
                    meta: {
                        title: '绑定支付宝',
                        requireAuth: true
                    }
                },
                {
                    path: '/lockWei',
                    component: LockWei,
                    meta: {
                        title: '绑定微信',
                        requireAuth: true
                    }
                },
                {
                    path: '/personalLevel',
                    component: PersonalLevel,
                    meta: {
                        title: '等级头衔',
                        requireAuth: true
                    }
                },
                {
                    path: '/detail',
                    component: Detail,
                    meta: {
                        title: '资金明细',
                        requireAuth: true
                    }
                },
                {
                    path: '/coinDetail',
                    component: CoinDetail,
                    meta: {
                        title: '账户明细',
                        requireAuth: true
                    }
                },
                {
                    path: '/transformDetail',
                    component: TransformDetail,
                    meta: {
                        title: '转账明细',
                        requireAuth: true
                    }
                },
                {
                    path: '/rechargeDetail',
                    component: RechargeDetail,
                    meta: {
                        title: '充值明细',
                        requireAuth: true
                    }
                },
                {
                    path: '/pay',
                    component: Pay,
                    meta: {
                        title: '充值',
                        requireAuth: true,
                        keepAlive:true
                    }
                },
                {
                    path: '/saoPay',
                    component: SaoPay,
                    meta: {
                        title: '扫码支付',
                        requireAuth: true
                    }
                },
                {
                    path: '/bankPay',
                    component: BankPay,
                    meta: {
                        title: '银行卡转账',
                        requireAuth: true
                    }
                },
                {
                    path: '/payWeiRes',
                    component: PayWeiRes,
                    meta: {
                        title: '微信支付',
                        requireAuth: true
                    }
                },
                {
                    path: '/lotteryRecord',
                    component: LotteryRecord,
                    meta: {
                        title: '投注记录',
                        requireAuth: true
                    }
                },
                {
                    path: '/chaseRecord',
                    component: LotteryRecord,
                    meta: {
                        title: '追号记录',
                        requireAuth: true
                    }
                },
                {
                    path: '/orders',
                    name:'Orders',
                    component: Orders,
                    meta: {
                        title: '普通投注详情',
                        requireAuth : true
                    }
                },
                {
                    path: '/chase',
                    name:'chase',
                    component: Chase,
                    meta: {
                        title: '追号投注详情',
                        requireAuth : true
                    }
                },
                {
                    path: '/ordersJoin',
                    name:'ordersJoin',
                    component: JoinDetail,
                    meta: {
                        title: '合买投注详情',
                        requireAuth : true
                    }
                },
                {
                    path: '/exchangeRecord',
                    component: ExchangeRecord,
                    meta: {
                        title: '兑换明细',
                        requireAuth: true
                    }
                },
                {
                    path: '/systemMsg',
                    component: SystemMsg,
                    meta: {
                        title: '消息中心',
                        requireAuth: true
                    }
                },
                {
                    path: '/bag',
                    component: Bag,
                    meta: {
                        title: '我的背包',
                        requireAuth: true
                    }
                },
                {
                    path: '/recPlay',
                    component: RecPlay,
                    meta: {
                        title: '最近玩过',
                        requireAuth: true
                    }
                },
                {
                    path: '/contact',
                    component: Contact,
                    meta: {
                        title: '联系我们',
                        requireAuth: true
                    }
                },
                {
                    path: '/agent/explain',
                    component: AgentExplain,
                    meta: {
                        title: '代理说明',
                        requireAuth: true
                    }
                }
            ]
        },
        {
            path: '/statis',
            component: Statis,
            meta: {
                title: '盈亏统计',
                requireAuth: true
            }
        },
        {
            path: '/login',
            component: Login,
            meta: {
                title: '登录',
                requireAuth: false
            }
        },
        {
            path:'/byname',
            component:LoginByName,
            meta:{
                title:'账号登录',
                requireAuth: false
            }
        },
        {
            path:'/bytel',
            component:LoginByTel,
            meta:{
                title:'手机短信登录',
                requireAuth: false
            }
        },
        {
            path: '/syxw',
            component: Syxw,
            meta: {
                title: '11选5',
                requireAuth: true
            }
        },
        {
            path: '/ssc',
            component: Ssc,
            meta: {
                title: '时时彩',
                requireAuth: true
            }
        },
        {
            path: '/pk10',
            component: Pk10,
            meta: {
                title: '天天pk10',
                requireAuth: true
            }
        },
        {
            path: '/ks',
            component: Ks,
            meta: {
                title: '快三',
                requireAuth: true
            }
        },
        {
            path: '/pc28',
            component: Pc28,
            meta: {
                title: 'PC蛋蛋',
                requireAuth: true
            }
        },
        {
            path: '/betOrder',
            component: BetOrder,
            meta: {
                title: '投注区',
                requireAuth: true
            }
        },
        {
            path: '/playInfo/pk',
            component: PkPlayInfo,
            meta: {
                title: '中奖说明',
                requireAuth: true
            }
        },
        {
            path: '/gameRecord/:gameid',
            name: 'gameRecord',
            component: GameRecord,
            meta: {
                title: '游戏记录',
                requireAuth: true
            }
        },
        {
            path: '/exchange',
            component: Exchange,
            meta: {
                title: '兑换',
                requireAuth: true
            }
        },
        {
            path: '/transform',
            component: Transform,
            meta: {
                title: '转账',
                requireAuth: true
            }
        },
        {
            path: '/signIn',
            component: SignIn,
            meta: {
                title: '签到',
                requireAuth: true
            }
        },
        {
            path: '/iframe',
            component: Iframe,
            meta: {
                title: '游戏',
                requireAuth: true
            }
        },
        {
            path: '/onLine',
            component: onLineKfIframe,
            meta: {
                title: '在线客服',
                requireAuth: false
            }
        },
        {
            path: '/agent',
            component: Agent,
            meta: {
                title: '代理中心',
                requireAuth: true
            }
        },
        {
            path: '/agent/report',
            component: AgentReport,
            meta: {
                title: '代理报表',
                requireAuth: true
            }
        },
        {
            path: '/agent/subReport',
            component: SubAgentReport,
            meta: {
                title: '下级报表',
                requireAuth: true
            }
        },
        {
            path: '/agent/invite',
            component: AgentInvite,
            meta: {
                title: '下级开户',
                requireAuth: true
            }
        },
        {
            path: '/agent/inviteCode',
            component: AgentInviteCode,
            meta: {
                title: '邀请码管理',
                requireAuth: true
            }
        },
        {
            path: '/agent/member',
            component: AgentMember,
            meta: {
                title: '会员管理',
                requireAuth: true
            }
        },
        {
            path: '/agent/betRecord',
            component: AgentBetRecord,
            meta: {
                title: '投注明细',
                requireAuth: true
            }
        },
        {
            path: '/agent/billRecord',
            component: AgentBillRecord,
            meta: {
                title: '资金明细',
                requireAuth: true
            }
        },
        {
            path: '/agent/rebateOdds',
            component: AgentRebateOdds,
            meta: {
                title: '返点赔率表',
                requireAuth: true
            }
        },
        {
            path: '/lotteryMore',
            component: LotteryMore,
            meta: {
                title: '彩票游戏',
                requireAuth: requireState
            }
        },
        {
            path: '/gameMore',
            component: GameMore,
            meta: {
                title: '休闲游戏',
                requireAuth: requireState
            }
        },
        {
            path: '/lottery/trade',
            component: LotteryTrendChart,
            meta: {
                title: '基本走势',
                requireAuth: requireState
            }
        },
        {
            path: '/discover/ranking',
            component: Ranking,
            meta: {
                title: '累计中奖排行榜',
                requireAuth: requireState
            }
        },
        {
            path: '/discover/winprize',
            component: WinPrize,
            meta: {
                title: '中奖快讯',
                requireAuth: requireState
            }
        },
        {
            path: '/JcLayout',
            component: JcLayout,
            children:[
                {
                    path: '/jc',
                    component: JcIndex,
                    meta: {
                        title: '大厅',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/jc/bf',
                    component: JcBf,
                    meta: {
                        title: '比分',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/jc/gd',
                    component: JcGendan,
                    meta: {
                        title: '球秘',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/jc/join',
                    component: JcJoin,
                    meta: {
                        title: '球秘',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/jc/search',
                    component: JcHotPersonSearch,
                    meta: {
                        title: '红人搜索',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/jc/kaijiang',
                    component: Kaijiang,
                    meta: {
                        title: '开奖',
                        requireAuth: requireState
                    }
                },
                {
                    path: '/jc/user',
                    component: User,
                    meta: {
                        title: '我的',
                        requireAuth: true
                    }
                },
                {
                    component: Header,
                    path: '/Header',
                    children:[
                        {
                            path: '/jc/gd/detail',
                            component: JcGdDetail,
                            meta: {
                                title: '跟单投注详情',
                                requireAuth: requireState
                            }
                        },
                        {
                            path: '/jc/join/detail',
                            component: JoinDetail,
                            meta: {
                                title: '跟单投注详情',
                                requireAuth: requireState
                            }
                        }
                    ]
                },
                {
                    path: '/jc/userRecord',
                    component: JcUserRecord,
                    meta: {
                        title: '用户战绩',
                        requireAuth: requireState
                    }
                }
            ]
        },
        {
            path: '/jczq',
            name: 'jczq',
            component: JcZq,
            meta: {
                title: '竞彩足球',
                requireAuth: requireState
            }
        },
        {
            path: '/jczq/bet',
            component: JcZqBetSet,
            meta: {
                title: '我的订单',
                requireAuth: requireState
            }
        },
        {
            path: '/jczq/optimalBonus',
            component: JcZqOptimalBonus,
            meta: {
                title: '奖金优化',
                requireAuth: requireState
            }
        },
        {
            path: '/bjdc',
            name: 'bjdc',
            component: Bjdc,
            meta: {
                title: '北京单场',
                requireAuth: requireState
            }
        },
        {
            path: '/bjdc/bet',
            component: BjdcBetSet,
            meta: {
                title: '我的订单',
                requireAuth: requireState
            }
        },
        {
            path: '/jclq',
            name: 'jclq',
            component: JcLq,
            meta: {
                title: '竞彩篮球',
                requireAuth: requireState
            }
        },
        {
            path: '/jclq/bet',
            component: JcLqBetSet,
            meta: {
                title: '我的订单',
                requireAuth: requireState
            }
        },
        {
            path: '/zucai',
            name: 'zucai',
            component: ZuCai,
            meta: {
                title: '足彩',
                requireAuth: requireState
            }
        },
        {
            path: '/zucai/bet',
            component: ZucaiBetSet,
            meta: {
                title: '我的订单',
                requireAuth: requireState
            }
        },
        {
            path: '/pls',
            name: 'pls',
            component: SzcPls,
            meta: {
                title: '排列3',
                requireAuth: requireState
            }
        },
        {
            path: '/plw',
            name: 'plw',
            component: SzcPlw,
            meta: {
                title: '排列5',
                requireAuth: requireState
            }
        },
        {
            path: '/qxc',
            name: 'qxc',
            component: SzcQxc,
            meta: {
                title: '七星彩',
                requireAuth: requireState
            }
        },
        {
            path: '/dlt',
            name: 'dlt',
            component: SzcDlt,
            meta: {
                title: '大乐透',
                requireAuth: requireState
            }
        },
        {
            path: '/ssq',
            name: 'ssq',
            component: FcSsq,
            meta: {
                title: '双色球',
                requireAuth: requireState
            }
        },
        {
            path: '/shuzicai/bet',
            component: ShuziCaiBetSet,
            meta: {
                title: '我的订单',
                requireAuth: requireState
            }
        },
        {
            component: Header,
            path: '/Header',
            children:[

            ]
        },
        {
            path:'*',
            component:Error,
            meta:{
                title:'页面未找到'
            }
        }
    ],
    // scrollBehavior (to, from, savedPosition) {
    //     if (savedPosition) {
    //         return savedPosition
    //     } else {
    //         if (from.meta.keepAlive) {
    //             from.meta.savedPosition = document.documentElement.scrollTop;
    //         }
    //         return { x: 0, y: to.meta.savedPosition || 0 }
    //     }
    // }
})

export default router
