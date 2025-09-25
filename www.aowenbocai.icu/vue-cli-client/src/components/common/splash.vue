<template>
    <div class="splash"></div>
</template>

<script>
    export default {
        data() {
            return {
            };
        }
    };
</script>

<style scoped type="text/scss" lang="scss">
    .splash{width: 100%;height: 100%;background: url("~@/assets/images/splash.png");
        background-size: cover;
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 10000;}
</style>
