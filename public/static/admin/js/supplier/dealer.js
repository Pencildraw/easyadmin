define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'supplier.dealer/index',
        add_url: 'supplier.dealer/add',
        edit_url: 'supplier.dealer/edit',
        delete_url: 'supplier.dealer/delete',
        export_url: 'supplier.dealer/export',
        modify_url: 'supplier.dealer/modify',
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