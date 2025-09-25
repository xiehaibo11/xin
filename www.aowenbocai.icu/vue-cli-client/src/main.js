// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import 'babel-polyfill'
import Vue from 'vue'
import MintUI from 'mint-ui'
import App from './App'
import router from './router'
import axios from 'axios';
import store from './store';
import querystring from 'querystring';
import {_} from 'underscore';

import 'mint-ui/lib/style.css' //引入mint-ui样式文件
// require('assets/fonts/iconfont.css') //引入图标字体
require('assets/css/red-mint.scss')//重置mint-ui样式文件
require('assets/css/style.scss')//公用样式文件
require('assets/css/lottery.scss') //投注相关公用样式文件
require('assets/css/jc.scss') //竞彩投注相关公用样式文件

import base from 'assets/js/base.js'  //公用js
import bet from 'assets/js/lottery.js'  //投注相关方法
Vue.prototype.$base = base
Vue.prototype.$bet = bet
Vue.prototype.$_ = _

Vue.axios = Vue.prototype.$axios = axios.create({
    headers: {'X-Requested-With': 'XMLHttpRequest'},
    transformRequest: [function (data) {
        data = querystring.stringify(data)
        return data
    }],
    timeout: 5000, // 请求的超时时间
    withCredentials: true // 允许携带cookie
});
// request拦截器
Vue.axios.interceptors.request.use(
    config => {
        let nowtamp = new Date().getTime();
        //记住密码
        if(localStorage.getItem("loginToken") != null){
            let cTime =  nowtamp - JSON.parse(localStorage.getItem("loginToken")).timeTemp
            let zq = 7 * 24 * 60 * 60 * 1000
            if(cTime > zq){ //超过七天
                localStorage.removeItem("loginToken")
                base.delCookie('has_login')
                base.delCookie('bAuth')
                base.delCookie('timetamp')
                if(router.currentRoute.meta.requireAuth){
                    MessageBox({
                        showClose: true,
                        message: "登录状态信息过期,请重新登录",
                        type: "error"
                    });
                    router.replace({
                        path:'/login',
                        query:{
                            redirect:router.currentRoute.fullPath
                        }
                    })
                }
            }else {
                let expires_time = (zq - cTime) / 1000 //过期时间
                let token = JSON.parse(localStorage.getItem("loginToken")).token //登录标识
                base.setCookie('has_login',token,expires_time)
                // base.setCookie('bAuth',1)
            }
        }else {
            let timetamp = base.getCookie('timetamp') || nowtamp
            let now = parseInt(nowtamp) - parseInt(timetamp);
            if(now < 24*60*1000){ //24分钟
                base.setCookie('timetamp',nowtamp)
            }else {
                base.delCookie('bAuth')
                base.delCookie('timetamp')
                if(router.currentRoute.meta.requireAuth){
                    MessageBox({
                        showClose: true,
                        message: "登录状态信息过期,请重新登录",
                        type: "error"
                    });
                    router.replace({
                        path:'/login',
                        query:{
                            redirect:router.currentRoute.fullPath
                        }
                    })
                }
            }
        }
        // 发送请求之前，要做的业务
        return config
    },
    error => {
        // // 错误处理代码
        // MessageBox({
        //     showClose: true,
        //     message: '内部服务错误，请稍后再试',
        //     type: "error"
        // });
        return Promise.reject(error)
    }
)

// response拦截器
Vue.axios.interceptors.response.use(
    response => {
        // 数据响应之后，要做的业务
        return response
    },
    error => {
        // // 错误处理代码
        // MessageBox({
        //     //  饿了么的消息弹窗组件,类似toast
        //     showClose: true,
        //     message: '内部服务错误，请稍后再试',
        //     type: "error"
        // });
        // 返回 response 里的错误信息
        // let errorInfo =  error.data.error ? error.data.error.message : error.data;
        return Promise.reject(error)
    }
)

import { Toast } from 'mint-ui'
import { MessageBox } from 'mint-ui';
Vue.$toast = Vue.prototype.$toast = Toast;
Vue.$messagebox = Vue.prototype.$messagebox = MessageBox;


// 一、执行返回动画
window.addEventListener("popstate", function(e) {
    router.isBack = true
}, false);

router.beforeEach((to,from, next) => {

    store.commit('setLoadStatus', true);
    // 如果isBack为true时，证明是用户点击了回退，执行slide-right动画
    let isBack = router.isBack
    let isformRules = store.state.isformRules
    router.isBack = false
    if (isformRules) {
        store.commit('setTransitionName','fade')
        router.isBack = false
    } else {
        if (isBack) {
            store.commit('setTransitionName','slide-right')  //后退
        } else {
            if(from.path=='/join'&& to.path == '/user'|| to.path == '/game' || to.path == '/discover' || to.path == '/kaijiang'|| to.path == '/activity'){
                store.commit('setTransitionName','fade')
            }else if(from.path=='/kaijiang'&&to.path=='/join'||to.path == '/user'|| to.path == '/game' || to.path == '/discover'|| to.path == '/activity'){
                store.commit('setTransitionName','fade')
            }else if(from.path=='/game'&&to.path=='/join'||to.path == '/user'|| to.path == '/discover' || to.path == '/kaijiang'|| to.path == '/activity'){
                store.commit('setTransitionName','fade')
            }else if(from.path=='/discover'&&to.path=='/join'||to.path == '/user'|| to.path == '/game'  || to.path == '/kaijiang'|| to.path == '/activity'){
                store.commit('setTransitionName','fade')
            }else if(from.path=='/user'&&to.path=='/join'|| to.path == '/game' || to.path == '/discover' || to.path == '/kaijiang'|| to.path == '/activity'){
                store.commit('setTransitionName','fade')
            }else if(from.path=='/activity'&&to.path=='/join'|| to.path == '/game' || to.path == '/discover' || to.path == '/kaijiang'|| to.path == '/user'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/agent/invite' &&  to.path == '/agent/inviteCode'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/agent/inviteCode' &&  to.path == '/agent/invite'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/gameMore' && to.path == '/lotteryMore'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/lotteryMore' && to.path == '/gameMore'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/jc'&& to.path == '/jc/bf' || to.path == '/jc/gd' || to.path == '/jc/kaijiang' || to.path == '/jc/user'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/jc/bf'&& to.path == '/jc'|| to.path == '/jc/gd' || to.path == '/jc/kaijiang' || to.path == '/jc/user'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/jc/gd' && to.path == '/jc' || to.path == '/jc/bf'|| to.path == '/jc/kaijiang' || to.path == '/jc/user'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/jc/kaijiang' && to.path == '/jc' || to.path == '/jc/bf' || to.path == '/jc/gd' || to.path == '/jc/user'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/jc/user' && to.path == '/jc' || to.path == '/jc/bf' || to.path == '/jc/gd' || to.path == '/jc/kaijiang'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/jc/gd' && to.path == '/jc/join'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/jc/join' && to.path == '/jc/gd'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/discover/winprize' && to.path == '/discover/ranking'){
                store.commit('setTransitionName','fade')
            }else if(from.path == '/discover/ranking' && to.path == '/discover/winprize'){
                store.commit('setTransitionName','fade')
            }else {
                store.commit('setTransitionName','slide-left')  //前进
            }
            // store.commit('setTransitionName','slide-left')  //前进
        }
        // 做完回退动画后，要设置成前进动画，否则下次打开页面动画将还是回退
        router.isBack = false
    }
    store.state.isformRules = false

    let loginSign = JSON.parse(localStorage.getItem("loginToken")) || base.getCookie('bAuth') ? true : false;
    let title = to.meta.title;
    if (title) document.title = title;
    let needAuth = to.matched.some(item => item.meta.requireAuth);
    if (needAuth){  // 判断该路由是否需要登录权限
        if (loginSign) {  // 判断是否登录
            next();
        }else {
            next({
                path: '/login',
                query: {
                    redirect:to.fullPath,
                }  // 将跳转的路由path作为参数，登录成功后跳转到该路由
            })
            router.app.$options.store.commit('setLoadStatus', false);
        }
    }else {
        next();
    }
})

router.afterEach((to, from) => {
    router.app.$options.store.commit('setLoadStatus', false);
})

Vue.use(MintUI)
import VueClipboard from 'vue-clipboard2' //复制
Vue.use(VueClipboard)
// import LyTab from 'ly-tab'; //可滑动导航
// Vue.use(LyTab);
/* eslint-disable no-new */
new Vue({
    el: '#app',
    store,
    router,
    components: { App },
    template: '<App/>'
})
