
<nav class=" bg-teal-500 pl-20 pr-20 p-6 mb-6">
    <div class="container mx-auto">
        <div class="flex items-center justify-between flex-wrap">
            <div class="flex items-center flex-shrink-0 text-white mr-6">
                <span class="font-semibold text-xl tracking-tight">Zachary Craig</span>
            </div>
            <div class="block lg:hidden">
                <button @click='toggle'  class="flex items-center px-3 py-2 border rounded text-teal-200 border-teal-400 hover:text-white hover:border-white">
                    <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
                </button>
            </div>
            <div  :class="shownav ? 'block': 'hidden'" class="w-full block flex-grow lg:flex lg:items-center lg:w-auto">
                <div class="text-sm lg:flex-grow items-center">
                    <a href="{{route('home')}}" class="block mt-4 lg:inline-block lg:mt-0 text-teal-200 hover:text-white mr-4">
                        Code
                    </a>
                    <a href="{{route('photography')}}" class="block mt-4 lg:inline-block lg:mt-0 text-teal-200 hover:text-white mr-4">
                        Photography
                    </a>
                    <a href="" class="block mt-4 lg:inline-block lg:mt-0 text-teal-200 hover:text-white">
                        Contact Me
                    </a>
                </div>
            </div>
        </div>

    </div>
</nav>
