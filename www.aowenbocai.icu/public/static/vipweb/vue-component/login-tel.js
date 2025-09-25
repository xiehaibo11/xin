$(function () {
    //手机登录组件
    Vue.component("tel-login",{
        template:'<el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" label-width="0px" class="ruleForm">\
                <el-form-item prop="tel">\
                  <el-input v-model="ruleForm.tel" type="tel" placeholder="手机号"></el-input>\
                </el-form-item>\
                <el-form-item prop="yzm">\
                  <el-input v-model.number="ruleForm.yzm" type="tel" class="yzm" placeholder="验证码" @keyup.enter.native="submitForm(\'ruleForm\')"></el-input>\
                    <el-button class="btn-yzm" type="primary" plain @click.native="sendSms(\'tel\')" v-if="smsState">{{text}}</el-button>\
                     <el-button class="btn-yzm" type="info" plain disabled v-else>{{text}}</el-button>\
                </el-form-item>\
                <el-form-item>\
                  <el-button class="btn-full" type="danger" @click="submitForm(\'ruleForm\')" :loading="loading">登录</el-button>\
                  <div class="tl"><el-checkbox v-model="remember">记住密码，一周内免登陆</el-checkbox></div>\
               </el-form-item>\
                 <el-alert v-if="errMsg.length" :title="errMsg" type="error" show-icon :closable="false"></el-alert>\
                </el-form>',
        data:function() {
            //手机号验证
            var checkTel = function(rule, value, callback){
                var reg = /^1[3-9]{1}[0-9]{9}$/;
                var res = reg.test(value);
                if (!value) {
                    return callback(new Error('手机号不能为空'));
                }
                if (!res) {
                    return callback(new Error('请输入正确的手机号'));
                }
                callback()
            };
            return {
                remember: false,//是否记住密码
                ruleForm: { //表单数据
                    tel: '',
                    yzm: '',
                },
                rules: { //表单验证
                    tel: [
                        { required: true,  message: '手机号不能为空', trigger: 'blur' },
                        {pattern: /^1[3-9]{1}[0-9]{9}$/ , message: '请输入正确的手机号', trigger: 'blur'}
                    ],
                    yzm: [
                        { required: true, message: '请输入验证码', trigger: 'blur' },
                    ]
                },
                errMsg:'', //错误提示
                loading:false,
                text:'获取验证码',
                smsState:true,
                countDown:120
            };
        },
        methods: {
            //发送验证码
            sendSms:function(){
                if(this.countDown != 120) return false;
                var reg = /^1[3-9]{1}[0-9]{9}$/;
                var res = reg.test(this.ruleForm.tel);
                if(!res){
                    this.$message({
                        showClose: true,
                        message: '手机号格式不正确，请输入正确的手机号',
                        type: 'error'
                    });
                    return false;
                }
                var _this = this;
                $.get('/index/login/sendsms',{
                    tel : _this.ruleForm.tel,
                },function(res){
                    if(!res.err) {
                        _this.smsState = false
                        _this.text = "已发送(" + _this.countDown+")";
                        var interval = setInterval(function(){
                            _this.countDown = _this.countDown - 1;
                            if(_this.countDown == 0){
                                _this.text = '获取验证码';
                                _this.countDown = 120;
                                clearInterval(interval);
                                _this.smsState = true
                                return;
                            }
                            _this.text = "已发送(" + _this.countDown+")";
                        },1000)
                    }else{
                        _this.$message({
                            showClose: true,
                            message: res.msg,
                            type: 'error'
                        });
                    }
                });
            },
            submitForm:function(formName) {
                var _this = this;
                _this.errMsg = ''
                _this.$refs[formName].validate(function(valid){
                    if (valid) {
                        _this.loading = true
                        $.post('/index/login/login', {
                            tel: _this.ruleForm.tel,
                            yzm: _this.ruleForm.yzm,
                            remember : _this.remember ? 1 : 0
                        }, function(res){
                            if (!res.err) {
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