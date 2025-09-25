<template>
    <div>
        <template v-if="transitionName == 'fade'">
            <transition :name="transitionName" mode="out-in">
                <keep-alive :include=needKeepAlive>
                    <router-view class="Router-fade contentH"  :style="{height: contentH + 'px'}"></router-view>
                </keep-alive>
            </transition>
        </template>
        <template v-else>
            <transition :name="transitionName">
                <keep-alive :include=needKeepAlive>
                    <router-view class="Router-fade contentH"  :style="{height: contentH + 'px'}"></router-view>
                </keep-alive>
            </transition>
        </template>
        <mt-tabbar class="page-part" v-model="selected" v-if="$route.path == '/jc/join' || $route.path == '/jc/bf' || $route.path == '/jc' ||$route.path == '/jc/gd' || $route.path == '/jc/kaijiang' || $route.path == '/jc/user'" >
            <mt-tab-item  v-for="(value,index) in link" :key="index" :id="value.name" @click.native="tabClick(value.href,value.name)">
                <i slot="icon" class="iconfont" :class="selected == value.name ? value.active : value.icon"></i>{{value.name}}
            </mt-tab-item>
        </mt-tabbar>
    </div>
</template>
<script>
    export default{
        name: 'JcLayout',
        data() {
            return {

            };
        },
        computed:{
            //彩票开关
            lotteryStatus(){
                return Number(this.$store.state.setting.lottery_status)
            },
            //合买是否开启
            join_isOpen(){
                return this.$store.state.setting.join_isOpen == 1 ? true : false
            },
            link(){
                return [{
                    href: '/jc',
                    name: '大厅',
                    icon: 'icon-jingcaidanguan',
                    active:'icon-jingcai'
                },{
                    href: '/jc/bf',
                    name: '比分',
                    icon: 'icon-caidanlan_bifentubiao_weixuan',
                    active:'icon-tabbar_bifen_s'
                },{
                    href: '/jc/gd',
                    name: '球秘',
                    icon: 'icon-changshuzuqiuchaojiliansaigai',
                    active:'icon-changshuzuqiuchaojiliansaigai'
                },{
                    href: '/jc/kaijiang',
                    name: '开奖',
                    icon: 'icon-result',
                    active:'icon-result-active'
                },{
                    href: '/jc/user',
                    name: '我的',
                    icon: 'icon-ziyuan',
                    active:'icon-ziyuan1'
                }]
            },
            selected:{
                get(){
                    return this.$route.meta.title
                },
                set(){}
            },
            needKeepAlive(){
                return this.$store.state.needKeepAlive
            },
            transitionName(){
                return this.$store.state.transitionName;
            },
            //屏幕高度
            contentH(){
                return this.$route.path == '/jc' || this.$route.path == '/jc/join' || this.$route.path == '/jc/gd' || this.$route.path == '/jc/user' ||
                this.$route.path == '/jc/bf' || this.$route.path == '/jc/gd' ? this.$store.state.clientHeight - 55 : this.$store.state.clientHeight
            }
        },
        methods:{
            tabClick(href,name){
                this.$router.push({
                    path: href,
                    meta:{
                        title:name,
                    }
                });
            }
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .mint-tabbar.is-fixed , .mint-tabbar{background-color: #ffffff}
    .iconfont{font-size: 22px;}
    .mint-tabbar {
        background-image: -webkit-linear-gradient(top, #e5e5e5, #e5e5e5 50%, transparent 50%);
        background-image: linear-gradient(180deg, #e5e5e5, #e5e5e5 50%, transparent 50%);
    }
    .mint-tab-item{
        position: relative;
    }
    .mint-tabbar > .mint-tab-item.is-selected{
        color: #ff2100;
        i{
            @include linear-gradient-deg135(#ff8831, #ff2100);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    }
    .mint-tab-item-icon > *{
        line-height: 24px;
    }
    .mint-tabbar > .mint-tab-item.is-selected{
        background: none;
    }
</style>
