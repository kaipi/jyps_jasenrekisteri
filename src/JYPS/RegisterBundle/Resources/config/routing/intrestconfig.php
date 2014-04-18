<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('member_intrestconfig', new Route('/', array(
    '_controller' => 'JYPSRegisterBundle:IntrestConfig:index',
)));

$collection->add('member_intrestconfig_show', new Route('/{id}/show', array(
    '_controller' => 'JYPSRegisterBundle:IntrestConfig:show',
)));

$collection->add('member_intrestconfig_new', new Route('/new', array(
    '_controller' => 'JYPSRegisterBundle:IntrestConfig:new',
)));

$collection->add('member_intrestconfig_create', new Route(
    '/create',
    array('_controller' => 'JYPSRegisterBundle:IntrestConfig:create'),
    array('_method' => 'post')
));

$collection->add('member_intrestconfig_edit', new Route('/{id}/edit', array(
    '_controller' => 'JYPSRegisterBundle:IntrestConfig:edit',
)));

$collection->add('member_intrestconfig_update', new Route(
    '/{id}/update',
    array('_controller' => 'JYPSRegisterBundle:IntrestConfig:update'),
    array('_method' => 'post|put')
));

$collection->add('member_intrestconfig_delete', new Route(
    '/{id}/delete',
    array('_controller' => 'JYPSRegisterBundle:IntrestConfig:delete'),
    array('_method' => 'post|delete')
));

return $collection;
