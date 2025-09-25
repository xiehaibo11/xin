<!--管理安全密码-->
<template>
    <div>
        <div class="input-title">设置安全密码 <em class="org f-mini">（兑换时可使用,请牢记！）</em> </div>
        <div>
            <mt-field label="安全密码" placeholder="请输入安全密码(至少6位)" v-model="password1" type="password"></mt-field>
            <mt-field label="确认密码" placeholder="请再次输入安全密码(至少6位)" v-model="password2" type="password"></mt-field>
        </div>
        <div class="btn-box">
            <mt-button @click.native="sumbit" type="danger" size="large">
                <mt-spinner :type="3" slot="icon" :size="15" color="#fff" class="btn-loading" v-if="loading"></mt-spinner>
                保 存
            </mt-button>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                loading:false,
                password1:'',
                password2:''
            }
        },
        methods:{
            sumbit(){
                if(!this.password1 || !this.password2){
                    this.$toast('安全密码不能为空！')
                    return
                }
                if(this.password1.length < 6 || this.password2.length < 6){
                    this.$toast('安全密码长度至少6位！')
                    return
                }
                if(this.password1 !== this.password2){
                    this.$toast('两次输入的安全密码不相同！')
                    return
                }
                this.loading =true
                this.$axios.post('/web/user/lockSafePwd',{
                    pwd:this.password1,
                    pwd2:this.password2
                }).then(({data}) =>{
                    if(!data.err){
                        this.$store.dispatch('getLockInfo'); //获取账号绑定信息
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
