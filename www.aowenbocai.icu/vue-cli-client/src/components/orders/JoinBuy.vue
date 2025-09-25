<template>
    <div class="join-buy flex-box">
        <input-number v-model='value' :max='max' :min='1' :step='1'></input-number> <em class="f-mini c-3" style="margin-left: 5px">{{unit}}</em>
        <mt-button class="btn-buy" type="danger" size="small" @click.native="submitOrder">购买</mt-button>
        <!--订单信息确认-->
        <mt-popup
            v-model="orderVisible"
            position="bottom">
            <div class="orders-info" v-if="orderVisible">
                <div class="order-title org border-bottom-1px">订单信息确认</div>
                <div class="order-info">
                    <div>
                        <span class="name">投注彩种:</span>
                        <span class="info">{{orderInfo.title}}</span>
                    </div>
                    <div>
                        <span class="name">投注方式:</span>
                        <span class="info">参与合买</span>
                    </div>
                    <div>
                        <span class="name">认购金额:</span>
                        <span class="info"><b class="red f-large">{{value}}</b> {{unit}}</span>
                    </div>
                    <div>
                        <span class="name">账户余额:</span>
                        <span class="info"><em>{{money}}</em> {{unit}}</span>
                        <span class="tips tc" style="font-size: 14px;color: #333" v-if="money < value"><b class="red">（余额不足，请充值!）</b></span>
                    </div>
                </div>
                <div class="order-footer tr">
                    <mt-button class="cancel" @click="orderVisible = false" size="small">取消</mt-button>
                    <mt-button type="primary"  size="small" v-if="money < value" @click="goPay">立即充值</mt-button>
                    <mt-button type="primary" v-else @click="payment" size="small">
                        <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                        确定购买
                    </mt-button>
                </div>
            </div>
        </mt-popup>
    </div>
</template>

<script>
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    import unitConvert from 'components/lottery/UnitConvert.vue' //彩票单位提示组件

    export default {
        name: 'joinBuy', //合买--购买
        components: {
            inputNumber,
            unitConvert
        },
        props: {
            max: {
                //必须是数字类型
                type: Number,
                default: Infinity
            },
            orderInfo:{
                type: Object
            }
        },
        data () {
            return {
                value: 1,
                orderVisible:false,
                loading:false
            }
        },
        computed:{
            unit(){ //投注单位
                return this.$store.state.setting.lottery_unit
            },
            money(){ //账户余额
                return this.$store.state.userinfo.money
            }
        },
        methods:{
            //订单信息确认
            submitOrder(){
                this.$store.commit('setLoadStatus',true)
                this.$axios('/index/moblie/checkLogin').then(({data}) => {
                    this.$store.commit('setBauth', data.status);
                    this.$store.commit('setLoadStatus',false)
                    if(!data.status){
                        this.$router.replace({
                            path:'/login',
                            query:{
                                redirect:this.$router.currentRoute.fullPath
                            }
                        })
                    }else {
                        this.$store.dispatch("getUserInfo"); //更新用户信息
                        this.orderVisible = true
                    }
                });
            },
            //充值
            goPay(){
                this.$router.replace({
                    path:'/pay',
                    query:{
                        redirect:this.$router.currentRoute.fullPath
                    }
                })
            },
            //付款
            payment(){
                this.loading = true
                this.$axios.post('/web/orders/buyJoin',{
                    buy_id: this.orderInfo.id,
                    lottery_id: this.orderInfo.lottery_id,
                    money: this.value
                }).then(({data}) =>{
                    if(!data.err){
                        this.value = 1 ;
                        this.$store.dispatch('getUserInfo') //更新用户信息
                    }
                    this.orderVisible = false
                    this.loading = false
                    setTimeout(()=>{
                        this.$messagebox({
                            title: '提示',
                            message: data.msg
                        });
                    },200)
                    this.$emit("update-join-list")//更新合买列表
                }).catch(function (error) {
                    console.log(error);
                })
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
