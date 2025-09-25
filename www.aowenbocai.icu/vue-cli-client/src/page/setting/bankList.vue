<template>
    <div>
        <div class="banks mt detail">
            <div>
                <mt-cell :title="item.numbers" :label="item.openname" v-for="(item,index) in bankList" :key="index">
                    <span class="iconfont icon-shanchu" @click="getSign(item.sign)"></span>
                </mt-cell>
                <div class="tc c-4" v-if="!bankList.length">您还未绑定任何银行卡！</div>
            </div>
            <!--删除提示-->
            <mt-actionsheet
                :actions="actions"
                v-model="sheetVisible">
            </mt-actionsheet>
        </div>
        <div class="mt-sm"><mt-cell title="+ 立即添加银行卡" is-link to="/lockBank"></mt-cell></div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                sheetVisible: false,
                actions: [{
                    name: '确定删除该银行卡？',
                    method: this.deleteBank
                }],
                sign:''
            }
        },
        computed:{
            bankList(){
                return this.$store.state.lockBank.banks
            }
        },
        created(){
//            this.$store.dispatch('getBanks');
        },
        methods:{
            getSign(sign){
                this.sheetVisible = true
                this.sign = sign
            },
            deleteBank(){
                this.$axios.get('web/user/deleteBank',{
                    params:{
                        sign : this.sign
                    }
                }).then(({data}) =>{
                    if(!data.err){
                        this.$store.dispatch('getBanks')//更新绑定银行卡信息
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
<style lang="scss" scoped type="text/scss">
    .binded{
        font-size: 13px;
    }
</style>
