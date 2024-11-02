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
                    // {field: 'id', title: '序号'},
                    {field: 'order_sn', title: '订单编号' ,width:200},
                    {field: 'identity_shop', title: '门店' ,search:'false',templet: function(d){
                        if (d.identity_shop == null) {
                            return '-';
                        } else {
                            return d.identity_shop+' '+d.shop_phone;
                        }
                    }},
                    {field: 'identity_supplier', title: '业务员' ,search:'false',templet: function(d){
                        if (d.identity_supplier == null) {
                            return '-';
                        } else {
                            return d.identity_supplier+' '+d.supplier_phone;
                        }
                    }},
                    // {field: 'user_id', title: '用户ID'},
                    {field: 'total_amount', title: '总金额' ,search:'false', templet: ea.table.price},
                    {field: 'goods_num', title: '商品数量' ,search:'false'},
                    {field: 'gift_num', title: '赠品数量' ,search:'false'},
                    // {field: 'supplier_id', title: '供应商ID'},
                    // {field: 'dealer_id', title: '经销商ID'},
                    {field: 'order_name', title: '收货人'},
                    {field: 'order_phone', title: '手机号'},
                    // {field: 'order_address',title: '收货地址'},
                    {field: 'order_address',title: '收货地址' ,templet: function(d){
                        return d.province+''+d.city+''+d.area+''+d.order_address;
                    }},
                    {field: 'create_time', minWidth: 80, title: '创建时间', search: 'range'},
                    // {width: 250, title: '操作', templet: ea.table.tool ,operat:['edit']},

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