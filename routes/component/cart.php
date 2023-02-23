<?php
Route::get('/wishlist.html', 'ShopCart@wishlist')
->name('wishlist');
Route::get('/wishlist_remove/{id}', 'ShopCart@removeItemWishlist')
->name('wishlist.remove');

Route::get('/compare.html', 'ShopCart@compare')
->name('compare');
Route::get('/compare_remove/{id}', 'ShopCart@removeItemCompare')
->name('compare.remove');    

Route::get('/cart.html', 'ShopCart@getCart')
->name('cart');
Route::post('/cart.html', 'ShopCart@addToCart')
->name('cart.add');
Route::get('/cart_remove/{id}', 'ShopCart@removeItem')
->name('cart.remove');
Route::get('/clear_Cart', 'ShopCart@clearCart')
->name('cart.clear');
Route::post('/add_to_cart_ajax', 'ShopCart@addToCartAjax')
->name('cart.add_ajax');
Route::post('/update_to_cart', 'ShopCart@updateToCart')
->name('cart.update');


Route::get('/checkout.html', 'ShopCart@getCheckout')
->name('checkout');
Route::post('/checkout.html', 'ShopCart@processCart')
->name('checkout.prepare');

Route::post('/order_add', 'ShopCart@addOrder')
->name('order.add');