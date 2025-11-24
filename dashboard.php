<?php $page = 'dashboard'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAUD Finance - Dashboard</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="header-section">
        <!-- Wrapper baru untuk menampung Tombol & Judul -->
        <div class="page-title-wrapper" style="display:flex; align-items:center;">
            <button class="mobile-toggle-btn" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="page-title">
                <h1>Dashboard</h1>
                <p>Overview of school finance</p>
            </div>
        </div>
        <!-- Sisa konten header (jika ada) -->
    </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-title"><i class="fa-solid fa-dollar-sign"></i> Total Profit</div>
                    <div class="stat-value" id="totalProfit">Rp 0</div>
                    <div class="stat-desc">
                        <i class="fa-solid fa-arrow-trend-up"></i> Based on all transactions
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
        </div>

        <div class="chart-section">
            <h3 class="section-title">Daily Income (Last 30 Days)</h3>
            <div style="height: 320px;">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>

        <div class="dashboard-grid">
            
            <div class="list-box">
                <h3 class="section-title">Recent Income (Today)</h3>
                <div id="recentIncomeList">
                    <p style="color:#9ca3af; font-size:13px;">Loading data...</p>
                </div>
            </div>

            <div class="list-box">
                <h3 class="section-title"><i class="fa-regular fa-user"></i> Users Without Income (Today)</h3>
                <div id="missingIncomeList">
                    <p style="color:#9ca3af; font-size:13px;">Checking users...</p>
                </div>
            </div>

        </div>
    </main>

    <!-- <div class="footer-badge"><i class="fa-solid fa-bolt"></i> Made with Emergent</div> -->

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getFirestore, collection, addDoc, onSnapshot, deleteDoc, doc, serverTimestamp, orderBy, query } 
        from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

        // 1. IMPORT CONFIG DARI FILE LUAR
        // Pastikan pakai "./" yang artinya "di folder yang sama"
        import { firebaseConfig } from './firebase-config.js'; 

        // 2. GUNAKAN VARIABEL IMPORT TADI
        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);

        // --- UTILS: Format Rupiah ---
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', currency: 'IDR', minimumFractionDigits: 0 
            }).format(number);
        }

        // --- UTILS: Get Today String (YYYY-MM-DD) ---
        const getTodayString = () => {
            const d = new Date();
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        async function loadDashboard() {
            try {
                // 1. Ambil Semua Users
                const usersSnap = await getDocs(collection(db, "users"));
                let allUsers = [];
                usersSnap.forEach(doc => {
                    allUsers.push({ id: doc.id, name: doc.data().name });
                });

                // 2. Ambil Semua Transaksi (Untuk hitung total & chart)
                // Idealnya di app besar pakai query 'where', tapi untuk PAUD scale fetch all masih oke
                const transRef = collection(db, "transactions");
                const q = query(transRef, orderBy("date", "asc")); 
                const transSnap = await getDocs(q);

                let totalIncome = 0;
                let totalExpense = 0;
                let todayTransactions = [];
                let paidUserIdsToday = new Set();
                const todayStr = getTodayString();

                // Data untuk Chart (Tanggal => Total)
                let chartDataMap = {}; 
                
                transSnap.forEach(doc => {
                    const t = doc.data();
                    const amount = parseInt(t.amount) || 0;

                    // Hitung Total Saldo Global
                    if(t.type === 'income') totalIncome += amount;
                    else if(t.type === 'expense') totalExpense += amount;

                    // Cek Transaksi Hari Ini
                    if (t.date === todayStr && t.type === 'income') {
                        todayTransactions.push(t);
                        paidUserIdsToday.add(t.userId);
                    }

                    // Data Chart (Hanya Income)
                    if(t.type === 'income') {
                        if(chartDataMap[t.date]) {
                            chartDataMap[t.date] += amount;
                        } else {
                            chartDataMap[t.date] = amount;
                        }
                    }
                });

                // --- RENDER 1: Total Profit ---
                document.getElementById('totalProfit').innerText = formatRupiah(totalIncome - totalExpense);

                // --- RENDER 2: Chart (30 Hari Terakhir) ---
                renderChart(chartDataMap);

                // --- RENDER 3: Recent Income (Today) ---
                const recentListEl = document.getElementById('recentIncomeList');
                recentListEl.innerHTML = "";
                
                if (todayTransactions.length === 0) {
                    recentListEl.innerHTML = "<p style='color:#9ca3af;font-size:13px'>No income recorded today.</p>";
                } else {
                    // Balik urutan agar yang terbaru diatas (jika query sort asc)
                    todayTransactions.reverse().forEach(t => {
                        const initials = t.userName ? t.userName.substring(0, 2).toUpperCase() : "US";
                        recentListEl.innerHTML += `
                            <div class="list-item">
                                <div class="user-meta">
                                    <div class="avatar-sm">${initials}</div>
                                    <div class="meta-text">
                                        <h4>${t.userName}</h4>
                                        <p>${t.date}</p>
                                    </div>
                                </div>
                                <div class="amount-plus">+${formatRupiah(t.amount)}</div>
                            </div>
                        `;
                    });
                }

                // --- RENDER 4: Users Without Income (Today) ---
                const missingListEl = document.getElementById('missingIncomeList');
                missingListEl.innerHTML = "";

                // Filter user yang ID-nya TIDAK ada di set paidUserIdsToday
                const unpaidUsers = allUsers.filter(u => !paidUserIdsToday.has(u.id));

                if (unpaidUsers.length === 0) {
                    missingListEl.innerHTML = "<p style='color:#16a34a;font-size:13px'><i class='fa-solid fa-check'></i> Great! All users contributed today.</p>";
                } else if (allUsers.length === 0) {
                    missingListEl.innerHTML = "<p style='color:#9ca3af;font-size:13px'>No users in database.</p>";
                } else {
                    unpaidUsers.forEach(u => {
                        const initials = u.name.substring(0, 2).toUpperCase();
                        missingListEl.innerHTML += `
                            <div class="list-item">
                                <div class="user-meta">
                                    <div class="avatar-sm" style="background-color:#9ca3af">${initials}</div>
                                    <div class="meta-text">
                                        <h4>${u.name}</h4>
                                        <p>No record today</p>
                                    </div>
                                </div>
                                <div class="status-pending">Waiting</div>
                            </div>
                        `;
                    });
                }

            } catch (error) {
                console.error("Error loading dashboard:", error);
            }
        }

        // --- FUNGSI CHART JS ---
        function renderChart(dataMap) {
            const ctx = document.getElementById('incomeChart').getContext('2d');
            
            // Buat array 30 hari terakhir
            const labels = [];
            const dataValues = [];
            
            for (let i = 29; i >= 0; i--) {
                const d = new Date();
                d.setDate(d.getDate() - i);
                const dateStr = d.toISOString().split('T')[0]; // Format YYYY-MM-DD
                
                // Format Tampilan Label (Tgl/Bln)
                const labelStr = d.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });
                
                labels.push(labelStr);
                dataValues.push(dataMap[dateStr] || 0); // Jika tidak ada data, isi 0
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Income (Rp)',
                        data: dataValues,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 2,
                        tension: 0.4, // Garis melengkung halus
                        fill: true,
                        pointRadius: 0, // Titik disembunyikan agar bersih
                        pointHoverRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false } // Sembunyikan legenda
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, maxTicksLimit: 10 }
                        }
                    }
                }
            });
        }

        // Jalankan
        loadDashboard();

    </script>
</body>
</html>