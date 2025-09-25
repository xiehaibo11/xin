<template>
    <div class="setting">
        <!--<Header/>-->
        <div class="cell">
            <p class="mt-sm">
                <mt-cell title="账户明细" is-link to="/coinDetail">
                    <i slot="icon" class="iconfont icon-zhangdanmingxi-xiugai"></i>
                </mt-cell>
                <mt-cell title="充值明细" is-link to="/rechargeDetail">
                    <i slot="icon" class="iconfont icon-chongzhi1"></i>
                </mt-cell>
                <mt-cell title="兑换明细" is-link to="/exchangeRecord">
                    <i slot="icon" class="iconfont icon-duihuan"></i>
                </mt-cell>
                <mt-cell title="转账明细" is-link to="/transformDetail" v-if="gameStatus">
                    <i slot="icon" class="iconfont icon-jinrongqianbi"></i>
                </mt-cell>
            </p>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
            }
        },
        computed:{
            gameStatus(){
                return this.$store.state.setting.game_status == 1 ? true : false
            }
        },
        methods:{
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">

</style>
