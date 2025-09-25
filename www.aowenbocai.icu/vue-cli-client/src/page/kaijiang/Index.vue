<template>
    <div>
        <mt-header title="开奖信息"></mt-header>
        <div class="contentH" :style="{height: contentH + 'px'}">
            <div class="card card">
                <news-prize/><!--滚动公告--中奖快讯-->
            </div>
            <div class="mt mf">
            <div class="kj-list card border-bottom-1px" v-for="(item,index) in list" :key="index">
                <ul>
                    <router-link tag="li" :to="'/viewCode?name=' + item.link + '&title=' + item.name + '&cz=' + item.cz + '&expectType=' + item.expect_type" class="mint-cell-allow-right">
                        <p>{{item.name}}
                            <template v-if="item.code.length > 1">
                                <em class="f-mini c-3">第{{item.expect}}期</em>
                                <em class="f-mini c-3">{{item.create_time}}</em>
                            </template>
                        </p>
                        <template v-if="item.code.length > 1">
                            <p class="code" v-if="item.cz !== 'ks' && item.cz !== 'pc28'"><em class="ball" v-for="(num,index) in item.code" :key="index">{{num}}</em></p>
                            <p class="code" v-if="item.cz == 'ks'">
                                <span class="open-dice" :class="'dice' + item.code[0]"></span>
                                <span class="open-dice" :class="'dice' + item.code[1]"></span>
                                <span class="open-dice" :class="'dice' + item.code[2]"></span>
                            </p>
                            <p class="code" v-if="item.cz == 'pc28'">
                                <em class="ball">{{item.code[0]}}</em>
                                <em>+</em>
                                <em class="ball">{{item.code[1]}}</em>
                                <em>+</em>
                                <em class="ball">{{item.code[2]}}</em>
                                <em>=</em>
                                <em class="ball" style="background-color: #2196F3">{{Number(item.code[0]) + Number(item.code[1]) + Number(item.code[2])}}</em>
                            </p>
                        </template>
                        <template v-else>
                            <p style="height: 28px;line-height: 28px" class="c-3 f-sm code">暂无开奖号码</p>
                        </template>
                    </router-link>
                </ul>
            </div>
            </div>
        </div>
    </div>
</template>

<script>
    import NewsPrize from "components/common/NewsPrize.vue"; //中奖快讯
    export default {
        name:'kaijiang',
        data() {
            return {
                subVal: 95
            };
        },
        components: {
            NewsPrize
        },
        computed:{
            list(){
                return this.$store.state.kaijiang
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - this.subVal
            }
        },
        methods:{
            chose(){
                this.subVal = 120
            }
        },
        activated(){
            this.$store.dispatch('getKaijiang');
        }
    };
</script>

<style lang="scss" scoped type="text/scss">
    .kj-list{
        li{
           padding: 10px;
           .code{margin:15px 0 5px}
        }
    }
    .open-dice{
        display: inline-block;
        width: 26px;
        height: 26px;
        text-align: center;
        background: url(~assets/images/m_open_num.png) no-repeat top left;
        background-size: 200% 600%;
        margin-top: 2px;
    }
    $height:-26px;
    .dice1{
        background-position:0 0;
    }
    .dice2{
        background-position:0 $height;
    }
    .dice3{
        background-position:0 $height * 2;
    }
    .dice4{
        background-position:0 $height * 3;
    }
    .dice5{
        background-position:0 $height * 4;
    }
    .dice6{
        background-position:0 $height * 5;
    }
</style>
