// home.js
document.getElementById("profile-page").addEventListener("click", function () {
  window.location.href = "profile.php";
});

document.getElementById("button-post").addEventListener("click", function () {
  document.getElementById("post-section").classList.remove("hidden");
});

document.getElementById("searchButton").addEventListener("click", function () {
  var query = document.getElementById("searchInput").value;
  if (query.trim() !== "") {
    fetchSearchResults(query);
  }
});

function fetchSearchResults(query) {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "search.php?q=" + encodeURIComponent(query), true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      document.getElementById("results").innerHTML = xhr.responseText;
    } else {
      document.getElementById("results").innerHTML = "Error fetching results!";
    }
  };
  xhr.send();
}

document.getElementById("searchButton").addEventListener("click", function () {
  document.getElementById("search-hide").classList.remove("hidden");
});

document.getElementById("search-cancel").addEventListener("click", function () {
  document.getElementById("search-hide").classList.add("hidden");
});

// Handle comment form submissions
document.querySelectorAll(".comment-form").forEach((form) => {
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("comment_post.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          // Clear the textarea
          this.querySelector('textarea[name="comment"]').value = "";
          // Refresh the page to show the new comment
          location.reload();
        } else {
          console.error(
            "Comment submission failed:",
            data.error || "Unknown error"
          );
          alert(
            "Failed to post comment: " + (data.error || "Please try again.")
          );
        }
      })
      .catch((error) => {
        console.error("Error posting comment:", error);
        alert("An error occurred while posting the comment. Please try again.");
      });
  });
});

// Handle like button clicks
document.querySelectorAll(".like-button").forEach((button) => {
  button.addEventListener("click", function () {
    const postId = this.getAttribute("data-post-id");
    fetch("like_post.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `post_id=${postId}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          location.reload(); // Refresh to update like count
        } else {
          alert(
            "Failed to like/unlike post: " + (data.error || "Please try again.")
          );
        }
      })
      .catch((error) => console.error("Error:", error));
  });
});
