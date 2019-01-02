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
                                <el-select
                                    value-key="id"
                                    @change="change"
                                    v-model="form.search_categorys"
                                    filterable
                                    multiple
                                    remote
                                    reserve-keyword
                                    :placeholder="form.classification"
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
                            <el-radio v-model="form.discount_type" :label="1">会员等级</el-radio>
                        </el-form-item>
                        <el-form-item label="会员折扣方式" prop="method">
                            <el-radio v-model="form.discount_method" :label="1">折扣</el-radio>
                            <el-radio v-model="form.discount_method" :label="2">固定金额</el-radio>
                        </el-form-item>
                        <el-form-item prop="">
                            <template v-for="(item,index) in member_list">
                                <el-input type="number" v-model.number="form.discount_value[item.id]" style="width:70%;padding:10px 0;">
                                    <template slot="prepend">[[item.level_name]]</template>
                                    <template slot="append" v-if="form.discount_method==1">折</template>
                                    <template slot="append" v-if="form.discount_method==2">元</template>
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
                let url = JSON.parse('{!! $url !!}');
                let categoryDiscount = JSON.parse('{!! $categoryDiscount?:'{}' !!}');
                console.log(categoryDiscount);
                let form ={
                        discount_type:1,
                        discount_method:1,
                        discount_value:[],
                        classification:"",
                        search_categorys:"",
                        ...categoryDiscount
                    };

                let classic =[];
                form.classification = classic.join(",");

                // var checkNumber = (rule, value, callback) => {
                //     if (!Number.isInteger(value)) {
                //         callback(new Error('请输入数字'));
                //     }
                //     setTimeout(() => {
                //         callback();
                //     }, 1000);
                // };

                return{
                    url:url,
                    form:form,
                    classic:classic,
                    member_list:member_list,
                    categorys:[],
                    dialogVisible:true,
                    dialogTableVisible:false,
                    loading: false,
                    submit_loading: false,
                    rules: {
                        // discount_value: [
                        //     { required: false,type: 'number', message: '请输入数字'},
                        //     { type: 'number', min: 1, max: 99999, message: '请输入1-99999'},
                            // { validator : checkNumber }
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
            mounted:function() {
                console.log("hahah");
                if(this.form.category_ids) {
                    for(var j=0;j<this.form.category_ids.length;j++){
                        this.classic[j] = "[ID:"+this.form.category_ids[j].id+"][分类："+this.form.category_ids[j].name+"]";
                    }
                }
                this.form.classification = this.classic.join(",");
            },
            watch: {
                classic(){
                    this.search_categorys = this.classic.join(",")
                }
            },
            methods: {
                change(item){
                    for(var k=0;k<item.length;k++){
                        this.classic[k] = "[ID:"+item[k].id+"][分类："+item[k].name+"]";
                    }
                    // console.log(this.classic);
                },
                visDia(){
                    this.dialogTableVisible=true;
                },
                choose(){
                    this.dialogTableVisible=false;
                    this.form.classification = this.classic.join(",");
                },
                goBack() {
                    window.location.href='{!! yzWebFullUrl('discount.batch-discount.index') !!}';
                },
                loadCategorys(query) {
                    if (query !== '') {
                        this.loading = true;
                        this.$http.get("{!! yzWebUrl('discount.batch-discount.select-category', ['keyword' => '']) !!}" + query).then(response => {
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
                    // if(this.form.discount_method == 1){
                    //     for(let i=0;i<this.member_list.length;i++){
                    //         if(this.form.discount_value[i]<10||this.form.discount_value[i]>0){
                    //             this.$message({message: "折扣数值不能大于10或者小于0",type: 'error'});
                    //             return false;
                    //         }
                    //     }
                    // }

                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            this.submit_loading = true;
                            this.$http.post(this.url,{'form_data':this.form}).then(response => {
                                console.log(this.form);
                                if (response.data.result) {
                                    this.$message({type: 'success',message: '操作成功!'});
                                    window.location.href='{!! yzWebFullUrl('discount.batch-discount.index') !!}';
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


