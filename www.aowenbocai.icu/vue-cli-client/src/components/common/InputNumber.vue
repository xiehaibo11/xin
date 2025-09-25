<template>
    <div class="input-number" :class="{'size-small':size=='small'}">
        <input class="number-input" type="tel" :value="currentValue" @change="handleChange" @keyup.up='handleUp' @keyup.down='handleDown'/>
        <span role="button" class="input-number_icon input-number__decrease border-right-1px" @click="handleDown" :class="{'is-disabled': currentValue <= min}">
            <i class="iconfont icon-jian"></i>
        </span>
        <span role="button" class="input-number_icon input-number_increase border-left-1px" @click="handleUp" :class="{'is-disabled': currentValue >= max}">
             <i class="iconfont icon-jia"></i>
        </span>
    </div>
</template>

<script>
    export default {
        name: 'inputNumber',
        props: {
            max: {
                //必须是数字类型
                type: Number/String,
                //默认值为Infinity
                default: Infinity
            },
            min: {
                type: Number,
                default: 1
            },
            value: {
                type: Number/String,
                default: 1
            },
            step: {
                type: Number,
                default: 1
            },
            size:{
                type:String,
                default:'normal'
            }
        },
        data () {
            return {
                currentValue: this.value
            }
        },
        watch: {
            //监听子组件currentValue是否改变
            currentValue: function(val) {
                //$emit与父组件通信  （子组件-->父组件）
                //this指向当前组件实例
                this.$emit('input', val);
                //定义自定义函数进行通信
                this.$emit('on-change', val)
            },
            //监听父组件value是否改变
            value: function(val) {
                this.updateValue(val);
            }
        },
        methods: {
            //父组件传递过来的值可能不符合条件（大于最大值，小于最小值）
            updateValue(val) {
                if(this.$base.isValueNumber(val)) {
                    if(val > this.max) {
                        val = this.max;
                    }else if(val < this.min) {
                        val = this.min;
                    }else {
                        this.currentValue = val
                    }
                } else {
                    this.currentValue = this.min;
                }
            },
            handleDown(){
                this.currentValue -= this.step;
                if(this.currentValue <= this.min) {
                    this.currentValue = this.min;
                }
                this.$emit('handle-down')
            },
            handleUp(){
                this.currentValue = Number(this.currentValue) + Number(this.step);
                if(this.currentValue >= this.max) {
                    this.currentValue = this.max;
                }
                this.$emit('handle-up')
            },
            handleChange(event) {
                var val = event.target.value.trim();
                var max = this.max;
                var min = this.min;
                if(this.$base.isValueNumber(val)) {
                    val = Number(val);
                } else {
                    this.currentValue = min;
                }
                this.currentValue = val;
                if(val > max) {
                    this.$toast({
                        message: '最大值为'+ max,
                        duration: 1000
                    })
                    this.currentValue = max;
                }
                if(val < min) {
                    this.currentValue = min;
                }
            },
        },
        //初始化
        mounted() {
            this.updateValue(this.value);
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped type="text/scss">
    $height:33px;
    .input-number{
        width: 100%;
        max-width: 120px;
        position: relative;
        display: inline-block;
        span{
            width: $height - 2;
            height: $height - 2;
            line-height: $height - 4;
            position: absolute;
            top: 1px;
            display: inline-block;
            background-color: $color-bg;
            text-align: center;
            i{
                font-size: 12px;
            }
            &.input-number__decrease{
                left: 1px;
            }
            &.input-number_increase{
                right: 1px;
            }
            &.is-disabled{
                color: #c0c4cc;
                cursor: not-allowed;
            }
        }
        .number-input{
            width: 100%;
            height: $height;
            line-height: $height;
            text-align: center;
            border:.5px solid $color-border-one;
            border-radius: 3px;
            font-size: 16px;
            resize: none;
            color: $color-font-primary;
        }
    }
    .size-small{
        $height:28px;
        width: 100%;
        max-width: 95px;
        position: relative;
        display: inline-block;
        span{
            width: $height - 2;
            height: $height - 2;
            line-height: $height - 4;
            position: absolute;
            top: 1px;
            display: inline-block;
            background-color: $color-bg;
            text-align: center;
            i{
                font-size: 12px;
            }
            &.input-number__decrease{
                left: 1px;
            }
            &.input-number_increase{
                right: 1px;
            }
            &.is-disabled{
                color: #c0c4cc;
                cursor: not-allowed;
            }
        }
        .number-input{
            width: 100%;
            height: $height;
            line-height: $height;
            text-align: center;
            border:.5px solid $color-border-one;
            border-radius: 3px;
            font-size: 13px;
            resize: none;
            color: $color-font-primary;
        }
    }
</style>
