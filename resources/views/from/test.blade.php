@extends('layouts.base')
@section('title', '折扣设置')
@section('content')

    <div class="w1200 m0a">
        @include('layouts.tabs')
        <div id="app">
            <el-form ref="form" :model="form" label-width="80px">
                <el-form-item label="活动名称">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
                {{--<el-form-item label="活动区域">--}}
                    {{--<el-select v-model="form.region" placeholder="请选择活动区域">--}}
                        {{--<el-option label="区域一" value="shanghai"></el-option>--}}
                        {{--<el-option label="区域二" value="beijing"></el-option>--}}
                    {{--</el-select>--}}
                {{--</el-form-item>--}}
                {{--<el-form-item label="活动时间">--}}
                    {{--<el-col :span="11">--}}
                        {{--<el-date-picker type="date" placeholder="选择日期" v-model="form.date1" style="width: 100%;"></el-date-picker>--}}
                    {{--</el-col>--}}
                    {{--<el-col class="line" :span="2">-</el-col>--}}
                    {{--<el-col :span="11">--}}
                        {{--<el-time-picker type="fixed-time" placeholder="选择时间" v-model="form.date2" style="width: 100%;"></el-time-picker>--}}
                    {{--</el-col>--}}
                {{--</el-form-item>--}}
                {{--<el-form-item label="即时配送">--}}
                    {{--<el-switch v-model="form.delivery"></el-switch>--}}
                {{--</el-form-item>--}}
                {{--<el-form-item label="活动性质">--}}
                    {{--<el-checkbox-group v-model="form.type">--}}
                        {{--<el-checkbox label="美食/餐厅线上活动" name="type"></el-checkbox>--}}
                        {{--<el-checkbox label="地推活动" name="type"></el-checkbox>--}}
                        {{--<el-checkbox label="线下主题活动" name="type"></el-checkbox>--}}
                        {{--<el-checkbox label="单纯品牌曝光" name="type"></el-checkbox>--}}
                    {{--</el-checkbox-group>--}}
                {{--</el-form-item>--}}
                <el-form-item label="折扣方式">
                    {{--<el-radio-group v-model="form.resource1">--}}
                        <el-radio v-model.bool="form.resource1" :label="true">商品原价</el-radio>
                        <el-radio v-model.bool="form.resource1" :label="false">商品现价</el-radio>
                    {{--</el-radio-group>--}}
                </el-form-item>
                <el-form-item label="批量设置">
                    {{--<el-radio-group v-model="form.resource2">--}}
                        <el-radio v-model.bool="form.resource2" :label="true">分类批量</el-radio>
                        <el-radio v-model.bool="form.resource2" :label="false">商品批量</el-radio>
                    {{--</el-radio-group>--}}
                </el-form-item>

                {{--<el-form-item label="活动名称">--}}
                    {{--<el-input v-model="form.name"></el-input>--}}
                {{--</el-form-item>--}}

                <template v-for="(dpp,index) in form.dpp">
                <el-form-item label="分类批量">
                    <el-form-item>
                        <el-row :gutter="20">
                            <el-col :span="9">
                                <el-form-item v-bind:prop="'dpp.reduce-'+index">
                                    <el-input placeholder="金额"
                                              v-model.number="dpp.enough" size="medium">
                                        <template slot="prepend">选择分类</template>
                                        <template slot="append">设置折扣</template>
                                    </el-input>
                                </el-form-item>
                            </el-col>

                            <el-col :span="3">
                                <el-button plain size="mini" @click="remove(this)">x</el-button>
                            </el-col>
                        </el-row>
                    </el-form-item>
                </el-form-item>
                </template>

                <el-form-item label="">
                    <el-row>
                        <el-button @click="add">增加满减规则</el-button>
                    </el-row>
                </el-form-item>

                {{--<el-form-item label="活动形式">--}}
                    {{--<el-input type="textarea" v-model="form.desc"></el-input>--}}
                {{--</el-form-item>--}}
                <el-form-item>
                    <el-button type="primary" @click="onSubmit">立即创建</el-button>
                    <el-button>取消</el-button>
                </el-form-item>
111

                <div class="col-sm-12">
                    <input type='hidden' class='form-control' id='goodsid'
                           name='level[goods_id]' value="{{ $levelModel->goods->id }}"/>
                    <div class='input-group'>
                        <div class='input-group-addon'
                             style='border:none;background:#fff;'>
                            <label class="radio-inline" style='margin-top:-3px;'>
                                购买指定商品</label>
                        </div>

                        <input type='text' class='form-control' id='goods'
                               value="{{ $levelModel->goods->title }}" readonly/>

                        <div class="input-group-btn">
                            <button type="button"
                                    onclick="$('#modal-goods').modal()"
                                    class="btn btn-default">选择商品
                            </button>
                        </div>
                    </div>
                </div>

                <div id="modal-goods" class="modal fade" tabindex="-1">
                    <div class="modal-dialog" style='width: 920px;'>
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                <h3>选择商品</h3></div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods"
                                               placeholder="请输入商品名称"/>
                                        <span class='input-group-btn'><button type="button" class="btn btn-default"
                                                                              onclick="search_goods();">搜索</button></span>
                                    </div>
                                </div>
                                <div id="module-menus-goods" style="padding-top:5px;"></div>
                            </div>
                            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal"
                                                         aria-hidden="true">关闭</a></div>
                        </div>
                    </div>
                </div>

            </el-form>
        </div>
    </div>

    <script language='javascript'>
        var vm = new Vue({
            el: '#app',
            delimiters: ['[[', ']]'],

            data() {
                let test_data = [];
                test_data = {
                    dpp : [],
                    dii : {
                        name: '',
                        resource1: false,
                        resource2: false,
                    }
                };

                return {
                        form : test_data,
                    }
            },

            methods: {
                onSubmit() {
                     console.log(this.form);
                    this.$http.post("{!! yzWebUrl('from.batchDiscount') !!}", {'post_data': this.form}).then(response => {
                        // console.log(response);
                        // return;
                    }, response => {
                        console.log(response);
                    });
                },

                add() {
                    this.form.dpp.push(
                        {
                            'enough': '',
                            'reduce': ''
                        }
                    )
                },
                remove(item) {
                    let i = this.form.dpp.indexOf(item)
                    this.form.dpp.splice(i, 1)
                },

            }

        });

    </script>
@endsection




