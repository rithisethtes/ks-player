# KS Player

![PHP](https://img.shields.io/badge/php-777bb4?style=for-the-badge&logo=php&logoColor=white)
![GitHub Release](https://img.shields.io/github/v/release/rithisethtes/ks-player?label=Version&style=for-the-badge)
[![Live Demo](https://img.shields.io/badge/Preview-Live_Demo-7ccf00?style=for-the-badge)](https://player.rithisethtes.com/?v=YSB6uGQtQq77oP1UvT1yLFpsrCjfOMfbFA1Mu0ptHXC3T-fpndgQTfnkgKqHvwA)

## Overview
**KS Player** is a modern PHP-based JW Player integration that dynamically fetches and processes video sources and subtitles from the Blogger JSON Feed API.

## Features
* **Modern Design:** A fully customized JW Player skin, enhanced with a sleek, modern design for a clean and professional look.
* **Monetization:** Supports placing popup ads, such as **Adsterra Popunder** or other ads directly on the video player.

## Requirements
Before installing, ensure your server meets the following requirements:
* **Web Server:** `Apache` or `Nginx`
* **PHP:** Version 8.2 or higher
* **PHP Extensions:** `cURL`, `JSON`, `OpenSSL`

## Installation & Setup
### 1. Deploy the Project Assets
Upload all project source files and directories to your web server's root directory (e.g., `public_html` or `/var/www/html`).
### 2. Configure
Open the `config.php` file and update the placeholder with your secret key and blog ID.

```php
// Secret key
define('SECRET_KEY', 'ENTER_SECRET_KEY_HERE');

// List of blog IDs
define('BLOGGER_IDS', [
    'ENTER_BLOG_ID_HERE',
]);
```
## Credits
This project is built upon the following technologies and services:
* **[JW Player](https://www.jwplayer.com/)** - Powerful video player platform providing robust video streaming, playback control, and customizable player UI.
* **[Blogger API](https://developers.google.com/blogger)** - Google's Blogger API for seamless content retrieval and video source management from Blogger blogs.

## Contact
If you have any questions, suggestions, or issues, feel free to connect with me on Facebook.

[![Facebook](https://img.shields.io/badge/facebook-1877f2?style=for-the-badge&logo=facebook)](https://www.facebook.com/rithisethtes)

## License
This project is open-source software licensed under the **MIT License**.

[![License](https://img.shields.io/github/license/rithisethtes/ks-player?style=for-the-badge)](LICENSE)
