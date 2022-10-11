import React from "react";
import GroupedBarChart from "./charts/GroupedBarChart";
import MonthlyRequestsTable from "./tables/MonthlyRequestsTable";

function HomeDisplay({ distData }) {
  console.log("distData: ", distData);
  const loadedFlg = Object.keys(distData).length;
  const distTotals = loadedFlg
    ? distData.reduce((totals, distRow) => {
        const month = distRow.month;
        const recCnt = parseInt(distRow.recipe_count);
        const recAccepted = parseInt(distRow.recipes_accepted);
        const recCreated = parseInt(distRow.recipes_entered);
        const recPrinted = parseInt(distRow.recipes_printed);
        if (!totals.hasOwnProperty(month)) {
          totals[month] = {
            month,
            cnt: recCnt,
            accepted: recAccepted,
            created: recCreated,
            printed: recPrinted,
          };
        } else {
          totals[month].cnt += recCnt;
          totals[month].accepted += recAccepted;
          totals[month].created += recCreated;
          totals[month].printed += recPrinted;
        }
        return totals;
      }, {})
    : {};
  const monthlyTotalsArray = Object.values(distTotals);
  const labels = loadedFlg
    ? monthlyTotalsArray.map((monthInfo) => monthInfo.month)
    : [];
  const datasets = [
    {
      label: "Requested",
      data: loadedFlg
        ? monthlyTotalsArray.map((monthInfo) => monthInfo.cnt)
        : [],
      backgroundColor: "rgb(255, 99, 132)",
      stack: "Stack 0",
    },
    {
      label: "Accepted",
      data: loadedFlg
        ? monthlyTotalsArray.map((monthInfo) => monthInfo.accepted)
        : [],
      backgroundColor: "rgb(75, 192, 192)",
      stack: "Stack 1",
    },
    {
      label: "Created",
      data: loadedFlg
        ? monthlyTotalsArray.map((monthInfo) => monthInfo.created)
        : [],
      backgroundColor: "lightblue",
      stack: "Stack 2",
    },
    {
      label: "Printed",
      data: loadedFlg
        ? monthlyTotalsArray.map((monthInfo) => monthInfo.printed)
        : [],
      backgroundColor: "lightgreen",
      stack: "Stack 3",
    },
  ];

  console.log("totals: ", distTotals);

  return (
    <div className="container text-center p-4">
      {loadedFlg ? (
        distData.length ? (
          <React.Fragment>
            <h2>Recipe Requests</h2>
            <MonthlyRequestsTable monthlyData={monthlyTotalsArray} />
            <GroupedBarChart labels={labels} datasets={datasets} />
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

export default HomeDisplay;
