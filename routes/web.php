<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response as FacadesResponse;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// tool get @bodyParam for API doc for table
//$router->get('api_doc/get_body_params/{table_name}', 'ApiDocController@getBodyParams');

// API user
$router->post('user/login', 'UserController@login');
$router->put('user/profile', 'UserController@profile');
$router->put('user/profile_change_password', 'UserController@profileChangePassword');
$router->put('user/profile_update', 'UserController@profileUpdate');
$router->put('user/update_role', 'UserController@updateRole');
$router->put('user/request_reset_password', 'UserController@requestResetPassword');
$router->put('user/verify_checksum_reset_password', 'UserController@verifyChecksumResetPassword');
$router->put('user/verify_otp_reset_password', 'UserController@verifyOtpResetPassword');
$router->put('user/reset_password', 'UserController@resetPassword');
$router->put('user/change_password', 'UserController@changePassword');
$router->put('user/detail', 'UserController@detail');
$router->put('user/list', 'UserController@list');
$router->put('user/create', 'UserController@create');
$router->put('user/update', 'UserController@update');
$router->put('user/lock', 'UserController@lock');
$router->put('user/active', 'UserController@active');
$router->put('user/update_role', 'UserController@updateRole');
$router->put('user/get_ref_list', 'UserController@getRefList');
$router->put('user/get_user_group_list', 'UserController@getUserGroupList');
$router->put('user/get_status', 'UserController@getStatus');

// API UserGroup
$router->put('user_group/create', 'UserGroupController@create');
$router->put('user_group/update', 'UserGroupController@update');
$router->put('user_group/get_role_list', 'UserGroupController@getRoleList');

// Api InvestorBankAccount
$router->put('investor_bank_account/list', 'InvestorBankAccountController@list');
$router->put('investor_bank_account/create', 'InvestorBankAccountController@create');
$router->put('investor_bank_account/set_default', 'InvestorBankAccountController@setDefault');
$router->put('investor_bank_account/get_status', 'InvestorBankAccountController@getStatus');
$router->put('investor_bank_account/lock', 'InvestorBankAccountController@lock');
$router->put('investor_bank_account/active', 'InvestorBankAccountController@active');
$router->put('investor_bank_account/detail', 'InvestorBankAccountController@detail');
$router->put('investor_bank_account/get_list', 'InvestorBankAccountController@getList');

// Api Investor
$router->put('investor/list', 'InvestorController@list');
$router->put('investor/create', 'InvestorController@create');
$router->put('investor/update', 'InvestorController@update');
$router->put('investor/get_status', 'InvestorController@getStatus');
$router->put('investor/get_vsd_status', 'InvestorController@getVsdStatus');
$router->put('investor/get_genders', 'InvestorController@getGenders');
$router->put('investor/get_id_types', 'InvestorController@getIdTypes');
$router->put('investor/get_scale_types', 'InvestorController@getScaleTypes');
$router->put('investor/get_invest_types', 'InvestorController@getInvestTypes');
$router->put('investor/get_zone_types', 'InvestorController@getZoneTypes');
$router->put('investor/get_trading_account_types', 'InvestorController@getTradingAccountTypes');
$router->put('investor/detail', 'InvestorController@detail');
$router->put('investor/get_list', 'InvestorController@getList');
$router->put('investor/get_list_trading_account_number', 'InvestorController@getListTradingAccountNumber');
$router->put('investor/get_detail_and_sip_products', 'InvestorController@getDetailAndSipProducts');
$router->put('investor/closed', 'InvestorController@closed');
$router->put('investor/reopen', 'InvestorController@reopen');
$router->put('investor/cancel', 'InvestorController@cancel');
$router->put('investor/active', 'InvestorController@active');
$router->put('investor/reject', 'InvestorController@reject');
$router->put('investor/sendMail', 'InvestorController@sendMail');
$router->post('investor/import_file_excel', 'InvestorController@importFileExcel');
$router->put('investor/export_file_excel', 'InvestorController@exportFileExcel');
$router->put('investor/export_vsd', 'InvestorController@exportVSD');
$router->put('investor/import', 'InvestorController@import');

// Api FundDistributor
$router->put('fund_distributor/list', 'FundDistributorController@list');
$router->put('fund_distributor/create', 'FundDistributorController@create');
$router->put('fund_distributor/update', 'FundDistributorController@update');
$router->put('fund_distributor/get_status', 'FundDistributorController@getStatus');
$router->put('fund_distributor/lock', 'FundDistributorController@lock');
$router->put('fund_distributor/active', 'FundDistributorController@active');
$router->put('fund_distributor/detail', 'FundDistributorController@detail');
$router->put('fund_distributor/get_list', 'FundDistributorController@getList');
$router->put('fund_distributor/get_list_code', 'FundDistributorController@getListCode');

// Api FundDistributorProduct
$router->put('fund_distributor_product/list', 'FundDistributorProductController@list');
$router->put('fund_distributor_product/create', 'FundDistributorProductController@create');
$router->put('fund_distributor_product/update', 'FundDistributorProductController@update');
$router->put('fund_distributor_product/lock', 'FundDistributorProductController@lock');
$router->put('fund_distributor_product/active', 'FundDistributorProductController@active');
$router->put('fund_distributor_product/detail', 'FundDistributorProductController@detail');
$router->put('fund_distributor_product/get_status', 'FundDistributorProductController@getStatus');

// Api FundDistributorLocation
$router->put('fund_distributor_location/list', 'FundDistributorLocationController@list');
$router->put('fund_distributor_location/create', 'FundDistributorLocationController@create');
$router->put('fund_distributor_location/update', 'FundDistributorLocationController@update');
$router->put('fund_distributor_location/lock', 'FundDistributorLocationController@lock');
$router->put('fund_distributor_location/active', 'FundDistributorLocationController@active');
$router->put('fund_distributor_location/detail', 'FundDistributorLocationController@detail');
$router->put('fund_distributor_location/get_status', 'FundDistributorLocationController@getStatus');

// Api FundDistributorStaff
$router->put('fund_distributor_staff/list', 'FundDistributorStaffController@list');
$router->put('fund_distributor_staff/create', 'FundDistributorStaffController@create');
$router->put('fund_distributor_staff/update', 'FundDistributorStaffController@update');
$router->put('fund_distributor_staff/lock', 'FundDistributorStaffController@lock');
$router->put('fund_distributor_staff/active', 'FundDistributorStaffController@active');
$router->put('fund_distributor_staff/detail', 'FundDistributorStaffController@detail');
$router->put('fund_distributor_staff/get_status', 'FundDistributorStaffController@getStatus');
$router->put('fund_distributor_staff/get_list', 'FundDistributorStaffController@getList');


//Api FundCertificate
$router->put('fund_certificate/list','FundCertificateController@list');
$router->put('fund_certificate/get_list','FundCertificateController@getlist');
$router->put('fund_certificate/add', 'FundCertificateController@add');
$router->put('fund_certificate/update', 'FundCertificateController@update');
$router->put('fund_certificate/active', 'FundCertificateController@active');
$router->put('fund_certificate/lock', 'FundCertificateController@lock');
$router->put('fund_certificate/get_fund_product', 'FundCertificateController@getFundProduct');
$router->put('fund_certificate/get_list_by_fund_distributor_product', 'FundCertificateController@getListByFundDistributorProduct');


// Api FundCompany
$router->put('fund_company/get_list', 'FundCompanyController@getList');


// Api SupervisingBank
$router->put('supervising_bank/get_list', 'SupervisingBankController@getList');

// Api Bank
$router->put('bank/get_list', 'BankController@getList');
$router->put('bank/list', 'BankController@list');
$router->put('bank/detail','BankController@detail');
$router->put('bank/create', 'BankController@create');
$router->put('bank/update', 'BankController@update');
$router->put('bank/active', 'BankController@active');
$router->put('bank/lock', 'BankController@lock');
$router->put('bank/get_status', 'BankController@getStatus');

// Api Country
$router->put('country/get_list', 'CountryController@getList');

// Api FundProduct
$router->put('fund_product/get_list', 'FundProductController@getList');
//$router->put('fund_product/get_detail_product', 'FundProductController@getDetailProduct');

// Api Commission
$router->put('account_commission/list', 'AccountCommissionController@list');
$router->put('account_commission/lock', 'AccountCommissionController@lock');
$router->put('account_commission/active', 'AccountCommissionController@active');
$router->put('account_commission/increase_balance', 'AccountCommissionController@increaseBalance');

// Api Test
$router->get('test', 'TestController@index');
$router->post('test', 'TestController@store');
$router->get('test/{id}', 'TestController@show');
$router->put('test/{id}', 'TestController@update');

// API FundCertificate
$router->get('fund_certificate/list', 'FundCertificateController@list');
$router->put('fund_certificate/add', 'FundCertificateController@add');
$router->put('fund_certificate/update', 'FundCertificateController@update');
$router->put('fund_certificate/active', 'FundCertificateController@active');
$router->put('fund_certificate/lock', 'FundCertificateController@lock');
$router->put('fund_certificate/detail', 'FundCertificateController@detail');

//Api FundProductType
$router->put('fund_product_type/list', 'FundProductTypeController@list');
$router->put('fund_product_type/get_list', 'FundProductTypeController@getlist');
$router->put('fund_product_type/add', 'FundProductTypeController@add');
$router->put('fund_product_type/detail', 'FundProductTypeController@detail');
$router->put('fund_product_type/update', 'FundProductTypeController@update');
$router->put('fund_product_type/lock', 'FundProductTypeController@lock');
$router->put('fund_product_type/active', 'FundProductTypeController@active');
$router->put('fund_product_type/get_status', 'FundProductTypeController@getStatus');


//Api FundProduct
$router->put('fund_product/list', 'FundProductController@list');
$router->put('fund_product/get_list', 'FundProductController@getlist');
$router->put('fund_product/add', 'FundProductController@add');
$router->put('fund_product/detail', 'FundProductController@detail');
$router->put('fund_product/update', 'FundProductController@update');
$router->put('fund_product/lock', 'FundProductController@lock');
$router->put('fund_product/active', 'FundProductController@active');
$router->put('fund_product/get_status', 'FundProductController@getStatus');

//Api Country
$router->put('country/list', 'CountryController@list');
$router->put('country/add', 'CountryController@add');
$router->put('country/detail', 'CountryController@detail');
$router->put('country/update', 'CountryController@update');
$router->put('country/lock', 'CountryController@lock');
$router->put('country/active', 'CountryController@active');
$router->put('country/get_status', 'CountryController@getStatus');

//Api SettingCommission
$router->put('setting_commission/list', 'SettingCommissionController@list');
$router->put('setting_commission/create', 'SettingCommissionController@create');
$router->put('setting_commission/lock', 'SettingCommissionController@lock');
$router->put('setting_commission/detail', 'SettingCommissionController@detail');

$router->put('setting_commission/getListFundDistributor', 'SettingCommissionController@getListFundDistributor');

//Api TradingOrderFeeBuy
$router->put('trading_order_fee_buy/list', 'TradingOrderFeeBuyController@list');
$router->put('trading_order_fee_buy/create', 'TradingOrderFeeBuyController@create');
$router->put('trading_order_fee_buy/lock', 'TradingOrderFeeBuyController@lock');

//Api TradingFrequency
$router->put('trading-frequency/list', 'TradingFrequencyController@list');
$router->put('trading-frequency/create', 'TradingFrequencyController@create');
$router->put('trading-frequency/lock', 'TradingFrequencyController@lock');
$router->put('trading-frequency/active', 'TradingFrequencyController@active');
$router->put('trading-frequency/get_status', 'TradingFrequencyController@getStatus');

//Api TradingOrderFeeSell
$router->put('trading_order_fee_sell/list', 'TradingOrderFeeSellController@list');
$router->put('trading_order_fee_sell/create', 'TradingOrderFeeSellController@create');
$router->put('trading_order_fee_sell/lock', 'TradingOrderFeeSellController@lock');
$router->put('trading_order_fee_sell/get_status', 'TradingOrderFeeSellController@getStatus');

// Api Cashin
$router->put('cashin/list', 'CashinController@list');
$router->post('cashin/import_statement', 'CashinController@importStatement');
// $router->get('cashin/test', 'CashinController@test');
$router->put('cashin/get_status', 'CashinController@getStatus');
$router->put('cashin/detail', 'CashinController@detail');
$router->put('cashin/update_investor', 'CashinController@updateInvestor');

// Api Statement
$router->put('statement/detail', 'StatementController@detail');
$router->put('statement/line', 'StatementController@line');
$router->put('statement/get_line_status', 'StatementController@getLineStatus');

// Api File Import
$router->put('file_import/detail', 'FileImportController@detail');
$router->put('file_import/line', 'FileImportController@line');
$router->put('file_import/get_line_status', 'FileImportController@getLineStatus');
$router->get('file_import/test', 'FileImportController@test');

// Commission
$router->put('commission/list', 'CommissionController@list');

//Api ReferralBankTransaction
$router->put('referral-bank/list', 'ReferralBankController@list');
$router->put('referral-bank/create', 'ReferralBankController@create');
$router->put('referral-bank/update', 'ReferralBankController@update');
$router->put('referral-bank/lock', 'ReferralBankController@lock');
$router->put('referral-bank/active', 'ReferralBankController@active');
$router->put('referral-bank/get_status', 'ReferralBankController@getStatus');
$router->put('referral-bank/detail','ReferralBankController@detail');

//Api InvestorFundProduct
$router->put('investor_fund_product/list', 'InvestorFundProductController@list');
$router->put('investor_fund_product/create', 'InvestorFundProductController@create');
$router->put('investor_fund_product/lock', 'InvestorFundProductController@lock');
$router->put('investor_fund_product/active', 'InvestorFundProductController@active');
$router->put('investor_fund_product/get_status', 'InvestorFundProductController@getStatus');
$router->put('investor_fund_product/get_fund_product', 'InvestorFundProductController@getFundProduct');

//Api MailTemplate
$router->put('mail_template/list', 'MailTemplateController@list');
$router->put('mail_template/detail', 'MailTemplateController@detail');
$router->put('mail_template/get_list', 'MailTemplateController@getList');

//Api MailTemplateLocale
$router->put('mail_template_locale/list', 'MailTemplateLocaleController@list');
$router->put('mail_template_locale/create', 'MailTemplateLocaleController@create');
$router->put('mail_template_locale/detail', 'MailTemplateLocaleController@detail');
$router->put('mail_template_locale/update', 'MailTemplateLocaleController@update');

// Export
$router->get('/export_buy_confirmation', 'PDFController@exportBuyConfirmation');
$router->get('/export_sale_confirmation', 'PDFController@exportSaleConfirmation');
$router->get('/pdf', 'PDFController@show');

//Api TradingSession
$router->put('trading-session/list','TradingSessionController@list');
$router->put('trading-session/cancel','TradingSessionController@cancel');


// Api TransactionFund
$router->put('transaction-fund/list','TransactionFundController@list');
$router->post('transaction_fund/import_file_excel', 'TransactionFundController@importFileExcel');
$router->put('transaction_fund/import', 'TransactionFundController@import');
$router->get('transaction_fund/test', 'TransactionFundController@test');

//Api TradingOrder
$router->put('trading_order/list', 'TradingOrderController@list');
$router->put('trading_order/create', 'TradingOrderController@create');

// TEST
$router->get('test/test', 'TestController@test');
$router->get('test/test_job', 'TestController@testJob');

//Api InvestorSip
$router->put('investor_sip/list', 'InvestorSipController@list');
$router->put('investor_sip/create', 'InvestorSipController@create');
$router->put('investor_sip/get_sip_type', 'InvestorSipController@getSipType');
$router->put('investor_sip/detail', 'InvestorSipController@getInvestorSipDetail');
$router->put('investor_sip/update', 'InvestorSipController@update');
$router->put('investor_sip/active', 'InvestorSipController@active');
$router->put('investor_sip/pause', 'InvestorSipController@pause');
$router->put('investor_sip/stop', 'InvestorSipController@stop');

