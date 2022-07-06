
function searchInfoWallet(user_id){

    console.log(user_id);
    // return;
    $.ajax({
        url: routeG + 'admin/get/user/wallet/' + user_id,
        data: {
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            document.getElementById('bodyTableTransaction').innerHTML = resp.table;

        },
        error: function(err) {
            console.log(err);
        }
    });
        
}
