<template>
    <div class="bank">
        <template v-if="alipay.length">
            <div v-if="!edit">
                <div class="input-title">您已绑定的支付宝账号信息如下 <a @click="edit = true" class="link">修改</a></div>
                <div>
                    <mt-field label="持卡人" placeholder="" v-model="lockInfo.is_name" type="text" disabled></mt-field>
                    <mt-field label="支付宝" placeholder="请输入新的支付宝账号" v-model = "alipay[0].numbers" type="text" disabled></mt-field>
                </div>
            </div>
            <div v-else>
                <div class="input-title">请添加新的支付宝账号信息</div>
                <div>
                    <mt-field label="持卡人" placeholder="" v-model="lockInfo.is_name" type="text" disabled></mt-field>
                    <mt-field label="支付宝" placeholder="请输入新的支付宝账号" v-model = "num" type="text"></mt-field>
                </div>
                <div class="btn-box">
                    <mt-button @click.native="sumbit(true)" type="danger" size="large">
                        <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                        确认重置
                    </mt-button>
                </div>
            </div>
        </template>
        <template v-else>
            <div class="input-title">请添加与您实名制相同姓名的支付宝账号</div>
            <div>
                <mt-field label="持卡人" placeholder="" v-model="lockInfo.is_name" type="text" disabled></mt-field>
                <mt-field label="支付宝" placeholder="请输入支付宝账号" v-model = "num" type="text"></mt-field>
            </div>
            <div class="btn-box">
                <mt-button @click.native="sumbit(false)" type="danger" size="large">
                    <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                    保 存
                </mt-button>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                loading:false,
                num:'',
                edit:false
            }
        },
        computed:{
            lockInfo(){
                return this.$store.state.lockInfo
            },
            alipay(){
                return this.$store.state.lockBank.alipay
            }
        },
        methods:{
            doEdit(){
                this.edit = true
                this.num = ''
            },
            //提交
            sumbit(flag){
                if(!this.num) {
                    this.$toast('支付宝账号不能为空');
                    return;
                }
                let data,url;
                if(flag){
                    data = {
                        type:2,
                        sign:this.alipay[0].sign,
                        numbers: this.num
                    }
                    url = '/web/user/editBank'
                }else {
                    data = {
                        type: 2,
                        name: '支付宝',
                        numbers: this.num
                    }
                    url = '/web/user/lockBank'
                }
                this.loading = true
                this.$axios.post(url,data).then(({data}) =>{
                    if(!data.err){
                        this.$store.dispatch('getLockInfo')//更新用户绑定信息
                        this.$store.dispatch('getAlipay')//更新绑定支付宝信息
                        setTimeout(()=>{
                            this.$router.goBack(-1);
                        },1000)
                    }
                    this.$toast({
                        message: data.msg,
                        duration: 1000
                    });
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                })
            }
        }
    }
</script>
