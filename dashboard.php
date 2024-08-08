
<?php
session_start(); // Start the session

// Check if session is not set or user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.html");
    exit();
}

// Get the username from the session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticket Table</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/simplePagination.css">
  <style>
    /* Custom styles */
    #logo {
      max-width: 100px;
      height: auto;
    }
    .status-dropdown {
      width: 150px;
    }
    .status-completed {
      background-color: green;
      color: white;
    }
    table, th, td {
      border: 1px solid black;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      padding: 8px;
      text-align: left;
    }
    .table th:nth-child(4), .table td:nth-child(4) {
      min-width: 300px;
      max-width: 500px;
      word-wrap: break-word;
      overflow-wrap: break-word;
      white-space: normal;
    }
    .small-search-input {
      width: 30%;
      border: 1px solid black;
    }
    .username-dropdown {
      cursor: pointer;
      padding: 10px;
      border: 1px solid #ddd;
    }
    .dropdown-menu {
      min-width: 150px;
    }
    body {
      text-align: left;
    }
    header {
      text-align: left;
      margin: 20px;
    }
    .container {
      max-width: 100%;
      padding: 0;
    }
    .table-container {
      margin: 100px;
    }
    #dropdownMenuButton {
      min-width: 80px;
      margin-right: 2%;
    }
    .small-button {
    font-size: 0.90rem; /* Smaller font size */
    padding: 0.25rem 0.5rem; /* Smaller padding */
    height: auto; /* Adjust height automatically */
    min-width: 90px; /* Set a minimum width if needed */
}
/* Custom styling for the dropdown menu */
.custom-dropdown-menu {
    font-size: 0.90rem; /* Smaller font size */
    padding: 0.25rem 0.5rem; /* Smaller padding */
    height: auto; /* Adjust height automatically */
    min-width: 90px;
    margin-top: 5px; /* Top margin from the button */
    border-radius: 0.25rem; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); /* Subtle shadow */
    padding: 0.5rem; /* Padding inside the dropdown */
    background-color: black; /* Background color */
    color:white;
}

/* Style for dropdown items */
.custom-dropdown-menu .dropdown-item {
    padding: 0.5rem 1rem; /* Padding for each item */
    border-radius: 0.25rem; /* Rounded corners for items */

    color:white; /* Smooth background color transition */
}

/* Hover effect for dropdown items */
.custom-dropdown-menu .dropdown-item:hover {
    background-color:red; /* Light background color on hover */
}

/* Optional: style for active state */
.custom-dropdown-menu .dropdown-item:active {
    background-color: red; /* Slightly darker background on click */
}

  </style>
</head>
<body>
<header class="bg-light py-3 text-center">
  <div class="d-flex justify-content-between align-items-center">
    <img id="logo" src="ve.jpeg" alt="Logo" class="mb-3">
    <h1>Welcome to the IT Ticket System</h1>
    <div class="dropdown">
  <button class="btn btn-danger dropdown-toggle username-dropdown small-button" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <?php echo htmlspecialchars($username); ?>
  </button>
  <div class="dropdown-menu custom-dropdown-menu" aria-labelledby="dropdownMenuButton">
      <a class="dropdown-item" href="#" id="logout">Logout</a>
  </div>
</div>
  </div>
</header>
<div class="container mt-5">
  <div class="table-container">
    <h2>Ticket Queries</h2>
    <div class="form-group">
      <input type="text" id="searchInput" class="form-control small-search-input" placeholder="Search...">
    </div>
    <div id="attendedByCounts" class="mb-4">
      <!-- Attended By counts will be loaded here -->
    </div>
    <table class="table table-striped">
      <thead class="thead-dark">
        <tr>
          <th scope="col">Ticket No.</th>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Query</th>
          <th scope="col">Type</th>
          <th scope="col">Office</th>
          <th scope="col">Priority</th>
          <th scope="col">High Priority Description</th>
          <th scope="col">Submit Date</th>
          <th scope="col">Status</th>
          <th scope="col">Attended By</th>
        </tr>
      </thead>
      <tbody id="ticketTableBody">
        <!-- Data will be loaded here -->
      </tbody>
    </table>
    <div id="pagination-container"></div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/jquery.simplePagination.js"></script>
<script>
  // Load table data with pagination
  function loadTableData(query = '', page = 1) {
    $.ajax({
        url: 'fetch_tickets.php',
        method: 'GET',
        data: { search: query, page: page },
        success: function(data) {
            const response = JSON.parse(data);
            $('#ticketTableBody').html(response.tickets);

            // Display attended_by counts
            let attendedByCountsHtml = '<h3>Attended By Counts:</h3><ul>';
            $.each(response.attended_by_counts, function(name, count) {
                attendedByCountsHtml += `<li>${name}: ${count}</li>`;
            });
            attendedByCountsHtml += '</ul>';
            $('#attendedByCounts').html(attendedByCountsHtml);

            $('#pagination-container').pagination({
                items: response.total,
                itemsOnPage: 10,
                currentPage: page,
                cssStyle: 'light-theme',
                onPageClick: function(pageNumber) {
                    localStorage.setItem('currentPage', pageNumber);
                    loadTableData(query, pageNumber);
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading table data: ' + error);
        }
    });
}

  // Load data on page load
  $(document).ready(function() {
    const savedPage = localStorage.getItem('currentPage') || 1; // Get saved page or default to 1
    const savedQuery = localStorage.getItem('searchQuery') || '';
    $('#searchInput').val(savedQuery);
    loadTableData(savedQuery, savedPage);

    // Reload data every 30 seconds
    setInterval(() => {
        const query = $('#searchInput').val();
        loadTableData(query, savedPage);
    }, 30000);

    // Search input handler
    $('#searchInput').on('input', function() {
        const query = $(this).val();
        localStorage.setItem('searchQuery', query); // Save search query
        loadTableData(query, savedPage);
    });

    // Update status on change
    $(document).on('change', '.status-dropdown', function() {
      var status = $(this).val();
      var ticketId = $(this).data('ticket-id');
      alert('Status changed to: ' + status);

      // Perform AJAX update to your PHP script for updating status
      $.ajax({
        url: 'update_status.php',
        method: 'POST',
        data: { ticketId: ticketId, status: status },
        success: function(response) {
          console.log('Status updated successfully');
          const query = $('#searchInput').val();
          loadTableData(query, savedPage); // Reload table data after update
        },
        error: function(xhr, status, error) {
          console.error('Error updating status: ' + error);
        }
      });
    });

    // Logout handler
    $('#logout').on('click', function(e) {
      e.preventDefault();
      $.ajax({
        url: 'logout.php',
        method: 'POST',
        success: function() {
          window.location.href = 'login.html'; // Redirect to login page
        },
        error: function(xhr, status, error) {
          console.error('Error logging out: ' + error);
        }
      });
    });
  });
</script>
</body>
</html>
