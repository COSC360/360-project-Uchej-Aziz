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
    <section class="col-md-6 mt-5">
      <img src="<?php echo "http://".$_SERVER['HTTP_HOST']; ?>/client/img/error-page-vector.svg"
      class="image-glitch-bearing" alt="Explaining Error Cause">
    </section>
    <section class="col-md-6 mt-5">
      <h1 class="fw-bold" style="color: #495057">Me Oh My...It's a shame</h1>
      <p class="mt-5">Looks like you bumped into one tornado 🌪️ of an error.</p>
      <p>
        The Error Id:
        <span class="text-danger fw-bold">404 - Page Not Found</span>
      </p>
      <a
        href="/"
        class="mt-5 header-figure btn-previous-glitch"
        style="display: block"
      >
        Return to where we came from 💨
      </a>
    </section>
  </section>
</main>
