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
    <?php
    use Middleware\AuthMiddleware;

    $user = AuthMiddleware::user();
    ?>
    <header>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1><a href="/" style="color: white; text-decoration: none;">Camagru</a></h1>
            <nav style="display: flex; gap: 1.5rem; align-items: center;">
                <a href="/gallery" style="color: white; text-decoration: none;">Gallery</a>
                <?php if ($user): ?>
                    <a href="/image/edit" style="color: white; text-decoration: none;">Create</a>
                    <a href="/user/profile" style="color: white; text-decoration: none;">Profile</a>
                    <span style="color: #ecf0f1;"><?= htmlspecialchars($user['username']) ?></span>
                    <form method="POST" action="/auth/logout" style="display: inline; margin: 0;">
                        <?= \Core\CSRF::field() ?>
                        <button type="submit" style="background: none; border: none; color: #ecf0f1; text-decoration: underline; cursor: pointer; font: inherit; padding: 0;">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="/auth/login" style="color: #ecf0f1; text-decoration: underline;">Login</a>
                    <a href="/auth/register" style="color: #ecf0f1; text-decoration: underline;">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main>
        <?= $content ?>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> Camagru. All rights reserved.</p>
    </footer>
</body>
</html>