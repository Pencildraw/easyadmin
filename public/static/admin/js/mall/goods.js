define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'mall.goods/index',
        add_url: 'mall.goods/add',
        edit_url: 'mall.goods/edit',
        delete_url: 'mall.goods/delete',
        export_url: 'mall.goods/export',
        modify_url: 'mall.goods/modify',
    };

    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: 'checkbox'},                    {field: 'id', title: '商品表'},                    {field: 'name', title: '商品名称'},                    {field: 'price', title: '价格'},                    {field: 'attr', title: '描述'},                    {field: 'status', title: '装填', templet: ea.table.switch},                    {field: 'purchase_price', title: '进价'},                    {field: 'create_time', title: '创建时间'},                    {field: 'inventory', title: '库存'},                    {field: 'sales_volume', title: '销量'},                    {field: 'cate_id', title: '商品分类ID'},                    {field: 'supplier_id', title: '供应商ID'},                    {field: 'salesman_remind', title: '业务员提点'},                    {field: 'shipping_cost', title: '发货费用'},                    {width: 250, title: '操作', templet: ea.table.tool},
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