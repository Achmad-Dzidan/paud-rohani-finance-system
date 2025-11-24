<?php $page = 'income'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAUD Finance - Income Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="centered-header">
            <div class="page-title-wrapper" style="display:flex; align-items:center; margin-bottom: 8px;">
                <button class="mobile-toggle-btn" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h1>Income Management</h1>
                    <p>Add or subtract income for users</p>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="toggle-container">
                <button class="toggle-btn active-income" id="btnIncome" onclick="setTransactionType('income')">
                    <i class="fa-solid fa-plus"></i> Add Income
                </button>
                <button class="toggle-btn" id="btnExpense" onclick="setTransactionType('expense')">
                    <i class="fa-solid fa-minus"></i> Subtract Income
                </button>
            </div>

            <form id="transactionForm">
                <div class="form-group">
                    <label class="form-label">Select User *</label>
                    <select id="userSelect" class="form-control" required>
                        <option value="" disabled selected>Choose a user</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount (Rp) *</label>
                    <input type="number" id="amountInput" class="form-control" placeholder="Enter amount" required min="1">
                </div>

                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" id="dateInput" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea id="noteInput" class="form-control" placeholder="Add any notes about this transaction"></textarea>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">Add Income</button>
            </form>
        </div>
    </main>

    <!-- <div class="footer-badge"><i class="fa-solid fa-bolt"></i> Made with Emergent</div> -->

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getFirestore, collection, addDoc, getDocs, serverTimestamp } 
        from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

        const firebaseConfig = {
            apiKey: "AIzaSyAxFeMsnK5FjkUgyNGDHZS2ZXyiowLdLEA",
            authDomain: "paud-rohani.firebaseapp.com",
            projectId: "paud-rohani",
            storageBucket: "paud-rohani.firebasestorage.app",
            messagingSenderId: "1051220684882",
            appId: "1:1051220684882:web:7d77a8367fa92b57c96f8b",
            measurementId: "G-SQHBZ6ST71"
            };

        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);

        const userSelect = document.getElementById('userSelect');
        const transactionForm = document.getElementById('transactionForm');
        const submitBtn = document.getElementById('submitBtn');
        const dateInput = document.getElementById('dateInput');
        let currentType = 'income';

        dateInput.valueAsDate = new Date();

        async function loadUsers() {
            try {
                const querySnapshot = await getDocs(collection(db, "users"));
                querySnapshot.forEach((doc) => {
                    const option = document.createElement('option');
                    option.value = doc.id;
                    option.text = doc.data().name;
                    userSelect.appendChild(option);
                });
            } catch (e) { console.error(e); }
        }
        loadUsers();

        transactionForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitBtn.disabled = true; submitBtn.innerText = "Processing...";
            
            try {
                await addDoc(collection(db, "transactions"), {
                    userId: userSelect.value,
                    userName: userSelect.options[userSelect.selectedIndex].text,
                    amount: parseInt(document.getElementById('amountInput').value),
                    date: dateInput.value,
                    note: document.getElementById('noteInput').value,
                    type: currentType,
                    createdAt: serverTimestamp()
                });
                alert(`Success!`);
                transactionForm.reset();
                dateInput.valueAsDate = new Date();
            } catch (error) { alert("Error: " + error.message); } 
            finally {
                submitBtn.disabled = false;
                submitBtn.innerText = currentType === 'income' ? "Add Income" : "Subtract Income";
            }
        });

        window.setTransactionType = (type) => {
            currentType = type;
            const btnIncome = document.getElementById('btnIncome');
            const btnExpense = document.getElementById('btnExpense');

            if (type === 'income') {
                btnIncome.className = 'toggle-btn active-income';
                btnExpense.className = 'toggle-btn';
                submitBtn.innerText = "Add Income";
                submitBtn.style.backgroundColor = "var(--success-green)";
            } else {
                btnIncome.className = 'toggle-btn';
                btnExpense.className = 'toggle-btn active-expense';
                submitBtn.innerText = "Subtract Income";
                submitBtn.style.backgroundColor = "var(--danger-red)";
            }
        }
    </script>
</body>
</html>