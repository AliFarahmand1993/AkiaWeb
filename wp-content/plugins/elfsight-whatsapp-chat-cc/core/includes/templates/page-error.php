<?php

if (!defined('ABSPATH')) exit;

?><article class="elfsight-admin-page-error elfsight-admin-page" data-elfsight-admin-page-id="error">
    <h1><?php esc_html_e('اوه ، مشکلی پیش آمد!', $this->textDomain); ?></h1>

    <p class="elfsight-admin-page-error-message">
        <?php esc_html_e('متأسفانه ، چنین صفحه ای وجود ندارد.', $this->textDomain); ?>
    </p>

    <a class="elfsight-admin-page-error-button elfsight-admin-button-large elfsight-admin-button-green elfsight-admin-button" href="#/widgets/" data-elfsight-admin-page="widgets"><?php esc_html_e('بازشگت به صفحه اصلی', $this->textDomain); ?></a>
</article>