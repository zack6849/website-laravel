require("./bootstrap");
require("datatables.net");
require("datatables.net-bs4");

console.log("Loading from " + $('#files_list').data('src'));
$("#files_list").DataTable({
    responsive: true,
    autowidth: true,
    ajax: {
        url: $("#files_list").data('src'),
        "type": "POST"
    },
    columns: [
        { data: 'id', name: 'file id' },
        { data: 'filename', name: 'filename' },
        { data: 'created_at', name: 'created_at' },
    ]
});
