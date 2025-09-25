<template>
    <div class="setting">
        <div class="cell">
            <p class="mt-sm">
                <mt-cell title="银行卡" is-link to="/bankList">
                    <i slot="icon" class="iconfont icon-yinhangqia1"></i>
                    <span class="c-3" v-if="bankList.length">已绑定 <b class="suc">{{bankList.length}}</b> 张</span>
                    <span class="f-sm red" v-else>未绑定</span>
                </mt-cell>
                <mt-cell title="支付宝" is-link to="/lockAlipay">
                    <i slot="icon" class="iconfont icon-zhifubao"></i>
                    <span class="c-3" v-if="alipayList.length">已绑定</span>
                    <span class="f-sm red" v-else>未绑定</span>
                </mt-cell>
                <!--<mt-cell title="微信" is-link to="/lockWei">-->
                    <!--<i slot="icon" class="iconfont icon-weixin1"></i>-->
                    <!--<span class="c-3" v-if="weiList.length">已绑定</span>-->
                    <!--<span class="f-sm red" v-else>未绑定</span>-->
                <!--</mt-cell>-->
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
            bankList(){
                return this.$store.state.lockBank.banks
            },
            alipayList(){
                return this.$store.state.lockBank.alipay
            },
//            weiList(){
//                return this.$store.state.lockBank.wei
//            }
        },
        methods:{
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .binded{
        font-size: 13px;
    }
</style>
