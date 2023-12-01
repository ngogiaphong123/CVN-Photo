<?php

use DI\ContainerBuilder;

$builder = new ContainerBuilder();
try {
    $container = $builder->build();
    return $container;
} catch (Exception $e) {
    error_log($e->getMessage());
}
