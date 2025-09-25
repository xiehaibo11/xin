<template>
    <div>
        <div class="not-page tc">
            <p class="tc img"><img src="~@/assets/images/404.svg" alt="" width="128"></p>
            <p class="error-404 c-4">error</p>
            <p class="tc c-3">页面出错啦</p>
            <p>
                <mt-button type="default" plain @click.native="$router.goBack(-1)">返回上一页</mt-button>
            </p>
        </div>
    </div>
</template>

<script>
    export default {
        name: '',
        data () {
            return {
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss" type="text/scss">
    .not-page{
        padding-top: 100px;
        p{
            margin-bottom: 30px;
        }
        .error-404{
            font-size: 60px;
            font-weight: bold;
        }
    }
</style>
