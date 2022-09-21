import React from "react";
import LineChart from "./charts/LineChart";
import PaymentTable from "./tables/PaymentTable";

function Payments({ venueObj }) {
  const loadedFlg = Object.keys(venueObj).length;
  const productData = loadedFlg ? venueObj.prod_calcs : [];
  // filter out historical and pending payments
  const paymentData = loadedFlg
    ? venueObj.payments.filter((payInfo) => "1" === payInfo.status)
    : [];

  const labels = loadedFlg
    ? paymentData.map((payInfo) => payInfo.timestamp.split(" ")[0])
    : [];
  let tmpPayTotal = 0;
  const datasets = loadedFlg
    ? [
        {
          label: "Total Payments",
          data: paymentData.map((payInfo) => {
            tmpPayTotal += parseFloat(payInfo.total_amount);
            // console.log(tmpPayTotal);
            return tmpPayTotal;
            // return payInfo.total_amount;
          }),
        },
      ]
    : [];

  return (
    <div className="container text-center p-4">
      {loadedFlg ? (
        paymentData.length ? (
          <React.Fragment>
            <h2>Payments</h2>
            <LineChart labels={labels} datasets={datasets} />
            <PaymentTable paymentData={paymentData} productData={productData} />
          </React.Fragment>
        ) : (
          <h2>No Payments Available</h2>
        )
      ) : (
        ""
      )}
    </div>
  );
}

export default Payments;
