<template>
    <div class="card">
        <div class="card-heard">
            <span>
                <slot name="header">标题</slot>
            </span>
        </div>
        <div class="card-cont">
            <slot name="content">内容</slot>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
            };
        },
    };
</script>

<style scoped type="text/scss" lang="scss">
    .card{
        background-color: $color-c;
        padding: pxTorem(10);
        .card-heard{
            padding: pxTorem(2) 0;
        }
    }
</style>
