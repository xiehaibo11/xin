<template>
    <div>
        <div class="tc mt card pay-ma">
            <!--支付宝扫码二维码-->
            <template v-if="payWay == 'alipaySao'">
                <img :src="payAlipayUrl[money] || payAlipayUrl.other" alt="">
                <div class="mt-sm">支付宝扫码支付 <b class="red f-large">{{money}}</b> 元</div>
                <div class="c-3 f-sm mt-sm">长按保存图片或截图，<br>打开支付宝扫一扫识别相册二维码支付</div>
            </template>
            <template v-if="payWay == 'wxSao'">
                <img :src="payWeiUrl[money] || payWeiUrl.other" alt="">
                <div class="mt-sm">微信扫码支付 <b class="red f-large">{{money}}</b> 元</div>
                <div class="c-3 f-sm mt-sm">长按保存图片或截图，<br>打开微信扫一扫识别相册二维码支付</div>
            </template>
        </div>
        <div class="pay-order">
            <div class="f-sm c-2"><b class="red">* </b>支付成功后请填写备注信息</div>
            <div class="tips tl">可填写交易订单号后6位或转账时的备注等相关信息</div>
            <input type="tel" class="input" placeholder="" v-model="orderNum">
        </div>
        <div class="btn-box">
            <mt-button size="large" type="danger" @click.native="submit">
                <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>提 交
            </mt-button>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                loading:false,
                orderNum:''//订单号
            }
        },
        computed:{
            money(){
                return this.$route.query.money
            },
            type(){
                return this.$route.query.type
            },
            payWay(){
                return this.$route.query.payWay
            },
            payWeiUrl(){
                return this.$store.state.rechargeInfo.payWeiUrl
            },
            payAlipayUrl(){
                return this.$store.state.rechargeInfo.payAlipayUrl
            }
        },
        methods:{
            //提交订单
            submit(){
                if(!this.orderNum.length){
                    this.$toast('请填写备注信息!')
                    return
                }
                this.loading = true
                let way
                if(this.payWay == 'alipaySao'){ //支付宝
                    way = 3
                }
                if(this.payWay == 'wxSao'){ //微信
                    way = 2
                }
                this.$axios.post('/pay/pay/saoToPay',{
                    way: way ,
                    total_amount: this.money,
                    type: this.type,
                    info: this.orderNum
                }).then(({data}) =>{
                    if(!data.err){
                        this.$messagebox.alert(data.msg).then(action => {
                            let redirect = this.$route.query.redirect
                            if(redirect){
                                this.$router.replace({
                                    path:redirect
                                })
                            }else {
                                this.$router.replace({
                                    path:'/'
                                })
                            }
                        });
                    }else {
                        this.$messagebox('提示',data.msg);
                    }
                    this.loading = false

                }).catch(function (error) {
                    console.log(error);
                })
            }
        },
        activated(){
            this.orderNum = ''
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style  scoped type="text/scss" lang="scss">
    .pay-ma{
        padding: 15px;
        img{
            max-width: 280px;
        }
    }
    .pay-order{
        padding: 10px 20px 0;
        .input{
            border:1px solid $color-border-one;
            padding: 5px;
            width: 100%;
            font-size: 15px;
            height: 35px;
            line-height:35px;
            border-radius: 5px;
            margin-top: 10px;
        }
    }
</style>
