<a href="/post/{{$post->id}}" class="list-group-item list-group-item-action">
  <img class="avatar-tiny" src="{{$post->author->avatar}}" />
  <strong>{{$post->title}}</strong> 
  <span class="small"> 
    @if(!isset($hideAuthor))
    by {{$post->author->username}} 
    @endif
    on {{$post->created_at->format('j/n/Y')}}</span>
</a>