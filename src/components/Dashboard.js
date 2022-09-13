import React, { useState, useEffect } from "react";
import Products from "./Products";
import Payments from "./Payments";
import RelatedPosts from "./RelatedPosts";
import Jobs from "./Jobs";
import Sidebar from "./Sidebar";
import dataFetch from "../assets/js/dataFetch";

function Dashboard({ venueId, onVenLoad }) {
  const [section, setSection] = useState(-1);
  const [venueObj, setVenueObj] = useState({});

  useEffect(() => {
    const fetchVenue = async () => {
      const venApiObj = await dataFetch("venue", `venue-id=${venueId}`);
      console.log(venApiObj.net_payable);
      setVenueObj(venApiObj);
      setSection(0);
      onVenLoad(venApiObj.name);
    };
    fetchVenue();
  }, [venueId, onVenLoad]);

  const onSelectSection = (menuChoice) => {
    setSection(menuChoice);
  };

  const getMainDisplay = (sectionId, venueObj) => {
    console.log("ehre is venue obj: ", venueObj);
    console.log("section id : ", sectionId);
    switch (sectionId) {
      case 0:
        return <p>Main Content Home</p>;
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
