<?php

use App\Admin\Controllers\FarmAnimalController;
use App\Admin\Controllers\HealthRecordController;
use App\Admin\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\RatingController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\Gen;
use App\Models\Order;
use App\Models\Utils;
use Carbon\Carbon;
use Dflydev\DotAccessData\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;



Route::get('mail-test', function () {

    $lastOrder = Order::orderBy('id', 'desc')->first();
    $lastOrder->order_state = 0;
    $lastOrder->stripe_url .= '1';
    $lastOrder->save();
    die('mail-test');
    Order::send_mails($lastOrder);

    $data['body'] = 'This should be the body of the <b>email</b>.';
    $data['data'] = $data['body'];
    $data['name'] = 'Hohn peter';
    $data['email'] = 'mubahood360@gmail.com';
    $data['subject'] = 'TEST UGANDA ' . ' - M-Omulimisa';

    Utils::mail_sender($data);
    die("success");
});

Route::get('test', function () {

    return;

    $stripe = env('STRIPE_KEY');
    $stripe = new \Stripe\StripeClient(
        env('STRIPE_KEY')
    );

    $name = 'Order payment for ' . date('Y-m-d H:i:s') . " " . rand(1, 100000);

    $resp = null;
    try {
        $resp = $stripe->products->create([
            'name' => $name,
            'default_price_data' => [
                'currency' => 'cad',
                'unit_amount' => 1 * 100,
            ],
        ]);
    } catch (\Throwable $th) {
        throw $th;
    }
    if ($resp == null) {
        throw new \Exception("Error Processing Request", 1);
    }
    if ($resp->default_price == null) {
        throw new \Exception("Error Processing Request", 1);
    }
    $linkResp = null;
    try {
        $linkResp = $stripe->paymentLinks->create([
            'currency' => 'cad',
            'line_items' => [
                [
                    'price' => $resp->default_price,
                    'quantity' => 1,
                ]
            ]
        ]);
    } catch (\Throwable $th) {
        throw $th;
    }
    if ($linkResp == null) {
        throw new \Exception("Error Processing Request", 1);
    }
});



Route::get('migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    return Artisan::output();
});

Route::get('clear', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('optimize:clear');
    exec('composer dump-autoload -o');
    return Artisan::output();
});
Route::get('artisan', function (Request $request) {
    // Artisan::call('migrate');
    //do run laravel migration command
    //php artisan l5-swagger:generate
    Artisan::call($request->command, ['--force' => true]);
    //returning the output
    return Artisan::output();
});




Route::match(['get', 'post'], '/pay', function () {
    $id = 1;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $order = \App\Models\Order::first();
        $id = $order->id;
    }
    $order = \App\Models\Order::find($id);
    $customer = $order->customer;
    //dd($customer);
    // $order->amount = 1;
    // $order->save();

    $task = null;
    if (isset($_GET['task'])) {
        $task = $_GET['task'];
    }
    if ($task == "success") {
        $order->payment_confirmation = 1;
        $data['get'] = $_GET;
        $data['post'] = $_POST;
        $order->stripe_id = json_encode($data);
        $order->save();
        die("Payment was successful");
    } else if ($task == "canceled") {
        $data['get'] = $_GET;
        $data['post'] = $_POST;
        $order->stripe_url = json_encode($data);
        $order->save();
        die("Payment was canceled");
    } else if ($task == "update") {
        $data['task'] = $task;
        $data['get'] = $_GET;
        $data['post'] = $_POST;
        $order->order_details = json_encode($data);
        $order->save();
        //return 200 response
        return response()->json(['status' => 'success', 'message' => 'Payment was updated.']);
    }

    $base_link = url('/pay?id=' . $id);
    return view('pay', [
        'order' => $order,
        'base_link' => $base_link
    ]);
});
Route::get('/process', function () {

    //set_time_limit(0);
    set_time_limit(-1);
    //ini_set('memory_limit', '1024M');
    ini_set('memory_limit', '-1');

    $folderPath2 = base_path('public/temp/pics/final');
    $folderPath = base_path('public/temp/pics/');
    $biggest = 0;
    $tot = 0;

    // Check if the folder exists
    if (is_dir($folderPath)) {
        // Get the list of items in the folder
        $items = scandir($folderPath);
        $items_1 = scandir($folderPath2);

        $i = 0;


        // Loop through the items
        foreach ($items as $item) {

            // Exclude the current directory (.) and parent directory (..)
            if ($item != '.' && $item != '..') {


                $ext = pathinfo($item, PATHINFO_EXTENSION);
                if ($ext == null) {
                    continue;
                }
                $ext = strtolower($ext);


                if (!in_array($ext, [
                    'jpg',
                    'jpeg',
                    'png',
                    'gif',
                ])) {
                    continue;
                }

                $target = $folderPath . $item;
                $target_file_size = filesize($target);

                $target_file_size_to_mb = $target_file_size / (1024 * 1024);
                $target_file_size_to_mb = round($target_file_size_to_mb, 2);
                /* if($target_file_size_to_mb > 2){
                    $source = $target;
                    $dest = $folderPath . "final/" . $item;
                    Utils::create_thumbail([
                        'source' => $source,
                        'target' => $dest
                    ]);
                    unlink($source); 
                } */


                if ($target_file_size > $biggest) {
                    $biggest = $target_file_size;
                }
                $tot += $target_file_size;


                continue;
                //echo $i.". ".$item . "<br>";
                $i++;
                continue;

                $i++;
                print_r($i . "<br>");



                $fileSize = filesize($folderPath . "/" . $item);
                $fileSize = $fileSize / (1024 * 1024);
                $fileSize = round($fileSize, 2);
                $fileSize = $fileSize . " MB";
                $url = "http://localhost:8888/ham/public/temp/pics-1/" . $item;

                $source = $folderPath . "/" . $item;
                $target = $folderPath . "/thumb/" . $item;
                Utils::create_thumbail([
                    'source' => $source,
                    'target' => $target
                ]);

                echo "<img src='$url' alt='$item' width='550'/>";
                $target_file_size = filesize($target);
                $target_file_size = $target_file_size / (1024 * 1024);
                $target_file_size = round($target_file_size, 2);
                $target_file_size = $target_file_size . " MB";
                $url_2 = "http://localhost:8888/ham/public/temp/pics-1/thumb/" . $item;
                echo "<img src='$url_2' alt='$item' width='550' />";



                // Print the item's name
                echo "<b>" . $fileSize . "<==>" . $target_file_size . "<b><br>";
            }
        }
    } else {
        echo "The specified folder does not exist.";
    }

    $biggest = $biggest / (1024 * 1024);
    $biggest = round($biggest, 2);
    $biggest = $biggest . " MB";
    $tot = $tot / (1024 * 1024);
    $tot = round($tot, 2);
    $tot = $tot . " MB";
    echo "Biggest: " . $biggest . "<br>";
    echo "Total: " . $tot . "<br>";
    die("=>done<=");
});
Route::get('/sync', function () {
    Utils::sync_products();
    Utils::sync_orders();
})->name("gen");
Route::get('/gen', function () {
    die(Gen::find($_GET['id'])->do_get());
})->name("gen");
Route::get('/gen-form', function () {
    die(Gen::find($_GET['id'])->make_forms());
})->name("gen-form");
Route::get('generate-class', [MainController::class, 'generate_class']);

Route::view('auth/register', 'auth.register');
Route::get('/user-activity', [HomeController::class, 'index'])->name('user-activity');


Route::get('farmers-farms/{id}', [FarmAnimalController::class, 'getFarms'])->name('farm.animals');
Route::get('farms-animals/{id}', [HealthRecordController::class, 'getAnimals'])->name('farm.animals');
Route::get('farmer-farms/{id}', [HealthRecordController::class, 'getFarms'])->name('farm.animals');

Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
Route::get('/paravets', [RatingController::class, 'index'])->name('paravet-ratings.index');
Route::get('/paravet-ratings', [HomeController::class, 'ratings']);

//action for post form auth/login

//auth/login gett
Route::get('auth/login', [AccountController::class, 'login'])->name('login');


// Auth::routes();
 
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
