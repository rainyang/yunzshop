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
            <div id="app"  v-loading="submit_loading">
                <template>
                    <el-form ref="form" :model="form" :rules="rules" label-width="15%">
                        <el-form-item label="选择分类" prop="classification">
                            <el-input v-model="form.classification" style="width:60%;" disabled></el-input>
                            <el-button type="primary" @click="visDia()">选择分类</el-button>
                            <el-dialog title="选择分类" :visible.sync="dialogTableVisible">
                                <!-- <el-input v-model="form.search_name"  placeholder="请输入分类名称" style="width:90%"></el-input> -->
                                <el-select
                                    value-key="id"
                                    @change="change"
                                    v-model="form.search_categorys"
                                    filterable
                                    multiple
                                    remote
                                    reserve-keyword
                                    placeholder="请输入分类名称"
                                    :remote-method="loadCategorys"
                                    :loading="loading"
                                    style="width:100%">
                                    <el-option
                                        v-for="item in categorys"
                                        :key="item.id"
                                        :label="'[ID:'+item.id+'][分类:'+item.name+']'"
                                        :value="item"
                                        >
                                        <!-- :value="'[ID:'+item.id+'][分类:'+item.name+']'" -->
                                    </el-option>
                                </el-select>
                                <!-- <el-button @click="search()">搜索</el-button><br> -->
                                {{--<div v-for="(item,index) in list">[[item.name]]</div>--}}
                                <span slot="footer" class="dialog-footer">
                                    <!-- <el-button @click="dialogVisible = false">取 消</el-button> -->
                                    <el-button type="primary" @click="choose()">确 定</el-button>
                                </span>
                            </el-dialog>
                        </el-form-item>
                        <el-form-item label="折扣类型" prop="type">
                            <el-radio v-model.number="form.discount_type" :label="1">会员等级</el-radio>
                        </el-form-item>
                        <el-form-item label="折扣方式" prop="method">
                            <el-radio v-model.number="form.discount_method" :label="1">折扣</el-radio>
                            <el-radio v-model.number="form.discount_method" :label="0">固定金额</el-radio>
                        </el-form-item>
                        <el-form-item>
                            <template v-for="(item,index) in member_list">
                                <el-input v-model="form.discount[index]" style="width:70%;padding:10px 0;">
                                    <template slot="prepend">[[item.level_name]]</template>
                                    <template slot="append" v-if="!form.discount_method">元</template>
                                    <template slot="append" v-if="form.discount_method">%</template>
                                </el-input>
                            </template>
                        </el-form-item>
                        
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
                let member_list = JSON.parse('{!! $levels?:'{}' !!}');
                console.log(member_list);
                // let list =[];
                return{
                    form:{
                        discount_type:1,
                        discount_method:1,
                        // member:"",
                        discount:[],
                        search_categorys:""
                    },
                    classic:[],
                    member_list:member_list,
                    categorys:[],
                    dialogVisible:true,
                    // list:list,
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
            // <!-- :value="'[ID:'+item.id+'][分类:'+item.name+']'" -->
            methods: {
                change(item){
                    for(var k=0;k<item.length;k++){
                        this.classic[k] = "[ID:"+item[k].id+"][分类："+item[k].name+"]";
                    }
                    // console.log(val)
                    console.log(this.classic);
                },
                visDia(){
                    this.dialogTableVisible=true;
                    console.log("haaaaa");
                },
                choose(){
                    this.dialogTableVisible=false;
                    console.log(this.form.search_categorys);
                    this.form.classification = this.classic.join(",");
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
                
                loadCategorys(query) {
                    if (query !== '') {
                        this.loading = true;
                        this.$http.get("{!! yzWebUrl('from.batch-discount.select-category', ['keyword' => '']) !!}" + query).then(response => {
                            this.categorys = response.data.data;
                            this.data=response.data.data;
                            this.loading = false;
                        }, response => {
                            console.log(response);
                        });
                    } else {
                        this.categorys = [];
                    }
                },
                submitForm(formName) {
                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            var obj={};
                            var index=[];
                            for(var i=0;i<this.member_list.length;i++){
                                index[i] = this.member_list[i].id
                                obj[''+index[i]+''] = this.form.discount[i];
                            }
                            this.form.discount=obj;
                            this.submit_loading = true;
                            delete(this.form['thumb_url']);
                            this.$http.post("{!! yzWebUrl('from.batch-discount.store-set') !!}",{'form_data':this.form}).then(response => {
                                console.log(this.form);
                                console.log(response);
                                if (response.data.result) {
                                    this.$message({type: 'success',message: '操作成功!'});
                                    window.location.href='{!! yzWebFullUrl('from.batch-discount.index') !!}';
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



