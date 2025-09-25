<template>
    <div>
        <mt-header :title="title">
            <a :href="downUrl" slot="left" target="_blank" v-if="!getIsApp && downUrl">
                APP<i class="iconfont icon-xiazai2"></i>
            </a>
            <div slot="right" v-if="qqOnline">
                <router-link to="/onLine" tag="div">
                    <span class="f-sm tr" style="color: #ffffff">
                        <b class="iconfont icon-zaixiankefu" style="padding-right: 2px"></b><em>在线客服</em>
                    </span>
                </router-link>
            </div>
        </mt-header>
        <div class="contentH" :style="{height: contentH + 'px'}">
            <!--sweiper图片-->
            <mt-swipe :auto="4000">
                <mt-swipe-item v-for="(item,index) in bannerList" :key="index">
                    <template v-if="item.url.length">
                        <a :href="item.url"><img :src="item.img_url" alt="" width="100%"></a>
                    </template>
                    <template v-else>
                        <img :src="item.img_url" alt="" width="100%">
                    </template>
                </mt-swipe-item>
            </mt-swipe>
            <!--sweiper图片 end-->
            <div class="card">
                <News-prize/><!--滚动公告--中奖快讯-->
            </div>
            <Gamelist/><!--游戏列表-->
        </div>
    </div>
</template>

<script>
    import Gamelist from "components/game/GameList.vue"; //游戏列表
    import NewsPrize from "components/common/NewsPrize.vue"; //中奖快讯

    export default {
        name:'game',
        data() {
            return {
                cur:0,
                show:false,
            };
        },
        computed: {
            //下载地址
            downUrl(){
                return this.$store.state.setting.moblie_domain
            },
            //在线客服
            qqOnline(){
                return this.$store.state.setting.wap_online_qq
            },
            //是否在app中
            getIsApp () {
                var ua = navigator.userAgent.toLowerCase();
                if (ua.match(/isapp/i) == "isapp") {
                    return true;
                }
                return false;
            },
            title(){
                return this.$store.state.setting.site_name
            },
            bannerList(){
                return this.$store.state.banner
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - 95
            },
        },
        components: {
            Gamelist,
            NewsPrize
        },
        created(){
            this.$store.dispatch('getBanner');
        }
    };
</script>

<style lang="scss" scoped type="text/scss">
    .mint-swipe{height:175px}
</style>
