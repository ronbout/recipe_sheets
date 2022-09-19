import React, { useMemo } from "react";
import { useTable, useSortBy, useGlobalFilter } from "react-table";

function ProductTable({ productData }) {
  const data = useMemo(() => productData, [productData]);
  const columns = useMemo(
    () => [
      {
        Header: "Product Id",
        accessor: "product_id",
      },
      {
        Header: "Title",
        Cell: ({ value }) => (
          <span title={value}>{value.substring(0, 40) + "..."}</span>
        ),
        accessor: "title",
        disableSortBy: true,
      },
      {
        Header: "Price",
        accessor: "price",
      },
      {
        Header: "Redeemed Qty",
        accessor: "redeemed_qty",
      },
      {
        Header: "Revenue",
        accessor: "revenue",
      },
      {
        Header: "Commission",
        accessor: "commission",
      },
      {
        Header: "VAT",
        accessor: "vat",
      },
      {
        Header: "Net Payable",
        accessor: "net_payable",
      },
      {
        Header: "Amount Paid",
        accessor: "paid_amount",
      },
      {
        Header: "Balance Due",
        accessor: "balance_due",
      },
    ],
    []
  );

  const tableInstance = useTable({ data, columns }, useGlobalFilter, useSortBy);

  const {
    getTableProps,
    getTableBodyProps,
    headerGroups,
    rows,
    prepareRow,
    state,
    setGlobalFilter,
  } = tableInstance;

  const { globalFilter } = state;

  return (
    <div className="container mt-5">
      <div className="row">
        <div className="col-sm-6"></div>
        <div className="col-sm-6 row">
          <label
            htmlFor="product-table-search-input"
            className="col-sm-2 col-form-label"
          >
            Search:{" "}
          </label>
          <div className="col-sm-8">
            <input
              id="product-table-search-input"
              type="text"
              className="form-control"
              value={globalFilter || ""}
              onChange={(e) => setGlobalFilter(e.target.value)}
            />
          </div>
        </div>
      </div>
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

export default ProductTable;
