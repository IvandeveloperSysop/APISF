<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\shareAppController;
use App\Http\Controllers\ticketController;
use App\Http\Controllers\periodsController;
use App\Http\Controllers\miniGameController;
use App\Http\Controllers\emailController;
use App\Http\Controllers\avisosController;
use App\Http\Controllers\statesController;
use App\Http\Controllers\campaignController;
use App\Http\Controllers\imageGlobalController;
use App\Http\Controllers\popUpController;
use App\Http\Controllers\ticketVirtualController;
use App\Http\Controllers\quizController;


// Rewards Controller
use App\Http\Controllers\rewardsProgram\winnersController;
use App\Http\Controllers\rewardsProgram\awardController;
use App\Http\Controllers\rewardsProgram\presentationController;
use App\Http\Controllers\rewardsProgram\storeController;
use App\Http\Controllers\rewardsProgram\walletController;
use App\Http\Controllers\rewardsProgram\ordersController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//userController
Route::get('usersApi', [userController::class, 'index']);
Route::post('user/profile', [userController::class, 'profile']);
Route::post('login', [userController::class, 'login']);
Route::post('user/info/score', [userController::class, 'getUserInfoScore']);

Route::post('register', [userController::class, 'register']);
Route::get('apiNew', [userController::class, 'apiNew']);
Route::post('updateProfile', [userController::class, 'updateProfile']);
Route::post('referUser', [userController::class, 'referUser']);
Route::post('insertRefer', [userController::class, 'insertRefer']);
Route::post('refer/friends', [userController::class, 'referUserFriends']);
Route::post('user/validFriend', [userController::class, 'validFriend']);


//  social CRUD facebook and google
Route::post('registerSocial', [userController::class, 'registerSocial']);
Route::post('loginSocial', [userController::class, 'loginSocial']);
Route::post('validExistsUserSocial', [userController::class, 'validExistsUserSocial']);

//  Top list
Route::post('user/topList', [userController::class, 'topList']);

//shareApp controller
Route::post('createAppShare', [shareAppController::class, 'createAppShare']);
Route::post('appShare/update', [shareAppController::class, 'updateAppSharePwa']);
Route::post('validExistShare', [shareAppController::class, 'validExistShare']);

//Ticket controller
Route::post('createTicket', [ticketController::class, 'createTicket']);
Route::post('get/tickets', [ticketController::class, 'tickets']);

//  Vouchers
Route::post('get/vouchers', [ticketVirtualController::class, 'getVouchers']);

//periodsController
Route::get('periods/get/{date}/{promo_id}/{pwa}', [periodsController::class, 'index']);

//MiniGame
Route::post('miniGame/get', [miniGameController::class, 'index']);
Route::post('miniGame/notGame', [miniGameController::class, 'notGame']);
Route::post('miniGame/getpoints', [miniGameController::class, 'getPoints']);

//SendEmail change password
Route::post('user/resetPassword', [emailController::class, 'changePasswordUser']);
Route::get('resetPassword/{id}/{token}', [userController::class, 'resetPassword'])->name('user-resetPassword');
Route::get('user/successPassword/', [userController::class, 'successPass'])->name('successPassword');
Route::post('user/changePassword/', [userController::class, 'changePassword'])->name('changePassword');

//Valid version user
Route::post('user/validVersion', [userController::class, 'validVersion']);

// Traer todos los periodos
Route::post('periods/get/all', [periodsController::class, 'getAllPeriods']);

// Traer todos winners por periodo
Route::post('winners/period/{id}', [winnersController::class, 'getWinnersPeriod']);

//  Avisos
Route::post('avisos/get/all', [avisosController::class, 'getAvisos']);
Route::post('avisos/delete/user', [avisosController::class, 'deleteAvisoUser']);

// Presentation
Route::post('get/presentations', [presentationController::class, 'getPresentations']);

// States
Route::post('get/states', [statesController::class, 'getStates']);

// campaign
Route::post('get/campaign', [campaignController::class, 'getCampaign']);

// PopUps
Route::post('popUps/get/information', [popUpController::class, 'getPopUp']);
Route::post('popUps/insert/popUpUser', [popUpController::class, 'insertPopUpUser']);
Route::post('popUps/modalHome/nonShow', [popUpController::class, 'disablePopUp']);


//Products store
Route::get('get/productsStore/{promo_id}', [storeController::class, 'getProductStorePwa'])->name('pwa-getProducts');
Route::post('insert/buy/product', [storeController::class, 'buyProduct']);
Route::get('get/details/product/{product_id}/{token_user}', [storeController::class, 'getDetailsProduct'])->name('pwa-getDetailsProduct');

//Awards
Route::get('/get/promotion/awards/{promo_id}/{window}/{pwa}', [awardController::class, 'writeAwardTable'])->name('awardsPromotion');

// wallets
Route::get('get/balance/user/{token}', [walletController::class, 'getUserBalance'])->name('getUserBalance');
Route::get('get/address/user/{user_id}', [userController::class, 'getAddressUser']);
Route::post('get/history/balance', [walletController::class, 'getUserHistoryBalance'])->name('getUserHistoryBalance');

//  Orders
Route::post('get/history/orders', [ordersController::class, 'ordersHistory'])->name('ordersHistory');

// Global
Route::post('get/imageGlobal', [imageGlobalController::class, 'getImageG']);
Route::post('get/imageGlobal/campaign', [imageGlobalController::class, 'getImageGCampaign']);


//  Quiz
Route::post('start/quiz', [quizController::class, 'startQuiz']);
Route::post('set/answer/quiz/user', [quizController::class, 'insertUserAnswer']);
Route::post('finish/quiz', [quizController::class, 'finishQuiz']);






