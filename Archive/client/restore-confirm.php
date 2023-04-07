<?php 
	$urlSecurity = $_SERVER['REQUEST_URI'];
	$urlSecurity = substr($urlSecurity, strpos($urlSecurity, ".") + 1);
	if ($urlSecurity === "php")
		header("Location: /");
?>
<main class="restore-page" style="width: 75%">
  <section class="row">
    <section class="col-md-6 bg-white">
      <section class="p-4 py-3 mt-5 mx-auto w-100">
        <a href="/" class="d-flex trademark align-items-center me-3 fs-2 mb-5">
          <i class="bi bi-globe-europe-africa"></i
          ><span class="ms-2">JoLa</span>
        </a>

        <h4 class="mb-5">Your Account is Just Round the Corner</h4>

        <form method="POST">
          <section class="mb-4 intake">
            <label for="code-intake" class="form-tag text-uppercase"
              >New Password</label
            >
            <input
              class="new-password-input"
              id="code-intake"
              type="password"
              placeholder="Fill In Here ✎"
              required
            />
          </section>
          <section class="mb-4 intake">
            <label for="password-intake" class="form-tag text-uppercase"
              >Confirm Your New Password</label
            >
            <input
              class="new-password-confirm-input"
              id="password-intake"
              type="password"
              placeholder="Fill In Here ✎"
              required
            />
          </section>
          <section
            class="btn-box d-flex text-uppercase justify-content-between mt-5 mb-4 w-50"
          >
            <button type="submit" class="restore-affirm-ultimate">
              Revive
            </button>
          </section>
        </form>

        <section
          class="scheme-report d-none mt-3 bg-danger d-inline-flex px-3 py-1 transition-text mb-4 w-100"
        >
          <section class="align-self-center">
            <i class="bi bi-radioactive"></i>
          </section>
          <section class="ms-3 mt-1 align-self-center">
            <h5>Was That A Glitch? Try Again...</h5>
            <p></p>
          </section>
        </section>
      </section>
    </section>
    <section class="col-md-6 d-none d-md-block d-xl-block bg-white">
      <section class="restore-detail-box text-light position-relative">
        <section class="restore-affirm-detail-image"></section>
        <section
          class="position-absolute top-50 start-50 translate-middle transition-text"
        >
          <h5 class="mt-5 fw-light">JoLa Forum</h5>
          <h2 class="text-uppercase mt-3 fw-bolder mb-3">
            Welcomes You Back 😄
          </h2>
          <hr />
          <p class="mt-3">It\'s Good To Have You Back 🫂</p>
        </section>
      </section>
    </section>
  </section>
</main>
