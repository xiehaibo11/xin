<template>
    <div>
        <iframe :src="resUrl" frameborder="0" style="width: 100%;height: 95vh"></iframe>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                getWeiResult:''
            }
        },
        computed:{
            resUrl(){
                return this.$route.query.res
            }
        },
        methods:{
            //提交订单

        },
        created(){
            this.getWeiResult = setInterval(()=>{
                this.$axios.post('/pay/pay/getResult').then(({data})=>{
                    if(!data.err){
                        clearInterval(time);
                        this.$router.goBack(-1)
                    }
                }).catch(({error})=>{
                    clearInterval(this.getWeiResult);
                    this.$router.goBack(-1)
                    console.log(error)
                })
            }, 5000);
        },
        beforeRouteLeave(to,from,next){
            clearInterval(this.getWeiResult)
            next();
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style  scoped type="text/scss" lang="scss">

</style>
