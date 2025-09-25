<template>
    <div>
        <div class="mint-header-button is-left" @click="back" style="position: absolute;left: 0 ;top: 0px;height: 40px;width: 40px;line-height: 40px;text-align: center;color: #ffffff;
z-index: 10;">
            <i class="mintui mintui-back"></i>
        </div>
        <div :style="{height: contentH + 'px',width: contentW + 'px'}">
            <iframe :src="qqOnline" frameborder="0"></iframe>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {

            }
        },
        computed:{
            src(){
                return this.$route.query.u
            },
            contentH(){
                return this.$store.state.clientHeight
            },
            contentW(){
                return this.$store.state.clientWidth
            },
            //在线客服
            qqOnline(){
                return this.$store.state.setting.wap_online_qq
            }
        },
        methods:{
            back(){
                this.$router.goBack(-1);//返回上一层
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    iframe{
        width: 100%;
        height: 100%;
    }
</style>
