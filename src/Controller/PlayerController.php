<?php

declare(strict_types=1);

namespace KsPlayer\Controller;

use KsPlayer\Security\Encryption;
use KsPlayer\Service\BloggerApi;

class PlayerController
{
    private Encryption $encryptionService;
    private BloggerApi $bloggerApiService;
    private ?string $postId = null;

    public function __construct()
    {
        $this->encryptionService = new Encryption();
        $this->bloggerApiService = new BloggerApi($this->encryptionService);
    }

    public function validateRequest(): void
    {
        $encryptedVideoId = $_GET['v'] ?? null;

        $decryptedValue = $encryptedVideoId
            ? $this->encryptionService->decryptPostId($encryptedVideoId)
            : null;

        if (!$decryptedValue || !ctype_digit((string) $decryptedValue)) {
            $this->abortWithError($encryptedVideoId ? 400 : 403);
        }

        $this->postId = (string) $decryptedValue;
    }

    public function getData(): ?array
    {
        if (!$this->postId) {
            return null;
        }

        $postData = $this->bloggerApiService->fetchPost($this->postId);

        if (isset($_GET['action']) && $_GET['action'] === 'get_post') {
            $this->verifyOrigin();

            if (!$postData) {
                $this->sendJsonResponse(['error' => 'Video not found.'], 404);
            }

            $this->sendJsonResponse($postData);
        }

        return $postData ?: null;
    }

    private function verifyOrigin(): void
    {
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';

        $hasValidOrigin = !empty($referrer)
            && parse_url($referrer, PHP_URL_HOST) === $_SERVER['HTTP_HOST'];

        $isXmlHttpRequest = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
        $fetchDestination = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? '';

        if (!$hasValidOrigin || !$isXmlHttpRequest || $fetchDestination === 'document') {
            $this->sendJsonResponse(['error' => '403 Forbidden: Access denied!'], 403);
        }
    }

    private function sendJsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data);
        exit;
    }

    private function abortWithError(int $statusCode): void
    {
        http_response_code($statusCode);
        $errorFilePath = "includes/error.php";

        if (file_exists($errorFilePath)) {
            include $errorFilePath;
            exit;
        }

        $errorMessage = ($statusCode === 400) ? 'Bad Request' : 'Access Denied';
        exit("Error {$statusCode}: {$errorMessage}.");
    }
}