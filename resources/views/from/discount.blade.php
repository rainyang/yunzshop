@extends('layouts.base')
@section('title', '折扣设置')
@section('content')
    <style>
        .el-form-item__label{padding-right:30px;}
    </style>

    <div class="w1200 m0a">
        @include('layouts.tabs')
        
        <div class="rightlist">
            <div id="app"  v-loading="submit_loading">
                <template>
                    <el-form ref="form" :model="form" :rules="rules" label-width="15%">
                        <el-form-item label="分类批量" prop="batch_list">
                            <template v-for="(item,index) in form.batch_list">
                                <el-input v-model="item.name" style="width:60%;padding:10px 0;"></el-input>
                                <el-button @click="settingBatch(index,form.batch_list[index].id)">设置折扣</el-button>
                                <el-button type="danger" icon="el-icon-close" @click="delBatch(index,form.batch_list[index].id)"></el-button>
                            </template><br>
                            <el-button type="primary" @click="addBatch()">添加批量折扣</el-button>
                        </el-form-item>
                    </el-form>
                </template>
            </div>
        </div>
    </div>
    
    <script>
        var vm = new Vue({
        el:"#app",
        delimiters: ['[[', ']]'],
            data() {
                let form = {
                    batch_list:[
                        {id:1,name:"分类1"},
                        {id:2,name:"分类2"},
                        {id:3,name:"分类3"},
                        {id:4,name:"分类4"},
                        ],
                };
                return{
                    form:form,
                    loading: false,
                    submit_loading: false,
                    rules: {
                        // sort: [
                        //     { required: true,type: 'number', message: '请输入数字'},
                        //     { type: 'number', min: 1, max: 99999, message: '请输入1-99999'},
                        // ],
                        // name: [
                        //     { required: true,message: '请输入分类名称', trigger: 'blur' },
                        //     { max : 45,message: '不能超过45个字符', }
                        // ],
                        // thumb: [
                        //     { required: true, message: '请选择图片'},
                        //  ]
                    },
                }
            },
            methods: {
                addBatch(){
                    this.form.batch_list.push(
                        {   
                            id:"",
                            name:""
                        }
                    )
                },
                delBatch(index,id){
                    this.$confirm('确定删除吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                        this.table_loading=true;
                        this.$http.post('{!! yzWebFullUrl('#') !!}',{id:id}).then(function (response) {
                        console.log(response.data);
                        if (response.data.result) {
                            this.form.batch_list.splice(index,1);
                            this.$message({type: 'success',message: '删除成功!'});
                        }
                        else{
                            this.$message({type: 'error',message: response.data.msg});
                        }
                        this.table_loading=false;
                    },function (response) {
                        this.$message({type: 'error',message: response.data.msg});
                        this.table_loading=false;
                    }
                    );
                    }).catch(() => {
                        this.$message({type: 'info',message: '已取消删除'});
                        });
                },
                settingBatch(index,id) {
                    console.log(index,id);
                    window.location.href='{!! yzWebFullUrl('from.batch-discount.store') !!}';
                },
            },
        });
    </script>
@endsection



