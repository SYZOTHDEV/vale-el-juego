<?php
header('Content-Type: application/json');
session_start();

// Inicializar sesión de jugador
if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
    $_SESSION['fury'] = 0;
    $_SESSION['streak'] = 0;
    $_SESSION['multiplier'] = 1;
}

// Base de datos de vehículos en PHP
$carCatalog = [
    [
        "id" => 1,
        "name" => "BMW M4 Competition",
        "color" => "0x0055ff",
        "taste" => "Excelente",
        "hint" => "Pista: A Valentín le fascina el rugido de los BMW modernos.",
        "img" => "https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=600&q=80"
    ],
    [
        "id" => 2,
        "name" => "BMW Serie 3 1998",
        "color" => "0x556677",
        "taste" => "Malo",
        "hint" => "Pista: Valentín odia los autos viejos desgastados.",
        "img" => "https://images.unsplash.com/photo-1520050206274-a1ae44613e6d?auto=format&fit=crop&w=600&q=80"
    ],
    [
        "id" => 3,
        "name" => "Porsche 911 GT3 RS",
        "color" => "0xff3300",
        "taste" => "Excelente",
        "hint" => "Pista: Le encantan los súperdeportivos exóticos de alta gama.",
        "img" => "https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=600&q=80"
    ],
    [
        "id" => 4,
        "name" => "Ford Mustang Shelby V8",
        "color" => "0xccaa00",
        "taste" => "Excelente",
        "hint" => "Pista: Los V8 Muscle de gran potencia son sus preferidos.",
        "img" => "https://images.unsplash.com/photo-1584345604476-8ec5e12e42dd?auto=format&fit=crop&w=600&q=80"
    ]
];

$action = $_GET['action'] ?? '';

if ($action === 'get_car') {
    $randomCar = $carCatalog[array_rand($carCatalog)];
    $_SESSION['current_car'] = $randomCar;
    echo json_encode(["status" => "success", "car" => $randomCar]);
    exit;
}

if ($action === 'rate_car') {
    $data = json_decode(file_get_contents('php://input'), true);
    $rating = $data['rating'] ?? '';
    $currentCar = $_SESSION['current_car'] ?? null;

    if (!$currentCar) {
        echo json_encode(["status" => "error", "message" => "No car active"]);
        exit;
    }

    $isCorrect = ($rating === $currentCar['taste']);

    if ($isCorrect) {
        $_SESSION['streak']++;
        if ($_SESSION['streak'] % 2 === 0 && $_SESSION['multiplier'] < 8) {
            $_SESSION['multiplier'] *= 2;
        }
        $_SESSION['score'] += 100 * $_SESSION['multiplier'];
    } else {
        $_SESSION['streak'] = 0;
        $_SESSION['multiplier'] = 1;
        $_SESSION['fury'] = min(100, $_SESSION['fury'] + 34);
    }

    echo json_encode([
        "status" => "success",
        "correct" => $isCorrect,
        "score" => $_SESSION['score'],
        "fury" => $_SESSION['fury'],
        "multiplier" => $_SESSION['multiplier'],
        "triggerTerror" => ($_SESSION['fury'] >= 100),
        "triggerVictory" => ($_SESSION['score'] >= 1000)
    ]);
    exit;
}

if ($action === 'get_murder_role') {
    $roles = ['Inocente', 'Murder', 'Sheriff'];
    $assignedRole = $roles[array_rand($roles)];
    $npcs = ["Vale", "Reta", "Oscar", "Philippe", "Seba", "Martin", "Ale", "Benja", "Joako", "Garcia", "Cris", "Cristian", "Pancho", "Tati"];
    
    echo json_encode([
        "status" => "success",
        "role" => $assignedRole,
        "npcs" => $npcs
    ]);
    exit;
}

if ($action === 'reset_session') {
    session_destroy();
    echo json_encode(["status" => "success"]);
    exit;
}
