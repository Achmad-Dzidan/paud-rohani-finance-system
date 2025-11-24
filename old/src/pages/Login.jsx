import React, { useState } from "react";
import { auth, signInWithEmailAndPassword } from "../firebase";

export default function Login({ onLogin }) {
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const EMAIL = "admin@paud.com"; // akun admin di Firebase
  const handleLogin = async () => {
    try {
      await signInWithEmailAndPassword(auth, EMAIL, password);
      onLogin(); // callback
    } catch (err) {
      setError("Incorrect password");
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-b from-[#f3f8ff] to-white px-4">
      <div className="bg-white w-full max-w-md p-8 rounded-2xl shadow-lg border border-gray-100">
        
        {/* ICON */}
        <div className="w-full flex justify-center mb-4">
          <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
            <svg width="32" height="32" fill="#1e4ed8" viewBox="0 0 24 24">
              <path d="M12 2L2 7v2h2v10h6V15h4v4h6V9h2V7L12 2z" />
            </svg>
          </div>
        </div>

        <h2 className="text-2xl font-semibold text-center">PAUD Finance System</h2>
        <p className="text-center text-gray-500 text-sm mb-6">
          School Administration Portal
        </p>

        {/* PASSWORD FIELD */}
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Admin Password
        </label>
        <input
          type="password"
          placeholder="Enter admin password"
          className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 mb-4"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />

        {/* ERROR */}
        {error && (
          <div className="text-red-500 text-sm mb-3 text-center">
            {error}
          </div>
        )}

        {/* LOGIN BUTTON */}
        <button
          onClick={handleLogin}
          className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition"
        >
          Login
        </button>

        {/* FOOTER */}
        <div className="mt-4 text-center text-sm bg-gray-100 p-2 rounded-lg text-gray-600">
          Default credentials: <span className="font-semibold">admin123</span>
        </div>
      </div>
    </div>
  );
}
