define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'company.user/index',
        add_url: 'company.user/add',
        edit_url: 'company.user/edit',
        delete_url: 'company.user/delete',
        export_url: 'company.user/export',
        modify_url: 'company.user/modify',
    };

    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: 'checkbox'},
                    {field: 'id', title: '用户表'},
                    {field: 'openid', title: 'openid'},
                    {field: 'nick_name', title: '昵称'},
                    {field: 'phone', title: '手机号'},
                    {field: 'avatar', title: '头像'},
                    {field: 'grade_id', title: '等级id'},
                    {field: 'type', search: 'select', selectList: {"1":"供应商","2":"经销商","3":"业务员","4":"店铺","5":"普通用户"}, title: '用户类型'},
                    {field: 'create_time', title: '创建时间'},
                    // {field: 'name', title: '账号'},
                    // {field: 'password', title: '密码'},
                    {field: 'status', title: '状态', templet: ea.table.switch},
                    {width: 250, title: '操作', templet: ea.table.tool},

                ]],
            });

            ea.listen();
        },
        add: function () {
            ea.listen();
        },
        edit: function () {
            ea.listen();
        },
    };
});