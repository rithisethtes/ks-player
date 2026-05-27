<?php

declare(strict_types=1);

$statusCode = $statusCode ?? http_response_code() ?: 403;

if ($statusCode === 400) {
    $pageTitle = 'Bad Request';
    $errorMessage = 'The server could not understand the request due to invalid syntax.';
} else {
    $pageTitle = 'Forbidden';
    $errorMessage = "You don't have permission to access this page.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $statusCode . ' ' . $pageTitle; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            display: grid;
            place-content: center;
            gap: clamp(1rem, 2vw, 1.75rem);
            min-block-size: 100dvh;
            padding-inline: clamp(1rem, 5vw, 3rem);
            font-family: system-ui, sans-serif;
            font-weight: 600;
            background: #000000;
            color: #bababa;
            text-align: center;
            letter-spacing: .05em;
            line-height: 1.6;
            -webkit-user-select: none;
            user-select: none;
        }
        h1 {
            font-size: clamp(4rem, 10vw, 6rem);
            line-height: 1;
        }
        h2 {
            font-size: clamp(1.75rem, 5vw, 2.25rem);
        }
    </style>
</head>
<body>
    <h1><?php echo $statusCode; ?></h1>
    <h2><?php echo $pageTitle; ?></h2>
    <p><?php echo $errorMessage; ?></p>
</body>
</html>