<?php
// Backend - index.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$config = require '../config/config.php';

$pdsHost = 'https://bsky.social';
$authUrl = "$pdsHost/xrpc/com.atproto.server.createSession";

// Datos de autenticación
$blueskyHandle = $config['blueskyHandle'];
$blueskyPassword = $config['blueskyPassword'];

// Autenticación en Bluesky
$data = [
    'identifier' => $blueskyHandle,
    'password' => $blueskyPassword
];

$ch = curl_init($authUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);

if (!isset($responseData['accessJwt'])) {
    echo json_encode(["error" => "Error en autenticación"]);
    exit;
}

$accessToken = $responseData['accessJwt'];

// Obtener parámetros
$keyword = isset($_GET['q']) ? urlencode($_GET['q']) : '';
$searchType = isset($_GET['type']) ? $_GET['type'] : 'keyword';
$language = isset($_GET['language']) ? $_GET['language'] : ''; // Filtro de idioma
$mentions = isset($_GET['mentions']) ? $_GET['mentions'] : ''; // Filtro de menciones

// Validación
if (empty($keyword)) {
    echo json_encode(["error" => "Se requiere una palabra clave para la búsqueda."]);
    exit;
}

// Recibimos el cursor en la URL (si existe)
$cursor = isset($_GET['cursor']) ? $_GET['cursor'] : null;

// Construimos la URL para la búsqueda
$searchUrl = "https://api.bsky.app/xrpc/app.bsky.feed.searchPosts?q=$keyword&limit=25";

// Si hay menciones, las agregamos a la URL
if ($mentions) {
    $mentions = urlencode($mentions); // Asegurarnos que el valor de la mención esté codificado correctamente
    $searchUrl .= "&mentions=$mentions"; // Añadimos el filtro de menciones
}

// Si hay un cursor, lo agregamos a la URL
if ($cursor) {
    $searchUrl .= "&cursor=$cursor";
}

// Si se pasa el filtro de idioma, lo agregamos a la URL
if ($language) {
    $searchUrl .= "&lang=$language";  // Añadimos el filtro de idioma
}

// Realizamos la solicitud a la API
$ch = curl_init($searchUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $accessToken]);

$response = curl_exec($ch);
curl_close($ch);

if (empty($response)) {
    echo json_encode(["error" => "No se obtuvo respuesta de la API."]);
    exit;
}

$responseData = json_decode($response, true);

// Verificamos que haya posts en la respuesta
if (isset($responseData['posts'])) {
    // Devolvemos los posts y el cursor para la siguiente solicitud
    $nextCursor = isset($responseData['cursor']) ? $responseData['cursor'] : null;
    echo json_encode([
        "posts" => $responseData['posts'],
        "cursor" => $nextCursor
    ]);
} else {
    echo json_encode(["error" => "No se encontraron posts para la búsqueda especificada."]);
}
?>
