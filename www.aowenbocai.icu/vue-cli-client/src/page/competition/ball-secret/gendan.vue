<template>
    <div>
        <header class="mint-header">
            <div class="mint-header-button is-left"></div>
            <h1 class="mint-header-title">
                <ul class="top-tab cf">
                    <li :class="{'active' : selected == 1}" @click="selected=1">跟单</li>
                    <li :class="{'active' : selected == 2}" @click="selected=2">合买</li>
                </ul>
            </h1>
            <div class="mint-header-button is-right">
                <!--<span class="flex-box search-ls">-->
                <!--<i class="mintui mintui-search"></i>-->
                <!--<em class="f-sm">联赛</em>-->
                <!--</span>-->
            </div>
        </header>
        <header class="mint-header tab-header" style="margin-top: -1px">
            <div class="mint-header-button is-left tab-header-left">
                <span class="active" @click="choseTab('/jc/gd')">足球</span>
                <span @click="choseTab('/jc/join')" v-if="joinIsOpen">篮球</span>
            </div>
            <div class="mint-header-button is-right">
                <router-link to="/jc/search?type=gd"><i class="iconfont icon-sousuo"></i></router-link>
                <!--<i style="padding-left: 10px" class="iconfont icon-jingshi"></i>-->
            </div>
        </header>
        <div class="contentH" :style="{height: contentH + 'px'}" ref="wrapper">
            <!--大神推荐-->
            <div class="card mt-sm">
                <div class="border-bottom-1px hot-person-title"><i class="iconfont icon-tuijian-copy org f-large"></i>大神推荐</div>
                <ul class="hot-person-list cf">
                    <template v-for="(item,index) in gdHotPerson">
                        <li @click="viewUserRecord(item.userid)">
                            <p class="photo" :style="{backgroundImage:'url(' + item.photo + ')'}"></p>
                            <p class="name">{{item.nickname}}</p>
                            <mt-badge size="small" type="error" v-if="item.order_num>0">{{item.order_num}}</mt-badge>
                            <p class="red f-mini">{{item.sl}}</p>
                        </li>
                    </template>
                </ul>
            </div>
            <!--大神推荐 end-->
            <!--跟单列表-->
            <page-item :url="url" class="gdList">
                <template slot-scope="props">
                    <div v-for="(item,index) in props.data" :key="index" class="gendan-list">
                        <div class="gandan-list-header flex-box">
                            <a class="flex-box tl" @click="viewUserRecord(item.userid)">
                                <span class="photo" :style="{backgroundImage:'url(' + '/uploads/personal/1.png?t=1522652072' + ')'}"></span>
                                <span class="name">{{item.nickname}}</span>
                            </a>
                            <span class="sl f-sm c-3">胜率: <em class="red">0.00%</em></span>
                        </div>
                        <div class="gandan-list-xy">跟我一起中大奖！！！</div>
                        <div class="gandan-list-info flex-box">
                            <div class="info-table border-right-1px">
                                <p class="border-bottom-1px">
                                    <span class="c-3 f-sm">类型</span>
                                    <span class="c-3 f-sm">自购金额</span>
                                    <span class="c-3 f-sm">单倍金额</span>
                                </p>
                                <p class="c-1">
                                    <span>竞彩足球</span>
                                    <span>500{{lotteryUnit}}</span>
                                    <span>10.00</span>
                                </p>
                            </div>
                            <div class="gendan-btn"><router-link to="/jc/gd/detail">跟单</router-link></div>
                        </div>
                        <div class="gandan-list-time tr">
                            <em class="f-mini c-3">截止: {{item.create_time}}</em>
                        </div>
                    </div>
                </template>
            </page-item>
            <!--跟单列表 end-->
        </div>
    </div>
</template>

<script>
    import pageItem from 'components/common/PageItem.vue'
    export default {
        name: 'jcGendan',
        components: {
            pageItem,
        },
        data() {
            return {
                selected:'1',
                url:'/web/details/list',
            }
        },
        computed:{
            joinIsOpen(){
                return this.$store.state.setting.join_isOpen
            },
            //列表高度
            contentH(){
                return this.$store.state.clientHeight - 135
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //跟单红人
            gdHotPerson(){
                return[
                    {nickname:'怪咖',photo:"/uploads/personal/1.png?t=1522652072",order_num:0,userid:1,sl: '120%'},
                    {nickname:'书生',photo:"/uploads/personal/1.png?t=1522652072",order_num:1,userid:2,sl: '150%'},
                    {nickname:'张三',photo:"/uploads/personal/1.png?t=1522652072",order_num:0,userid:3,sl: '160%'},
                    {nickname:'李四',photo:"/uploads/personal/1.png?t=1522652072",order_num:4,userid:4,sl: '560%'},
                    {nickname:'王五',photo:"/uploads/personal/1.png?t=1522652072",order_num:0,userid:5,sl: '800%'},
                    {nickname:'马六',photo:"/uploads/personal/1.png?t=1522652072",order_num:2,userid:6,sl: '120%'},
                    {nickname:'马六',photo:"/uploads/personal/1.png?t=1522652072",order_num:0,userid:7,sl: '120%'},
                    {nickname:'马六',photo:"/uploads/personal/1.png?t=1522652072",order_num:0,userid:8,sl: '120%'}
                ]
            },
        },
        methods:{
            //顶部tab切换
            choseTab(n){
                this.$router.push({
                    path:n
                })
            },
            //查看用户战绩
            viewUserRecord(id){
                this.$router.push({
                    path:'/jc/userRecord',
                    query:{
                        id : id
                    }
                })
            }
        },
        created(){
            this.$store.commit('setKeepAlivePage','jcGendan')
        },
        beforeRouteLeave(to, from, next){
            if(to.path == '/jc/gd/detail' || to.path == '/jc/userRecord' ||  to.path == '/jc/search'){
                this.$store.commit('setPageLoad',false)   //禁止滚动加载事件
                this.$store.commit('setPageYOffset', this.$refs.wrapper.scrollTop)
                this.$store.commit('setKeepAlivePage','jcGendan')
            }else {
                this.$store.commit('delKeepAlivePage','jcGendan')
            }
            next();
        },
        activated(){
            this.$refs.wrapper.scrollTop = this.$store.state.pageYOffset;
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .tab-header{
        .tab-header-left{
            span{
                display: inline-block;
                height: 38px;
                line-height: 38px;
                padding: 0 10px;
                &.active{
                    border-bottom:2px solid #ffffff;
                    color: #fdd9d9;
                }
            }
        }
    }
    .hot-person-title{
        padding:10px 5px;
        font-weight: 400;
        color: #000000;
    }
    .hot-person-list{
        padding: 5px 0 15px 0;
        li{
            float: left;
            width: 25%;
            text-align: center;
            position: relative;
            .photo{
                width: 50px;
                height: 50px;
                @include rounded(50%);
                background-repeat: no-repeat;
                background-position: center;
                background-size: cover;
                //border:2px solid #ffffff;
                //@include shadow(0,0,5px,#888);
                margin: 10px auto 5px;
            }
            .mint-badge{
                position: absolute;
                right: 18px;
                top: 8px;
            }
            .mint-badge.is-error {
                background-color: #FF5722;
            }
            .mint-badge.is-size-small {
                font-size: 12px;
                padding: 0;
                width: 16px;
                height: 16px;
                line-height: 16px;
                @include rounded(50%);
                border: 1px solid #fff;
            }
            .name{
                font-size: 15px;
                line-height: 1;
            }
        }
    }
</style>
