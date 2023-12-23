  // Check if the user has already accepted cookies
  if (!localStorage.getItem('cookiesAccepted')) {
    // Show the cookie banner
    document.getElementById('cookie-banner').style.display = 'block';
  }

  // Handle cookie acceptance
  document.getElementById('accept-cookies').addEventListener('click', function() {
    // Set a localStorage flag to remember the acceptance
    localStorage.setItem('cookiesAccepted', true);

    // Hide the cookie banner
    document.getElementById('cookie-banner').style.display = 'none';
  });

  // Recent views functionality
  window.addEventListener('DOMContentLoaded', function() {
    // Retrieve the recent articles from localStorage
    var recentArticles = JSON.parse(localStorage.getItem('recentArticles')) || [];

    // Display the recent articles in the recent views section
    var recentViews = document.getElementById('recent-articles');
    recentArticles.forEach(function(article) {
      var listItem = document.createElement('li');
      listItem.innerHTML = '<img src="' + article.image + '" alt="Article Image">' +
        '<a href="' + article.url + '">' + article.title + '</a>';
      recentViews.appendChild(listItem);
    });

    // Save the article data when the user visits an article
    var articles = document.querySelectorAll('.blog-post a');
    articles.forEach(function(article) {
      article.addEventListener('click', function() {
        var articleData = {
          title: article.querySelector('h2').textContent,
          image: article.querySelector('img').src,
          url: article.href
        };

        // Add the article to the recent articles list
        recentArticles.unshift(articleData);

        // Limit the number of recent articles to 5
        if (recentArticles.length > 5) {
          recentArticles.pop();
        }

        // Update the recent articles in localStorage
        localStorage.setItem('recentArticles', JSON.stringify(recentArticles));
      });
    });

    // Show the recent views section
    document.getElementById('recent-views').style.display = 'block';
  });