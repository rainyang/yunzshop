@extends('layouts.base')
@section('title', '服务费设置')
@section('content')
    <style>

    </style>
    <div id="app-vue">
        <template>
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#" ><i class="fa" v-html="form.service.name" ></i>设置</a></li>
                </ul>
            </div>
            <div class="rightlist">
                <div id="test-vue">
                    <el-form ref="form" :rules="rules" :model="form" label-width="17%">

                        <el-form-item :label="form.service.name">

                            <el-radio v-model.number="form.service.open" :label=1>开启</el-radio>
                            <el-radio v-model.number="form.service.open" :label=0>关闭</el-radio>
                        </el-form-item>

                        <el-form-item label="自定义名称">
                            <el-form-item prop="service.name">
                                <el-input :placeholder="form.service.name"
                                          v-model.number="form.service.name" size="medium"
                                          style="width: 27%">
                                </el-input>
                                {{--<span v-html="form.service.name"></span>--}}
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
        </template>
    </div>
    <script>
        var app = new Vue({
            el: '#app-vue',
            delimiters: ['[[', ']]'],
            data() {
                // 默认数据
                let temp = JSON.parse('{!! $setting?:'{}' !!}');
                console.log(temp);
                // if (!temp || temp=={}) {
                //     console.log(11,'111')
                //     temp = {
                //         service: {
                //             'open': 0,
                //             'name': '服务费',
                //         },
                //
                //     }
                // }
                let temp1 = {
                        service: {
                            'open': 0,
                            'name': '服务费',
                        },
                        ...temp,
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
                    form: temp1,
                    props: {
                        label: 'areaname',
                        children: 'children',
                        isLeaf: 'isLeaf'
                    },
                    name:'11111',
                    loading: false,
                    formLoading: false,
                    centerDialogVisible: false,
                    treeData: [],
                    rules: rules
                }
            },
            mounted: function () {
                console.log(this.form.service.name,'2222')
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
                    this.$http.post("{!! yzWebUrl('plugin.service-fee.admin.setting.setting') !!}", {'setting': this.form}).then(response => {
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
