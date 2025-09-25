<!--彩票单位转换提示-->
<template>
    <em>1{{unit}}={{scaleLottery}}{{coin}}</em>
</template>

<script>
    export default {
        name: 'unitConvert',
        data () {
            return {
            }
        },
        computed:{
            coin(){ //金豆单位
                return this.$store.state.coinName
            },
            unit(){ //彩票投注单位
                return this.$store.state.setting.lottery_unit
            },
            scaleLottery(){ //彩金：金豆 比例
                return this.$store.state.setting.coin_lottery
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
