// 电子协议
document.writeln("<style>");
document.writeln("  /*电子协议组件样式*/");
document.writeln("  .v-modal-agree-lay{");
document.writeln("    position: fixed;");
document.writeln("    width: 100%;");
document.writeln("    height: 100%;");
document.writeln("    z-index: 1000;");
document.writeln("    background-color: #323965;");
document.writeln("    left: 0;");
document.writeln("    top: 0;");
document.writeln("  }");
document.writeln("  .v-modal-agree-box{");
document.writeln("    display: flex;");
document.writeln("    width: 100%;");
document.writeln("    position: absolute;");
document.writeln("    top: 0;");
document.writeln("    bottom: 0;");
document.writeln("    left: 0;");
document.writeln("    overflow-y: scroll;");
document.writeln("    overflow-x: hidden;");
document.writeln("  }");
document.writeln("  .v-modal-agree-box .agree-cont{");
document.writeln("    padding: .5rem;");
document.writeln("    color: #eeeeee;");
document.writeln("    fonts-size: .65rem;");
document.writeln("  }");
document.writeln("  .v-modal-agree-box .agree-cont p{");
document.writeln("    line-height: 1rem;");
document.writeln("    padding: .2rem 0;");
document.writeln("    fonts-size: .65rem;");
document.writeln("    text-align: left;");
document.writeln("  }");
document.writeln("  .v-modal-agree-box .agree-cont img{");
document.writeln("    max-width: 100%;");
document.writeln("    height: auto;");
document.writeln("    padding: .2rem 0;");
document.writeln("  }");
document.writeln("  .word{");
document.writeln("    fonts-size: .7rem;");
document.writeln("    text-decoration: underline;");
document.writeln("    color: #dedede;");
document.writeln("   }");
document.writeln("</style>");
document.writeln("");
document.writeln("<template id=\'krAgreement\'>");
document.writeln("  <span>");
document.writeln("      <a @click=\'getNews\' class=\'word\'>{{title}}</a>");
document.writeln("      <transition  name=\'fade\' v-if=\'isShow\'>");
document.writeln("          <div class=\'v-modal-agree-lay\'>");
document.writeln("              <div class=\'v-modal-agree-box\'>");
document.writeln("                    <div class=\'v-modal-agree-cont\'>");
document.writeln("                         <header class=\'header\'>");
document.writeln("                            <div class=\'header-nav\'><a class=\'nav-word\' @click=\'isShow=!isShow\' style=\'text-decoration: none\'><i class=\'icon-svg icon_left\'></i></a></div>");
document.writeln("                            <div class=\'header-center\'>{{item.title}}</div>");
document.writeln("                         </header>");
document.writeln("                          <div class=\'p-height\'></div>");
document.writeln("                          <div class=\'agree-cont\' v-html=\'item.content\'></div>");
document.writeln("                    </div>");
document.writeln("                </div>");
document.writeln("          </div>");
document.writeln("        </transition>");
document.writeln("   </span>");
document.writeln("</template>");

$(function () {
    Vue.component('kr-agreement', {
        props: ['id','title'], //文章id , 协议名称
        template: '#krAgreement',
        data(){
            return{
                item:[],
                isShow:false
            }
        },
        methods:{
            getNews(){
                var _this = this;
                $.get('/news/index/view/?id=' + _this.id,(res)=>{
                    if(res.err){
                        $.alert(res.msg)
                        return false
                    }
                    _this.$set(_this,'item',res.data)
                })
                _this.isShow = !_this.isShow
            }
        }
    });
});