<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registration Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-100 flex flex-col items-center p-6">

<div class="w-full max-w-4xl flex flex-col items-center">

  <!-- Registration Form (frosted wrapper) -->
  <div class="wrapper p-6 mb-10 w-full max-w-2xl">
    <h3 class="text-2xl font-semibold mb-6 text-center">Registration Form</h3>

    <?php if (isset($_GET['ok'])): ?>
      <div class="bg-green-200 text-green-800 p-3 rounded mb-4">Operation completed successfully.</div>
    <?php elseif (isset($_GET['err'])): ?>
      <div class="bg-red-200 text-red-800 p-3 rounded mb-4"><?php echo htmlspecialchars($_GET['err']); ?></div>
    <?php endif; ?>

    <!-- Registration Form -->
    <form method="post" action="register.php" id="registrationForm" class="space-y-4" enctype="multipart/form-data">
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-medium mb-1">First Name</label>
          <input type="text" name="firstName" required class="w-full border rounded p-2">
        </div>
        <div>
          <label class="block font-medium mb-1">Last Name</label>
          <input type="text" name="lastName" required class="w-full border rounded p-2">
        </div>
      </div>

      <div>
        <label class="block font-medium mb-1">Date of Birth</label>
        <input type="date" name="dob" required class="w-full border rounded p-2">
      </div>

      <div>
        <label class="block font-medium mb-1">Gender</label>
        <div class="flex gap-4">
          <label class="flex items-center gap-1"><input type="radio" name="gender" value="male" required> Male</label>
          <label class="flex items-center gap-1"><input type="radio" name="gender" value="female" required> Female</label>
          <label class="flex items-center gap-1"><input type="radio" name="gender" value="other" required> Other</label>
        </div>
      </div>

      <div>
        <label class="block font-medium mb-1">Email</label>
        <input type="email" name="email" required class="w-full border rounded p-2">
      </div>

      <div>
        <label class="block font-medium mb-1">Password</label>
        <input type="password" name="password" minlength="6" class="w-full border rounded p-2" placeholder="Enter only for new registration">
      </div>

      <div>
        <label class="block font-medium mb-1">Phone Number</label>
        <input type="tel" name="number" required class="w-full border rounded p-2" placeholder="+8801XXXXXXXXX">
      </div>

      <div>
        <label class="block font-medium mb-1">Profile Picture</label>
        <input type="file" name="picture" accept="image/*" class="w-full border rounded p-2">
      </div>

      <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Submit</button>
    </form>
  </div>

  <!-- Registered Users List -->
  <div class="info-container mt-10 w-full max-w-2xl">
    <?php
    $result = $mysqli->query("SELECT * FROM registration ORDER BY id DESC");
    while($row = $result->fetch_assoc()):
        $img = (isset($row['picture']) && $row['picture']) ? "uploads/" . $row['picture'] : "uploads/default.png";
        $createdAt = $row['created_at'] ?? '';
        $lastUpdated = $row['updated_at'] ?? $createdAt;
    ?>
<div class="info-item flex items-center gap-4 p-4 border-b border-gray-300">
    <!-- Left: Profile Picture -->
    <div class="flex justify-center items-center w-1/6">
        <img src="<?php echo $img; ?>" alt="Profile Picture" class="rounded-full w-20 h-20 object-cover">
    </div>

    <!-- Left: Name, Email, Phone -->
    <div class="info-left flex flex-col gap-0 w-1/3">
        <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($row['firstName'].' '.$row['lastName']); ?></span>
        <span class="text-gray-600 text-sm"><?php echo htmlspecialchars($row['email']); ?></span>
        <span class="text-gray-600 text-sm"><?php echo htmlspecialchars($row['phone']); ?></span>
    </div>

    <!-- Middle: Submitted + Last Updated -->
    <div class="info-middle flex flex-col gap-1 w-1/3 text-gray-700 text-sm">
        <span>Submitted: <?php echo htmlspecialchars($createdAt); ?></span>
        <span>Last Updated: <?php echo htmlspecialchars($lastUpdated); ?></span>
    </div>

    <!-- Right: Edit / Delete vertically -->
    <div class="info-right flex flex-col gap-2 w-1/3">
        <button onclick="openModal(<?php echo $row['id']; ?>)" class="btn-action edit w-full">Edit</button>
        <form method="post" action="register.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="btn-action delete w-full">Delete</button>
        </form>
    </div>
</div>


    <?php endwhile; ?>
  </div>

</div>

<!-- Modals (move outside wrapper/info-container to float above page) -->
<?php
$result = $mysqli->query("SELECT * FROM registration ORDER BY id DESC");
while($row = $result->fetch_assoc()):
?>
<div id="modal-<?php echo $row['id']; ?>" class="modal hidden">
  <div class="modal-content">
    <h3 class="text-xl font-semibold mb-4 text-center">Edit User</h3>
    <form method="post" action="register.php" class="space-y-3" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label>First Name</label>
          <input type="text" name="firstName" value="<?php echo htmlspecialchars($row['firstName']); ?>" class="w-full border rounded p-2">
        </div>
        <div>
          <label>Last Name</label>
          <input type="text" name="lastName" value="<?php echo htmlspecialchars($row['lastName']); ?>" class="w-full border rounded p-2">
        </div>
      </div>

      <div>
        <label>Date of Birth</label>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($row['dob']); ?>" class="w-full border rounded p-2">
      </div>

      <div>
        <label>Gender</label>
        <div class="flex gap-4">
          <label><input type="radio" name="gender" value="male" <?php echo ($row['gender']==='male') ? 'checked' : ''; ?>> Male</label>
          <label><input type="radio" name="gender" value="female" <?php echo ($row['gender']==='female') ? 'checked' : ''; ?>> Female</label>
          <label><input type="radio" name="gender" value="other" <?php echo ($row['gender']==='other') ? 'checked' : ''; ?>> Other</label>
        </div>
      </div>

      <div>
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" class="w-full border rounded p-2">
      </div>

      <div>
        <label>Phone Number</label>
        <input type="tel" name="number" value="<?php echo htmlspecialchars($row['phone']); ?>" class="w-full border rounded p-2">
      </div>

      <div>
        <label>Profile Picture</label>
        <input type="file" name="picture" accept="image/*" class="w-full border rounded p-2">
      </div>

      <div class="flex justify-end gap-2 mt-4">
        <button type="submit" class="btn-action edit">Save</button>
        <button type="button" onclick="closeModal(<?php echo $row['id']; ?>)" class="btn-action delete">Cancel</button>
      </div>
    </form>
  </div>
</div>
<?php endwhile; ?>

<script src="main.js"></script>
</body>
</html>
