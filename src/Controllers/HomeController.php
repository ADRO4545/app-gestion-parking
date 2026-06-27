<?php
// src/Controllers/HomeController.php
namespace App\Controllers;

use App\Models\TarifModel;

class HomeController {
    public function index() {
        // 1. On instancie le modèle et on récupère les données SQL
        $tarifModel = new TarifModel();
        $tarifs = $tarifModel->getAllTarifs();

        // 2. On transmet ces données à la Vue (le fichier HTML)
        require_once __DIR__ . '/../Views/home.php';
    }
}