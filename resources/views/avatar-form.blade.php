<x-layout pagetitle="Manage Your Avatar">
  <div class="container container--narrow py-md-5">
    <h2 class="mb-3 text-center">Upload a new avatar</h2>
    {{-- enctype needed to recognise attached file --}}
    <form action="/manage-avatar" method="POST" enctype="multipart/form-data"> 
      @csrf
      <div class="mb-3">
        <input type="file" name="avatar" id="avatar" required>
        @error('avatar')
            <p class="alert alert-danger small">{{$message}}</p>
        @enderror

      </div>
      <button class="btn btn-primary">Save</button>
    </form>
  </div>
</x-layout>