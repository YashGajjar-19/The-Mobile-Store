<?php
session_start();
require_once '../includes/config.php';

// Security Check
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../user/login.php?error=unauthorized");
    exit();
}

// Fetch all contact messages
$messages_stmt = $conn->prepare("SELECT name, email, subject, message, submitted_at FROM contact_messages ORDER BY submitted_at DESC");
$messages_stmt->execute();
$messages_result = $messages_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages | Admin</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .message-card {
            background-color: var(--body);
            border-radius: 1rem;
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            border-left: 5px solid var(--blue);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--glass);
        }

        .message-sender h3 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--darkest);
        }

        .message-sender a {
            font-size: 0.9rem;
            color: var(--dark);
            text-decoration: none;
        }

        .message-sender a:hover {
            text-decoration: underline;
        }

        .message-date {
            font-size: 0.85rem;
            color: var(--dark);
            font-weight: 500;
        }

        .message-subject {
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .message-body {
            line-height: 1.6;
        }
    </style>
</head>

<body class="admin-page">
    <main class="main-content" style="margin-left: 50px; margin-top: 50px;">
        <div class="dashboard-header-top">
            <div class="dashboard-title-group">
                <h2>Inbox Messages</h2>
                <div class="title-line"></div>
            </div>
            <a href="dashboard.php" class="button">Back to Dashboard</a>
        </div>

        <?php if ($messages_result->num_rows > 0): ?>
            <?php while ($message = $messages_result->fetch_assoc()): ?>
                <div class="message-card">
                    <div class="message-header">
                        <div class="message-sender">
                            <h3><?php echo htmlspecialchars($message['name']); ?></h3>
                            <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>"><?php echo htmlspecialchars($message['email']); ?></a>
                        </div>
                        <div class="message-date">
                            <?php echo date('M d, Y, h:i A', strtotime($message['submitted_at'])); ?>
                        </div>
                    </div>
                    <p class="message-subject"><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                    <div class="message-body">
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You have no new messages.</p>
        <?php endif; ?>

    </main>
</body>

</html>