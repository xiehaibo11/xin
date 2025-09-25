<!--短信安全验证-->
<template>
    <div>
        <mt-field :label="labelWord" :placeholder="'请输入'+ labelWord"  v-model="num" disabled></mt-field>
        <mt-field label="验证码" placeholder="请输入验证码" v-model="yzm" type="tel">
            <div @click="sendSms" class="btn-yzm" :class="yzmState ? 'abled': 'disabled'">{{text}}</div>
        </mt-field>
    </div>
</template>

<script>
    export default {
        name: 'telSafeCheck',
        props:{
            url : { //'发送短信/邮件地址'
                type: String,
                require:true
            },
            bindYzm:{ //验证码值
            },
            checkWay:{ //验证方式1短信 2邮件
                type: Number,
                default: 1,
            }
        },
        model:{
            prop: 'bindYzm',
            event: 'change'
        },
        data () {
            return {
                numSet:'',
                labelWord:this.checkWay == 1 ? '手机号' : '邮箱',
                yzm:this.bindYzm,
                loading:false,
                text:'获取验证码',
                countDown:120,
                yzmState:true
            }
        },
        computed:{
            num:{
                get(){
                    return this.checkWay == 1 ? this.$store.state.lockInfo.tel : this.$store.state.lockInfo.email
                },
                set(val){
                    this.numSet = val
                }
            }
        },
        watch:{
            yzm(val){
                this.$emit('change',val)
            }
        },
        methods:{
            //发送手机验证码
            sendSms(){
                if(this.countDown != 120) return false;
                this.yzmState = false;
                this.text = '发送中...';
                this.$axios.get(this.url).then(({data}) =>{
                    if(!data.err){
                        this.text = "重新发送("+this.countDown+"s)";
                        var interval = setInterval(()=>{
                            this.countDown = this.countDown - 1;
                            if(this.countDown == 0){
                                this.text = '获取验证码';
                                this.yzmState = true
                                this.countDown = 120;
                                clearInterval(interval);
                                return;
                            }
                            this.text = "重新发送("+this.countDown+"s)";
                        },1000);
                        this.$toast({
                            message: '发送成功',
                            duration: 1500
                        });
                    }else {
                        this.$toast({
                            message: data.msg,
                            duration: 1500
                        });
                        this.text = '获取验证码';
                        this.yzmState = true
                    }
                }).catch(function (error) {
                    console.log(error);
                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
