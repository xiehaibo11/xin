<template>
    <div id="app">
        <template v-if="transitionName == 'fade'">
            <transition :name="transitionName" mode="out-in">
                <keep-alive :include=needKeepAlive>
                    <router-view class="Router contentH" :style="{height: contentH + 'px'}"></router-view>
                </keep-alive>
            </transition>
        </template>
        <template v-else>
            <transition :name="transitionName">
                <keep-alive :include=needKeepAlive>
                    <router-view class="Router contentH" :style="{height: contentH + 'px'}"></router-view>
                </keep-alive>
            </transition>
        </template>
        <Loading v-if="loadStatus"/>
    </div>
</template>

<script>
    import Loading from 'components/common/loading.vue'
    export default {
        name: 'App',
        data() {
            return {
            }
        },
        components: {
            Loading
        },
        computed: {
            loadStatus() { //加载状态
                return this.$store.state.loadStatus;
            },
            bAuth(){ //是否登录
                return this.$store.state.bAuth;
            },
            transitionName(){
                return this.$store.state.transitionName;
            },
            show(){
                return this.$store.state.splash
            },
            needKeepAlive(){
                return this.$store.state.needKeepAlive
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight
            }
        },
        mounted(){
            // 获取浏览器可视区域高度
            let clientH = document.documentElement.clientHeight
            let clienW = document.documentElement.clientWidth
            let screenSize = {
                h : clientH,
                w : clienW
            }
            this.$store.commit('setClientH',screenSize)

            window.onresize = ()=>{
                let clientH = document.documentElement.clientHeight
                let clienW = document.documentElement.clientWidth
                let screenSize = {
                    h : clientH,
                    w : clienW
                }
                this.$store.commit('setClientH',screenSize)
            };

            this.$store.dispatch('getSystemSet');//获取系统设置信息
            this.$store.dispatch('getLotteryNav');//获取游戏导航
            this.$store.dispatch('getLotteryList'); //获取彩票游戏列表
            this.$store.dispatch('getGameList'); //获取休闲游戏列表
            this.$store.dispatch('getNavList'); //获取彩票分组列表
            this.$store.dispatch('getNavClassId'); //获取新闻相关对应栏目id
            if(this.bAuth){
                this.$store.dispatch('getRechargeList'); //获取充值配置
                this.$store.dispatch('getUserInfo') //获取个人信息
                this.$store.dispatch('getLockInfo'); //获取账号绑定信息
//                this.$store.dispatch('getTrueInfo'); //获取实名制信息
                this.$store.dispatch('getSystemMsg'); //获取系统消息
            }

            var _ss = document.getElementsByClassName("contentH");
            _ss.ontouchmove = function (ev) {
                var _point = ev.touches[0],
                    _top = _ss.scrollTop;
                // 什么时候到底部
                var _bottomFaVal = _ss.scrollHeight - _ss.offsetHeight;
                // 到达顶端
                if (_top === 0) {
                    // 阻止向下滑动
                    if (_point.clientY > startY) {
                        ev.preventDefault();
                    } else {
                        // 阻止冒泡
                        // 正常执行
                        ev.stopPropagation();
                    }
                } else if (_top === _bottomFaVal) {
                    // 到达底部
                    // 阻止向上滑动
                    if (_point.clientY < startY) {
                        ev.preventDefault();
                    } else {
                        // 阻止冒泡
                        // 正常执行
                        ev.stopPropagation();
                    }
                } else if (_top > 0 && _top < _bottomFaVal) {
                    ev.stopPropagation();
                } else {
                    ev.preventDefault();
                }
            };
        }
    }
</script>
