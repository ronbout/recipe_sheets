import React from "react";

function Sidebar({ selectedItem, onSelect }) {
  const onLinkSelect = (menuItem) => {
    onSelect(menuItem);
  };

  const menuItems = ["Home", "Status by Month", "Recipes"];

  const menuItemLinks = menuItems.map((item, ndx) => {
    const classNames =
      "nav-link text-white " + (ndx === selectedItem ? " active" : "");
    return (
      <li className="nav-item" key={ndx}>
        <button className={classNames} onClick={() => onLinkSelect(ndx)}>
          {item}
        </button>
      </li>
    );
  });
  return (
    <section className="sidebar d-flex flex-column flex-shrink-0 p-3 text-white bg-dark">
      <ul className="nav nav-pills flex-column m-0">{menuItemLinks}</ul>
    </section>
  );
}

export default Sidebar;
