<?php

declare(strict_types=1);

namespace KsPlayer\Service;

use KsPlayer\Security\Encryption;

class BloggerApi
{
    private Encryption $encryptionService;

    public function __construct(Encryption $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function fetchPost(string $postId): ?array
    {
        $videoIndex = max(0, (int) ($_GET['ep'] ?? 1) - 1);
        $blogIndex = max(0, (int) ($_GET['b'] ?? 1) - 1);
        $blogIds = defined('BLOGGER_IDS') ? BLOGGER_IDS : [];
        $blogId = $blogIds[$blogIndex] ?? ($blogIds[0] ?? '');

        $feedUrl = "https://www.blogger.com/feeds/{$blogId}/posts/default/{$postId}?alt=json";

        $curlHandle = curl_init($feedUrl);
        $this->applyCurlOptions($curlHandle);

        $curlResponse = curl_exec($curlHandle);
        curl_close($curlHandle);

        if (!$curlResponse) {
            return null;
        }

        $decodedData = json_decode($curlResponse, true);

        $postEntry = $decodedData['entry'] ?? [];
        $postTitle = $postEntry['title']['$t'] ?? 'No Title';
        $postLabels = $postEntry['category'] ?? [];
        $htmlContent = $postEntry['content']['$t'] ?? '';

        $isMovie = false;
        foreach ($postLabels as $labelEntry) {
            if (isset($labelEntry['term']) && strtolower(trim($labelEntry['term'])) === 'movie') {
                $isMovie = true;
                break;
            }
        }

        preg_match('/<img.+?src=["\']([^"\'\s]+)["\']/', $htmlContent, $regexMatches);
        $featuredImage = $regexMatches[1] ?? '';

        $sanitizedContentItems = array_values(array_filter(array_map(
            'trim',
            explode(';', strip_tags($htmlContent))
        )));

        $videoList = [];
        $currentVideoIndex = -1;

        foreach ($sanitizedContentItems as $contentItem) {
            $stringParts = array_map('trim', explode('|', $contentItem));

            if ($currentVideoIndex >= 0 && count($stringParts) === 2) {
                $videoList[$currentVideoIndex]['subtitles'][$stringParts[1]] = $stringParts[0];
            } else {
                $currentVideoIndex++;
                $videoList[$currentVideoIndex] = [
                    'videoUrl' => $contentItem,
                ];
            }
        }

        $selectedVideo = $videoList[$videoIndex] ?? null;
        if (!$selectedVideo) {
            return null;
        }

        $responsePayload = [
            'featuredImage' => $featuredImage,
            'title' => $postTitle,
            'videoUrl' => $selectedVideo['videoUrl'],
        ];

        if (!empty($selectedVideo['subtitles'])) {
            $encryptionKey = bin2hex(random_bytes(32));
            $encryptedSubtitles = $this->encryptSubtitles($selectedVideo['subtitles'], $encryptionKey);

            if ($encryptedSubtitles) {
                $responsePayload['subtitles'] = $encryptedSubtitles;
                $responsePayload['key'] = $encryptionKey;
            }
        }

        if ($isMovie) {
            $responsePayload['isMovie'] = true;
        }

        return $responsePayload;
    }

    private function encryptSubtitles(array $subtitleSources, string $encryptionKey): array
    {
        if (empty($subtitleSources)) {
            return [];
        }

        $curlMultiHandle = curl_multi_init();
        $trackList = [];

        foreach ($subtitleSources as $subtitleUrl => $subtitleLabel) {
            $curlHandle = curl_init(trim($subtitleUrl));
            $this->applyCurlOptions($curlHandle);

            curl_multi_add_handle($curlMultiHandle, $curlHandle);
            $trackList[(int) $curlHandle] = [
                'ch' => $curlHandle,
                'label' => $subtitleLabel,
            ];
        }

        $stillRunning = null;
        do {
            curl_multi_exec($curlMultiHandle, $stillRunning);
            curl_multi_select($curlMultiHandle);
        } while ($stillRunning > 0);

        $encryptedCaptions = [];

        foreach ($trackList as $track) {
            $trackContent = curl_multi_getcontent($track['ch']);
            if ($trackContent) {
                $encryptedCaptions[] = [
                    'label' => $track['label'],
                    'data' => $this->encryptionService->encryptSubtitleContent($trackContent, $encryptionKey),
                ];
            }

            curl_multi_remove_handle($curlMultiHandle, $track['ch']);
            curl_close($track['ch']);
        }

        curl_multi_close($curlMultiHandle);

        return $encryptedCaptions;
    }

    private function applyCurlOptions($curlHandle): void
    {
        curl_setopt_array($curlHandle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 15,
        ]);
    }
}