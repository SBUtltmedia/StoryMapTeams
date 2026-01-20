<?php
// --- CONFIGURATION ---
// The directory where the JSON files will be stored.
// For security, this should ideally be outside of the public web root.
$data_directory = 'storymap_data';

// --- SECURITY & SETUP ---
// Set the content type to JSON for all responses.
header('Content-Type: application/json');
// Set CORS headers to allow requests from any origin.
// For production, you should restrict this to your specific domain.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle pre-flight CORS OPTIONS requests.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Ensure the data directory exists.
if (!is_dir($data_directory)) {
    if (!mkdir($data_directory, 0755, true)) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Failed to create data directory.']);
        exit;
    }
}

// --- LOGIC ---
// Get the ID from the query string for ALL request types.
$id = null;
if (isset($_GET['id'])) {
    // Sanitize the ID to prevent directory traversal attacks (e.g., ../../... )
    $id = basename($_GET['id']);
}

// If no ID is provided in the URL, the request is invalid.
if (!$id) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => "The 'id' parameter is required in the query string (e.g., ?id=123)."]);
    exit;
}

$file_path = $data_directory . '/' . $id . '.json';

// --- REQUEST HANDLING ---

// Handle GET request to fetch a StoryMap
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($file_path)) {
        http_response_code(200); // OK
        readfile($file_path);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'StoryMap not found.']);
    }
    exit;
}

// Handle POST request to create or update a StoryMap
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The JSON data is expected in the 'json_data' field of the POST body.
    if (!isset($_POST['json_data'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => "The 'json_data' parameter is required in the POST body."]);
        exit;
    }

    $json_data = $_POST['json_data'];
    // Verify that the received data is valid JSON.
    $decoded_data = json_decode($json_data);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data provided.']);
        exit;
    }

    // Write the data to the file. file_put_contents handles creating/overwriting.
    // JSON_PRETTY_PRINT makes the saved file human-readable.
    if (file_put_contents($file_path, json_encode($decoded_data, JSON_PRETTY_PRINT))) {
        http_response_code(200); // OK
        echo json_encode(['success' => true, 'message' => 'StoryMap saved successfully.']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Failed to write to file on server.']);
    }
    exit;
}

// If the request method is not GET, POST, or OPTIONS, it's not allowed.
http_response_code(405); // Method Not Allowed
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
?>


