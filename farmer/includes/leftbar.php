<style>
.ts-sidebar {
  background-color: #2E7D32; /* Dark green background */
  width: 220px;
  min-height: 100vh;
  padding-top: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: white;
}

.ts-sidebar-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.ts-sidebar-menu li {
  padding: 12px 20px;
  cursor: pointer;
  position: relative;
}

.ts-sidebar-menu li a {
  color: white;
  text-decoration: none;
  display: flex;
  align-items: center;
  font-weight: 600;
}

.ts-sidebar-menu li a:hover {
  background-color: #66BB6A; /* lighter green on hover */
  color: #1B5E20;
  border-radius: 4px;
}

.ts-sidebar-menu li i {
  margin-right: 10px;
  font-size: 18px;
}

/* Label style */
.ts-label {
  font-size: 14px;
  font-weight: 700;
  padding: 12px 20px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #A5D6A7;
  cursor: default;
}

/* Submenu styles */
.ts-sidebar-menu li ul {
  list-style: none;
  padding-left: 20px;
  display: none; /* hidden by default */
  margin-top: 5px;
}

.ts-sidebar-menu li:hover > ul {
  display: block;
}

.ts-sidebar-menu li ul li {
  padding: 8px 20px;
  font-weight: 400;
}

.ts-sidebar-menu li ul li a:hover {
  background-color: #A5D6A7;
  color: #1B5E20;
  border-radius: 4px;
}
</style>

<nav class="ts-sidebar">
  <ul class="ts-sidebar-menu">
    <li class="ts-label">Main</li>
    <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
    
    <li>
      <a href="#"><i class="fa fa-files-o"></i> Support <i class="fa fa-caret-down" style="margin-left:auto;"></i></a>
      <ul>
        <li><a href="addsupport.php">Request support</a></li>
        <li><a href="supportstatus.php">View Support Status</a></li>
      </ul>
    </li>
  </ul>
</nav>
