<!--暂停销售提示-->
<template>
    <div class="stopSale" v-if="isStop">
        <div>
            <svg slot="icon" class="icon" aria-hidden="true">
            <use xlink:href="#icon-shuizhuo"></use>
            </svg>
        </div>
        <p>对不起，该彩种暂停销售</p>
    </div>
</template>

<script>
    export default {
        name: 'unitConvert',
        props:{
            isStop:{
                default:false
            }
        },
        data () {
            return {
            }
        },
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .stopSale{
        position: fixed;
        overflow: hidden;
        height: 100%;
        height: calc(100% - 40px);
        width: 100%;
        display: flex;
        flex-direction: column;
        flex: 1;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        top: 40px;
        left: 0;
        background-color: rgba(0, 0, 0, 0.74);
        padding-bottom: 40%;
        p{
            font-size: 20px;
            color: #ffffff;
            margin-top: 30px;
            height: 50px;
            line-height: 50px;
            padding: 0 15px;
            @include shadow(0,0,10px,#fff);
            @include rounded(10px);
        }
        .icon {
            margin-right: 10px;
            width:120px;
            height: 120px;
            fill: currentColor;
            overflow: hidden;
        }
    }
</style>
