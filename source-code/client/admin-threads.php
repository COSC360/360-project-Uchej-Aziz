<?php
$url = $_SERVER['REQUEST_URI'];
$url = substr($url, strpos($url, ".") + 1);
if ($url === "php")
	header("Location: /");
?>
<main style="
	width: 75%;
	margin-top: 3rem;
	margin-left: auto;
	margin-right: auto;
  ">
	<section class="row">
		<section class="col-md-2 agenda">
			<h6 class="text-uppercase text-secondary">Panel 🛠️</h6>
			<nav class="mt-3">
				<ul>
					<li>
						<a href="/" class="rounded"><i class="bi bi-house-door"></i><span class="ms-2">Home</span></a>
					</li>
					<li>
						<a href="/admin" class="rounded"><i class="bi bi-bank2"></i><span class="ms-2">Mgmt Portal</span></a>
					</li>
					<li>
						<a href="/admin/users" class="rounded"><i class="bi bi-person-bounding-box"></i><span class="ms-2">Users</span></a>
					</li>
					<li>
						<a href="/admin/threads" class="rounded effective"><i class="bi bi-cloud-plus-fill"></i><span class="ms-2">Threads</span></a>
					</li>
				</ul>
			</nav>
		</section>

		<section class="chief-panel col-md-10 mx-auto mb-4">
			<h3 class="fw-bold mb-3">Thread Finder 🧐</h3>

			<form class="rounded p-3 bg-white">
				<section>
					<label for="search-the-content" class="fw-bold" style="display: block">Thread Name</label>
					<input placeholder="Enter Thread Name Here 😀" class="mt-2 p-2 w-100 discover-input-thread" id="search-the-content" name="search" type="text" />
				</section>
			</form>

			<?php
			require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/AdminClass.class.php';
			$ths = (new AdminClass())->viewThreads([]);
			if (count($ths) === 0) {
				echo '<section class="profile-null-data bg-none glitch-data text-center p-3 scheme-report"><img src="http://' . $_SERVER['HTTP_HOST'] . '/client/img/error-empty-content.svg" alt="content not present at the moment" class="null-data mx-auto" style="display: block"><p class="pt-5">Not found that info</p></section>';
			} else {
			?>

				<section class="overflow-auto table-responsive chief-discover-threads-table">
					<table class="mt-4 table table-striped table-hover chief-panel-threads w-100">
						<thead>
							<tr>
								<th scope="col">Thread ID</th>
								<th scope="col">Title</th>
								<th scope="col">Link</th>
								<th scope="col">Date Created</th>
								<th scope="col">Creator</th>
								<th scope="col">Mode</th>
								<th scope="col">Participants</th>
								<th scope="col">Operation</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($ths as $th) {
								echo "<tr><td scope='row'>" . $th['idThread'] . "</td>";
								echo "<td>" . $th['title'] . "</td>";
								echo '<td><a href="/t/' . $th['link'] . '">/t/' . $th['link'] . '/</a></td>';
								echo "<td>" . $th['created_date'] . "</td>";
								echo '<td><a href="/account/' . $th['ownerId'] . '">' . $th['ownerName'] . '</a></td>';


								if (!$th['isRowHidden'] && !$th['isRowDeleted'])
									echo '<td><span class="rounded p-1 text-light" style="background-color: #5f3dc4">In Effect</span></td>';

								else if ($th['isRowHidden'] && !$th['isRowDeleted'])
									echo '<td><span class="bg-warning rounded p-1 text-light" style="background-color: #495057">Concealed</span></td>';

								else
									echo '<td><span class="bg-dark rounded p-1 text-light">Removed</span></td>';


								echo "<td>" . $th['members'] . "</td>";
								echo "<td>";
								if (!$th['isRowHidden'] && !$th['isRowDeleted'])
									echo '<button class="chief-threads-perform-remove d-flex justify-content-between" data-id="' . $th['idThread'] . '" data-status="delete"><i class="bi bi-trash3"></i>Remove</button><br /><button class="disguise chief-threads-perform-disguise d-flex justify-content-between" data-id="' . $th['idThread'] . '" data-status="hide"><i class="bi bi-cloud-moon-fill"></i>Conceal</button><br />';

								else
									echo "<button class=\"chief-threads-perform-recover d-flex justify-content-between\" data-id=\"" . $th['idThread'] . "\" data-status=\"restore\"><i class=\"bi bi-sunrise-fill\"></i>Recover</button>";


								echo "</td>";
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
				</section>
			<?php } ?>
		</section>
	</section>
</main>