$(function () {
    //用户名登录组件
    Vue.component("web-login",{
        template:'<el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" label-width="0px" class="ruleForm">\
                <el-form-item prop="username">\
                  <el-input type="text" v-model="ruleForm.username" auto-complete="off" placeholder="手机号/用户名/邮箱" @keyup.enter.native="submitForm(\'ruleForm\')"></el-input>\
                </el-form-item>\
                <el-form-item  prop="password">\
                  <el-input type="password" v-model="ruleForm.password" auto-complete="off" placeholder="密码" @keyup.enter.native="submitForm(\'ruleForm\')"></el-input>\
                </el-form-item>\
                <el-form-item>\
                  <el-button class="btn-full" type="danger" @click="submitForm(\'ruleForm\')" :loading="loading">登录</el-button>\
                  <div class="tl"><el-checkbox v-model="remember">记住密码，一周内免登陆</el-checkbox></div>\
               </el-form-item>\
                <el-alert v-if="errMsg.length" :title="errMsg" type="error" show-icon :closable="false"></el-alert>\
                </el-form>',
        data:function() {
            return {
                loading:false,
                errMsg:'',
                remember: false,//是否记住密码
                ruleForm: {
                    username: '',
                    password: '',
                },
                rules: {
                    username: [
                        { required: true, message: '请输入账号', trigger: 'blur' },
                    ],
                    password: [
                        { required: true, message: '请输入密码', trigger: 'blur' },
                        { min: 6, message: '密码长度大于6位', trigger: 'blur' }
                    ]
                }
            };
        },
        methods: {
            submitForm:function(formName) {
                var _this = this;
                _this.errMsg = ''
                _this.$refs[formName].validate(function(valid){
                    if (valid) {
                        _this.loading = true
                        $.post('/index/login/loginbyname',{
                            username : _this.ruleForm.username,
                            password : _this.ruleForm.password,
                            remember : _this.remember ? 1 : 0
                        },function(res){
                            if(!res.err){
                                _this.$emit('success',[res.nickanme,res.money,res.url])
                                _this.loading = false
                                return
                            }
                            _this.loading = false
                            _this.errMsg = res.msg
                        })
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            resetForm:function(formName) {
                this.$refs[formName].resetFields();
            }
        }
    })
})