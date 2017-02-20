应用总目录
    1.frontend   前台
        controller 控制器
            order (对应原/addons/sz_yi/core/mobile/order)
                confirm (对应原/addons/sz_yi/core/mobile/order/confirm.php)
                    Base.php    (过度使用,重构完成后 所有逻辑应转移到 模块中)
                    Display.php (对应confirm.php中的if($operation=='display'))
    2.modules    模块
        goods
            model
                backend
                frontend
                common
    db_models  数据库model
    backend    后端(同1)
    plugins    插件
        commission
            frontend   前端(同1)
            modules    模块(同2)