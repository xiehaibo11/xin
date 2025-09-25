<!--绑定用户名密码-->
<template>
    <div>
        <div class="input-title">设置用户名、密码 <em class="org f-mini">（可使用用户名+ 密码进行登录,请牢记！）</em> </div>
        <div>
            <mt-field label="用户名" placeholder="请输入用户名"  v-model="username" type="text"></mt-field>
            <mt-field label="密码" placeholder="请输入密码(长度6-25)"  v-model="password1" type="password"></mt-field>
            <mt-field label="确认密码" placeholder="请再次输入密码(长度6-25)"  v-model="password2" type="password"></mt-field>
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
                username:'',
                password1:'',
                password2:''
            }
        },
        methods:{
            sumbit(){
                if(!this.username){
                    this.$toast('用户名不能为空！')
                    return
                }
                if(!this.password1 || !this.password2){
                    this.$toast('密码不能为空！')
                    return
                }
                if(this.password1.length < 6 || this.password2.length < 6){
                    this.$toast('密码长度要求为（6-25）！')
                    return
                }
                if(this.password1 !== this.password2){
                    this.$toast('两次输入的密码不相同！')
                    return
                }
                this.loading =true
                this.$axios.get('/index/user/lockName',{
                    params:{
                        name:this.username,
                        pwd:this.password1,
                        pwd2:this.password2
                    }
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
