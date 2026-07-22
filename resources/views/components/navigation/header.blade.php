<nav class="bg-nav-950 px-4 py-4 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-screen-2xl">
        <div class="flex items-center justify-between flex-wrap">
            <div class="mr-6 flex shrink-0 items-center text-white">
                <a href="{{route('home')}}" class="font-semibold text-xl tracking-tight hover:text-nav-200">Zachary Craig</a>
            </div>
            <div class="block lg:hidden">
                <button data-nav-toggle type="button" class="flex items-center px-3 py-2 border rounded nav-link border-nav-400 hover:border-white">
                    <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
                </button>
            </div>
            <div data-nav-content class="hidden w-full grow lg:flex lg:w-auto lg:items-center">
                <div class="text-sm lg:grow items-center">
                    <a href="{{route('home')}}" class="block lg:inline-block lg:mt-0 nav-link mr-4">
                        Home
                    </a>
                    <a href="{{route('radio')}}" class="block lg:inline-block lg:mt-0 nav-link mr-4">
                        Logbook
                    </a>
                    <a href="{{route('photos')}}" class="block lg:inline-block lg:mt-0 nav-link mr-4">
                        Photos
                    </a>
                    <a href="{{route('phone.lookup.index')}}" class="block lg:inline-block lg:mt-0 nav-link mr-4">
                        Who's Calling Me?
                    </a>
                </div>
                <div class="items-end">
                    @auth
                        <a href="{{route('file.index')}}" class="block lg:inline-block lg:mt-0 nav-link mr-4">
                            My Files
                        </a>
                        @can('access-admin')
                            <a href="{{route('admin.logbook.index')}}" class="block lg:inline-block lg:mt-0 nav-link mr-4">
                                Logbook
                            </a>
                            <a href="{{route('admin.backgrounds.index')}}" class="block lg:inline-block lg:mt-0 nav-link mr-4">
                                Backgrounds
                            </a>
                        @endcan
                        <form class="block lg:inline-block" action="{{route('logout')}}" method="POST">
                            {{csrf_field()}}
                            <button type="submit" class="lg:mt-0 nav-link mr-4">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>

    </div>
</nav>
