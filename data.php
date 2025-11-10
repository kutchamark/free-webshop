<?php
// Redirect script removed: previously forced users to the Discord invite when the
// server date was on/after 2025-05-28. That caused localhost / dev environments
// to be redirected away from the site.
// If you need a timed redirect in future, implement it intentionally and
// conditionally (for example, only on production) rather than unconditionally
// including it in `data.php` which is loaded on every page.
?>