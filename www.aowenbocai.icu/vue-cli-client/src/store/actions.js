import Vue from 'vue'
import Router from 'vue-router'

export default {
    //获取系统设置信息
    getSystemSet({commit, dispatch, state}) {
        Vue.axios('/index/moblie/getSetting').then(({data}) => {
            commit('setSystemSet', data.data);
        });
    },
    //检查登录状态
    checkLogin({commit, dispatch, state}) {
        return new Promise((resolve,reject)=>{
            commit('setLoadStatus', true);
            Vue.axios('/index/moblie/checkLogin').then(({data}) => {
                commit('setBauth', data.status);
                commit('setLoadStatus', false);
                resolve();
            });
        });
    },
    //获取个人信息
    getUserInfo({commit, dispatch, state}) {
        Vue.axios('/index/user/getinfo').then(({data}) => {
            commit('setUserInfo', data.data);
        });
    },
    //获取个人绑定信息
    getLockInfo({commit, dispatch, state}) {
        Vue.axios('/index/user/lockinfo').then(({data}) => {
            commit('setLockInfo', data.data);
        });
    },
    //获取实名制信息
    getTrueInfo({commit, dispatch, state}) {
        Vue.axios('/index/user/getTrueInfo').then(({data}) => {
            commit('setTrueInfo', data);
        });
    },
    //获取彩票类游戏列表
    getLotteryList({commit, dispatch, state}) {
        Vue.axios('/index/game/getAllList?type=1').then(({data}) => {
            commit('setLotteryList', data.data);
        });
    },
    //获取休闲类游戏列表
    getGameList({commit, dispatch, state}) {
        Vue.axios('/index/game/getAllList?type=0').then(({data}) => {
            commit('setGameList', data.data);
        });
    },
    //获取彩票分组列表
    getNavList({commit, dispatch, state}) {
        Vue.axios('/index/game/getNavList').then(({data}) => {
            commit('setNavList', data.data);
        });
    },
    //获取新闻相关对应栏目id
    getNavClassId({commit, dispatch, state}) {
        Vue.axios('/index/news/getNavClassId').then(({data}) => {
            commit('setNavClassId', data);
        });
    },
    //获取新闻公告
    getNewsPrize({commit, dispatch, state}) {
        Vue.axios('/news/index/getnewlist/nav_id/1/rows=10').then(({data}) => {
            commit('setNewsPrize', data.data);
        });
    },
    //获取中奖快讯列表
    getPrizeList({commit, dispatch, state}) {
        Vue.axios('/index/moblie/getAwardList').then(({data}) => {
            commit('setPrizeList', data.data);
        });
    },
    //获取前10排行榜
    getRankingList({commit, dispatch, state}) {
        Vue.axios('/index/moblie/getTop10').then(({data}) => {
            commit('setRankingList', data.data);
        });
    },
    //获取首页banner图
    getBanner({commit, dispatch, state}) {
        Vue.axios('/index/moblie/getBanner').then(({data}) => {
            commit('setBanner', data.data);
        });
    },
    //获取开奖公告
    getKaijiang({commit, dispatch, state}) {
        Vue.axios('/index/lottery/get_kaijiang').then(({data}) => {
            commit('setKaijiang', data);
        });
    },
    //获取彩种列表
    getLotteryNav({commit, dispatch, state}) {
        Vue.axios('/index/moblie/getLotteryNav').then(({data}) => {
            commit('setLotteryNav', data);
        });
    },
    //获取道具列表
    getBagList({commit, dispatch, state}) {
        Vue.axios('/index/user/getbaglist').then(({data}) => {
            commit('setBagList', data);
        });
    },
    // //获取消费统计
    // getStatic({commit, dispatch, state}) {
    //     commit('setLoadStatus',true)
    //     Vue.axios('/web/user/getStatic').then(({data}) => {
    //         commit('setStatic', data);
    //         commit('setLoadStatus',false)
    //     });
    // },
    //获取最近玩过的游戏
    getExtList({commit, dispatch, state}) {
        Vue.axios('/web/user/getExtList').then(({data}) => {
            commit('setExtList', data.data);
        });
    },
    //获取银行卡绑定信息
    getBanks({commit}) {
        Vue.axios('/web/user/getBanks/type/1').then(({data}) => {
            commit('setBanks', data);
        });
    },
    //获取支付宝绑定信息
    getAlipay({commit}) {
        Vue.axios('/web/user/getBanks/type/2').then(({data}) => {
            commit('setAlipay', data);
        });
    },
    //获取微信绑定信息
    getWei({commit}) {
        Vue.axios('/web/user/getBanks/type/3').then(({data}) => {
            commit('setWei', data);
        });
    },
    //获取兑换商品列表
    getExchangeList({commit}){
        Vue.axios('/web/user/getexchangetype?type=1').then(({data}) => {
            commit('setExchangeList', data.data);
        });
    },
    //获取签到信息
    getSignInfo({commit}){
        Vue.axios('/index/User/signbefore').then(({data}) => {
            commit('setSignInfo', data.data);
        });
    },
    //获取系统消息
    getSystemMsg({commit}){
        Vue.axios('/web/user/getmessage').then(({data}) => {
            commit('setSystemMsg', data.data);
        });
    },
    //获取帮助中心相关信息
    getHelpData({commit}){
        Vue.axios('/index/news/getHelpNews').then(({data}) => {
            commit('setHelpData', data);
        });
    },
    getRechargeList({commit, dispatch, state}) {
        return new Promise((resolve,reject)=>{
            let isRes = false;
            if (!state.rechargeInfo) {
                commit('setLoadStatus', true);
            } else {
                isRes = true;
                resolve();
            }
            Vue.axios('/web/pay/getPayInfo').then(({data}) => {
                commit('setRechargeList', data);
                commit('setLoadStatus', false);
                if (!isRes) {
                    resolve();
                }
            });
        });
    },
    //获取代理说明
    getAgentExplain({commit, dispatch, state}) {
        commit('setLoadStatus',true)
        Vue.axios('/index/news/getAgentExplain').then(({data}) => {
            if(!data.err){
                commit('setAgentExplain', data.data);
            }
            else {
                commit('setAgentExplain', {content:'暂无相关说明'});
            }
            commit('setLoadStatus',false)
        });
    },
}
