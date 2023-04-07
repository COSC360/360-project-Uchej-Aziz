<?php 
	$url = $_SERVER['REQUEST_URI'];
	$url = substr($url, strpos($url, ".") + 1);
	if ($url === "php")
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

        <h4 class="mb-5">Your Membership is Just Round the Corner</h4>

        <form method="POST">
          <section class="mb-4 intake">
            <label for="code-intake" class="text-uppercase form-tag"
              >Confirmation Code</label
            >
            <input
              class="code-roster-intake-affirm"
              id="code-intake"
              type="number"
              placeholder="#999#"
              min="999"
              max="10000091"
              required
            />
          </section>
          <section
            class="text-uppercase btn-box d-flex justify-content-between mt-5 mb-4 w-50"
          >
            <button type="submit" class="roster-affirm-ultimate">Verify</button>
          </section>
        </form>

        <section
          class="d-none mt-3 bg-danger d-inline-flex scheme-report px-3 py-1 transition-text mb-4 w-100"
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
    <section class="bg-white d-none d-xl-block col-md-6 d-md-block">
      <section class="text-light position-relative roster-detail-box">
        <section class="roster-affirm-detail-image"></section>
        <section
          class="login-detail-box transition-text top-50 start-50 translate-middle position-absolute"
        >
          <h5 class="fw-light mt-5">JoLa Forum</h5>
          <h2 class="fw-bolder text-uppercase mt-3 mb-3">Welcomes You 😄</h2>
          <hr />
          <p class="mt-3">We Know You\'ll Love It...</p>
        </section>
      </section>
    </section>
  </section>
</main>
