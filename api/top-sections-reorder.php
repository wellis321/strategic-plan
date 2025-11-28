<?php
require_once __DIR__ . '/../config/bootstrap.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isOrganizationAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['order']) || !is_array($input['order'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

try {
    $topSectionModel = new OrganizationTopSection();
    $topSectionModel->reorder($input['order'], $organizationId);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('Top sections reorder error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
