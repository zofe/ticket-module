<?php

/*
|--------------------------------------------------------------------------
| Ticket Module Configurations
|--------------------------------------------------------------------------
|
|
*/
return [
    'layout' => 'ticket::admin',
    'menu_admin' => 'ticket::admin_menu',
    'menu_admin_position' => 2,

    'operators_roles'=>['admin'],
    'notification_email' => 'notification@email.it',

//    'company_relation' => 'company',
//    'company_tablename' => 'companies',
//    'company_fk' => 'company_id',
//    'company_field' => 'business_name',
//
//    'company_subject_relation' => 'router',
//    'company_subject_tablename' => 'routers',
//    'company_subject_fk' => 'router_id',
//    'company_subject_field' => 'name',
];
