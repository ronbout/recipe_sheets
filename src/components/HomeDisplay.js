import React from "react";
import VenueSummary from "./VenueSummary";

function HomeDisplay({ venueObj }) {
  return (
    <div className="container text-center p-4">
      <h2>Venue Summary</h2>
      <VenueSummary venueObj={venueObj} />
    </div>
  );
}

export default HomeDisplay;
