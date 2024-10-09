<div class="container">
   <h1>{{ $title }}</h1>
   <p>{{ $description }}</p>
   <h2>{{ $subtitle }}</h2>
   <p>{!! $content !!}</p>
   <ul>
      @foreach($phones as $phone)
      <li>{{ $phone }}</li>
      @endforeach
   </ul>
</div>
