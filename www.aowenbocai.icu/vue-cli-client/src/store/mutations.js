import Vue from 'vue';
import {_} from 'underscore';

export default {
    //动态添加缓存页面
    setKeepAlivePage(state, name){
        var index = state.needKeepAlive.indexOf(name);
        if(index == -1){
            state.needKeepAlive.push(name)
        }
    },
    delKeepAlivePage(state,name){
        var index = state.needKeepAlive.indexOf(name);
        if (index > -1) {
            state.needKeepAlive.splice(index, 1);
        }
    },
    setSystemSet(state, info) {
        if (!_.isEqual(state.setting, info)) {
            Vue.set(state, 'setting', info);
            if(info){
                localStorage.setItem('setting',JSON.stringify(info));
            }
        }
    },
    setLotteryNav(state,dataInfo){
        if (!_.isEqual(state.lotteryNav, dataInfo.data)){
            Vue.set(state, 'lotteryNav', dataInfo.data);
            if(dataInfo.data){
                localStorage.setItem('lotteryNav',JSON.stringify(dataInfo.data));
            }
        }
        if (!_.isEqual(state.gameNav, dataInfo.game)){
            Vue.set(state, 'gameNav', dataInfo.game);
            if(dataInfo.game){
                localStorage.setItem('gameNav',JSON.stringify(dataInfo.game));
            }
        }
    },
    setBauth(state, status){
        if (!_.isEqual(state.bAuth, status)) {
            Vue.set(state, 'bAuth', status);
            // 如果设置为false（退出登录），清理相关cookie
            if (!status) {
                document.cookie = 'bAuth=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                document.cookie = 'has_login=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            }
        }
    },
    setUserInfo(state, info) {
        if (!_.isEqual(state.userinfo, info)) {
            Vue.set(state, 'userinfo', info);
        }
        if (!_.isEqual(state.userRebate, info.rebate)){
            Vue.set(state, 'userRebate', info.rebate);
        }
    },
    clearUserInfo(state, info) {
        Vue.set(state, 'userinfo', {
            rebate:{},
            user_grade:{}
        });
        // 清理所有用户相关状态
        Vue.set(state, 'lockInfo', []);
        Vue.set(state, 'trueInfo', '');
        Vue.set(state, 'trueState', '');
        // 清理本地存储的用户数据
        localStorage.removeItem('loginToken');
        localStorage.removeItem('userinfo');
    },
    setLockInfo(state, info) {
        if (!_.isEqual(state.lockInfo, info)) {
            Vue.set(state, 'lockInfo', info);
        }
    },
    setTrueInfo(state, info) {
        if(!info.err){
            if (!_.isEqual(state.trueInfo, info.data)) {
                Vue.set(state, 'trueInfo', info.data);
                Vue.set(state, 'trueState', 1);
            }
        }else {
            Vue.set(state, 'trueState', 0);
        }
    },
    setLoadStatus(state, status) {
        state.loadStatus = status;
        // if (status) {
        //     if (state.loadTime == 0) {
        //         state.loadStatus = true;
        //     }
        //     state.loadTime ++;
        // } else {
        //     state.loadTime --;
        //     if (state.loadTime == 0) {
        //         state.loadStatus = false;
        //     }
        // }
    },
    setTransitionName(state, name){
        Vue.set(state, 'transitionName', name);
    },
    setPageLoad(state,status){
        Vue.set(state.page, 'loadState', status);
    },
    setPageLoad2(state,status){
        Vue.set(state.page, 'loadState2', status);
    },
    setGameList(state, list) {
        if (!_.isEqual(state.gameList, list)){
            Vue.set(state, 'gameList', list);
            if(list){
                localStorage.setItem('gameList',JSON.stringify(list));
            }
        }
    },
    setLotteryList(state, list) {
        if (!_.isEqual(state.lotteryList, list)){
            Vue.set(state, 'lotteryList', list);
            if(list){
                localStorage.setItem('lotteryList',JSON.stringify(list));
            }
        }
    },
    setNavList(state,list){
        if (!_.isEqual(state.lotteryShow, list)){
            Vue.set(state, 'lotteryShow', list);
            if(list){
                localStorage.setItem('navList',JSON.stringify(list));
            }
        }
    },
    setNavClassId(state,data){
        if (!_.isEqual(state.newNavClassId, data)){
            Vue.set(state, 'newNavClassId', data);
            if(data){
                localStorage.setItem('news_nav_class_id',JSON.stringify(data));
            }
        }
    },
    setNewsPrize(state, list) {
        if (!_.isEqual(state.newsPrize, list)){
            Vue.set(state, 'newsPrize', list);
        }
    },
    setPrizeList(state, list) {
        if (!_.isEqual(state.winPrizeList, list)){
            Vue.set(state, 'winPrizeList', list);
        }
    },
    setRankingList(state, list) {
        if (!_.isEqual(state.rankingList, list)){
            Vue.set(state, 'rankingList', list);
        }
    },
    setBanner(state, list) {
        if (!_.isEqual(state.banner, list)){
            Vue.set(state, 'banner', list);
        }
    },
    setKaijiang(state, list) {
        if (!_.isEqual(state.kaijiang, list)){
            Vue.set(state, 'kaijiang', list);
        }
    },
    setBagList(state, list) {
        if (!_.isEqual(state.bag, list)){
            Vue.set(state, 'bag', list);
        }
    },
    setStatic(state, list) {
        if (!_.isEqual(state.static, list)){
            Vue.set(state, 'static', list);
        }
    },
    setExtList(state, list) {
        if (!_.isEqual(state.extList, list)){
            Vue.set(state, 'extList', list);
        }
    },
    setBanks(state,info){
        if(info.err){
            Vue.set(state.lockBank, 'banks', []);
        }else {
            if (!_.isEqual(state.lockBank.banks, info.data)){
                Vue.set(state.lockBank, 'banks', info.data);
            }
        }
    },
    setAlipay(state,info){
        if(info.err){
            Vue.set(state.lockBank, 'alipay', []);
        }else {
            if (!_.isEqual(state.lockBank.alipay, info.data)){
                Vue.set(state.lockBank, 'alipay', info.data);
            }
        }
    },
    setWei(state,info){
        if(info.err){
            Vue.set(state.lockBank, 'wei', []);
        }else {
            if (!_.isEqual(state.lockBank.wei, info.data)){
                Vue.set(state.lockBank, 'wei', info.data);
            }
        }
    },


    //设置投注基本数据
    setBetData(state,info){
        Vue.set(state.lottery.info, 'time', info.down_time);
        Vue.set(state.lottery.info, 'expect', info.expect);
        Vue.set(state.lottery.info, 'sort_expect', info.sort_expect);
        Vue.set(state.lottery.info, 'lastIssue', info.lastIssue);
        Vue.set(state.lottery.info, 'todayTime', info.todayTime);
        Vue.set(state.lottery.info, 'totalIssue', info.startIssue);
        Vue.set(state.lottery.info, 'timelong', info.timelong);
    },
    //设置最新一期开奖号码
    setAwardNumber(state,info){
        Vue.set(state.lottery.info, 'awardNumber', info);
    },
    //设置玩法配置数据
    setPlayInfo(state,info){
        Vue.set(state.lottery, 'play',info);
    },
    //设置miss遗漏
    setMiss(state,info){
        Vue.set(state.lottery, 'miss',info);
    },
    //设置期号类型
    setType(state,info){
        Vue.set(state.lottery, 'expect_type',info);
    },
    //设置第一期
    setFirstIssue(state,data){
        Vue.set(state.lottery.info, 'firstIssue',data);
    },
    setSmallInfo(state,info){
        Vue.set(state.lottery, 'small',info);
    },
    //设置返点相关数据
    setRebateInfo(state,data){
        Vue.set(state.lottery, 'upUserRebate',data.up_user_rebate);
        Vue.set(state.lottery, 'userRebate',data.user_rebate);
        Vue.set(state.lottery, 'sliderValue', data.user_rebate);
        Vue.set(state.lottery, 'bonus_base',data.bonus_base);
    },
    //清空基本投注数据
    clearBetData(state,info){
        Vue.set(state.lottery.info, 'time', '');
        Vue.set(state.lottery.info, 'expect', '');
    },
    //倒计时
    setDownTime(state){
        state.lottery.info.time--;
    },
    //清除倒计时
    clearDownTime(state){
        clearInterval(state.intervalTime);
    },
    //添加投注数据
    pushBetNum(state,betinfo){
        state.lottery.betArr.unshift(betinfo)
        sessionStorage.setItem('betinfo',JSON.stringify(state.lottery.betArr));
    },
    //添加投注数据(数组)
    pushBetArr(state,arr){
        for(let i in arr){
            state.lottery.betArr.unshift(arr[i])
        }
        sessionStorage.setItem('betinfo',JSON.stringify(state.lottery.betArr));
    },
    //清除投注数据
    clearBetNum(state){
        Vue.set(state.lottery, 'betArr',[]);
        sessionStorage.removeItem('betinfo')
    },
    //删除投注号码
    deleteBetArr(state,index){
        state.lottery.betArr.splice(index, 1);
        sessionStorage.setItem('betinfo',JSON.stringify(state.lottery.betArr));
    },
    //设置近期开奖数组
    setRecentOpen(state,data){
        Vue.set(state.lottery, 'recent_open',data);
    },
    //设置pk10玩法介绍
    setPlayBouns(state,data){
        if (!_.isEqual(state.lottery.pk10Bouns, data)){
            Vue.set(state.lottery, 'pk10Bouns', data);
            if(data){
                localStorage.setItem('pk10Bouns',JSON.stringify(data));
            }
        }
    },
    //倒计时为0时 更新开奖数组
    updateRecentOpen(state,data){
        let arr = state.lottery.recent_open;
        let isExit = 0 //判断是否已经含有该期号
        for(var i in arr){
            if(arr[i].expect == data.expect){
                isExit +=1;
            }
        }
        if(!isExit){
            if(arr.length == 10){
                arr.pop();
            }
            arr.unshift(data)
        }
    },
    //清除获取开奖号码定时器
    clearNewCode(state){
        clearInterval(state.newCodeFun);
    },
    //清除开奖动画
    clearRandomNum(state){
        clearInterval(state.rNumTimer);
    },
    isGetNewCode(state,status){
        Vue.set(state.lottery, 'isGetCode',status);
    },

    setExchangeList(state,list){
        if (!_.isEqual(state.exchangeList, list)){
            Vue.set(state, 'exchangeList', list);
        }
    },

    //初始加载合买列表
    setJoinList(state, list) {
        if (!_.isEqual(state.joinList, list)){
            if(state.joinList.length){ //下拉刷新替换数据
                state.joinList.splice(14, state.joinList.length-14);
            }
            Vue.set(state, 'joinList', list);
            if(list){
                localStorage.setItem('joinList',JSON.stringify(list));
            }
        }
    },
    //加载更多合买列表数据
    loadMoreJoinList(state,list){
        for(let p in list){
            state.joinList.push(list[p])
        }
    },
    //购买后更新对应页数据
    updataJoinList(state,info){
        let perPage = parseInt(info.per_page)
        let currentPage = parseInt(info.current_page)
        let n = parseInt(perPage * currentPage);
        let m = parseInt(n - perPage)
        for(var i = 0 ; i< info.data.length;i++){
            state.joinList.splice(m + i,1,info.data[i]);
        }
        Vue.set(state, 'joinList', state.joinList);
        if(currentPage == 1){
            localStorage.setItem('joinList',JSON.stringify(info.data));
        }
    },
    //修改滚动合买页面滚动条位置
    setPageYOffset(state, val) {
        state.pageYOffset = val;
    },
    //2次修改页面滚动条位置
    setPageYOffset2(state, val) {
        state.pageYOffset2 = val;
    },
    //更新合买数据
    setJoinIndex(state, val){
        state.updateIndex = val
    },

    setRechargeList(state,info){
        if (!_.isEqual(state.rechargeInfo, info)){
            Vue.set(state, 'rechargeInfo', info);
            localStorage.setItem('paySetting',JSON.stringify(info));
        }
    },
    setSignInfo(state,info){
        if (!_.isEqual(state.signInfo, info)){
            if(info){
                Vue.set(state, 'signInfo', info);
                sessionStorage.setItem('signInfo',JSON.stringify(info));
            }
        }
    },
    setSystemMsg(state,list){
        if (!_.isEqual(state.systemMsg, list)){
            Vue.set(state, 'systemMsg', list);
        }
    },
    setHelpData(state,list){
        if (!_.isEqual(state.helpData, list)){
            Vue.set(state, 'helpData', list);
        }
    },
    //更改单位信息
    changeModelValue(state,arr){
        if (!_.isEqual(state.lottery.label, arr[0])){
            Vue.set(state.lottery, 'label', arr[0]);
        }
        if (!_.isEqual(state.lottery.value, arr[1])){
            Vue.set(state.lottery, 'value', arr[1]);
        }
    },
    //更改倍数信息
    changeMulValue(state,value){
        if (!_.isEqual(state.lottery.multiple, value)){
            Vue.set(state.lottery, 'multiple', value);
        }
    },
    //获取屏幕高度
    setClientH(state,value){
        if (!_.isEqual(state.clientHeight, value)){
            Vue.set(state, 'clientHeight', value.h);
            Vue.set(state, 'clientWidth', value.w);
        }
    },
    //更改用户所选返点值
    changeRebateVal(state,value){
        if (!_.isEqual(state.lottery.sliderValue, value)){
            Vue.set(state.lottery, 'sliderValue', value);
        }
    },

    //代理说明
    setAgentExplain(state,data){
        if (!_.isEqual(state.agent.explain, data)){
            Vue.set(state.agent, 'explain', data);
        }
    },
    //代理报表
    setAgentReport(state, list) {
        if (!_.isEqual(state.agent.static, list)){
            Vue.set(state.agent, 'static', list);
        }
    },


    //竞彩足球投注赛事数据
    setJcZqPlan(state,data){
        Vue.set(state.jczq, 'plan', data);
        sessionStorage.setItem('m_jczq_plan',JSON.stringify(data));
    },
    //竞彩足球投注赛事列表
    setJcZqTempMatchData(state,data){
        Vue.set(state.jczq, 'tempMatchData', data);
        sessionStorage.setItem('m_match_data',JSON.stringify(data));
    },
    // 清空竞彩足球投注数据
    clearJczqBetData(state){
        sessionStorage.removeItem('m_jczq_plan')
        sessionStorage.removeItem('m_match_data')
        sessionStorage.removeItem('jczq_gg_type')
        Vue.set(state.jczq, 'plan', {});
        Vue.set(state.jczq, 'tempMatchData', {});
    },

    //北京单场投注赛事数据
    setBjdcPlan(state,data){
        Vue.set(state.bjdc, 'plan', data);
        sessionStorage.setItem('m_bjdc_plan',JSON.stringify(data));
    },
    //北京单场投注赛事列表
    setBjdcTempMatchData(state,data){
        Vue.set(state.bjdc, 'tempMatchData', data);
        sessionStorage.setItem('m_bjdc_match_data',JSON.stringify(data));
    },
    // 清空北京单场投注数据
    clearBjdcBetData(state){
        sessionStorage.removeItem('m_bjdc_plan')
        sessionStorage.removeItem('m_bjdc_match_data')
        sessionStorage.removeItem('bjdc_gg_type')
        Vue.set(state.bjdc, 'plan', {});
        Vue.set(state.bjdc, 'tempMatchData', {});
    },

    //竞彩篮球投注赛事数据
    setJcLqPlan(state,data){
        Vue.set(state.jclq, 'plan', data);
        sessionStorage.setItem('m_jclq_plan',JSON.stringify(data));
    },
    //竞彩篮球投注赛事列表
    setJcLqTempMatchData(state,data){
        Vue.set(state.jclq, 'tempMatchData', data);
        sessionStorage.setItem('m_jclq_match_data',JSON.stringify(data));
    },
    // 清空竞彩篮球投注数据
    clearJclqBetData(state){
        sessionStorage.removeItem('m_jclq_plan')
        sessionStorage.removeItem('m_jclq_match_data')
        sessionStorage.removeItem('jclq_gg_type')
        Vue.set(state.jclq, 'plan', {});
        Vue.set(state.jclq, 'tempMatchData', {});
    },

    //足彩投注赛事数据（任9，胜负彩）
    setZucaiPlan(state,data){
        Vue.set(state.zucai, 'plan', data);
        sessionStorage.setItem('m_zucai_plan',JSON.stringify(data));
    },
    //足彩投注赛事列表（任9，胜负彩）
    setZucaiTempMatchData(state,data){
        Vue.set(state.zucai, 'tempMatchData', data);
        sessionStorage.setItem('m_zucai_match_data',JSON.stringify(data));
    },
    // 清空足彩投注数据（任9，胜负彩）
    clearZucaiBetData(state){
        sessionStorage.removeItem('m_zucai_plan')
        sessionStorage.removeItem('m_zucai_match_data')
        Vue.set(state.zucai, 'plan', {});
        Vue.set(state.zucai, 'tempMatchData', {});
    },

    //数字彩添加投注数据（大乐透、排列3、排列5、七星彩）
    pushSzcBetNum(state,betinfo){
        state.shuzicai.plan.unshift(betinfo)
        sessionStorage.setItem('m_szc_plan',JSON.stringify(state.shuzicai.plan));
    },
    //清空数字彩投注数据
    clearSzcBetNum(state){
        sessionStorage.removeItem('m_szc_plan')
        Vue.set(state.shuzicai, 'plan', []);
    },
    //删除投注号码
    deleteSzcBetArr(state,index){
        state.shuzicai.plan.splice(index, 1);
        sessionStorage.setItem('m_szc_plan',JSON.stringify(state.shuzicai.plan));
    },
}
