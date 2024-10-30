<section class="py-32 md:py-48 flex items-center justify-center px-8">
  <div class="w-full max-w-4xl text-center flex flex-col items-center gap-10">
    <header class="flex flex-col items-center gap-6">
      <h1 class="text-5xl md:text-7xl font-black font-grotesk tracking-tight">Still wondering where's your bus?</h1>
      <p class="max-w-prose text-xl md:text-2xl text-neutral-500 font-light tracking-wide">
        Track your ride in real-time and never miss a moment. You'll always know exactly when to head to the stop, saving you from long waits and missed connections.
      </p>
    </header>

    <!-- TODO: Throttled result list -->
    <search class="ring ring-black p-1 w-full max-w-xl rounded-md">
      <form action="search.php" class="grid grid-cols-[56px_1fr_88px] h-16">
        <div class="flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </div>

        <div class="text-lg p-2 flex">
          <label for="bus" class="sr-only">Enter any bus line</label>
          <input type="text" id="bus" name="bus" class="w-full bg-transparent outline-none text-xl font-medium placeholder:text-xl placeholder:font-light" placeholder="Enter any bus line">
        </div>

        <button class="bg-[#55f458] text-[#144e16] flex items-center justify-center rounded-md">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
          </svg>
        </button>
      </form>
    </search>

    <p class="font-light text-neutral-500">
      <span class="text-black font-medium">ProTip!</span> Lorem ipsum dolor sit amet consectetur.
    </p>
  </div>
</section>
