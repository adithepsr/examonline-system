<?php
/**
 * E-Exam System
 * Licensed under MIT (LICENSE)
 * Copyright © 2024 อ.อดิเทพ ศรีมันตะ (อ.มนต์)
 */
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Exam Portal | Thailand Online Examination</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #f97316;
            --primary-dark: #ea580c;
            --secondary: #1e293b;
            --white: #ffffff;
            --gray-light: #f8fafc;
            --gold: #fbbf24;
            --shadow-premium: 0 20px 50px rgba(0,0,0,0.08);
        }

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            user-select: none;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
            background-color: var(--gray-light);
        }
        
        .main-container {
            width: 100%;
            height: 100%;
            display: flex;
            position: relative;
        }

        /* Banner Section - Left Side */
        .banner-section {
            flex: 1.4;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            position: relative;
            color: white;
            padding: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .mesh-gradient {
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(249, 115, 22, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(251, 191, 36, 0.1) 0%, transparent 50%);
            z-index: 1;
        }

        .banner-content {
            position: relative;
            z-index: 10;
            max-width: 600px;
            animation: fadeInBlur 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes fadeInBlur {
            from { opacity: 0; transform: translateX(-30px); filter: blur(10px); }
            to { opacity: 1; transform: translateX(0); filter: blur(0); }
        }

        .banner-label {
            display: inline-block;
            background: rgba(249, 115, 22, 0.1);
            color: var(--primary);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 20px;
            border: 1px solid rgba(249, 115, 22, 0.2);
        }

        .banner-title {
            font-size: 5.5rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            line-height: 0.9;
            margin-bottom: 24px;
            letter-spacing: -3px;
            background: linear-gradient(to bottom, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .banner-text {
            font-size: 1.25rem;
            color: #94a3b8;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .quote-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 24px 32px;
            border-radius: 24px;
            position: relative;
            transition: all 0.3s;
        }

        .quote-card:hover { border-color: rgba(249, 115, 22, 0.3); transform: translateY(-5px); }
        .quote-icon { font-size: 2rem; color: var(--primary); opacity: 0.5; margin-bottom: 15px; }
        .quote-text { font-size: 1.15rem; font-style: italic; color: #cbd5e1; margin-bottom: 12px; }
        .quote-author { color: var(--primary); font-weight: 700; font-family: 'Outfit', sans-serif; }

        /* Login Section - Right Side */
        .login-section {
            flex: 1;
            background: var(--white);
            padding: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: -40px 0 100px rgba(0,0,0,0.03);
            z-index: 20;
        }

        .logo-box { text-align: center; margin-bottom: 40px; }
        .school-logo {
            width: 140px;
            margin-bottom: 24px;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.05));
            transition: all 0.5s;
            cursor: pointer;
        }
        .school-logo:hover { transform: scale(1.1) rotate(5deg); }

        .login-title { font-size: 2.5rem; font-family: 'Outfit', sans-serif; font-weight: 800; color: var(--secondary); margin-bottom: 8px; }
        .login-subtitle { color: #64748b; font-size: 1rem; }

        .code-display {
            width: 100%;
            max-width: 380px;
            height: 100px;
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            border-radius: 24px;
            margin: 40px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.3s;
        }

        .code-display.active {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(249, 115, 22, 0.1);
        }

        .student-code-text {
            font-size: 3.5rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 10px;
            line-height: 1;
        }

        .code-label {
            position: absolute;
            top: -12px;
            left: 24px;
            background: white;
            padding: 0 12px;
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 6px;
        }

        /* Numpad Styling */
        .numpad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            width: 100%;
            max-width: 380px;
        }

        .num-btn {
            height: 75px;
            border: none;
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 20px;
            font-size: 2rem;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            color: var(--secondary);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .num-btn:hover {
            transform: translateY(-4px);
            background: #fff;
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 10px 25px rgba(249, 115, 22, 0.15);
        }

        .num-btn:active { transform: scale(0.95); }

        .btn-action { background: #fff7ed; color: var(--primary); border: none; }
        .btn-action:hover { background: #ffedd5; }

        .btn-enter {
            grid-column: span 3;
            height: 75px;
            background: linear-gradient(135deg, var(--primary) 0%, #facc15 100%);
            color: white;
            border-radius: 24px;
            font-size: 1.5rem;
            font-weight: 800;
            margin-top: 10px;
            box-shadow: 0 15px 30px rgba(249, 115, 22, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border: none;
        }

        .btn-enter:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(249, 115, 22, 0.4); filter: brightness(1.05); }

        .ip-indicator {
            position: absolute;
            bottom: 40px;
            left: 40px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 10;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-container { flex-direction: column; overflow-y: auto; }
            .banner-section { padding: 60px 30px; min-height: 45vh; align-items: center; text-align: center; }
            .banner-title { font-size: 3.5rem; }
            .login-section { border-radius: 40px 40px 0 0; margin-top: -40px; padding: 60px 30px; }
            .banner-content { width: 100%; }
        }
    </style>
</head>
<body>
    <?php
    $quotes = [
        ["text" => "การเรียนรู้ไม่ได้เกิดขึ้นโดยบังเอิญ แต่ต้องแสวงหาด้วยความกระตือรือร้น", "author" => "Abigail Adams"],
        ["text" => "อนาคตเป็นของคนที่เชื่อในความงดงามของความฝัน", "author" => "Eleanor Roosevelt"],
        ["text" => "ความพยายามอยู่ที่ไหน ความสำเร็จอยู่ที่นั่น", "author" => "สุภาษิตไทย"],
        ["text" => "Education is the most powerful weapon which you can use to change the world.", "author" => "Nelson Mandela"],
        ["text" => "อย่ายอมแพ้ในวันนี้ เพราะวันแห่งชัยชนะรอคุณอยู่ข้างหน้า", "author" => "Anonymous"]
    ];
    $randomQuote = $quotes[array_rand($quotes)];
    ?>

    <div class="main-container">
        <!-- Banner Side -->
        <div class="banner-section">
            <div class="mesh-gradient"></div>
            <div class="banner-content">
                <span class="banner-label">E-Exam Platinum Edition</span>
                <h1 class="banner-title">Smart<br>Testing</h1>
                <p class="banner-text">ก้าวสู่โลกแห่งการทดสอบยุคใหม่ รวดเร็ว ปลอดภัย และแม่นยำ พร้อมประมวลผลทันทีด้วยระบบอัจฉริยะ</p>
                
                <div class="quote-card">
                    <i class="bi bi-quote quote-icon"></i>
                    <div class="quote-text">"<?php echo $randomQuote['text']; ?>"</div>
                    <div class="quote-author">/ <?php echo $randomQuote['author']; ?></div>
                </div>
            </div>
            
            <div class="ip-indicator">
                <i class="bi bi-broadcast text-primary"></i> Access Point: <?php echo $_SERVER['REMOTE_ADDR']; ?>
            </div>
        </div>

        <!-- Login Side -->
        <div class="login-section">
            <div class="logo-box">
                <img src="https://edu.msu.ac.th/TH/assets/images/LOGO.png" alt="Portal Logo" class="school-logo" id="portalTrigger">
                <h2 class="login-title">Student Login</h2>
                <p class="login-subtitle">ระบุรหัสนักเรียนเพื่อเข้าสู่ระบบสอบ</p>
            </div>

            <div class="code-display" id="screen">
                <span class="code-label">Student ID</span>
                <div class="student-code-text" id="display"></div>
            </div>

            <div class="numpad">
                <button class="num-btn" onclick="press('1')">1</button>
                <button class="num-btn" onclick="press('2')">2</button>
                <button class="num-btn" onclick="press('3')">3</button>
                <button class="num-btn" onclick="press('4')">4</button>
                <button class="num-btn" onclick="press('5')">5</button>
                <button class="num-btn" onclick="press('6')">6</button>
                <button class="num-btn" onclick="press('7')">7</button>
                <button class="num-btn" onclick="press('8')">8</button>
                <button class="num-btn" onclick="press('9')">9</button>
                
                <button class="num-btn btn-action" onclick="clearAll()"><i class="bi bi-c-circle"></i></button>
                <button class="num-btn" onclick="press('0')">0</button>
                <button class="num-btn btn-action" onclick="del()"><i class="bi bi-backspace"></i></button>

                <button class="btn-enter" onclick="submit()">
                    Start Exam <i class="bi bi-arrow-right-circle-fill"></i>
                </button>
            </div>

            <div class="mt-auto pt-4 text-muted small">
                &copy; <?php echo date('Y'); ?> Education Evolution. Secure Mode Enabled.
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let code = '';
        const display = $('#display');
        const screen = $('#screen');

        // Admin Secret Portal (Triple Click)
        let t = 0;
        $('#portalTrigger').click(() => {
            t++;
            if(t === 3) window.location.href = 'admin/login.php';
            setTimeout(() => t = 0, 1000);
        });

        function press(n) {
            if(code.length < 12) {
                code += n;
                render();
            }
        }

        function del() {
            code = code.slice(0, -1);
            render();
        }

        function clearAll() {
            code = '';
            render();
        }

        function render() {
            display.text(code);
            screen.addClass('active');
            setTimeout(() => screen.removeClass('active'), 150);
        }

        function submit() {
            if(!code) return Swal.fire({ icon: 'warning', title: 'กรุณากรอกรหัสนักเรียน', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            
            Swal.showLoading();
            $.post('student/verify_student.php', { student_code: code }, (res) => {
                if(res.success) {
                    Swal.fire({ icon: 'success', title: 'ยินดีต้อนรับ', text: res.student_name, timer: 1500, showConfirmButton: false })
                        .then(() => window.location.href = 'student/exam_instructions.php');
                } else {
                    Swal.fire({ icon: 'error', title: 'ไม่พบรหัสนี้', text: 'กรุณาตรวจสอบรหัสนักเรียนอีกครั้ง', confirmButtonColor: '#f97316' });
                }
            }, 'json');
        }

        $(document).keydown((e) => {
            if(e.key >= '0' && e.key <= '9') press(e.key);
            if(e.key === 'Backspace') del();
            if(e.key === 'Enter') submit();
        });
    </script>
</body>
</html>
