<nav aria-label="breadcrumb" role="navigation">
<ol class="breadcrumb page-head-nav">

	@foreach( $parents as $parent)
		<li class="breadcrumb-item">
			<a href="{{ $parent['url'] }}">{{ $parent['anchorText'] }}</a></li>
	@endforeach
  
  <li class="breadcrumb-item active">{{ $active }}</li>
</ol>
</nav>