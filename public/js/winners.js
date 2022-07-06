let awardQty;
let awardQtyArr = [];
let winnersArr = [];

function searchUsers(){

    const promoId = document.getElementById('promoId').value;

    $.ajax({
        url: routeG+'admin/searchUsers/winners',
        data: {
            promoId,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            // console.log(resp);
            if(resp.result == 'ok'){
                document.getElementById('bodyTableWinnersSelect').innerHTML = resp.table;
                awardQty = resp.awards_qty;
                for (var i = 1; i < awardQty + 1; i++) {
                    userIdAssigned = null;

                    if(resp.usersValid && resp.usersValid.length > 0){
                        resp.usersValid.forEach(({userId, position}) => {
                            if(i == position){
                                userIdAssigned = userId; 
                                winnersArr[userId] = position;
                            }
                        });
                    }
                    awardQtyArr[i] = userIdAssigned;
                    // awardQtyArr = {
                    //     ...awardQtyArr,
                    //     [i]:{
                    //         'userId': userIdAssigned,
                    //     }
                    // };
                    
                }

                console.log(awardQtyArr);
                console.log(winnersArr);

            }else{
                console.log(resp)
            }
        },
        error: function(err) {
            console.log(err);
        }
    });


}

function searchAwardPosition(userId, validUpdate = false){

    

   positionUser = document.getElementById('positionSelectedWinner'+userId).value;
   promoId = document.getElementById('promoId').value;
   document.getElementById('alertSelectWinners').innerHTML = "";
   document.getElementById('positionSelectedWinner'+userId).classList.remove('is-invalid');

    $.ajax({
        url: routeG+'admin/get/awardPosition/winner',
        data: {
            promoId,
            positionUser,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            // console.log(resp);
            if(resp.result == 'ok'){
                document.getElementById('awardWinner'+userId).value = resp.name;
                validExistI = winnersArr.indexOf(userId);
                if (userId in winnersArr) {
                    awardQtyArr[winnersArr[userId]] = null; 
                }
                winnersArr[userId] = positionUser; 
                awardQtyArr[positionUser] = userId;

                selectPosition = document.getElementById('positionSelectedWinner'+userId);
                selectPosition.setAttribute("disabled","disabled");

                
            }else{
                document.getElementById('alertSelectWinners').innerHTML = resp.message;
                document.getElementById('positionSelectedWinner'+userId).classList.add('is-invalid');
            }
            
        },
        error: function(err) {
            console.log(err);
        }
    });

    console.log(awardQtyArr);
    console.log(winnersArr);



}

function checkedEvent(checkBox,userId){

    // console.log(awardQtyArr);

    optionsPositions = "<option value='0'>Selecciona la posición</option>";

    if(checkBox){

        awardQtyArr.forEach( function(value, index) {
            if(!value){
                optionsPositions = optionsPositions + `<option value='${ index }'>${ index }</option>`;
            }
            // console.log("En el índice " + index + " hay este valor: " + value);
        });
    
        selectPosition = document.getElementById('positionSelectedWinner'+userId);
        selectPosition.innerHTML = optionsPositions;
        selectPosition.removeAttribute("disabled");
    }else{

        
        document.getElementById('awardWinner'+userId).value = "";
        selectPosition = document.getElementById('positionSelectedWinner'+userId);
        // console.log(selectPosition.value);
        // return;
        awardQtyArr = {
            ...awardQtyArr,
            [selectPosition.value]:{
                'userId': null,
                'position': selectPosition.value,
            }
        };
        selectPosition.innerHTML = optionsPositions;
        selectPosition.setAttribute("disabled","disabled");
        
    }

}

function enableUpdatePosition( userId ){

    optionsPositions = "<option value='0'>Selecciona la posición</option>";

    awardQtyArr.forEach( function(user, position) {
        if(user == userId){
            optionsPositions = optionsPositions + `<option value='${ position }' selected>${ position }</option>`;
        }

        if(!user){
            optionsPositions = optionsPositions + `<option value='${ position }'>${ position }</option>`;
        }
        // console.log("En el índice " + index + " hay este valor: " + value);
    });

    selectPosition = document.getElementById('positionSelectedWinner'+userId);
    selectPosition.innerHTML = optionsPositions;
    selectPosition.removeAttribute("disabled");

}

function deleteWinner(winnerId){


    Swal.fire({
        title: '¿Estas seguro de borrar este ganador?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Borrar`,
        denyButtonText: `Cancelar`,
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: routeG+'admin/delete/winner',
                data: {
                    winnerId,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    // console.log(resp);
                    if( resp.result == 'ok' ){
                        document.getElementById('bodyTableWinners').innerHTML = resp.table;
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


function postWinners(){

    // console.log(awardQtyArr);

    promoId = document.getElementById('promoId').value;
    
    Swal.fire({
        title: '¿Estas seguro de agregar a los ganadores?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: routeG+'admin/post/winners/score',
                data: { 
                    awardQtyArr,
                    promoId,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    // console.log(resp);
                    if(resp.result == 'ok'){

                        document.getElementById('bodyTableWinners').innerHTML = resp.table;

                        $('#addWinner').modal({
                            keyboard: false
                        });
                        $('#addWinner').modal('hide');

                        document.getElementById('alerts').innerHTML = resp.message;

                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}