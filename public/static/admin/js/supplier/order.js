define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'supplier.order/index',
        add_url: 'supplier.order/add',
        edit_url: 'supplier.order/edit',
        delete_url: 'supplier.order/delete',
        export_url: 'supplier.order/export',
        modify_url: 'supplier.order/modify',
    };

    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: 'checkbox'},
                    {field: 'id', title: '订单表'},
                    {field: 'order_status', title: '订单状态'},
                    // {field: 'user_id', title: '用户ID'},
                    {field: 'order_name', title: '下单用户'},
                    {field: 'order_phone', title: '下单用户手机号'},
                    {field: 'order_address', title: '下单用户地址'},
                    {field: 'total_amount', title: '下单总金额'},
                    {field: 'order_amount', title: '下单订单金额'},
                    {field: 'ok_amount', title: '下单已支付金额'},
                    // {field: 'supplier_id', title: '供应商ID'},
                    // {field: 'dealer_id', title: '经销商ID'},
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