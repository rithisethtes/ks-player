<?php
/**
 * KS Player
 * 
 * A PHP-based JW Player integration that dynamically fetches video sources
 * and subtitles from the Blogger JSON Feed API.
 * 
 * @package   KsPlayer
 * @author    RithiSeth Tes
 * @version   0.1.0
 * @link      https://github.com/rithisethtes/ks-player
 * @license   MIT License
 * @copyright 2026 RithiSeth Tes
 */

declare(strict_types=1);

namespace KsPlayer;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

use KsPlayer\Controller\PlayerController;

$playerController = new PlayerController();
$playerController->validateRequest();

$playerData = $playerController->getData();

$isMovie = $playerData['isMovie'] ?? false;
$featuredImageUrl = $playerData['featuredImage'] ?? '';
$videoTitle = htmlspecialchars($playerData['title'] ?? 'No Title', ENT_QUOTES, 'UTF-8');

$episodeNumber = (int) ($_GET['ep'] ?? 1);

$pageTitle = $isMovie ? $videoTitle : "{$videoTitle} Episode {$episodeNumber}";
$siteName = 'KS Player';
$siteTitle = "{$pageTitle} - {$siteName}";
$metaDescription = "Watch {$pageTitle} online in full HD quality with fast streaming, multi-language subtitles, and compatible across all devices.";

$isSecureConnection = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$serverProtocol = $isSecureConnection ? 'https' : 'http';
$currentRequestUrl = "{$serverProtocol}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Indexing -->
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex, nofollow">
  <!-- DNS Prefetch -->
  <link rel="preconnect" href="//fonts.googleapis.com">
  <link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
  <link rel="dns-prefetch" href="//www.blogger.com">
  <link rel="dns-prefetch" href="//blogger.googleusercontent.com">
  <link rel="dns-prefetch" href="//content.jwplatform.com">
  <!-- Site Info -->
  <title><?php echo $siteTitle; ?></title>
  <meta name="description" content="<?php echo $metaDescription; ?>">
  <link rel="canonical" href="<?php echo $currentRequestUrl; ?>">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <!-- Open Graph Meta Tags -->
  <meta property="og:locale" content="en_US">
  <meta property="og:locale:alternate" content="km_KH">
  <meta property="og:type" content="video.tv_show">
  <meta property="og:title" content="<?php echo $videoTitle; ?>">
  <meta property="og:site_name" content="<?php echo $siteName; ?>">
  <meta property="og:description" content="<?php echo $metaDescription; ?>">
  <meta property="og:url" content="<?php echo $currentRequestUrl; ?>">
  <meta property="og:image" content="<?php echo $featuredImageUrl; ?>">
  <meta property="og:image:type" content="image/webp">
  <meta property="og:image:width" content="1920">
  <meta property="og:image:height" content="1080">
  <meta property="og:image:alt" content="<?php echo $videoTitle; ?>">
  <meta name="twitter:card" content="summary_large_image">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
  <!-- Player Styles -->
  <link rel="stylesheet" href="assets/css/player.min.css">
</head>
<body>
  <div class="video-container">
    <div id="ks-player"></div>
    <div class="loader">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
        <defs>
          <linearGradient id="lg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#49c628"/>
            <stop offset="100%" stop-color="#70f570"/>
          </linearGradient>
        </defs>
        <circle cx="25" cy="25" r="20" fill="none" stroke="url(#lg)" stroke-width="6" stroke-linecap="round"/>
      </svg>
    </div>
  </div>
  <script src="assets/js/crypto-js.min.js"></script>
  <script src="https://content.jwplatform.com/libraries/IDzF9Zmk.js"></script>
  <script src="assets/js/player.min.js"></script>
</body>
</html>