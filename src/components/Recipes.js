import React, { useContext } from "react";
import { UserContext } from "./context/UserProvider";

function Recipes(props) {
  const { userState } = useContext(UserContext);
  return (
    <div className="container text-center p-4">
      <h2>Recipes</h2>
      <p>{userState.userInfo.user_display_name}</p>
    </div>
  );
}

export default Recipes;
