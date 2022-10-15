import React, { useState, useEffect, useRef, useContext } from "react";
import { fetchLogin } from "../assets/js/dataFetch";
import { UserContext } from "./context/UserProvider";

const Login = () => {
  const { userState, handleLogin } = useContext(UserContext);
  const usernameRef = useRef();
  const errRef = useRef();

  const [username, setUsername] = useState("");
  const [user, setUser] = useState({});
  const [loggedIn, setLoggedIn] = useState(userState.isLoggedIn);
  const [password, setPassword] = useState("");
  const [errMsg, setErrMsg] = useState("");

  useEffect(() => {
    usernameRef.current && usernameRef.current.focus();
  }, []);

  useEffect(() => {
    setErrMsg("");
  }, [username, password]);

  const handleLoginSubmit = async (e) => {
    e.preventDefault();
    console.log("submitted", username, password);
    const loginObj = await fetchLogin({ username, password });
    if (loginObj.hasOwnProperty("token")) {
      setUsername("");
      setPassword("");
      setLoggedIn(true);
      setUser(loginObj);
      handleLogin(loginObj);
    } else {
      setErrMsg("Invalid Login Credentials");
    }
    console.log(loginObj);
  };

  return (
    <>
      {loggedIn ? (
        <section className="mt-5 mx-auto w-50">
          <h1>You are logged in, {user.user_display_name}!</h1>
          <p>Role: {user.user_role}</p>
          <p>Email: {user.user_email}</p>
        </section>
      ) : (
        <section className="mt-5 mx-auto w-50">
          {errMsg && <p ref={errRef}>{errMsg}</p>}
          <h1>Sign In</h1>
          <form onSubmit={handleLoginSubmit}>
            <div className="mb-3">
              <label htmlFor="username" className="form-label">
                Username:
              </label>
              <input
                type="text"
                className="form-control"
                ref={usernameRef}
                id="username"
                onChange={(e) => setUsername(e.target.value)}
                value={username}
                required
              />
            </div>

            <div className="mb-3">
              <label htmlFor="password" className="form-label">
                Password:
              </label>
              <input
                type="password"
                className="form-control"
                id="password"
                autoComplete="new-password"
                onChange={(e) => setPassword(e.target.value)}
                value={password}
                required
              />
            </div>
            <button className="btn btn-primary">Sign In</button>
          </form>
        </section>
      )}
    </>
  );
};

export default Login;
