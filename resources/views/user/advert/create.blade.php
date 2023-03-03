<div class="row">
  <div class="col">
    <h2>New text ad</h2>
  </div>
</div>
<div class="row">
  <div class="col">
    
    @if ($errors->any())
    
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    {!! Form::open(['url' => 'user/adverts'])!!}
      
      {!! Form::hidden('adgroup_id', $adgroup->id) !!}
      
      <div class="form-group" style="margin-bottom:1.538rem">
        <label for="final_urls">Final URL</label>
        {{ Form::text('final_urls',$defaultFinalURL, ['class'=>'form-control','placeholder'=>'Final URL', 'id' => 'final_urls']) }}
      </div>
      
      <div class="form-group" style="margin-bottom:1.538rem">
        <label for="headline_1">Headline 1</label>
        {{ Form::text('headline_1',null, ['class'=>'form-control adwords-headline','placeholder'=>'Headline 1', 'id' => 'headline_1']) }}
      </div>
      
      <div class="form-group" style="margin-bottom:1.538rem">
        <label for="headline_2">Headline 2</label>
        {{ Form::text('headline_2',null, ['class'=>'form-control adwords-headline','placeholder'=>'Headline 2', 'id' => 'headline_2']) }}
        
      </div>
      <div class="row">
        <div class="col">
          <label for="path_1">Display path</label>
        </div>
      </div>
      <div class="row">
        <div class="form-group col" style="margin-bottom:1.538rem">
          
          {{ Form::text('path_1',null, ['class'=>'form-control adwords-url','placeholder'=>'Path 1', 'id' => 'path_1']) }}
        </div>
        <div class="form-group col" style="margin-bottom:1.538rem">
          
          {{ Form::text('path_2',null, ['class'=>'form-control adwords-url','placeholder'=>'Path  2', 'id' => 'path_2']) }}
        </div>
    </div>

      <div class="form-group" style="margin-bottom:1.538rem">
        <label for="description">Description</label>
        {{ Form::textarea('description',null, ['class'=>'form-control adwords-description','placeholder'=>'Description', 'rows'=>2, 'id' => 'description']) }}
      </div>

      <button data-toggle="collapse" data-target="#create-advert" type="button" class="btn btn-secondary md-close">Cancel</button>
      
      <button type="submit" class="btn btn-primary md-close">Create ad</button>

    {!! Form::close() !!}
  </div>
  <div class="col">
    <div class="tab-container">
      <ul role="tablist" class="nav nav-tabs">
        
        <li class="nav-item"><a href="#account" data-toggle="tab" role="tab" class="nav-link active">Account</a></li>
        
        <li class="nav-item"><a href="#campaign" data-toggle="tab" role="tab" class="nav-link">Campaign</a></li>
        
        <li class="nav-item"><a href="#adgroup" data-toggle="tab" role="tab" class="nav-link">Ad Group</a></li>
      
      </ul>

      <div class="tab-content">
    
        <div id="account" role="tabpanel" class="tab-pane active">

          <h3>Headline 1</h3>
              
          @forelse($accountWinners->headline_1 as $text)

            <p class="use-winner" data-target="headline_1" data-value="{{ $text }}">

            << {{ $text }}

          @empty
            <p>No recommendations are available.

          @endforelse
          
          <h3>Headline 2</h3>
          @forelse($accountWinners->headline_2 as $text)

            <p class="use-winner" data-target="headline_2" data-value="{{ $text }}">

            << {{ $text }}

          @empty
            <p>No recommendations are available.

          @endforelse
          
          <h3>Display path</h3>
          @forelse($accountWinners->path as $text)

            <p class="use-winner" data-target="path" data-value="{{ $text }}">

            << {{ $text }}
          
          @empty
            <p>No recommendations are available.
          
          @endforelse

          <h3>Description</h3>
          @forelse($accountWinners->description as $text)

            <p class="use-winner" data-target="description" data-value="{{ $text }}">

            << {{ $text }}
          
          @empty
            <p>No recommendations are available.
          
          @endforelse

        </div>

        <div id="campaign" role="tabpanel" class="tab-pane">
          <h3>Headline 1</h3>
              
          @forelse($campaignWinners->headline_1 as $text)

            <p class="use-winner" data-target="headline_1" data-value="{{ $text }}">

            << {{ $text }}

          @empty
            <p>No recommendations are available.
          
          @endforelse
          
          <h3>Headline 2</h3>
          @forelse($campaignWinners->headline_2 as $text)

            <p class="use-winner" data-target="headline_2" data-value="{{ $text }}">

            << {{ $text }}

          @empty
            <p>No recommendations are available.
          @endforelse
          
          <h3>Display path</h3>
          @forelse($campaignWinners->path as $text)

            <p class="use-winner" data-target="path" data-value="{{ $text }}">

            << {{ $text }}
          
          @empty
            <p>No recommendations are available.
          @endforelse

          <h3>Description</h3>
          @forelse($campaignWinners->description as $text)

            <p class="use-winner" data-target="description" data-value="{{ $text }}">

            << {{ $text }}
          
          @empty
            <p>No recommendations are available.
          @endforelse
        </div>

        <div id="adgroup" role="tabpanel" class="tab-pane">
          <h3>Headline 1</h3>
              
          @forelse($adgroupWinners->headline_1 as $text)

            <p class="use-winner" data-target="headline_1" data-value="{{ $text }}">

            << {{ $text }}
          @empty
            <p>No recommendations are available.
          @endforelse
          
          <h3>Headline 2</h3>
          @forelse($adgroupWinners->headline_2 as $text)

            <p class="use-winner" data-target="headline_2" data-value="{{ $text }}">
            << {{ $text }}
          @empty
            <p>No recommendations are available.
          
          @endforelse
          
          <h3>Display path</h3>
          @forelse($adgroupWinners->path as $text)

            <p class="use-winner" data-target="path" data-value="{{ $text }}">

            << {{ $text }}
          @empty
            <p>No recommendations are available.

          @endforelse

          <h3>Description</h3>
          @forelse($adgroupWinners->description as $text)

            <p class="use-winner" data-target="description" data-value="{{ $text }}">

            << {{ $text }}
          @empty
            <p>No recommendations are available.

          @endforelse

        </div>
      </div>
    </div>
  </div>
</div>

@section('javascript')
  @if ($errors->any())
    <script>
      $("#create-advert").collapse("show");
    </script>
  @endif

  @if($showCreator)
  <script>
      $("#create-advert").collapse("show");
    </script>
  @endif

  <script>

    $("#headline_1, #headline_2").maxlength({
      alwaysShow: true,
      placement: "bottom-right-inside",
      customMaxAttribute: "30",
      limitReachedClass:"text-danger",
      validate:true
      
    });

    $("#path_1, #path_2").maxlength({
      alwaysShow: true,
      placement: "bottom-right-inside",
      customMaxAttribute: "15",
      limitReachedClass:"text-danger",
      validate:true
      
    });

    $("#description").maxlength({
      alwaysShow: true,
      placement: "bottom-right-inside",
      customMaxAttribute: "80",
      limitReachedClass:"text-danger",
      validate:true
      
    });

    $(".use-winner").click(function(e){
      var target = $(this).data("target");
      var value = $(this).data("value");

      if (target=="path") {
        $a = value.split("/");

        $("#path_1").val($a[0]);
        $("#path_2").val($a[1]);
        
      }
      else{
        $("#" + target).val(value);
      }

      return false;
    });

  </script>

@endsection