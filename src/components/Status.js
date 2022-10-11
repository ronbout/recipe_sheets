import React from "react";
import RequestsTable from "./tables/RequestsTable";

function Status({ distData }) {
  console.log(distData);
  const loadedFlg = Object.keys(distData).length;
  return (
    <div className="container text-center p-4">
      {loadedFlg ? (
        distData.length ? (
          <React.Fragment>
            <h2>Recipe Requests</h2>
            <RequestsTable requestsData={distData} />
          </React.Fragment>
        ) : (
          <h2>No Recipe Requests Available</h2>
        )
      ) : (
        ""
      )}
    </div>
  );
}

export default Status;
