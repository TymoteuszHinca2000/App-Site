<form role="search" method="get" class="search-form" action="{{ home_url('/') }}">
    <label>
        <span class="sr-only">
            {{ _x('Szukaj:', 'label', 'appandsite') }}
        </span>

        <input
            type="search"
            placeholder="{!! esc_attr_x('Szukaj &hellip;', 'placeholder', 'appandsite') !!}"
            value="{{ get_search_query() }}"
            name="s"
        />
    </label>

    <button>{{ _x('Szukaj', 'submit button', 'appandsite') }}</button>
</form>
