<template>
    <div class="join-buy flex-box">
        <input-number v-model='value' :max='max' :min='1' :step='1'></input-number> <em class="f-mini c-3" style="margin-left: 5px">份</em>
        <mt-button class="btn-buy" type="danger" size="small" @click.native="submitOrder">购买</mt-button>
    </div>
</template>

<script>
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件

    export default {
        name: 'joinBuy', //合买--购买
        components: {
            inputNumber
        },
        props: {
            max: {
                //必须是数字类型
                type: Number,
                default: Infinity
            },
            index:{},
            orderInfo:{
                type: Object
            }
        },
        data () {
            return {
                value: 1
            }
        },
        computed:{
            unit(){
                return this.$store.state.setting.lottery_unit
            }
        },
        methods:{
            //订单信息确认
            submitOrder(){
                let data = {
                    buy:this.value,
                    orderInfo:this.orderInfo,
                    index:this.index
                }
                this.$emit('do-buy',data)
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    .join-buy{
        justify-content: flex-end;
    }
    .btn-buy{
        margin-left: 5px;
    }
</style>
