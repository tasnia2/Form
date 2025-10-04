<?php  ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Registration Form</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="styles.css" rel="stylesheet"> 
</head>
<body>
  <div class="wrapper">
    <div class="panel-header">
      <h3>Registration Form</h3>
    </div>
    <div class="panel-body">
      <?php if (isset($_GET['ok'])): ?>
        <div class="alert success">Registration successfully completed.</div>
      <?php elseif (isset($_GET['err'])): ?>
        <div class="alert error"><?php echo htmlspecialchars($_GET['err']); ?></div>
      <?php endif; ?>

     <form method="post" action="register.php" novalidate>

  <div class="row">
    <div class="input-group half">
      <label for="firstName">First Name</label>
      <input id="firstName" name="firstName" type="text" required>
    </div>
    <div class="input-group half">
      <label for="lastName">Last Name</label>
      <input id="lastName" name="lastName" type="text" required>
    </div>
  </div>


  <div class="input-group">
    <label for="dob">Date of Birth</label>
    <input id="dob" name="dob" type="date" required>
  </div>

 
  <div class="input-group gender-box">
    <label class="gender-title">Gender</label>
    <div class="gender-options">
      <label><input type="radio" name="gender" value="male" required> Male</label>
<label><input type="radio" name="gender" value="female" required> Female</label>
<label><input type="radio" name="gender" value="other" required> Other</label>


    </div>
  </div>

 
  <div class="input-group">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required>
  </div>

 
  <div class="input-group">
    <label for="password">Password</label>
    <input id="password" name="password" type="password" minlength="6" required>
  </div>

 
  <div class="input-group">
    <label for="number">Phone Number</label>
    <input id="number" name="number" type="tel" placeholder="+8801XXXXXXXXX" required>
  </div>

  <button type="submit" class="btn">Submit</button>
</form>

    </div>
  </div>
</body>
</html>
