<!--搜索框-->
<template>
    <div class="searchCont">
        <input type="text" :placeholder="placeholder" class="searchInput" v-model="value" @keyup.enter="search">
        <span class="searchBtn" @click="search"><i class="iconfont icon-sousuo"></i></span>
    </div>
</template>

<script>
    export default {
        name: 'searchCont',
        props:{
            inputValue:{
                type:String
            },
            placeholder:{
                type:String,
                default:'搜索'
            }
        },
        model:{
            prop: 'inputValue',
            event: 'change'
        },
        data () {
            return {
                value : this.inputValue
            }
        },
        watch:{
            inputValue(val){
                if(val == ''){
                    this.value = ''
                }
            },
            value(val){
                this.$emit('change',val)
            }
        },
        methods:{
            search(){
//                if(!this.value.length){
//                    this.$messagebox('提示','搜索内容不能为空')
//                    return
//                }
                this.$emit('search')
            }
        }
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped type="text/scss" lang="scss">
    .searchCont{
        position: relative;
        margin: 10px;
        overflow: hidden;
        .searchInput{
            text-align: left;
            margin-right: 0;
            color: #999;
            display: block;
            border: none;
            border-right: none;
            -webkit-appearance: none;
            padding:0 45px 0 8px;
            width: 100%;
            height: 35px;
            line-height: 35px;
            @include rounded(6px);
            font-size: 15px;
        }
        .searchBtn{
            position: absolute;
            right: 0px;
            top:0;
            display: block;
            width:40px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            @include rounded(6px);
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            font-size: 14px;
            background-color: #ef4f4f;
            color: #ffffff;
        }
    }
    input{
            outline:none;
            border:none;
            outline:medium;
        }
     *:focus {
         outline: none;
     }
     ::selection{background:transparent; }
     ::-moz-selection{background:transparent; }
</style>
