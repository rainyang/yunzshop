@extends('layouts.base')
@section('title', '编辑优惠券')
@section('content')

    <div id="app-coupon">
        <el-tabs v-model="defaultActive" type="card">

            <el-tab-pane label="基本信息" name="baseInfo">
                <el-form ref="form2" :model="form2" label-width="17%">
                    <el-form-item label="排序">
                        <el-input placeholder="排序"
                                  v-model.number="form2.sort" size="medium">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="优惠券名称">
                        <el-input placeholder="优惠券名称"
                                  v-model.number="form2.name" size="medium">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="是否开启">
                        <el-switch v-model="form2.status">
                        </el-switch>
                        <el-tooltip class="item" effect="dark" placement="top-end">
                            <div slot="content">关闭后,用户无法领取, 但是已经被领取的可以继续使用</div>
                            <i class="el-icon-info"></i>
                        </el-tooltip>
                    </el-form-item>
                </el-form>
            </el-tab-pane>

            <el-tab-pane label="优惠方式" name="discountType">
                <el-form ref="form" :model="form" label-width="17%">
                    <el-form-item label="使用条件 - 订单金额">
                        <el-input placeholder="金额"
                                  v-model.number="form.enough" size="medium">
                            <template slot="prepend">满</template>
                            <template slot="append">元</template>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="使用条件 - 会员等级">
                        <el-select ref="test" v-model="form.memberLevel" value-key="id"
                                   placeholder="请选择">
                            <el-option
                                    v-for="memberLevel in memberLevels"
                                    :key="memberLevel.id"
                                    :label="memberLevel.name"
                                    :value="memberLevel">
                            </el-option>

                        </el-select>
                        <el-tooltip class="item" effect="dark" placement="top-end">
                            <div slot="content"><strong>[[form.memberLevel.name]]</strong>及<strong>[[form.memberLevel.name]]</strong>以上等级的会员可用
                            </div>
                            <i class="el-icon-info"></i>
                        </el-tooltip>

                    </el-form-item>
                    <el-form-item label="使用时间">
                        <el-row class="el-input--medium">
                            <el-col :span="2.5">

                                <div class="el-input-group__prepend">
                                    <el-radio v-model="form.time_limit">日期</el-radio>
                                </div>
                            </el-col>
                            <el-col :span="10.5">
                                <el-date-picker
                                        v-model="form.dateRange"
                                        type="daterange"
                                        range-separator="至"
                                        start-placeholder="开始日期"
                                        end-placeholder="结束日期"
                                        size="medium"
                                >
                                </el-date-picker>
                            </el-col>
                            <el-col :span="2.5">

                                <div class="el-input-group__append">
                                    内有效
                                </div>
                            </el-col>
                        </el-row>

                    </el-form-item>
                    <el-form-item label="">
                        <el-input placeholder="金额"
                                  v-model.number="form.enough"
                                  size="medium"
                                  style="width:50%"
                        >
                            <template slot="prepend">
                                <el-radio v-model="form.time_limit">获取后</el-radio>
                            </template>
                            <template slot="append">天内有效</template>
                        </el-input>

                    </el-form-item>
                </el-form>
            </el-tab-pane>
        </el-tabs>

    </div>

@endsection('content')
@section('js')
    <script>
        var app = new Vue({
            el: '#app-coupon',
            delimiters: ['[[', ']]'],
            data() {

                return {
                    rules: {},
                    form: {
                        enough: 0,
                        dateRange: '',
                        memberLevel: {
                            id: '2',
                            name: '默认'
                        },
                        time_limit: ''
                    },
                    form2: {
                        sort: '',
                        name: '',
                        status: '',
                    },
                    defaultActive: 'discountType',
                    memberLevels: [
                        {
                            id: '1',
                            name: '红心',
                        },
                        {
                            id: '2',
                            name: '默认'
                        },
                        {
                            id: '3',
                            name: '其他'
                        }
                    ]
                }
            },
            mounted: function () {
            },
            methods: {
                handleSelect(key, keyPath) {
                    console.log(key, keyPath);
                }
            }
        });
    </script>
@endsection('js')
