@include('partials.main-heading')

<article id="post-{{ get_the_ID() }}" @php(post_class('bg-white'))>
    @php(the_content())
</article>
