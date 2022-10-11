import React, { useMemo } from "react";
import { useTable, useSortBy } from "react-table";

function MonthlyRequestsTable({ monthlyData }) {
  const data = useMemo(() => monthlyData, [monthlyData]);
  const columns = useMemo(
    () => [
      {
        Header: "Month",
        accessor: "month",
      },
      {
        Header: "Recipes Requested",
        accessor: "cnt",
      },
      {
        Header: "Recipes Accepted",
        accessor: "accepted",
      },
      {
        Header: "Recipes Created",
        accessor: "created",
      },
      {
        Header: "Recipes Printed",
        accessor: "printed",
      },
    ],
    []
  );

  const tableInstance = useTable({ data, columns }, useSortBy);

  const { getTableProps, getTableBodyProps, headerGroups, rows, prepareRow } =
    tableInstance;

  return (
    <div className="container mt-5">
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

export default MonthlyRequestsTable;
