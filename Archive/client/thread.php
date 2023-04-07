<?php
$urlSecurity = $_SERVER["REQUEST_URI"];
$urlSecurity = substr($urlSecurity, strpos($urlSecurity, ".") + 1);
if ($urlSecurity === "php") {
    header("Location: /");
}

require_once $_SERVER["DOCUMENT_ROOT"] .
    "/server/controllers/ThreadsClass.class.php";
$threadInfo = (new ThreadsClass())->getThread($url[1]);
//print "THREAD INFO:";
//print_r($threadInfo);
?>
<section class="thread-header-bar mb-5">
    <section class="image-thread-background" style="background-image: url('<?php echo "http://" .
        "" .
        $_SERVER["HTTP_HOST"] .
        "/server/uploads/thread_backgrounds/" .
        $threadInfo[0]["thread_background"]; ?>');"></section>


    <section class="bg-light">
        <section style="width: 75%; margin-left: auto; margin-right: auto">
            <section class="d-inline-flex justify-content-center w-50">
                <img alt="thread_profile_picture" class="me-2 image-thread-account img-thumbnail" src="<?php echo "http://" .
                    "" .
                    $_SERVER["HTTP_HOST"] .
                    "/server/uploads/thread_profile/" .
                    $threadInfo[0]["thread_profile"]; ?>" />
                <section class="py-2">
                    <h3 class=""><?php echo $threadInfo[0]["title"]; ?></h3>
                    <a href="" class="thread-sm-url"><?php echo "t/" . $url[1]; ?></a>
                </section>
                <section class="py-2 ms-3">
                    <button type="button" class="aboard-thread-btn" data-status="<?php echo $threadInfo[0][
                    "isSubscribed"
                    ]; ?>">
                        <?php if ($threadInfo[0]["isSubscribed"] == 0) {
                            echo "Join";
                        } else {
                            echo "Leave";
                        } ?>
                    </button>
                </section>
            </section>
        </section>
    </section>
</section>

<main style="width: 75%; margin-left: auto; margin-right: auto">
    <section class="row">
        <section class="agenda col-md-2">
            <h6 class="text-uppercase">Panel 🛠️</h6>
            <nav class="mt-3">
                <ul>
                    <li>
                        <a href="/" class="rounded">
                            <i class="bi bi-house-door"></i>
                            <span class="ms-2">Home</span>
                        </a>
                    </li>
                    <?php if (
                        isset($_SESSION["IS_AUTHORIZED"]) &&
                        isset($_SESSION["IS_ADMIN"]) &&
                        $_SESSION["IS_ADMIN"]
                    ) { ?>
                        <li>
                            <a href="/admin" class="rounded"><i class="bi bi-bank2"></i><span class="ms-2">Mgmt Portal</span></a>
                        </li>
                    <?php } ?>
                    <li>
                        <a href="/search" class="rounded effective"><i class="bi bi-cloud-plus-fill"></i><span class="ms-2">Threads</span></a>
                    </li>
                    <li>
                        <a href="/search" class="rounded"><i class="bi bi-cloud-fog2"></i><span class="ms-2">My Threads</span></a>
                    </li>
                    <li>
                        <a href="/search" class="rounded"><i class="bi bi-blockquote-left"></i><span class="ms-2">Responses</span></a>
                    </li>
                </ul>
            </nav>
        </section>
        <section class="col-md-6 overflow-auto affair-threads mx-auto mb-4 threads-data">

            <?php if ($threadInfo[0]["isRowHidden"] == 1) { ?>
                <section class="bg-danger mb-3 scheme-report">
                    <section class="d-inline-flex px-3 py-3 w-100 scheme-report-data">
                        <i class="bi bi-bug-fill text-center my-auto text-light"></i>
                        <p class="ms-3 my-auto">
                            This post has been retained from the public by the Admin.<br /><span class="fw-bolder">This is because:</span>
                            The Admin Deemed the Situation as a Violation of Association
                            Rules.
                        </p>
                    </section>
                </section>
            <?php } ?>

            <section class="d-inline-flex rounded flex-fill bg-white mb-4 w-100 p-3">
                <section class="w-50">
                    <button class="prime-posts-arrange arrange-thread-btn me-4">
                        <i class="bi bi-reception-4 me-2"></i><span>Leading</span>
                    </button>
                    <button class="arrange-thread-btn appended-posts-arrange me-4">
                        <i class="bi bi-lightbulb-fill me-2"></i><span>Recent</span>
                    </button>
                </section>
                <section class="w-50">
                    <input class="w-100 mt-1 px-2 discover-thread" placeholder="Retrieve a Thread 🧐" type="text" />
                </section>
            </section>
            <section class="overflow-auto after-outcome-section">
                <?php
                require_once $_SERVER["DOCUMENT_ROOT"] .
                    "/server/controllers/PostsClass.class.php";
                require_once $_SERVER["DOCUMENT_ROOT"] .
                    "/server/controllers/CommentsClass.class.php";
                $sortedPosts = (new PostsClass())->loadPostByThread([$url[1]]);
                if (count($sortedPosts) == 0) { ?>
                    <section class="scheme-report text-center glitch-data bg-none p-3 mt-2">
                        <img src="<?php echo "http://" .
                            $_SERVER[
                            "HTTP_HOST"
                            ]; ?>/client/img/error-empty-content.svg" alt="content not present at the moment" class="null-data mx-auto" style="display: block">
                        <p class="pt-5">There is nothing to return...</p>
                    </section>
                <?php } else {foreach ($sortedPosts as $post) {
                    //print "POST INFO::";
                    //print_r($post);
                    echo '<article class="rounded p-4 mb-5">';
                    echo '<section class="row">';
                    echo '<section class="col-sm-2">';
                    echo '<section
							class="d-flex text-center flex-md-column flex-sm-row after-polls justify-content-center justify-content-evenly" data-post-id="' .
                        $post["idPost"] .
                        '">';
                    if ($post["isVoted"] == 0) {
                        echo '<i class="bi bi-arrow-up-circle my-auto"></i>';
                        echo '<span class="mt-2 mb-2" style="display: block"
								><a href="#">' .
                            $post["numOfVotes"] .
                            "</a></span>";
                        echo '<i class="bi bi-arrow-down-circle my-auto"></i
									>';
                    } elseif ($post["isVoted"] == 1 && $post["typeVote"] == 1) {
                        echo '<i class="bi bi-arrow-up-circle polls-boost my-auto"></i
									>';
                        echo '<span class="mt-2 mb-2" style="display: block"
								><a href="#" class="polls-boost">' .
                            $post["numOfVotes"] .
                            "</a></span>";
                        echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
                    } elseif ($post["isVoted"] == 1 && $post["typeVote"] == -1) {
                        echo '<i class="bi bi-arrow-up-circle my-auto"></i>';
                        echo '<span class="mt-2 mb-2" style="display: block"
								><a href="#" class="polls-deduct">' .
                            $post["numOfVotes"] .
                            "</a></span>";
                        echo '<i class="bi bi-arrow-down-circle polls-deduct my-auto"></i>';
                    }
                    echo "</section>";
                    echo "</section>";
                    echo '<section class="col-sm-10">';
                    echo '<h4><a href="/t/' . $post["link"] . "/" . $post["idPost"] . '">' . $post["postTitle"] . "</a></h4>";
                    echo '<p class="null-border">';
                    if (
                        is_null($post["image"]) &&
                        is_null($post["media_link"]) &&
                        !is_null($post["content"])
                    ) {
                        echo $post["content"];
                    } elseif (
                        !is_null($post["image"]) &&
                        is_null($post["media_link"]) &&
                        is_null($post["content"])
                    ) {
                        echo '<img src="http://' .
                            "" .
                            $_SERVER["HTTP_HOST"] .
                            "/server/uploads/post_images/" .
                            $post["image"] .
                            '" alt="image-content">';
                    } elseif (
                        is_null($post["image"]) &&
                        !is_null($post["media_link"]) &&
                        is_null($post["content"])
                    ) {
                        echo '<iframe
								height="300"
								width="100%"
								title="YouTube video player"
								src="' .
                            $post["media_link"] .
                            '"
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
								frameborder="0"
								allowfullscreen
							  ></iframe
							  >';
                    } elseif (
                        !is_null($post["image"]) &&
                        is_null($post["media_link"]) &&
                        !is_null($post["content"])
                    ) {
                        echo $post["content"];
                        echo '<img src="http://' .
                            "" .
                            $_SERVER["HTTP_HOST"] .
                            "/server/uploads/post_images/" .
                            $post["image"] .
                            '" alt="image-content" class="pt-2">';
                    } elseif (
                        is_null($post["image"]) &&
                        !is_null($post["media_link"]) &&
                        !is_null($post["content"])
                    ) {
                        echo $post["content"];
                        echo '<iframe
								width="100%"
								class="pt-2"
								src="' .
                            $post["media_link"] .
                            '"
								height="300"
								frameborder="0"
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
								title="YouTube video player"
								allowfullscreen
							  ></iframe
							  >';
                    } elseif (
                        !is_null($post["image"]) &&
                        !is_null($post["media_link"]) &&
                        !is_null($post["content"])
                    ) {
                        echo $post["content"];
                        echo '<img src="http://' .
                            "" .
                            $_SERVER["HTTP_HOST"] .
                            "/server/uploads/post_images/" .
                            $post["image"] .
                            '" alt="image-content" class="pt-2">';
                        echo '<iframe
								width="100%"
								class="pt-2"
								src="' .
                            $post["media_link"] .
                            '"
								height="300"
								frameborder="0"
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
								title="YouTube video player"
								allowfullscreen
							  ></iframe>';
                    } else {
                        echo $post["content"];
                    }
                    echo "</p>";
                    echo '<section
							class="after-info-box revoke d-flex justify-content-between mt-0"
						  >';
                    echo '<section class="account-info-short d-flex align-middle">';
                    echo '<img class="img-fluid image-header-account" src="http://' .
                        "" .
                        $_SERVER["HTTP_HOST"] .
                        "/server/uploads/profilePictures/" .
                        $post["profile_image"] .
                        '" alt="' .
                        $post["username"] .
                        '-Profile-Picture" />';
                    echo '<span class="ms-2"><a href="/account/' .
                        $post["ownerId"] .
                        '">' .
                        $post["username"] .
                        "</a> Posted This</span>";
                    echo "</section>";
                    if ($post["timestamp"] / 60 < 60) {
                        echo '<span class="schedule-after" style="display: block"
								>' .
                            ceil($post["timestamp"] / 60) .
                            "mins ago</span>";
                    } elseif (
                        $post["timestamp"] / 60 >= 60 &&
                        $post["timestamp"] / 60 < 1409
                    ) {
                        echo '<span class="schedule-after" style="display: block"
								>' .
                            ceil($post["timestamp"] / 3600) .
                            "hrs ago</span>";
                    } else {
                        echo '<span class="schedule-after" style="display: block"
								>' .
                            ceil($post["timestamp"] / 86400) .
                            "days ago</span>";
                    }
                    echo '<section class="after-info-remarks">';
                    echo '<a href="/t/' .
                        $post["link"] .
                        "/" .
                        $post["idPost"] .
                        '"><i class="bi bi-blockquote-left"></i
								><span class="ms-1">' .
                        $post["totalComments"] .
                        "</span></a>";
                    echo "</section>";
                    echo "</section>";
                    if (
                        (isset($_SESSION["IS_AUTHORIZED"]) &&
                            isset($_SESSION["IS_ADMIN"]) &&
                            $_SESSION["IS_ADMIN"]) ||
                        $_SESSION["USERNAME"] == $post["username"]
                    ) {
                        echo '<section class="mt-2 mb-2">';
                        $post["isHidden"] == 0
                            ? ($buttonText = "Hide")
                            : ($buttonText = "Unhide");
                        echo '<button
								class="after-disguise me-4 disguise" data-post-id="' .
                            $post["idPost"] .
                            '">' .
                            $buttonText .
                            "</button>";
                        echo '<button
								class="remove after-remove" data-post-id="' .
                            $post["idPost"] .
                            '">Remove</button>';
                        echo "</section>";
                    }
                    $postComments = (new CommentsClass())->getAllCommentsFromPostId(
                        $post["idPost"],
                        0
                    );

                    if (!empty($postComments)) {
                        foreach ($postComments as $comment) {
                            echo '<article class="rounded p-4 px-0">';
                            echo '<section class="row">';
                            echo '<section class="col-sm-2">';
                            echo '<section
									class="d-flex flex-md-column remark-polls justify-content-evenly flex-sm-row justify-content-center text-center" data-comment-id="' .
                                $comment["idComment"] .
                                '">';
                            if ($comment["isVoted"] == 0) {
                                echo '<i class="bi bi-arrow-up-circle my-auto"></i>';
                                echo '<span class="mt-2 mb-2" style="display: block"
										><a href="#">' .
                                    $comment["numOfVotes"] .
                                    "</a></span>";
                                echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
                            } elseif (
                                $comment["isVoted"] == 1 &&
                                $comment["typeVote"] == 1
                            ) {
                                echo '<i
										class="bi bi-arrow-up-circle polls-boost my-auto"
									  ></i>';
                                echo '<span class="mt-2 mb-2" style="display: block"
										><a href="#" class="polls-boost">' .
                                    $comment["numOfVotes"] .
                                    "</a></span>";
                                echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
                            } elseif (
                                $comment["isVoted"] == 1 &&
                                $comment["typeVote"] == -1
                            ) {
                                echo '<i class="bi bi-arrow-up-circle my-auto"></i>';
                                echo '<span class="mt-2 mb-2" style="display: block"
										><a href="#" class="polls-deduct">' .
                                    $comment["numOfVotes"] .
                                    "</a></span>";
                                echo '<i
										class="bi bi-arrow-down-circle polls-deduct my-auto"
									  ></i>';
                            }
                            echo "</section>";
                            echo "</section>";
                            echo '<section class="col-sm-10">';
                            echo '<p class="null-border">';
                            echo $comment["content"];
                            echo "</p>";
                            echo '<section
									class="d-flex revoke after-info-box justify-content-between"
								  >';
                            echo '<section
									class="d-flex account-info-short align-middle"
								  >';
                            echo '<img class="image-header-account img-fluid" src="http://' .
                                "" .
                                $_SERVER["HTTP_HOST"] .
                                "/server/uploads/profilePictures/" .
                                $comment["profile_image"] .
                                '" alt="' .
                                $comment["username"] .
                                '-Profile-Picture" />';
                            echo '<span class="ms-2"><a href="/account/' .
                                $comment["ownerId"] .
                                '">' .
                                $comment["username"] .
                                "</a> replies</span>";
                            echo "</section>";
                            if ($comment["timestamp"] / 60 < 60) {
                                echo '<span class="schedule-after" style="display: block">' .
                                    ceil($comment["timestamp"] / 60) .
                                    "mins ago</span>";
                            } elseif (
                                $comment["timestamp"] / 60 >= 60 &&
                                $comment["timestamp"] / 60 < 1409
                            ) {
                                echo '<span class="schedule-after" style="display: block">' .
                                    ceil($comment["timestamp"] / 3600) .
                                    "hrs ago</span>";
                            } else {
                                echo '<span class="schedule-after" style="display: block">' .
                                    ceil($comment["timestamp"] / 86400) .
                                    "days ago</span>";
                            }
                            echo "</section>";
                            echo '<section class="mt-2">';
                            if (
                                (isset($_SESSION["IS_AUTHORIZED"]) &&
                                    isset($_SESSION["IS_ADMIN"]) &&
                                    $_SESSION["IS_ADMIN"]) ||
                                $_SESSION["USERNAME"] == $comment["username"]
                            ) {
                                echo '<button class="remove remark-remove" data-comment-id="' .
                                    $comment["idComment"] .
                                    '">Remove</button>';
                            }
                            echo "</section>";
                            echo "</section>";
                            echo "</section>";
                            echo "</article>";
                        }
                    }
                    echo "</section>";
                    echo "</section>";
                    echo "</article>";
                }}
                ?>
            </section>
            <section class="col-md-3">
                <?php if ($threadInfo[0]["isRowHidden"] == 0) { ?>
                    <section class="rounded text-center after-generate-section"><?php echo '<a style="display: block" href=/t/' .
                            $url[1] .
                            "/create-post>"; ?><i class="bi bi-send-plus"></i><span class="ms-3">Discuss Something New</span></a>
                    </section>
                <?php } ?>
                <!-- select all users. -->
                <!-- Display the top 5 users with their profile pictures. -->
                <section class="rounded prime-threads-box mt-4 mb-4 px-3 py-3">
                    <h5>Prime Profiles</h5>
                    <section>
                        <?php
                        $topUsers = (new ThreadsClass())->getTopUsers($url[1]);
                        if (count($topUsers) != 0) {
                            foreach ($topUsers as $user) {
                                echo '<section class="d-flex align-middle py-2">';
                                echo '<section class="prime-thread-info-label me-auto d-inline-flex">';
                                echo '<img class="image-header-account img-fluid" src="http://' .
                                    $_SERVER["HTTP_HOST"] .
                                    "/server/uploads/profilePictures/" .
                                    $user["profile_image"] .
                                    '" alt="' .
                                    $user["username"] .
                                    '_Profile_Picture">';
                                echo '<span class="ms-1"><a href="/account/' .
                                    $user["userId"] .
                                    '">';
                                echo $user["username"];
                                echo "</a></span>";
                                echo "</section>";
                                echo '<section class="prime-thread-info-polls-boost">';
                                echo '<span class="me-2">' .
                                    $user["count"] .
                                    '</span><i class="bi bi-arrow-up-circle"></i>';
                                echo "</section>";
                                echo "</section>";
                            }
                        }
                        ?>
                    </section>
                </section>
                <section class="d-flex rounded important-channels mt-4 mb-5 p-4 py-3 flex-column">
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