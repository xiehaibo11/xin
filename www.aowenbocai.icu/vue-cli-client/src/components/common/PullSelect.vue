<!--下拉选项组件-->
<template>
    <div class="select-box" @click="changeStatus">
        <span class="select-title"  :class="{'select-title-active': selectStatus}">
            {{selectOptions[defaultIndex].label}}
            <i class="iconfont icon-xialajiantou f-mini c-4 rotateIcon" :class="selectStatus ? 'is-active':'no-active'"></i>
        </span>
        <transition name="slide-down">
           <ul class="select-options" v-show="selectStatus">
               <li class="select-option-item c-2 f-mini" v-for="(item,index) in selectOptions" :key="index" @click="shoseOptions(index)" :class="{'active':defaultIndex == index}">
                   {{item.label}}
               </li>
           </ul>
        </transition>
        <transition name="fade">
        <div class="v-modal" style="z-index:-100;top: 108px;" v-show="selectStatus"></div>
        </transition>
    </div>
</template>
<script>
export default{
    name: 'oSelect',
    model:{
        prop: 'value',
        event: 'change'
    },
    props:{ // 子组件接收父组件传过来的值，使用props
        selectOptions : {
            type: Array
        },
        value:{
            type: String,
            default: '',
        },
        selectStatus:{
            default: false,
        },
        defaultIndex:{
            default: 0
        }
    },
    data(){
        return{
//            defaultIndex: 0,//默认显示索引值
            svalue:this.value //改变选中的值
        }
    },
    methods:{
        shoseOptions(index){
            this.defaultIndex = index
            this.svalue = this.selectOptions[this.defaultIndex].value
            this.$emit("change",this.svalue)
        },
        changeStatus(){
            this.$emit("change-status")
        }
    },
    created(){

    }
}
</script>
<style lang="scss" scoped type="text/scss">
    .select-box{
        /*position: relative;*/
        width: 100%;
        max-width: 200px;
        line-height: 20px;
        text-align: center;
        margin: 5px auto;
        .select-title{
            padding: 3px 15px;
            display: inline-block;
            transition-duration: 300ms;
            cursor: pointer;
            background-color: #f7f7f7;
            border-radius: 4px;
            font-size: 12px;
            color: #333333;
            i{
                margin-left: 3px;
            }
        }
        .select-title-active{
            color: $bColor;
            i{
                color: $bColor;
            }
        }
        .select-options{
            position: absolute;
            top: 37px;
            left: 0;
            background-color: #ffffff;
            z-index: 50;
            width: 100%;
            padding:10px 10px 10px;
            box-shadow: 0 2px 8px #ddd;
            .select-option-item{
                width: 23%;
                margin: 3px 5%;
                float: left;
                height: 24px;
                line-height: 24px;
                background-color: #f7f7f7;
                border-radius: 5px;
                &.active{
                    background-color: $bColor;
                    color: #ffffff;
                }
            }
        }
    }
    .slide-down-enter-active,.slide-down-leave{
        transition: all .3s ease-in-out;
        transform-origin:0 top;
        transform: scaleY(1);
    }
    .slide-down-enter{
        transform: scaleY(0);
    }
    .slide-down-leave-active{
        transition: all 0.3s;
        transform-origin:0 top;
        transform: scaleY(0);
    }
</style>
