<template>
    <div>
        <div :style="{height: contentH + 'px',width: contentW + 'px'}">
            <iframe :src="src" frameborder="0"></iframe>
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
