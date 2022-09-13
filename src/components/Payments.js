import React from "react";
import LineChart from "./charts/LineChart";

function Payments({ venueObj }) {
  const paymentData = venueObj.payments;
  /**
   *
   * sort paymentDate by timestamp
   *
   *
   */
  const labels = paymentData.map((payInfo) => payInfo.timestamp.split(" ")[0]);
  let tmpPayTotal = 0;
  const datasets = [
    {
      label: "Total Payments",
      data: paymentData.map((payInfo) => {
        tmpPayTotal += parseFloat(payInfo.total_amount);
        // console.log(tmpPayTotal);
        return tmpPayTotal;
        // return payInfo.total_amount;
      }),
    },
  ];
  return (
    <div className="container text-center p-4">
      <h2>Payments</h2>
      <LineChart labels={labels} datasets={datasets} />
    </div>
  );
}

export default Payments;
