<?php

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    ':module-namespace_:module-name',
    __DIR__ . '/src'
);
