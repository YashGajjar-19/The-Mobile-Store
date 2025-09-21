<?php
$page_title = '404 - Page Not Found';
require_once '../includes/header.php';
?>

<style>
    .center-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        text-align: center;
        font-family: var(--paragraph);
        z-index: 1;
        padding: 20px;
    }

    .error-content {
        max-width: 850px;
        margin: 0 auto;
        background-color: white;
        padding: 60px 2rem;
        border-radius: 1rem;
        box-shadow: var(--shadow);
    }

    .error-image {
        width: 100%;
        max-width: 300px;
        margin-bottom: 2rem;
    }

    .error-content h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--darkest);
        margin-bottom: 1.2rem;
    }

    .error-content p {
        font-size: 1.1rem;
        color: var(--dark);
        margin-bottom: 2.5rem;
    }
</style>

<div class="center-container">
    <div class="error-content">
        <img src="./assets/images/svg/404.svg" alt="404 Not Found" class="error-image">
        <h1>Oops! Page Not Found</h1>
        <p>The page you are looking for might have been removed, had its name changed, or is temporarily
            unavailable.</p>
        <a href="index.php" class="button">Go to Homepage</a>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>