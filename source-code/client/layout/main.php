<?php
$urlSecurity = $_SERVER['REQUEST_URI'];
$urlSecurity = substr($urlSecurity, strpos($urlSecurity, ".") + 1);
if ($urlSecurity === "php") header("Location: /");
?>

<main style="
        width: 75%;
        margin-top: 3rem;
        margin-left: auto;
        margin-right: auto;
      ">
    <section class="row">
        <section class="col-md-2 agenda">
            <h6 class="text-uppercase">Panel 🛠️</h6>
            <nav class="mt-3">
                <ul>
                    <li>
                        <a href="/" class="effective rounded"><i class="bi bi-house-door"></i><span class="ms-2">Home</span></a>
                    </li>
                    <li>
                        <a href="/search" class="rounded"><i class="bi bi-blockquote-left"></i><span class="ms-2">Responses</span></a>
                    </li>

                    <li>
                        <a href="/search" class="rounded"><i class="bi bi-cloud-plus-fill"></i><span class="ms-2">Threads</span></a>
                    </li>
                    <li>
                        <a href="/search" class="rounded"><i class="bi bi-cloud-fog2"></i><span class="ms-2">My Threads</span></a>
                    </li>
                    <?php if (isset($_SESSION['IS_AUTHORIZED']) && isset($_SESSION['IS_ADMIN']) && $_SESSION['IS_ADMIN']) { ?>
                        <li>
                            <a href="/admin" class="rounded"><i class="bi bi-bank2"></i><span class="ms-2">Mgmt Portal</span></a>
                        </li>
                        <?php
                    } ?>
                </ul>
            </nav>
        </section>
        <section class="col-md-6 affair-threads overflow-auto mx-auto mb-4">
            <?php
            require_once SERVER_DIRECTORY . '/controllers/PostsClass.class.php';

            $ps = (new PostsClass())->loadAllPosts();

            if (count($ps) != 0) {
                foreach ($ps as $post) {
                    echo ' <article class="rounded p-4 mb-5"> ';
                    echo ' <section class="row">';
                    echo '
              <section class="col-sm-2">
                ';
                    echo '
                <section
                  class="d-flex flex-md-column flex-sm-row justify-content-center justify-content-evenly text-center after-polls"
                  data-post-id="' . $post['idPost'] . '"
                >
                  ';
                    if ($post['isVoted'] == 0) {
                        echo '<i
                    class="bi bi-arrow-up-circle my-auto"
                  ></i
                  >';
                        echo '<span class="mt-2 mb-2" style="display: block"
                    ><a href="#">' . $post['numOfVotes'] . '</a></span
                  >';
                        echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
                    }
                    else if ($post['isVoted'] == 1 && $post['typeVote'] == 1) {
                        echo '<i class="bi bi-arrow-up-circle polls-boost my-auto"></i
                  >';
                        echo '<span class="mt-2 mb-2" style="display: block"
                    ><a href="#" class="polls-boost"
                      >' . $post['numOfVotes'] . '</a
                    ></span
                  >';
                        echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
                    }
                    else if ($post['isVoted'] == 1 && $post['typeVote'] == - 1) {
                        echo '<i class="bi bi-arrow-up-circle my-auto"></i>';
                        echo '<span class="mt-2 mb-2" style="display: block"
                    ><a href="#" class="polls-deduct"
                      >' . $post['numOfVotes'] . '</a
                    ></span
                  >';
                        echo '<i
                    class="bi bi-arrow-down-circle polls-deduct my-auto"
                  ></i
                  >';
                    }
                    echo '
                </section>
                ';
                    echo '
              </section>
              ';
                    echo '
              <section class="col-sm-10">
                ';
                    echo '<span class="thread-label"
                  >Discussed at
                  <a href="/t/' . $post['link'] . '"
                    >/t/' . $post['link'] . '</a
                  ></span
                >';
                    echo '
                <h4>
                  <a href="/t/' . $post['link'] . '/' . $post['idPost'] . '"
                    >' . $post['postTitle'] . '</a
                  >
                </h4>
                ';
                    echo '
                <p>
                  ';
                    if (is_null($post['image']) && is_null($post['media_link']) && !is_null($post['content'])) {
                        echo $post['content'];
                    }
                    else if (!is_null($post['image']) && is_null($post['media_link']) && is_null($post['content'])) {
                        echo '<img
                    src="http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/post_images/' . $post['image'] . '"
                    alt="content-img"
                  />';
                    }
                    else if (is_null($post['image']) && !is_null($post['media_link']) && is_null($post['content'])) {
                        echo '<iframe
                    width="100%"
                    height="300"
                    src="' . $post['media_link'] . '"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe
                  >';
                    }
                    else if (!is_null($post['image']) && is_null($post['media_link']) && !is_null($post['content'])) {
                        echo $post['content'];
                        echo '<img
                    src="http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/post_images/' . $post['image'] . '"
                    alt="content-img"
                    class="pt-2"
                  />';
                    }
                    else if (is_null($post['image']) && !is_null($post['media_link']) && !is_null($post['content'])) {
                        echo $post['content'];
                        echo '<iframe
                    class="pt-2"
                    width="100%"
                    height="300"
                    src="' . $post['media_link'] . '"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe
                  >';
                    }
                    else if (!is_null($post['image']) && !is_null($post['media_link']) && !is_null($post['content'])) {
                        echo $post['content'];
                        echo '<img
                    src="http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/post_images/' . $post['image'] . '"
                    alt="content-img"
                    class="pt-2"
                  />';
                        echo '<iframe
                    class="pt-2"
                    width="100%"
                    height="300"
                    src="' . $post['media_link'] . '"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe
                  >';
                    }
                    else {
                        echo $post['content'];
                    }
                    echo '
                </p>
                ';
                    echo '
                <section class="after-info-box d-flex justify-content-between">
                  ';
                    echo '
                  <section class="account-info-short d-flex align-middle">
                    ';
                    echo '<img
                      class="img-fluid image-header-account"
                      src="http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/profilePictures/' . $post['profile_image'] . '"
                      alt="' . $post['username'] . '-profile-picture"
                    />';
                    echo '<span class="ms-2"
                      ><a href="/account/' . $post['ownerId'] . '"
                        >' . $post['username'] . '</a
                      >
                      Posted This.</span
                    >';
                    echo '
                  </section>
                  ';
                    if ($post['timestamp'] / 60 < 60) {
                        echo '<span
                    class="schedule-after"
                    style="display: block"
                    >' . ceil($post['timestamp'] / 60) . 'm ago</span
                  >';
                    }
                    else if ($post['timestamp'] / 60 >= 60 && $post['timestamp'] / 60 < 1409) {
                        echo '<span
                    class="schedule-after"
                    style="display: block"
                    >' . ceil($post['timestamp'] / 3600) . 'h ago</span
                  >';
                    }
                    else {
                        echo '<span
                    class="schedule-after"
                    style="display: block"
                    >' . ceil($post['timestamp'] / 86400) . 'd ago</span
                  >';
                    }
                    echo '
                  <section class="after-info-remarks">
                    ';
                    echo '<a
                      href="/t/' . $post['link'] . '/' . $post['idPost'] . '"
                      ><i class="bi bi-blockquote-left"></i
                      ><span class="ms-1">' . $post['totalComments'] . '</span></a
                    >';
                    echo '
                  </section>
                  ';
                    echo '
                </section>
                ';
                    echo '
              </section>
              ';
                    echo '
            </section>
            ';
                    echo '
          </article>
          ';
                }
            }
            else { ?>
                <section class="scheme-report glitch-report text-center bg-none p-3">
                    <img src="<?php echo "http://" . $_SERVER['HTTP_HOST']; ?>/client/img/error-empty-content.svg" alt="no content available" style="display: block" class="null-data mx-auto">
                    <p class="pt-5">There is nothing to return...</p>
                </section>
                <?php
            } ?>
        </section>
        <section class="col-md-3">
            <section class="after-generate-section text-center rounded">
                <a style="display: block" href="<?php if (isset($_SESSION['IS_AUTHORIZED'])) { ?>/t/create<?php
                }
                else { ?>/login<?php
                } ?>"><i class="bi bi-send-plus"></i><span class="ms-3">Discuss Something New</span></a>
            </section>

            <section class="prime-threads-box mt-4 mb-4 rounded px-3 py-3">
                <h5>Prime Threads</h5>
                <section>
                    <?php
                    require_once SERVER_DIRECTORY . '/controllers/ThreadsClass.class.php';

                    $ths = (new ThreadsClass())->getTopThreads();
                    $counter = 0;
                    if (count($ths) == 0) {
                        echo '
              <p class="text-center mt-3">No discussions yet.</p>';
                    }
                    else {
                        foreach ($ths as $th) {
                            echo '
              <section class="d-flex align-middle py-2">
                ';
                            echo '
                <section class="prime-thread-info-label me-auto">
                  ';
                            if ($counter == 0) {
                                echo '<i
                    class="bi bi-reception-4 gold-position"
                  ></i
                  >';
                                echo '<span class="ms-1"
                    ><a href="/t/' . $th['link'] . '"
                      >t/' . $th['link'] . '</a
                    ></span
                  >';
                            }
                            else if ($counter == 1) {
                                echo '<i
                    class="bi bi-reception-4 silver-position"
                  ></i
                  >';
                                echo '<span class="ms-1"
                    ><a href="/t/' . $th['link'] . '"
                      >t/' . $th['link'] . '</a
                    ></span
                  >';
                            }
                            else if ($counter == 2) {
                                echo '<i
                    class="bi bi-reception-4 bronze-position"
                  ></i
                  >';
                                echo '<span class="ms-1"
                    ><a href="/t/' . $th['link'] . '"
                      >t/' . $th['link'] . '</a
                    ></span
                  >';
                            }
                            else {
                                echo '<span class="ms-1"
                    ><a href="/t/' . $th['link'] . '"
                      >t/' . $th['link'] . '</a
                    ></span
                  >';
                            }
                            echo '
                </section>
                <section class="prime-thread-info-polls-boost">
                  <span class="me-2">' . $th['total_posts'] . '</span
                  ><i class="bi bi-arrow-up-circle"></i>';
                            echo '
                </section>
                ';
                            echo '
              </section>
              ';
                            $counter += 1;
                        }
                    } ?>
                </section>
            </section>

            <section class="important-channels mt-4 mb-5 p-3 rounded d-flex flex-column">
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
