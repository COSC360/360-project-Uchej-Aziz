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
    <section class="agenda col-md-2">
      <section
        class="text-center d-flex align-items-center rounded profile-details flex-column"
        style="padding: 1rem; margin-bottom: 0.5rem"
      >
        <img
          class="profile-details-image"
          src="<?php echo $_SESSION['USER_IMAGE']; ?>"
          alt="<?php echo $_SESSION['USERNAME']; ?>-Profile-Picture"
          style="display: block"
        />
        <h4 style="margin-top: 1rem"><?php echo $_SESSION['USERNAME']; ?></h4>
        <a href="/account/edit">Profile Configuration</a>
      </section>
      <h6 class="text-uppercase" style="margin-top: 1.5rem">Panel 🛠️</h6>
      <nav style="margin-top: 1rem">
        <ul>
          <li>
            <a href="/" class="rounded effective">
              <i class="bi bi-house-door"></i>
              <span style="margin-left: 0.5rem">Home</span>
            </a>
          </li>
          <?php if (isset($_SESSION['IS_AUTHORIZED']) && isset($_SESSION['IS_ADMIN']) && $_SESSION['IS_ADMIN']) { ?>
          <li>
            <a href="/admin" class="rounded"
              ><i class="bi bi-bank2"></i
              ><span style="margin-left: 0.5rem">Mgmt Portal</span></a
            >
          </li>
          <?php } ?>
          <li>
            <a href="/search" class="rounded"
              ><i class="bi bi-cloud-plus-fill"></i
              ><span style="margin-left: 0.5rem">Threads</span></a
            >
          </li>
          <li>
            <a href="/search" class="rounded"
              ><i class="bi bi-cloud-fog2"></i
              ><span style="margin-left: 0.5rem">My Threads</span></a
            >
          </li>
          <li>
            <a href="/search" class="rounded"
              ><i class="bi bi-blockquote-left"></i
              ><span style="margin-left: 0.5rem">Responses</span></a
            >
          </li>
        </ul>
      </nav>
    </section>

    <section
      class="profile-folio overflow-auto mx-auto col-md-7"
      style="margin-bottom: 1.5rem"
    >
      <h3 style="margin-bottom: 1.5rem">Profile 🫥</h3>
      <section
        class="bg-white regular-profile-section-data"
        style="padding: 1rem"
      >
        <form enctype="multipart/form-data">
          <h5>Modify My Profile Picture</h5>
          <section
            class="d-inline-flex align-items-center justify-content-lg-between"
            style="width: 100%; margin-top: 1rem"
          >
            <img
              class="profile-changed-account-image"
              style="display: block"
              src="<?php echo $_SESSION['USER_IMAGE']; ?>"
              alt="<?php echo $_SESSION['USERNAME']; ?>-Profile-Picture"
            />

            <input
              name="profile-picture-update"
              class="account-configurations-image"
              accept="image/png, image/jpg, image/gif"
              type="file"
              style="margin-left: 1rem"
            />
          </section>

          <h5 style="margin-top: 3rem">Modify My Profile Username</h5>
          <section
            class="profile-configurations-alter-username"
            style="margin-top: 1rem; display: block"
          >
            <label for="profile-settings-user-name">My Profile Username:</label>
            <input
              placeholder="<?php echo $_SESSION['USERNAME']; ?>"
              class="account-configurations-username"
              style="width: 100%; padding: 0.5rem"
              id="profile-settings-user-name"
              type="text"
              name="profile-username-update"
            />
          </section>

          <h5 style="margin-top: 3rem">Modify Profile Password</h5>
          <section class="row" style="margin-top: 1rem">
            <section
              class="col-sm-6 former-password-section"
              style="margin-bottom: 1rem"
            >
              <label
                for="profile-settings-old-password"
                class="mb-2"
                style="display: block"
                >My Current Password:</label
              >
              <input
                name="profile-oldpassword-update"
                type="password"
                id="profile-settings-old-password"
                class="w-100 p-2 account-configurations-formerpassword"
                placeholder="Obsolete Password Here 🚮"
              />
            </section>
            <section class="col-sm-6 appended-password-section mb-3">
              <label
                for="profile-settings-new-password"
                class="mb-2 text-success"
                style="display: block"
                >My Newly Set Password:</label
              >
              <input
                name="profile-newpassword-update"
                id="profile-settings-new-password"
                type="password"
                class="w-100 p-2 account-configurations-appendedpassword"
                placeholder="New Password Here 🤩"
              />
            </section>
          </section>

          <section class="mt-3 d-none bg-danger scheme-report">
            <section class="w-100 px-3 py-3 scheme-report-data d-inline-flex">
              <i class="bi text-center my-auto text-light bi-bug-fill"></i>
              <p class="ms-3 my-auto">
                Your information couldn't be modified.<br /><span
                  class="fw-bolder"
                  >This is because:</span
                >
                <span class="reason"></span>
              </p>
            </section>
          </section>

          <button class="mt-4 rounded btn-profile-change p-2">
            Modify Profile Information
          </button>
        </form>
      </section>

      <h3 class="mb-3 mt-4">Leave Forum</h3>
      <section
        class="bg-white remove-section-data p-3 flex-column align-items-start justify-content-between d-flex"
      >
        <p class="text-danger" style="font-size: 0.9rem">
          Think twice because deactivating this account is not reversible.
        </p>
        <button class="btn btn-profile-remove">Deactivate Account</button>
      </section>
    </section>

    <section class="col-md-3">
      <section
        class="flex-column rounded d-flex important-channels mb-5 p-4 py-3"
      >
        <section class="row">
          <section class="col-md-6">
            <h5 class="mt-2 text-uppercase">Frequent</h5>
            <nav>
              <ul class="list-group">
                <li><a href="/threads">Threads</a></li>
                <li><a href="/help">Assistance</a></li>
                <li><a href="/ads">Broadcast</a></li>
                <li><a href="/blog">Site</a></li>
              </ul>
            </nav>
          </section>
          <section class="col-md-6">
            <h5 class="mt-2 text-uppercase">Enterprise</h5>
            <nav>
              <ul class="list-group">
                <li><a href="/careers">Careers</a></li>
                <li><a href="/about">About Us</a></li>
                <li><a href="/terms">T & Conditions</a></li>
                <li><a href="/privacy">Confidentiality</a></li>
                <li><a href="/press">Press</a></li>
              </ul>
            </nav>
          </section>
        </section>
      </section>
    </section>
  </section>
</main>
