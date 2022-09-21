import React from "react";

function VenueSummaryCard({ numberDisplay, title, icon, iconClass }) {
  return (
    <div className="col-md d-flex flex-column px-4 py-3 venue-card">
      <div className="d-flex justify-content-between">
        <h3 className="numbers" id="vouchers-total">
          {numberDisplay}
        </h3>
        <div className={`eclipse_icon_bg ${iconClass}`}>{icon}</div>
      </div>
      <div className="row">
        <div className="col-10 text-start">
          <p className="titles">{title}</p>
        </div>
      </div>
    </div>
  );
}

export default VenueSummaryCard;
