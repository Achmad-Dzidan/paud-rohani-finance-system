<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAUD Finance System - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-color: #f8fafc;
            --text-dark: #111827;
            --text-gray: #6b7280;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-dark);
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .logo-container {
            background-color: #dbeafe;
            color: var(--primary-blue);
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            font-size: 24px;
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .subtitle {
            color: var(--text-gray);
            font-size: 14px;
            margin-bottom: 32px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            color: var(--text-gray);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-primary {
            width: 100%;
            background-color: var(--primary-blue);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            margin-top: 8px;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .info-box {
            margin-top: 20px;
            background-color: #eff6ff;
            color: #1e40af;
            padding: 12px;
            border-radius: 8px;
            font-size: 12px;
            border: 1px solid #dbeafe;
        }

        .toggle-link {
            margin-top: 15px;
            font-size: 12px;
            color: var(--text-gray);
        }
        
        .toggle-link span {
            color: var(--primary-blue);
            cursor: pointer;
            font-weight: 600;
        }

        .footer-badge {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 11px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo-container">
            <i class="fa-solid fa-school"></i>
        </div>
        
        <h1>PAUD Finance System</h1>
        <p class="subtitle">School Administration Portal</p>

        <form id="authForm">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" id="email" placeholder="admin@paud.com" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" placeholder="Enter password" required>
                </div>
            </div>

            <button type="submit" class="btn-primary" id="submitBtn">Login</button>
        </form>

        <div class="info-box">
            For <b>private</b> access
        </div>

        <div class="toggle-link">
            <p id="toggleText">Belum punya akun? <span onclick="toggleMode()">Daftar disini</span></p>
        </div>
    </div>

    <!-- <div class="footer-badge">
        <i class="fa-solid fa-bolt"></i> Made with Emergent
    </div> -->

    <script type="module">
        // Import fungsi yang dibutuhkan dari Firebase SDK
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword, createUserWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

        // KONFIGURASI FIREBASE ANDA (Ganti dengan data dari Firebase Console)
        const firebaseConfig = {
            apiKey: "AIzaSyAxFeMsnK5FjkUgyNGDHZS2ZXyiowLdLEA",
            authDomain: "paud-rohani.firebaseapp.com",
            projectId: "paud-rohani",
            storageBucket: "paud-rohani.firebasestorage.app",
            messagingSenderId: "1051220684882",
            appId: "1:1051220684882:web:7d77a8367fa92b57c96f8b",
            measurementId: "G-SQHBZ6ST71"
            };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        // Variabel Status Mode (Login atau Register)
        let isLoginMode = true;

        const authForm = document.getElementById('authForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn');
        const toggleText = document.getElementById('toggleText');

        // Fungsi Toggle antara Login dan Register
        window.toggleMode = function() {
            isLoginMode = !isLoginMode;
            if (isLoginMode) {
                submitBtn.innerText = "Login";
                toggleText.innerHTML = 'Belum punya akun? <span onclick="toggleMode()">Daftar disini</span>';
            } else {
                submitBtn.innerText = "Register";
                toggleText.innerHTML = 'Sudah punya akun? <span onclick="toggleMode()">Login disini</span>';
            }
        }

        // Event Listener saat Form di-submit
        authForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const email = emailInput.value;
            const password = passwordInput.value;

            if (isLoginMode) {
                // PROSES LOGIN
                signInWithEmailAndPassword(auth, email, password)
                    .then((userCredential) => {
                        alert("Login Berhasil! Selamat datang, " + userCredential.user.email);
                        // Redirect ke halaman dashboard, contoh:
                        window.location.href = 'dashboard.php';
                    })
                    .catch((error) => {
                        const errorCode = error.code;
                        const errorMessage = error.message;
                        alert("Gagal Login: " + errorMessage);
                    });
            } else {
                // PROSES REGISTER
                createUserWithEmailAndPassword(auth, email, password)
                    .then((userCredential) => {
                        alert("Registrasi Berhasil! Silahkan login.");
                        toggleMode(); // Kembalikan ke mode login
                    })
                    .catch((error) => {
                        alert("Gagal Register: " + error.message);
                    });
            }
        });
    </script>
</body>
</html>