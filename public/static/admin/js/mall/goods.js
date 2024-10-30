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
                    {type: 'checkbox'},
                    {field: 'name', title: '商品名称'},
                    {field: 'images', title: '主图', templet: ea.table.lazyimg},
                    {field: 'price', title: '价格', templet: ea.table.price},
                    {field: 'purchase_price', title: '进价', templet: ea.table.price},
                    {field: 'shipping_cost', title: '运营物流费',templet: ea.table.price},
                    {field: 'sales_volume', title: '销量'},
                    {field: 'attr', title: '描述',templet: ea.table.text},
                    {field: 'create_time', title: '创建时间'},
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