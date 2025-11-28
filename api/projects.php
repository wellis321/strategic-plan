<?php
// Projects API endpoint
header('Content-Type: application/json');

$projectModel = new Project();
$method = getRequestMethod();

try {
    switch ($method) {
        case 'GET':
            $filters = [
                'goal_id' => getQueryParam('goal_id'),
                'search' => getQueryParam('search')
            ];
            $projects = $projectModel->getAll($filters);
            jsonResponse(['success' => true, 'data' => $projects]);
            break;

        case 'POST':
            // Validate CSRF token
            $postData = json_decode(file_get_contents('php://input'), true) ?? getPostData();

            if (!validateCsrfToken($postData['csrf_token'] ?? '')) {
                jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
            }

            $data = sanitizeInput($postData);
            $errors = $projectModel->validate($data);

            if (!empty($errors)) {
                jsonResponse(['success' => false, 'errors' => $errors], 400);
            }

            $projectId = $projectModel->create($data);
            $project = $projectModel->getById($projectId);

            jsonResponse(['success' => true, 'data' => $project], 201);
            break;

        case 'PUT':
            $id = getQueryParam('id');
            if (!$id) {
                jsonResponse(['success' => false, 'error' => 'Project ID required'], 400);
            }

            $putData = json_decode(file_get_contents('php://input'), true);

            if (!validateCsrfToken($putData['csrf_token'] ?? '')) {
                jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
            }

            $data = sanitizeInput($putData);
            $data['id'] = $id;
            $errors = $projectModel->validate($data);

            if (!empty($errors)) {
                jsonResponse(['success' => false, 'errors' => $errors], 400);
            }

            $projectModel->update($id, $data);
            $project = $projectModel->getById($id);

            jsonResponse(['success' => true, 'data' => $project]);
            break;

        case 'DELETE':
            $id = getQueryParam('id');
            if (!$id) {
                jsonResponse(['success' => false, 'error' => 'Project ID required'], 400);
            }

            $deleteData = json_decode(file_get_contents('php://input'), true);

            if (!validateCsrfToken($deleteData['csrf_token'] ?? '')) {
                jsonResponse(['success' => false, 'error' => 'Invalid security token'], 403);
            }

            $projectModel->delete($id);
            jsonResponse(['success' => true, 'message' => 'Project deleted successfully']);
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
