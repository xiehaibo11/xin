<!--投注页面-投注协议-->
<template>
    <div>
        <!--投注协议-->
        <div class="tc flex-box" style="justify-content: center;font-size: 13px;height: 30px">
            <label class="mint-radiolist-label" style="padding:0px">
                    <span class="mint-radio">
                        <input type="checkbox" class="mint-radio-input" v-model="check">
                        <span class="mint-radio-core"></span>
                    </span>
                <span class="mint-radio-label" style="margin-left: 0">我已阅读并同意</span>
            </label>
            <em @click="agreeVisible = true" class="org">《用户服务协议》</em><em @click="gameVisible = true" class="org">《游戏服务协议》</em>
        </div>
        <!--投注协议 end-->
        <!--用户服务协议-->
        <mt-popup
            class="agree-poput"
            v-model="agreeVisible"
            position="right">
            <div class="visible-cont">
                <mt-header title="用户服务协议">
                    <mt-button icon="back" slot="left" @click.native="agreeVisible = false">关闭</mt-button>
                </mt-header>
                <div v-html="user_web" class="service-cont"></div>
            </div>
        </mt-popup>
        <!--游戏服务协议-->
        <mt-popup
            class="agree-poput"
            v-model="gameVisible"
            position="right">
            <div class="visible-cont">
                <mt-header title="游戏服务协议">
                    <mt-button icon="back" slot="left" @click.native="gameVisible = false">关闭</mt-button>
                </mt-header>
                <div v-html="web_service" class="service-cont"></div>
            </div>
        </mt-popup>
    </div>

</template>

<script>
    export default {
        name: 'betAgreeChecked',
        props:{
            checked : { //协议选择状态
                type: Boolean,
                require:true,
                default:true
            }
        },
        model:{
            prop: 'checked',
            event: 'change'
        },
        data () {
            return {
                check:this.checked,
                agreeVisible: false,
                gameVisible : false
            }
        },
        watch:{
            check(val){
                this.$emit('change',val)
            }
        },
        computed:{
            //用户服务协议
            user_web(){
                return this.$store.state.setting.user_service
            },
            //游戏服务协议
            web_service(){
                return this.$store.state.setting.web_service
            }
        }
    }
</script>
