<!--中奖快讯-->
<template>
    <div class="detail">
        <header class="mint-header">
            <div class="mint-header-button is-left"  @click="goBack">
                <button class="mint-button mint-button--default mint-button--normal">
                    <span class="mint-button-icon"><i class="mintui mintui-back"></i></span>
                </button>
            </div>
            <h1 class="mint-header-title">
                <ul class="top-tab cf">
                    <li class="active">中奖快讯</li>
                    <router-link to="/discover/ranking" tag="li">排行榜</router-link>
                </ul>
            </h1>
            <div class="mint-header-button is-right"></div>
        </header>
        <div class="contentH" :style="{height: contentH + 'px'}">
            <ul class="prize-item">
                <li v-for="(item,index) in listData" class="flex-box prize-list border-bottom-1px card">
                    <div class="user-photo" v-if="item.photo" :style="{backgroundImage:'url(' + item.photo + ')'}"></div>
                    <div>
                        <p><em class="abled">{{item.nickname}}</em> 在{{item.lotteryName}}</p>
                        <p>喜中 <em class="red">{{item.money}}{{lotteryUnit}}</em></p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'winPrize',
        data () {
            return {
            }
        },
        computed:{
            listData(){
                return this.$store.state.winPrizeList
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
            //栏目id
            newNavClassId(){
                return this.$store.state.newNavClassId
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - 40
            }
        },
        methods:{
            goBack(){
                this.$router.isBack = true
                this.$router.push({
                    path:'/discover'
                })
            }
        },
        created(){
            this.$store.dispatch('getPrizeList')
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .prize-item{
        li{
            padding:10px;
            font-size: 14px;
            .user-photo{
                margin-right: 10px;
            }
        }
    }
</style>
