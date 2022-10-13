import React, { createContext } from "react";

export const UserContext = createContext({});

const UserProvider = ({ userState, handleLogin, handleLogout, children }) => {
  return (
    <UserContext.Provider value={{ userState, handleLogin, handleLogout }}>
      {children}
    </UserContext.Provider>
  );
};

export default UserProvider;
