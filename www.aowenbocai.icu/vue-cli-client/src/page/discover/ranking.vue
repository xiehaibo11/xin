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
                    <router-link to="/discover/winprize" tag="li">中奖快讯</router-link>
                    <li class="active">排行榜</router-link></li>
                </ul>
            </h1>
            <div class="mint-header-button is-right"></div>
        </header>
        <div>
            <ul class="card rank-th border-bottom-1px">
                <li class="flex-box">
                    <div class="rank">排名</div>
                    <div class="name">用户名</div>
                    <div class="money tr">累计中奖金额</div>
                </li>
            </ul>
        </div>
        <div class="contentH" :style="{height: contentH + 'px'}">
            <ul class="card rank-item">
                <li v-for="(item,index) in listData" class="flex-box prize-list border-bottom-1px">
                    <div class="rank">
                        <em v-if="index==0">
                            <svg slot="icon" class="icon" aria-hidden="true">
                                <use xlink:href="#icon-guanjun"></use>
                            </svg>
                        </em>
                        <em v-if="index==1">
                            <svg slot="icon" class="icon" aria-hidden="true">
                                <use xlink:href="#icon-yajun"></use>
                            </svg>
                        </em>
                        <em v-if="index==2">
                            <svg slot="icon" class="icon" aria-hidden="true">
                                <use xlink:href="#icon-jijun"></use>
                            </svg>
                        </em>
                        <em class="rank-num" v-if="index>2">{{index+1}}</em>
                    </div>
                    <div class="name">
                        <div class="flex-box">
                            <em class="user-photo" v-if="item.photo" :style="{backgroundImage:'url(' + item.photo + ')'}"></em>
                            <em style="word-break: break-all;">{{item.nickname}}</em>
                        </div>
                    </div>
                    <div class="money tr"><em class="red">{{item.award}}</em>{{lotteryUnit}}</div>
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
            //栏目id
            newNavClassId(){
                return this.$store.state.newNavClassId
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - 75
            },
            listData(){
                return this.$store.state.rankingList
            },
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit
            },
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
            this.$store.dispatch('getRankingList')
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .user-photo{
        width: 32px;
        height: 32px;
    }
    .icon {
        margin-right: 10px;
        width: 32px;
        height: 32px;
        fill: currentColor;
        overflow: hidden;
    }
    .rank{
        width: 15%;
        .rank-num{
            display: inline-block;
            width: 24px;
            line-height: 24px;
            @include rounded(50%);
            background-color: #cccfd0;
            color: #ffffff;
            text-align: center;
            margin-left: 5px;
        }
    }
    .name{
        width: 40%;
    }
    .money{
        width: 45%;
    }
    .rank-item{
        padding: 0 10px;
        li{
            font-size: 14px;
            height: 47px;
            color: #666666;
        }
    }
    .rank-th{
        padding: 0 10px;
        height: 35px;
        line-height: 35px;
        li{
            color: #000000;
            font-size: 14px;
        }
    }
</style>
