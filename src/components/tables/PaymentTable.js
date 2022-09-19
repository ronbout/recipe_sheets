import React, { useMemo, useState } from "react";
import { useTable, useSortBy } from "react-table";
import ProductFilter from "../ProductFilter";

function PaymentTable({ paymentData, productData }) {
  const [selectedProdId, setSelectedProdId] = useState("0");
  const data = useMemo(() => {
    return "0" !== selectedProdId
      ? paymentData.filter((payInfo) => selectedProdId === payInfo.product_id)
      : paymentData;
  }, [paymentData, selectedProdId]);

  const columns = useMemo(
    () => [
      {
        Header: "Payment Id",
        accessor: "id",
        disableSortBy: true,
      },
      {
        Header: "Product Id",
        accessor: "product_id",
        enableSorting: true,
      },
      {
        Header: "Product Amount",
        accessor: "amount",
        sortDescFirst: true,
      },
      {
        Header: "Total Payment Amount",
        accessor: "total_amount",
        sortDescFirst: true,
      },
      {
        Header: "Date and Time",
        accessor: "timestamp",
        sortDescFirst: true,
      },
    ],
    []
  );

  const tableInstance = useTable({ data, columns }, useSortBy);

  const { getTableProps, getTableBodyProps, headerGroups, rows, prepareRow } =
    tableInstance;

  const handleSelectProd = (e) => {
    const prodId = e.target.value;
    setSelectedProdId(prodId);
    console.log(prodId);
  };

  return (
    <div className="container mt-5">
      <ProductFilter
        products={productData}
        selectProd={selectedProdId}
        handleSelectProd={handleSelectProd}
      />
      <table
        className="table table-striped table-success mt-4"
        {...getTableProps()}
      >
        <thead>
          {headerGroups.map((headerGroup) => {
            return (
              <tr {...headerGroup.getHeaderGroupProps()}>
                {headerGroup.headers.map((column) => {
                  return (
                    <th
                      className={
                        column.canSort && !column.isSorted
                          ? "col-sortable-hover"
                          : ""
                      }
                      scope="col"
                      {...column.getHeaderProps(column.getSortByToggleProps())}
                    >
                      {column.render("Header")}
                      <span>
                        {column.isSorted ? (
                          column.isSortedDesc ? (
                            " 🔽"
                          ) : (
                            " 🔼"
                          )
                        ) : (
                          <span className="hover-sort-indicator">
                            {column.sortDescFirst ? " 🔽" : " 🔼"}
                          </span>
                        )}
                      </span>
                    </th>
                  );
                })}
              </tr>
            );
          })}
        </thead>
        <tbody {...getTableBodyProps()}>
          {rows.map((row) => {
            prepareRow(row);
            return (
              <tr {...row.getRowProps()}>
                {row.cells.map((cell) => {
                  return (
                    <td {...cell.getCellProps()}>{cell.render("Cell")}</td>
                  );
                })}
              </tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
}

export default PaymentTable;
