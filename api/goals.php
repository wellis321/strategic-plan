<?php
// Goals API endpoint
header('Content-Type: application/json');

// Authentication check
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'error' => 'Authentication required'], 401);
    exit;
}

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

$goalModel = new Goal();
$method = getRequestMethod();

try {
    switch ($method) {
        case 'GET':
            $id = getQueryParam('id');
            if ($id) {
                $goal = $goalModel->getById($id);
                if (!$goal) {
                    jsonResponse(['success' => false, 'error' => 'Goal not found'], 404);
                }
                // Verify goal belongs to user's organization
                if ($goal['organization_id'] != $organizationId) {
                    jsonResponse(['success' => false, 'error' => 'Access denied'], 403);
                }
                jsonResponse(['success' => true, 'data' => $goal]);
            } else {
                $goals = $goalModel->getAll(['organization_id' => $organizationId]);
                jsonResponse(['success' => true, 'data' => $goals]);
            }
            break;

        case 'POST':
            $postData = json_decode(file_get_contents('php://input'), true) ?? getPostData();

            if (!validateCsrfToken($postData['csrf_token'] ?? '')) {
                jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
            }

            $data = sanitizeInput($postData);
            // Force organization_id from current user, ignore any from request
            $data['organization_id'] = $organizationId;
            $errors = $goalModel->validate($data);

            if (!empty($errors)) {
                jsonResponse(['success' => false, 'errors' => $errors], 400);
            }

            $goalId = $goalModel->create($data);
            $goal = $goalModel->getById($goalId);

            jsonResponse(['success' => true, 'data' => $goal], 201);
            break;

        case 'PUT':
            $id = getQueryParam('id');
            if (!$id) {
                jsonResponse(['success' => false, 'error' => 'Goal ID required'], 400);
            }

            // Verify goal exists and belongs to user's organization
            $existingGoal = $goalModel->getById($id);
            if (!$existingGoal || $existingGoal['organization_id'] != $organizationId) {
                jsonResponse(['success' => false, 'error' => 'Goal not found or access denied'], 403);
            }

            $putData = json_decode(file_get_contents('php://input'), true);

            if (!validateCsrfToken($putData['csrf_token'] ?? '')) {
                jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
            }

            $data = sanitizeInput($putData);
            $data['id'] = $id;
            // Ensure organization_id cannot be changed
            $data['organization_id'] = $organizationId;
            $errors = $goalModel->validate($data);

            if (!empty($errors)) {
                jsonResponse(['success' => false, 'errors' => $errors], 400);
            }

            $goalModel->update($id, $data);
            $goal = $goalModel->getById($id);

            jsonResponse(['success' => true, 'data' => $goal]);
            break;

        case 'DELETE':
            $id = getQueryParam('id');
            if (!$id) {
                jsonResponse(['success' => false, 'error' => 'Goal ID required'], 400);
            }

            // Verify goal exists and belongs to user's organization
            $goal = $goalModel->getById($id);
            if (!$goal || $goal['organization_id'] != $organizationId) {
                jsonResponse(['success' => false, 'error' => 'Goal not found or access denied'], 403);
            }

            $deleteData = json_decode(file_get_contents('php://input'), true);

            if (!validateCsrfToken($deleteData['csrf_token'] ?? '')) {
                jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
            }

            $goalModel->delete($id);
            jsonResponse(['success' => true, 'message' => 'Goal deleted successfully']);
            break;

        default:
            jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
    }

} catch (Exception $e) {
    if (APP_ENV === 'development') {
        jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
    } else {
        jsonResponse(['success' => false, 'error' => 'Internal server error'], 500);
    }
}
?>
