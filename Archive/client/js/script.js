$(document).ready(() => {
  let username = "";
  let email = "";
  const contentCollection = {
    "first-phase": {
      fields: {
        fieldNames: ["username"],
        inputFieldPlaceHolders: ["JoLa"],
      },
      systemMessageTitle: "Your Username",
      systemMessageDescription: "Our community guidelines say that your username should not be longer than 8 characters and doesn't include any special characters.",
    },
    "second-phase": {
      fields: {
        fieldNames: ["email"],
        inputFieldPlaceHolders: ["aziz@JoLa.ug"],
      },
      systemMessageTitle: "Your Email",
      systemMessageDescription: "Our community guidelines say that your email should not be longer than 25 characters.",
    },
    "third-phase": {
      fields: {
        fieldNames: ["password", "password"],
        inputFieldPlaceHolders: ["Secret Password", "Repeat Secret Password"],
      },
      systemMessageTitle: "Your Password",
      systemMessageDescription: "We recommend to create a password with minimum length of 8 characters, one uppercase letter, one special symbol.",
    },
  };
  const goNextFunction = (step) => {

    $("form").animate({opacity: 0},
        {
          duration: 1000,
          specialEasing: {
            width: "linear",
            height: "easeOutBounce",
          },
          complete: () => {
            let i;
            $(".intake").remove();
            $(".scheme-report").remove();

            let composition = "";

            switch (step) {
              case "second-phase": {
                $(".move-forward").removeClass("first-phase");
                $(".move-forward").addClass("second-phase");
                for (i = 0; i < contentCollection[step].fields.fieldNames.length; i++) {
                  composition += `<section class="intake mb-4"><label for="${contentCollection[step].fields.fieldNames[i]}Input" class="form-tag text-uppercase">${contentCollection[step].fields.fieldNames[i]}</label><input type="${contentCollection[step].fields.fieldNames[i]}" class="${contentCollection[step].fields.fieldNames[i]}-roster-input" placeholder="${contentCollection[step].fields.inputFieldPlaceHolders[i]}"></section>`;
                }
                $("form").css("opacity", 1);
                $("form").before(`${composition}`);
                break;
              }
              case "third-phase": {
                $(".move-forward").removeClass("second-phase");
                $(".move-forward").addClass("final-phase");
                for (
                    i = 0;
                    i < contentCollection[step].fields.fieldNames.length / 2;
                    i++
                ) {
                  composition += `<section class="intake mb-4"><label for="${
                      contentCollection["third-phase"].fields.fieldNames[i]
                  }Input" class="form-tag text-uppercase">${
                      contentCollection["third-phase"].fields.fieldNames[i]
                  }</label><input type="${
                      contentCollection["third-phase"].fields.fieldNames[i]
                  }" class="${
                      contentCollection["third-phase"].fields.fieldNames[i]
                  }-roster-input" placeholder="${
                      contentCollection["third-phase"].fields
                          .inputFieldPlaceHolders[i]
                  }"></section>
							  <section class="intake mb-4"><label for="${
                      contentCollection["third-phase"].fields.fieldNames[i]
                  }Input" class="form-tag text-uppercase">${
                      contentCollection["third-phase"].fields
                          .inputFieldPlaceHolders[i + 1]
                  }</label><input type="${
                      contentCollection["third-phase"].fields.fieldNames[i]
                  }" class="${
                      contentCollection["third-phase"].fields.fieldNames[i]
                  }-restate-roster-input" placeholder="${
                      contentCollection["third-phase"].fields
                          .inputFieldPlaceHolders[i + 1]
                  }">
							  </section>`;
                }
                $("form").css("opacity", 1);
                $("form").before(`${composition}`);
                $(".move-forward").text("Register");
                break;
              }
              case "final-phase": {
                $("form").remove();
                $(".scheme-report").remove();
                $("h4.mb-5").text("Thanks for Joining Our Community.");
                $("h4.mb-5")
                    .after(`<p class="gain-roster-first-phase">We are almost done. Check your email to verify your account creation.</p>
						  <section class="btn-box text-uppercase w-100 mt-5 mb-4">
							  <a href="/" class="register-take-back-btn">Take me back</a>
						  </section>
						  `);
              }
            }
            if (step !== "final-phase") {
              $("form")
                  .after(`<section class="scheme-report mt-3 routine-info d-inline-flex px-3 py-1 transition-text mb-4">	
							  <section class="align-self-center">
								  <i class="bi bi-info-square-fill"></i>
							  </section>
							  <section class="ms-3 mt-1 align-self-center">
								  <h5>${contentCollection[step].systemMessageTitle}</h5>
								  <p>${contentCollection[step].systemMessageDescription}</p>
							  </section>
						  </section><section class="scheme-report bg-danger d-inline-flex px-3 py-2 transition-text w-100 d-none">
						  
						  <section class="align-self-center">
							  <i class="bi bi-radioactive"></i>
						  </section>
						  <section class="ms-3 mt-1 align-self-center">
							  <h5>Oops...</h5>
							  <p></p>
						  </section>
					  </section>`);
            }
          },
        }
    );
  };

  $(".move-backwards").click((e) => {
    e.preventDefault();
    window.location.href = "/register";
  });

  /* Register Button */
  $(".move-forward").click((e) => {
console.log('Register button clicked');


    let filter
    ;
    e.preventDefault();
    console.log('Prevent Default');
    console.log(e.target.classList.value);
    console.log(e.target.classList[1]);



    switch (e.target.classList[1]) {
      case "first-phase": {
        console.log($(".username-roster-intake").val());
        if (
          $(".username-roster-intake").val().length < 3 ||
          $(".username-roster-intake").val().length > 8
        ) {
          console.log('Username should be between 3 to 8 characters.');
          $(".scheme-report.bg-danger section:last-child p").text(
            "Username should be between 3 to 8 characters."
          );

          $(".scheme-report.bg-danger").removeClass("d-none");
          break;
        }

        const regex = /^[a-z0-9]+$/;
        if (!regex.test($(".username-roster-intake").val())) {
          $(".scheme-report.bg-danger section:last-child p").text(
            "Only small letters and numbers are allowed."
          );

          $(".scheme-report.bg-danger").removeClass("d-none");
          break;
        }

        $.post(
          `http://${$(location).attr(
            "host"
          )}/server/helperClasses/UserHelperClass.class.php`,
          {
            username: $(".username-roster-intake").val(),
          }
        ).done(function (outcome) {
          if (parseInt(outcome["response"]) === 200) {
            $(".scheme-report.bg-danger").addClass("d-none");
            username = $(".username-roster-intake").val();
            goNextFunction("second-phase");
          } else if (parseInt(outcome["response"]) === 403) {
            $(location).prop("href", "/");
          } else {
            $(".scheme-report.bg-danger section:last-child p").text(
              outcome["data"]["message"]
            );

            $(".scheme-report.bg-danger").removeClass("d-none");
          }
        });
        break;
      }
      case "second-phase": {
        console.log($(".email-roster-input").val());
        filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        if (!filter.test($(".email-roster-input").val())) {
          $(".scheme-report.bg-danger section:last-child p").text(
            "Email format is not valid."
          );
          $(".scheme-report.bg-danger").removeClass("d-none");
          return;
        }

        $.post(
          `http://${$(location).attr(
            "host"
          )}/server/helperClasses/UserHelperClass.class.php`,
          {
            email: $(".email-roster-input").val(),
          }
        ).done(function (outcome) {
          if (parseInt(outcome["response"]) === 200) {
            $(".scheme-report.bg-danger").addClass("d-none");
            email = $(".email-roster-input").val();
            goNextFunction("third-phase");
          } else if (parseInt(outcome["response"]) === 403) {
            $(location).prop("href", "/");
          } else {
            $(".scheme-report.bg-danger section:last-child p").text(
              outcome["data"]["message"]
            );

            $(".scheme-report.bg-danger").removeClass("d-none");
          }
        });
        break;
      }
      case "final-phase": {
        filter = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/;
        if (!filter.test($(".password-roster-input").val())) {
          $(".scheme-report.bg-danger section:last-child p").text(
            "Password must be minimum 8 characters, one uppercase letter, and one special symbol."
          );
          $(".scheme-report.bg-danger").removeClass("d-none");
          return;
        }

        if (
          $(".password-roster-input").val() !==
          $(".password-restate-roster-input").val()
        ) {
          $(".scheme-report.bg-danger section:last-child p").text(
            "Passwords don't match."
          );
          $(".scheme-report.bg-danger").removeClass("d-none");
          return;
        }

        $.post(
          `http://${$(location).attr(
            "host"
          )}/server/helperClasses/UserHelperClass.class.php`,
          {
            username: username,
            email: email,
            password: $(".password-roster-input").val(),
            repeatpassword: $(".password-restate-roster-input").val(),
          }//
        ).done(function (outcome) {
          if (parseInt(outcome["response"]) === 200) {
            $(".scheme-report.bg-danger").addClass("d-none");

            goNextFunction("final-phase");
          } else if (parseInt(outcome["response"]) === 403) {
            $(location).prop("href", "/");
          } else {
            $(".scheme-report.bg-danger section:last-child p").text(
              outcome["data"]["message"]
            );

            $(".scheme-report.bg-danger").removeClass("d-none");
          }
        });

        break;
      }
    }
  });

  /* Create Thread Title */
  $(".generate-thread-banner").on("keyup keydown", (e) => {
    let formattedURL = e.target.value;

    const regex = /^[a-zA-Z0-9\s]+$/;

    if (!regex.test(formattedURL) && formattedURL !== "") {
      $(".generate-thread-data .scheme-report.glitch p").text(
        "Title shouldn't contain numbers or special characters."
      );
      $(".generate-thread-data .scheme-report.glitch p").removeClass("d-none");

      if (e.key !== "Backspace") {
        return e.preventDefault();
      }
    }

    if (formattedURL.length > 12) {
      $(".generate-thread-data .scheme-report.glitch p").text(
        "* Title should contain less than 12 characters."
      );
      $(".generate-thread-data .scheme-report.glitch p").removeClass("d-none");

      if (e.key !== "Backspace") {
        return e.preventDefault();
      }
    }

    if (formattedURL.length <= 12)
      $(".generate-thread-data .scheme-report.glitch p").addClass("d-none");

    formattedURL = formattedURL.split("-").join(" ");
    formattedURL = formattedURL.split(" ").join("-").toLowerCase();

    $(".generate-thread-propose-link").text(formattedURL);
  });

  /* Create Thread */
  $(".btn-generate-thread").click((e) => {
    e.preventDefault();
    const formattedURL = $(".generate-thread-banner").val();
    const regex = /^[a-zA-Z0-9\s]+$/;

    if (!regex.test(formattedURL)) {
      $("span.glitch-report").text(
        "Title shouldn't contain special characters."
      );
      $(".scheme-report").removeClass("d-none");
      return;
    }

    if (formattedURL.length > 12) {
      $("span.glitch-report").text(
        "Title should contain less than 12 characters."
      );
      $(".scheme-report").removeClass("d-none");
      return;
    }

    if (formattedURL.length <= 12) $(".scheme-report").addClass("d-none");

    let URL = formattedURL.split("-").join(" ");
    URL = formattedURL.split(" ").join("-").toLowerCase();

    const form_data = new FormData();
    form_data.append(
      "threadBackground",
      $(".generate-thread-transfer-wrap").get(0).files[0]
    );
    form_data.append(
      "threadProfile",
      $(".generate-thread-transfer-image").get(0).files[0]
    );
    form_data.append("title", formattedURL);
    form_data.append("url", URL);
    for (var pair of form_data.entries()) {
      console.log(pair[0]+ ', ' + pair[1]);
    }

    $.ajax({
      url: `http://${$(location).attr(
        "host"
      )}/server/helperClasses/ThreadHelperClass.class.php`,
      type: "POST",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: (outcome) => {
        if (parseInt(outcome["response"]) === 200) {
          $(".scheme-report").addClass("d-none");
          $(location).prop("href", `/t/${URL}`);
        } else if (parseInt(outcome["response"]) === 403) {
          $(location).prop("href", "/");
        } else {
          $(".scheme-report").removeClass("d-none");
          $(".scheme-report section:last-child p").text(
            outcome["data"]["message"]
          );
        }
      },
    });
  });

  /* Create Post */
  $(".btn-generate-after").click((e) => {
    e.preventDefault();
    const postTitle = $(".generate-after-label").val();
    const youtubeLink = $(".generate-after-text-link").val();
    const titleRegex = /^[a-zA-Z0-9\s]+$/;
    const youtubeRegex =
        /^(https|http):\/\/(?:www\.)?youtube.com\/embed\/[A-z0-9]+$/;

    // Title validation
    if (postTitle.length === 0) {
      $("span.glitch-report").text("You cannot have an empty title.");
      $(".scheme-report").removeClass("d-none");
      return;
    } else if (postTitle.length < 5 && !titleRegex.test(postTitle)) {
      $("span.glitch-report").text(
        "The post title should be at least 5 characters and cannot contain any special characters."
      );
      $(".scheme-report").removeClass("d-none");
      return;
    } else if (postTitle.length > 75 && !titleRegex.test(postTitle)) {
      $("span.glitch-report").text(
        "The post title should be less than 75 characters and cannot contain any special characters."
      );
      $(".scheme-report").removeClass("d-none");
      return;
    } else if (postTitle.length < 5) {
      $("span.glitch-report").text(
        "The post title should be at least 5 characters."
      );
      $(".scheme-report").removeClass("d-none");
      return;
    } else if (postTitle.length > 75) {
      $("span.glitch-report").text(
        "The post title should be less than 75 characters."
      );
      $(".scheme-report").removeClass("d-none");
      return;
    } else if (!titleRegex.test(postTitle)) {
      $("span.glitch-report").text(
        "The post title shouldn't contain special characters."
      );
      $(".scheme-report").removeClass("d-none");
      return;
    } else {
      $(".scheme-report").addClass("d-none");
    }

    if (youtubeLink.length > 0 && !youtubeRegex.test(youtubeLink)) {
      $("span.glitch-report").text(
        'The YouTube link is invalid. It should contain "embed" in the link.'
      );
      $(".scheme-report").removeClass("d-none");
      return;
    }

    if (
      $(".generate-after-image").get(0).files.length === 0 &&
      youtubeLink.length === 0 &&
      $(".generate-after-text").val().length === 0
    ) {
      $("span.glitch-report").text(
        "Post Body Text cannot remain empty if you are not uploading an image or a YouTube link."
      );
      $(".scheme-report").removeClass("d-none");
      return;
    }

    const threadUrl = window.location.pathname.split("/")[2];

    const form_data = new FormData();
    form_data.append("postTitle", postTitle);
    form_data.append("postBody", $(".generate-after-text").val());
    form_data.append("postImage", $(".generate-after-image").get(0).files[0]);
    form_data.append("postYoutubeLink", youtubeLink);
    form_data.append("threadUrl", threadUrl);
    console.log(form_data);

    $.ajax({
      url: `http://${$(location).attr(
        "host"
      )}/server/helperClasses/PostHelperClass.class.php`,
      type: "POST",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: (outcome) => {
        console.log(outcome);
        if (parseInt(outcome["response"]) === 200) {
          $(".scheme-report").addClass("d-none");
          $(location).prop("href", `/t/${threadUrl}`);
        } else if (parseInt(outcome["response"]) === 403) {
          $(location).prop("href", "/");
        } else {
          $(".scheme-report").removeClass("d-none");
          $(".scheme-report section:last-child p").text(
            outcome["data"]["message"]
          );
        }
      },
    });
  });

  /* Join Thread */
  $(".aboard-thread-btn").click((e) => {
    e.preventDefault();
    const threadUrl = window.location.pathname.split("/")[2];
    const form_data = new FormData();
    form_data.append("threadUrl", threadUrl);
    form_data.append("dataStatus", $(".aboard-thread-btn").data("status"));

    $.ajax({
      url: `http://${$(location).attr(
        "host"
      )}/server/helperClasses/ThreadHelperClass.class.php`,
      type: "POST",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: (outcome) => {
        console.log(outcome);
        if (parseInt(outcome["response"]) === 200) {
          if ($(".aboard-thread-btn").data("status") === 0) {
            $(".aboard-thread-btn").data("status", 1);
            $(".aboard-thread-btn").text("Leave");
          } else {
            $(".aboard-thread-btn").data("status", 0);
            $(".aboard-thread-btn").text("Join");
          }
        } else if (parseInt(outcome["response"]) === 403) {
          $(location).prop("href", "/");
        }
      },
    });
  });

  /* Sort Posts by Top Votes or Newest In Thread*/
  $(".prime-posts-arrange, .appended-posts-arrange").click((e) => {
    e.preventDefault();
    const threadUrl = window.location.pathname.split("/")[2];
    const sortType =
        $(e.target).text().trim() === "Top"
            ? $(".prime-posts-arrange").text().trim()
            : $(".appended-posts-arrange").text().trim();
    $.ajax({
      url: `http://${$(location).attr(
        "host"
      )}/server/helperClasses/PostHelperClass.class.php`,
      dataType: "json",
      contentType: "application/json;charset=utf-8",
      type: "GET",
      data: {
        threadUrl: threadUrl,
        sortType: sortType,
      },
      success: function (outcome) {
        $("article").remove();
        $(".after-outcome-section").html("");
        if (
          parseInt(outcome["response"]) !== 400 &&
          !jQuery.isEmptyObject(outcome)
        ) {
          $.each(outcome, (_, component) => {
            console.log(component);
            let outcome = `<article class="rounded p-4 mb-5">`;
            outcome += `<section class="row">`;
            outcome += `<section class="col-md-2">`;
            outcome += `<section class="d-flex flex-md-column flex-sm-row justify-content-center justify-content-evenly text-center after-polls" data-post-id="${component["idPost"]}">`;
            if (component["isVoted"] === 0) {
              outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
              outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#">${component["numOfVotes"]}</a></span>`;
              outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
            } else if (
              component["isVoted"] === 1 &&
              component["typeVote"] === 1
            ) {
              outcome += `<i class="bi bi-arrow-up-circle polls-boost my-auto"></i>`;
              outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-boost">${component["numOfVotes"]}</a></span>`;
              outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
            } else if (
              component["isVoted"] === 1 &&
              component["typeVote"] === -1
            ) {
              outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
              outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-deduct">${component["numOfVotes"]}</a></span>`;
              outcome += `<i class="bi bi-arrow-down-circle my-auto polls-deduct"></i>`;
            }
            outcome += `</section></section><section class="col-sm-10">`;
            outcome += `<h4><a href="/t/${component["link"]}/${component["idPost"]}">${component["title"]}</a></h4>`;
            outcome += `<p class="null-border">`;
            if (
              component["image"] == null &&
              component["media_link"] == null &&
              component["content"] != null
            ) {
              outcome += `${component["content"]}`;
            } else if (
              component["image"] != null &&
              component["media_link"] == null &&
              component["content"] == null
            ) {
              outcome += `<img src="http://${$(location).attr(
                "host"
              )}/server/uploads/post_images/${
                component["image"]
              }" alt="content-img">`;
            } else if (
              component["image"] == null &&
              component["media_link"] != null &&
              component["content"] == null
            ) {
              outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else if (
              component["image"] != null &&
              component["media_link"] == null &&
              component["content"] != null
            ) {
              outcome += `${component["content"]}`;
              outcome += `<img src="http://${$(location).attr(
                "host"
              )}/server/uploads/post_images/${
                component["image"]
              }" alt="content-img">`;
            } else if (
              component["image"] == null &&
              component["media_link"] != null &&
              component["content"] != null
            ) {
              outcome += `${component["content"]}`;
              outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else if (
              component["image"] != null &&
              component["media_link"] != null &&
              component["content"] != null
            ) {
              outcome += `${component["content"]}`;
              outcome += `<img src="http://${$(location).attr(
                "host"
              )}/server/uploads/post_images/${
                component["image"]
              }" alt="content-img">`;
              outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else {
              outcome += component["content"];
            }
            outcome += `</p>`;
            outcome += `<section class="after-info-box revoke d-flex justify-content-between mt-0"><section class="account-info-short d-flex align-middle">`;
            outcome += `<img class="img-fluid my-auto image-header-account" src="http://${$(
              location
            ).attr("host")}/server/uploads/user_images/${
              component["profile_image"]
            }" alt="${component["username"]}-account-image"/>`;
            outcome += `<span class="ms-2">Posted by <a href="/account/${component["ownerId"]}">${component["username"]}</a></span>`;
            outcome += `</section>`;
            if (component["timestamp"] / 60 < 60) {
              outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                component["timestamp"] / 60
              )}m ago</span>`;
            } else if (
              component["timestamp"] / 60 >= 60 &&
              component["timestamp"] / 60 < 1409
            ) {
              outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                component["timestamp"] / 3600
              )}h ago</span>`;
            } else {
              outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                component["timestamp"] / 86400
              )}d ago</span>`;
            }
            outcome += `<section class="after-info-remarks">`;
            outcome += `<a href="/t/${component["link"]}/${component["idPost"]}"><i class="bi bi-blockquote-left"></i><span class="ms-1">${component["totalComments"]}</span></a>`;
            outcome += `</section>`;
            outcome += `</section>`;
            if (component["isAdmin"] === 1 || component["isOwner"] === 1) {
              outcome += `<section class="mt-2">`;
              const hideButtonText =
                  component["isHidden"] === 1 ? "Unhide" : "Hide";
              outcome += `<button class="disguise me-4 after-disguise" data-post-id="${component["idPost"]}">${hideButtonText}</button>`;
              outcome += `<button class="remove after-remove" data-post-id="${component["idPost"]}">Delete</button>`;
              outcome += `</section>`;
            }
            component["comments"].forEach((remark) => {
              outcome += `<article class="rounded p-4 px-0">`;
              outcome += `<section class="row">`;
              outcome += `<section class="col-sm-2">`;
              outcome += `<section class="d-flex flex-md-column flex-sm-row justify-content-center justify-content-evenly text-center remark-polls" data-comment-id="${remark["idComment"]}">`;
              if (remark["isVoted"] === 0) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#">${remark["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (remark["isVoted"] === 1 && remark["typeVote"] === 1) {
                outcome += `<i class="bi bi-arrow-up-circle polls-boost my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-boost">${remark["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (remark["isVoted"] === 1 && remark["typeVote"] === -1) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-deduct">${remark["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle polls-deduct my-auto"></i>`;
              }
              outcome += `</section>`;
              outcome += `</section>`;
              outcome += `<section class="col-sm-10">`;
              outcome += `<p class="null-border">${remark["content"]}</p>`;
              outcome += `<section class="after-info-box revoke d-flex justify-content-between">`;
              outcome += `<section class="account-info-short d-flex align-middle">`;
              outcome += `<img class="img-fluid my-auto image-header-account" src="http://${$(
                location
              ).attr("host")}/server/uploads/user_images/${
                remark["profile_image"]
              }" alt="${remark["username"]}-account-image"/>`;
              outcome += `<span class="ms-2"><a href="/account/${remark["ownerId"]}">${remark["username"]}</a> replied</span>`;
              outcome += `</section>`;
              if (remark["timestamp"] / 60 < 60) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  remark["timestamp"] / 60
                )}m ago</span>`;
              } else if (
                remark["timestamp"] / 60 >= 60 &&
                remark["timestamp"] / 60 < 1409
              ) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  remark["timestamp"] / 3600
                )}h ago</span>`;
              } else {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  remark["timestamp"] / 86400
                )}d ago</span>`;
              }
              outcome += `</section>`;
              if (remark["isAdmin"] === 1 || remark["isOwner"] === 1) {
                outcome += `<section class="mt-2">`;
                outcome += `<button class="remove remark-remove" data-comment-id="${remark["idComment"]}">Delete</button>`;
                outcome += `</section>`;
              }
              outcome += `</section>`;
              outcome += `</section>`;
              outcome += `</article>`;
            });
            outcome += `</section>`;
            outcome += `</section>`;
            outcome += `</article>`;
            $(".after-outcome-section").append(outcome);
          });

        }
      },
    });
  });

  /* Search Posts on Thread */
  $(".discover-thread").on("keydown", (e) => {
    if (e.key === "Enter") e.preventDefault();
  });

  $(".discover-thread").on("input", (e) => {
    e.preventDefault();
    $.ajax({
      url: `http://${$(location).attr(
        "host"
      )}/server/helperClasses/PostHelperClass.class.php`,
      dataType: "json",
      contentType: "application/json;charset=utf-8",
      type: "GET",
      data: {
        query: e.target.value,
        threadUrl: window.location.pathname.split("/")[2],
        postSearch: true,
      },
      success: function (outcome) {
        $("article").remove();
        $(".after-outcome-section").html("");
        if (
          parseInt(outcome["response"]) !== 400 &&
          !jQuery.isEmptyObject(outcome)
        ) {
          $.each(outcome, (_, component) => {
            console.log(component);
            let outcome = `<section class="search-result post bg-white mb-3 p-3"><section class="row"><section class="col-sm-2"><section class="d-flex flex-md-column flex-sm-row justify-content-center justify-content-evenly text-center after-polls" data-post-id="${component["idPost"]}">`;
            if (component["isVoted"] === 0) {
              outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
              outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#">${component["numOfVotes"]}</a></span>`;
              outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
            } else if (
              component["isVoted"] === 1 &&
              component["typeVote"] === 1
            ) {
              outcome += `<i class="bi bi-arrow-up-circle polls-boost my-auto"></i>`;
              outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-boost">${component["numOfVotes"]}</a></span>`;
              outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
            } else if (
              component["isVoted"] === 1 &&
              component["typeVote"] === -1
            ) {
              outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
              outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-deduct">${component["numOfVotes"]}</a></span>`;
              outcome += `<i class="bi bi-arrow-down-circle my-auto polls-deduct"></i>`;
            }
            outcome += `</section></section><section class="col-sm-10">`;
            outcome += `<h4><a href="/t/${component["media_link"]}/${component["idPost"]}">${component["title"]}</a></h4>`;
            outcome += `<p class="null-border">`;
            if (
              component["image"] == null &&
              component["media_link"] == null &&
              component["content"] != null
            ) {
              outcome += `${component["content"]}`;
            } else if (
              component["image"] != null &&
              component["media_link"] == null &&
              component["content"] == null
            ) {
              outcome += `<img src="http://${$(location).attr(
                "host"
              )}/server/uploads/post_images/${
                component["image"]
              }" alt="content-img">`;
            } else if (
              component["image"] == null &&
              component["media_link"] != null &&
              component["content"] == null
            ) {
              outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else if (
              component["image"] != null &&
              component["media_link"] == null &&
              component["content"] != null
            ) {
              outcome += `${component["content"]}`;
              outcome += `<img src="http://${$(location).attr(
                "host"
              )}/server/uploads/post_images/${
                component["image"]
              }" alt="content-img">`;
            } else if (
              component["image"] == null &&
              component["media_link"] != null &&
              component["content"] != null
            ) {
              outcome += `${component["content"]}`;
              outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else if (
              component["image"] != null &&
              component["media_link"] != null &&
              component["content"] != null
            ) {
              outcome += `${component["content"]}`;
              outcome += `<img src="http://${$(location).attr(
                "host"
              )}/server/uploads/post_images/${
                component["image"]
              }" alt="content-img">`;
              outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else {
              outcome += component["content"];
            }
            outcome += `</p>`;

            outcome += `<section class="after-info-box revoke d-flex justify-content-between mt-0"><section class="account-info-short d-flex align-middle">`;
            outcome += `<img class="img-fluid my-auto image-header-account" src="http://${$(
              location
            ).attr("host")}/server/uploads/user_images/${
              component["profile_image"]
            }" alt="${component["username"]}-account-image"/>`;
            outcome += `<span class="ms-2">Posted by <a href="/account/${component["ownerId"]}">${component["username"]}</a></span>`;
            outcome += `</section>`;

            if (component["timestamp"] / 60 < 60) {
              outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                component["timestamp"] / 60
              )}m ago</span>`;
            } else if (
              component["timestamp"] / 60 >= 60 &&
              component["timestamp"] / 60 < 1409
            ) {
              outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                component["timestamp"] / 3600
              )}h ago</span>`;
            } else {
              outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                component["timestamp"] / 86400
              )}d ago</span>`;
            }
            outcome += `<section class="after-info-remarks">`;
            outcome += `<a href="/t/${component["link"]}/${component["idPost"]}"><i class="bi bi-blockquote-left"></i><span class="ms-1">${component["totalComments"]}</span></a>`;
            outcome += `</section>`;
            outcome += `</section>`;
            if (component["isAdmin"] === 1 || component["isOwner"] === 1) {
              outcome += `<section class="mt-2">`;
              const hideButtonText =
                  component["isHidden"] === 1 ? "Unhide" : "Hide";
              outcome += `<button class="disguise me-4 after-disguise" data-post-id="${component["idPost"]}">${hideButtonText}</button>`;
              outcome += `<button class="remove after-remove" data-post-id="${component["idPost"]}">Delete</button>`;
              outcome += `</section>`;
            }
            component["comments"].forEach((remark) => {
              outcome += `<article class="rounded p-4 px-0">`;
              outcome += `<section class="row">`;
              outcome += `<section class="col-sm-2">`;
              outcome += `<section class="d-flex flex-md-column flex-sm-row justify-content-center justify-content-evenly text-center remark-polls" data-comment-id="${remark["idComment"]}">`;
              if (remark["isVoted"] === 0) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#">${remark["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (remark["isVoted"] === 1 && remark["typeVote"] === 1) {
                outcome += `<i class="bi bi-arrow-up-circle polls-boost my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-boost">${remark["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (remark["isVoted"] === 1 && remark["typeVote"] === -1) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-deduct">${remark["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle polls-deduct my-auto"></i>`;
              }
              outcome += `</section>`;
              outcome += `</section>`;
              outcome += `<section class="col-sm-10">`;
              outcome += `<p class="null-border">${remark["content"]}</p>`;
              outcome += `<section class="after-info-box revoke d-flex justify-content-between">`;
              outcome += `<section class="account-info-short d-flex align-middle">`;
              outcome += `<img class="img-fluid my-auto image-header-account" src="http://${$(
                location
              ).attr("host")}/server/uploads/user_images/${
                remark["profile_image"]
              }" alt="${remark["username"]}-account-image"/>`;
              outcome += `<span class="ms-2"><a href="/account/${remark["ownerId"]}">${remark["username"]}</a> replied</span>`;
              outcome += `</section>`;
              if (remark["timestamp"] / 60 < 60) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  remark["timestamp"] / 60
                )}m ago</span>`;
              } else if (
                remark["timestamp"] / 60 >= 60 &&
                remark["timestamp"] / 60 < 1409
              ) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  remark["timestamp"] / 3600
                )}h ago</span>`;
              } else {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  remark["timestamp"] / 86400
                )}d ago</span>`;
              }
              outcome += `</section>`;
              if (remark["isAdmin"] === 1 || remark["isOwner"] === 1) {
                outcome += `<section class="mt-2">`;
                outcome += `<button class="remove remark-remove" data-comment-id="${remark["idComment"]}">Delete</button>`;
                outcome += `</section>`;
              }
              outcome += `</section>`;
              outcome += `</section>`;
              outcome += `</article>`;
            });
            outcome += `</section>`;
            outcome += `</section>`;
            outcome += `</article>`;
            $(".after-outcome-section").append(outcome);
          });

        }
      },
    });
  });

  /* Delete Post */
  $(document).on("click", ".after-remove", (e) => {
    e.preventDefault();
    let postId = $(e.target).attr("data-post-id");
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/PostHelperClass.class.php`,
      {
        postId: postId,
        postDelete: true,
      }
    ).done((_) => {
      window.location = `/t/${window.location.pathname.split("/")[2]}`;
    });
  });

  /* Hide(Disable) Post */
  $(document).on("click", ".after-disguise", (e) => {
    e.preventDefault();
    let buttonText = $(e.target).text().trim().toLowerCase();
    let postId = $(e.target).attr("data-post-id");
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/PostHelperClass.class.php`,
      {
        postId: postId,
        hidePost: true,
        buttonText: buttonText,
      }
    ).done((outcome) => {
      if (parseInt(outcome["response"]) === 200) {
        $(e.target).text(outcome["changeButtonText"]);
      }
    });
  });

  /* Delete Comment */
  $(document).on("click", ".remark-remove", (e) => {
    e.preventDefault();
    let commentId = $(e.target).attr("data-comment-id");
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/CommentHelperClass.class.php`,
      {
        commentId: commentId,
        removeCommentById: true,
      }
    ).done((outcome) => {});
  });

  /* Insert Comment */
  $(document).on("click", ".btn-respond-after", (e) => {
    e.preventDefault();
    let splitUrl = window.location.pathname.split("/");
    let threadUrl = splitUrl[2];
    let postId = splitUrl[3];
    let commentBody = $(".after-remark").val();
    let formData = new FormData();
    formData.append("commentBody", commentBody);
    formData.append("postId", postId);
    formData.append("threadUrl", threadUrl);
    $.ajax({
      url: `http://${$(location).attr(
        "host"
      )}/server/helperClasses/CommentHelperClass.class.php`,
      type: "POST",
      data: formData,
      processData: false,
      cache: false,
      contentType: false,
      success: (outcome) => {
        $(".after-remark").val("");
      },
    });
  });

  /* Create Post Title */
  $(".generate-after-label").on("keyup, keydown", (e) => {
    const postTitle = e.target.value;
    const regex = /^[a-zA-Z0-9\s]+$/;
    if (!regex.test(postTitle) && postTitle !== "") {
      $(".generate-after-data .scheme-report.glitch p").text(
        "* Title shouldn't contain numbers or special characters."
      );
      $(".generate-after-data .scheme-report.glitch p").removeClass("d-none");

      if (e.key !== "Backspace") {
        return e.preventDefault();
      }
    }

    if (postTitle.length > 75) {
      $(".generate-after-data .scheme-report.glitch p").text(
        "* Title should contain less than 75 characters."
      );
      $(".generate-after-data .scheme-report.glitch p").removeClass("d-none");

      if (e.key !== "Backspace") {
        return e.preventDefault();
      }
    }

    if (postTitle.length <= 75)
      $(".generate-after-data .scheme-report.glitch p").addClass("d-none");
  });

  $(".generate-thread-transfer-image").change((event) => {
    if (event.target.files.length > 0) {
      const src = URL.createObjectURL(event.target.files[0]);
      const preview = $(".account-thread-generate-peek");
      preview.attr("src", src);
      preview.addClass("generate-thread-account-image");
      preview.removeClass("d-none");
    }
  });

  $(".generate-after-image").change((event) => {
    if (event.target.files.length > 0) {
      const src = URL.createObjectURL(event.target.files[0]);
      const preview = $(".account-after-generate-peek");
      preview.attr("src", src);
      preview.addClass("generate-after-wrap-image");
      preview.removeClass("d-none");
    }
  });

  $(".generate-thread-transfer-wrap").change((event) => {
    if (event.target.files.length > 0) {
      const src = URL.createObjectURL(event.target.files[0]);
      const preview = $(".account-thread-generate-wrap");
      preview.attr("src", src);
      preview.removeClass("d-none");
    }
  });

  $(".discover-page-input-box").on("keydown", (e) => {
    if (e.key === "Enter") e.preventDefault();
  });

  $(".discover-page-input-box").on("input", (e) => {
    e.preventDefault();
    if (e.target.value.length === 0) {
      $(".discover-outcome-inquiry").text("All");
    } else {
      $(".discover-outcome-inquiry").text(e.target.value);
    }

    if (
      ($(".threads-choice").prop("checked") ||
        !$(".threads-choice").prop("checked")) &&
      !$(".posts-choice").prop("checked") &&
      !$(".remarks-choice").prop("checked")
    ) {
      $.ajax({
        url: `http://${$(location).attr(
          "host"
        )}/server/helperClasses/ThreadHelperClass.class.php`,
        dataType: "json",
        contentType: "application/json;charset=utf-8",
        type: "GET",
        data: {
          query: e.target.value,
          threadSearch: true,
        },
        success: function (outcome) {
          $(".discover-outcome-section").html("");
          if (
            parseInt(outcome["response"]) !== 400 &&
            !jQuery.isEmptyObject(outcome)
          ) {
            $.each(outcome, (_, component) => {
              let outcome = `<section class="search-result thread mb-3 p-3 bg-white">`;
              const url = `http://${$(location).attr(
                  "host"
              )}/server/uploads/thread_backgrounds/${
                  component["thread_background_picture"]
              }`;
              outcome += `<section class="image-thread-background ${component["link"]}"></section>`;
              outcome += `<section class="image-thread-discover-wrap d-flex">`;
              outcome += `<img class="image-thread-account img-thumbnail" src="http://${$(
                location
              ).attr("host")}/server/uploads/thread_profile/${
                component["thread_cover_picture"]
              }" alt="thread_profile_picture"/>`;
              outcome += `<section>`;
              outcome += `<h3 class="">${component["title"]}</h3>`;
              outcome += `<a href="/t/${component["link"]}" class="thread-sm-url">t/${component["link"]}</a>`;
              outcome += `</section></section></section>`;
              $(".discover-outcome-section").append(outcome);
              $(`.${component["link"]}`).css(
                "backgroundImage",
                'url("' + url + '")'
              );
            });
            return;
          }
          $(".discover-outcome-section").html(
            `<section class="scheme-report glitch-data text-center bg-none p-3 mt-2"><img src="http://${$(
              location
            ).attr(
              "host"
            )}/client/img/error-empty-content.svg" alt="no content available" class="d-block null-data mx-auto"><p class="pt-5">It's a little bit lonely here. We couldn't find anything...</p></section>`
          );

        },
      });
    } else if (
      $(".posts-choice").prop("checked") &&
      !$(".remarks-choice").prop("checked") &&
      ($(".threads-choice").prop("checked") ||
        !$(".threads-choice").prop("checked"))
    ) {
      $.ajax({
        url: `http://${$(location).attr(
          "host"
        )}/server/helperClasses/PostHelperClass.class.php`,
        dataType: "json",
        contentType: "application/json;charset=utf-8",
        type: "GET",
        data: {
          query: e.target.value,
          postSearch: true,
        },
        success: function (outcome) {
          $(".discover-outcome-section").html("");
          if (
            parseInt(outcome["response"]) !== 400 &&
            !jQuery.isEmptyObject(outcome)
          ) {
            $.each(outcome, (_, component) => {
              let outcome = `<section class="search-result post bg-white mb-3 p-3"><section class="row"><section class="col-sm-2"><section class="d-flex flex-md-column flex-sm-row justify-content-center justify-content-evenly text-center after-polls" data-post-id="${component["idPost"]}">`;

              if (component["isVoted"] === 0) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#">${component["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (
                component["isVoted"] === 1 &&
                component["typeVote"] === 1
              ) {
                outcome += `<i class="bi bi-arrow-up-circle polls-boost my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-boost">${component["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (
                component["isVoted"] === 1 &&
                component["typeVote"] === -1
              ) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-deduct">${component["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto polls-deduct"></i>`;
              }
              outcome += `</section></section><section class="col-sm-10">`;
              outcome += `<h4><a href="/t/${component["link"]}/${component["idPost"]}">${component["title"]}</a></h4>`;
              outcome += `<p class="null-border">`;
              if (
                component["image"] == null &&
                component["media_link"] == null &&
                component["content"] != null
              ) {
                outcome += `${component["content"]}`;
              } else if (
                component["image"] != null &&
                component["media_link"] == null &&
                component["content"] == null
              ) {
                outcome += `<img src="http://${$(location).attr(
                  "host"
                )}/server/uploads/post_images/${
                  component["image"]
                }" alt="content-img">`;
              } else if (
                component["image"] == null &&
                component["media_link"] != null &&
                component["content"] == null
              ) {
                outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
              } else if (
                component["image"] != null &&
                component["media_link"] == null &&
                component["content"] != null
              ) {
                outcome += `${component["content"]}`;
                outcome += `<img src="http://${$(location).attr(
                  "host"
                )}/server/uploads/post_images/${
                  component["image"]
                }" alt="content-img">`;
              } else if (
                component["image"] == null &&
                component["media_link"] != null &&
                component["content"] != null
              ) {
                outcome += `${component["content"]}`;
                outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
              } else if (
                component["image"] != null &&
                component["media_link"] != null &&
                component["content"] != null
              ) {
                outcome += `${component["content"]}`;
                outcome += `<img src="http://${$(location).attr(
                  "host"
                )}/server/uploads/post_images/${
                  component["image"]
                }" alt="content-img">`;
                outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
              } else {
                outcome += component["content"];
              }
              outcome += `</p>`;

              outcome += `<section class="after-info-box revoke d-flex justify-content-between mt-0"><section class="account-info-short d-flex align-middle">`;
              outcome += `<img class="img-fluid my-auto image-header-account" src="http://${$(
                location
              ).attr("host")}/server/uploads/profilePictures/${
                component["profile_image"]
              }" alt="${component["username"]}-account-image"/>`;
              outcome += `<span class="ms-2">Posted by <a href="/account/${component["ownerId"]}">${component["username"]}</a></span>`;
              outcome += `</section>`;

              if (component["timestamp"] / 60 < 60) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  component["timestamp"] / 60
                )}m ago</span>`;
              } else if (
                component["timestamp"] / 60 >= 60 &&
                component["timestamp"] / 60 < 1409
              ) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  component["timestamp"] / 3600
                )}h ago</span>`;
              } else {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  component["timestamp"] / 86400
                )}d ago</span>`;
              }
              outcome += `<section class="after-info-remarks">`;
              outcome += `<a href="/t/${component["link"]}/${component["idPost"]}"><i class="bi bi-blockquote-left"></i><span class="ms-1">${component["totalComments"]}</span></a>`;
              outcome += `</section></section><section class="mt-2"><button class="disguise me-4 after-disguise">Hide</button><button class="remove thread-remove">Delete</button></section>
							  </section>
						  </section>
					  </section>`;

              $(".discover-outcome-section").append(outcome);
            });
            return;
          }
          $(".discover-outcome-section").html(
            `<section class="scheme-report glitch-data text-center bg-none p-3 mt-2"><img src="http://${$(
              location
            ).attr(
              "host"
            )}/client/img/error-empty-content.svg" alt="no content available" class="d-block null-data mx-auto"><p class="pt-5">It's a little bit lonely here. We couldn't find anything...</p></section>`
          );

        },
      });
    } else if (
      !$(".posts-choice").prop("checked") &&
      $(".remarks-choice").prop("checked") &&
      ($(".threads-choice").prop("checked") ||
        !$(".threads-choice").prop("checked"))
    ) {
      $.ajax({
        url: `http://${$(location).attr(
          "host"
        )}/server/helperClasses/CommentHelperClass.class.php`,
        dataType: "json",
        contentType: "application/json;charset=utf-8",
        type: "GET",
        data: {
          query: e.target.value,
          commentSearch: true,
        },
        success: function (outcome) {
          $(".discover-outcome-section").html("");
          if (
            parseInt(outcome["response"]) !== 400 &&
            !jQuery.isEmptyObject(outcome)
          ) {
            $.each(outcome, (_, component) => {
              let outcome = `<section class="search-result comment bg-white mb-3 p-3"><section class="row"><section class="col-sm-2"><section class="d-flex flex-md-column flex-sm-row justify-content-center justify-content-evenly text-center remark-polls" data-comment-id="${component["idComment"]}">`;
              if (component["isVoted"] === 0) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#">${component["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (
                component["isVoted"] === 1 &&
                component["typeVote"] === 1
              ) {
                outcome += `<i class="bi bi-arrow-up-circle polls-boost my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-boost">${component["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (
                component["isVoted"] === 1 &&
                component["typeVote"] === -1
              ) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-deduct">${component["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto polls-deduct"></i>`;
              }
              outcome += `</section></section><section class="col-sm-10"><p class="null-border">`;
              outcome += `${component["content"]}`;
              outcome += `</p><section class="after-info-box revoke d-flex justify-content-between"><section class="account-info-short d-flex align-middle">`;
              outcome += `<img class="img-fluid my-auto image-header-account" src="http://${$(
                location
              ).attr("host")}/server/uploads/profilePictures/${
                component["profile_image"]
              }" alt="${component["username"]}-account-image"/>`;
              outcome += `<span class="ms-2">Posted by <a href="/account/${component["ownerId"]}">${component["username"]}</a></span>`;
              outcome += `</section>`;
              if (component["timestamp"] / 60 < 60) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  component["timestamp"] / 60
                )}m ago</span>`;
              } else if (
                component["timestamp"] / 60 >= 60 &&
                component["timestamp"] / 60 < 1409
              ) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  component["timestamp"] / 3600
                )}h ago</span>`;
              } else {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  component["timestamp"] / 86400
                )}d ago</span>`;
              }
              outcome += "</section></section></section></section>";
              $(".discover-outcome-section").append(outcome);
            });
            return;
          }
          $(".discover-outcome-section").html(
            `<section class="scheme-report glitch-data text-center bg-none p-3 mt-2"><img src="http://${$(
              location
            ).attr(
              "host"
            )}/client/img/error-empty-content.svg" alt="no content available" class="d-block null-data mx-auto"><p class="pt-5">It's a little bit lonely here. We couldn't find anything...</p></section>`
          );

        },
      });
    }
  });

  $(".posts-choice, .threads-choice, .remarks-choice").change((e) => {
    if (
      !$(".threads-choice").prop("checked") &&
      $(".posts-choice").prop("disabled") &&
      $(".remarks-choice").prop("disabled")
    ) {
      $(".posts-choice").prop("disabled", false);
      $(".threads-choice").prop("disabled", false);
      $(".remarks-choice").prop("disabled", false);
    } else if (
      $(".threads-choice").prop("checked") &&
      !$(".posts-choice").prop("disabled") &&
      !$(".remarks-choice").prop("disabled")
    ) {
      $(".posts-choice").prop("disabled", true);
      $(".threads-choice").prop("disabled", false);
      $(".remarks-choice").prop("disabled", true);
      $(".posts-choice").prop("checked", false);
      $(".remarks-choice").prop("checked", false);
      $(".discover-outcome-choices").text("Threads");
    } else if (
      !$(".threads-choice").prop("checked") &&
      $(".posts-choice").prop("checked") &&
      !$(".remarks-choice").prop("checked")
    ) {
      $(".discover-outcome-choices").text("Posts");
      $(".remarks-choice").prop("disabled", true);
    } else if (
      !$(".threads-choice").prop("checked") &&
      !$(".posts-choice").prop("checked") &&
      $(".remarks-choice").prop("checked")
    ) {
      $(".discover-outcome-choices").text("Comments");
      $(".posts-choice").prop("disabled", true);
    } else if (
      !$(".threads-choice").prop("checked") &&
      !$(".posts-choice").prop("checked") &&
      !$(".remarks-choice").prop("checked")
    ) {
      $(".discover-outcome-choices").text("Threads");
      $(".remarks-choice").prop("disabled", false);
      $(".posts-choice").prop("disabled", false);
    } else {
      $(".discover-outcome-choices").text("Threads");
      $(".posts-choice").prop("disabled", true);
      $(".remarks-choice").prop("disabled", true);
      $(".posts-choice").prop("checked", false);
      $(".remarks-choice").prop("checked", false);
    }
  });

  $(".account-configurations-image").change((event) => {
    if (event.target.files.length > 0) {
      const src = URL.createObjectURL(event.target.files[0]);
      const preview = $(".profile-changed-account-image");
      preview.attr("src", src);
    }
  });

  $(".login-btn").click((event) => {
    event.preventDefault();

    if (
      $(".login-email-input").val().length === 0 ||
      $(".login-password-input").val().length === 0
    ) {
      $(".login-form .scheme-report section:last-child p").text(
        'Fields "Email" and "Password" shouldn\'t be empty'
      );
      $(".login-form .scheme-report").removeClass("d-none");
      return;
    }


    const filter =
        /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (!filter.test($(".login-email-input").val())) {
      $(".login-form .scheme-report section:last-child p").text(
        "Email format is not valid."
      );
      $(".login-form .scheme-report").removeClass("d-none");
      return;
    }

    if ($(".login-password-input").val().length < 6) {
      $(".login-form .scheme-report section:last-child p").text(
        "Password should be longer than 5 letters."
      );
      $(".login-form .scheme-report").removeClass("d-none");
      return;
    }

    $.ajax({
      url: `http://${$(location).attr(
        "host"
      )}/server/helperClasses/UserHelperClass.class.php`,
      dataType: "json",
      contentType: "application/json;charset=utf-8",
      type: "GET",
      data: {
        email: $(".login-email-input").val(),
        password: $(".login-password-input").val(),
      },
      success: function (outcome) {
        if (
          parseInt(outcome["response"]) === 200 ||
          parseInt(outcome["response"]) === 403
        ) {
          $(location).prop("href", "/");
          return;
        }

        $(".login-form .scheme-report section:last-child p").text(
          outcome["data"]["message"]
        );
        $(".login-form .scheme-report").removeClass("d-none");

      },
    });
  });

  $(".roster-affirm-ultimate").click((e) => {
    e.preventDefault();
    const code = $(".code-roster-intake-affirm").val();

    if (!$.isNumeric(code)) {
      $(".scheme-report section:last-child p").text(
        "Confirmation code must be numeric."
      );
      $(".scheme-report").removeClass("d-none");
      return;
    }

    if (parseInt(code) < 1000 || parseInt(code) > 99999) {
      $(".scheme-report section:last-child p").text("Invalid code.");
      $(".scheme-report").removeClass("d-none");
      return;
    }

    $(".scheme-report.bg-danger").addClass("d-none");
    const token = new URLSearchParams(window.location.search).get("token");

    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/TokenHelperClass.class.php`,
      {
        code: code,
        token: token,
      }
    ).done(function (outcome) {
      if (
        parseInt(outcome["response"]) === 200 ||
        parseInt(outcome["response"]) === 403
      ) {
        $(location).prop("href", "/");
        return;
      }

      $(".scheme-report section:last-child p").text(outcome["data"]["message"]);
      $(".scheme-report").removeClass("d-none");

    });
  });

  $(".restore-affirm-ultimate").click((e) => {
    e.preventDefault();
    const filter = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/;
    if (!filter.test($(".new-password-input").val())) {
      $(".scheme-report.bg-danger section:last-child p").text(
        "Password must be minimum 8 characters, one uppercase letter, and one special symbol."
      );
      $(".scheme-report.bg-danger").removeClass("d-none");
      return;
    }

    if (
      $(".new-password-input").val() !== $(".new-password-confirm-input").val()
    ) {
      $(".scheme-report.bg-danger section:last-child p").text(
        "Passwords don't match."
      );
      $(".scheme-report.bg-danger").removeClass("d-none");
      return;
    }

    $(".scheme-report.bg-danger").addClass("d-none");

    const token = new URLSearchParams(window.location.search).get("token");

    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/TokenHelperClass.class.php`,
      {
        password: $(".new-password-input").val(),
        repeatpassword: $(".new-password-confirm-input").val(),
        token: token,
      }
    ).done(function (outcome) {
      if (
        parseInt(outcome["response"]) === 200 ||
        parseInt(outcome["response"]) === 403
      ) {
        $(".new-password-input").val("");
        $(".new-password-confirm-input").val("");
        $(".scheme-report section h5").text("Success");
        $(".scheme-report section:last-child p").text(
          "Password has been recovered."
        );
        $(".scheme-report").removeClass("d-none");
        $(".scheme-report").removeClass("bg-danger");
        $(".scheme-report").addClass("bg-success");
        setTimeout(() => {
          window.location = "/";
        }, 3001);
        return;
      }

      $(".scheme-report section:last-child p").text(outcome["data"]["message"]);
      $(".scheme-report").removeClass("d-none");

    });
  });

  $(".restore-affirm").click((e) => {
    e.preventDefault();

    const filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!filter.test($(".restore-email-input-affirm").val())) {
      $(".scheme-report.bg-danger section:last-child p").text(
        "Email format is not valid."
      );
      $(".scheme-report.bg-danger").removeClass("d-none");
      return;
    }

    $(".scheme-report.bg-danger").addClass("d-none");

    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/UserHelperClass.class.php`,
      {
        email: $(".restore-email-input-affirm").val(),
        state: false,
      }
    ).done(function (outcome) {
      if (
        parseInt(outcome["response"]) === 200 ||
        parseInt(outcome["response"]) === 403
      ) {
        $(".scheme-report section h5").text("Success");
        $(".scheme-report section:last-child p").text(
          "Recovery email has been sent to your email address."
        );
        $(".scheme-report").removeClass("d-none");
        $(".scheme-report").removeClass("bg-danger");
        $(".scheme-report").addClass("bg-success");
        return;
      }

      $(".scheme-report section:last-child p").text(outcome["data"]["message"]);
      $(".scheme-report").removeClass("d-none");

    });
  });

  $(".account-configurations-image").change((e) => {
    e.preventDefault();
    const form_data = new FormData();
    form_data.append(
      "img_profile",
      $(".account-configurations-image").get(0).files[0]
    );
    $.ajax({
      url: `http://${$(location).attr(
        "host"
      )}/server/helperClasses/UserHelperClass.class.php`,
      type: "POST",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: function (outcome) {
        if (
          parseInt(outcome["response"]) === 200 ||
          parseInt(outcome["response"]) === 403
        ) {
          $(".scheme-report-data i").removeClass("bi-bug-fill");
          $(".scheme-report-data i").addClass("bi-stars");
          $(".scheme-report section h5").text("Success");
          $(".scheme-report section:last-child p").text(
            "Image has been updated"
          );
          $(".scheme-report").removeClass("d-none");
          $(".scheme-report").removeClass("bg-danger");
          $(".scheme-report").addClass("bg-success");
          setTimeout(() => {
            window.location = "/account/edit";
          }, 3000);
          return;
        }

        $(".reason").text(outcome["data"]["message"]);
        $(".scheme-report").removeClass("d-none");

      },
    });
  });

  $(".btn-profile-change").click((e) => {
    let filter;
    e.preventDefault();

    if (
      $(".account-configurations-username").val().length === 0 &&
      $(".account-configurations-formerpassword").val().length === 0 &&
      $(".account-configurations-appendedpassword").val().length === 0
    ) {
      $(".scheme-report.bg-danger section:last-child p").text(
        "Fields are empty. Nothing to update."
      );

      $(".scheme-report.bg-danger").removeClass("d-none");

      return;
    }

    if (
      ($(".account-configurations-username").val().length < 3 ||
        $(".account-configurations-username").val().length > 8) &&
      $(".account-configurations-formerpassword").val().length === 0 &&
      $(".account-configurations-appendedpassword").val().length === 0
    ) {
      $(".scheme-report.bg-danger section:last-child p").text(
        "Username should be between 3 to 8 characters."
      );

      $(".scheme-report.bg-danger").removeClass("d-none");

      return;
    }

    const regex = /^[a-z0-9]+$/;

    if (
      !regex.test($(".account-configurations-username").val()) &&
      $(".account-configurations-formerpassword").val().length === 0 &&
      $(".account-configurations-appendedpassword").val().length === 0
    ) {
      $(".scheme-report.bg-danger section:last-child p").text(
        "Only small letters and numbers are allowed."
      );

      $(".scheme-report.bg-danger").removeClass("d-none");

      return;
    }

    if (
      regex.test($(".account-configurations-username").val()) &&
      ($(".account-configurations-username").val().length >= 3 ||
        $(".account-configurations-username").val().length <= 8) &&
      $(".account-configurations-formerpassword").val().length === 0 &&
      $(".account-configurations-appendedpassword").val().length === 0
    ) {
      $.post(
        `http://${$(location).attr(
          "host"
        )}/server/helperClasses/UserHelperClass.class.php`,
        {
          aUsername: $(".account-configurations-username").val(),
        }
      ).done(function (outcome) {
        if (parseInt(outcome["response"]) === 200) {
          $(".scheme-report-data i").removeClass("bi-bug-fill");
          $(".scheme-report-data i").addClass("bi-stars");
          $(".scheme-report section h5").text("Success");
          $(".scheme-report section:last-child p").text(
            "Account details has been updated."
          );
          $(".scheme-report").removeClass("d-none");
          $(".scheme-report").removeClass("bg-danger");
          $(".scheme-report").addClass("bg-success");
          $(".account-configurations-username").val("");
          setTimeout(() => {
            window.location = "/account/edit";
          }, 3000);
          return;
        } else if (parseInt(outcome["response"]) === 403) {
          $(".scheme-report section:last-child p").text("Invalid action.");
          $(".scheme-report").removeClass("d-none");
          return;
        }

        $(".scheme-report section:last-child p").text(
          outcome["data"]["message"]
        );
        $(".scheme-report").removeClass("d-none");

      });
    }

    if ($(".account-configurations-username").val().length === 0) {
      filter = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/;
      if (!filter.test($(".account-configurations-formerpassword").val())) {
        $(".scheme-report.bg-danger section:last-child p").text(
          "Password must be minimum 8 characters, one uppercase letter, and one special symbol."
        );
        $(".scheme-report.bg-danger").removeClass("d-none");
        return;
      }

      if (!filter.test($(".account-configurations-appendedpassword").val())) {
        $(".scheme-report.bg-danger section:last-child p").text(
          "Password must be minimum 8 characters, one uppercase letter, and one special symbol."
        );
        $(".scheme-report.bg-danger").removeClass("d-none");
        return;
      }

      $.post(
        `http://${$(location).attr(
          "host"
        )}/server/helperClasses/UserHelperClass.class.php`,
        {
          aOldPassword: $(".account-configurations-formerpassword").val(),
          aNewPassword: $(".account-configurations-appendedpassword").val(),
        }
      ).done(function (outcome) {
        if (parseInt(outcome["response"]) === 200) {
          $(".scheme-report-data i").removeClass("bi-bug-fill");
          $(".scheme-report-data i").addClass("bi-stars");
          $(".scheme-report section h5").text("Success");
          $(".scheme-report section:last-child p").text(
            "Account details has been updated."
          );
          $(".scheme-report").removeClass("d-none");
          $(".scheme-report").removeClass("bg-danger");
          $(".scheme-report").addClass("bg-success");
          $(".account-configurations-formerpassword").val("");
          $(".account-configurations-appendedpassword").val("");
          setTimeout(() => {
            window.location = "/account/edit";
          }, 3000);
          return;
        } else if (parseInt(outcome["response"]) === 403) {
          $(".scheme-report section:last-child p").text("Invalid action.");
          $(".scheme-report").removeClass("d-none");
          return;
        }

        $(".scheme-report section:last-child p").text(
          outcome["data"]["message"]
        );
        $(".scheme-report").removeClass("d-none");

      });
    }

    if (
      $(".account-configurations-username").val().length !== 0 &&
      $(".account-configurations-formerpassword").val().length !== 0 &&
      $(".account-configurations-appendedpassword").val().length !== 0
    ) {
      if (
        !regex.test($(".account-configurations-username").val()) ||
        $(".account-configurations-username").val().length < 3 ||
        $(".account-configurations-username").val().length > 8
      ) {
        $(".scheme-report.bg-danger section:last-child p").text(
          "Username should be between 3 to 8 characters with small letters or numbers."
        );
        $(".scheme-report.bg-danger").removeClass("d-none");
        return;
      }

      filter = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/;
      if (!filter.test($(".account-configurations-formerpassword").val())) {
        $(".scheme-report.bg-danger section:last-child p").text(
          "Password must be minimum 8 characters, one uppercase letter, and one special symbol."
        );
        $(".scheme-report.bg-danger").removeClass("d-none");
        return;
      }

      if (!filter.test($(".account-configurations-appendedpassword").val())) {
        $(".scheme-report.bg-danger section:last-child p").text(
          "Password must be minimum 8 characters, one uppercase letter, and one special symbol."
        );
        $(".scheme-report.bg-danger").removeClass("d-none");
        return;
      }

      $.post(
        `http://${$(location).attr(
          "host"
        )}/server/helperClasses/UserHelperClass.class.php`,
        {
          aUsername: $(".account-configurations-username").val(),
          aOldPassword: $(".account-configurations-formerpassword").val(),
          aNewPassword: $(".account-configurations-appendedpassword").val(),
        }
      ).done(function (outcome) {
        if (parseInt(outcome["response"]) === 200) {
          $(".scheme-report-data i").removeClass("bi-bug-fill");
          $(".scheme-report-data i").addClass("bi-stars");
          $(".scheme-report section h5").text("Success");
          $(".scheme-report section:last-child p").text(
            "Account details has been updated."
          );
          $(".scheme-report").removeClass("d-none");
          $(".scheme-report").removeClass("bg-danger");
          $(".scheme-report").addClass("bg-success");
          $(".account-configurations-username").val("");
          $(".account-configurations-formerpassword").val("");
          $(".account-configurations-appendedpassword").val("");
          setTimeout(() => {
            window.location = "/account/edit";
          }, 3000);
          return;
        } else if (parseInt(outcome["response"]) === 403) {
          $(".scheme-report section:last-child p").text("Invalid action.");
          $(".scheme-report").removeClass("d-none");
          return;
        }

        $(".scheme-report section:last-child p").text(
          outcome["data"]["message"]
        );
        $(".scheme-report").removeClass("d-none");

      });
    }
  });

  $(".btn-profile-remove").click((e) => {
    e.preventDefault();

    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/UserHelperClass.class.php`,
      {
        deleteAccount: true,
      }
    ).done(function (outcome) {
      if (parseInt(outcome["response"]) === 200) {
        $(".scheme-report-data i").removeClass("bi-bug-fill");
        $(".scheme-report-data i").addClass("bi-stars");
        $(".scheme-report section h5").text("Success");
        $(".scheme-report section:last-child p").text(
          "Account has been disabled."
        );
        $(".scheme-report").removeClass("d-none");
        $(".scheme-report").removeClass("bg-danger");
        $(".scheme-report").addClass("bg-success");
        setTimeout(() => {
          window.location = "/logout";
        }, 3000);
        return;
      } else if (parseInt(outcome["response"]) === 403) {
        $(".scheme-report section:last-child p").text("Invalid action.");
        $(".scheme-report").removeClass("d-none");
        return;
      }

      $(".scheme-report section:last-child p").text(outcome["data"]["message"]);
      $(".scheme-report").removeClass("d-none");

    });
  });

  $(".discover-input-container").on("input", (e) => {
    const regex = /^[a-z0-9]+$/;

    if (
      !regex.test($(".discover-input-container").val()) &&
      $(".discover-input-container").val().length !== 0
    ) {
      $(".chief-discover-profiles-table").addClass("d-none");
      return;
    }

    $(".chief-discover-profiles-table").removeClass("d-none");

    $.get(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/AdminHelperClass.class.php`,
      {
        query: $(".discover-input-container").val(),
      }
    ).done(function (outcome) {
      if (parseInt(outcome["response"]) === 200) {
        $("tbody").html("");
        $.each(outcome["data"], (_, value) => {
          $.each(value, function (_, component) {
            let outcome = `<tr><td scope="row">${component["id"]}</td><td><a href="/account/${component["id"]}">${component["username"]}</a></td><td>${component["regdate"]}</td><td>${component["email"]}</td>`;

            outcome +=
              component["is_email_confirmed"] === 1
                ? `<td><span class="bg-success rounded p-1 text-light">Confirmed</span></td>`
                : `<td><span class="bg-danger rounded p-1 text-light">Not Confirmed</span></td>`;

            outcome +=
              component["is_admin"] === 1
                ? '<td><span class="chief-profile-mode true p-1 rounded text-light">Yes</span></td>'
                : '<td><span class="chief-profile-mode false p-1 rounded text-light">No</span></td>';

            outcome +=
              component["is_account_disabled"] === 1
                ? `<td><button class="chief-profiles-perform-section" data-id=\"${component["id"]}\" data-status=\"unblock\">Unblock</button><br>`
                : `<td><button class="chief-profiles-perform-section" data-id=\"${component["id"]}\" data-status=\"block\">Block</button><br>`;

            outcome +=
              component["is_admin"] === 1
                ? `<button class="chief-profiles-perform-chief" data-id=\"${component["id"]}\" data-status=\"demote-admin\">Demote Admin</button>`
                : `<button class="chief-profiles-perform-chief" data-id=\"${component["id"]}\" data-status=\"new-admin\">Make Admin</button>`;

            outcome += `</td></tr>`;

            $("tbody").append(outcome);
          });
        });
        $(".chief-discover-profiles-table").removeClass("d-none");
        return;
      } else if (parseInt(outcome["response"]) === 403) {
        $("tbody").html("");
        $(".chief-discover-profiles-table").addClass("d-none");
        return;
      } else if (parseInt(outcome["response"]) === 400) {
        $("tbody").html("");
        $(".chief-discover-profiles-table").addClass("d-none");
        return;
      }
      $("tbody").html("");
      $.each(outcome, (_, component) => {
        let outcome = `<tr><td scope="row">${component["id"]}</td><td><a href="/account/${component["id"]}">${component["username"]}</a></td><td>${component["regdate"]}</td><td>${component["email"]}</td>`;

        outcome +=
          component["is_email_confirmed"] === 1
            ? `<td><span class="bg-success rounded p-1 text-light">Confirmed</span></td>`
            : `<td><span class="bg-danger rounded p-1 text-light">Not Confirmed</span></td>`;

        outcome +=
          component["is_admin"] === 1
            ? '<td><span class="chief-profile-mode true p-1 rounded text-light">Yes</span></td>'
            : '<td><span class="chief-profile-mode false p-1 rounded text-light">No</span></td>';

        outcome +=
          !component["is_account_disabled"] === 1
            ? `<td><button class="chief-profiles-perform-section" data-id=\"${component["id"]}\" data-status=\"unblock\">Unblock</button><br>>`
            : `<td><button class="chief-profiles-perform-section" data-id=\"${component["id"]}\" data-status=\"block\">Block</button><br>`;

        outcome +=
          component["is_admin"] === 1
            ? `<button class="chief-profiles-perform-chief" data-id=\"${component["id"]}\" data-status=\"demote-admin\">Demote Admin</button>`
            : `<button class="chief-profiles-perform-chief" data-id=\"${component["id"]}\" data-status=\"new-admin\">Make Admin</button>`;

        outcome += `</td></tr>`;

        $("tbody").append(outcome);
      });
      $(".chief-discover-profiles-table").removeClass("d-none");

    });
  });

  $(document).on("click", ".chief-profiles-perform-section", (e) => {
    e.preventDefault();

    const action = e.target.attributes[2].nodeValue === "block" ? true : false;

    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/AdminHelperClass.class.php`,
      {
        action: action,
        userId: parseInt(e.target.attributes[1].value),
      }
    ).done(function (_) {
      setTimeout(() => {
        window.location = "/admin/users";
      }, 1000);

    });
  });

  $(document).on("click", ".chief-profiles-perform-chief", (e) => {
    e.preventDefault();

    const action =
        e.target.attributes[2].nodeValue === "new-admin" ? true : false;
    //console.log(e.target.attributes[2].nodeValue);
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/AdminHelperClass.class.php`,
      {
        actionAdmin: action,
        userId: parseInt(e.target.attributes[1].value),
      }
    ).done(function (_) {
      setTimeout(() => {
        window.location = "/admin/users";
      }, 1000);

    });
  });

  $(".discover-input-thread").on("input", (e) => {
    const regex = /^[a-zA-Z0-9\s]+$/;

    if (
      !regex.test($(".discover-input-thread").val()) &&
      $(".discover-input-thread").val().length !== 0
    ) {
      $(".chief-discover-threads-table ").addClass("d-none");
      return;
    }

    $(".chief-discover-threads-table").removeClass("d-none");

    $.get(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/AdminHelperClass.class.php`,
      {
        queryThread: $(".discover-input-thread").val(),
      }
    ).done(function (outcome) {
      if (parseInt(outcome["response"]) === 200) {
        $("tbody").html("");
        $.each(outcome["data"], (_, value) => {
          $.each(value, function (_, component) {
            let outcome = `<tr><td scope='row'>${component["thread_id"]}</td>`;
            outcome += `<td>${component["title"]}</td>`;
            outcome += `<td><a href=\"/t/${component["link"]}\">/t/${component["link"]}/</a></td>`;
            outcome += `<td>${component["created_date"]}</td>`;
            outcome += `<td><a href=\"/account/${component["ownerId"]}\">${component["ownerName"]}</a></td>`;
            if (component["isRowHidden"] !== 1 && component["is_deleted"] !== 1)
              outcome += `<td><span class=\"bg-success rounded p-1 text-light\">Active</span></td>`;
            else if (
              component["isRowHidden"] === 1 &&
              component["is_deleted"] !== 1
            )
              outcome += `<td><span class=\"bg-warning rounded p-1 text-light\">Hidden</span></td>`;
            else
              outcome += `<td><span class=\"bg-danger rounded p-1 text-light\">Deleted</span></td>`;

            outcome += `<td>${component["members"]}</td><td>`;

            if (component["isRowHidden"] !== 1 && component["is_deleted"] !== 1)
              outcome += `<button class=\"chief-threads-perform-remove\" data-id=\"${component["thread_id"]}\" data-status=\"remove\">Delete</button><br><button class=\"chief-threads-perform-disguise disguise\" data-id=\"${component["thread_id"]}\" data-status=\"disguise\">Hide</button><br>`;
            else
              outcome += `<button class=\"chief-threads-perform-recover\" data-id=\"${component["thread_id"]}\" data-status=\"restore\">Restore</button>`;

            outcome += `</td></tr>`;
            $("tbody").append(outcome);
          });
        });
        $(".chief-discover-threads-table").removeClass("d-none");
        return;
      } else if (parseInt(outcome["response"]) === 403) {
        $("tbody").html("");
        $(".chief-discover-threads-table").addClass("d-none");
        return;
      } else if (parseInt(outcome["response"]) === 400) {
        $("tbody").html("");
        $(".chief-discover-threads-table").addClass("d-none");
        return;
      }
      $("tbody").html("");
      $.each(outcome, (_, component) => {
        let outcome = `<tr><td scope='row'>${component["thread_id"]}</td>`;
        outcome += `<td>${component["title"]}</td>`;
        outcome += `<td><a href=\"/t/${component["link"]}\">/t/${component["link"]}/</a></td>`;
        outcome += `<td>${component["created_date"]}</td>`;
        outcome += `<td><a href=\"/account/${component["ownerId"]}\">${component["ownerName"]}</a></td>`;
        if (component["isRowHidden"] !== 1 && component["is_deleted"] !== 1)
          outcome += `<td><span class=\"bg-success rounded p-1 text-light\">Active</span></td>`;
        else if (component["isRowHidden"] === 1 && component["is_deleted"] !== 1)
          outcome += `<td><span class=\"bg-warning rounded p-1 text-light\">Hidden</span></td>`;
        else
          outcome += `<td><span class=\"bg-danger rounded p-1 text-light\">Deleted</span></td>`;

        outcome += `<td>${component["members"]}</td><td>`;

        if (component["isRowHidden"] !== 1 && component["is_deleted"] !== 1)
          outcome += `<button class=\"chief-threads-perform-remove\" data-id=\"${component["thread_id"]}\" data-status=\"remove\">Delete</button><br><button class=\"chief-threads-perform-disguise disguise\" data-id=\"${component["thread_id"]}\" data-status=\"disguise\">Hide</button><br>`;
        else
          outcome += `<button class=\"chief-threads-perform-recover\" data-id=\"${component["thread_id"]}\" data-status=\"restore\">Restore</button>`;

        outcome += `</td></tr>`;
        $("tbody").append(outcome);
      });
      $(".chief-discover-threads-table").removeClass("d-none");

    });
  });

  $(document).on("click", ".chief-threads-perform-remove", (e) => {
    e.preventDefault();

    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/AdminHelperClass.class.php`,
      {
        actionTypeDelete: $(".chief-threads-perform-remove").data("status"),
        threadId: parseInt(e.target.attributes[1].nodeValue),
      }
    ).done(function (_) {
      setTimeout(() => {
        window.location = "/admin/threads";
      }, 1000);

    });
  });

  $(document).on("click", ".chief-threads-perform-disguise", (e) => {
    e.preventDefault();
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/AdminHelperClass.class.php`,
      {
        actionTypeHide: $(".chief-threads-perform-disguise").data("status"),
        threadId: parseInt(e.target.attributes[1].nodeValue),
      }
    ).done(function (_) {
      setTimeout(() => {
        window.location = "/admin/threads";
      }, 1000);

    });
  });

  $(document).on("click", ".chief-threads-perform-recover", (e) => {
    e.preventDefault();
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/AdminHelperClass.class.php`,
      {
        actionTypeRecover: $(".chief-threads-perform-recover").data("status"),
        threadId: parseInt(e.target.attributes[1].nodeValue),
      }
    ).done(function (_) {
      setTimeout(() => {
        window.location = "/admin/threads";
      }, 1000);

    });
  });

  $(document).on("click", ".after-polls > .bi-arrow-up-circle", (e) => {
    let postId = parseInt($(e.target.parentNode).data("post-id"));
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/PostHelperClass.class.php`,
      {
        postId: postId,
        type: "voteUp",
      }
    ).done((outcome) => {
      if (parseInt(outcome["response"]) === 200) {
        $(
          ".after-polls[data-post-id=" + postId + "] .bi-arrow-down-circle"
        ).removeClass("polls-deduct");
        $(".after-polls[data-post-id=" + postId + "] > span > a").removeClass(
          "polls-deduct"
        );
        $(
          ".after-polls[data-post-id=" + postId + "] .bi-arrow-up-circle"
        ).addClass("polls-boost");
        $(".after-polls[data-post-id=" + postId + "]> span > a").addClass(
          "polls-boost"
        );
        $(".after-polls[data-post-id=" + postId + "] > span > a").text(
          parseInt(outcome["numOfVotes"])
        );
      }

    });
  });

  $(document).on("click", ".remark-polls > .bi-arrow-up-circle", (e) => {
    let commentId = parseInt($(e.target.parentNode).data("comment-id"));
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/CommentHelperClass.class.php`,
      {
        commentId: commentId,
        type: "voteUp",
      }
    ).done((outcome) => {
      if (parseInt(outcome["response"]) === 200) {
        $(
          ".remark-polls[data-comment-id=" +
            commentId +
            "] .bi-arrow-down-circle"
        ).removeClass("polls-deduct");
        $(
          ".remark-polls[data-comment-id=" + commentId + "] > span > a"
        ).removeClass("polls-deduct");
        $(
          ".remark-polls[data-comment-id=" + commentId + "] .bi-arrow-up-circle"
        ).addClass("polls-boost");
        $(
          ".remark-polls[data-comment-id=" + commentId + "]> span > a"
        ).addClass("polls-boost");
        $(".remark-polls[data-comment-id=" + commentId + "] > span > a").text(
          parseInt(outcome["numOfVotes"])
        );
      }

    });
  });

  $(document).on("click", ".remark-polls > .bi-arrow-down-circle", (e) => {
    let commentId = parseInt($(e.target.parentNode).data("comment-id"));
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/CommentHelperClass.class.php`,
      {
        commentId: commentId,
        type: "voteDown",
      }
    ).done((outcome) => {
      if (parseInt(outcome["response"]) === 200) {
        $(
          ".remark-polls[data-comment-id=" + commentId + "] .bi-arrow-up-circle"
        ).removeClass("polls-boost");
        $(
          ".remark-polls[data-comment-id=" + commentId + "] > span > a"
        ).removeClass("polls-boost");
        $(
          ".remark-polls[data-comment-id=" +
            commentId +
            "] .bi-arrow-down-circle"
        ).addClass("polls-deduct");
        $(
          ".remark-polls[data-comment-id=" + commentId + "] > span > a"
        ).addClass("polls-deduct");
        $(".remark-polls[data-comment-id=" + commentId + "] > span > a").text(
          parseInt(outcome["numOfVotes"])
        );
      }

    });
  });

  $(document).on("click", ".after-polls > .bi-arrow-down-circle", (e) => {
    let postId = parseInt($(e.target.parentNode).data("post-id"));
    $.post(
      `http://${$(location).attr(
        "host"
      )}/server/helperClasses/PostHelperClass.class.php`,
      {
        postId: postId,
        type: "voteDown",
      }
    ).done((outcome) => {
      if (parseInt(outcome["response"]) === 200) {
        $(
          ".after-polls[data-post-id=" + postId + "] .bi-arrow-up-circle"
        ).removeClass("polls-boost");
        $(".after-polls[data-post-id=" + postId + "] > span > a").removeClass(
          "polls-boost"
        );
        $(
          ".after-polls[data-post-id=" + postId + "] .bi-arrow-down-circle"
        ).addClass("polls-deduct");
        $(".after-polls[data-post-id=" + postId + "] > span > a").addClass(
          "polls-deduct"
        );
        $(".after-polls[data-post-id=" + postId + "] > span > a").text(
          parseInt(outcome["numOfVotes"])
        );
      }

    });
  });

  setInterval(() => {
    if ($(".after-article-content").length !== 0) {
      $.ajax({
        url: `http://${$(location).attr(
          "host"
        )}/server/helperClasses/CommentHelperClass.class.php`,
        dataType: "json",
        contentType: "application/json;charset=utf-8",
        type: "GET",
        data: {
          postUrl: window.location.pathname.split("/")[3],
          commentFind: true,
        },
        success: (response) => {
          let outcome = "";
          $.each(response, (_, remark) => {
            outcome += `<article class="rounded p-4 px-0">`;
            outcome += `<section class="row">`;
            outcome += `<section class="col-sm-2">`;
            outcome += `<section class="d-flex flex-md-column flex-sm-row justify-content-center justify-content-evenly text-center remark-polls" data-comment-id="${remark["idComment"]}">`;
            if (remark["isVoted"] === 0) {
              outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
              outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#">${remark["numOfVotes"]}</a></span>`;
              outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
            } else if (remark["isVoted"] === 1 && remark["typeVote"] === 1) {
              outcome += `<i class="bi bi-arrow-up-circle polls-boost my-auto"></i>`;
              outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-boost">${remark["numOfVotes"]}</a></span>`;
              outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
            } else if (remark["isVoted"] === 1 && remark["typeVote"] === -1) {
              outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
              outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-deduct">${remark["numOfVotes"]}</a></span>`;
              outcome += `<i class="bi bi-arrow-down-circle polls-deduct my-auto"></i>`;
            }
            outcome += `</section>`;
            outcome += `</section>`;
            outcome += `<section class="col-sm-10">`;
            outcome += `<p class="null-border">${remark["content"]}</p>`;
            outcome += `<section class="after-info-box revoke d-flex justify-content-between">`;
            outcome += `<section class="account-info-short d-flex align-middle">`;
            outcome += `<img class="img-fluid my-auto image-header-account" src="http://${$(
              location
            ).attr("host")}/server/uploads/user_images/${
              remark["profile_image"]
            }" alt="${remark["username"]}-account-image"/>`;
            outcome += `<span class="ms-2"><a href="/account/${remark["ownerId"]}">${remark["username"]}</a> replied</span>`;
            outcome += `</section>`;
            if (remark["timestamp"] / 60 < 60) {
              outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                remark["timestamp"] / 60
              )}m ago</span>`;
            } else if (
              remark["timestamp"] / 60 >= 60 &&
              remark["timestamp"] / 60 < 1409
            ) {
              outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                remark["timestamp"] / 3600
              )}h ago</span>`;
            } else {
              outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                remark["timestamp"] / 86400
              )}d ago</span>`;
            }
            outcome += `</section>`;
            if (remark["isAdmin"] === 1 || remark["isOwner"] === 1) {
              outcome += `<section class="mt-2">`;
              outcome += `<button class="remove remark-remove" data-comment-id="${remark["idComment"]}">Delete</button>`;
              outcome += `</section>`;
            }
            outcome += `</section>`;
            outcome += `</section>`;
            outcome += `</article>`;
          });

          $(".after-article-content").html(outcome);
        },
      });
    }
  }, 1000);

  setInterval(() => {
    if ($(".affair-after-exclusive").length !== 0) {
      const threadUrl = window.location.pathname.split("/")[2];
      const postId = window.location.pathname.split("/")[3];
      $.ajax({
        url: `http://${$(location).attr(
          "host"
        )}/server/helperClasses/PostHelperClass.class.php`,
        dataType: "json",
        contentType: "application/json;charset=utf-8",
        type: "GET",
        data: {
          threadUrl: threadUrl,
          postId: parseInt(postId),
        },
        success: (outcome) => {
         // console.log(Object.keys(outcome).length);
          if (Object.keys(outcome).length === 0) {
            console.log("outcome", outcome);
            window.location = `/t/${threadUrl}`;
          } else {
            if (Boolean(parseInt(outcome.isHidden))) {
              if ($(".scheme-report").length === 0) {
                $(".affair-after-exclusive")
                  .prepend(`<section class="scheme-report bg-danger mb-3">
								  <section class="scheme-report-data d-inline-flex px-3 py-3 w-100">
									  <i class="bi bi-bug-fill text-center my-auto text-light"></i>
									  <p class="ms-3 my-auto">This post was disabled.<br><span class="fw-bolder">Reason:</span> Violation of Community Guidelines.</p>
								  </section>
							  </section>`);
              }

              if ($(".respond-after").length !== 0) {
                $(".respond-after").hide();
              }
            } else {
              if ($(".scheme-report").length !== 0) {
                $(".scheme-report").remove();
              }

              if ($(".respond-after").length !== 0) {
                $(".respond-after").show();
              }
            }
          }
        },
      });
    }
  }, 1000);

  setInterval(() => {
    if ($(".threads-data").length !== 0) {
      const threadUrl = window.location.pathname.split("/")[2];
      const sortType = "Top";
      $.ajax({
        url: `http://${$(location).attr(
          "host"
        )}/server/helperClasses/PostHelperClass.class.php`,
        dataType: "json",
        contentType: "application/json;charset=utf-8",
        type: "GET",
        data: {
          threadUrl: threadUrl,
          sortType: sortType,
        },
        success: function (outcome) {
          $("article").remove();
          $(".after-outcome-section").html("");
          var r = jQuery.isEmptyObject(outcome);
          var res = outcome["response"];
          console.log(outcome);
          if (
            parseInt(outcome["response"]) !== 400 &&
            !jQuery.isEmptyObject(outcome)
          ) {
            $.each(outcome, (_, component) => {
              console.log(component);
              let outcome = `<article class="rounded p-4 mb-5">`;
              outcome += `<section class="row">`;
              outcome += `<section class="col-sm-2">`;
              outcome += `<section class="d-flex justify-content-evenly text-center justify-content-center after-polls flex-md-column flex-sm-row" data-post-id="${component["idPost"]}">`;
              if (component["isVoted"] == 0) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#">${component["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (
                component["isVoted"] == 1 &&
                component["typeVote"] == 1
              ) {
                outcome += `<i class="bi bi-arrow-up-circle polls-boost my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-boost">${component["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
              } else if (
                component["isVoted"] == 1 &&
                component["typeVote"] == -1
              ) {
                outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-deduct">${component["numOfVotes"]}</a></span>`;
                outcome += `<i class="bi bi-arrow-down-circle my-auto polls-deduct"></i>`;
              }
              outcome += `</section></section><section class="col-sm-10">`;
              outcome += `<h4><a href="/t/${component["link"]}/${component["idPost"]}">${component["postTitle"]}</a></h4>`;
              outcome += `<p class="null-border">`;
              if (
                component["image"] == null &&
                component["media_link"] == null &&
                component["content"] != null
              ) {
                outcome += `${component["content"]}`;
              } else if (
                component["image"] != null &&
                component["media_link"] == null &&
                component["content"] == null
              ) {
                outcome += `<img src="http://${$(location).attr(
                  "host"
                )}/server/uploads/post_images/${
                  component["image"]
                }" alt="content-img">`;
              } else if (
                component["image"] == null &&
                component["media_link"] != null &&
                component["content"] == null
              ) {
                outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
              } else if (
                component["image"] != null &&
                component["media_link"] == null &&
                component["content"] != null
              ) {
                outcome += `${component["content"]}`;
                outcome += `<img src="http://${$(location).attr(
                  "host"
                )}/server/uploads/post_images/${
                  component["image"]
                }" alt="content-img">`;
              } else if (
                component["image"] == null &&
                component["media_link"] != null &&
                component["content"] != null
              ) {
                outcome += `${component["content"]}`;
                outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
              } else if (
                component["image"] != null &&
                component["media_link"] != null &&
                component["content"] != null
              ) {
                outcome += `${component["content"]}`;
                outcome += `<img src="http://${$(location).attr(
                  "host"
                )}/server/uploads/post_images/${
                  component["image"]
                }" alt="content-img">`;
                outcome += `<iframe class="pt-2" width="100%" height="300" src="${component["media_link"]}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
              } else {
                outcome += component["content"];
              }
              outcome += `</p>`;
              outcome += `<section class="after-info-box revoke d-flex justify-content-between mt-0"><section class="account-info-short d-flex align-middle">`;
              outcome += `<img class="img-fluid my-auto image-header-account" src="http://${$(
                location
              ).attr("host")}/server/uploads/profilePictures/${
                component["profile_image"]
              }" alt="${component["username"]}-account-image"/>`;
              outcome += `<span class="ms-2">Posted by <a href="/account/${component["ownerId"]}">${component["username"]}</a></span>`;
              outcome += `</section>`;
              if (component["timestamp"] / 60 < 60) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  component["timestamp"] / 60
                )}m ago</span>`;
              } else if (
                component["timestamp"] / 60 >= 60 &&
                component["timestamp"] / 60 < 1409
              ) {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  component["timestamp"] / 3600
                )}h ago</span>`;
              } else {
                outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                  component["timestamp"] / 86400
                )}d ago</span>`;
              }
              outcome += `<section class="after-info-remarks">`;
              outcome += `<a href="/t/${component["link"]}/${component["idPost"]}"><i class="bi bi-blockquote-left"></i><span class="ms-1">${component["totalComments"]}</span></a>`;
              outcome += `</section>`;
              outcome += `</section>`;
              if (component["isAdmin"] || component["isOwner"] ) {
                outcome += `<section class="mt-2">`;
                const hideButtonText =
                    component["isHidden"] === 1 ? "Unhide" : "Hide";
                outcome += `<button class="disguise me-4 after-disguise" data-post-id="${component["idPost"]}">${hideButtonText}</button>`;
                outcome += `<button class="remove after-remove" data-post-id="${component["idPost"]}">Delete</button>`;
                outcome += `</section>`;
              }
              component["comments"].forEach((remark) => {
                outcome += `<article class="rounded p-4 px-0">`;
                outcome += `<section class="row">`;
                outcome += `<section class="col-sm-2">`;
                outcome += `<section class="d-flex flex-md-column flex-sm-row justify-content-center justify-content-evenly text-center remark-polls" data-comment-id="${remark["idComment"]}">`;
                if (remark["isVoted"] === 0) {
                  outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                  outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#">${remark["numOfVotes"]}</a></span>`;
                  outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
                } else if (remark["isVoted"] === 1 && remark["typeVote"] === 1) {
                  outcome += `<i class="bi bi-arrow-up-circle polls-boost my-auto"></i>`;
                  outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-boost">${remark["numOfVotes"]}</a></span>`;
                  outcome += `<i class="bi bi-arrow-down-circle my-auto"></i>`;
                } else if (remark["isVoted"] === 1 && remark["typeVote"] === -1) {
                  outcome += `<i class="bi bi-arrow-up-circle my-auto"></i>`;
                  outcome += `<span style="display: block" class="mt-2 mb-2"><a href="#" class="polls-deduct">${remark["numOfVotes"]}</a></span>`;
                  outcome += `<i class="bi bi-arrow-down-circle polls-deduct my-auto"></i>`;
                }
                outcome += `</section>`;
                outcome += `</section>`;
                outcome += `<section class="col-sm-10">`;
                outcome += `<p class="null-border">${remark["content"]}</p>`;
                outcome += `<section class="after-info-box revoke d-flex justify-content-between">`;
                outcome += `<section class="account-info-short d-flex align-middle">`;
                outcome += `<img class="img-fluid my-auto image-header-account" src="http://${$(
                  location
                ).attr("host")}/server/uploads/profilePictures/${
                  remark["profile_image"]
                }" alt="${remark["username"]}-account-image"/>`;
                outcome += `<span class="ms-2"><a href="/account/${remark["ownerId"]}">${remark["username"]}</a> replied</span>`;
                outcome += `</section>`;
                if (remark["timestamp"] / 60 < 60) {
                  outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                    remark["timestamp"] / 60
                  )}m ago</span>`;
                } else if (
                  remark["timestamp"] / 60 >= 60 &&
                  remark["timestamp"] / 60 < 1409
                ) {
                  outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                    remark["timestamp"] / 3600
                  )}h ago</span>`;
                } else {
                  outcome += `<span style="display: block" class="schedule-after">${Math.ceil(
                    remark["timestamp"] / 86400
                  )}d ago</span>`;
                }
                outcome += `</section>`;
                if (remark["isAdmin"] === 1 || remark["isOwner"] === 1) {
                  outcome += `<section class="mt-2">`;
                  outcome += `<button class="remove remark-remove" data-comment-id="${remark["idComment"]}">Delete</button>`;
                  outcome += `</section>`;
                }
                outcome += `</section>`;
                outcome += `</section>`;
                outcome += `</article>`;
              });
              outcome += `</section>`;
              outcome += `</section>`;
              outcome += `</article>`;

              $(".after-outcome-section").append(outcome);

            });
            console.log('LOG');
          } else if (jQuery.isEmptyObject(outcome)) {
            console.log('0');
            $("article").remove();
            $(".after-outcome-section")
              .html(`<section class="scheme-report glitch-data text-center bg-none p-3 mt-2">
						  <img src="http://${$(location).attr(
                "host"
              )}/client/img/error-empty-content.svg" alt="no content available" class="d-block null-data mx-auto">
						  <p class="pt-5">It's a little bit lonely here. We couldn't find anything...</p>
					  </section>`);
          }

        },
      });
    }
  }, 1000);
});
