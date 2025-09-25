<template>
    <div class="progress-circle">
        <div>
            <svg :height="option.size" :width="option.size" x-mlns="http://www.w3.org/200/svg">
                <circle
                    :r="option.radius"
                    :cx="option.cx"
                    :cy="option.cy"
                    :stroke="option.outerColor"
                    :stroke-width="option.strokeWidth"
                    fill="none"
                    stroke-linecap="round"/>
                <circle
                    id="progressRound"
                    :stroke-dasharray="progressHandle"
                    :r="option.radius"
                    :cx="option.cx"
                    :cy="option.cy"
                    :stroke-width="option.strokeWidth"
                    :stroke="option.innerColor"
                    fill="none"
                    class="progressRound"
                />
                <text :x="option.cx + 2" :y="option.cy + 3" text-anchor="middle"
                      font-size="1.0rem"
                      fill="red"
                >
                    {{progress}}%
                </text>
                <text :x="option.cx + 2" :y="option.cy + 18" text-anchor="middle" class='f-mini'
                      font-size="0.7rem"
                      fill="orange"
                >
                    保{{bdProgress}}%
                </text>
            </svg>
        </div>

    </div>
</template>

<script type="text/ecmascript-6">
    export default {
        name: 'CommonLoopProgress',
        props: {
            bdProgress: {
                required: true,
            },
            progress: {
                required: true,
            },
            progressOption: {
                type: Object,
                default: () => {},
            },
        },
        data () {
            return {
            }
        },
        computed: {
            progressHandle () {
                let circleLength = Math.floor(2 * Math.PI * this.option.radius);
                let progressLength = this.progress * circleLength
                return `${progressLength/100 + 1},1000000`
            },
            option () {
                // 所有进度条的可配置项
                let baseOption = {
                    radius: 40,
                    strokeWidth: 5,
                    outerColor: '#E6E6E6',
                    innerColor: '#e50e03',
                }
                Object.assign(baseOption, this.progressOption)
                // 中心位置自动生成
                baseOption.cy = baseOption.cx = baseOption.radius + baseOption.strokeWidth
                baseOption.size = (baseOption.radius + baseOption.strokeWidth) * 2
                return baseOption
            },
        },
    }
</script>

<style lang="scss" scoped type="text/scss">
    .progressRound {
        transform-origin: center;
        transform: rotate(-90deg);
        transition: stroke-dasharray 0.3s ease-in;
    }
    .bd-name{
        background-color: $Success;
        color: #ffffff;
        border-radius: 3px;
        font-size: 10px;
        width:15px;
        height:15px;
        line-height: 15px;
        display: inline-block;
        margin-right: 3px;
    }
</style>
