<?php
$urlSecurity = $_SERVER['REQUEST_URI'];
$urlSecurity = substr($urlSecurity, strpos($urlSecurity, ".") + 1);
if ($urlSecurity === "php")
	header("Location: /");

require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/ThreadsClass.class.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/PostsClass.class.php';
$threadInfo = (new ThreadsClass())->getThread($url[1]);
$currentPost = (new PostsClass())->loadSpecificPost([$url[1], $url[2]]);

?>
<section class="thread-header-bar mb-5">
	<section class="image-thread-background" style="background-image: url('<?php echo 'http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/thread_backgrounds/' . $threadInfo[0]['thread_background']; ?>');">
	</section>


	<section class="bg-light">
		<section style="width: 75%; margin-left: auto; margin-right: auto">
			<section class="d-inline-flex justify-content-center w-50">
				<img class="me-2 image-thread-account img-thumbnail" src="<?php echo 'http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/thread_profile/' . $threadInfo[0]['thread_profile']; ?>" alt="thread-profile-image">
				<section class="py-2">
					<h3 class=""><?php echo $threadInfo[0]["title"] ?></h3>
					<a href="<?php echo "/t" . "/" . "$url[1]" ?>" class="thread-sm-url"><?php echo "t/" . $url[1] ?></a>
				</section>
				<section class="py-2 ms-3">
					<button type="button" class="aboard-thread-btn" data-status="<?php echo $threadInfo[0]["isSubscribed"]; ?>">
						<?php
						if ($threadInfo[0]["isSubscribed"] == 0) {
							echo "Join";
						} else {
							echo "Leave";
						}
						?>
					</button>
				</section>
			</section>
		</section>
	</section>
</section>

<main style="width: 75%; margin-left: auto; margin-right: auto">
	<section class="row">
		<section class="col-md-2 agenda">
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
		<section class="affair-threads mx-auto affair-after-exclusive col-md-6 overflow-auto mb-4">
			<?php
			if (!empty($currentPost)) {
                //print "POST INFO:";
                //print_r($currentPost);
				if ($threadInfo[0]["isRowHidden"] == 1 || $currentPost['isHidden'] == 1) {
			?>
					<section class="bg-danger scheme-report mb-3">
						<section class="d-inline-flex scheme-report-data px-3 py-3 w-100">
							<i class="bi bi-bug-fill my-auto text-center text-light"></i>
							<p class="ms-3 my-auto">This post has been retained from the public by the Admin.<br /><span class="fw-bolder">This is because:</span> The Admin Deemed the Situation as a Violation of Association
								Rules.
							</p>
						</section>
					</section>
			<?php }
			} ?>
			<!-- Normal Content-->
			<?php if (!empty($currentPost)) {
				require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/CommentsClass.class.php';
				echo '<article class="rounded p-4 mb-5">';
				echo '<section class="row">';
				echo '<section class="col-sm-2">';
				echo '<section data-post-id="' . $currentPost['idPost'] . '" class="d-flex justify-content-evenly text-center justify-content-center after-polls flex-md-column flex-sm-row">';
				if ($currentPost['isVoted'] == 0) {
					echo '<i class="bi bi-arrow-up-circle my-auto"></i>';
					echo '<span class="mt-2 mb-2" style="display: block"
						><a href="#">' . $currentPost['numOfVotes'] . '</a></span>';
					echo '<i class="bi bi-arrow-down-circle"></i>';
				} else if ($currentPost['isVoted'] == 1 && $currentPost['typeVote'] == 1) {
					echo '<i class="bi bi-arrow-up-circle my-auto polls-boost"></i>';
					echo '<span class="mt-2 mb-2" style="display: block"
						><a href="#" class="polls-boost">' . $currentPost['numOfVotes'] . '</a></span>';
					echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
				} else if ($currentPost['isVoted'] == 1 && $currentPost['typeVote'] == -1) {
					echo '<i class="bi bi-arrow-up-circle my-auto"></i>';
					echo '<span class="mt-2 mb-2" style="display: block"
						><a href="#" class="polls-deduct">' . $currentPost['numOfVotes'] . '</a></span>';
					echo '<i class="bi bi-arrow-down-circle my-auto polls-deduct"></i>';
				}
				echo '</section>';
				echo '</section>';
				echo '<section class="col-sm-10">';
				echo '<h4><a href="/t/' . $currentPost['link'] . '/' . $currentPost['idPost'] . '">' . $currentPost["postTitle"] . '</a></h4>';
				echo '<p class="null-border">';
				if (is_null($currentPost['image']) && is_null($currentPost['link']) && !is_null($currentPost['content'])) {
					echo $currentPost['content'];
				} else if (!is_null($currentPost['image']) && is_null($currentPost['link']) && is_null($currentPost['content'])) {
					echo '<img src="http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/post_images/' . $currentPost['image'] . '" alt="image-content">';
				} else if (is_null($currentPost['image']) && !is_null($currentPost['media_link']) && is_null($currentPost['content'])) {
					echo '<iframe
						height="300"
						width="100%"
						title="YouTube video player"
						src="' . $currentPost['media_link'] . '"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						frameborder="0"
						allowfullscreen
					  ></iframe
					  >';
				} else if (!is_null($currentPost['image']) && is_null($currentPost['media_link']) && !is_null($currentPost['content'])) {
					echo $currentPost['content'];
					echo '<img src="http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/post_images/' . $currentPost['image'] . '" alt="image-content" class="pt-2">';
				} else if (is_null($currentPost['image']) && !is_null($currentPost['media_link']) && !is_null($currentPost['content'])) {
					echo $currentPost['content'];
					echo '<iframe
						width="100%"
						class="pt-2"
						src="' . $currentPost['media_link'] . '"
						height="300"
						frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						title="YouTube video player"
						allowfullscreen
					  ></iframe>';
				} else if (!is_null($currentPost['image']) && !is_null($currentPost['media_link']) && !is_null($currentPost['content'])) {
					echo $currentPost['content'];
					echo '<img src="http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/post_images/' . $currentPost['image'] . '" alt="image-content" class="pt-2">';
					echo '<iframe
						width="100%"
						class="pt-2"
						src="' . $currentPost['media_link'] . '"
						height="300"
						frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						title="YouTube video player"
						allowfullscreen
					  ></iframe>';
				} else {
					echo $currentPost['content'];
				}
				echo '</p>';
				echo '<section
					class="after-info-box mt-0 d-flex justify-content-between revoke"
				  >';
				echo '<section class="d-flex account-info-short align-middle">';
				echo '<img class="image-header-account img-fluid" src="http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/profilePictures/' . $currentPost['profile_image'] . '" alt="' . $currentPost['username'] . '-Profile-Picture" />';
				echo '<span class="ms-2"><a href="/account/' . $currentPost['ownerId'] . '">' . $currentPost['username'] . '</a> Posted This</span>';
				echo '</section>';
				if ($currentPost['timestamp'] / 60 < 60) {
					echo '<span class="schedule-after" style="display: block"
						>' . ceil($currentPost['timestamp'] / 60) . 'mins ago</span>';
				} else if ($currentPost['timestamp'] / 60 >= 60 && $currentPost['timestamp'] / 60 < 1409) {
					echo '<span class="schedule-after" style="display: block"
						>' . ceil($currentPost['timestamp'] / 3600) . 'hrs ago</span>';
				} else {
					echo '<span class="schedule-after" style="display: block"
						>' . ceil($currentPost['timestamp'] / 86400) . 'days ago</span>';
				}
				echo '<section class="after-info-remarks">';
				echo '<a href="/t/' . $currentPost['link'] . '/' . $currentPost['idPost'] . '"><i class="bi bi-blockquote-left"></i
						><span class="ms-1">' . $currentPost['totalComments'] . '</span></a>';
				echo '</section>';
				echo '</section>';
				if ((isset($_SESSION['IS_AUTHORIZED']) && isset($_SESSION['IS_ADMIN']) && $_SESSION['IS_ADMIN']) || $_SESSION["USERNAME"] == $currentPost["username"]) {
					echo '<section class="mt-2 mb-2">';
					$currentPost['isHidden'] == 0 ? $buttonText = 'Hide' : $buttonText = 'Unhide';
					echo '<button class="me-4 disguise after-disguise" data-post-id="' . $currentPost['idPost'] . '">' . $buttonText . '</button>';
					echo '<button class="remove after-remove" data-post-id="' . $currentPost['idPost'] . '">Remove</button>';
					echo '</section>';
				}
				if ($currentPost['isHidden'] == 0 && $threadInfo[0]['isRowHidden'] == 0) {
			?>
					<section class="respond-after my-3">
						<h6>Comment as <span><a href="<?php echo '/' . 'account/' . $currentPost["currentUserId"] . '' ?>"><?php echo $_SESSION['USERNAME']; ?>.</a></span></h6>
						<textarea class="w-100 after-remark" placeholder="Say something 🗣️"></textarea>
						<button class="btn btn-respond-after btn-sm">Comment</button>
					</section>
				<?php }
				$postComments = (new CommentsClass())->getAllCommentsFromPostId($currentPost["idPost"], 1);
				echo '<section class="after-article-content">';
				foreach ($postComments as $comment) {
					echo '<article class="rounded p-4 px-0">';
					echo '<section class="row">';
					echo '<section class="col-sm-2">';
					echo '<section
								data-comment-id="' . $comment['idComment'] . '"
								class="d-flex justify-content-center justify-content-evenly text-center flex-sm-row flex-md-column remark-polls"
							  >';
					if ($comment['isVoted'] == 0) {
						echo '<i class="bi bi-arrow-up-circle my-auto"></i
										>';
						echo '<span class="mt-2 mb-2" style="display: block"><a href="#">' . $comment['numOfVotes'] . '</a></span>';
						echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
					} else if ($comment['isVoted'] == 1 && $comment['typeVote'] == 1) {
						echo '<i
									class="bi bi-arrow-up-circle polls-boost my-auto"
								  ></i>';
						echo '<span class="mt-2 mb-2" style="display: block"><a href="#" class="polls-boost">' . $comment['numOfVotes'] . '</a></span>';
						echo '<i class="bi bi-arrow-down-circle my-auto"></i>';
					} else if ($comment['isVoted'] == 1 && $comment['typeVote'] == -1) {
						echo '<i class="bi bi-arrow-up-circle my-auto"></i>';
						echo '<span class="mt-2 mb-2" style="display: block"><a href="#" class="polls-deduct">' . $comment['numOfVotes'] . '</a></span>';
						echo '<i
									class="bi bi-arrow-down-circle polls-deduct my-auto"
								  ></i>';
					}
					echo '</section>';
					echo '</section>';
					echo '<section class="col-sm-10">';
					echo '<p class="null-border">';
					echo $comment['content'];
					echo '</p>';
					echo '<section
								class="revoke after-info-box d-flex justify-content-between"
							  >';
					echo '<section
								class="account-info-short d-flex align-middle"
							  >';
					echo '<img class="image-header-account img-fluid" src="http://' . '' . $_SERVER['HTTP_HOST'] . '/server/uploads/profilePictures/' . $comment['profile_image'] . '" alt="' . $comment['username'] . '-Profile-Picture" />';
					echo '<span class="ms-2"><a href="/account/' . $comment["ownerId"] . '">' . $comment["username"] . '</a> replies</span>';
					echo '</section>';
					if ($comment['timestamp'] / 60 < 60) {
						echo '<span class="schedule-after" style="display: block">' . ceil($comment['timestamp'] / 60) . 'mins ago</span>';
					} else if ($comment['timestamp'] / 60 >= 60 && $comment['timestamp'] / 60 < 1409) {
						echo '<span class="schedule-after" style="display: block">' . ceil($comment['timestamp'] / 3600) . 'hrs ago</span>';
					} else {
						echo '<span class="schedule-after" style="display: block">' . ceil($comment['timestamp'] / 86400) . 'days ago</span>';
					}
					echo '</section>';
					echo '<section class="mt-2">';
					if ((isset($_SESSION['IS_AUTHORIZED']) && isset($_SESSION['IS_ADMIN']) && $_SESSION['IS_ADMIN']) || $_SESSION["USERNAME"] == $comment["username"]) {
						echo '<button class="remove remark-remove" data-comment-id="' . $comment['idComment'] . '">Remove</button>';
					}
					echo '</section>';
					echo '</section>';
					echo '</section>';
					echo '</article>';
				}
				echo '</section>';
				echo '</section>';
				echo '</section>';
				echo '</article>';
			} else {
				echo '<section
						class="mt-2 scheme-report p-3 text-center bg-none glitch-data"
					  >'; ?>
				<img src="<?php echo "http://" . $_SERVER['HTTP_HOST']; ?>/client/img/error-empty-content.svg" alt="content not present at the moment" class="null-data mx-auto" style="display: block">
			<?php
				echo '<p class="pt-5">There\'s nothing to return...</p>';
				echo '</section>';
			}
			?>
		</section>
		<section class="col-md-3">
			<section class="rounded after-generate-section text-center">
				<?php
				if (!empty($currentPost))
					if ($threadInfo[0]["isRowHidden"] == 0 && $currentPost['isHidden'] == 0) { ?>
					<section class="rounded after-generate-section text-center"><?php echo '<a style="display: block" href=/t/' . $url[1] . '/create-post>'; ?><i class="bi bi-send-plus"></i><span class="ms-3">Discuss Something New</span></a></section>
				<?php } ?>
			</section>

			<section class="rounded prime-threads-box mt-4 mb-4 px-3 py-3">
				<h5>Prime Profiles</h5>
				<section>
					<?php $topUsers = (new ThreadsClass())->getTopUsers($url[1]);
					if (count($topUsers) != 0) {
						foreach ($topUsers as $user) {
							echo '<section class="d-flex align-middle py-2">';
							echo '<section class="prime-thread-info-label me-auto d-inline-flex">';
							echo '<img class="image-header-account img-fluid" src="http://' . $_SERVER['HTTP_HOST'] . '/server/uploads/profilePictures/' . $user['profile_image'] . '" alt="' . $user['username'] . '_Profile_Picture">';
							echo '<span class="ms-1"><a href="/account/' . $user["userId"] . '">';
							echo $user['username'];
							echo '</a></span>';
							echo '</section>';
							echo '<section class="prime-thread-info-polls-boost">';
							echo '<span class="me-2">' . $user['count'] . '</span><i class="bi bi-arrow-up-circle"></i>';
							echo '</section>';
							echo '</section>';
						}
					}
					?>
				</section>
			</section>

			<section class="d-flex flex-column p-4 py-3 mt-4 mb-5 rounded important-channels">
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