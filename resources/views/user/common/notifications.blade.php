<!--not used: this puts a list if notifications in the top menu-->
<li class="nav-item dropdown">
	<a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
@if(Auth::user()->unreadNotifications->isEmpty())
	<span class="icon mdi mdi-notifications-none"></span>
@else
	<span class="icon mdi mdi-notifications"  style="color:red;"></span><span class="indicator"></span>
@endif

	</a>
<ul class="dropdown-menu be-notifications">
  <li>
    <div class="title">Notifications<span class="badge badge-pill">{{ count(Auth::user()->unreadNotifications) }}</span></div>
    <div class="list">
      <div class="be-scroller">
        <div class="content">
          <ul>
          	@foreach(Auth::user()->unreadNotifications as $notification)
            <li class="notification bg-danger">
            	<a href="#">
                <div class="notification">
                  <div class="text text-white">
                  	{{ $notification->data['message'] }}
                  </div><span class="date text-white">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
              </a>
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </li>
</ul>
</li>