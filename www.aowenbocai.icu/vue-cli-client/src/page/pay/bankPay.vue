<template>
    <div>
        <div class="bank-pay-head">
            <div class="mt">需转账金额 <b class="red f-large">{{money}}</b> 元</div>
            <div class="red f-mini mt-sm" style="line-height: 1.1"> (<i class="iconfont icon-jinggao"></i>仅限于使用银行卡转账，您实际支付金额必须与此处的金额一致，若不一致，将无法到账。)</div>
        </div>
        <div class="mt-sm card">
            <div class="bank-pay-title"><em class="tip-num">1</em><label>入款银行选择:</label></div>
            <div>
                <ul class="bank-list cf">
                    <li v-for="(item,index) in bankList" :class="{'active': bankCur == index}" @click="bankCur=index">
                        <div><span class="label">开户银行：</span>{{item.bank_name}}</div>
                        <div><span class="label">开户网点：</span>{{item.bank_area}}<a class="link ml-sm" @click="doCopy(item.bank_area)">复制</a></div>
                        <div><span class="label">收款账号：</span>{{item.bank_num}}<a class="link ml-sm" @click="doCopy(item.bank_num)">复制</a></div>
                        <div><span class="label">收款人：</span>{{item.name}}<a class="link ml-sm" @click="doCopy(item.name)">复制</a></div>
                    </li>
                </ul>
            </div>
            <div class="c-3 f-sm" style="padding:10px">* 您目前选择的是<b class="red">{{curSelectBank}}</b></div>
        </div>
        <div class="mt-sm card">
            <div class="bank-pay-title"><em class="tip-num">2</em><label>填写转账资料：</label></div>
            <div class="pay-order">
                <input type="text" class="input" placeholder="付款人姓名" v-model="name">
                <input type="text" class="input" placeholder="付款银行名称" v-model="bank_name">
            </div>
            <div class="btn-box">
                <mt-button size="large" type="danger" @click.native="submit">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>提 交
                </mt-button>
            </div>
        </div>
        <div class="tips f-mini" style="padding: 10px">
            <p class="pay-ico c-3"><i class="iconfont icon-jinggao red"></i> 转账须知：</p>
            <p class="c-3">* 转账方式仅限于您使用<em style="color: #f46e00">银行卡</em>进行转账，请勿使用其他方式。</p>
            <p class="c-3">* 所填写转账资料必须与您实际支付信息一致，若不一致，将无法到账。</p>
            <p class="c-3">* 以上银行账号仅限本次使用，账号不定期更换！请您根据本页面所提供的收款账号进行转账，若存款至过期账号，本网站恕不负责！</p>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                loading:false,
                bankCur: 0 ,

                name:'',
                bank_name:''
            }
        },
        computed:{
            bankList(){
                return this.$store.state.rechargeInfo.bank_config
            },
            money(){
                return this.$route.query.money
            },
            type(){
                return this.$route.query.type
            },
            payWay(){
                return this.$route.query.payWay
            },
            curSelectBank() {
                return this.bankList.length ? this.bankList[this.bankCur].bank_name : ''
            }
        },
        methods:{
            //复制链接
            doCopy(t) {
                this.$copyText(t).then((e)=> {
                    this.$messagebox('提示', '复制成功！');
                }, (e)=> {
                    this.$messagebox('提示', '复制失败！');
                })
            },
            //提交订单
            submit(){
                if(!this.name.length){
                    this.$toast('请输入付款人姓名!')
                    return
                }
                if(!this.bank_name.length){
                    this.$toast('请输入付款银行名称!')
                    return
                }
                this.loading = true
                this.$axios.post('/pay/pay/saoToPay',{
                    way: 4,
                    total_amount: this.money,
                    type: this.type,
                    re_bank_suffix : this.bankCur,
                    name: this.name,
                    bank_name : this.bank_name
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
            this.name = ''
            this.bank_name = ''
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style  scoped type="text/scss" lang="scss">
    .bank-pay-head{
        padding: 0 10px;
    }
    .bank-pay-title{
        padding: 0 10px;
        height: 50px;
        line-height: 50px;
        em.tip-num{
            display: inline-block;
            width: 22px;
            height: 22px;
            @include rounded(50%);
            line-height: 22px;
            text-align: center;
            background-color: #4CAF50;
            color: #ffffff;
            margin-right: 6px;
        }
    }
    .bank-list{
        li{
            padding:5px;
            border:1px solid #e9e9e9;
            background-color: #f9f9f9;
            @include rounded(3px);
            width: 90%;
            margin: 0 auto;
            margin-bottom: 10px;
            line-height: 1.4;
            color: #333333;
            cursor: pointer;
            font-size: 14px;
            &.active{
                border-color: #ff6c08;
                background-color: #ffffff;
            }
            span.label{
                display: inline-block;
                width: 70px;
                text-align: right;
                color: #888888;
                font-size: 12px;
            }
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
            margin-bottom: 10px;
        }
    }
</style>
