<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth/login');
});

route::resource('almacen/categoria','CategoriaController');

route::resource('almacen/articulo','ArticuloController');

route::resource('ventas/cliente','ClienteController');

route::resource('compras/proveedor','ProveedorController');

route::resource('compras/ingreso','IngresoController');

route::resource('ventas/venta','VentaController');

route::resource('mermas/merma','MermaController');

route::resource('seguridad/usuario','UsuarioController');




Route::auth();

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/ruta', 'HomeController@ruta')->name('ruta');

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/{slug?}', 'HomeController@ruta')->name('ruta');

