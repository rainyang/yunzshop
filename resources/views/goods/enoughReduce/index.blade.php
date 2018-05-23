@extends('layouts.base')
@section('title', '满额优惠设置')
@section('content')
    <style>

    </style>
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>满额优惠设置</a></li>
        </ul>
    </div>
    <div class="rightlist">
        @include('layouts.tabs')
        <div id="test-vue">
            <el-form ref="form" :model="form" label-width="17%">

                <el-form-item label="满额减">

                    <template v-for="enoughReduce in form.enoughReduce">
                        <el-row>
                                <el-input placeholder="金额" v-model="enoughReduce.enough" size="medium" style="width: 25%">
                                    <template slot="prepend">满</template>
                                    <template slot="append">元</template>
                                </el-input>
                                <el-input placeholder="金额" v-model="enoughReduce.reduce" size="medium" style="width: 25%">
                                    <template slot="prepend">减</template>
                                    <template slot="append">元</template>
                                </el-input>
                                <el-button type="danger" plain size="mini" @click="remove(this)">x</el-button>
                        </el-row>
                    </template>

                    <el-button @click="add">增加优惠项目</el-button>

                </el-form-item>


                <el-form-item label="满额包邮">

                    <el-row>
                        <el-tooltip :content="form.open?'已开启':'已关闭'" placement="top">
                            <el-switch v-model="form.freeFreight.open">
                            </el-switch>
                        </el-tooltip>
                    </el-row>

                    <el-input placeholder="金额" v-model="form.freeFreight.enough" size="medium" style="width: 27%">
                        <template slot="prepend">满</template>
                        <template slot="append">元包邮</template>
                    </el-input>

                </el-form-item>

                <el-form-item label="不参与地区">
                    <el-row>
                        <el-tag
                                v-for="city in form.freeFreight.cities"
                                :key="city">
                            [[city]]
                        </el-tag>
                    </el-row>

                    <el-button @click="centerDialogVisible = true">编辑不参加包邮地区</el-button>
                    <el-dialog
                            title="请选择地区"
                            :visible.sync="centerDialogVisible"
                            center>

                        <el-tree
                                v-loading="loading"
                                :props="props"
                                node-key="id"
                                :default-checked-keys="form.freeFreight.city_ids"
                                :default-expanded-keys="form.freeFreight.province_ids"
                                show-checkbox
                                lazy
                                accordion
                                ref="addressTree"
                                :data="treeData"
                                :load="loadNode">
                        </el-tree>

                        <span slot="footer" class="dialog-footer">
                            <el-button @click="centerDialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="saveAreas">确 定</el-button>
                        </span>

                    </el-dialog>


                </el-form-item>

                <el-form-item>
                    <el-button type="success" @click="onSubmit">提交</el-button>
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
                return {
                    // form: {
                    //     enoughReduce: [{'enough': 100, 'reduce': 10}, {'enough': 200, 'reduce': 20}],
                    //     freeFreight: {
                    //         'open': false,
                    //         'enough': 88,
                    //         'cities': ['北京市', '天津市'],
                    //         'city_ids': [110100, 120100],
                    //         'province_ids': [110000, 120000],
                    //     },
                    //
                    // },
                    form:{!! $setting !!},
                    props: {
                        label: 'areaname',
                        children: 'children',
                        isLeaf: 'isLeaf'
                    },
                    loading: false,
                    centerDialogVisible: false,
                    treeData: []
                }
            },

            methods: {
                add() {
                    this.form.enoughReduce.push(
                        {
                            'enough': '',
                            'reduce': ''
                        }
                    )
                },
                remove(item) {
                    let i = this.form.enoughReduce.indexOf(item)
                    this.form.enoughReduce.splice(i, 1)
                },
                onSubmit() {
                    this.$http.post("{!! yzWebUrl('goods.enough-reduce.store') !!}", {'setting': this.form}).then(response => {
                        console.log(response);
                        return;
                        window.location.href = 'http://www.ddhbb.com';
                    }, response => {
                        console.log(response);
                    });
                },
                handleClose(area) {
                    this.form.areas.splice(this.form.areas.indexOf(area), 1);
                },
                loadNode(node, resolve) {
                    this.loading = true;
                    if (!node.data.id) {
                        //省份
                        node.data.id = 0;
                        this.$http.get("{!! yzWebUrl('area.list', ['parent_id'=> 0]) !!}").then(response => {
                            response.data.data.forEach(function (province) {
                                province.isLeaf = false;
                            });
                            resolve(response.data.data);

                            this.loading = false;
                        }, response => {
                            console.log(response);
                        });
                    } else {
                        //城市
                        this.$http.get("{!! yzWebUrl('area.list', ['parent_id'=> '']) !!}" + node.data.id).then(response => {
                            //城市没有子节点
                            response.data.data.forEach(function (city) {
                                city.isLeaf = true;
                            })
                            resolve(response.data.data);
                            // 载入数据后,刷新已选中
                            this.loading = false;
                        }, response => {
                            console.log(response);
                        });
                    }
                },
                saveAreas() {
                    let cities = [];
                    let city_ids = [];
                    this.$refs.addressTree.getCheckedNodes().forEach(function (node) {

                        if (node.level == 2) {
                            city_ids.push(node.id);
                            cities.push(node.areaname)
                        }
                    });
                    this.form.freeFreight.city_ids = city_ids;
                    this.form.freeFreight.cities = cities;
                    this.centerDialogVisible = false

                }

            }
        });
    </script>
@endsection

