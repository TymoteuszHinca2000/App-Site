<button class="js-menu-toggler group relative cursor-pointer" aria-label="Menu">
    <div
        class="ring-opacity-30 relative flex h-[50px] w-[50px] transform items-center justify-center overflow-hidden rounded-full bg-brand shadow-md ring-0 ring-gray-200 transition-all duration-200 group-[.is-open]:ring-4 hover:ring-8"
    >
        <div
            class="flex h-[20px] w-[20px] origin-center transform flex-col justify-between overflow-hidden transition-all duration-300"
        >
            <div
                class="h-[2px] w-7 origin-left transform bg-white transition-all duration-300 group-[.is-open]:translate-x-10"
            ></div>
            <div
                class="h-[2px] w-7 transform rounded bg-white transition-all delay-75 duration-300 group-[.is-open]:translate-x-10"
            ></div>
            <div
                class="h-[2px] w-7 origin-left transform bg-white transition-all delay-150 duration-300 group-[.is-open]:translate-x-10"
            ></div>

            <div
                class="absolute top-1/2 flex w-0 -translate-x-10 -translate-y-1/2 transform items-center justify-between transition-all duration-500 group-[.is-open]:w-12 group-[.is-open]:translate-x-0"
            >
                <div
                    class="absolute h-[2px] w-5 rotate-0 transform bg-white transition-all delay-300 duration-500 group-[.is-open]:rotate-45"
                ></div>
                <div
                    class="absolute h-[2px] w-5 -rotate-0 transform bg-white transition-all delay-300 duration-500 group-[.is-open]:-rotate-45"
                ></div>
            </div>
        </div>
    </div>
</button>
