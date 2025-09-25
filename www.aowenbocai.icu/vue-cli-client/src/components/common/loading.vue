<template>
    <div class="loading-box">
        <!--<div class="loading_container">-->
            <!--<div class="load_img" :style="{backgroundPositionY: -(positionY%7)*2.5 + 'rem'}">-->
            <!--</div>-->
            <!--<svg class="load_ellipse" xmlns="http://www.w3.org/2000/svg" version="1.1">-->
                <!--<ellipse cx="26" cy="10" rx="26" ry="10" style="fill:rgba(0, 0, 0, .2);stroke:none;"></ellipse>-->
            <!--</svg>-->
            <!--&lt;!&ndash;<div class="reload-btn">重新加载</div>&ndash;&gt;-->
        <!--</div>-->
    </div>
</template>

<script>
    import { Indicator } from 'mint-ui';
    export default {
        data() {
            return {
                positionY: 0,
                timer: null,
            };
        },
        mounted() {
//            this.timer = setInterval(() => {
//                this.positionY++;
//            }, 600);
            Indicator.open({
                text: '加载中...',
                spinnerType: 'fading-circle'
            });
        },
        beforeDestroy() {
            Indicator.close()
//            clearInterval(this.timer);
        }
    };
</script>

<style scoped type="text/css" lang="css">
    .loading-box {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.1);
        z-index: 9999;
    }
    @keyframes load {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-50px);
        }
        100% {
            transform: translateY(0px);
        }
    }
    @keyframes ellipse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(0.3);
        }
        100% {
            transform: scale(1);
        }
    }
    .loading_container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 2.5rem;
        height: 2.5rem;
    }
    .load_img {
        width: 100%;
        height: 100%;
        background: url(~@/assets/images/loading.png) no-repeat 0 0;
        background-size: 2.5rem auto;
        transform: translateY(0px);
        animation: load 0.6s infinite ease-in-out;
        position: relative;
        z-index: 11;
    }
    .load_ellipse {
        position: absolute;
        width: 2.6rem;
        height: 2rem;
        top: 2.2rem;
        left: -0.05rem;
        z-index: 10;
        animation: ellipse 0.6s infinite ease-in-out;
    }
    .reload-btn {
        /* position: absolute; */
        width: 4rem;
        text-align: center;
    }
</style>
