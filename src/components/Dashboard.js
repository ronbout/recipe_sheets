import React, { useState, useEffect } from "react";
import HomeDisplay from "./HomeDisplay";
import Status from "./Status";
import Recipes from "./Recipes";
import Sidebar from "./Sidebar";
import Login from "./Login";
import { fetchDistRequests } from "../assets/js/dataFetch";

function Dashboard() {
  const [section, setSection] = useState(-1);
  const [requestsObj, setRequestsObj] = useState({});

  useEffect(() => {
    const getRequestsObj = async () => {
      const reqObj = await fetchDistRequests();
      setRequestsObj(reqObj);
      setSection(0);
    };
    getRequestsObj();
  }, []);

  const onSelectSection = (menuChoice) => {
    setSection(menuChoice);
  };

  const getMainDisplay = (sectionId) => {
    switch (sectionId) {
      case 0:
        return <HomeDisplay distData={requestsObj} />;
      case 1:
        return <Status distData={requestsObj} />;
      case 2:
        return <Recipes />;
      default:
        return <Login />;
    }
  };

  return (
    <div className="container row min-vh-100 bg-light mt-5">
      <div className="side-menu col-sm-2 bg-dark text-light">
        <Sidebar selectedItem={section} onSelect={onSelectSection} />
      </div>
      <div className="main-content col-sm-10">
        {-1 !== section ? (
          getMainDisplay(section)
        ) : (
          <h2>Loading Recipe Distributor data...</h2>
        )}
      </div>
    </div>
  );
}

export default Dashboard;
