<style>
.brand {
  background-color: #4CAF50;
  color: white;
  padding: 8px 15px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  font-size: 14px;
}

.brand a {
  color: white;
  font-size: 18px;
  font-weight: 600;
  text-decoration: none;
}

.brand a:hover {
  color: #C8E6C9;
}

.menu-btn {
  font-size: 20px;
  cursor: pointer;
  color: white;
  margin-left: 15px;
}

.ts-profile-nav {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  align-items: center;
}

.ts-profile-nav li {
  position: relative;
  margin-left: 15px;
}

.ts-profile-nav li a {
  color: white;
  text-decoration: none;
  display: flex;
  align-items: center;
  font-size: 14px;
}

.ts-profile-nav li a:hover {
  color: #C8E6C9;
}

.ts-avatar {
  border-radius: 50%;
  margin-right: 6px;
  width: 28px;
  height: 28px;
  object-fit: cover;
}

.ts-account ul {
  display: none;
  position: absolute;
  background: white;
  color: #333;
  top: 35px;
  right: 0;
  min-width: 150px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  border-radius: 4px;
  padding: 5px 0;
  z-index: 1000;
}

.ts-account:hover ul {
  display: block;
}

.ts-account ul li {
  padding: 8px 15px;
}

.ts-account ul li a {
  color: #4CAF50;
  font-weight: 500;
  font-size: 13px;
}

.ts-account ul li a:hover {
  background-color: #4CAF50;
  color: white;
  display: block;
  border-radius: 4px;
}
</style>

<div class="brand clearfix">
  <div style="display: flex; align-items: center;">
    <span class="menu-btn"><i class="fa fa-bars"></i></span>
    <a href="dashboard.php" style="margin-left: 10px;">INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</a>
  </div>
  <ul class="ts-profile-nav">
    <li class="ts-account">
      <a href="#">
        <img src="img/ts-avatar.jpg" class="ts-avatar hidden-side" alt="User Avatar"> 
        Account <i class="fa fa-angle-down hidden-side"></i>
      </a>
      <ul>
        <li><a href="change-password.php">Change Password</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </li>
  </ul>
</div>
