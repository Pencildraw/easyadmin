define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'dealer.order/index',
        add_url: 'dealer.order/add',
        edit_url: 'dealer.order/edit',
        delete_url: 'dealer.order/delete',
        export_url: 'dealer.order/export',
        modify_url: 'dealer.order/modify',
    };

    return {
        index: function () {
            ea.table.render({
                init: init,
                toolbar: ['refresh'],
                cols: [[
                    {type: 'checkbox'},
                    {field: 'id', title: '序号'},
                    {field: 'order_sn', title: '订单编号'},
                    {field: 'transaction_id', title: '支付编号'},
                    // {field: 'order_status', title: '订单状态'},
                    // {field: 'user_id', title: '用户ID'},
                    {field: 'total_amount', title: '总金额' ,search:'false'},
                    {field: 'order_amount', title: '订单金额' ,search:'false'},
                    {field: 'ok_amount', title: '已支付金额' ,search:'false'},
                    {field: 'goods_num', title: '商品数量' ,search:'false'},
                    {field: 'gift_num', title: '赠品数量' ,search:'false'},
                    // {field: 'supplier_id', title: '供应商ID'},
                    // {field: 'dealer_id', title: '经销商ID'},
                    // {field: 'order_name', title: '用户'},
                    // {field: 'order_phone', title: '用户手机号'},
                    {title: '订单信息' ,templet: function(d){
                        return d.order_name+' '+d.order_phone+' '+d.order_address;
                    }},
                    {field: 'create_time', minWidth: 80, title: '创建时间', search: 'range'},
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