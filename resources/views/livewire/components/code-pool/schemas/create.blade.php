<div class="space-y-4">
    <header>
        <h1 class="text-xl font-bold md:text-2xl">
            {{ __('lunarphp-virtual-product::code-pool.pages.schemas.create.title') }}
        </h1>
    </header>

    <form action="#"
          method="POST"
          wire:submit.prevent="save">
        @include('lunarphp-virtual-product::partials.forms.code-pool-schema')
    </form>
</div>
