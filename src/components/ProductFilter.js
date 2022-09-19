import React from "react";

function ProductFilter({ products, selectProd = 0, handleSelectProd }) {
  const sortedProducts = products
    .filter((prod) => prod.paid_amount > 0)
    .sort((prodA, prodB) => {
      return prodA.product_id < prodB.product_id ? -1 : 1;
    });
  const prodOptions = sortedProducts.map((prodInfo) => {
    const prodId = prodInfo.product_id;
    const prodTitle = prodInfo.title.substring(0, 80);
    return (
      <option key={prodId} value={prodId}>{`${prodId}: ${prodTitle}`}</option>
    );
  });

  return (
    <select value={selectProd} onChange={handleSelectProd}>
      <option value="0">View All Products</option>
      {prodOptions}
    </select>
  );
}

export default ProductFilter;
