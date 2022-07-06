let imgPromo;
let imgBanner;
let extension;
let extensionBanner;
let positionsArr = [];


function updatePromo( promo_id ){

    promoTitle = document.getElementById('promoTitle').value;
    begin_date = document.getElementById('begin_date').value;
    end_date = document.getElementById('end_date').value;
    awards_qty = document.getElementById('awards_qty').value;

    if(imgPromo && extension){
        image = imgPromo._value;
    }else{
        image = "";
    }

    if( imgBanner && extensionBanner ){
        imageBanner = imgBanner._value
    }else{
        imageBanner = "";
    }

    Swal.fire({
        title: '¿Deseas guardar la promoición?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {

            loading();
            $.ajax({
                url: routeG+'admin/update/promo',
                data: {
                    promoTitle,
                    begin_date,
                    end_date,
                    awards_qty,
                    promo_id,
                    image,
                    imageBanner,
                    extension,
                    extensionBanner,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    Swal.close();
                    if(resp.result == 'ok'){
                        imgPromo = null;
                        imgBanner = null;
                        document.getElementById('addImagePromo').value = '';
                        document.getElementById('addImageBanner').value = '';
                        alertSuccess();
                    }else{
                        console.log(resp)
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}

function downPromo(promo_id){

    Swal.fire({
        title: '¿Deseas dar de baja la promoción?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: routeG+'admin/down/promo',
                data: {
                    promo_id,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    if(resp.result == 'ok'){
                        // document.getElementById('bodyTableBody').innerHTML = resp.table;
                        
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'La promoción fue dada de baja',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        location.reload();
                    }else{
                        console.log(resp)
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}

function insertPromo(){
    
    promoTitle = document.getElementById('promoTitle').value;
    begin_date = document.getElementById('begin_date').value;
    end_date = document.getElementById('end_date').value;
    awards_qty = document.getElementById('awards_qty').value;

    if(imgPromo && extension){
        image = imgPromo._value;
    }else{
        image = "";
    }

    if( imgBanner && extensionBanner ){
        imageBanner = imgBanner._value
    }else{
        imageBanner = "";
    }

    Swal.fire({
        title: '¿Deseas agregar la promoción?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {
            loading();
            $.ajax({
                url: routeG+'admin/add/promo',
                data: {
                    promoTitle,
                    begin_date,
                    end_date,
                    awards_qty,
                    image,
                    imageBanner,
                    extension,
                    extensionBanner,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    Swal.close();
                    console.log(resp);
                    if(resp.result == 'ok'){
                        document.getElementById('bodyTable').innerHTML = resp.table;
                        
                        $('#addPromo').modal({
                            keyboard: false
                        });
                        $('#addPromo').modal('hide');

                        document.getElementById("formAddPromo").reset();
                    }else{
                        console.log(resp)
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}

function endPromotion(promo_id){

    Swal.fire({
        title: '¿Deseas terminar la promoción?',
        text: 'Antes de terminar la promoción favor de revisar si los ganadores ya estan cargados',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: routeG+'admin/down/end/promotion',
                data: {
                    promo_id,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    // console.log(resp);
                    if(resp.result == 'ok'){
                        // document.getElementById('bodyTableBody').innerHTML = resp.table;
                        
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'La promoción fue dada de baja',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        location.reload();
                    }else{
                        console.log(resp)
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}

async function changeImgToB64Promo(inputName){
    let file = document.getElementById(inputName).files[0];
    extension = document.getElementById(inputName).files[0]['name'].split('.').pop()
    const  fileType = file['type'];
    const validImageTypes = ['image/gif', 'image/jpeg', 'image/png'];
    if (!validImageTypes.includes(fileType)) {
        console.log('nonImage');
        return;
    }else{
        imgPromo = toBase64(file);
    }
}

async function changeImgToB64Banner(inputName){
    let file = document.getElementById(inputName).files[0];
    extensionBanner = document.getElementById(inputName).files[0]['name'].split('.').pop()
    const  fileType = file['type'];
    const validImageTypes = ['image/gif', 'image/jpeg', 'image/png'];
    if (!validImageTypes.includes(fileType)) {
        console.log('nonImage');
        return;
    }else{
        imgBanner = toBase64(file);
    }
}

toBase64 = file => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
});

function loading(){
    let timerInterval
    Swal.fire({
        title: 'Cargando..',
        didOpen: () => {
            Swal.showLoading()
        },
        willClose: () => {
            clearInterval(timerInterval)
        }
    }).then((result) => {
        /* Read more about handling dismissals below */
        if (result.dismiss === Swal.DismissReason.timer) {
            console.log('I was closed by the timer')
        }
    })
}

function alertSuccess(){
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Cambios guardados',
        showConfirmButton: false,
        timer: 1500
    });
}
