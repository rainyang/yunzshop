@extends('layouts.base')
<style>
    .text {
        font-size: 14px;
    }

    .item {
        margin-bottom: 18px;
    }

    .item>span{
        display: inline-block;
    }
    .clearfix:before,
    .clearfix:after {
        display: table;
        content: "";
    }
    .clearfix:after {
        clear: both
    }

    .box-card {
        width: 50%;
        margin-top:20px;
        float:left;
    }

    .box-log {
        width: 48%;
        margin-top:20px;
        float:right;
    }
</style>
@section('content')
<div class="w1200 m0a">
<div class="rightlist">

    @include('layouts.tabs')
    <div id="app">

        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span>队列管理</span>
                <span style="float: right;">
                <el-button size="small" v-if="stopAllState" disabled type="info"><i style="" class="el-icon-loading"></i>停止进程中...</el-button>
                <el-button size="small" v-else @click="stopAll" type="info">停止所有进程<i style="" class="el-icon-caret-right el-icon--right"></i></el-button>
                <el-button  size="small" v-if="startAllState" disabled type="success"><i style="" class="el-icon-loading"></i>启动进程中...</el-button>
                <el-button size="small" v-else  @click="startAll" type="success">启动所有进程<i style="" class="el-icon-caret-right el-icon--right"></i></el-button>
                <el-button size="small" v-if="restartState" disabled type="primary"><i style="" class="el-icon-loading"></i>重启队列中...</el-button>
                <el-button size="small" @click="restart" type="primary">重启队列<i style="" class="el-icon-caret-right el-icon--right"></i></el-button>
                </span>
            </div>
            <div v-for="supervisor in list"  class="text item">
                <span style="width:30%">${ supervisor.name }</span>
                <span style="width:30%">
                    <el-button round v-if="supervisor.statename == 'RUNNING'" type="text" size="small">已启动<i style="" class="el-icon-circle-check-outline el-icon--right"></i></el-button>
                    <el-button round v-else type="danger" size="small">已停止<i style="" class="el-icon-circle-close-outline el-icon--right"></i></el-button>
                </span>
                <span style="width:30%; float:right">
                    <el-button @click="stop(supervisor)" v-if="supervisor.statename == 'RUNNING'" type="info" size="small">停止<i style="" class="el-icon-close el-icon--right"></i></el-button>
                    <el-button @click="start(supervisor)" v-else type="success" size="small">启动<i style="" class="el-icon-caret-right el-icon--right"></i></el-button>
                    <el-button @click="showlog(supervisor)" type="info" size="small">日志<i style="" class="el-icon-search el-icon--right"></i></el-button>
                </span>
            </div>
        </el-card>

        <el-card class="box-log" v-if="log">
            <div slot="header" class="clearfix">
                <span>日志查看(${ currentProcess.name })</span>
                <span style="float: right;">
                <el-button size="small" @click="clearLog" type="info">清除日志<i style="" class="el-icon-caret-right el-icon--right"></i></el-button>
                <el-button  size="small" @click="reloadLog" type="success">重载<i style="" class="el-icon-caret-right el-icon--right"></i></el-button>
                </span>
            </div>
            <div  class="text item" style="height:800px;overflow:auto">
                ${ log[0] || '还没有日志哦' }
            </div>
        </el-card>
    </div>
</div>
</div>
    @include('public.admin.mylink')
@endsection

@section('js')
    <script>
        new Vue({
            el: '#app',
            delimiters: ['${', '}'],
            data: {
                list:[],
                log:'',
                state:2,
                currentProcess:'',
                lodingState: false,
                stopAllState: false,
                startAllState: false,
                restartState: false,
            },

            methods: {
                processlist () {
                    var that = this;
                    let url = "{!! yzWebUrl("supervisord.supervisord.process") !!}";
                    console.log(url);
                    axios.get(url)
                            .then(function (response) {
                                console.log('response:', response.data);
                                if (response.data.process.errno == 0) {
                                    that.list = response.data.process.val;
                                    that.state = response.data.state.statecode;
                                } else {
                                    that.$message.error('错了哦,' + response.data.errstr);
                                }
                                //that.$Message.success('提交成功啦');

                            })
                            .catch(function (error) {
                                console.log('error:', error);
                                //that.$Message.error('提交失败啦');
                            });
                },
                stopAll () {
                    var that = this;
                    this.stopAllState = true;
                    let url = "{!! yzWebUrl("supervisord.supervisord.stopAll") !!}";
                    console.log(url);
                    axios.get(url)
                            .then(function (response) {
                                console.log('response:', response.data);
                                if (response.data.errno == 0) {
                                    that.$message({
                                        message: '已停止所有进程',
                                        type: 'success'
                                    });
                                    that.stopAllState = false;

                                    that.processlist();
                                } else {
                                    that.$message.error('错了哦,' + response.data.errstr);
                                    that.stopAllState = false;
                                }
                                //that.$Message.success('提交成功啦');

                            })
                            .catch(function (error) {
                                console.log('error:', error);
                                //that.$Message.error('提交失败啦');
                            });
                },

                startAll () {
                    var that = this;
                    let url = "{!! yzWebUrl("supervisord.supervisord.startAll") !!}";
                    console.log(url);
                    this.startAllState = true;
                    console.log('this.startAllState', this.startAllState);
                    axios.get(url)
                            .then(function (response) {
                                console.log('response:', response.data);
                                if (response.data.errno == 0) {
                                    that.$message({
                                        message: '已启动所有进程',
                                        type: 'success'
                                    });
                                    that.startAllState = false;
                                    console.log('this.startAllState', that.startAllState);

                                    that.processlist();
                                } else {
                                    that.$message.error('错了哦,' + response.data.errstr);
                                    that.startAllState = false;
                                }
                                //that.$Message.success('提交成功啦');

                            })
                            .catch(function (error) {
                                console.log('error:', error);
                                //that.$Message.error('提交失败啦');
                            });
                },

                restart () {
                    var that = this;
                    this.restartState = true;

                    let url = "{!! yzWebUrl("supervisord.supervisord.restart") !!}";
                    console.log(url);
                    axios.get(url)
                            .then(function (response) {
                                console.log('response:', response.data);
                                if (response.data.errno == 0) {
                                    that.$message({
                                        message: '已重启队列',
                                        type: 'success'
                                    });

                                    that.processlist();
                                    that.restartState = true;
                                } else {
                                    that.restartState = true;
                                    that.$message.error('错了哦,' + response.data.errstr);
                                }
                                //that.$Message.success('提交成功啦');

                            })
                            .catch(function (error) {
                                console.log('error:', error);
                                //that.$Message.error('提交失败啦');
                            });
                },

                showlog (process) {
                    var that = this;
                    let url = "{!! yzWebUrl("supervisord.supervisord.showlog") !!}"+"&process="+process.group + ":" + process.name;
                    console.log(url);
                    axios.get(url)
                            .then(function (response) {
                                console.log('response:', response.data);
                                if (response.data.errno == 0) {
                                    that.log = response.data.val;
                                    that.currentProcess = process;
                                } else {
                                    that.$message.error('错了哦,' + response.data.errstr);
                                }
                                //that.$Message.success('提交成功啦');

                            })
                            .catch(function (error) {
                                console.log('error:', error);
                                //that.$Message.error('提交失败啦');
                            });
                },

                clearLog () {
                    var that = this;

                    //停止状态清除无效
                    if (this.currentProcess.state == 0) {
                        this.$message.error('进程必须启动状态才能清除哦');
                        return false;
                    }
                    let url = "{!! yzWebUrl("supervisord.supervisord.clearlog") !!}"+"&process="+this.currentProcess.group + ":" + this.currentProcess.name;
                    console.log(url);
                    axios.get(url)
                            .then(function (response) {
                                //console.log('response:', response.data);
                                if (response.data.errno == 0) {
                                    that.$message({
                                        message: '日志已清除',
                                        type: 'success'
                                    });
                                    //that.processlist();
                                } else {
                                    that.$message.error('错了哦,' + response.data.errstr);
                                }
                                //that.$Message.success('提交成功啦');

                            })
                            .catch(function (error) {
                                console.log('error:', error);
                                //that.$Message.error('提交失败啦');
                            });
                },

                reloadLog () {
                    this.showlog(this.currentProcess);
                },

                stop (process) {
                    var that = this;
                    let url = "{!! yzWebUrl("supervisord.supervisord.stop") !!}"+"&process="+process.group + ":" + process.name;
                    console.log(url);
                    axios.get(url)
                            .then(function (response) {
                                console.log('response:', response.data);
                                if (response.data.val) {
                                    that.$message({
                                        message: '进程已停止',
                                        type: 'success'
                                    });
                                    that.processlist();
                                } else {
                                    that.$message.error('错了哦,' + response.data.errstr);
                                }
                                //that.$Message.success('提交成功啦');

                            })
                            .catch(function (error) {
                                console.log('error:', error);
                                //that.$Message.error('提交失败啦');
                            });
                },

                start (process) {
                    var that = this;
                    let url = "{!! yzWebUrl("supervisord.supervisord.start") !!}"+"&process="+process.group + ":" + process.name;
                    console.log(url);
                    axios.get(url)
                            .then(function (response) {
                                console.log('response:', response.data);
                                if (response.data.val) {
                                    that.$message({
                                        message: '进程已启动',
                                        type: 'success'
                                    });
                                    that.processlist();
                                } else {
                                    that.$message.error('错了哦,' + response.data.errstr);
                                }
                                //that.$Message.success('提交成功啦');

                            })
                            .catch(function (error) {
                                console.log('error:', error);
                                //that.$Message.error('提交失败啦');
                            });
                }
            },
            mounted () {
                this.processlist();
            }
        })
    </script>
@endsection('js')
