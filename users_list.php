<?php
// users_list.php
require 'config.php';

// Only admin users should access this page
if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

// Fetch all users
$query = "SELECT UserID, Username, Email, IsAdmin FROM Users ORDER BY Username ASC";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User List - Admin - Cleaning App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function searchUsers() {
        var input = document.getElementById("userSearchInput").value.toLowerCase();
        var table = document.getElementById("usersTable");
        var tr = table.getElementsByTagName("tr");
        for (var i = 1; i < tr.length; i++) { // skip header row
            var tdUsername = tr[i].getElementsByTagName("td")[1]; // Username column
            var tdEmail = tr[i].getElementsByTagName("td")[2]; // Email column
            if (tdUsername || tdEmail) {
                var usernameText = tdUsername.textContent || tdUsername.innerText;
                var emailText = tdEmail.textContent || tdEmail.innerText;
                if (usernameText.toLowerCase().indexOf(input) > -1 || emailText.toLowerCase().indexOf(input) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    </script>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2>User List</h2>
        <input type="text" id="userSearchInput" onkeyup="searchUsers()" placeholder="Search by username or email"
            class="form-control mb-3">
        <table class="table table-bordered" id="usersTable">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Admin?</th>
                    <th>Profile</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                    <td><?php echo htmlspecialchars($user['Username']); ?></td>
                    <td><?php echo htmlspecialchars($user['Email']); ?></td>
                    <td><?php echo $user['IsAdmin'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="user_profile.php?userid=<?php echo $user['UserID']; ?>"
                            class="btn btn-primary btn-sm">View Profile</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>