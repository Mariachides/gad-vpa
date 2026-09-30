<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$activePage = 'avatar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Assistant | GAD VPA</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-purple: #6f42c1;
            --gradient-purple: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
            --chat-bg: #ffffff;
            --user-msg: #6f42c1;
            --bot-msg: #f1f3f5;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f7fa;
            color: #2d3436;
        }

        /* --- CHAT CONTAINER --- */
        .chat-container {
            max-width: 850px;
            margin: 20px auto; /* Small margin from top */
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bot-status-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mini-avatar {
            width: 35px;
            height: 35px;
            background: var(--light-purple, #f3effb);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            border: 1px solid #eee;
        }

        /* --- MESSAGE AREA --- */
        .chat-messages {
            height: 420px; /* Adjusted for better visibility */
            overflow-y: auto;
            padding: 20px;
            background-color: var(--chat-bg);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .message {
            max-width: 80%;
            padding: 10px 16px;
            border-radius: 16px;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        .message.user {
            background-color: var(--user-msg);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .message.avatar {
            background-color: var(--bot-msg);
            color: #444;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        /* --- QUICK QUESTIONS --- */
        .quick-replies {
            padding: 12px 20px;
            background: #fafafa;
            border-top: 1px solid #f0f0f0;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .btn-quick {
            border: 1px solid var(--primary-purple);
            color: var(--primary-purple);
            background: white;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .btn-quick:hover {
            background: var(--primary-purple);
            color: white;
        }

        /* --- INPUT AREA --- */
        .chat-input-wrapper {
            padding: 15px 20px;
            background: white;
            border-top: 1px solid #eee;
        }

        .input-pill {
            background: #f1f3f5;
            border-radius: 30px;
            padding: 4px 4px 4px 18px;
            display: flex;
            align-items: center;
        }

        .form-control-custom {
            background: transparent;
            border: none;
            flex-grow: 1;
            padding: 8px 0;
            outline: none;
            font-size: 0.95rem;
        }

        .btn-send-circle {
            background: var(--primary-purple);
            color: white;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: transform 0.2s;
        }

        .btn-send-circle:hover {
            transform: scale(1.05);
            background: var(--dark-purple);
        }

        footer {
            padding: 20px 0;
            color: #999;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>

<?php include 'includes/user_nav.php'; ?>

<main class="main-content">
    <div class="container chat-container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <div class="bot-status-info">
                            <div class="mini-avatar">💜</div>
                            <div>
                                <p class="mb-0 fw-bold" style="font-size: 0.9rem;">Virtual Assistant</p>
                                <small class="text-success" style="font-size: 0.75rem;">
                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i> Active Now
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="chat-messages" id="chatMessages">
                        <div class="message avatar">
                            👋 Kumusta! Ako ang iyong GAD Virtual Assistant. Maaari mo akong tanungin tungkol sa iyong karapatan o GAD policies. Paano kita matutulungan?
                        </div>
                    </div>

                    <div class="quick-replies">
                        <button class="btn btn-quick" onclick="sendQuickQuestion('What is GAD?')">What is GAD?</button>
                        <button class="btn btn-quick" onclick="sendQuickQuestion('My Rights')">My Rights</button>
                        <button class="btn btn-quick" onclick="sendQuickQuestion('RA 9262')">RA 9262</button>
                        <button class="btn btn-quick" onclick="sendQuickQuestion('Report')">Report</button>
                    </div>

                    <div class="chat-input-wrapper">
                        <div class="input-pill">
                            <input type="text" class="form-control-custom" id="chatInput" placeholder="Write a message...">
                            <button class="btn-send-circle" id="sendBtn">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <p class="mb-1">&copy; 2026 GAD VPA System</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#" class="text-decoration-none text-muted">Privacy</a>
                <span>&bull;</span>
                <a href="#" class="text-decoration-none text-muted">Help Center</a>
            </div>
        </div>
    </footer>
</main>

<script>
    function sendQuickQuestion(text) {
        document.getElementById('chatInput').value = text;
        document.getElementById('sendBtn').click();
    }

    document.getElementById('sendBtn').addEventListener('click', function () {
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        const message = text.toLowerCase();
        
        if (!text) return;

        const messagesDiv = document.getElementById('chatMessages');

        // User Bubble
        const userDiv = document.createElement('div');
        userDiv.className = 'message user shadow-sm';
        userDiv.textContent = text;
        messagesDiv.appendChild(userDiv);

        input.value = '';
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        // Bot Response
        setTimeout(() => {
            const botDiv = document.createElement('div');
            botDiv.className = 'message avatar shadow-sm';
            
            let response = "Pasensya na, maaari mo bang linawin ang iyong tanong? Maaari mo ring gamitin ang mga suggested topics sa taas.";

            if (message.includes("gad")) {
                response = "Ang Gender and Development (GAD) ay isang framework upang matiyak ang pantay na karapatan at oportunidad para sa lahat ng kasarian.";
            } else if (message.includes("rights") || message.includes("karapatan")) {
                response = "Bilang mamamayan, protektado ka ng batas laban sa anumang uri ng diskriminasyon at gender-based violence.";
            } else if (message.includes("9262")) {
                response = "Ang Republic Act 9262 ay batas na nagbibigay proteksyon sa mga kababaihan at kanilang mga anak laban sa karahasan.";
            } else if (message.includes("report")) {
                response = "Maaari kang mag-report nang anonymous. I-click lamang ang 'Report' menu o makipag-ugnayan sa pinakamalapit na GAD office.";
            }

            botDiv.textContent = response;
            messagesDiv.appendChild(botDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }, 700);
    });

    document.getElementById('chatInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') document.getElementById('sendBtn').click();
    });
</script>

</body>
</html>