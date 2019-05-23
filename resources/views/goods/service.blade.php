@extends('layouts.base')
@section('title', '服务费设置')
@section('content')
    <style>

    </style>
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>服务费设置</a></li>
        </ul>
    </div>
    <div class="rightlist">
        <div id="test-vue">
            <el-form ref="form" :rules="rules" :model="form" label-width="17%">

                <el-form-item label="开启服务费">
                    <el-radio v-model.bool="form.service.open" :label=1>开启</el-radio>
                    <el-radio v-model.bool="form.service.open" :label=0>关闭</el-radio>
                </el-form-item>

                <el-form-item label="自定义名称">
                    <el-form-item prop="service.name">
                        <el-input placeholder="服务费"
                                  v-model.number="form.service.name" size="medium"
                                  style="width: 27%">
                        </el-input>
                        <p class="help-block">自定义名称，为空默认服务费</p>
                    </el-form-item>
                </el-form-item>

                <el-form-item>
                    <el-button type="success" @click.native.prevent="onSubmit" v-loading="formLoading">提交
                    </el-button>
                    <el-button>取消</el-button>
                </el-form-item>
            </el-form>
        </div>


    </div>

    <script>
        var app = new Vue({
            el: '#test-vue',
            delimiters: ['[[', ']]'],
            data() {
                // 默认数据
                let temp = JSON.parse('{!! $setting !!}');
                        console.log('=================='.temp);
                if (!temp || temp.length === 0) {
                    temp = {
                        service: {
                            'open': 1,
                            'name': '服务费',
                        },

                    }
                }
                //验证规则
                let amountRules = {
                    type: 'number',
                    min: 0,
                    max: 999999999,
                    message: '请输入正确金额',
                    transform(value) {
                        console.log(value);
                        return Number(value)
                    }
                };
                let rules = {
                    // 'service.name': [],
                };

                return {
                    form: temp,
                    props: {
                        label: 'areaname',
                        children: 'children',
                        isLeaf: 'isLeaf'
                    },
                    loading: false,
                    formLoading: false,
                    centerDialogVisible: false,
                    treeData: [],
                    rules: rules
                }
            },
            mounted: function () {
                console.log(this.form)
            },
            methods: {
                onSubmit() {
                    if (this.formLoading) {
                        return;
                    }
                    this.formLoading = true;

                    this.$refs.form.validate((valid) => {
                        console.log(valid)
                });
                    this.$http.post("{!! yzWebUrl('goods.service') !!}", {'setting': this.form}).then(response => {
                        if (response.data.result) {
                        this.$message({
                            message: response.data.msg,
                            type: 'success'
                        });
                    } else {
                        this.$message({
                            message: response.data.msg,
                            type: 'error'
                        });
                    }

                    this.formLoading = false;
                }, response => {
                        console.log(response);
                    });
                },
                // handleClose(area) {
                //     this.form.areas.splice(this.form.areas.indexOf(area), 1);
                // },
                checkAreas(node,checked,children) {
                    if(node.isLeaf){
                        return;
                    }
                    if(checked){

                    }
                },

            }
        });
    </script>
@endsection

