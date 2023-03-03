@isset($account->id)

<div class="left-sidebar-wrapper">
  
    <div id="close-sidebar"><svg class="svg-inline--fa fa-times fa-w-11" aria-hidden="true" data-prefix="fas" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512" data-fa-i2svg=""><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg><!-- <i class="fas fa-times"></i> -->
    </div>
  
	<div class="left-sidebar-spacer">
    	<div class="left-sidebar-scroll">
      		<div class="left-sidebar-content">
        		<ul class="sidebar-elements">

              <li class="parent">

                    <a href="">Accounts</a>
                    <ul>

                        <li class="">
                            <a href="{{ url('user/accounts') }}">View All</a>
                        </li>
                        <li class="">
                      
                            <a href="{{ url('user/feed/' . $account->id) }}">{{ $account->name }}</a>
                
                        </li>
                    </ul>
         
              </li>
              

              <li class="parent">
                <a href="">Reports</a>
                <ul class="sub-menu">
                    
                  <li><a href="{{ url('user/ngramperformance/' . $account->id) }}">Ad text n-grams</a></li>
                  <li><a href="{{ url('user/searchqueryperformance/' . $account->id) }}">Search queries</a></li>

                </ul>
              </li>
                    
              <li>
                <a href="{{ url('user/budget-commander/' . $account->id) }}">Budget Commander</a>
              </li>

        		</ul>
      		</div>
    	</div>
  	</div>
</div>
@endisset