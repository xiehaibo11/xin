<!--返点值 滑块设置-->
<template>
    <div class="rebate-range-box flex-box" style="justify-content: flex-end">
        <span class="f-mini rang-val">{{rebateVal}}%</span>
        <input type="range" v-model="sliderValue" :min="0" :max="userRebate" step="0.1">
        <span class="f-mini rang-gain">{{gain}}</span>
    </div>
</template>

<script>
    export default {
        props:['gain'],
        data () {
            return {
            }
        },
        computed:{
            cz(){
                let path = this.$route.path.replace('/','')
                return path
            },
            //网站返点值
            rebate(){
                return Number(this.$store.state.lottery.bonus_base)
            },
            //当前用户返点值
            userRebate(){
                return this.$store.state.lottery.userRebate || 0
            },
            //用户所选 返点值
            rebateVal () {
                return this.$store.getters.rebateVal
            },
            sliderValue:{
                get(){
                    return this.$store.state.lottery.sliderValue
                },
                set(val){
                    this.$store.commit('changeRebateVal',val)
                }
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .rebate-range-box{
        height: 37px;
    }
    input[type=range] {
        // 去除浏览器默认的样式
        -webkit-appearance: none;
        -moz-appearance: none;
        border-radius: 10px;
        width: 100px;
        &:focus {
            // 去除落焦时的外边框效果
            outline: none;
        }
    }
    input[type=range]::-webkit-slider-runnable-track {
        height: 2px;
        border-radius: 10px; /*将轨道设为圆角的*/
        background: #bdbdbd;
    }
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        position: relative;
        top: -10px;
        background: #ffc34f;
        cursor: move;
        border:none;
        box-shadow: 0 1px 3px rgba(0,0,0,.4);
    }
    .rang-val{
        color: #333;
        display: inline-block;
        width: 25px;
        text-align: right;
        margin-right: 5px;
    }
    .ks-notes .rang-val{
        color: #ffffff;
    }
    .rang-gain{
        color: #333;
        display: inline-block;
        width: 48px;
        margin-left: 5px;
        word-break: break-all;
        line-height: 1;
    }
    .ks-notes .rang-gain{
        color: #ffffff;
    }
</style>
