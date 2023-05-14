<?php
/**
 * SyncSHOP e-commerce platform
 * Author: SYNCDEV SRL <salut@syncshop.eu> @link https://www.syncshop.eu
 * (C) All rights reserved. Changing this code without the author's consent is strictly prohibited.
 */

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => $config->db->driver,
    'host'      => $config->db->host,
    'database'  => $config->db->name,
    'username'  => $config->db->user,
    'password'  => $config->db->password,
    'charset'   => $config->db->charset,
    'collation' => $config->db->collation,
    'prefix'    => $config->db->prefix,
]);

$capsule->setAsGlobal();