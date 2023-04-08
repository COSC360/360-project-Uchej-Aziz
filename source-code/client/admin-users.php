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
			<h6 class="text-uppercase">Panel 🛠️</h6>
			<nav class="mt-3">
				<ul>
					<li>
						<a href="/" class="rounded"><i class="bi bi-house-door"></i><span class="ms-2">Home</span></a>
					</li>
					<li>
						<a href="/admin" class="rounded"><i class="bi bi-bank2"></i><span class="ms-2">Mgmt Portal</span></a>
					</li>
					<li>
						<a href="/admin/users" class="rounded effective"><i class="bi bi-person-bounding-box"></i><span class="ms-2">Users</span></a>
					</li>
					<li>
						<a href="/admin/threads" class="rounded"><i class="bi bi-cloud-plus-fill"></i><span class="ms-2">Threads</span></a>
					</li>
				</ul>
			</nav>
		</section>

		<section class="col-md-10 chief-panel mx-auto mb-4">
			<h3 class="fw-bold mb-3">User Finder 🧐</h3>

			<form class="bg-white rounded p-3">
				<section>
					<label for="search-the-content" class="fw-bold" style="display: block">Username</label>
					<input placeholder="Enter User's Name Here 😀" class="mt-2 p-2 w-100 discover-input-container" id="search-the-content" name="search" type="text" />
				</section>
			</form>

			<?php
			require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/AdminClass.class.php';
			$users = (new AdminClass())->getUsers([]);
			if (count($users) === 0) {
				echo '<section
						class="profile-null-data text-center p-3 bg-none glitch-data scheme-report"
					  ><img src="http://' . $_SERVER['HTTP_HOST'] . '/client/img/error-empty-content.svg" alt="content not present at the moment" class="null-data mx-auto"
					  style="display: block"><p class="pt-5">Not found that info</p>
					</section>';
			} else {
			?>
				<section class="overflow-auto chief-discover-profiles-table table-responsive">

					<table class="mt-4 table table-striped table-hover w-100">
						<thead>
							<tr>
								<th scope="col">User ID</th>
								<th scope="col">Username</th>
								<th scope="col">Registration Date</th>
								<th scope="col">Email</th>
								<th scope="col">Email Mode</th>
								<th scope="col">Administrator</th>
								<th scope="col">Operation</th>
							</tr>
						</thead>
						<tbody>
							<?php

							foreach ($users as $user) {
								echo "<tr><td scope='row'>" . $user['id'] . "</td>";
								echo "<td><a href='/account/" . $user['id'] . "'>" . $user['username'] . "</a></td>";
								echo "<td>" . $user['regdate'] . "</td>";
								echo "<td>" . $user['email'] . "</td>";
								echo ($user['isUserConfirmed']) ? "<td><span class=\"rounded p-1 text-light\" style=\"background-color: #fab005\">Approved</span></td>" : "<td><span class=\"rounded p-1 text-light\" style=\"background-color: #868e96\">Unaffirmed</span></td>";
								echo ($user['adminStatus']) ? "<td><span class=\"true p-1 text-light rounded chief-profile-mode\">Aye</span></td>" : "<td><span class=\"text-light rounded chief-profile-mode false p-1\">Nay</span></td>";
								echo "<td>";
								echo ($user['status']) ? "<button class=\"chief-profiles-perform-section\" data-id=\"" . $user['id'] . "\" data-status=\"unblock\">Unblock</button><br>" : "<button class=\"chief-profiles-perform-section td-button-block\" data-id=\"" . $user['id'] . "\" data-status=\"block\">Block</button><br>";
								echo ($user['adminStatus']) ? "<button class=\"chief-profiles-perform-chief td-button-demote\" data-id=\"" . $user['id'] . "\" data-status=\"demote-admin\">Delegate Admin</button>" : "<button class=\"chief-profiles-perform-chief\" data-id=\"" . $user['id'] . "\" data-status=\"new-admin\">Promote Admin</button>";
								echo "</td></tr>";
							}

							?>
						</tbody>
					</table>
				</section>
			<?php } ?>
		</section>
	</section>
</main>