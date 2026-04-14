<?php
// Connessione al database MariaDB
$host = 'localhost';
$db   = 'catalogo';
$user = 'io';
$pass = 'mille';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['errore' => 'Connessione al database fallita: ' . $conn->connect_error]);
    exit;
}

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    //GET: elenca tutti i libri 
    case 'GET':
        $result = $conn->query("SELECT * FROM libri");
        $libri = [];
        while ($row = $result->fetch_assoc()) {
            $libri[] = $row;
        }
        echo json_encode($libri);
        break;

    //POST: aggiunge un nuovo libro 
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);

        // Validazione campi obbligatori
        if (empty($body['id']) || empty($body['titolo']) || empty($body['autore']) || empty($body['anno'])) {
            http_response_code(400);
            echo json_encode(['errore' => 'Campi obbligatori mancanti: id, titolo, autore, anno']);
            exit;
        }

        $id     = (int) $body['id'];
        $titolo = $conn->real_escape_string($body['titolo']);
        $autore = $conn->real_escape_string($body['autore']);
        $anno   = (int) $body['anno'];

        $sql = "INSERT INTO libri (id, titolo, autore, anno) VALUES ($id, '$titolo', '$autore', $anno)";

        if ($conn->query($sql)) {
            http_response_code(201);
            echo json_encode(['id' => $id, 'titolo' => $titolo, 'autore' => $autore, 'anno' => $anno]);
        } else {
            http_response_code(500);
            echo json_encode(['errore' => 'Inserimento fallito: ' . $conn->error]);
        }
        break;

    //PUT: aggiorna un libro esistente
    case 'PUT':
        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['errore' => 'Parametro id mancante']);
            exit;
        }

        $id = (int) $_GET['id'];

        // Verifica esistenza
        $check = $conn->query("SELECT id FROM libri WHERE id = $id");
        if ($check->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['errore' => "Libro con id $id non trovato"]);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['titolo']) || empty($body['autore']) || empty($body['anno'])) {
            http_response_code(400);
            echo json_encode(['errore' => 'Campi obbligatori mancanti: titolo, autore, anno']);
            exit;
        }

        $titolo = $conn->real_escape_string($body['titolo']);
        $autore = $conn->real_escape_string($body['autore']);
        $anno   = (int) $body['anno'];

        $sql = "UPDATE libri SET titolo='$titolo', autore='$autore', anno=$anno WHERE id=$id";

        if ($conn->query($sql)) {
            http_response_code(200);
            echo json_encode(['messaggio' => "Libro con id $id aggiornato con successo"]);
        } else {
            http_response_code(500);
            echo json_encode(['errore' => 'Aggiornamento fallito: ' . $conn->error]);
        }
        break;

    //DELETE: rimuove un libro
    case 'DELETE':
        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['errore' => 'Parametro id mancante']);
            exit;
        }

        $id = (int) $_GET['id'];

        // Verifica esistenza
        $check = $conn->query("SELECT id FROM libri WHERE id = $id");
        if ($check->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['errore' => "Libro con id $id non trovato"]);
            exit;
        }

        if ($conn->query("DELETE FROM libri WHERE id = $id")) {
            http_response_code(200);
            echo json_encode(['messaggio' => "Libro con id $id eliminato con successo"]);
        } else {
            http_response_code(500);
            echo json_encode(['errore' => 'Eliminazione fallita: ' . $conn->error]);
        }
        break;

    //Metodo non supportato
    default:
        http_response_code(405);
        echo json_encode(['errore' => 'Metodo HTTP non supportato']);
        break;
}

$conn->close();
?>