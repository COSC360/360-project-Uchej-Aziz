<?php 
	$urlSecurity = $_SERVER['REQUEST_URI'];
	$urlSecurity = substr($urlSecurity, strpos($urlSecurity, ".") + 1);
	if ($urlSecurity === "php")
		header("Location: /");
?>
<main class="login-page" style="width: 75%">
  <section class="row">
    <section class="col-md-6 bg-white">
      <section class="p-4 py-3 mt-5 mx-auto w-100">
        <a href="/" class="d-flex align-items-center trademark me-3 fs-2 mb-5">
          <i class="bi bi-globe-europe-africa"></i
          ><span class="ms-2">JoLa</span>
        </a>

        <form method="POST" class="login-form">
          <section class="mb-4">
            <label for="email-input" class="text-uppercase form-tag"
              >Email Address</label
            >
            <input
              class="login-email-input"
              id="email-input"
              placeholder="aziz@gmail.com"
              type="email"
              required
            />
          </section>
          <section class="mb-4">
            <label for="password-input" class="text-uppercase form-tag"
              >Password</label
            >
            <input
              class="login-password-input"
              id="password-input"
              placeholder="Aziz's Password"
              type="password"
              required
            />
          </section>
          <section
            class="mt-5 justify-content-between d-flex btn-box text-uppercase"
          >
            <button type="submit" class="login-btn">Login</button>
            <a href="/register" class="text-capitalize">Sign Up</a>
          </section>

          <p class="mt-4 login-box-assistance-recover text-center mb-4">
            Forgot your Password?
            <a href="/restore">Sort it Out!</a>
          </p>

          <section
            class="mt-3 px-3 transition-text py-2 bg-danger w-100 scheme-report d-inline-flex d-none"
          >
            <section class="align-self-center">
              <i class="bi bi-radioactive"></i>
            </section>
            <section class="ms-3 mt-1 align-self-center" style="color: #495057">
              <h5>Me Oh My...</h5>
              <p></p>
            </section>
          </section>
        </form>
      </section>
    </section>

    <section class="bg-white d-xl-block d-md-block col-md-6 d-none">
      <section class="text-light position-relative">
        <section class="login-detail-bg-image"></section>
        <section
          class="position-absolute translate-middle transition-text top-50 login-detail-box start-50"
        >
          <h5 class="fw-light mt-5">JoLa Forum</h5>
          <h2 class="fw-bolder text-uppercase mt-3 mb-3">
            Welcomes You Back 😄
          </h2>
          <hr />
          <p class="mt-3">It's Good To See You Again 🫂</p>
        </section>
      </section>
    </section>
  </section>
</main>
