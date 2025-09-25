<!--停止追号-->
<template>
    <div @click="showTips">
        <mt-button class="stop-btn" size="small" type="danger">停止</mt-button>
    </div>
</template>

<script>
    export default {
        props:['lotteryId','id','orderId'],
        data () {
            return {
            }
        },
        computed:{

        },
        methods:{
            showTips(){
                this.$messagebox.confirm(
                    '确定对该期停止追号吗？'
                ).then(()=>{
                    this.stopChase();
                }).catch((err)=>{

                });
            },
            stopChase(){
                this.$axios.post('/web/orders/stopLottery',{
                    lottery_id: this.lotteryId,
                    id: this.id
                }).then(({data})=>{
                    this.$toast(data.msg)
                    this.$axios.post('/web/orders/reloadExpect', {
                        lottery_id: this.lotteryId,
                        id: this.orderId
                    }).then(({data})=>{
                        this.$emit('reload-expect', data.data);
                    });
                })
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/css" lang>
    .stop-btn{
        display: inline-block;
        font-size: 11px;
        padding: 0 5px;
        height: 16px;
        background-color: #FF9800;
    }
</style>
