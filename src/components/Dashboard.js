import React, { useState, useEffect } from "react";
import HomeDisplay from "./HomeDisplay";
import Products from "./Products";
import Payments from "./Payments";
import RelatedPosts from "./RelatedPosts";
import Jobs from "./Jobs";
import Sidebar from "./Sidebar";
import { fetchVenue } from "../assets/js/dataFetch";

function Dashboard({ venueId, onVenLoad }) {
  const [section, setSection] = useState(-1);
  const [venueObj, setVenueObj] = useState({});

  useEffect(() => {
    const getVenueObj = async () => {
      const venApiObj = await fetchVenue(venueId);
      setVenueObj(venApiObj);
      setSection(0);
      onVenLoad(venApiObj.name);
    };
    getVenueObj();
  }, [venueId, onVenLoad]);

  const onSelectSection = (menuChoice) => {
    setSection(menuChoice);
  };

  const getMainDisplay = (sectionId, venueObj) => {
    switch (sectionId) {
      case 0:
        return <HomeDisplay venueObj={venueObj} />;
      case 1:
        return <Products venueObj={venueObj} />;
      case 2:
        return <Payments venueObj={venueObj} />;
      case 3:
        return <RelatedPosts venueObj={venueObj} />;
      case 4:
        return <Jobs venueObj={venueObj} />;
      default:
        return <h2>Under Construction</h2>;
    }
  };

  return (
    <div className="container row min-vh-100 bg-light mt-5">
      <div className="side-menu col-sm-2 bg-dark text-light">
        <Sidebar selectedItem={section} onSelect={onSelectSection} />
      </div>
      <div className="main-content col-sm-10">
        {-1 !== section && getMainDisplay(section, venueObj)}
      </div>
    </div>
  );
}

export default Dashboard;
