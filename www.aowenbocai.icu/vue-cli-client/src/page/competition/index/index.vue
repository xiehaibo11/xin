<template>
    <div>
        <header class="mint-header jc-index-header">
            <div class="mint-header-button is-left">
                <a :href="downUrl" slot="left" target="_blank" v-if="!getIsApp && downUrl">
                    APP<i class="iconfont icon-xiazai2"></i>
                </a>
            </div>
            <h1 class="mint-header-title"><img :src="logo" alt="" height="30"></h1>
            <div class="mint-header-button is-right" v-if="qqOnline">
                <router-link to="/onLine" tag="div">
                    <span class="f-sm tr" style="color: #e50e03">
                        <b class="iconfont icon-zaixiankefu" style="padding-right: 2px"></b><em>在线客服</em>
                    </span>
                </router-link>
            </div>
        </header>
        <div class="contentH" :style="{height: contentH + 'px'}" ref="wrapper">
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
                <NewsPrize/><!--滚动公告--中奖快讯-->
            </div>
            <div class="con_title border-bottom-1px">
                <div class="c_title">热门推荐</div>
            </div>
            <div class="index-list clearfloat card">
                <div class="index-list-group">
                    <a v-for="(value, key) in jcGameList" :key="key" class="list-item" @click="toLottery(value.pause,value.name)">
                        <div class="list-item-image">
                            <img :src="value.image" :alt="value.title">
                        </div>
                        <div class="list-item-text">
                            <div class="title" style="position: relative">{{value.title}}
                                <i v-if="value.todayIsOpen" class="iconfont icon-jinrikaijiang red" style="font-size: 28px;position: absolute;left:50%;margin-left: 13px"></i></div>
                            <mt-badge v-if="value.pause" size="small" color="#888">暂停销售</mt-badge>
                            <div class="memo" v-else>{{value.remark}}</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="con_title border-bottom-1px">
                <div class="c_title">热门资讯 <router-link :to="'/news/news?navid='+ newNavClassId.news_id" class="fr f-sm">更多+</router-link></div>
            </div>
            <div class="mf-sm">
                <ul class="hot-news">
                    <li v-for="(item,index) in hotNews" :key="index" class="border-bottom-1px" @click="toDetail(item.id,item.nav_id,index)" :class="{'readed': readArr.indexOf(index) > -1}">
                        <a class="flex-box">
                            <div class="desc flex">
                                <h3>{{item.title}}</h3>
                                <div class="desc-info">
                                    <span class="pul-time">{{item.create_time}}</span>
                                    <!--<span class="comm-num">0评论</span>-->
                                    <!--<span class="icon-tg">大神推单</span>-->
                                </div>
                            </div>
                            <div class="list-img" v-if="item.pic" :style="{backgroundImage:'url(' + item.pic + ')'}"></div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import NewsPrize from "components/common/NewsPrize.vue"; //中奖快讯

    export default {
        name:'jcIndex',
        components: {
            NewsPrize
        },
        data() {
            return {
                readArr:[] //已读数组
            };
        },
        computed: {
            //栏目id
            newNavClassId(){
                return this.$store.state.newNavClassId
            },
            //在线客服
            qqOnline(){
                return this.$store.state.setting.wap_online_qq
            },
            //下载地址
            downUrl(){
                return this.$store.state.setting.moblie_domain
            },
            //是否在app中
            getIsApp () {
                var ua = navigator.userAgent.toLowerCase();
                if (ua.match(/isapp/i) == "isapp") {
                    return true;
                }
                return false;
            },
            logo(){
                return this.$store.state.setting.logo_url
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
            //竞彩游戏列表
            jcGameList(){
                return [
                    {id: 301, name: "jczq", title: "竞彩足球", image: "/static/vipweb/images/wap/jczq.png", remark: "返奖率高达73%",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:0},
                    {id: 302, name: "jczq?type=dg", title: "竞足单关", image: "/static/vipweb/images/wap/jczqdg.png", remark: "猜中一场就中奖",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:0},
                    {id: 302, name: "jczq?type=gy", title: "冠军/冠亚军", image: "/static/vipweb/images/wap/jczqdg.png", remark: "猜中一场就中奖",sort: 0,status: 0,type: 3,pause: 1,todayIsOpen:0},
                    {id: 303, name: "jclq", title: "竞彩篮球", image: "/static/vipweb/images/wap/jclq.png", remark: "固赔更高返奖",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:0},
                    {id: 304, name: "jclq?type=dg", title: "竞篮单关", image: "/static/vipweb/images/wap/jclqdg.png", remark: "猜胜负易中奖",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:0},
                    {id: 305, name: "sfc", title: "胜负彩", image: "/static/vipweb/images/wap/sfc.png", remark: "2元可中500万",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:0},
                    {id: 306, name: "rx9", title: "任选9", image: "/static/vipweb/images/wap/rx9.png", remark: "小单收获大奖",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:0},
                    {id: 306, name: "bjdc", title: "北京单场", image: "/static/vipweb/images/wap/bjdc.png", remark: "赢长串，奖金高",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:0},
                    {id: 307, name: "dlt", title: "大乐透", image: "/static/vipweb/images/wap/dlt.png", remark: "3元可中1800万",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:1},
                    {id: 307, name: "pls", title: "排列3", image: "/static/vipweb/images/wap/pls.png", remark: "简单3位赢千元",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:1},
                    {id: 307, name: "plw", title: "排列5", image: "/static/vipweb/images/wap/plw.png", remark: "2元赢取10万元",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:0},
                    {id: 307, name: "qxc", title: "七星彩", image: "/static/vipweb/images/wap/qxc.png", remark: "2元可中500万",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:0},
                    {id: 307, name: "ssq", title: "双色球", image: "/static/vipweb/images/wap/ssq.png", remark: "2元可中500万",sort: 0,status: 0,type: 3,pause: 0,todayIsOpen:1},
                ]
            },
            //热门资讯
            hotNews(){
                return [
                    {content_index: "开奖时间：11选5投注区号码范围为01～11", create_time: "1分钟前",id: 60,nav_id: 2,pic: '//img.500.com/upimages/openplatform/2018/0916/1537085001858.jpg',read_count: 0,title: "锶含竞彩 【1月29日重心资源】"},
                    {content_index: "开奖时间：11选5投注区号码范围为01～11", create_time: "5分钟前",id: 61,nav_id: 2,pic: '//img.500.com/upimages/openplatform/2019/0102/1546398172109.jpg',read_count: 0,title: "近期11中9！第二弹冲300%收益！点关注上车不迷路！"},
                    {content_index: "开奖时间：11选5投注区号码范围为01～11", create_time: "5分钟前",id: 63,nav_id: 2,pic: null,read_count: 0,title: "红单竞彩：【近16中13】英甲强胆推荐：牛津联VS巴恩斯利"},
                ]
            }
        },
        methods:{
            toLottery(pause,name){
                let path = name=='sfc' || name == 'rx9' ? '/zucai?name='+ name : '/' + name
                if(!pause){
                    this.$router.push({
                        path: path
                    })
                }
            },
            toDetail(id,navid,i){ //进入文章详情
                this.$store.commit('setPageYOffset', this.$refs.wrapper.scrollTop)
                if(this.readArr.indexOf(i) == -1){
                    this.readArr.push(i)
                }
                this.$router.push({
                    path:'/news/detail',
                    query:{
                        id:id,
                        navid:navid,
                    }
                })
            }
        },
        created(){
            this.$store.dispatch('getBanner');
            this.$store.commit('setKeepAlivePage','jcIndex')
        },
        //进入详情页后禁止滚动加载事件
        beforeRouteLeave(to, from, next){
            if(to.path =='/news/detail'){
                this.$store.commit('setKeepAlivePage','jcIndex')
            }else {
                this.$store.commit('delKeepAlivePage','jcIndex')
            }
            next();
        },
        activated(){
            this.$refs.wrapper.scrollTop = this.$store.state.pageYOffset;
        }
    };
</script>

<style lang="scss" scoped type="text/scss">
    .jc-index-header{
        background-color: #fff;
        color: $bColor;
        border-bottom: 1px solid #eeeeee;
    }
    .mint-badge.is-size-small{
        padding: 0 6px;
    }
    .mint-swipe{height:175px}
    .con_title{
        font-size:16px;
        padding:10px 0;
        background-color: #ffffff;
        margin-top: 10px;
        .c_title{
            position: relative;
            padding: 0 10px;
            color: #000000;
            &:after{
                content: '';
                display: block;
                width: 2px;
                height: 18px;
                background-color: #e50e03;
                position: absolute;
                left: 0;
                top: 1px;
            }
        }
    }
    .hot-news{
        padding: 0 5px;
        background-color: #ffffff;
        li{
            a{
                padding: 10px;
                .desc{
                    h3{
                        font-size:16px;
                        font-weight: 400;
                        line-height: 1.4;
                        overflow: hidden;
                        word-spacing: normal;
                        -webkit-line-clamp: 2;
                        text-overflow: ellipsis;
                        display: -webkit-box;
                        color: #333333;
                        min-height: 48px;
                    }
                    .desc-info{
                        color: #999;
                        font-size: 12px;
                    }
                }
                .list-img{
                    width: 93px;
                    height: 60px;
                    background-size: cover;
                    background-repeat: no-repeat;
                    background-position: center;
                }
            }
        }
    }
</style>
