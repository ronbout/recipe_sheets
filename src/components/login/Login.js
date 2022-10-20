import React, { useState, useEffect, useRef, useContext } from "react";
import { fetchLogin } from "../../assets/js/dataFetch";
import { UserContext } from "../context/UserProvider";

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
        <section className="login-form">
          {errMsg && <p ref={errRef}>{errMsg}</p>}
          <div className="login-title-container">
            <h1>Recipe Exec</h1>
            <p>
              Lorem ipsum dolor sit amet consectetur adipisicing elit.
              Necessitatibus placeat ducimus error id in, culpa ut accusantium
            </p>
          </div>
          <form onSubmit={handleLoginSubmit}>
            <div className="mb-3">
              <label htmlFor="username">Email</label>
              <input
                type="text"
                className="form-control"
                ref={usernameRef}
                id="username"
                onChange={(e) => setUsername(e.target.value)}
                value={username}
                placeholder="Email"
                required
              />
            </div>

            <div className="mb-5">
              <label htmlFor="password">Password:</label>
              <input
                type="password"
                className="form-control"
                id="password"
                autoComplete="new-password"
                onChange={(e) => setPassword(e.target.value)}
                value={password}
                placeholder="password"
                required
              />
            </div>
            <button type="submit">
              <span>Sign In</span>
            </button>
          </form>
        </section>
      )}
    </>
  );
};

export default Login;
