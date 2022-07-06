<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ticketController;
use App\Http\Controllers\adminController;
use App\Http\Controllers\userController;
use App\Http\Controllers\shareAppController;
use App\Http\Controllers\periodsController;
use App\Http\Controllers\popUpController;
use App\Http\Controllers\filtersController;
use App\Http\Controllers\quizController;


// Rewards Controller
use App\Http\Controllers\rewardsProgram\winnersController;
use App\Http\Controllers\rewardsProgram\storeController;
use App\Http\Controllers\rewardsProgram\awardController;
use App\Http\Controllers\rewardsProgram\promotionController;
use App\Http\Controllers\rewardsProgram\bonusController;
use App\Http\Controllers\rewardsProgram\walletController;
use App\Http\Controllers\rewardsProgram\ordersController;
use App\Http\Controllers\rewardsProgram\presentationController;
use App\Http\Controllers\rewardsProgram\adminValidators;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [adminController::class, 'index'])->name('adminIndex');
// Route::get('/navLayout', [adminController::class, 'index'])->name('adminIndex');

Route::get("/navLayout", function(){
    return view("layouts.navbar");
 });
 

// Ticket Controller
Route::post('createTicket', [ticketController::class, 'createTicket'])->name('createTicket');
Route::get('admin/ticket/update/{id}/{view}', [ticketController::class, 'updateTicket'])->name('ticketUpdate');
Route::post('admin/ticket/update/conf/{id}/{status}/{comment}/{numTicket}', [ticketController::class, 'updateTicketConf'])->name('ticketConfig');
Route::post('admin/filtersTickets/search', [ticketController::class, 'searchTickets'])->name('searchTickets');
Route::post('admin/ticket/revalidate', [ticketController::class, 'revalidate'])->name('revalidate');


// App share Controller
Route::get('admin/AppShare/update/{id}', [shareAppController::class, 'updateAppShare'])->name('shareAppUpdate');
Route::get('admin/AppShare/update/conf/{id}/{status}/{comment}', [shareAppController::class, 'updateAppShareConf'])->name('appShareConfig');
Route::get('admin/createAppShare', [shareAppController::class, 'index'])->name('createApp');
Route::get('admin/createAppShare/Aprobe', [shareAppController::class, 'sharesAprobe'])->name('shareAprobe');
Route::get('admin/createAppShare/Cancel', [shareAppController::class, 'sharesCancel'])->name('shareCancel');
Route::post('admin/filterShare/search', [shareAppController::class, 'searchShare'])->name('searchShare');


// User controller
Route::get('referUser/{token}', [userController::class, 'referUser']);

//ResetPassword
Route::get('/user/resetPassword/{id}/{token}', [userController::class, 'resetPassword'])->name('user_resetPassword');
Route::get('/user/successPassword/', [userController::class, 'successPass'])->name('success-Password');
Route::post('/user/changePassword/', [userController::class, 'changePassword'])->name('change-Password');

//  Login
Route::post('/admin/logout', [adminController::class, 'logout'])->name('logoutAdmin');
Route::put('/admin/login', [adminController::class, 'login'])->name('loginAdmin');

Route::get('admin', [adminController::class, 'index'])->name('admin');

Route::group(['middleware' => ['userAuthenticate']], function() {
    

    // admin Controller
    Route::get('admin/ticket/aprobados', [adminController::class, 'ticketAprove'])->name('aprobados');
    Route::get('admin/ticket/cancelados', [adminController::class, 'ticketCancel'])->name('cancelados');
    Route::post('admin/getCortes/data', [adminController::class, 'getCortes'])->name('infoCortes');


    //Promociones
    Route::get('/admin/promociones', [promotionController::class, 'promociones'])->name('adminPromociones');
    Route::get('/admin/promociones/details/{id}', [promotionController::class, 'promoDetails'])->name('promoDetails');
    Route::post('/admin/get/promos/selected/awards', [promotionController::class, 'selectedAwards'])->name('selectedAwards');
    Route::post('/admin/update/promo', [promotionController::class, 'updatePromo'])->name('updatePromo');
    Route::post('/admin/down/promo', [promotionController::class, 'downPromo'])->name('downPromo');
    Route::post('/admin/add/promo', [promotionController::class, 'addPromo'])->name('addPromo');
    Route::post('/admin/down/end/promotion', [promotionController::class, 'endPromo'])->name('endPromo');
    
    
    // Award
    Route::get('/admin/awards', [awardController::class, 'awardsAdmin'])->name('awardsAdmin');
    Route::get('/admin/awards/redeemed', [awardController::class, 'awardsRedeemedAdmin'])->name('awardsRedeemedAdmin');
    Route::get('/admin/get/award/details/{id}', [awardController::class, 'awardsAdminDetails'])->name('awardsAdminDetails');
    Route::post('/admin/add/award', [awardController::class, 'addAward'])->name('addAward');
    Route::post('/admin/update/award', [awardController::class, 'updateAward'])->name('updateAward');
    Route::post('/admin/get/positions/awards', [awardController::class, 'getPositionsAward'])->name('getPositionsAward');
    Route::post('/admin/get/positions/awards/updates', [awardController::class, 'getPositionsAwardUpdates'])->name('getPositionsAwardUpdates');
    Route::post('/admin/delete/award', [awardController::class, 'deleteAward'])->name('deleteAward');
    
    // Store
    Route::get('/admin/store', [storeController::class, 'storeAdmin'])->name('storeAdmin');
    Route::get('/admin/get/store/details/{id}', [storeController::class, 'storeAdminDetails'])->name('storeAdminDetails');
    Route::post('/admin/add/product/store', [storeController::class, 'storeAddProductAdmin'])->name('storeAddProductAdmin');
    Route::post('/admin/update/store/product', [storeController::class, 'storeUpdateProduct'])->name('storeUpdateProduct');
    Route::post('/admin/delete/store/product', [storeController::class, 'storeDeleteProduct'])->name('storeDeleteProduct');
    
    
    
    // Pop Ups
    Route::get('/admin/get/popUps', [popUpController::class, 'popUpAdmin'])->name('popUpAdmin');
    Route::post('/admin/get/information/popUp', [popUpController::class, 'getInfoPopUpAdmin'])->name('getInfoPopUpAdmin');
    Route::post('/admin/add/popUp', [popUpController::class, 'addPopUpAdmin'])->name('addPopUpAdmin');
    Route::post('/admin/update/popUp', [popUpController::class, 'updatePopUpAdmin'])->name('updatePopUpAdmin');
    Route::post('/admin/update/status/popUp', [popUpController::class, 'updateStatusPopUp'])->name('updateStatusPopUp');
    
    //  Orders
    Route::get('/admin/get/orders', [ordersController::class, 'ordersAdmin'])->name('ordersAdmin');
    Route::get('/admin/get/order/details/{id}', [ordersController::class, 'ordersDetailsAdmin'])->name('ordersDetailsAdmin');
    Route::post('/admin/order/update', [ordersController::class, 'ordersUpdateStatus'])->name('ordersUpdateStatus');
    Route::post('/admin/order/canceled', [ordersController::class, 'cancelOrder'])->name('cancelOrder');
    
    
    
    //Periods
    Route::post('/admin/new/period', [periodsController::class, 'addPeriod'])->name('addPeriod');
    
    //Bonus
    Route::post('/admin/new/bonus', [bonusController::class, 'addBonus'])->name('addBonus');
    Route::post('/admin/get/bonus/{id}', [bonusController::class, 'getBonus'])->name('getBonus');
    Route::post('/admin/update/bonus', [bonusController::class, 'updateBonus'])->name('updateBonus');
    Route::post('/admin/down/bonus/{id}', [bonusController::class, 'downBonus'])->name('downBonus');
    
    //Winners
    Route::post('/admin/searchUsers/winners', [winnersController::class, 'searchUsers'])->name('searchUsers');
    Route::post('/admin/post/winners/score', [winnersController::class, 'insertWinnersAdmin'])->name('insertWinnersAdmin');
    Route::post('/admin/get/awardPosition/winner', [winnersController::class, 'getAwardWinner'])->name('getAwardWinner');
    Route::post('/admin/delete/winner', [winnersController::class, 'deleteWinner'])->name('deleteWinner');
    
    // Wallets
    Route::get('/admin/wallets', [walletController::class, 'walletsAdmin'])->name('walletsAdmin');
    Route::post('/admin/get/user/wallet/{user_id}', [walletController::class, 'walletUserAdmin'])->name('walletUserAdmin');
    
    //  Presentations
    Route::get('/admin/presentations', [presentationController::class, 'adminPresentations'])->name('adminPresentations');
    Route::get('/admin/get/presentation/{id}', [presentationController::class, 'getDetailsPresentation'])->name('getDetailsPresentation');
    Route::post('/admin/update/presentation', [presentationController::class, 'presentationUpdate'])->name('presentationUpdate');
    Route::post('/admin/add/presentation', [presentationController::class, 'adminAddPresentation'])->name('adminAddPresentation');
    Route::post('/admin/down/presentation', [presentationController::class, 'downPresentation'])->name('downPresentation');
    
    //  Non ARCA
    Route::get('/admin/presentationsNonArca', [presentationController::class, 'adminPresentationsNonArca'])->name('adminPresentationsNonArca');
    Route::get('/admin/get/presentationNonArca/{id}', [presentationController::class, 'getDetailsPresentationNonArca'])->name('getDetailsPresentationNonArca');
    Route::post('/admin/update/presentationNonArca', [presentationController::class, 'presentationUpdateNonArca'])->name('presentationUpdateNonArca');
    Route::post('/admin/add/presentationNonArca', [presentationController::class, 'addPresentationNonArca'])->name('addPresentationNonArca');
    Route::post('/admin/get/types/presentationNonArca', [presentationController::class, 'typesPresentationNonArca'])->name('typesPresentationNonArca');
    Route::post('/admin/down/presentationNonArca', [presentationController::class, 'downPresentationNonArca'])->name('downPresentationNonArca');
    
    //  Quizzes
    Route::get('/admin/preguntas', [quizController::class, 'index'])->name('adminQuizzes');
    Route::get('/admin/preguntas/detalles/{id}', [quizController::class, 'details'])->name('quizDetails');
    Route::post('/admin/add/quiz', [quizController::class, 'insert'])->name('addQuiz');
    Route::post('/admin/find/answers', [quizController::class, 'findAnswer'])->name('findAnswer');
    Route::post('/admin/add/question', [quizController::class, 'insertQuestion'])->name('addQuestion');
    Route::post('/admin/update/question', [quizController::class, 'updateQuestion'])->name('updateQuestion');
    Route::put('/admin/delete/question', [quizController::class, 'deleteQuestion'])->name('deleteQuestion');
    Route::post('/admin/insert/answers', [quizController::class, 'insertAnswer'])->name('addAnswer');
    Route::post('/admin/delete/answers', [quizController::class, 'deleteAnswer'])->name('deleteAnswer');
    Route::post('/admin/update/answers', [quizController::class, 'updateAnswer'])->name('updateAnswer');
    Route::post('/admin/find/answer', [quizController::class, 'findQuestionAnswer'])->name('findQuestionAnswer');
    
    
    //  Users validators
    Route::get('/admin/users/validators', [adminValidators::class, 'getUserValidators'])->name('adminValidators');
    Route::get('/admin/get/validatorDetails/{id}', [adminValidators::class, 'getUserValidatorDetails'])->name('getUserValidatorDetails');
    Route::post('/admin/update/userValidator', [adminValidators::class, 'updateValidators'])->name('updateValidators');
    Route::post('/admin/down/validator/user', [adminValidators::class, 'downValidators'])->name('downValidators');
    Route::post('/admin/added/validator/user', [adminValidators::class, 'addUserValidators'])->name('addUserValidators');

});


//  Filters
Route::post('admin/filters/promotion', [filtersController::class, 'searchPromotion'])->name('searchPromotion');
Route::post('admin/filters/awards', [filtersController::class, 'searchAwards'])->name('searchAwards');
Route::post('admin/filters/presentation', [filtersController::class, 'searchPresentations'])->name('searchPresentations');
Route::post('admin/filters/validatorsUsers', [filtersController::class, 'searchValidators'])->name('searchValidators');




//PDF
Route::get('/tickets/pdf', [ticketController::class, 'createPDF'])->name('createPDF');




