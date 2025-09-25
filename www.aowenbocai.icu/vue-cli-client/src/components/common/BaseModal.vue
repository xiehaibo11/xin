<!--* msg: 移动端弹窗组件，自定义标题、内容、位置。-->
<template>
    <div class="jc-modal-layout" v-show="visible"><!-- @click.self="closeMyself"-->
        <transition name="modal-zoom">
            <div v-show="visible" class="v-modal-p">
                <div class="v-modal-cont">
                    <div class="modal-head">
                        <!--弹窗头部 title-->
                        <slot name="header">提示信息</slot>
                    </div>
                    <div class="modal-cont flex" ref="wrapper">
                        <!--弹窗的内容-->
                        <slot name="main">弹窗内容</slot>
                    </div>
                    <div class="modal-foot">
                        <div class="flex-box">
                            <div class="modal-btn btn-cancle flex" @click="hanleCancel">取消</div>
                            <div class="modal-btn btn-sure flex" @click="hanleSure">确定</div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    /** 弹窗组件*/
    export default {
        name:'baseModal',
        props: {
            show: {
                type: Boolean,
                default: false
            }
        },
        data(){
            return{
                visible : this.show
            }
        },
        model:{
            prop:'show',
            event:'change'
        },
        watch:{
            show(val){
                this.visible = val
                this.$refs.wrapper.scrollTop = 0;
            }
        },
        methods: {
            closeMyself() {
                this.visible = false
                this.$emit("change");
            },
            //取消
            hanleCancel(){
                this.visible = false
                this.$emit("change");
                this.$emit('hanle-cancel')
            },
            //确认
            hanleSure(){
                this.$emit('hanle-sure')
            }
        }
    };
</script>
<style lang="scss" scoped type="text/scss">
    .jc-modal-layout{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height:100%;
        background: rgba(0, 0, 0, 0.66);
        z-index: 105;
    }
    .v-modal-p {
        display: flex;
        align-items: center;
        z-index: 106;
        position: fixed;
        top: 20px;
        bottom: 20px;
        left: 0;
        right: 0;
        padding: 0 10px;
    }
    .v-modal-cont {
        width: 100%;
        text-align: center;
        background: #ffffff;
        color: #333333;
        display: flex;
        flex-direction: column;
        max-height: 100%;
        @include rounded(6px);
        overflow: hidden;
        .modal-head{
            margin-top: 6px;
            text-align: center;
            font-size: 15px;
            padding: 10px;
            box-sizing: border-box;
            background: #fff;
            border-bottom: 1px solid #dfdfdf;
        }
        .modal-cont{
            background-color: #f4f4f4;
            overflow-y: auto;
            min-height: 100px;
            overflow-x: hidden;
            text-align: left;
        }
        .modal-foot{
            height: 45px;
            border-top: 1px solid #dfdfdf;
            .modal-btn{
                display: table-cell;
                height: 45px;
                text-align: center;
                font-size: 16px;
                line-height: 48px;
                width: 50px;
            }
            .btn-sure{
                background-color:$Danger;
                color: #ffffff;
            }
        }
    }
    /*--Vue--弹窗动画效果*/
    .modal-opacity-enter-active {
        animation: opacityIn 0.2s ease;
        -webkit-animation: opacityIn 0.2s ease;
        -moz-animation: opacityIn 0.2s ease;
    }
    .modal-opacity-leave--active {
        display: none;
    }
    .modal-zoom-enter-active {
        animation: zoomIn 0.4s ease;
        -webkit-animation: zoomIn 0.4s ease;
        -moz-animation: zoomIn 0.4s ease;
    }
    .modal-zoom-leave-active {
        display: none;
    }
    .fade-enter-active,
    .fade-leave-active {
        opacity: 1;
        transition: all 0.2s linear;
    }
    .fade-enter,
    .fade-leave-active {
        opacity: 0;
    }

    @keyframes zoomIn {
        0% {
            transform: scale(0.5);
        }
        70% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }
    @-webkit-keyframes zoomIn {
        0% {
            transform: scale(0.5);
        }
        70% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }
    @-moz-keyframes zoomIn {
        0% {
            transform: scale(0.5);
        }
        70% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }
    @keyframes opacityIn {
        100%{
            transform: scale(0);
        }
    }
    @-webkit-keyframes opacityIn {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 0.6;
        }
    }
    @-moz-keyframes opacityIn {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 0.6;
        }
    }
</style>
