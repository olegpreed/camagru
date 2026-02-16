<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sofadi+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <title><?= htmlspecialchars($title ?? 'Camagru') ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        
        body {
            font-family:  "MS Sans Serif", "Tahoma", sans-serif;
            /* color: #d5d5d5; */
            background: url(
				'/assets/images/body_bg.png');
			background-size: cover;
            background-attachment: fixed;
            height: 100vh;
			font-size: 13px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        /* Header Container */
        header {
            display: flex;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .header-container {
            max-width: 900px;
            width: 100%;
        }
        
        /* Top Header Bar */
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            background: 
			url('/assets/images/header_bg.png'), linear-gradient(0deg, #5585ff 0%, #3e78ff 100%);
            background-size: 100% 100%, fill;
            background-repeat: no-repeat;
            background-blend-mode: soft-light;
        }
        
        .site-logo {
            color: #fff;
            font-family: 'Sofadi One';
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .user-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
        }
        
        .username {
            color: #ffc0e0;
            font-weight: bold;
        }
        
        .user-actions span {
            color: #fff;
        }
        
        .user-actions a,
        .user-actions button {
            color: #fff;
            text-decoration: none;
        }
        
        .user-actions a:hover {
            color: #ffc0e0;
            text-decoration: underline;
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            font: inherit;
            padding: 0;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .logout-btn:hover {
            color: #ffc0e0;
            text-decoration: underline;
        }
        
        /* Navigation Tabs */
        .header-nav {
            display: flex;
			/* background: rgba(255, 255, 255, 0.5); */
			border: 1px solid rgb(0, 0, 0);
			/* border-top: none; */
        }
        
        .nav-tab {
            flex: 1;
            text-align: center;
            padding: 10px 20px;
            border-bottom: none;
            text-decoration: underline;
            font-size: 14px;
            position: relative;
        }
        
        .nav-tab:not(:first-child) {
            border-left: none;
        }
        
        .nav-tab.disabled {
            color: rgb(108, 108, 108);
            cursor: not-allowed;
        }
        
        /* Main Content */
        main {
            flex: 1;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
			padding-top: 10px;
            overflow-y: auto;
            min-height: 0;
        }
        
        .footer-container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            padding: 15px 20px;
            text-align: center;
        }
        
        footer {
            flex-shrink: 0;
        }
        
        .footer-badges {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .footer-badges img {
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .copyright {
            color: #fff;
            font-size: 11px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                height: auto;
                min-height: 100vh;
                overflow: visible;
            }
            
            main {
                overflow-y: visible;
            }
            
            /* .header-top {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            } */
            
            /* .header-nav {
                flex-direction: column;
                padding: 0;
            } */
            
            .nav-tab {
                border-left: 1px solid #000000;
				border-right: 1px solid #000000;
			}
            
            main {
                margin: 10px auto;
                padding: 0 10px;
            }
            
            .site-logo {
                font-size: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .user-actions {
                font-size: 11px;
                gap: 8px;
            }
            
            .nav-tab {
                padding: 8px 15px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <?php
    use Middleware\AuthMiddleware;

    $user = AuthMiddleware::user();
    ?>
    <header>
        <div class="header-container">
            <!-- Top Bar: Logo and User Actions -->
            <div class="header-top">
                <a href="/" class="site-logo">WebSnap.com</a>
                <div class="user-actions">
                    <?php if ($user): ?>
                        <span class="username">[<?= htmlspecialchars($user['username']) ?>]</span>
                        <span>|</span>
                        <form method="POST" action="/auth/logout" style="display: inline; margin: 0;">
                            <?= \Core\CSRF::field() ?>
                            <button type="submit" class="logout-btn">Logout →</button>
                        </form>
                    <?php else: ?>
                        <a href="/auth/login">Login</a>
                        <span>|</span>
                        <a href="/auth/register">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Navigation Tabs -->
            <div class="header-nav">
                <a href="/gallery" class="nav-tab">Gallery</a>
                <a href="<?= $user ? '/image/edit' : '#' ?>" class="nav-tab <?= $user ? '' : 'disabled' ?>">Create</a>
                <a href="<?= $user ? '/user/profile' : '#' ?>" class="nav-tab <?= $user ? '' : 'disabled' ?>">Profile</a>
            </div>
        </div>
    </header>
    
    <main>
        <?= $content ?>
    </main>
    
    <footer>
        <div class="footer-container">
            <div class="footer-badges">
                <a href="https://www.php.net/" target="_blank" rel="noopener noreferrer">
                    <img src="https://cyber.dabamos.de/88x31/php4_88x31.gif" alt="PHP" width="88" height="31">
                </a>
                <a href="https://www.mozilla.org/firefox/" target="_blank" rel="noopener noreferrer">
                    <img src="https://cyber.dabamos.de/88x31/firefox3.gif" alt="Firefox" width="88" height="31">
                </a>
                <a href="https://github.com/olegpreed" target="_blank" rel="noopener noreferrer">
                    <img src="	https://cyber.dabamos.de/88x31/github.gif" alt="GitHub" width="88" height="31">
                </a>
                <img src="https://cyber.dabamos.de/88x31/html3_s1.gif" alt="HTML" width="88" height="31">
                <a href="https://www.42bangkok.com/" target="_blank" rel="noopener noreferrer">
                    <img src="https://cyber.dabamos.de/88x31/es-88x31.gif" alt="42 Bangkok" width="88" height="31">
                </a>
            </div>
            <p class="copyright">&copy; <?= date('Y') ?> Camagru. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>