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
                cols: [[
                    {type: 'checkbox'},
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