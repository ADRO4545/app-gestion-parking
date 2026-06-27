<?php

namespace App\Services;

class Router
{
    private array $routes = [
        'home'               => ['App\Controllers\HomeController', 'index'],
        'login'              => ['App\Controllers\AuthController', 'login'],
        'register'           => ['App\Controllers\AuthController', 'register'],
        'logout'             => ['App\Controllers\AuthController', 'logout'],
        'dashboard'          => ['App\Controllers\DashboardController', 'index'],
        'admin_dashboard'    => ['App\Controllers\AdminController', 'dashboard'],
        'book'               => ['App\Controllers\ReservationController', 'book'],
        'my_reservations'    => ['App\Controllers\ReservationController', 'myReservations'],
        'update_reservation' => ['App\Controllers\ReservationController', 'updateReservation'],
        'verify_email'       => ['App\Controllers\AuthController', 'verifyEmail'],
        'send_code_ajax'     => ['App\Controllers\AuthController', 'sendCodeAjax'],
        'login_ajax'         => ['App\Controllers\AuthController', 'loginAjax'],
        'cancel_reservation' => ['App\Controllers\ReservationController', 'cancelReservation'],
        'account'            => ['App\Controllers\AccountController', 'index'],
        'update_account'     => ['App\Controllers\AccountController', 'update'],
    ];

    public function handleRequest(): void
    {
        $routeDemandee = $_GET['action'] ?? 'login';

        if (array_key_exists($routeDemandee, $this->routes)) {
            $controllerName = $this->routes[$routeDemandee][0];
            $methodName = $this->routes[$routeDemandee][1];

            $controller = new $controllerName();
            $controller->$methodName();
        } else {
            http_response_code(404);
            echo "404 - Page non trouvée";
        }
    }
}
