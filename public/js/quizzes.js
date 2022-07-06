
answerValid = false;

function addQuiz(){
    title_quiz = document.getElementById('title_quiz').value;
    promo_id = document.getElementById('promo_id').value;

    Swal.fire({
        title: '¿Deseas agregar la Trivia?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {

            loading();
            $.ajax({
                url: routeG+'admin/add/quiz',
                data: {
                    title_quiz,
                    promo_id,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    Swal.close();
                    if(resp.result == 'ok'){
                        document.getElementById('title_quiz').value = '';
                        alertSuccess();
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

function updateQuiz(){

    question= document.getElementById('title_questionAnswer');
    question_id = document.getElementById('questionIdDetails').value;


    if (question.value) {
        
        Swal.fire({
            title: '¿Deseas guardar los cambios?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {
                loading();
                $.ajax({
                    url: routeG+'admin/update/question',
                    data: {
                        question : question.value,
                        question_id,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        
                        if(resp.result == 'ok'){
                            question.classList.remove('is-invalid')
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
    } else {
        question.classList.add('is-invalid');
        document.getElementById('alertUpdateQuestion').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            "Favor de llenar todos los campos requeridos"+
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
    }
}

function addQuestion(){

    title = document.getElementById('title_question');
    if(title.value){
        document.getElementById('formAddQuestion').submit();
    }else{
        title.classList.add('is-invalid');
        document.getElementById('alertAddQuestion').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            "Favor de llenar todos los campos requeridos"+
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
    }

}

function findAnswers(question_id){

    $.ajax({
        url: routeG+'admin/find/answers',
        data: {
            question_id,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            
            if(resp.result == 'ok'){
                document.getElementById('bodyTableAnswers').innerHTML = resp.bodyT;
                document.getElementById('title_questionAnswer').value = resp.question;
                document.getElementById('questionIdDetails').value = resp.question_id;
            }else{
                console.log(resp)
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function addAnswer(){

    correct_answer = this.answerValid;
    answer_add = document.getElementById('answer_add');
    question_id = document.getElementById('questionIdDetails').value;

    if (answer_add.value) {
        
        Swal.fire({
            title: '¿Deseas agregar la respuesta?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {
                loading();
                $.ajax({
                    url: routeG+'admin/insert/answers',
                    data: {
                        answer : answer_add.value,
                        question_id,
                        correct_answer,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        
                        if(resp.result == 'ok'){
                            document.getElementById('bodyTableAnswers').innerHTML = resp.bodyT;
                            alertSuccess();
                            $('#addAnswerModal').modal('hide');
                            $('#findAnswers').modal('show');
                            document.getElementById('formAddAnswer').reset();
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
    } else {
        answer_add.classList.add('is-invalid');
        document.getElementById('alertAnswerAdd').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            "Favor de llenar todos los campos requeridos"+
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
    }

}

function updateAnswer(){
    validAnswer = this.answerValid;
    answer = document.getElementById('answer_update');
    answer_id = document.getElementById('answer_id_update').value;
    question_id = document.getElementById('question_id_update').value;

    if (answer.value) {
        
        Swal.fire({
            title: '¿Deseas guardar los cambios?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {
                loading();
                $.ajax({
                    url: routeG+'admin/update/answers',
                    data: {
                        answer_id,
                        answer : answer.value,
                        validAnswer,
                        question_id,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        
                        if(resp.result == 'ok'){
                            document.getElementById('bodyTableAnswers').innerHTML = resp.bodyT;
                            alertSuccess();
                            $('#updateAnswerModal').modal('hide');
                            $('#findAnswers').modal('show');
                            document.getElementById('formUpdateAnswer').reset();
                            resetValidAnswer();
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

    }else{
        answer.classList.add('is-invalid');
        document.getElementById('alertAnswerUpdate').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            "Favor de llenar todos los campos requeridos"+
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
    }
}

function deleteQuestion(){
    Swal.fire({
        title: '¿Deseas eliminar la pregunta?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteQuestionForm').submit();
        }
    });
}

function validAnswerF(){

    this.answerValid = !this.answerValid

}

function resetValidAnswer(){
    this.answerValid = false;
}

function findAnswer(answer_id){
    $.ajax({
        url: routeG+'admin/find/answer',
        data: {
            answer_id,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            
            if(resp.result == 'ok'){
                document.getElementById('answer_update').value = resp.answer['answer'];
                document.getElementById('answer_id_update').value = resp.answer['id'];
                document.getElementById('question_id_update').value = resp.answer['question_id'];

                if(resp.answer['correct_answer'] == 1){
                    document.getElementById("validAnswerUpdate").checked = true;
                    this.answerValid = true;
                }

            }else{
                console.log(resp)
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function showModalQDetails(){
    $('#findAnswers').modal('show');
    document.getElementById('formUpdateAnswer').reset();
}

function deleteAnswers(answerId){

    Swal.fire({
        title: '¿Deseas eliminar la respuesta?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Aceptar`,
        denyButtonText: `Cancelar`,
    }).then((result) => {
        if (result.isConfirmed) {
            loading();
            $.ajax({
                url: routeG+'admin/delete/answers',
                data: {
                    answerId,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    
                    if(resp.result == 'ok'){
                        document.getElementById('bodyTableAnswers').innerHTML = resp.bodyT;
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