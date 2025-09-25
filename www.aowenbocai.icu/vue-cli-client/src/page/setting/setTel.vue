<!--绑定手机-->
<template>
    <div>
        <div class="input-title">绑定手机</div>
        <get-sms :get-way="1" url="/index/user/sendSms" v-model="options"></get-sms><!--绑定手机-->
        <div class="btn-box">
            <mt-button @click.native="sumbit" type="danger" size="large">
                <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                保 存
            </mt-button>
        </div>
    </div>
</template>

<script>
    import GetSms from 'components/user/GetSms.vue'
    export default {
        components:{
            GetSms
        },
        data () {
            return {
                loading:false,
                options:{
                    yzm:'',
                    num:''//验证码值
                },
            }
        },
        methods:{
            //绑定
            sumbit(){
                this.loading =true
                let data={
                    tel: this.options.num,
                    yzm: this.options.yzm
                }
                this.$axios.get('/index/User/lockTel',{params:data}).then(({data}) =>{
                    if(!data.err){
                        this.$store.dispatch('getUserInfo');
                        this.$store.dispatch('getLockInfo');
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

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
</style>
