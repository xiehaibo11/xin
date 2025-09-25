<template>
    <div>
        <mt-header :title="title">
            <mt-button icon="back" slot="left" @click.native="back"></mt-button>
        </mt-header>
        <transition :name="transitionName">
            <keep-alive :include=needKeepAlive>
                <router-view class="Router-list contentH"  :style="{height: contentH + 'px'}"></router-view>
            </keep-alive>
        </transition>
    </div>
</template>
<script>
    export default {
        name: 'Header',
        data () {
            return {
            }
        },
        computed:{
            title(){
                return this.$route.meta.title
            },
            transitionName(){
                return this.$store.state.transitionName;
            },
            needKeepAlive(){
                return this.$store.state.needKeepAlive
            },
            //屏幕高度
            contentH(){
                return this.$store.state.clientHeight - 40
            }
        },
        methods:{
            back(){
                this.$router.goBack(-1);//返回上一层
            }
        }
    }
</script>
