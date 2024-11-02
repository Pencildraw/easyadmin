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
                toolbar:[],
                defaultToolbar:['filter'],
                cols: [[
                    {type: 'checkbox'},
                    {field: 'identity_dealer', title: '归属' ,search:'false'},
                    {field: 'identity_salesman', title: '业务员' ,search:'false'},
                    {field: 'shop_name', title: '店铺名称'},
                    {field: 'name', title: '店铺(账号)'},
                    {field: 'phone', title: '联系方式'},
                    {field: 'status', title: '状态', templet: ea.table.switch,search:'false'},
                    // {field: 'goods_id', title: '代理商品'},
                    {field: 'qrcode_image', title: '二维码', templet: ea.table.lazyimg ,search:'false'},
                    {field: 'count_order', title: '订单数', Width: 40,search:'false'},
                    {field: 'count_goods_num', title: '销售数量', Width: 40,search:'false'},
                    {field: 'count_order_price', title: '销售额', Width: 40,templet: ea.table.price ,search:'false'},
                    {field: 'shop_address',title: '店铺地址',search:'false' ,templet: function(d){
                        return d.province+''+d.city+''+d.area+''+d.shop_address;
                    }},
                    {field: 'create_time', minWidth: 100, title: '创建时间', search: 'range'},
                    {width: 100, title: '操作', templet: ea.table.tool
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