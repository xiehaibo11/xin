<template>
    <div>
        <div class="date-query-fixed" :style="{height: contentH}">
            <div class="mf-sm card border-bottom-1px" style="padding: 10px">
                <div class="flex-box">
                    <div class="flex tl">
                        <slot name="info"></slot>
                    </div>
                    <em class="f-mini c-3" v-if="time.start && time.end && !shoseTime"  @click="shoseTime = !shoseTime">{{time.start}}至{{time.end}}</em>
                    <div style="margin-left: 5px">
                        <i slot="icon" class="iconfont icon-rili f-large c-3" @click="shoseTime = !shoseTime"></i>
                    </div>
                </div>
                <div v-if="shoseTime">
                    <div class="chose-time flex-box border-top-1px">
                        选择日期
                        <span @click="openSpicker" class="time-value">{{time.start}}</span>至
                        <span @click="openEpicker" class="time-value">{{time.end}}</span>
                        <mt-button @click.native="doSearch" size="small" class="btn-search">查询</mt-button>
                    </div>
                </div>
                <div>
                    <mt-datetime-picker
                        ref="picker"
                        type="date"
                        :value="startData"
                        year-format="{value} 年"
                        month-format="{value} 月"
                        date-format="{value} 日"
                        :start-date = 'startData'
                        :end-date = 'endData'
                        v-model="time.startBefore" @confirm="handleConfirm">
                    </mt-datetime-picker>
                    <mt-datetime-picker
                        ref="pickerEnd"
                        type="date"
                        :value="startData"
                        year-format="{value} 年"
                        month-format="{value} 月"
                        date-format="{value} 日"
                        :start-date = 'startData'
                        :end-date = 'endData'
                        v-model="time.endBefore" @confirm="handleConfirmEnd">
                    </mt-datetime-picker>
                </div>
            </div>
        </div>
        <div style="height: 46px"></div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
                shoseTime:false,
                time:{
                    startBefore: '',
                    start: '',
                    endBefore: '',
                    end: ''
                }
            }
        },
        computed:{
            contentH(){
                return this.shoseTime? '100%':'auto'
            },
            //日期的最小可选值
            startData() {
                return new Date("January 1,2018");
            },
            //日期的最大可选值
            endData() {
                return new Date();
            }
        },
        watch:{
            timeValue(val){
                this.$emit('change',val)
            }
        },
        methods:{
            //选择时间
            openSpicker() {
                this.$refs.picker.open();
            },
            //选择结束时间
            openEpicker() {
                if(this.time.endBefore == ''){
                    this.time.endBefore = this.time.startBefore; //初始==开始时间
                }
                this.$refs.pickerEnd.open();
            },
            //开始时间点击确定按钮之后
            handleConfirm(){
                if (this.time.startBefore == '') {
                    this.time.startBefore = this.startData;
                }
                this.time.start = this.$bet.formatTime('Y-m-d',this.time.startBefore/1000);
            },
            //结束时间点击确定按钮之后
            handleConfirmEnd(){
                if (this.time.endBefore == '') {
                    this.time.endBefore = this.time.startBefore;
                }
                this.time.end = this.$bet.formatTime('Y-m-d',this.time.endBefore/1000);
            },
            doSearch(){
                if(!this.time.startBefore){
                    this.$messagebox('提示','请选择开始日期')
                    return
                }
                if(!this.time.endBefore){
                    this.$messagebox('提示','请选择结束日期')
                    return
                }
                if(this.time.startBefore > this.time.endBefore){
                    this.$messagebox('提示','开始日期不能大于结束日期')
                    this.time.endBefore = this.time.startBefore
                    this.time.end = this.time.start
                    return
                }
                let data = 'starttime=' + this.time.start + '&endtime='+ this.time.end
                this.shoseTime = false
                this.$emit('do-search',data)
            }
        }
    }
</script>

<style scoped type="text/scss" lang="scss">
    .chose-time{
        justify-content: flex-end;
        padding: 10px 0 0 10px;
        font-size: 12px;
        margin-top: 10px;
    }
    .time-value{
        width: 80px;
        border:none;
        margin: 0 4px;
        border-bottom: 1px solid $color-border-three;
        text-align: center;
        padding:0 5px;
        display: inline-block;
        height: 26px;
        line-height: 26px;
        color: #000000;
        font-size: 12px;
    }
    .btn-search{
        height: 26px;
        margin-left: 5px;
    }
    .date-query-fixed{
        position: absolute;
        width: 100%;
        top: 0px;
        left: 0;
        z-index: 100;
        overflow: hidden;
    }
</style>
