import React from "react";

import Dashboard from "./components/Dashboard";

const venueId = window?.tasteVenuePortal?.venueId
  ? window.tasteVenuePortal.venueId
  : 14829;
// console.log(window?.tasteVenuePortal);

window.euroLocale = Intl.NumberFormat("en-IE", { minimumFractionDigits: 2 });

function App() {
  return (
    <div className="container">
      <Dashboard venueId={venueId} />
    </div>
  );
}

export default App;
