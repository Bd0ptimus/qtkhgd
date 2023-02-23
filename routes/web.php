<?php

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

/*
 Home
*/
Route::get('/', 'ShopFront@index')->name('home');
Route::get('index.html', 'ShopFront@index');

/*
 Auth
*/
require_once 'component/auth.php';


/*
 Member
*/
require_once 'component/member.php';

/*
 Cart
*/
require_once 'component/cart.php';

/*
 Category
*/
require_once 'component/category.php';

/*
 Brand
*/
require_once 'component/brand.php';

/*
 Vendor
*/
require_once 'component/vendor.php';

/*
 Product
*/
require_once 'component/product.php';

/*
 Content
*/
require_once 'component/content.php';

//Language
Route::get('locale/{code}', function ($code) {
    session(['locale' => $code]);
    return back();
})->name('locale');

//Currency
Route::get('currency/{code}', function ($code) {
    session(['currency' => $code]);
    return back();
});

Route::get('tasks/download-attachment/{id}', 'DownloadController@downloadAttachment')
    ->name('download-attachment')
    ->where('id', '[0-9]+');;

Route::get('export/word-test1', 'ExportWordController@exportUseDocx')->name('export.test1');
Route::get('export/word-test2', 'ExportWordController@exportUseHTML')->name('export.test2');
Route::get('export/view-template', 'ExportWordController@getTempHTML')->name('export.view-template');
Route::get('import/file-exist', 'ImportWordController@handle')->name('import.file-exist');
Route::post('import/file-upload', 'ImportWordController@upload')->name('import.upload');
Route::get('test-import-export-word', 'ImportWordController@getFileUploadForm')->name('word.test');

Route::get('simulator', 'Controller@simulator')->name('simulators');

Route::group(['prefix' => 'simulator'], function ($router) {
    $router->get('/view/{slug}', 'SimulatorController@view')->name('simulator.index');
});

//--Please keep 2 lines route (pages + pageNotFound) at the bottom
Route::get('/{key}.html', 'ContentFront@pages')->name('pages');
// Route::fallback('ShopFront@pageNotFound')->name('pageNotFound'); //Make sure before using this route. There will be disadvantages when detecting 404 errors for static files like images, scripts ..
//--end keep
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
//=======End Front
