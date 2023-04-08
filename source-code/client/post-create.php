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
      <h6 class="text-uppercase">Panel 🛠️</h6>
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
              ><i class="bi bi-blockquote-left"></i
              ><span class="ms-2">Responses</span>
            </a>
          </li>
          <li>
            <a href="/search" class="rounded"
              ><i class="bi bi-cloud-fog2"></i
              ><span class="ms-2">My Threads</span></a
            >
          </li>
        </ul>
      </nav>
    </section>
    <section
      class="overflow-auto generate-after-large-box mx-auto col-md-9 p-4 mb-4"
    >
      <h2>Post Something 🫧</h2>

      <section class="scheme-report bg-danger mb-3 mt-4 d-none">
        <section class="d-inline-flex scheme-report-data px-3 py-3 w-100">
          <i class="bi bi-bug-fill my-auto text-light text-center"></i>
          <p class="ms-3 my-auto">
            Post could not be successfully made. Please try again. <br /><span
              class="fw-bolder"
              >This is because: </span
            ><span class="glitch-report"></span>
          </p>
        </section>
      </section>

      <form enctype="multipart/form-data">
        <section class="mt-4">
          <section class="explanation">
            <h5 class="fw-bold mb-3">Post Title 📝</h5>
            <p>
              Enter your
              <span class="fw-bolder text-decoration-underline">unique</span>
              Title
            </p>
          </section>
          <section class="bg-white rounded generate-after-data p-4">
            <label for="create-the-post-name" style="display: block"
              >Post Title:</label
            >
            <section class="d-inline-flex align-items-center mt-2 w-100">
              <input
                class="p-1 generate-after-label"
                id="create-the-post-name"
                type="text"
                placeholder="Your Title Goes Here 👁️"
                name="post-title"
                required
              />
            </section>
            <section class="scheme-report glitch mt-2">
              <p class="d-none"></p>
            </section>
          </section>
        </section>

        <section class="mt-5">
          <section class="explanation">
            <h5 class="fw-bold">Say Something 🗣️</h5>
            <p>What's On Your Mind?</p>
          </section>
          <section class="rounded bg-white p-4">
            <label for="create-the-post-text" style="display: block"
              >Post Body:</label
            >
            <section
              class="d-inline-flex align-items-center justify-content-between w-100 mt-2"
            >
              <textarea
                placeholder="Exercise your freedom to speak 👥"
                class="ps-2 py-1 generate-after-text"
                id="create-the-post-text"
                name="post-text"
                rows="5"
              ></textarea>
            </section>
            <section class="glitch scheme-report mt-2">
              <p class="d-none"></p>
            </section>
          </section>
        </section>

        <section class="mt-5">
          <section class="explanation">
            <h5 class="fw-bold">Uploading Any Image? ☄️</h5>
            <p>Make your Post More Engaging With an Image</p>
          </section>
          <section class="rounded bg-white p-4">
            <label for="create-the-post-image" style="display: block"
              >Upload image file:</label
            >
            <section class="d-inline-flex align-items-center mt-2 w-100">
              <input
                name="cover-post-image"
                type="file"
                accept="image/png, image/jpg, image/gif"
                class="generate-after-image"
                id="create-the-post-image"
                required
              />
            </section>
            <section class="mt-2">
              <img
                alt="image preview post"
                class="d-none account-after-generate-peek"
              />
            </section>
            <section class="glitch scheme-report mt-2">
              <p class="d-none"></p>
            </section>
          </section>
        </section>

        <section class="mt-5">
          <section class="explanation">
            <h5 class="fw-bold">How About a Video? 🎥</h5>
            <p>
              Enhance Post Visuals With a
              <span class="fw-bolder text-decoration-underline">$currentPost['media_link']</span>
              Video.
            </p>
          </section>
          <section class="p-4 bg-white rounded">
            <label for="create-the-post-text-url" style="display: block"
              >YouTube URL</label
            >
            <section
              class="d-inline-flex justify-content-between align-items-center mt-2 w-100"
            >
              <input
                class="ps-2 py-1 generate-after-text-link"
                id="create-the-post-text-url"
                type="text"
                placeholder="YouTube Links Only"
                name="post-text"
              />
            </section>
            <section class="glitch scheme-report mt-2">
              <p class="d-none"></p>
            </section>
          </section>
        </section>

        <button type="submit" class="p-3 mt-3 btn-generate-after">
          Create Post
        </button>
      </form>
    </section>
  </section>
</main>
