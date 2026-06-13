<div class="brand clearfix" style="background-color: #15964b; padding: 15px;">
  <a href="dashboard.php" style="font-size: 25px; color: white; text-decoration: none;">
    INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM
  </a>
  <span class="menu-btn" style="color: white;"><i class="fa fa-bars"></i></span>
  <ul class="ts-profile-nav" style="float: right;">
    <li class="ts-account" style="position: relative;">
      <a href="#" style="color: white;">
        <img src="img/ts-avatar.jpg" class="ts-avatar hidden-side" alt="" style="border-radius: 50%; width: 30px; height: 30px; vertical-align: middle;">
        Account <i class="fa fa-angle-down hidden-side"></i>
      </a>
      <ul style="position: absolute; background-color: white; list-style: none; padding: 10px; border-radius: 4px; top: 100%; right: 0; display: none;">
        <li><a href="change-password.php">Change Password</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </li>
  </ul>
</div>

<script>
// Show/hide dropdown menu on click
document.querySelector('.ts-account > a').addEventListener('click', function (e) {
  e.preventDefault();
  const dropdown = this.nextElementSibling;
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});
</script>
