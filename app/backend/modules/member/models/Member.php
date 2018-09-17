<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午1:55
 */

namespace app\backend\modules\member\models;

class Member extends \app\common\models\Member
{
    static protected $needLog = true;


    public function address()
    {
        return $this->hasMany('app\backend\modules\member\models\MemberAddress', 'uid', 'uid');
    }

    /**
     * @param $keyWord
     *
     */
    public static function getMemberByName($keyWord)
    {
        return self::uniacid()
            ->searchLike($keyWord)
            ->whereHas('yzMember', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with('yzMember')
            ->with('hasOneFans')
            ->get();
    }

    /**
     * 获取会员列表
     *
     * @return mixed
     */
    public static function getMembers()
    {
        return self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid()
            ->whereHas('yzMember', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['yzMember' => function ($query) {
                return $query->select(['member_id', 'parent_id', 'is_agent', 'group_id', 'level_id', 'is_black'])->uniacid()
                    ->with(['group' => function ($query1) {
                        return $query1->select(['id', 'group_name'])->uniacid();
                    }, 'level' => function ($query2) {
                        return $query2->select(['id', 'level', 'level_name'])->uniacid();
                    }, 'agent' => function ($query3) {
                        return $query3->select(['uid', 'avatar', 'nickname'])->uniacid();
                    }]);
            }, 'hasOneFans' => function ($query4) {
                return $query4->select(['uid', 'openid', 'follow as followed'])->uniacid();
            }, 'hasOneOrder' => function ($query5) {
                return $query5->selectRaw('uid, count(uid) as total, sum(price) as sum')
                    ->uniacid()
                    ->where('status', 3)
                    ->groupBy('uid');
            }])
            ->orderBy('uid', 'desc');
    }

    /**
     * 获取会员信息
     *
     * @param $id
     * @return mixed
     */
    public static function getMemberInfoById($id)
    {
        return self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid()
            ->where('uid', $id)
            ->whereHas('yzMember', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['yzMember' => function ($query) {
                return $query->select(['member_id', 'parent_id', 'is_agent', 'group_id', 'level_id', 'is_black', 'alipayname', 'alipay', 'content', 'status', 'custom_value', 'validity', 'member_form', 'withdraw_mobile','wechat'])->where('is_black', 0)
                    ->with(['group' => function ($query1) {
                        return $query1->select(['id', 'group_name']);
                    }, 'level' => function ($query2) {
                        return $query2->select(['id', 'level', 'level_name']);
                    }, 'agent' => function ($query3) {
                        return $query3->select(['uid', 'avatar', 'nickname']);
                    }]);
            }, 'hasOneFans' => function ($query2) {
                return $query2->select(['uid', 'follow as followed']);
            }, 'hasOneOrder' => function ($query5) {
                return $query5->selectRaw('uid, count(uid) as total, sum(price) as sum')
                    ->uniacid()
                    ->where('status', 3)
                    ->groupBy('uid');
            }
            ])
            ->first();
    }

    /**
     * 获取会员信息（不判断黑名单）
     * @param $id
     * @return mixed
     */
    public static function getMemberInfoBlackById($id)
    {
        return self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid()
            ->where('uid', $id)
            ->whereHas('yzMember', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['yzMember' => function ($query) {
                return $query->select(['member_id', 'parent_id', 'is_agent', 'group_id', 'level_id', 'is_black', 'alipayname', 'alipay', 'content', 'status', 'custom_value', 'validity', 'member_form', 'withdraw_mobile','wechat'])
                    ->with(['group' => function ($query1) {
                        return $query1->select(['id', 'group_name']);
                    }, 'level' => function ($query2) {
                        return $query2->select(['id', 'level', 'level_name']);
                    }, 'agent' => function ($query3) {
                        return $query3->select(['uid', 'avatar', 'nickname']);
                    }]);
            }, 'hasOneFans' => function ($query2) {
                return $query2->select(['uid', 'follow as followed']);
            }, 'hasOneOrder' => function ($query5) {
                return $query5->selectRaw('uid, count(uid) as total, sum(price) as sum')
                    ->uniacid()
                    ->where('status', 3)
                    ->groupBy('uid');
            }
            ])
            ->first();
    }

    /**
     * 获取会员基本信息
     *
     * @param $id
     * @return mixed
     */
    public static function getMemberBaseInfoById($id)
    {
        return self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid()
            ->where('uid', $id)
            ->whereHas('yzMember', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['yzMember' => function ($query) {
                return $query->select(['member_id', 'parent_id', 'is_agent', 'group_id', 'level_id', 'is_black', 'alipayname', 'alipay', 'content', 'status', 'custom_value', 'validity', 'member_form', 'withdraw_mobile','wechat'])->where('is_black', 0)
                    ->with(['group' => function ($query1) {
                        return $query1->select(['id', 'group_name']);
                    }, 'level' => function ($query2) {
                        return $query2->select(['id', 'level', 'level_name']);
                    }, 'agent' => function ($query3) {
                        return $query3->select(['uid', 'avatar', 'nickname']);
                    }]);
            }, 'hasOneFans' => function ($query2) {
                return $query2->select(['uid', 'follow as followed', 'unionid']);
            }
            ])
            ->first();
    }

    /**
     * 更新会员信息
     *
     * @param $data
     * @param $id
     * @return mixed
     */
    public static function updateMemberInfoById($data, $id)
    {
        return self::uniacid()
            ->where('uid', $id)
            ->update($data);
    }

    /**
     * 检索会员信息
     *
     * @param $parame
     * @return mixed
     */
    public static function searchMembers($parame, $credit = null)
    {
        if (!isset($credit)) {
            $credit = 'credit2';
        }
        $result = self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid();

        if (!empty($parame['search']['mid'])) {
            $result = $result->where('uid', $parame['search']['mid']);
        }
        if (isset($parame['search']['searchtime']) && $parame['search']['searchtime'] == 1) {
            if ($parame['search']['times']['start'] != '请选择' && $parame['search']['times']['end'] != '请选择') {
                $range = [strtotime($parame['search']['times']['start']), strtotime($parame['search']['times']['end'])];
                $result = $result->whereBetween('createtime', $range);
            }
        }

        if (!empty($parame['search']['realname'])) {
            $result = $result->where(function ($w) use ($parame) {
                $w->where('nickname', 'like', '%' . $parame['search']['realname'] . '%')
                    ->orWhere('realname', 'like', '%' . $parame['search']['realname'] . '%')
                    ->orWhere('mobile', 'like', $parame['search']['realname'] . '%');
            });
        }

        $result = $result->whereHas('yzMember', function ($query) use ($parame) {
            $query->whereNull('deleted_at');

            if($parame['search']['custom_value']){
                $query->where('custom_value', 'like', '%' . $parame['search']['custom_value'] . '%');
            }


        });

        if (!empty($parame['search']['groupid']) || !empty($parame['search']['level']) || $parame['search']['isblack'] != ''
            || $parame['search']['isagent'] != ''
        ) {

            $result = $result->whereHas('yzMember', function ($q) use ($parame) {
                if (!empty($parame['search']['groupid'])) {
                    $q = $q->where('group_id', $parame['search']['groupid']);
                }

                if (!empty($parame['search']['level'])) {
                    $q = $q->where('level_id', $parame['search']['level']);
                }

                if ($parame['search']['isblack'] != '') {
                    $q->where('is_black', $parame['search']['isblack']);
                }

                if ($parame['search']['isagent'] != '') {
                    $q->where('is_agent', $parame['search']['isagent']);
                }
            });
        }

        //余额区间搜索
        if ($parame['search']['min_credit2']) {
            $result = $result->where($credit, '>', $parame['search']['min_credit2']);
        }
        if ($parame['search']['max_credit2']) {
            $result = $result->where($credit, '<', $parame['search']['max_credit2']);
        }

        if ($parame['search']['followed'] != '') {
            $result = $result->whereHas('hasOneFans', function ($q2) use ($parame) {
                $q2->where('follow', $parame['search']['followed']);
            });
        }


        $result = $result->with(['yzMember' => function ($query) {
            return $query->select(['member_id', 'parent_id', 'inviter', 'is_agent', 'group_id', 'level_id', 'is_black', 'withdraw_mobile'])
                ->with(['group' => function ($query1) {
                    return $query1->select(['id', 'group_name'])->uniacid();
                }, 'level' => function ($query2) {
                    return $query2->select(['id', 'level_name'])->uniacid();
                }, 'agent' => function ($query3) {
                    return $query3->select(['uid', 'avatar', 'nickname'])->uniacid();
                }]);
        }, 'hasOneFans' => function ($query4) {
            return $query4->select(['uid', 'follow as followed'])->uniacid();
        }, 'hasOneOrder' => function ($query5) {
            return $query5->selectRaw('uid, count(uid) as total, sum(price) as sum')
                ->uniacid()
                ->groupBy('uid');
        }]);

        return $result;
    }

    /**
     * 获取会员关系链资料申请
     *
     * @return mixed
     */
    public static function getMembersToApply($filters)
    {
        $filters['referee_id'] = [];
        if ($filters['referee'] == '1' && $filters['referee_info']) {
            $query = self::select(['uid'])
                ->uniacid()
                ->searchLike($filters['referee_info'])
                ->get();

            if (!empty($query)) {
                $data = $query->toArray();

                foreach ($data as $item) {
                    $filters['referee_id'][] = $item['uid'];
                }
            }
        }

        $query = self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile']);
        $query->uniacid();

        if (isset($filters['member'])) {
            $query->searchLike($filters['member']);
        }

        $query->whereHas('yzMember', function ($query) use ($filters) {
            if (isset($filters['searchtime']) && $filters['searchtime'] == 1) {
                if ($filters['times']) {
                    $range = [strtotime($filters['times']['start']), strtotime($filters['times']['end'])];
                    $query = $query->whereBetween('apply_time', $range);
                }
            }

            if ($filters['referee'] == '0') {
                $query->where('parent_id', $filters['referee']);
            } elseif ($filters['referee'] == '1' && !empty($filters['referee_id'])) {
                $query->whereIn('parent_id', $filters['referee_id']);
            }

            $query->where('status', 1);
        });

        $query->with(['yzMember' => function ($query) {
            return $query->select(['member_id', 'parent_id', 'apply_time'])
                ->with(['agent' => function ($query3) {
                    return $query3->select(['uid', 'avatar', 'nickname']);
                }]);
        }])
            ->orderBy('uid', 'desc');
        return $query;
    }

    /**
     * 推广下线
     *
     * @param $request
     * @return mixed
     */
    public static function getAgentInfoByMemberId($request)
    {
        $query = self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid();

        if ($request->keyword) {
            $query->searchLike($request->keyword);
        }

        $query->whereHas('yzMember', function ($query) use ($request) {
            $query->where('parent_id', $request->id)->where('inviter', 1);

            if ($request->aid) {
                $query->where('member_id', $request->aid);
            }

            if ($request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->isblack != '') {
                $query->where('is_black', $request->isblack);
            }

        });

        if ($request->followed != '') {
            $query->whereHas('hasOneFans', function ($jq) use ($request) {
                $jq->where('follow', $request->followed);
            });
        }

        $query->with(['yzMember' => function ($query) {
            return $query->select(['member_id', 'parent_id', 'is_agent', 'group_id', 'level_id', 'is_black', 'status'])->with(['agent' => function ($query) {
                return $query->select(['uid', 'avatar', 'nickname']);
            }]);
        }, 'hasOneFans' => function ($query) {
            return $query->select(['uid', 'follow as followed']);
        }
        ])
            ->orderBy('uid', 'desc');

        return $query;
    }
}