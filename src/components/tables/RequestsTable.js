import React, { useMemo } from "react";
import { useTable, useSortBy, useGlobalFilter } from "react-table";

function RequestsTable({ requestsData }) {
  const data = useMemo(() => requestsData, [requestsData]);
  const columns = useMemo(
    () => [
      {
        Header: "Request Id",
        accessor: "id",
      },
      {
        Header: "Month",
        accessor: "month",
      },
      {
        Header: "Cuisine",
        accessor: "cuisine",
      },
      {
        Header: "Classification",
        accessor: "classification",
      },
      {
        Header: "Dietary",
        accessor: "dietary",
      },
      {
        Header: "Equipment",
        accessor: "equipment",
      },
      {
        Header: "Meal Type",
        accessor: "meal_type",
      },
      {
        Header: "Notes",
        accessor: "notes",
      },
      {
        Header: "Recipe Count",
        accessor: "recipe_count",
      },
      {
        Header: "Recipes Accepted",
        accessor: "recipes_accepted",
      },
      {
        Header: "Recipes Created",
        accessor: "recipes_entered",
      },
      {
        Header: "Recipes Printed",
        accessor: "recipes_printed",
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

export default RequestsTable;
