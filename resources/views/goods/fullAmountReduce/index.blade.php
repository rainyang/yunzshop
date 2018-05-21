@extends('layouts.base')
@section('title', '满额优惠设置')
@section('content')

    <div class="rightlist">
        @include('layouts.tabs')
        <div id="test-vue">
            <el-form ref="form" :model="form" label-width="17%">
                <el-form-item label="满额减">
                    <el-button @click="add" type="primary">+增加优惠项目</el-button>
                </el-form-item>
                <template v-for="item in items">
                    <el-form-item :inline="true" label="活动区域">
                        <el-input-group>
                            <el-input placeholder="金额" v-model="item.value" suffix="¥" size="small">
                                <template slot="prepend">满</template>
                                <template slot="append">元</template>
                            </el-input>
                            <el-input placeholder="金额" v-model="item.value" suffix="¥" size="small">
                                <template slot="prepend">减</template>
                                <template slot="append">元</template>
                            </el-input>
                            <el-button type="danger" icon="el-icon-delete" circle @click="remove(this)"></el-button>
                        </el-input-group>
                    </el-form-item>
                </template>
            </el-form>
        </div>


    </div>

    <script>
        Vue.component('c1', {
            props: ['item'],
            template: '#c1'
        })
        var app = new Vue({
            el: '#test-vue',
            delimiters: ['[[', ']]'],
            data() {
                return {
                    items: [],
                    form: {
                        name: '',
                        region: '',
                        date1: '',
                        date2: '',
                        delivery: false,
                        type: [],
                        resource: '',
                        desc: ''
                    }
                }
            },
            methods: {
                add() {
                    this.items.push({
                        value: ''
                    })
                },
                remove(item) {
                    var i = this.items.indexOf(item)
                    this.items.splice(i, 1)
                },
                onSubmit() {
                    console.log('submit!');
                }
            }
        });
    </script>
@endsection

