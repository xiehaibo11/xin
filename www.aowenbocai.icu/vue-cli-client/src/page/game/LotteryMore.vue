<template>
    <div>
        <header class="mint-header">
            <div class="mint-header-button is-left"  @click="goBack">
                <button class="mint-button mint-button--default mint-button--normal">
                    <span class="mint-button-icon"><i class="mintui mintui-back"></i></span>
                </button>
            </div>
            <h1 class="mint-header-title">
                <template v-if="gameStatus && lotteryStatus">
                    <ul class="top-tab cf">
                        <li class="active">彩票游戏</li>
                        <router-link to="/gameMore" tag="li" v-if="gameStatus">休闲游戏</router-link>
                    </ul>
                </template>
                <template v-else>{{title}}</template>
            </h1>
            <div class="mint-header-button is-right"></div>
        </header>
        <div class="lottery-more-head" >
            <ul class="" ref="wrapper">
                <li @click="cz = 'all'" :class="{'active' : cz == 'all'}">
                    <i class="iconfont icon-quanbu"></i>
                    <p>全部彩种</p>
                </li><li v-for="(item,index) in lotteryList" @click="choseCz(item.type,item.data)" :class="{'active' : cz == item.type}">
                    <i class="iconfont icon-shishicai1" v-if="item.type == 'ssc'"></i>
                    <i class="iconfont icon-xuan" v-if="item.type == 'syxw'"></i>
                    <i class="iconfont icon-kuai1" v-if="item.type == 'ks'"></i>
                    <i class="iconfont icon-xingyun1" v-if="item.type == 'pc28'"></i>
                    <i class="iconfont icon-icon-test" v-if="item.type == 'pk10'"></i>
                    <p>{{item.label}}</p>
                </li>
            </ul>
        </div>
        <div class="contentH" :style="{height: contentH + 'px'}">
            <div class="index-list clearfloat card" style="padding-top: 10px">
                <div class="index-list-group">
                    <template v-if="cz == 'all'">
                        <a v-for="(value, key) in lottery" :key="key" @click="toLottery(value.name)" class="list-item">
                            <div class="list-item-image">
                                <img :src="value.image" :alt="value.title">
                            </div>
                            <div class="list-item-text">
                                <div class="title">{{value.title}}</div>
                                <mt-badge v-if="value.pause" size="small" color="#888">暂停销售</mt-badge>
                                <div class="memo" v-else>{{value.remark}}</div>
                            </div>
                        </a>
                    </template>
                    <template v-else>
                        <a v-for="(value, key) in gameArrList" :key="key" @click="toLottery(value.link)" class="list-item">
                            <div class="list-item-image">
                                <img :src="value.image" :alt="value.title">
                            </div>
                            <div class="list-item-text">
                                <div class="title">{{value.label}}</div>
                                <mt-badge v-if="value.pause" size="small" color="#888">暂停销售</mt-badge>
                                <div class="memo" v-else>{{value.remark}}</div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name:'lotteryMore',
        data() {
            return {
                cz: 'all',
                gameArrList:[],
            };
        },
        computed: {
            title(){
                return this.$route.meta.title
            },
            //分类彩种列表
            lotteryList(){
                return this.$store.state.lotteryShow
            },
            //全部彩种列表
            lottery(){
                return this.$store.state.lotteryList;
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - 105
            },
            //游戏开关
            gameStatus(){
                return Number(this.$store.state.setting.game_status)
            },
            //彩票开关
            lotteryStatus(){
                return Number(this.$store.state.setting.lottery_status)
            },
        },
        methods:{
            choseCz(cz,data){
                this.cz = cz
                this.$set(this,'gameArrList',data)
            },
            //返回
            goBack(){
                this.$router.isBack = true
                this.$router.push({
                    path:'/game'
                })
            },
            toLottery(name){
                let nameA = name.indexOf('/')>-1 ? name.replace("/","") : name;
                let path
                if(name.indexOf('11')>-1){
                    path = 'syxw'
                }else if(name.indexOf('ssc')>-1){
                    path = 'ssc'
                }else if(name.indexOf('10')>-1){
                    path = 'pk10'
                }else if(name.indexOf('28')>-1){
                    path = 'pc28'
                }else if(name.indexOf('ks')>-1){
                    path = 'ks'
                }
                this.$router.push({
                    path:'/'+ path,
                    query:{
                        name:nameA
                    }
                })
            },
        },
        created(){
            this.$nextTick(()=>{
                this.$store.commit('setKeepAlivePage','lotteryMore')
            })
        },
        //返回首页时清除倒计时等
        beforeRouteLeave(to, from, next){
            if(to.path !=='/game'){
                this.$store.commit('setPageYOffset', this.$refs.wrapper.scrollLeft)
                this.$store.commit('setKeepAlivePage','lotteryMore')
            }else {
                this.$store.commit('delKeepAlivePage','lotteryMore')
            }
            next();
        },
        activated(){
            this.$refs.wrapper.scrollLeft = this.$store.state.pageYOffset;
        }
    };
</script>

<style lang="scss" scoped type="text/scss">
    .lottery-more-head{
        background-color: #000000;
        width: 100%;
        ul{
            white-space: nowrap;
            overflow-x: scroll;
            li{
                height: 65px;
                background-color: #232323;
                color: #999999;
                width: 80px;
                display: inline-block;
                text-align: center;
                font-size: 14px;
                i{
                    font-size: 26px;
                    line-height: 1;
                    display: block;
                    margin: 10px 0 2px 0;
                }
                &.active{
                    background-color: #000000;
                    color: #e7e7e7;
                    i{
                        color: #e7e7e7;
                    }
                }
            }
        }
    }
</style>
<style lang="scss" scoped type="text/scss">
    .mint-badge.is-size-small{
        padding: 0 6px;
    }
</style>
