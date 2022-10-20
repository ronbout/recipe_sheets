import React, { useState, useEffect } from "react";
import UserProvider from "./components/context/UserProvider";
import Dashboard from "./components/Dashboard";
import LoginPage from "./components/login/LoginPage";

function App() {
  const [userState, setUserState] = useState({
    isLoggedIn: false,
    userInfo: {},
  });

  useEffect(() => {
    const storedUser = localStorage.getItem("userInfo");
    storedUser &&
      setUserState({ isLoggedIn: true, userInfo: JSON.parse(storedUser) });
  }, []);

  const handleLogin = (userInfo) => {
    setUserState({ isLoggedIn: true, userInfo });
    localStorage.setItem("userInfo", JSON.stringify(userInfo));
  };

  const handleLogout = () => {
    setUserState({ isLoggedIn: false, userInfo: {} });
    localStorage.removeItem("userInfo");
  };

  return (
    <UserProvider
      userState={userState}
      handleLogin={handleLogin}
      handleLogout={handleLogout}
    >
      <div className="container">
        {userState.isLoggedIn ? (
          <>
            <h3>{userState.userInfo.user_display_name}</h3>
            <Dashboard />
          </>
        ) : (
          <LoginPage />
        )}
      </div>
    </UserProvider>
  );
}

export default App;
