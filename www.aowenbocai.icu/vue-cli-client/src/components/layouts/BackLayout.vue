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
        <mt-tabbar class="page-part" v-model="selected" v-if="$route.path == '/join' || $route.path == '/game' ||$route.path == '/kaijiang' || $route.path == '/discover' || $route.path == '/user'|| $route.path == '/activity'" >
            <mt-tab-item  v-for="(value,index) in link" :key="index" :id="value.name" @click.native="tabClick(value.href,value.name)">
                <i slot="icon" class="iconfont" :class="selected == value.name ? value.active : value.icon"></i>{{value.name}}
                <span v-if="index == 4 && msgNum > 0" class="red-point" style="position: absolute;left:50%;margin-left: 4px;top: 5px;"></span><!--用户中心消息提示-->
            </mt-tab-item>
        </mt-tabbar>
        <!--筛选-->
        <mt-popup
            class="notice-popup-box"
            v-model="noticeVisible"
            :closeOnClickModal=false
            position="right">
            <div>
                <div class="notice-popup-head">平台公告</div>
                <div class="notice-popup-body contentH" :style="{'height':popupHeight + 'px'}">
                    <ul>
                        <li v-for="(item,index) in prizeData" @click="cur=index" class="border-bottom-1px" :class="{'active': cur==index}">
                            <div class="title"><i class="iconfont icon-hongdian red"></i> {{item.title}}</div>
                            <div v-if="cur == index" class="details">
                                <div v-html="item.content"></div>
                                <div class="tr mt c-3">发布时间：{{item.create_time}}</div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="notice-popup-foot">
                    <div class="f-sm flex-box c-2">
                        <div class="flex-box mr-sm" @click="show = !show"><span class="input-checkbox" :class="{'checkbox-checked': show}"></span>今日不再显示</div>
                        <div><mt-button type="danger" size="small" @click.native="closePopup">关闭</mt-button></div>
                    </div>
                </div>
            </div>
        </mt-popup>
        <!--筛选 end-->
    </div>
</template>
<script>
    export default{
        name: 'Layout',
        data() {
            return {
                noticeVisible : false,
                cur:0,
                show:false
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
                if(this.lotteryStatus){
                    return this.join_isOpen ? [{
                        href: '/join',
                        name: '合买',
                        icon: 'icon-bar-joint',
                        active:'icon-bar-joint-selected'
                    },{
                        href: '/kaijiang',
                        name: '开奖',
                        icon: 'icon-result',
                        active:'icon-result-active'
                    },{
                        href: '/game',
                        name: '大厅',
                        icon: 'icon-youxi',
                        active:'icon-tuijian'
                    },{
                        href: '/discover',
                        name: '发现',
                        icon: 'icon-faxian1',
                        active:'icon-ziyuanmdpi'
                    },{
                        href: '/user',
                        name: '我的',
                        icon: 'icon-ziyuan',
                        active:'icon-ziyuan1'
                    }] : [{
                        href: '/activity?navid='+ this.$store.state.newNavClassId.activity_id,
                        name: '活动',
                        icon: 'icon-huodong1',
                        active:'icon-huodong2'
                    },{
                        href: '/kaijiang',
                        name: '开奖',
                        icon: 'icon-result',
                        active:'icon-result-active'
                    },{
                        href: '/game',
                        name: '大厅',
                        icon: 'icon-youxi',
                        active:'icon-tuijian'
                    },{
                        href: '/discover',
                        name: '发现',
                        icon: 'icon-faxian1',
                        active:'icon-ziyuanmdpi'
                    },{
                        href: '/user',
                        name: '我的',
                        icon: 'icon-ziyuan',
                        active:'icon-ziyuan1'
                    }]
                }else {
                    return [{
                        href: '/activity?navid='+ this.$store.state.newNavClassId.activity_id,
                        name: '活动',
                        icon: 'icon-huodong1',
                        active:'icon-huodong2'
                    },{
                        href: '/discover',
                        name: '发现',
                        icon: 'icon-faxian1',
                        active:'icon-ziyuanmdpi'
                    },{
                        href: '/game',
                        name: '大厅',
                        icon: 'icon-youxi',
                        active:'icon-tuijian'
                    },{
                        href: '/user',
                        name: '我的',
                        icon: 'icon-ziyuan',
                        active:'icon-ziyuan1'
                    }]
                }
            },
            msgNum(){
                return this.$store.getters.getMsgNum
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
                return this.$route.path == '/joinDetail' || this.$route.path == '/activity/detail' ? this.$store.state.clientHeight : this.$store.state.clientHeight - 55
            },

            noticePopupOpen(){
                return Number(this.$store.state.setting.notice_popup_open)
            },
            //公告列表
            prizeData(){
                return this.$store.state.newsPrize
            },
            popupHeight(){
                return this.$store.state.clientHeight - 235
            },
            isShow(){
                if(!localStorage.getItem("m_notice_show_close")){
                    return true
                }else {
                    let data = JSON.parse(localStorage.getItem("m_notice_show_close"))
                    //当前时间戳
                    let curTamp = new Date().getTime();
                    let leftTime = data.leftTamp
                    let setTime = data.setTime
                    return curTamp - setTime > leftTime ? true : false
                }
            }
        },
        watch:{
            prizeData(val){
                if(this.$route.path == '/game'){
                    if(val.length){
                        if(this.noticePopupOpen && this.isShow){
                            setTimeout(()=>{
                                this.noticeVisible = true
                            },500)
                        }
                    }
                }
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
            },
            closePopup(){
                this.noticeVisible = false
                if(this.show){
                    var curDate = new Date();
                    //当前时间戳
                    var curTamp = curDate.getTime();
                    //当前日期
                    var curDay = curDate.toLocaleDateString();
                    var curWeeHours = 0;
                    curWeeHours = new Date(curDay).getTime() - 1;
                    //当日已经过去的时间（毫秒）
                    var passedTamp = curTamp - curWeeHours;
                    //当日剩余时间
                    var leftTamp = 24 * 60 * 60 * 1000 - passedTamp;
                    let data = {
                        leftTamp : leftTamp,
                        setTime : curTamp
                    }
                    localStorage.setItem('m_notice_show_close',JSON.stringify(data))
                }
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

    .notice-popup-box{
        padding: 0;
        width: 90%;
        right: 5%;
        @include rounded(3px);
        .notice-popup-head{
            background-color: $bColor;
            color: #ffffff;
            font-size: 18px;
            text-align: center;
            height: 45px;
            line-height: 45px;
        }
        .notice-popup-body{
            padding: 0 10px;
            ul>li{
                padding: 10px 0;
                font-size: 15px;
                &.active{
                    .title{
                        color: $bColor;
                    }
                }
                .details{
                    padding: 5px;
                    margin-top: 5px;
                    background-color: #f1f1f1;
                    font-size: 12px;
                    img{
                        max-width: 100%;
                        height: auto;
                    }
                }
            }
        }
        .notice-popup-foot{
            border-top:1px solid #eeeeee;
            height: 45px;
            line-height: 45px;
            padding: 0 10px;
        }
    }
</style>
