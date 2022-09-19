import React from "react";
import BarChart from "./charts/BarChart";
import ProductTable from "./tables/ProductTable";

function Products({ venueObj }) {
  const loadedFlg = Object.keys(venueObj).length;
  const productData = loadedFlg ? venueObj.prod_calcs : [];
  const labels = loadedFlg
    ? productData.map((prodInfo) => prodInfo.product_id)
    : [];
  const datasets = [
    {
      label: "Net Payable",
      data: loadedFlg
        ? productData.map((prodInfo) => prodInfo.net_payable)
        : [],
    },
  ];

  return (
    <div className="container text-center p-4">
      <h2>Products</h2>
      {loadedFlg ? (
        <React.Fragment>
          <BarChart labels={labels} datasets={datasets} />
          <ProductTable productData={productData} />
        </React.Fragment>
      ) : (
        ""
      )}
    </div>
  );
}

export default Products;
