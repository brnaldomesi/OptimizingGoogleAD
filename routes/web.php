<?php

use App\Events\AccountSyncedSuccessfully;

Auth::routes();

Route::get('/', function () {
    return view('public.index');
});

Route::get('/user/api/update_is_synced', function(){
    AccountSyncedSuccessfully::dispatch();
    return "update is synced";
});

Route::get('/user/notifications', 'User\NotificationController@all');

Route::get('/user/notifications/unread', 'User\NotificationController@unread');

Route::post('/user/notifications', 'User\NotificationController@update');

Route::put('/user/notifications', 'User\NotificationController@markAllAsRead');


Route::get('/temp', 'TempController@index');

Route::get('/terms', function () {
    return view('terms');
});

Route::group(['namespace' => 'User', 'prefix' => 'user', 'middleware' => ['web','auth']], function () {

		Route::get('/dashboard', 'DashboardController@index');
		Route::get('/first-run/accounts-downloaded', 'FirstRunController@accountsDownloaded');

});

Route::get('/handle-authentication', 'AuthenticateAdWordsController');
Route::get('/registration_error', function(){
    return view('user.registration-error');
});

Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::group(['middleware' => ['web','auth', 'admin']], function () {
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('python_log', 'api\PythonLogController@index');
});

Route::group(['middleware' => ['web','auth', 'can:view,user']], function () {
  Route::get('/api/user/{user}/accounts', 'api\AccountController@accounts');
  Route::get('/api/user/{user}/number_of_synced_accounts', 'api\AccountController@numberOfSyncedAccounts');
  Route::get('/api/user/{user}/ssoToken', 'api\AccountController@getSsoToken');
});


Route::group(['middleware' => 'can:view,account'], function () {
  
  // Plans
  Route::get('/api/plans', 'api\PlanController@index');
  Route::post('/api/plan', 'api\PlanController@getPlan');
  
  // Get/Set Budget
  Route::get('/api/account/{account}/budget', 'api\AccountBudgetController@show');
  Route::patch('/api/account/{account}/budget', 'api\AccountBudgetController@update');

  // Get/Set KPI
  Route::get('/api/account/{account}/kpi', 'api\AccountKpiController@show');
  Route::patch('/api/account/{account}/kpi', 'api\AccountKpiController@update');

  // Get/Set Budget Commander switches
  Route::get('/api/account/{account}/actions', 'api\BudgetCommanderActionsController@show');
  Route::post('/api/account/{account}/actions', 'api\BudgetCommanderActionsController@update');


  Route::get('/api/account/{account}/bcgraph', 'api\AccountBudgetCommanderGraph@show');
  Route::get('/api/account/{account}/budget_commander_campaigns', 'api\AccountBudgetGroupCampaigns@show');
  Route::get('/api/account/{account}/budget_groups', 'api\AccountBudgetGroups@show');
  Route::post('/api/account/{account}/budget_groups', 'api\AccountBudgetGroups@add');
  Route::delete('/api/account/{account}/budget_groups', 'api\AccountBudgetGroups@delete');

  // Get Budget Commander Graph Data
  Route::post('/api/account/{account}/api_mutation', 'api\ApiMutationController@update');

  // TODO
  // Route::get('/api/account/user/{userid}/', 'api\AccountUserController@show');



  Route::post('/api/account/{account}/toggle_is_synced', 'api\AccountController@toggleIsSynced');
  Route::get('/api/account/{account}/toggle_is_synced', 'api\AccountController@toggleIsSynced');
  Route::get('/api/account/{account}/get_sync_info', 'api\AccountController@getSyncInfo');
  Route::get('/api/account/{account}/currency', 'api\AccountController@currency');
  Route::get('/api/account/{account}/feed/pause_feed_item', 'api\FeedController@pauseFeedItem');
 
  //AD TEST
  Route::get('/api/account/{account}/adtest/adgroup', 'api\AdTestController@adgroup');
  Route::get('/api/account/{account}/adgroup/{adgroup}/keywords', 'api\AdTestController@keywords');
  Route::get('/api/account/{account}/adtest/account/headlines', 'api\AdTestController@accountHeadlines');
  Route::get('/api/account/{account}/adtest/campaign/{campaign}/headlines', 'api\AdTestController@campaignHeadlines');
  Route::get('/api/account/{account}/adtest/adgroup/{adgroup}/headlines', 'api\AdTestController@adgroupHeadlines');
  Route::get('/api/account/{account}/adtest/account/descriptions', 'api\AdTestController@accountDescriptions');
  Route::get('/api/account/{account}/adtest/campaign/{campaign}/descriptions', 'api\AdTestController@campaignDescriptions');
  Route::get('/api/account/{account}/adtest/adgroup/{adgroup}/descriptions', 'api\AdTestController@adgroupDescriptions');
  Route::get('/api/account/{account}/adtest/account/n_grams', 'api\AdTestController@accountNGrams');
  Route::get('/api/account/{account}/adtest/campaign/{campaign}/n_grams', 'api\AdTestController@campaignNGrams');
  Route::get('/api/account/{account}/adtest/adgroup/{adgroup}/n_grams', 'api\AdTestController@adgroupNGrams');


  //FEED
  Route::get('/api/account/{account}/feed/new', 'api\FeedController@new');
  Route::get('/api/account/{account}/feed/read', 'api\FeedController@read');
  Route::get('/api/account/{account}/feed/archived', 'api\FeedController@archived');
  Route::get('/api/account/{account}/feed/snoozed', 'api\FeedController@snoozed');
  Route::post('/api/account/{account}/archive_feed_item', 'api\FeedController@archiveFeedItem');
  Route::get('/api/account/{account}/archive_feed_item', 'api\FeedController@archiveFeedItem');
  Route::post('/api/account/{account}/snooze_feed_item', 'api\FeedController@snoozeFeedItem');

});

Route::group(['namespace' => 'User','middleware' => ['auth','web']], function () {

	Route::get('/{any}', 'SinglePageController@index')->where('any', '.*');
	Route::get('/{any}/{any}', 'SinglePageController@index')->where('any', '.*');
	Route::post('/user/connect', 'ConnectAdWordsController');

});

Route::group(['namespace' => 'User', 'prefix' => 'user', 'middleware' => ['auth','web','can:view,account']], function () {
    // Route::get('/accounts', 'AccountController@index');
    // Route::get('/accounts/{account}', 'AccountController@show');
    // Route::put('/accounts/{account}', 'AccountController@update');


    Route::get('/adgroups/{scope}/{id}/{tab?}', 'AdgroupController@index');

    Route::get('/adverts/{adgroup_id}/{showCreator?}', 'AdvertController@index');

    Route::post('/adverts', 'CreateAdvertController');

    Route::get('/campaigns/{accountId}', 'CampaignController@index');

    Route::post('/enable-adverts', 'EnableAdvertsController');

    Route::get('/first-run/get-account', 'FirstRunController@getAccountFromGoogle');

    Route::get('/first-run/first-report-processed', 'FirstRunController@firstReportProcessed');

    Route::get('ngramperformance/{account}', 'AdNGramPerformanceController');

    Route::get('searchqueryperformance/{account}', 'SearchQueryPerformanceController');

    Route::get('budget-commander/{account}', 'BudgetCommanderController@index');

    Route::post('/pause-adverts', 'PauseAdvertsController');

    Route::get('/subscription', 'SubscriptionsController@index');
    Route::get('/subscription/payment', 'SubscriptionsController@payment');
    Route::post('/subscription/payment', 'SubscriptionsController@payment');
    Route::post('/subscription/add', 'SubscriptionsController@add_subscription');

});


