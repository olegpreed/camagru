<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Camagru') ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        header {
            background: #2c3e50;
            color: white;
            padding: 1rem 2rem;
        }
        
        header h1 {
            font-size: 1.5rem;
        }
        
        main {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }
        
        footer {
            background: #34495e;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header>
        <h1>Camagru</h1>
    </header>
    
    <main>
        <?= $content ?>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> Camagru. All rights reserved.</p>
    </footer>
</body>
</html>