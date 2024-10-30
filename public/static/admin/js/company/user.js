define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'company.user/index',
        add_url: 'company.user/add',
        edit_url: 'company.user/edit',
        delete_url: 'company.user/delete',
        export_url: 'company.user/export',
        modify_url: 'company.user/modify',
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