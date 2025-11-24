<?php $page = 'users'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAUD Finance - User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="header-section">
        <div class="page-title-wrapper" style="display:flex; align-items:center;">
            <button class="mobile-toggle-btn" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="page-title">
                <h1>User Management</h1>
            </div>
        </div>
        
        <!-- Tombol Add User tetap di sini, tapi di HP akan turun ke bawah karena flex-direction: column di CSS -->
        <button class="btn-add" onclick="openModal()">
            <i class="fa-solid fa-user-plus"></i> Add User
        </button>
    </div>

        <div class="user-grid" id="userGrid">
            <p style="color:var(--text-gray)">Loading users...</p>
        </div>
    </main>

    <div class="modal-overlay" id="addUserModal">
        <div class="modal-box">
            <div class="modal-header">
                <div>
                    <h3>Add New User</h3>
                    <p>Enter the name of the user you want to add.</p>
                </div>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form id="addUserForm">
                <div class="modal-body">
                    <label>User Name *</label>
                    <input type="text" id="userNameInput" placeholder="Enter full name" required autocomplete="off">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- <div class="footer-badge">
        <i class="fa-solid fa-bolt"></i> Made with Emergent
    </div> -->

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getFirestore, collection, addDoc, onSnapshot, deleteDoc, doc, serverTimestamp, orderBy, query } 
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
        const usersCollection = collection(db, "users");

        const modal = document.getElementById('addUserModal');
        const userGrid = document.getElementById('userGrid');
        const addForm = document.getElementById('addUserForm');
        const nameInput = document.getElementById('userNameInput');

        window.openModal = () => { modal.classList.add('active'); nameInput.focus(); }
        window.closeModal = () => { modal.classList.remove('active'); addForm.reset(); }

        addForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                await addDoc(usersCollection, { name: nameInput.value, createdAt: serverTimestamp() });
                closeModal();
            } catch (error) { alert("Error: " + error.message); }
        });

        const q = query(usersCollection, orderBy("createdAt", "desc"));
        onSnapshot(q, (snapshot) => {
            userGrid.innerHTML = "";
            if(snapshot.empty) { userGrid.innerHTML = "<p>No users found.</p>"; return; }
            snapshot.forEach((doc) => {
                const user = doc.data();
                const initials = user.name ? user.name.substring(0, 2).toUpperCase() : "U";
                const dateStr = user.createdAt ? new Date(user.createdAt.seconds * 1000).toLocaleDateString() : "-";
                
                userGrid.innerHTML += `
                    <div class="user-card">
                        <div class="user-info-wrapper">
                            <div class="avatar blue">${initials}</div>
                            <div class="info"><h3>${user.name}</h3><p>Added ${dateStr}</p></div>
                        </div>
                        <button class="btn-delete" onclick="deleteUser('${doc.id}')"><i class="fa-regular fa-trash-can"></i></button>
                    </div>`;
            });
        });

        window.deleteUser = async (id) => {
            if(confirm("Delete user?")) { await deleteDoc(doc(db, "users", id)); }
        }
        
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    </script>
</body>
</html>