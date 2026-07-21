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
                    <div class="lg:relative block lg:inline-block lg:mr-4">
                        <button data-tools-toggle type="button" class="flex w-full items-center gap-1 lg:w-auto lg:mt-0 nav-link">
                            Tools
                            <svg data-tools-chevron class="h-3 w-3 fill-current transition-transform" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5.516 7.548c.436-.446 1.043-.48 1.576 0L10 10.405l2.908-2.857c.533-.48 1.141-.446 1.574 0 .436.445.408 1.197 0 1.615-.406.418-4.695 4.502-4.695 4.502a1.095 1.095 0 0 1-1.576 0S5.919 9.581 5.516 9.163c-.409-.418-.436-1.17 0-1.615z"/></svg>
                        </button>
                        <div data-tools-menu class="hidden pl-4 lg:pl-0 lg:absolute lg:bg-white lg:rounded lg:shadow-lg lg:py-1 lg:w-48 lg:z-10">
                            <a href="{{route('phone.lookup.index')}}" class="block py-1 lg:py-2 lg:px-4 nav-link lg:text-gray-700 lg:hover:bg-gray-100 lg:hover:text-gray-900">
                                Who's Calling Me?
                            </a>
                            <a href="{{route('radio')}}" class="block py-1 lg:py-2 lg:px-4 nav-link lg:text-gray-700 lg:hover:bg-gray-100 lg:hover:text-gray-900">
                                Radio Map
                            </a>
                        </div>
                    </div>
                    <a href="{{route('photos')}}" class="block lg:inline-block lg:mt-0 nav-link mr-4">
                        Photos
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
