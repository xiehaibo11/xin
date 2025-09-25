<template>
    <div>
        <div><img src="~assets/images/agentIntro.wap.jpg" alt="" width="100%"></div>
        <div class="explainCoint" v-html="agentExplainInfo.content"></div>
    </div>
</template>

<script>
    export default {
        name: 'agentExplain',
        data () {
            return {
            }
        },
        computed:{
            agentExplainInfo(){
                return this.$store.state.agent.explain
            }
        },
        created(){
            this.$store.dispatch('getAgentExplain');//获取代理说明
        },
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .explainCoint{
        padding: 15px;
        line-height: 1.5;
        font-size: 15px;
    }
</style>
