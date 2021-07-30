function initList () {
	subscribers = $('#subscribers').DataTable({
        pageLength: 25,
		ajax: {
			url: '/get_subscribers',
            "dataType": "json",
            "type": "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
		},
        "columns": [
            { "data": "email" },
            { "data": "name" },
            { "data": "country" },
            { "data": "date" },
            { "data": "time" },
            { "data": "delete" }
        ]	,
        processing: true,
        serverSide: true,
        language: {
            "search": "Search Email:"
         }
	});
}

initList();

$(document).on('click', '.delete', function(event) {
    var id = $(event.target).data("id");
    $.get( "subdelete/"+id, function( data ) {
        subscribers.columns.adjust().draw(); 
    });
});