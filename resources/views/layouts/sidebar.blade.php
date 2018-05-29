  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="/{{ $user->picture }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ $user->firstname}}</p>
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
        @if (in_array($setup->role_id, array(1,2,3,4,5,6,7,8,8,9,10), true))
          <li class="active"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
        @endif

          <li><a href="{{ url('dtr') }}"><i class="fa fa-calendar"></i> <span>My Attendance</span></a></li>
          <li><a href="{{ url('forms') }}"><i class="fa fa-pencil-square-o"></i> <span>File Form</span></a></li>
        
        @if (in_array($setup->role_id, array(1,2,3,4,6,7,8), true))
          <li><a href="{{ url('form-approval') }}"><i class="fa fa-file-text-o"></i> <span>Form Approval</span></a></li>
        @endif
        @if (in_array($setup->role_id, array(2,3), true))
          <li><a href="{{ url('employee-management') }}"><i class="fa fa-users"></i> <span>Employee Management</span></a></li>
        @endif
        @if (in_array($setup->role_Id, array(2,3), true))
          <li><a href="{{ url('reports') }}">Reports</a></li>
        @endif
        
          <li><a href="{{ url('system-management/accounts') }}"><i class="fa fa-sitemap"></i><span>Accounts Leads</span></a></li>
      
        @if (in_array($setup->role_id, array(1,2,3), true))
          <li class="treeview">
            <a href="#"><i class="fa fa-cogs"></i> <span>System Management</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ url('system-management/accounts') }}"><i class="fa fa-sitemap"></i><span>Accounts Leads</span></a></li>
              <li><a href="{{ url('system-management/shift') }}"><i class="fa fa-clock-o"></i><span>Shifts</span></a></li>
              <li><a href="{{ url('system-management/department') }}">Department</a></li>
              <li><a href="{{ url('system-management/division') }}">Division</a></li>
              <li><a href="{{ url('system-management/country') }}">Country</a></li>
              <li><a href="{{ url('system-management/province') }}">Province</a></li>
              <li><a href="{{ url('system-management/city') }}">City</a></li>
            </ul>
          </li>
        @endif
        @if (in_array($setup->role_id, array(1,2), true))
          <li><a href="{{ url('user-management') }}"><i class="fa fa-user"></i> <span>User management</span></a></li>
        @endif
        @if (in_array($setup->role_id, array(2), true))
          <li class="treeview">
            <a href="#"><i class="fa fa-cogs"></i> <span>Timekeeping</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ url('timekeeping/period') }}"><i class="fa fa-file-text"></i><span>Period Cover</span></a></li>
            </ul>
            <ul class="treeview-menu">
              <li><a href="{{ url('timekeeping/process') }}"><i class="fa fa-cog fa-spinner"></i><span>Processing</span></a></li>
            </ul>
          </li>
        @endif
        @if (in_array($setup->role_id, array(1,2), true))
          <li><a href="{{ url('report') }}"><i class="fa fa-line-chart"></i>Report</a></li>
        @endif
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>