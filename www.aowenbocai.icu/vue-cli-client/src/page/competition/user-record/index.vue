<template>
    <div :style="{width: contentW + 'px'}" style="overflow-x: hidden">
        <div class="user-record-header">
            <div class="record-header-box">
                <div class="icon-back">
                    <i class="mintui mintui-back" slot="left" @click="$router.goBack(-1)"></i>
                </div>
                <div class="user-record-info tc">
                    <p class="photo" :style="{backgroundImage:'url(' + userinfo.photo + ')'}"></p>
                    <p class="name">{{userinfo.nickname}}</p>
                </div>
            </div>
        </div>
        <div class="user-record-tab flex-box">
            <span class="flex tc" :class="{'active':selected == 1}" @click="selected='1'">战绩</span>
            <span class="flex tc" :class="{'active':selected == 2}" @click="selected='2'">发单</span>
        </div>
        <div class="contentH" :style="{height: contentH + 'px'}" ref="wrapper">
            <div class="record-cont" v-if="selected == 1">
                <div class="record-max">三天最高回报榜 NO.18</div>
                <div class="record-statistics">
                    <div class="s-title">竞足战绩</div>
                    <div class="s-cont">
                        <ul class="cf">
                            <li>
                                <p class="red">89%</p>
                                <p class="f-mini c-3">近三天胜率</p>
                            </li>
                            <li>
                                <p class="red">159%</p>
                                <p class="f-mini c-3">近三天回报率</p>
                            </li>
                            <li>
                                <p class="red">--</p>
                                <p class="f-mini c-3">当前连红</p>
                            </li>
                            <li>
                                <p class="red">80%</p>
                                <p class="f-mini c-3">近七天胜率</p>
                            </li>
                            <li>
                                <p class="red">131%</p>
                                <p class="f-mini c-3">近七天回报率</p>
                            </li>
                            <li>
                                <p class="red">9</p>
                                <p class="f-mini c-3">历史连红</p>
                            </li>
                        </ul>
                    </div>
                    <div class="s-title mt-sm">足彩战绩</div>
                    <div class="s-cont">
                        <ul class="cf">
                            <li>
                                <p class="red">1注</p>
                                <p class="f-mini c-3">胜负彩一等奖</p>
                            </li>
                            <li>
                                <p class="red">15注</p>
                                <p class="f-mini c-3">胜负彩二等奖</p>
                            </li>
                            <li>
                                <p class="red">6注</p>
                                <p class="f-mini c-3">任九一等奖</p>
                            </li>
                        </ul>
                    </div>
                    <div class="s-title mt-sm">竞篮战绩</div>
                    <div class="s-cont">
                        <ul class="cf">
                            <li>
                                <p class="red">89%</p>
                                <p class="f-mini c-3">近三天胜率</p>
                            </li>
                            <li>
                                <p class="red">159%</p>
                                <p class="f-mini c-3">近三天回报率</p>
                            </li>
                            <li>
                                <p class="red">--</p>
                                <p class="f-mini c-3">当前连红</p>
                            </li>
                            <li>
                                <p class="red">80%</p>
                                <p class="f-mini c-3">近七天胜率</p>
                            </li>
                            <li>
                                <p class="red">131%</p>
                                <p class="f-mini c-3">近七天回报率</p>
                            </li>
                            <li>
                                <p class="red">9</p>
                                <p class="f-mini c-3">历史连红</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--跟单列表-->
            <page-item2 :url="url" v-if="selected == 2">
                <template slot-scope="props">
                    <div v-for="(item,index) in props.data" :key="index" class="gendan-list">
                        <div class="gandan-list-header flex-box c-3 f-sm">
                            {{item.create_time}} 发起方案
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
                            <div class="gendan-btn">
                                <template v-if="0">
                                    <p class="c-3 f-sm">奖金({{lotteryUnit}})</p>
                                    <p class="c-3">0.00</p>
                                </template>
                                <template v-else>
                                    <router-link to="/jc/gd/detail">跟单</router-link>
                                </template>
                            </div>
                        </div>
                        <div class="gandan-list-time tr" v-if="1">
                            <em class="f-mini c-3">截止: {{item.create_time}}</em>
                        </div>
                    </div>
                </template>
            </page-item2>
            <!--跟单列表 end-->
        </div>
    </div>
</template>

<script>
    import pageItem2 from 'components/common/PageItem2.vue'
    export default {
        name: 'userRecord',
        components: {
            pageItem2,
        },
        data () {
            return {
                selected: '1',
                url:'/web/details/list',
                userinfo:{"id":1,"username":"admin","type":2,"money":30784580,"game_money":300000,"explan":"这个签名屌423","email":"15****22@qq.com","nickname":"超级大超级大超级大超","photo":"\/uploads\/personal\/1.jpg?t=1522652072","fristLogin":0,"tel":"151****2354","rebate":{"ssc":"10","ks":"10","syxw":"9","pk10":"10","pc28":"5"},"sex":"2","birth":"1990-01-01"}
            }
        },
        watch:{
            selected(val){
                this.$refs.wrapper.scrollTop = 0
            }
        },
        computed:{
            //列表高度
            contentH(){
                return this.$store.state.clientHeight - 200
            },
            contentW(){
                return this.$store.state.clientWidth
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            isShow(){
                return this.selected == 2 ? true : false
            }
        },
        created(){
            this.$store.commit('setKeepAlivePage','userRecord')
        },
        beforeRouteLeave(to, from, next){
            if(to.path == '/jc/gd/detail'){
                this.$store.commit('setPageLoad2',false)   //禁止滚动加载事件
                this.$store.commit('setPageYOffset2', this.$refs.wrapper.scrollTop)
                this.$store.commit('setKeepAlivePage','userRecord')
            }else {
                this.$store.commit('delKeepAlivePage','userRecord')
            }
            next();
        },
        activated(){
            this.$refs.wrapper.scrollTop = this.$store.state.pageYOffset2;
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .user-record-header{
        $height:160px;
        margin:auto;overflow:hidden;
        height: $height;
        background-color: #fff;
        .record-header-box{
            position: relative;
            z-index: 10;
        }
        &:after{
            width: 180%;
            height: $height;
            position: absolute;
            left: -40%;
            top:0;
            z-index: 0;
            content: '';
            border-radius: 0 0 50% 50%;
            @include linear-gradient-deg90(#a22118, #bb271c);
        }
        .icon-back{
            padding: 0 10px;
            i{
                display: inline-block;
                color: #ffffff;
                width: 40px;
                height: 40px;
                line-height: 40px;
            }
        }
        .user-record-info{
            .photo{
                width: 70px;
                height: 70px;
                margin: 0 auto 10px;
                border-radius: 100%;
                //@include shadow(0,0,8px, lighten($bColor,30%));
                /*border:3px solid #ffffff;*/
                background-size: cover;
            }
            .name{
                color: #ffffff;
                margin: 5px 0;
            }
        }
    }
    .user-record-tab{
        $h:40px;
        height: $h;
        background-color: #ffffff;
        border-bottom: 1px solid #ececec;
        span{
            position: relative;
            display: inline-block;
            height: $h;
            line-height: $h;
            &.active:after{
                width: 20px;
                height: 3px;
                @include rounded(1.5px);
                position: absolute;
                left: 50%;
                margin-left: -10px;
                bottom:0;
                z-index: 0;
                content: '';
                @include linear-gradient-deg90(#a22118, #bb271c);
            }
        }
    }
    .record-cont{
        background-color: #ffffff;
        .record-max{
            background-color: #fbf4e0;
            color: #b39c74;
            padding: 0 10px;
            font-size: 12px;
            height: 32px;
            line-height: 32px;
        }
        .record-statistics{
            background-color: #ffffff;
            padding: 0 15px;
            .s-title{
                height: 35px;
                line-height: 35px;
            }
            .s-cont{
                ul{
                    li{
                        padding: 10px 0;
                        float: left;
                        width: 31.3%;
                        margin-bottom: 10px;
                        margin-right: 3%;
                        background-color: #f8f8f8;
                        text-align: center;
                        max-width: 150px;
                        border:1px solid #f3f3f3;
                        &:nth-child(3n+3){
                            margin-right: 0;
                        }
                    }
                }
            }
        }
    }
</style>
