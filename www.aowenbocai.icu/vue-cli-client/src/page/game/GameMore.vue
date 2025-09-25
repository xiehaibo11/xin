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
                        <router-link to="/lotteryMore" tag="li">彩票游戏</router-link>
                        <li class="active">休闲游戏</li>
                    </ul>
                </template>
                <template v-else>{{title}}</template>
            </h1>
            <div class="mint-header-button is-right"></div>
        </header>
        <div class="contentH" :style="{height: contentH + 'px'}">
            <div class="index-list clearfloat card" style="padding-top: 10px">
                <div class="index-list-group">
                    <a @click="toGame(value.name,value.pause)" v-for="(value, key) in list" :key="key" class="list-item">
                        <div class="list-item-image" style="height: 68px">
                            <img :src="value.image" :alt="value.title">
                        </div>
                        <div class="list-item-text">
                            <div class="title">{{value.title}}</div>
                            <mt-badge v-if="value.pause" size="small" color="#888">暂停销售</mt-badge>
                            <div class="memo" v-else>{{value.remark}}</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name:'gameMore',
        data() {
            return {
            };
        },
        computed: {
            title(){
                return this.$route.meta.title
            },
            list() {
                return this.$store.state.gameList;
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - 40
            },
            //彩票开关
            lotteryStatus(){
                return Number(this.$store.state.setting.lottery_status)
            },
            //游戏开关
            gameStatus(){
                return Number(this.$store.state.setting.game_status)
            }
        },
        methods:{
            //返回
            goBack(){
                this.$router.isBack = true
                this.$router.push({
                    path:'/game'
                })
            },
            toGame(name,pause){
                if(!pause){
                    this.$router.push({
                        path:'/iframe',
                        query:{
                            u:name
                        }
                    })
                }
            }
        },
        created(){
            this.$store.dispatch('getBanner');
        }
    };
</script>

<style lang="scss" scoped type="text/scss">
    .mint-badge.is-size-small{
        padding: 0 6px;
    }
</style>
