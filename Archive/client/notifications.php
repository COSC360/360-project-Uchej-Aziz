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
      <h6 class="text-uppercase">Panel 🛠️</h6>
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
              ><i class="bi bi-bank2"></i><span class="ms-2">Mgmt Portal</span>
            </a>
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
    <section class="col-md-6 overflow-auto notices-box mx-auto mb-4">
      <?php 
			
				require_once $_SERVER["DOCUMENT_ROOT"].'/server/controllers/NotificationsClass.class.php';
				
				$nots = (new NotificationsClass())->get([]); if
      (count($nots) === 0) echo '
      <section
        class="glitch-data p-3 bg-none text-center scheme-report profile-null-data"
      >
        <img
          src="http://'.$_SERVER['HTTP_HOST'].'/client/img/error-empty-content.svg"
          alt="content not present at the moment"
          class="null-data mx-auto"
          style="display: block"
        />
        <p class="pt-5">There is nothing to return...</p>
      </section>
      '; foreach($nots as $key=>$notification_day) { echo '
      <section class="mb-4">
        <h3>'.$key.'</h3>
        '; foreach($notification_day as $not) { echo '
        <section>
          <section class="d-inline-flex notice-report px-3 py-3 w-100 mt-3">
            '; if ($not['notificationType'] == 1) echo '<i
              class="bi bi-blockquote-left text-center my-auto text-success"
            ></i
            >'; else if ($not['notificationType'] == 2) echo '<i
              class="bi bi-envelope-plus-fill text-center my-auto text-success"
            ></i
            >'; else if ($not['notificationType'] == 3) echo '<i
              class="bi bi-arrow-down-circle my-auto polls-deduct text-center"
            ></i
            >'; else if ($not['notificationType'] == 4) echo '<i
              class="bi bi-arrow-up-circle polls-boost text-center my-auto"
            ></i
            >'; else if ($not['notificationType'] == 5) echo '<i
              class="bi bi-trash3 text-center my-auto text-danger"
            ></i
            >'; else echo '<i
              class="bi bi-bug-fill my-auto text-danger text-center"
            ></i
            >'; echo '
            <p class="ms-3 my-auto">
              <a href="/account/'.$not['idUserReply'].'"
                >'.$not['replied_username'].'</a
              >
              '.$not['content'].'
              <a href="/t/'.$not['link'].'"
                >/t/'.$not['link'].'</a
              >.
            </p>
          </section>
        </section>
        '; } echo '
      </section>
      '; } ?>
    </section>

    <section class="col-md-3">
      <section class="text-center rounded after-generate-section">
        <a href="/t/create" style="display: block"
          ><i class="bi bi-send-plus"></i
          ><span class="ms-3">Kickoff A Thread</span></a
        >
      </section>

      <section class="py-3 prime-threads-box rounded mb-4 mt-4 px-3">
        <h5>Prime Threads</h5>
        <section>
          <?php 
					require_once SERVER_DIRECTORY.'/controllers/ThreadsClass.class.php';

					$ths = (new ThreadsClass())->getTopThreads(); $counter = 0; if
          (count($ths) == 0) { echo '
          <p class="text-center mt-3">No Info.</p>
          '; } else { foreach($ths as $th) { echo '
          <section class="d-flex align-middle py-2">
            '; echo '
            <section class="prime-thread-info-label me-auto">
              '; if ($counter == 0) { echo '<i
                class="bi bi-reception-4 gold-position"
              ></i
              >'; echo '<span class="ms-1"
                ><a href="/t/'.$th['link'].'/"
                  >t/'.$th['link'].'/</a
                ></span
              >'; } else if ($counter == 1) { echo '<i
                class="bi bi-reception-4 silver-position"
              ></i
              >'; echo '<span class="ms-1"
                ><a href="/t/'.$th['link'].'/"
                  >t/'.$th['link'].'/</a
                ></span
              >'; } else if ($counter == 2) { echo '<i
                class="bi bi-reception-4 bronze-position"
              ></i
              >'; echo '<span class="ms-1"
                ><a href="/t/'.$th['link'].'/"
                  >t/'.$th['link'].'/</a
                ></span
              >'; } else { echo '<span class="ms-1"
                ><a href="/t/'.$th['link'].'/"
                  >t/'.$th['link'].'/</a
                ></span
              >'; } echo '
            </section>
            <section class="prime-thread-info-polls-boost">
              <span class="me-2">'.$th['total_posts'].'</span
              ><i class="bi bi-arrow-up-circle"></i>'; echo '
            </section>
            '; echo '
          </section>
          '; $counter += 1; } } ?>
        </section>
      </section>

      <section
        class="important-channels d-flex p-3 mt-4 rounded mb-5 flex-column"
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
