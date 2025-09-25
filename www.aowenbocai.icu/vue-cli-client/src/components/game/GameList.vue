<template>
    <div>
        <div v-if="lotteryStatus">
            <div class="title-name"><i class="iconfont icon-biaoti"></i>彩票游戏</div>
            <div class="index-list clearfloat card">
                <div class="index-list-group">
                    <a v-for="(value, key) in lottery.slice(0,11)" :key="key" @click="toLottery(value.name)" class="list-item">
                        <div class="list-item-image">
                            <img :src="value.image" :alt="value.title">
                        </div>
                        <div class="list-item-text">
                            <div class="title">{{value.title}}</div>
                            <mt-badge v-if="value.pause" size="small" type="error">暂停销售</mt-badge>
                            <div class="memo" v-else>{{value.remark}}</div>
                        </div>
                    </a>
                    <router-link to="lotteryMore" v-if="lottery.length > 11">
                        <div class="list-item-image">
                            <i class="iconfont icon-gengduo2" style="font-size: 60px;line-height: 1;color: #d6d6d6;"></i>
                        </div>
                        <div class="list-item-text">
                            <div class="title">更多彩种</div>
                            <div class="memo">查看其它</div>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
        <div class="mf" v-if="gameStatus">
            <div class="title-name"><i class="iconfont icon-biaoti"></i>休闲游戏</div>
            <div class="index-list clearfloat card">
                <div class="index-list-group">
                    <a @click="toGame(value.name,value.pause)" v-for="(value, key) in list.slice(0,8)" :key="key" class="list-item">
                        <div class="list-item-image" style="height: 68px">
                            <img :src="value.image" :alt="value.title">
                        </div>
                        <div class="list-item-text">
                            <div class="title">{{value.title}}</div>
                            <mt-badge v-if="value.pause" size="small" color="#888">暂停销售</mt-badge>
                            <div class="memo" v-else>{{value.remark}}</div>
                        </div>
                    </a>
                    <router-link to="gameMore" v-if="list.length > 8" class="list-item">
                        <div class="list-item-image" style="height: 68px">
                            <i class="iconfont icon-gengduo2" style="font-size: 60px;line-height: 1;color: #d6d6d6;"></i>
                        </div>
                        <div class="list-item-text">
                            <div class="title">更多游戏</div>
                            <div class="memo">查看其它</div>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
            };
        },
        computed: {
            list() {
                return this.$store.state.gameList;
            },
            lottery(){
                return this.$store.state.lotteryList;
            },
            //游戏开关
            gameStatus(){
                return Number(this.$store.state.setting.game_status)
            },
            //彩票开关
            lotteryStatus(){
                return Number(this.$store.state.setting.lottery_status)
            }
        },
        methods:{
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
        }
    };

</script>
<style lang="scss" scoped type="text/scss">
    .mint-badge.is-size-small{
        padding: 0 6px;
    }
</style>
