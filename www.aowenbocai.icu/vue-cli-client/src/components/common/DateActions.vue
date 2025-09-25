<!--按时间段查询-->
<template>
    <div>
        <mt-button @click.native="sheetVisible = true">{{showText}} <i class="iconfont icon-xialajiantou"></i></mt-button>
        <!--日期选择-->
        <mt-actionsheet
            :actions="actions"
            v-model="sheetVisible">
        </mt-actionsheet>
        <!--日期选择 end-->
    </div>
</template>

<script>
    export default {
        name: 'dateActions',
        data () {
            return {
                sheetVisible:false,
                actions: [{
                    name: '今天',
                    method: this.chose1
                }, {
                    name: '昨天',
                    method: this.chose2
                },{
                    name: '本月',
                    method: this.chose3
                },{
                    name: '上月',
                    method: this.chose4
                }],
                time: '1'
            }
        },
        computed:{
            showText(){
                const textObj = {
                    1 : '今天',
                    2 : '昨天',
                    3 : '本月',
                    4 : '上月'
                }
                return textObj[this.time]
            },
        },
        watch:{
            time(val){
                this.$emit('change-date',val)
            }
        },
        methods:{
            chose1(){
                this.time = 1
            },
            chose2(){
                this.time = 2
            },
            chose3(){
                this.time = 3
            },
            chose4(){
                this.time = 4
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
