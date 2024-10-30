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