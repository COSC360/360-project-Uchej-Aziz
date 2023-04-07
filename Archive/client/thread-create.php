<?php 
	$urlSecurity = $_SERVER['REQUEST_URI'];
	$urlSecurity = substr($urlSecurity, strpos($urlSecurity, ".") + 1);
	if ($urlSecurity === "php")
		header("Location: /");
?>

<main
  style="width: 75%; margin-top: 3rem; margin-left: auto; margin-right: auto"
>
  <section class="row">
    <section class="col-md-3 agenda">
      <h6 class="mt-4 text-uppercase">Panel 🛠️</h6>
      <nav class="mt-3">
        <ul>
          <li>
            <a href="/" class="rounded">
              <i class="bi bi-house-door"></i>
              <span class="ms-2">Home</span>
            </a>
          </li>

          <?php if (isset($_SESSION['IS_AUTHORIZED']) && isset($_SESSION['IS_ADMIN']) && $_SESSION['IS_ADMIN']) { ?>
          <li>
            <a href="/admin" class="rounded"
              ><i class="bi bi-bank2"></i
              ><span class="ms-2">Mgmt Portal</span></a
            >
          </li>
          <?php } ?>

          <li>
            <a href="/search" class="rounded effective"
              ><i class="bi bi-cloud-plus-fill"></i
              ><span class="ms-2">Threads</span></a
            >
          </li>
          <li>
            <a href="/search" class="rounded"
              ><i class="bi bi-cloud-fog2"></i
              ><span class="ms-2">My Threads</span></a
            >
          </li>
          <li>
            <a href="/search" class="rounded"
              ><i class="bi bi-blockquote-left"></i
              ><span class="ms-2">Responses</span></a
            >
          </li>
        </ul>
      </nav>
    </section>
    <section
      class="generate-thread-large-box overflow-auto col-md-9 mx-auto mb-4 p-4"
    >
      <h2>Start A Thread</h2>

      <section class="d-none scheme-report bg-danger mb-3 mt-4">
        <section class="d-inline-flex scheme-report-data px-3 py-3 w-100">
          <i class="bi bi-bug-fill text-center my-auto text-light"></i>
          <p class="ms-3 my-auto">
            Your thread couldn't be created. <br /><span class="fw-bolder"
              >This is because: </span
            ><span class="glitch-report"></span>
          </p>
        </section>
      </section>

      <form enctype="multipart/form-data">
        <section class="mt-4">
          <section class="explanation">
            <h5 class="fw-bold">Thread Name</h5>
            <p>
              Enter your
              <span class="fw-bolder text-decoration-underline">unique</span>
              thread Title
            </p>
          </section>
          <section class="generate-thread-data rounded bg-white p-4">
            <label for="create-the-title" style="display: block"
              >Thread Name:</label
            >
            <section class="d-inline-flex align-items-center mt-2 w-100">
              <input
                name="title"
                type="text"
                placeholder="Fill In Here ✎"
                class="p-1 generate-thread-banner"
                id="create-the-title"
                required
              />
              <span class="ms-4 generate-thread-proposed-banner"
                >Link: <span class="generate-thread-propose-link"></span
              ></span>
            </section>
            <section class="glitch scheme-report mt-2">
              <p class="d-none"></p>
            </section>
          </section>
        </section>

        <section class="mt-5">
          <section class="explanation">
            <h5 class="fw-bold">Select Your Background Image</h5>
            <p>This is compulsory.</p>
          </section>
          <section class="bg-white p-4 rounded">
            <label for="create-the-background-image" style="display: block"
              >Upload Image:</label
            >
            <section class="d-inline-flex align-items-center mt-2 w-100">
              <input
                type="file"
                name="cover-thread-image"
                class="generate-thread-transfer-wrap"
                id="create-the-background-image"
                accept="image/png, image/jpg, image/gif"
                required
              />
            </section>
            <section class="mt-2">
              <img
                alt="cover preview  thread"
                class="generate-thread-wrap-image d-none account-thread-generate-wrap"
              />
            </section>
            <section class="scheme-report glitch mt-2">
              <p class="d-none"></p>
            </section>
          </section>
        </section>

        <section class="mt-5">
          <section class="explanation">
            <h5 class="fw-bold">Select Your Profile Image 👤</h5>
            <p>This is compulsory.</p>
          </section>
          <section class="rounded bg-white p-4">
            <label for="create-background-transfer-image" style="display: block"
              >Upload Image:</label
            >
            <section
              class="justify-content-between d-inline-flex align-items-center mt-2 w-100"
            >
              <input
                name="profile-thread-image"
                class="generate-thread-transfer-image"
                id="create-background-transfer-image"
                accept="image/png, image/jpg, image/gif"
                type="file"
                required
              />
              <section>
                <img
                  alt="preview profile thread"
                  class="account-thread-generate-peek d-none"
                />
              </section>
            </section>
            <section class="glitch scheme-report mt-2">
              <p class="d-none"></p>
            </section>
          </section>
        </section>
        <button type="submit" class="p-3 mt-3 btn-generate-thread">
          Post Your Thread
        </button>
      </form>
    </section>
  </section>
</main>
