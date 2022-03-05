<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="{{route('dashboard.index')}}">&#x2800 Accueil &#x2800<span class="sr-only"></span></a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="{{route('users.show', Auth::User()->id)}}">&#x2800 Historique &#x2800<span class="sr-only"></span></a>
      </li>
    </ul>
</div>