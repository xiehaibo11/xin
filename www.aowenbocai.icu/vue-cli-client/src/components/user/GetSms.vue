<!--输入手机或邮箱获取相关验证码-->
<template>
    <div>
        <mt-field :label="labelWord" :placeholder="'请输入'+ labelWord"  v-model="options.num" :type="getWay == 1 ? 'tel' : 'text'"></mt-field>
        <mt-field label="验证码" placeholder="请输入验证码" v-model="options.yzm" type="tel" v-if="isShow">
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
            options:{ //号码和验证码 num yzm
                type:Object,
                require:true
            },
            getWay:{ //验证方式1短信 2邮件
                type: Number,
                default: 1,
            }
        },
        model:{
            prop: 'options',
            event: 'change'
        },
        data () {
            return {
                labelWord:this.getWay == 1 ? '手机号' : '邮箱',
                loading:false,
                text:'获取验证码',
                countDown:120,
                yzmState:false
            }
        },
        computed:{
            isYzm(){
                return Number(this.$store.state.setting.tel_checked) ? true: false //是否开启短信接口
            },
            isEmail(){
                return Number(this.$store.state.setting.email_checked) ? true: false //是否开启邮箱验证
            },
            isShow(){ //是否显示验证码框
                return this.getWay == 1 ? this.isYzm : this.isEmail
            }
        },
        watch:{
            'options.num'(val){
                if(val){
                    this.yzmState = true
                }
            },
            options(val){
                this.$emit('change',val)
            }
        },
        methods:{
            //发送验证码
            sendSms(){
                let data;
                if(this.countDown != 120) return false;
                if(this.getWay == 1){
                    if(!this.$base.testTel(this.options.num)){
                        this.$toast({
                            message: '请输入正确的手机号',
                            duration: 1500
                        });
                        return false;
                    }
                    data = {
                        tel:this.options.num
                    }
                }
                if(this.getWay == 2){
                    if(!this.$base.testEmail(this.options.num)){
                        this.$toast({
                            message: '请输入正确的邮箱地址',
                            duration: 1500
                        });
                        return false;
                    }
                    data = {
                        email:this.options.num
                    }
                }
                this.yzmState = false;
                this.text = '发送中...';
                this.$axios.get(this.url,{params:data}).then(({data}) =>{
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
