<template>
    <div>
        <template v-if="transitionName == 'fade'">
            <transition :name="transitionName" mode="out-in">
                <keep-alive :include=needKeepAlive>
                    <router-view class="Router"></router-view>
                </keep-alive>
            </transition>
        </template>
        <template v-else>
            <transition :name="transitionName">
                <keep-alive :include=needKeepAlive>
                    <router-view class="Router"></router-view>
                </keep-alive>
            </transition>
        </template>
    </div>
</template>
<script>
    export default {
        name: 'TransitionA',
        data () {
            return {
            }
        },
        computed:{
            transitionName(){
                return this.$store.state.transitionName || 'fade';
            },
            needKeepAlive(){
                return this.$store.state.needKeepAlive
            }
        }
    }
</script>
