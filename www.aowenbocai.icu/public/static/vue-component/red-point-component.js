document.writeln("<template id=\'redPoint\'>");
document.writeln("    <span v-if=\'lockInfo.tel ==1\' class=\'red-point\'></span>");
document.writeln("</template>");

document.writeln("<style>");
document.writeln("    .red-point{");
document.writeln("        position: absolute;");
document.writeln("        top: .2rem;");
document.writeln("        width: 0.6rem;");
document.writeln("        height: 0.6rem;");
document.writeln("        border-radius: 50%;");
document.writeln("        background-color: rgb(255, 0, 0);");
document.writeln("        border: 1px solid #ffffff;");
document.writeln("        right: .3rem;");
document.writeln("    }");
document.writeln("</style>");

//绑定红点提示
Vue.component('red-point',{
    template:"#redPoint",
    data:function data(){
        return{
            lockInfo : {}
        }
    },
    created:function created() {
        var _this = this;
        $.get('/index/User/lockInfo', function (res) {
            _this.$set(_this,'lockInfo',res.data)
        });
    }
});