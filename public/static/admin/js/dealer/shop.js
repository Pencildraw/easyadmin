define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'dealer.shop/index',
        add_url: 'dealer.shop/add',
        edit_url: 'dealer.shop/edit',
        delete_url: 'dealer.shop/delete',
        export_url: 'dealer.shop/export',
        modify_url: 'dealer.shop/modify',
    };

    return {
        index: function () {
            ea.table.render({
                init: init,
                toolbar:['add'],
                defaultToolbar:['filter'],
                cols: [[
                    {type: 'checkbox'},
                    {field: 'name', title: '名称(账号)'},
                    {field: 'phone', title: '联系方式'},
                    {field: 'status', title: '状态', templet: ea.table.switch},
                    {field: 'goods_id', title: '代理商品'},
                    {field: 'create_time', title: '创建时间'},
                    {width: 250, title: '操作', templet: ea.table.tool
                        ,operat:['edit']
                    },

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