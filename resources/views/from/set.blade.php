@extends('layouts.base')
@section('title', '折扣设置')
@section('content')
    <style>
        #app{padding:30px 0;}
        .el-form-item__label{padding-right:30px;}
    </style>

    <div class="w1200 m0a">
        @include('layouts.tabs')
        
        <div class="rightlist">
            <div id="app"  v-loading="submit_loading" v-loading="submit_loading">
                <template>
                    <el-form ref="form" :model="form" :rules="rules" label-width="15%">
                        <el-form-item label="选择分类" prop="classification">
                            <el-input v-model="form.classification" style="width:60%;" disabled></el-input>
                            <el-button type="primary" @click="visDia()">选择分类</el-button>
                            <el-dialog title="选择分类" :visible.sync="dialogTableVisible">
                                <!-- <el-input v-model="form.search_name"  placeholder="请输入分类名称" style="width:90%"></el-input> -->
                                <el-select
                                    v-model="form.search_name"
                                    filterable
                                    multiple
                                    remote
                                    reserve-keyword
                                    placeholder="请输入分类名称"
                                    :remote-method="loadMembers"
                                    :loading="loading"
                                    style="width:100%">
                                    <el-option
                                        v-for="item in members"
                                        :key="item.uid"
                                        :label="'[UID:'+item.uid+'][昵称:'+item.nickname+']'"
                                        :value="item.uid">
                                    </el-option>
                                </el-select>
                                <!-- <el-button @click="search()">搜索</el-button><br> -->
                                <div v-for="(item,index) in list">[[item.name]]</div>
                                <span slot="footer" class="dialog-footer">
                                    <!-- <el-button @click="dialogVisible = false">取 消</el-button> -->
                                    <el-button type="primary" @click="dialogTableVisible = false">确 定</el-button>
                                </span>
                            </el-dialog>
                        </el-form-item>
                        <el-form-item label="折扣类型" prop="type">
                            <el-radio v-model.number="form.type" :label="1">会员等级</el-radio>
                        </el-form-item>
                        <el-form-item label="折扣方式" prop="way">
                            <el-radio v-model.number="form.way" :label="1">折扣</el-radio>
                            <el-radio v-model.number="form.way" :label="0">固定金额</el-radio>
                        </el-form-item>
                        <el-form-item>
                            <template v-for="(item,index) in member_list">
                                <el-input v-model="form.value[index]" style="width:70%;padding:10px 0;">
                                    <template slot="prepend">[[item.name]]</template>
                                    <template slot="append">元</template>
                                </el-input>
                            </template>
                        </el-form-item>
                            <!-- <el-input v-model="form.member" style="width:70%;padding:10px 0;">
                                <template slot="prepend">芸众会员1434</template>
                                <template slot="append">元</template>
                            </el-input> -->
                            <!-- <el-input v-model="form.member" style="width:70%;padding:10px 0;">
                                <template slot="prepend">桃心</template>
                                <template slot="append">元</template>
                            </el-input>
                            <el-input v-model="form.member" style="width:70%;padding:10px 0;">
                                <template slot="prepend">等级三</template>
                                <template slot="append">元</template>
                            </el-input>
                            <el-input v-model="form.member" style="width:70%;padding:10px 0;">
                                <template slot="prepend">会员等级四</template>
                                <template slot="append">元</template>
                            </el-input>
                            <el-input v-model="form.member" style="width:70%;padding:10px 0;">
                                <template slot="prepend">黑心</template>
                                <template slot="append">元</template>
                            </el-input> -->
                        
                    <el-form-item>
                        <a href="#">
                            <el-button type="success" @click="submitForm('form')">
                                提交
                            </el-button>
                        </a>
                        <a href="#">
                            <el-button @click="goBack()">
                                返回列表
                            </el-button>
                        </a>
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
                    type:1,
                    way:1,
                    member:"",
                    value:[],
                    search_name:"",
                    batch_list:[
                        {id:1,name:"分类1"},
                        {id:2,name:"分类2"},
                        {id:3,name:"分类3"},
                        {id:4,name:"分类4"},
                        ],
                };
                let members=[];
                let member_list = [
                        {id:1,name:"芸众会员1434"},
                        {id:2,name:"桃心"},
                        {id:3,name:"等级3"},
                        {id:4,name:"会员等级4"},
                        {id:4,name:"黑心"},
                ];
                let list =[];
                return{
                    form:form,
                    member_list:member_list,
                    members:members,
                    dialogVisible:true,
                    list:list,
                    dialogTableVisible:false,
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
                visDia(){
                    this.dialogTableVisible=true;
                    console.log("haaaaa");
                },
                goBack() {
                    history.back(-1);
                },
                search() {
                    // this.list =[
                    //     {name:"1212"},
                    //     {name:"1212"},
                    //     {name:"1212"},
                    //     {name:"1212"},

                    // ]
                    console.log("search");
                },
                
                loadMembers(query) {
                    if (query !== '') {
                        this.loading = true;
                        this.$http.get("{!! yzWebUrl('#', ['keyword' => '']) !!}" + query).then(response => {
                            this.members = response.data.data;
                            this.data=response.data.data;
                            this.loading = false;
                        }, response => {
                            console.log(response);
                        });
                    } else {
                        this.members = [];
                    }
                },
                submitForm(formName) {
                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            this.submit_loading = true;
                            delete(this.form['thumb_url']);
                            this.$http.post("{!! yzWebFullUrl('#') !!}",{'form_data':this.form}).then(response => {
                                if (response.data.result) {
                                    this.$message({type: 'success',message: '操作成功!'});
                                    window.location.href='{!! yzWebFullUrl('plugin.asset.Backend.Modules.Category.Controllers.records') !!}';
                                } else {
                                    this.$message({message: response.data.msg,type: 'error'});
                                    this.submit_loading = false;
                                }
                            },response => {
                                this.submit_loading = false;
                            });
                        }
                        else {
                            console.log('error submit!!');
                            return false;
                        }
                    });
                },
                
            },
        });
    </script>
@endsection



