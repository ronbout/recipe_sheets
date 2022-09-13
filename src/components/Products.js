import React from "react";
import BarChart from "./charts/BarChart";

function Products({ venueObj }) {
  const productData = venueObj.prod_calcs;
  const labels = productData.map((prodInfo) => prodInfo.product_id);
  const datasets = [
    {
      label: "Net Payable",
      data: productData.map((prodInfo) => prodInfo.net_payable),
    },
  ];

  return (
    <div className="container text-center p-4">
      <h2>Products</h2>
      <BarChart labels={labels} datasets={datasets} />
    </div>
  );
}

export default Products;
