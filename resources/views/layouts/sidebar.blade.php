  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset("/bower_components/AdminLTE/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ Auth::user()->firstname}}</p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <!-- search form (Optional) -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <!-- Optionally, you can add icons to the links -->
        <li class="active"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="{{ url('forms') }}"><i class="fa fa-pencil-square-o"></i> <span>File Form</span></a></li>
        <li><a href="{{ url('employee-management') }}"><i class="fa fa-users"></i> <span>Employee Management</span></a></li>
        <li class="treeview">
          <a href="#"><i class="fa fa-cogs"></i> <span>System Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('system-management/shift') }}"><i class="fa fa-clock-o"></i><span>Shifts</span></a></li>
            <li><a href="{{ url('system-management/department') }}">Department</a></li>
            <li><a href="{{ url('system-management/division') }}">Division</a></li>
            <li><a href="{{ url('system-management/country') }}">Country</a></li>
            <li><a href="{{ url('system-management/province') }}">Province</a></li>
            <li><a href="{{ url('system-management/city') }}">City</a></li>
            <li><a href="{{ url('system-management/report') }}">Report</a></li>
          </ul>
        </li>
        <li><a href="{{ route('user-management.index') }}"><i class="fa fa-user"></i> <span>User management</span></a></li>
        <li class="treeview">
          <a href="#"><i class="fa fa-clock-o"></i> <span>Timekeeping</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('timekeeping.period_cover') }}"><i class="fa fa-file"></i><span>Period Cover</span></a></li>
          </ul>
        </li>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>