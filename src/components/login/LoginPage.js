import Login from "./Login";
import "./login.scss";

const LoginPage = () => {
  return (
    <main className="login-page">
      <section className="login-image"></section>
      <section className="login-form-container">
        <Login />
      </section>
    </main>
  );
};

export default LoginPage;
