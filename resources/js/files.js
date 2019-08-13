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
        { data: 'actions', name: 'actions', orderable: false, searchable: false},
    ],
    columnDefs: [
        {
            "render": function(data, type, row){
                var deletion_url = row.delete_url;
                var view_url = row.view_url;
                return "<a href='" + view_url + "'><button class='btn btn-info mr-2'>View</button></a><a href='" + deletion_url + "'><button class='btn btn-danger'>Delete</button></a>";
            },
            "targets": 3
        }
    ]
});
