define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'company.dealer/index',
        add_url: 'company.dealer/add',
        edit_url: 'company.dealer/edit',
        delete_url: 'company.dealer/delete',
        export_url: 'company.dealer/export',
        modify_url: 'company.dealer/modify',
    };

    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: 'checkbox'},                    {field: 'id', title: '经销商'},                    {field: 'name', title: '名称'},                    {field: 'phone', title: '联系方式'},                    {field: 'email', title: '邮件'},                    {field: 'status', title: '状态', templet: ea.table.switch},                    {field: 'create_time', title: '创建时间'},                    {field: 'dealer_name', title: '经销商名称'},                    {field: 'supplier_id', title: '供应商ID'},                    {field: 'goods_id', title: '代理商品ID'},                    {width: 250, title: '操作', templet: ea.table.tool},
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