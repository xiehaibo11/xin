<!--模式1 单位选择、倍数选择-->
<template>
    <div class="flex-box">
        <input class="input-mul" type="tel" v-model="getMultiple" @change="handleMul"> <em class="c-w f-sm">倍</em>
        <div class="ks-model f-sm">
            <span @click="popupVisible = true">{{getLable}} <i class="iconfont icon-jiantou c-3 f-mini"></i></span> 模式
            <!--<i class="iconfont icon-yiwen c-3" @click="showModel"></i>-->
        </div>
        <mt-popup
            v-model="popupVisible"
            position="bottom">
            <div class="options-list">
                <span v-for="(item,index) in modelChoseOption" @click="choseModel(item.label,item.value)" :class="{'active': item.value == getValue}">{{item.label}}</span>
            </div>
        </mt-popup>
    </div>
</template>

<script>
    import inputNumber from 'components/common/InputNumber.vue' //购买输入框组件
    export default {
        components:{
            inputNumber
        },
        data () {
            return {
                popupVisible:false,

                label:'元', //单位值
                value:1, //单位值
                multiple:1,//倍数
            }
        },
        watch:{
            multiple:function (val) {
                this.$store.commit('changeMulValue',val)
            }
        },
        computed:{
            modelChoseOption(){
                return this.$store.state.setting.mode_unit_value
            },
            getLable:{
                get(){
                    return this.$store.state.lottery.label
                },
                set(newValue){
                    this.label = newValue
                }
            },
            getValue:{
                get(){
                    return this.$store.state.lottery.value
                },
                set(newValue){
                    this.value = newValue
                }
            },
            getMultiple:{
                get(){
                    return this.$store.state.lottery.multiple
                },
                set(newValue){
                    this.multiple = newValue
                }
            },
            scale() {
                return this.$store.getters.getScale
            },
            //投注单位
            lotteryUnit(){
                return this.$store.state.setting.lottery_unit;
            },
        },
        methods:{
            showModel(){
                this.$messagebox({
                    title: '提示',
                    message: '1元=1' + this.lotteryUnit +' ; 1角=0.1' + this.lotteryUnit +' ; 1分=0.01' + this.lotteryUnit +' ; 1厘=0.001' + this.lotteryUnit,
                    confirmButtonText:'我知道了'
                });
            },
            choseModel(t,v){
                this.value = v
                this.label = t
                let arr = [t,v]
                this.popupVisible = false
                this.$store.commit('changeModelValue',arr)
            },
            handleMul(){
                this.getMultiple = parseInt(this.getMultiple) || 1
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .ks-model{
        span{
            display: inline-block;
            padding: 0 2px;
            height: 22px;
            text-align: center;
            line-height: 22px;
            background-color: #f8f8f8;
            color: #333333;
            border:1px solid #d5d5d5;
            border-radius: 2px;
            width: 35px;
            margin-left: 5px;
        }
    }
    .input-mul{
        box-sizing: content-box;
        display:inline-block;
        border:none;
        height: 18px;
        line-height: 18px;
        padding: 2px 2px;
        width: 35px;
        text-align: center;
        margin: 0 2px 0 0px;
        border-radius: 2px;
        color: #333333;
        background-color: #f8f8f8;
        border:1px solid #d5d5d5;
        font-size: 14px;
    }
    .options-list{
        display: flex;
        span{
            flex: 1;
            text-align: center;
            background-color: #e8e8e8;
            padding: 10px 0;
            margin: 10px;
            border-radius: 5px;
            &.active{
               background-color: #ffc34f;
                color: #000000;
            }
        }
    }
</style>
