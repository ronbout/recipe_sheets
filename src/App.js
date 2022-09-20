import React, { useState } from "react";

import Dashboard from "./components/Dashboard";

const venueId = 14829;

function App() {
  const [venName, setVenName] = useState("");

  return (
    <div className="container">
      {venName ? "" : <h2>Loading Venue data...</h2>}
      <Dashboard venueId={venueId} onVenLoad={setVenName} />
    </div>
  );
}

export default App;
