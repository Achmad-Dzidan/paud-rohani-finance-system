import React, { useState } from "react";
import Login from "./pages/Login";

export default function App() {
  const [loggedIn, setLoggedIn] = useState(false);

  if (!loggedIn) return <Login onLogin={() => setLoggedIn(true)} />;

  return (
    <div>
      {/* Dashboard nanti dibuat */}
      <h1 className="text-3xl p-6">Dashboard</h1>
    </div>
  );
}
