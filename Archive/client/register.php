<?php 
	$urlSecurity = $_SERVER['REQUEST_URI'];
	$urlSecurity = substr($urlSecurity, strpos($urlSecurity, ".") + 1);
	if ($urlSecurity === "php")
		header("Location: /");
?>
<main class="register-page" style="width: 75%">
  <section class="row">
    <section class="col-md-6 bg-white">
      <section class="p-4 py-3 mt-5 mx-auto w-100">
        <a href="/" class="d-flex align-items-center trademark me-3 fs-2 mb-5">
          <i class="bi bi-globe-europe-africa"></i
          ><span class="ms-2">JoLa</span>
        </a>

        <h4 class="mb-5">Your Quest Is Just Beginning... 💫</h4>

        <form>
          <section class="mb-4 intake">
            <label for="user-name-input" class="text-uppercase form-tag"
              >Username</label
            >
            <input
              class="username-roster-intake"
              id="user-name-input"
              placeholder="Fill in Here ✎"
              type="text"
              required
            />
          </section>
          <section
            class="d-flex text-uppercase btn-box justify-content-between mt-5 mb-4"
          >
            <button type="submit" class="first-phase move-backwards">
              Previous Step
            </button>
            <button type="submit" class="move-forward first-phase ">
              Next Step
            </button>
          </section>
        </form>

        <section
          class="d-inline-flex scheme-report mt-1 routine-info px-3 py-1 transition-text mb-2 w-100"
        >
          <section class="align-self-center">
            <i class="bi bi-info-square-fill"></i>
          </section>
          <section class="ms-3 mt-1 align-self-center">
            <h5>Username Criteria</h5>
            <p>
              Please ensure your username is free of any special characters and
              does not go beyond 8 characters.
            </p>
          </section>
        </section>

        <section
          class="scheme-report d-inline-flex bg-danger px-3 py-2 transition-text w-100 d-none"
        >
          <section class="align-self-center">
            <i class="bi bi-radioactive"></i>
          </section>
          <section class="ms-3 mt-1 align-self-center" style="color: #495057">
            <h5>Me Oh My...</h5>
            <p></p>
          </section>
        </section>
      </section>
    </section>
    <section class="col-md-6 d-none d-md-block d-xl-block bg-white">
      <section class="position-relative roster-detail-box text-light">
        <section class="roster-detail-image"></section>
        <section
          class="top-50 login-detail-box transition-text start-50 position-absolute translate-middle"
        >
          <h5 class="fw-light mt-5">JoLa Forum</h5>
          <h2 class="fw-bolder text-uppercase mt-3 mb-3">Welcomes You 😄</h2>
          <hr />
          <p class="mt-3">This Is Your New Home... 🏠</p>
        </section>
      </section>
    </section>
  </section>
</main>
