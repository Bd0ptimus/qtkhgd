<?php
Route::get('/search.html', 'ShopFront@search')
->name('search');
Route::post('/subscribe', 'ContentFront@emailSubscribe')
->name('subscribe');
Route::get('/contact.html', 'ContentFront@getContact')
->name('contact');
Route::post('/contact.html', 'ContentFront@postContact')
->name('postContact');
Route::get('/news.html', 'ContentFront@news')
->name('news');
Route::get('/news/{name}_{id}.html', 'ContentFront@newsDetail')
->where(['id' => '[0-9]+'])
->name('newsDetail');
