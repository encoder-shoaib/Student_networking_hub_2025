document.getElementById('profile-page').addEventListener('click', function() {
    // Navigate to another page
    window.location.href = 'profile.php'; // Replace 'profile.html' with the desired URL
});

document.getElementById('button-post').addEventListener('click', function () {
    console.log('llksdkjlflksdfj')
    document.getElementById('post-section').classList.remove('hidden');
});


document.getElementById("searchButton").addEventListener("click", function() {
    var query = document.getElementById("searchInput").value;
    if(query.trim() !== "") {
        fetchSearchResults(query);
    }
});

function fetchSearchResults(query) {
    // Send an AJAX request to the PHP server
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "search.php?q=" + encodeURIComponent(query), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById("results").innerHTML = xhr.responseText;
        } else {
            document.getElementById("results").innerHTML = "Error fetching results!";
        }
    };
    xhr.send();
}


// search hide 

document.getElementById('searchButton').addEventListener('click',function(){
    document.getElementById('search-hide').classList.remove('hidden');
})


document.getElementById('search-cancel').addEventListener('click',function(){
    document.getElementById('search-hide').classList.add('hidden');
})


