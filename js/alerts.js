// GLOBAL FUNCTION: SHOW ALERT WITH STACK + AUTO REMOVE
window.showAlert = function (message, type = "success") {

    const container = document.getElementById("global-alert-container");
    if (!container) return;

    const alert = document.createElement("div");
    alert.className = "alert-box";
    alert.style.backgroundColor = type === "success" ? "#007F00" : "#B00020";

    alert.innerHTML = `
        <span>✔</span>
        <span>${message}</span>
    `;

    container.appendChild(alert);

    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.style.animation = "slideOut 0.3s forwards";
        setTimeout(() => alert.remove(), 300);
    }, 5000);
};

// Save alert so it appears after page redirect
window.queueAlert = function (message, type = "success") {
    localStorage.setItem("pendingAlert", JSON.stringify({ message, type }));
};

// Load alert if exists
window.loadPendingAlert = function () {
    const stored = localStorage.getItem("pendingAlert");
    if (!stored) return;

    const { message, type } = JSON.parse(stored);
    showAlert(message, type);

    localStorage.removeItem("pendingAlert");
};
