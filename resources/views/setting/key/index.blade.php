@extends('layouts.base')

@section('title','注册芸商城')

@section('content')
    <script src="../addons/yun_shop/static/yunshop/js/industry.js"></script>
    <script type="text/javascript">
        function formcheck(event) {

            if ($(':input[name="upgrade[key]"]').val() == '' || $(':input[name="upgrade[secret]"]').val() == '') {
                if($(':input[name="upgrade[key]"]').val() == '')
                    Tip.focus(':input[name="upgrade[key]"]', 'Key 不能为空');
                else
                    Tip.focus(':input[name="upgrade[secret]"]', '密钥不能为空')
                return false;
            }
            return true
        }
    </script>
<div class="w1200 m0a">
<div class="rightlist">
<!-- 新增加右侧顶部三级菜单 -->
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">注册商城</a></li>
        </ul>
    </div>
    <div class="form-group message-box" style="display: none">
        <div class="span4">
            <div class="alert alert-block">
                <a class="close" data-dismiss="alert">×</a>
                <span id="message"></span>
            </div>
        </div>
    </div>
    <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-default" style="width:996px;">
            <div class='panel-body'>

                <div id="register">
                    <template>
                        <el-row v-show="page=='register'">
                            <el-button type="info" @click="redirect(1)" plain>免费版</el-button>
                            <el-button type="primary" @click="redirect(2)" plain>授权版</el-button>
                        </el-row><!--register end-->

                        <el-form ref="form" :model="form" :rules="rules" label-width="100px" class="demo-ruleForm" v-show="page=='free'">
                            <el-form-item label="公司名称" prop="name">
                                <el-input v-model="form.name" placeholder="请输入公司名称" autocomplete="off"></el-input>
                            </el-form-item>
                            <el-form-item label="行业" prop="trades">
                                <el-select v-model="form.trades" value-key="id" style="width:100%" placeholder="请选择行业">
                                    <el-option v-for="item in opt_trades.data"
                                               :key="item.id"
                                               :label="item.name"
                                               :value="item">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="所在区域" required>
                                <el-col :span="4">
                                    <el-form-item prop="province">
                                        <el-select v-model="form.province" value-key="id" placeholder="省" @change="change_province">
                                            <el-option v-for="item in opt_province"
                                                       :key="item.id"
                                                       :label="item.areaname"
                                                       :value="item">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col style="text-align: center" :span="1">-</el-col>
                                <el-col :span="4">
                                    <el-form-item prop="city">
                                        <el-select v-model="form.city" value-key="id" placeholder="市" @change="change_city">
                                            <el-option v-for="item in opt_city"
                                                       :key="item.id"
                                                       :label="item.areaname"
                                                       :value="item">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col style="text-align: center" :span="1">-</el-col>
                                <el-col :span="4">
                                    <el-form-item prop="area">
                                        <el-select v-model="form.area" value-key="id" placeholder="区">
                                            <el-option v-for="item in opt_area"
                                                       :key="item.id"
                                                       :label="item.areaname"
                                                       :value="item">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-form-item>
                            <el-form-item label="详细地址" prop="address">
                                <el-input v-model="form.address" placeholder="请输入详细地址" autocomplete="off"></el-input>
                            </el-form-item>
                            <el-form-item label="手机号" prop="mobile">
                                <el-input placeholder="请输入手机号" v-model="form.mobile" style="width:200px" autocomplete="off"></el-input>
                                <el-button type="info" @click="sendSms()" style="width:150px; margin-left: 50px" plain :disabled="isDisabled">[[captcha_text]]</el-button>
                            </el-form-item>
                            <el-form-item label="验证码" prop="captcha">
                                <el-input v-model="form.captcha" style="width:150px" placeholder="请输入验证码"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click.native.prevent="onSubmit" v-loading="formLoading">提交</el-button>
                            </el-form-item>
                        </el-form><!--free end-->

                        <div v-show="page=='auth'">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">Key</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="text" name="upgrade[key]" class="form-control" value="{{ $set['key'] }}" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">密钥</label>
                                <div class="col-sm-9 col-xs-12">
                                    <input type="text" name="upgrade[secret]" class="form-control" value="{{ $set['secret'] }}" />
                                </div>
                            </div>

                            <div class="form-group"></div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-9 col-xs-12">
                                    @if(!$set['secret'] || !$set['key'])
                                        <input type="hidden" name="type" value="create" />
                                        <input type="submit" name="submit" value="注册商城" class="btn btn-success " onclick="return formcheck(this)" />
                                    @else
                                        <input type="hidden" name="type" value="cancel" />
                                        <input type="submit" name="submit" value="取消商城" class="btn btn-success " onclick="return formcheck(this)" />
                                    @endif
                                </div>
                            </div>
                        </div><!--auth end-->
                    </template>
                </div>
            </div>
        </div>
</div>
</div>
    <script>
        var app = new Vue({
            el: '#register',
            delimiters: ['[[', ']]'],
            data() {
                // 默认数据
                let redirectUrl = JSON.parse('{!! $url !!}');
                let page = JSON.parse('{!!  $page !!}');
                let province = JSON.parse('{!! $province !!}');

                var validateMobile = (rule, value, callback) => {
                    if (!(/^1\d{10}$/.test(value))) {
                        callback(new Error('手机号格式不正确'));
                    } else {
                        callback();
                    }
                };

                return {
                    page: page.type,
                    redirectUrl:redirectUrl,
                    form: {
                        name: '',
                        trades: '',
                        province: '',
                        city: '',
                        area: '',
                        address: '',
                        mobile: '',
                        captcha: ''
                    },
                    opt_trades: industry,
                    opt_province: province.data,
                    opt_city:'',
                    opt_area:'',
                    t: 60,
                    captcha_text: '获取验证码',
                    isDisabled: false,
                    formLoading: false,
                    rules: {
                        name: [
                            { required: true, message: '请输入公司名称', trigger: 'blur' }
                        ],
                        trades: [
                            { required: true, message: '请选择行业', trigger: 'change' }
                        ],
                        province: [
                            { required: true, message: '请选择省', trigger: 'change' }
                        ],
                        city: [
                            { required: true, message: '请选择市', trigger: 'change' }
                        ],
                        area: [
                            { required: true, message: '请选择区', trigger: 'change' }
                        ],
                        address: [
                            { required: true, message: '请输入详情地址', trigger: 'blur' }
                        ],
                        mobile: [
                            { required: true, message: '请输入手机号', trigger: 'blur' },
                            { validator: validateMobile, trigger: 'blur' }
                        ],
                        captcha: [
                            { required: true, message: '请输入验证码', trigger: 'blur' }
                        ]
                    }
                }
            },
            mounted: function () {
            },
            methods: {
                redirect:function (type) {
                    switch (type) {
                        case 1:
                            location.href = this.redirectUrl.free;
                            break;
                        case 2:
                            location.href = this.redirectUrl.auth;
                            break;
                    }
                },
                sendSms:function () {
                    let that = this;
                    let rTime = that.t;

                    if (that.form.mobile.length >= 0) {
                        this.$refs.form.validateField('mobile');
                        return false;
                    }

                    // 倒计时
                    let interval = window.setInterval(() => {
                        if (--that.t <= 0) {
                            that.t = rTime;
                            that.isDisabled = false;
                            that.captcha_text = '获取验证码';

                            window.clearInterval(interval);
                        } else {
                            that.isDisabled = true;
                            that.captcha_text = '(' + that.t + 's)后重新获取';
                        }
                    }, 1000);
                },
                change_province: function (item) {
                    let that = this;
                    that.$http.post("{!! yzWebUrl('setting.key.getcity') !!}", {'data': item}).then(response => {

                        if (response.data.result) {
                            that.opt_city = response.data.data;
                        } else {
                            this.$message({
                                message: '未获取到数据',
                                type: 'error'
                            });
                        }

                        this.formLoading = false;
                    }, response => {
                        console.log(response);
                    });
                },
                change_city: function (item) {
                    let that = this;
                    that.$http.post("{!! yzWebUrl('setting.key.getarea') !!}", {'data': item}).then(response => {

                        if (response.data.result) {
                        that.opt_area = response.data.data;
                    } else {
                        this.$message({
                            message: '未获取到数据',
                            type: 'error'
                        });
                    }

                    this.formLoading = false;
                }, response => {
                        console.log(response);
                    });
                },
                onSubmit: function () {
                    if (this.formLoading) {
                        return;
                    }

                    this.$refs.form.validate((valid) => {
                        if (valid) {
                            /*this.$http.post("{!! yzWebUrl('plugin.share-chain.admin.set.sub') !!}", {'setting': this.form}).then(response => {
                                if (response.data.result) {
                                    this.$message({
                                        message: response.data.msg,
                                        type: 'success'
                                    });
                                    window.location.reload();
                                } else {
                                    this.$message({
                                        message: response.data.msg,
                                        type: 'error'
                                    });
                                }

                                 this.formLoading = false;
                            }, response => {
                                console.log(response);
                            });*/
                        } else {
                            return false;
                }
                });
                }
            },
            watch: {
                'form.province': function (newValue, oldValue) {
                    this.form.city = null
                    this.opt_city = [{id:0,areaname:'请选择'}];
                    this.form.area = null
                    this.opt_area = [{id:0,areaname:'请选择'}];
                },
                'form.city': function (newValue, oldValue) {
                    this.form.area = null
                    this.opt_area = [{id:0,areaname:'请选择'}];
                }
            }
        });
    </script>
@endsection
