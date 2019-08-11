<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top" id="pageHeader">
    <div class="container">
        <a class="navbar-brand" href="/">
            zack6849
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="navbar-nav mr-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPhotography" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-camera"></i>Photography
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownPhotography">
                        <a class="dropdown-item" target="_blank" rel="noopener"
                           href="https://www.flickr.com/photos/zack6849/"><i class="fab fa-flickr"></i>Flickr</a>
                        <a class="dropdown-item" target="_blank" rel="noopener"
                           href="https://www.instagram.com/zack6849/"><i class="fab fa-instagram"></i>Instagram</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownCode" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-code"></i>Code
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownCode">
                        <a class="dropdown-item" target="_blank" rel="noopener" href="https://stackoverflow.com/users/1932789/zack6849">
                            <i class="fab fa-stack-overflow"></i>StackOverflow
                        </a>
                        <a class="dropdown-item" target="_blank" rel="noopener" href="https://github.com/zack6849">
                            <i class="fab fa-github"></i>Github
                        </a>
                        <a class="dropdown-item" target="_blank" rel="noopener" href="https://gitlab.com/zack6849">
                            <i class="fab fa-gitlab"></i>Gitlab
                        </a>
                        <a class="dropdown-item" target="_blank" rel="noopener" href="https://bitbucket.org/zack6849">
                            <i class="fab fa-bitbucket"></i>BitBucket
                        </a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownContact" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-comment-alt"></i>Contact
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownContact">
                        <a class="dropdown-item" target="_blank" rel="noopener"
                           href="https://www.linkedin.com/in/zack6849/"><i class="fab fa-linkedin"></i>LinkedIn</a>
                        <a class="dropdown-item" target="_blank" rel="noopener" href="https://telegram.me/zack6849"><i
                                class="fab fa-telegram"></i>Telegram</a>
                        <a class="dropdown-item" target="_blank" rel="noopener" href="https://keybase.io/zack6849"><i
                                class="fas fa-key"></i>Keybase</a>
                    </div>
                </div>
            </div>
            <div class="navbar-nav">
                @auth
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownContact" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i>{{auth()->user()->name}}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownContact">
                            <a class="dropdown-item"  href="{{route('file.index')}}"><i class="fas fa-save"></i>My Files</a>
                            <a class="dropdown-item"  href=""><i class="fas fa-cog"></i>Settings</a>
                            <a class="dropdown-item"  href="{{route('logout')}}"><i class="fas fa-sign-out-alt"></i>Log Out</a>
                        </div>
                    </div>
                @endauth
                @guest
                    <a class="nav-item nav-link active" href="{{route('login')}}">Login <span class="sr-only">(current)</span></a>
                @endguest
            </div>
        </div>
    </div>
</nav>
