<?php 
	$url = $_SERVER['REQUEST_URI'];
	$url = substr($url, strpos($url, ".") + 1);
	if ($url === "php")
		header("Location: /");
?>
<main
      style="
        width: 75%;
        margin-top: 3rem;
        margin-left: auto;
        margin-right: auto;
      "
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
							  ><i class="bi bi-bank2"></i>
							  <span class="ms-2">Mgmt Portal</span></a
							>
						  </li>
					<?php } ?>
					<li>
						<a href="/search" class="effective rounded"
						  ><i class="bi bi-cloud-plus-fill"></i
						  ><span class="ms-2">Search Something</span></a
						>
					  </li>
					
					</ul>
				</nav>
			  </section>
			  <section class="overflow-auto discover-large-box col-md-9 mx-auto mb-4">
				<form class="discover-box rounded bg-white p-3">
				  <section>
					<label
					  for="search-the-content"
					  class="fw-bold"
					  style="display: block"
					  >Search The Forum</label
					>
					<input
					  name="search"
					  id="search-the-content"
					  type="text"
					  placeholder="Fill In Here ✎"
					  class="mt-2 p-2 w-100 discover-page-input-box"
					/>
				  </section>
				  <section class="mt-3">
					<label
					  for="search-the-content-with-options"
					  class="fw-bold"
					  style="display: block"
					  >Filters</label
					>
					<section class="d-inline-flex mt-1">
					  <section class="me-2">
						<input
						  name="thread-search"
						  id="thread-option-search-explanation"
						  type="checkbox"
						  value="Threads"
						  class="threads-choice"
						  checked
						/>
						<label for="thread-option-search-explanation">Threads</label>
					  </section>
					  <section class="me-2">
						<input
						  name="post-search"
						  id="post-option-search-explanation"
						  type="checkbox"
						  value="Posts"
						  class="posts-choice"
						  disabled
						/>
						<label for="post-option-search-explanation">Posts</label>
					  </section>
					  <section>
						<input
						  name="comments-search"
						  id="remark-option-search-explanation"
						  type="checkbox"
						  value="Comments"
						  class="remarks-choice"
						  disabled
						/>
						<label for="remark-option-search-explanation"
						  >Reactions</label
						>
					  </section>
					</section>
				  </section>
				</form>

				<section class="mt-5">
					<h4>
						Results Returned for '<span
						  class="discover-trademark-paint discover-outcome-inquiry"
						  >All</span
						>'. Option:
						<span class="discover-outcome-choices discover-trademark-paint"
						  >Threads</span
						>.
					  </h4>
					  <section class="discover-outcome-section overflow-auto mt-4">

				<?php 
			
					require_once $_SERVER["DOCUMENT_ROOT"].'/server/controllers/ThreadsClass.class.php';
					
					$ths = (new ThreadsClass())->viewThreads();
					
					if (count($ths) == 0) {
				?>
				<section
                class="scheme-report glitch-data text-center bg-none p-3 mt-2"
              >
						<img src="<?php echo "http://".$_SERVER['HTTP_HOST']; ?>/client/img/error-empty-content.svg" alt="content not available at the moment" class="null-data mx-auto"
						style="display: block">
						<p class="pt-5">There is nothing to return...</p>
					</section>
				<?php 
					} else {
						foreach ($ths as $th) {
							echo '<section class="discover-outcome-thread mb-3 p-3 bg-white">';
							echo '<section class="image-thread-background" style="background-image: url('."http://".''.$_SERVER['HTTP_HOST'].'/server/uploads/thread_backgrounds/'.$th['thread_background_picture'].'");"></section>';
							echo '<section class="image-thread-discover-wrap d-flex">';
							echo '<img class="img-thumbnail image-thread-account" src="http://'.''.$_SERVER['HTTP_HOST'].'/server/uploads/thread_profile/'.$th['thread_cover_picture'].'" alt="Thread account Image">';
							echo '<section>';
							echo '<h3 class="">'.$th['title'].'</h3>';
							echo '<a href="/t/'.$th['link'].'" class="thread-sm-url">t/'.$th['link'].'</a>';
							echo '</section>';
							echo '</section>';
							echo '</section>';
						}
					}
				?>
			</section>
		</section>
	</section>
</section>
</main>