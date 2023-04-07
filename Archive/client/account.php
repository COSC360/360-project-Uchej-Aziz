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
    <section class="col-md-2 agenda">
      <section
        class="profile-details rounded d-flex flex-column text-center align-items-center p-3 mb-2"
      >
        <img
          class="profile-details-image"
          style="display: block"
          src="<?php echo $_SESSION['USER_IMAGE']; ?>"
          alt="<?php echo $_SESSION['USERNAME']; ?>-Profile-Picture"
        />
        <h4 class="mt-3"><?php echo $_SESSION['USERNAME']; ?></h4>
        <a href="/account/edit">Profile Configuration</a>
      </section>
      <h6 class="text-uppercase mt-4">Panel 🛠️</h6>
      <nav class="mt-3">
        <ul>
          <li>
            <a href="/" class="rounded effective">
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
            <a href="/search" class="rounded"
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

    <section class="col-md-7 overflow-auto profile-folio mx-auto mb-4">
      <h3 class="mb-4">Profile 🫥</h3>

      <section class="overflow-auto account-operation">
        <?php 
						require_once $_SERVER["DOCUMENT_ROOT"].'/server/controllers/UsersClass.class.php';

						if (count($url) == 2 && $router->getTitle() == "Account" && is_numeric($url[1]))
        { $content = (new UsersClass())->findPostsAndComments([$url[1], 1]);
        } else if (count($url) == 1 && $router->getTitle() == "Account") {
        $content = (new
        UsersClass())->findPostsAndComments([$_SESSION['USERNAME'], 0]); }
        $result = ""; if (count($content) != 0) { foreach ($content as $value) {
        $result .= '
        <section
          class="mb-3 account-operation-data p-3 align-items-center rounded d-inline-flex w-100 bg-white"
        >
          <section class="me-3" style="display: block">
            '; $result .= ($value['type'] === "body_comments") ? '<i
              class="bi bi-blockquote-left"
            ></i
            >' : '<i class="bi bi-cloud-fog2"></i>'; $result .= '
          </section>
          <section class="detail">
            <span
              >'; $result .= ($value['type'] === "body_comments") ? 'New Comment:' :
              'New Post:'; $result .= '</span
            >
            <p>
              '; $result .= (strlen($value['content']) == 0) ? 'Image
              Attachment' : substr($value['content'], 0, 80).'...'; $result .= '
            </p>
            <section class="detail-channel mt-0">
              '; $result .= ($value['type'] === "body_comments") ? '<span
                >Reacted at </span
              >' : '<span>Discussed at </span>'; $result .= '<a
                href="/t/'.$value['url'].'s"
                >/t/'.$value['url'].'/</a
              >
            </section>
          </section>
        </section>
        '; } echo $result; } else { ?>
        <section
          class="bg-none scheme-report text-center profile-null-data glitch-data p-3"
        >
          <img src="<?php echo "http://".$_SERVER['HTTP_HOST']; ?>/client/img/error-empty-content.svg"
          alt="content not present at the moment" class="null-data mx-auto"
          style="display: block">
          <p class="pt-5">There is nothing to return...</p>
        </section>
        <?php } ?>
      </section>
    </section>

    <section class="col-md-3">
      <section
        class="important-channels flex-column mb-5 d-flex p-4 py-3 rounded"
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
