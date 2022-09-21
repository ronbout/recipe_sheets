import React, { useState } from "react";

import Dashboard from "./components/Dashboard";

const venueId = window?.tasteVenuePortal?.venueId
  ? window.tasteVenuePortal.venueId
  : 14876;
// console.log(window?.tasteVenuePortal);

window.euroLocale = Intl.NumberFormat("en-IE", { minimumFractionDigits: 2 });

function App() {
  const [venName, setVenName] = useState("");

  return (
    <div className="container">
      <Dashboard venueId={venueId} onVenLoad={setVenName} />
    </div>
  );
}

export default App;
