<?php 
	$url = $_SERVER['REQUEST_URI'];
	$url = substr($url, strpos($url, ".") + 1);
	if ($url === "php")
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
            <a href="/" class="rounded"
              ><i class="bi bi-house-door"></i><span class="ms-2">Home</span></a
            >
          </li>
          <li>
            <a href="/admin" class="rounded effective"
              ><i class="bi bi-bank2"></i
              ><span class="ms-2">Mgmt Portal</span></a
            >
          </li>
          <li>
            <a href="/admin/users" class="rounded"
              ><i class="bi bi-person-bounding-box"></i
              ><span class="ms-2">Users</span></a
            >
          </li>
          <li>
            <a href="/admin/threads" class="rounded"
              ><i class="bi bi-cloud-plus-fill"></i
              ><span class="ms-2">Threads</span></a
            >
          </li>
        </ul>
      </nav>
    </section>

    <section class="col-md-10 overflow-auto mx-auto mb-4 chief-panel">
      <section class="row">
        <section class="col-sm-5">
          <?php 
						require_once $_SERVER["DOCUMENT_ROOT"].'/server/controllers/AdminClass.class.php';
				
						$stats = (new AdminClass())->getAllStatistics([]); function numsize($size,
          $round=2){ $unit=['', 'K', 'M', 'B', 'T']; if ((int)$size !== 0)
          return
          round($size/pow(1000,($i=floor(log($size,1000)))),$round).$unit[$i];
          return $size; } ?>
          <section
            class="d-flex align-items-center p-3 rounded mb-4 chief-d-profiles"
          >
            <section>
              <i class="bi bi-people"></i>
            </section>
            <section class="ms-auto align-right detail" style="display: block">
              <h5>Signed up Users</h5>
              <span><?php echo numsize($stats[0]); ?></span>
            </section>
          </section>

          <section
            class="chief-d-threads align-items-center mb-4 rounded d-flex p-3"
          >
            <section>
              <i class="bi bi-cloud-fog2"></i>
            </section>
            <section class="ms-auto align-right detail" style="display: block">
              <h5>Threads</h5>
              <span><?php echo numsize($stats[1]); ?></span>
            </section>
          </section>

          <section
            class="d-flex chief-d-posts p-3 rounded mb-4 align-items-center"
          >
            <section>
              <i class="bi bi-chat-left-text-fill"></i>
            </section>
            <section class="ms-auto align-right detail" style="display: block">
              <h5>Discussions</h5>
              <span><?php echo numsize($stats[3]); ?></span>
            </section>
          </section>

          <section
            class="d-flex rounded align-items-center chief-d-remarks mb-4 p-3"
          >
            <section>
              <i class="bi bi-vector-pen"></i>
            </section>
            <section class="ms-auto align-right detail" style="display: block">
              <h5>Reviews</h5>
              <span><?php echo numsize($stats[2]); ?></span>
            </section>
          </section>
        </section>
        <section class="col-sm-7">
          <h3 class="fw-bold mb-3">What's Making Rounds💫</h3>
          <section class="chief-d-prime overflow-auto">
            <?php 
					require_once SERVER_DIRECTORY.'/controllers/PostsClass.class.php';

					$ps = (new PostsClass())->loadAllPosts(); if (count($ps) != 0) {
            foreach ($ps as $post) { echo '
            <article class="p-4 mb-5 bg-white rounded">
              '; echo '
              <section class="row">
                '; echo '
                <section class="col-sm-2">
                  '; echo '
                  <section
                    class="text-center justify-content-center flex-md-column d-flex after-polls justify-content-evenly flex-sm-row"
                    data-post-id="'.$post['idPost'].'"
                  >
                    '; if ($post['isVoted'] == 0) { echo '<i
                      class="bi bi-arrow-up-circle my-auto"
                    ></i
                    >'; echo '<span class="mt-2 mb-2" style="display: block"
                      ><a href="#">'.$post['numOfVotes'].'</a></span
                    >'; echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
                    } else if ($post['isVoted'] == 1 && $post['typeVote'] == 1)
                    { echo '<i
                      class="bi bi-arrow-up-circle polls-boost my-auto"
                    ></i
                    >'; echo '<span class="mt-2 mb-2" style="display: block"
                      ><a href="#" class="polls-boost"
                        >'.$post['numOfVotes'].'</a
                      ></span
                    >'; echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
                    } else if ($post['isVoted'] == 1 && $post['typeVote'] == -1)
                    { echo '<i class="bi bi-arrow-up-circle my-auto"></i>'; echo
                    '<span class="mt-2 mb-2" style="display: block"
                      ><a href="#" class="polls-deduct"
                        >'.$post['numOfVotes'].'</a
                      ></span
                    >'; echo '<i
                      class="bi bi-arrow-down-circle my-auto polls-deduct"
                    ></i
                    >'; } echo '
                  </section>
                  '; echo '
                </section>
                '; echo '
                <section class="col-sm-10">
                  '; echo '<span class="thread-label"
                    >Discussed at 
                    <a href="/t/'.$post['link'].'"
                      >/t/'.$post['link'].'</a
                    ></span
                  >'; echo '
                  <h4>
                    <a href="/t/'.$post['link'].'/'.$post['idPost'].'"
                      >'.$post['postTitle'].'</a>
                    <h1></h1>
                  </h4>
                  '; echo '
                  <p>
                    '; if (is_null($post['image']) &&
                    is_null($post['media_link']) && !is_null($post['content'])) {
                    echo $post['content']; } else if (!is_null($post['image'])
                    && is_null($post['media_link']) && is_null($post['content'])) {
                    echo '<img
                      src="http://'.''.$_SERVER['HTTP_HOST'].'/server/uploads/post_images/'.$post['image'].'"
                      alt="image-content"
                    />'; } else if (is_null($post['image']) &&
                    !is_null($post['media_link']) && is_null($post['content'])) {
                    echo '<iframe
                      height="300"
                      width="100%"
                      title="YouTube video player"
                      src="'.$post['media_link'].'"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      frameborder="0"
                      allowfullscreen
                    ></iframe
                    >'; } else if (!is_null($post['image']) &&
                    is_null($post['media_link']) && !is_null($post['content'])) {
                    echo $post['content']; echo '<img
                      src="http://'.''.$_SERVER['HTTP_HOST'].'/server/uploads/post_images/'.$post['image'].'"
                      alt="image-content"
                      class="pt-2"
                    />'; } else if (is_null($post['image']) &&
                    !is_null($post['media_link']) && !is_null($post['content'])) {
                    echo $post['content']; echo '<iframe
                      height="300"
                      width="100%"
                      title="YouTube video player"
                      src="'.$post['media_link'].'"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      frameborder="0"
                      class="pt-2"
                      allowfullscreen
                    ></iframe
                    >'; } else if (!is_null($post['image']) &&
                    !is_null($post['media_link']) && !is_null($post['content'])) {
                    echo $post['content']; echo '<img
                      src="http://'.''.$_SERVER['HTTP_HOST'].'/server/uploads/post_images/'.$post['image'].'"
                      alt="image-content"
                      class="pt-2"
                    />'; echo '<iframe
                      width="100%"
                      class="pt-2"
                      src="'.$post['media_link'].'"
                      height="300"
                      frameborder="0"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      title="YouTube video player"
                      allowfullscreen
                    ></iframe
                    >'; } else { echo $post['content']; } echo '
                  </p>
                  '; echo '
                  <section
                    class="d-flex after-info-box justify-content-between"
                  >
                    '; echo '
                    <section class="d-flex account-info-short align-middle">
                      '; echo '<img
                        class="image-header-account img-fluid"
                        src="http://'.''.$_SERVER['HTTP_HOST'].'/server/uploads/profilePictures/'.$post['profile_image'].'"
                        alt="'.$post['username'].'-profile-picture"
                      />'; echo '<span class="ms-2"
                        >Posted by
                        <a href="/account/'.$post['ownerId'].'"
                          >'.$post['username'].'</a
                        ></span
                      >'; echo '
                    </section>
                    '; if ($post['timestamp'] / 60 < 60) { echo '<span
                      class="schedule-after"
                      style="display: block"
                      >'.ceil($post['timestamp'] / 60).'mins ago</span
                    >'; } else if ($post['timestamp'] / 60 >= 60 &&
                    $post['timestamp'] / 60 < 1409) { echo '<span
                      class="schedule-after"
                      style="display: block"
                      >'.ceil($post['timestamp'] / 3600).'hrs ago</span
                    >'; } else { echo '<span
                      class="schedule-after"
                      style="display: block"
                      >'.ceil($post['timestamp'] / 86400).'d ago</span
                    >'; } echo '
                    <section class="after-info-remarks">
                      '; echo '<a
                        href="/t/'.$post['link'].'/'.$post['idPost'].'"
                        ><i class="bi bi-blockquote-left"></i
                        ><span class="ms-1">'.$post['totalComments'].'</span></a
                      >'; echo '
                    </section>
                    '; echo '
                  </section>
                  '; echo '
                </section>
                '; echo '
              </section>
              '; echo '
            </article>
            '; } } else { ?>
            <section class="p-3 glitch-data bg-none scheme-report text-center">
              <img src="<?php echo "http://".$_SERVER['HTTP_HOST']; ?>/client/img/error-empty-content.svg"
              alt="No Data to return yet" class="null-data mx-auto"
              style="display: block">
              <p class="pt-5">There's nothing to return...</p>
            </section>
            <?php } ?>
          </section>
        </section>
      </section>
    </section>
  </section>
</main>
