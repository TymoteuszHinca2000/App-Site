
<article id="post-{{ get_the_ID() }}" class="{{ esc_attr(implode(' ', get_post_class('single-project'))) }}">
    @php
        the_content();
    @endphp
</article>
